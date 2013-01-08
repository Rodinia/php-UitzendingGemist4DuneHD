<?php
	require_once dirname(__FILE__).'/../lib/util.php';
	
	// -------------------- Functions ----------------------

    # Suppress DOM warnings
    libxml_use_internal_errors(true);
    
	function wgetEpisodesByProgramId($programId, $max_pages, $page_offset = 1)
	{
		$ug_search_url = 'http://www.uitzendinggemist.nl/programmas/'.$programId.'/afleveringen?';
        return wgetEpisodes($ug_search_url, $max_pages, $page_offset);
	}
    
    function wgetEpisodes($ug_search_url, $max_pages, $page_offset = 1)
	{
		$episodes = array(); // result

		$pagefound = false;
		$page = $page_offset;
		$num = 0;

		do
		{
			$url = $ug_search_url.(strpos($ug_search_url, '?') ? '&':'?').'&page='.$page++;

            $dom = loadHtmlAsDom($url);
			
			$xpath = new DOMXpath($dom);
            
            $pagefound = false;

			// Find all images
            foreach($xpath->query("//li[@class='episode active knav' or @class='broadcast active']") as $element)
			{
				// Extract local episode ID
                $localid = $element->getAttribute('id');
                $data_remote_id = $element->getAttribute('data-remote-id');
//				if($data_remote_id)
//					$data_remote_id = $element->getAttribute('id');

                $pagefound = true;

				// Extract caption
                $h2list = $xpath->query("div/h2/a", $element);
                $h2_title = $h2list->length==0 ? null : $h2list->item(0)->getAttribute('title');
				$h3_anchor = $xpath->query("div/h3/a", $element)->item(0);
				
				$h3_title = $h3_anchor->getAttribute('title');
                $caption = $h2_title ? $h2_title." - ".$h3_title : $h3_title;
				$localepiid = substr($h3_anchor->getAttribute('href'), 14);

                $episodes[$num++] = array(
					'localepiid' => $localepiid,
                    'caption' => $caption
                );

                $pagefound = true;
			}
		}
		while($pagefound && $page<($page_offset + $max_pages));

		return $episodes;
	}
	
	// Resolve remote-episode-ID base on local-episode-ID 
	function wgetEpisodeId($localepiid)
	{
		$dom = new DOMDocument();
		//echo "# wget Stream Info: $infoUrl\n";
		$dom->loadHTMLFile('http://www.uitzendinggemist.nl/afleveringen/'.$localepiid);
		$xpath = new DOMXpath($dom);
     	$domnodelist = $xpath->query("//span[@id='episode-data']");
		return $domnodelist->item(0)->getAttribute('data-episode-id');
	}
	
	// compressie_formaat should be one of:  wmv|mov|wvc1
	// compressie_kwaliteit  should be one of: sb|bb|std (low to high)
	function getStreamUrl($epiid, $secret, $compressie_formaat = 'mov', $compressie_kwaliteit = 'bb')
	{
		foreach(getStreams($epiid, $secret) as $stream)
		{
			if( $stream->getAttribute('compressie_formaat') == $compressie_formaat && $stream->getAttribute('compressie_kwaliteit') == $compressie_kwaliteit)
			{
				foreach($stream->getElementsByTagName('streamurl') as $streamurl)
					return trim($streamurl->nodeValue); //.'start=0';
			}
		}
		return NULL;
	}
	
	// stream attribute "compressie_formaat"   should is likely one of: wmv|mov|wvc1
	// stream attribute "compressie_kwaliteit" should is likely one of: sb|bb|std    (low to high)
	function getStreams($epiid, $secret)
	{
		$infoUrl = makeStreamInfoUrl($epiid, $secret);

		$dom = new DOMDocument();
		//echo "# wget Stream Info: $infoUrl\n";
		$html = $dom->loadHTMLFile($infoUrl);
		
		$xpath = new DOMXpath($dom);
     	$domnodelist = $xpath->query("//stream");
		// Convert to array
		$streams = array();
		foreach($domnodelist as $stream)
		{
			$streams[] = $stream;
		}
		// Sort array
		return sortStreams($streams);
	}

	// Sort streams on quality, and most suitable for Dune HD
	function sortStreams($streams)
	{
		usort($streams, "stream_cmp");
		return $streams;
	}
	
	// lowest number, first in stream list
	function stream_cmp($stra, $strb)
	{
	    // first sort on Quality (bandwith)
        $result = stream_cmp_quality($stra, $strb);
        // if and only if quality is equel, sort on format
        return $result == 0 ? stream_cmp_format($stra, $strb) : $result;
	}
    
    function stream_cmp_quality($stra, $strb)
    {
        // Compare stream quality (bandwith)
        $cka = $stra->getAttribute('compressie_kwaliteit');
        $ckb = $strb->getAttribute('compressie_kwaliteit');
        return compressieKwaliteitToNum($cka) - compressieKwaliteitToNum($ckb);
	}
    
    function stream_cmp_format($stra, $strb)
    {
        // Compare format
		$cfa = $stra->getAttribute('compressie_formaat');
		$cfb = $strb->getAttribute('compressie_formaat');
        return compressieFormaatToNum($cfa) - compressieFormaatToNum($cfb);
	}
	
	function compressieFormaatToNum($compressieFormaat)
	{
		switch($compressieFormaat)
		{
			case 'mov' : return 0; // MP4/H.264
			case 'wvc1': return 1; // WMV/MMS (Windows Media Video 9 Advanced Profile)
			case 'wmv' : return 2; // WMV
		}
		trigger_error('Unsupported compressie-formaat: '.$compressieFormaat);
	}
	
	function compressieKwaliteitToNum($compressieKwaliteit)
	{
		switch($compressieKwaliteit)
		{
			case 'std': return 0; // ?? best quality 640x360, 1 Mbit/sec
			case 'bb' : return 1; // broadband 320x180 = (WMA 9.1 / 500 kbit/s)
			case 'sb' : return 2; // slowband 160 x 90 = (WMA 9.1 / 100 kbit/s)
		}
		trigger_error('Unsupported compressie-kwaliteit: '.$compressieKwaliteit);
	}
	
	// Utility which follow redirects since the Dune HD does not support HTTP redirects
	function followRedirects($streamurl, &$contentType, $maxRedirects = 4)
	{
		$numRedirects=0;
		while($numRedirects<$maxRedirects)
		{
			$newUrl = checkRedirectUrl($streamurl, $contentType);
			// echo "#   content-type: $contentType\n";
			if(!$newUrl)
			{
				// Would expect  video/x-ms-asf is returned for ASX, but typically UG returns video/x-ms-wmv
				if( $contentType == "video/x-ms-wmv" || $contentType == "video/x-ms-asf")
				{
					$asxRef = getAsxRef($streamurl);
					//echo "# getAsxRef()=$asxRef\n";
					if( startsWith($asxRef, 'mms://') )
						return $asxRef;
					$newUrl = $asxRef;
				}
				else
					break;
			}
			$streamurl = $newUrl;
			// echo "# redirected.\n";
			++$numRedirects;
		}
		return $streamurl;
	}
	
    function checkRedirectUrl($url, &$contentType)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, true); // Only get header (will do HEAD-request, rather then GET-request)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'GET'); // Override HEAD-request

        $out = curl_exec($ch);

        // line endings is the wonkiest piece of this whole thing
        $out = str_replace("\r", "", $out);

        // only look at the headers
        $headers_end = strpos($out, "\n\n");
        if( $headers_end !== false )
        {
            $out = substr($out, 0, $headers_end);
        }

        $location = NULL;
        $contentType = NULL;

        $headers = explode("\n", $out);
        foreach($headers as $header)
        {
            // echo "# header: $header\n";
            if( startsWith($header, 'Location: ') )
            {
                $location = substr($header, 10);
            }
            else if(  startsWith($header, 'Content-Type: ') )
            {
                $contentType = substr($header, 14);
            }
            else if(  startsWith($header, 'Content-Type: ') )
            {
                $contentType = substr($header, 14);
            }
        }

        curl_close($ch);

        return $location;
    }

    function getAsxRef($url_asx)
    {
        $dom = new DOMDocument();
        $html = $dom->loadHTMLFile($url_asx);

        if(!$html) return null;
		
		$xpath = new DOMXpath($dom);
     	
        foreach($xpath->query("//ref") as $ref)
		{
			return trim( $ref->getAttribute('href') );
		}
    }

	function makeStreamInfoUrl($epiid, $secret)
	{
		return 'http://pi.omroep.nl/info/stream/aflevering/'. $epiid .'/'. episodeHash($epiid, $secret);
	}

	function makeAfleveringMetaDataUrl($epiid, $secret)
	{
		return 'http://pi.omroep.nl/info/metadata/aflevering/'. $epiid .'/'. episodeHash($epiid, $secret);
	}

	function getAfleveringMetaDataUrl($epiid, $secret)
	{
		$url = makeAfleveringMetaDataUrl($epiid, $secret);

		$dom = new DOMDocument();
		$html = $dom->loadHTMLFile($url);

		$result =  array();
		foreach($dom->getElementsByTagName('aflevering') as $aflevering)
		{
			foreach($aflevering->getElementsByTagName('serie') as $serie)
			{
				$result['serie_id'] = $serie->getAttribute('id');
				foreach($serie->getElementsByTagName('serie') as $serie_titel)
				{
					$result['serie_titel'] = $serie_titel->nodeValue;
				}
			}
			$result['prid'] = $aflevering->getAttribute('prid');

			$streamSense = $aflevering->getElementsByTagName('streamsense')->item(0);
			$result['sko_dt'] = $streamSense->getElementsByTagName('sko_dt')->item(0)->nodeValue;
		}
		return $result;
	}

	function makePlaylistSerieMetaDataUrl($serie_id, $secret)
	{
		return 'http://pi.omroep.nl/info/playlist/serie/'. $serie_id .'/'. episodeHash($serie_id, $secret);
	}

	function makeStreamUrl($epiid, $secret)
	{
		return 'http://pi.omroep.nl/info/stream/aflevering/'. $epiid .'/'. episodeHash($epiid, $secret);
	}

	// Berekent episode hash voor uitzending gemist
	function episodeHash($episode, $secret)
	{
		$hashInput = $episode.'|'.$secret[1];
		//echo "hash-input: ".$hashInput;
		$md5 = md5($hashInput);
		return strtoupper($md5);
	}

	function getSessionKey()
	{
		$sessionUrl = 'http://pi.omroep.nl/info/security/';

		$rawKey = wgetSessionKey($sessionUrl);
		$decodedKey = base64_decode($rawKey);
		return explode('|', $decodedKey);
	}
    
    // Extraxts stream URL from SMIL (Content-Type: application/smil)
    function wgetVideoSrcFromSmil($urlToSmil)
	{
		$dom = new DOMDocument();
		$dom->loadHTMLFile($urlToSmil);
		$xpath = new DOMXpath($dom);
     	$videos = $xpath->query("//video");

		foreach($videos as $video)
        {
            return $video->getAttribute('src');
		}
	}

	// Download session key
	/* ToDo
	function wgetSessionKey($sessionUrl)
	{
		$doc = new DOMDocument();
		$html = $doc->loadHTMLFile($sessionUrl);
		$xpath = new DOMXpath($doc);
        return $xpath->query("/session/key")->nodeValue;
	}*/
	
	// Download session key
	
	function wgetSessionKey($sessionUrl)
	{
		$dom = new DOMDocument();
		$html = $dom->loadHTMLFile($sessionUrl);
		foreach($dom->getElementsByTagName('session') as $session)
		{
			foreach($session->getElementsByTagName('key') as $key)
			{
				return $key->nodeValue;
			}
		}
		return 0;
	}

    function wgetProgramPrefixLinks()
    {
        $result = array();
		
		$ug_url = 'http://www.uitzendinggemist.nl/programmas/';

		$doc = new DOMDocument();
        $doc->loadHTMLFile($ug_url) || error('Failed to load HTML file: $ug_url');

		$xpath = new DOMXpath($doc);
        $elements = $xpath->query("/html/body/div[@id='content']/div[@id='series-index']/div[1]/div[@id='series-index-letters']/ol/li/a");
		
		foreach($elements as $element)
        {
            $href = $element->getAttribute('href');
			$programId=substr($href, 12);
			$result[] = $programId;
        }
		
		return $result;
    }
	
	function getMaxPage($xpath, $divid)
	{
		$elements = $xpath->query("/html/body//div[@id='".$divid."']/div[@class='pagination']/a");
		$maxPage = 1;
		foreach ($elements as $element)
		{
			$value = $element->nodeValue;
			if(is_numeric($value))
					$maxPage = $value;
		}
		return $maxPage;
	}
	
	function wgetProgramsAZ($suffix)
    {
        return wgetPrograms('http://www.uitzendinggemist.nl/programmas/'.$suffix.'?display_mode=detail', 'series-index-series');
    }

    function wgetPrograms($url, $divid)
    {
        //$query="/html/body/div[@id='content']/div[@id='series-index']/div[@class='right-column']/div[@id='series-index-series']/ol/li/h2/a";
		//$query="/html/body//div[@id='".$divid."']/ol/li/h2/a";
		$query="/html/body//div[@id='".$divid."']/ol/li//a[@class='series series-image']";
		$xpath = getProgamHtmlXpath($url, 1);
		$maxPage = getMaxPage($xpath, $divid);
		
		$result = array();
		$nodeList = $xpath->query($query);
		foreach ($nodeList as $href) $result[] = $href;
		
		for($page=2;$page<=$maxPage;++$page)
		{
			$xpath = getProgamHtmlXpath($url, $page);
			$nodeList = $xpath->query($query);
			foreach ($nodeList as $href) $result[] = $href;
		}
		return $result;
    }
	
	function getProgamHtmlXpath($url, $page)
	{
		$ug_url = $url.(strpos($url, '?') === false ? '?' : '&').'page='.$page;
		//echo "# Loading url: $ug_url\n";
        
        //$doc = new DOMDocument();
        //$doc->loadHTMLFile($ug_url);
        $dom = loadHtmlAsDom($ug_url);
        $dom->strictErrorChecking = false;

        return new DOMXpath($dom);
	}
	
	function wgetBroadcasters()
    {
		$query="/html/body//div[@id='broadcasters-page']/ol[@class='broadcasters']/li/a";
		$xpath = getProgamHtmlXpath('http://www.uitzendinggemist.nl/omroepen', 1);
		
		$result = array();
		$nodeList = $xpath->query($query);
		
		foreach ($nodeList as $href) $result[] = $href;
		
		return $result;
    }
?>
<?php
	require_once dirname(__FILE__).'/util.php';
	
	// -------------------- Functions ----------------------

    # Suppress DOM warnings
    libxml_use_internal_errors(true);
    
    function wgetEpisodesWeekarchief($ug_search_url, $max_pages, $page_offset = 1)
	{
		$episodes = array(); // result

		$itemQueries = array(); // result
		$itemQueries['program'] = "h2/a";
		$itemQueries['episode'] = "h3/a";
		$itemQueries['href'] = "h3/a/@href";
        $itemQueries['data-images'] = "../div[@class='image']/a/img/@data-images";

		$result = privWgetEpisodes($ug_search_url, "//li[@class='broadcast active']/div[@class='info']", $itemQueries, $max_pages, $page_offset);
		foreach($result as $item)
		{
			$episodes[] = array(
				'refid' => substr($item['href'], 14),
				'title' => $item['program'].' - '.$item['episode'],
                'img'   =>  getImgSrcFromDataImages($item['data-images'])
			);
		}
		return $episodes;
	}
	
	function wgetEpisodesByProgram($ug_search_url, $max_pages, $page_offset = 1)
	{
		$episodes = array(); // result

        $itemQueries = array(
            'episode'     => 'h3/a',
            'href'        => 'h3/a/@href',
            'data-images' => "../div[@class='image']/a/img/@data-images",
            'description' =>  ".");

		$result = privWgetEpisodes($ug_search_url, "//ul/li[@class='episode active']/div[@class='description']", $itemQueries, $max_pages, $page_offset);
		foreach($result as $item)
		{
			$description = false;
            foreach(preg_split("/((\r?\n)|(\r\n?))/", trim($item['description'])) as $line)
            {
                $description = trim($line);
            }
            
            $episodes[] = array(
				'refid' => substr($item['href'], 14),
                'img' => getImgSrcFromDataImages($item['data-images']),
				'title' => $item['episode'],
				'description' => $description);
		}
		return $episodes;
	}
    
    function getImgSrcFromDataImages($data_images)
    {
        $images = explode(',', trim($data_images, '[]'));
        $img = trim($images[0], '"');
        return str_replace('140x79', '280x148', $img);
    }
    
    function wgetProgramsAZ($suffix, $max_pages, $page_offset)
    {
        return wgetPrograms('http://www.uitzendinggemist.nl/programmas/'.$suffix.'?display_mode=detail', $max_pages, $page_offset);
    }
    
    function wgetPrograms($ug_search_url, $max_pages = 1, $page_offset = 1)
    {
        $itemQueries = array(); // result
		$itemQueries['name'] = "h2/a";
		$itemQueries['href'] = "h2/a/@href";
        $itemQueries['data-images'] = "../div[@class='image']/a/img/@data-images";
        
        $query="/html//li[@class='series']/div[@class='info']";
		
        return privWgetEpisodes($ug_search_url, $query, $itemQueries, $max_pages, $page_offset);
    }
    
    function wgetSearchPrograms($ug_search_url, $max_pages = 1, $page_offset = 1)
    {
        $itemQueries = array(); // result
		$itemQueries['name'] = "h3/a";
		$itemQueries['href'] = "h3/a/@href";
        $itemQueries['data-images'] = "div[@class='img']/a/img/@data-images";
        
        $query="//ul[@id='series-result']/li[@class='series knav']/div[@class='wrapper']";
		
        return privWgetEpisodes($ug_search_url, $query, $itemQueries, $max_pages, $page_offset);
    }
	
	function privWgetEpisodes($ug_search_url, $query, $itemQueries, $max_pages, $page_offset = 1)
	{
		$episodes = array(); // result

		$pagefound = false;
		$page = $page_offset;
		$num = 0;

		do
		{
			$url = $ug_search_url.(strpos($ug_search_url, '?') ? '&':'?').'page='.$page++;
			//echo "<p>Read from url = $url</p>\n";
			$dom = loadHtmlAsDom($url);
			$xpath = new DOMXpath($dom);
            $pagefound = false;
			foreach($xpath->query($query) as $episode)
			{
				$pagefound = true;
				
				$items = array(); // result
				foreach($itemQueries as $itemKey => $itemQuery)
				{
					$items[$itemKey] = $xpath->query( $itemQuery, $episode)->item(0)->nodeValue;
					//echo "<p>$itemKey => $itemQuery = ".$items[$itemKey]."</p>\n";
				}
				$episodes[] = $items;
			}
		}
		while($pagefound && $page<($page_offset + $max_pages));

		return $episodes;
	}
     
	// Resolve remote-episode-ID base on local-episode-ID 
	function wgetEpisodeData($localepiid)
	{
		//echo "# wgetEpisodeId($localepiid)\n";
		//echo '#   url = http://www.uitzendinggemist.nl/afleveringen/'.$localepiid."\n";
		$dom = loadHtmlAsDom('http://www.uitzendinggemist.nl/afleveringen/'.$localepiid);
		$xpath = new DOMXpath($dom);
<<<<<<< HEAD
     	$ed = $xpath->query("//span[@id='episode-data']")->item(0);
		$eda = array();
		foreach ($ed->attributes as $name => $value) $eda[$name] = $value->value;
		//print_r($eda);
		return $eda;
    }
    
    function wgetEpisodeId($localepiid)
    {
        $epData = wgetEpisodeData($localepiid);
		return $epData['data-episode-id'];
    }
=======
     	$domnodelist = $xpath->query("//span[@id='episode-data']");
		//return $domnodelist->item(0)->getAttribute('data-episode-id');
		return $domnodelist->item(0)->getAttribute('data-player-id');
	}
>>>>>>> fa0dcc354e476fcfa828c8af6c24ecef47dc0050
	
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
	function getStreams($ed)
	{
		//print_r($ed);
        if( $ed['data-episode-id'] != null )
        {
            $secret = getSessionKey();
            return getStreamsByEpisodeId($ed['data-episode-id'], $secret);
        }
        else
        {
            return getStreamsByPlayerId($ed['data-player-id']);
        }
    }
    // http://ida.omroep.nl/odi/?prid=VPWON_1219719&puboptions=adaptive,h264_bb,h264_sb,h264_std&adaptive=yes&part=1&token=gbq5f6ov0jqv05u5lj5t98p5j3&callback=jQuery18207517273030243814_1395569608767&_=1395569609791

    function getStreamsByPlayerId($playerid)
	{
        //echo "# getStreamsByPlayerId($playerid)\n";
        
        $token = getPlayerToken();
        $time = time();
        //echo "#   token = $token\n";
        //echo "#   time  = $time\n";
        
        $url ='http://ida.omroep.nl/odi/?prid='.$playerid.
            '&puboptions=adaptive,h264_bb,h264_sb,h264_std,wmv_bb,wmv_sb,wvc1_std'.
			'&adaptive=no'.
			'&part=1'.
			'&token='.$token.
            '&callback=cb'.
            '&_='.$time;

        //echo "#   url   = $url\n";
        return getJson($url);
    }
    
    function getJson($url)
    {
        $contents = file_get_contents($url); 
        $contents = utf8_encode($contents);
        preg_match('#\((.*?)\)#', $contents, $matches); // Keep oonly JSON structure
        return json_decode($matches[1], true);
    }

    function getPlayerToken()
    {
        $contents = file_get_contents('http://ida.omroep.nl/npoplayer/i.js'); 
        $contents = utf8_encode($contents); 
        //preg_match('/"((?:\\"|[^"])*)"/', $contents, $matches);
        preg_match('/"([^"]*)"/', $contents, $matches); 
        return $matches[1]; 
    }

    function getStreamsByEpisodeId($epiid, $secret)
	{    
        //echo "# getStreams($epiid, $secret)\n";
        $infoUrl = makeStreamInfoUrl($epiid, $secret);
       
		//echo "# infoUrl=$infoUrl\n";

		$dom = new DOMDocument();
		//echo "# wget Stream Info: $infoUrl\n";
		//$html = $dom->loadHTMLFile($infoUrl);
		if( !$dom->loadXML(curlGet($infoUrl)) ) die('Failed to load XML stream info from: '.$infoUrl);
				
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
	
	function makeStreamInfoUrl($epiid, $secret)
	{
		//echo "# makeStreamUrl(epiid=$epiid, secret=$secret)\n";
		return 'http://pi.omroep.nl/info/stream/aflevering/'. $epiid .'/'. episodeHash($epiid, $secret);
	}

	function makeAfleveringMetaDataUrl($epiid, $secret)
	{
		return 'http://pi.omroep.nl/info/metadata/aflevering/'. $epiid .'/'. episodeHash($epiid, $secret);
	}

	function getAfleveringMetaDataUrl($epiid, $secret)
	{
		$url = makeAfleveringMetaDataUrl($epiid, $secret);

		//$dom = new DOMDocument();
		//$html = $dom->loadHTMLFile($url);
        $dom = loadHtmlAsDom($url);

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
		//echo "# makeStreamUrl(epiid=$epiid, secret=$secret)\n";
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
		$dom = loadXmlAsDom($sessionUrl);
        $xpath = new DOMXpath($dom);
        return $xpath->query("/session/key")->item(0)->nodeValue;
        return $result->item(0)->nodeValue;
        
        /*$dom = loadHtmlAsDom($sessionUrl); // ToDo: use XML method
		foreach($dom->getElementsByTagName('session') as $session)
		{
			foreach($session->getElementsByTagName('key') as $key)
			{
				return $key->nodeValue;
			}
		}
		return 0;*/
	}

    function wgetProgramPrefixLinks()
    {
        $result = array();
		
		$doc = loadHtmlAsDom('http://www.uitzendinggemist.nl/programmas/');
        
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
	
	function getLastPage($xpath, $divid)
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
	function wgetGenres()
    {
                $query="/html/body//div[@id='genres-page']/ol[@class='genres']/li/a";
                $xpath = getProgamHtmlXpath('http://www.uitzendinggemist.nl/genres', 1);

                $result = array();
                $nodeList = $xpath->query($query);

                foreach ($nodeList as $href) $result[] = $href;

                return $result;
    }
        function wgetRegios()
    {
		$query="/html/body//div[@id='broadcasters-page']/ol[@class='broadcasters']/li/a";

                $xpath = getProgamHtmlXpath('http://www.uitzendinggemist.nl/omroepen/regio', 1);

                $result = array();
                $nodeList = $xpath->query($query);

                foreach ($nodeList as $href) $result[] = $href;

                return $result;
    }


?>

<?php
	// -------------------- Functions ----------------------

	function wgetEpisodes($programma, $max_pages, $page_offset = 1)
	{
		$ug_search_url = 'http://www.uitzendinggemist.nl/programmas/'.$programma.'/afleveringen?';

		$dom = new DOMDocument();

		$episodes = array(); // result

		$pagefound = false;
		$page = $page_offset;
		$num = 0;

		do
		{
			$url = $ug_search_url . '&page='.$page++;

			$html = $dom->loadHTMLFile($url);

			$pagefound = false;

			// Find all images
			foreach($dom->getElementsByTagName('li') as $element)
			{
				if($element->getAttribute('class') == 'episode active knav')
				{
					// Extract ID
					$epiid = $element->getAttribute('id');
					$epiid = substr($epiid, 8);
					$pagefound = true;

					$data_remote_id = $element->getAttribute('data-remote-id');

					// Extract Title

					$caption = extractTitle($element);

					$episodes[$num++] = array(
						"epiid" => $data_remote_id,
						"localid" => $epiid,
						"caption" => $caption
					);

					$pagefound = true;
				}
			}
		}
		while($pagefound && $page<($page_offset + $max_pages));

		return $episodes;
	}


	function extractTitle($element)
	{
		foreach($element->getElementsByTagName('div') as $ey)
		{
			if($ey->getAttribute('class') == 'description')
			{
				foreach($ey->getElementsByTagName('h3') as $ez)
				{
					foreach($ez->getElementsByTagName('a') as $ea)
					{
						return $ea->getAttribute('title');
					}
				}
			}
		}
	}

    // compressie_formaat should be one of:  wmv|mov|wvc1
	// compressie_kwaliteit  should be one of: sb|bb|std (low to high)
	function getStreamUrl($epiid, $secret, $compressie_formaat = 'mov', $compressie_kwaliteit = 'bb')
	{
		$infoUrl = makeStreamInfoUrl($epiid, $secret);

		$dom = new DOMDocument();
		$html = $dom->loadHTMLFile($infoUrl);

		foreach($dom->documentElement->getElementsByTagName('stream') as $stream)
		{
			if( $stream->getAttribute('compressie_formaat') == $compressie_formaat && $stream->getAttribute('compressie_kwaliteit') == $compressie_kwaliteit)
			{
				foreach($stream->getElementsByTagName('streamurl') as $streamurl)
					return trim($streamurl->nodeValue); //.'start=0';
			}
		}
		return NULL;
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

	function makeSerieMetaDataUrl($serie_id, $secret)
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



?>
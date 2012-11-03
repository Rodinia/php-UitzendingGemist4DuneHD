<?php

    libxml_use_internal_errors(true);
    
    // Vara functions

	function getConfigXml($configXmlUrl)
	{
		$doc = new DOMDocument();
		$doc->loadHTMLFile($configXmlUrl) || error('Failed to load HTML file: ' + $configXmlUrl);
		$result =  array();

		foreach($doc->documentElement->getElementsByTagName('file') as $file)
		{
			$result['file'] =  trim($file->nodeValue);
		}

		return $result;
	}

	function makeConfigXmlUrl($mediaid)
	{
		return 'http://media.vara.nl/player/config.xml.php?mediaid='. $mediaid .'/'. '&autostart=false';
	}

	function getVideoConfigXml($videoUrl)
	{
		// Fix XML (otherwise CDATA doesn't work)
		$xml = file_get_contents($videoUrl);
		$xml = '<?xml version="1.0"?>'."\n".$xml;

		error_reporting(E_ALL);
		$dom = new DOMDocument();
		$dom->loadXML($xml);

		$result = array();

		//$dom->documentElement->getElementsByTagName('tracklist')
		$playlist = $dom->getElementsByTagName('playlist')->item(0);

		$xml = $playlist->ownerDocument->saveXML($playlist);


		$tracklist = $playlist->getElementsByTagName('trackList')->item(0);

		$location = $tracklist->getElementsByTagName('location')->item(0);

		$result['location'] = $location->childNodes->item(0)->nodeValue;

		$image = $tracklist->getElementsByTagName('image')->item(0);
		$result['image'] =  trim($image->childNodes->item(0)->nodeValue);

		return $result;
	}
    
    function getRecent()
	{
		$doc = new DOMDocument();
        $url = 'http://omroep.vara.nl/gemist';
		$doc->loadHTMLFile($url) || error($url);
        $xpath = new DOMXpath($doc);
        $divs = $xpath->query("//div[@class='ankeiler']");
        
        $result = array();
		foreach($divs as $ankeiler)
		{
			// Extract media id
            $a = $ankeiler->getElementsByTagName('a')->item(0);
            $href = $a->getAttribute('href');
            $item['id'] = substr($href,7);
            
            // Extract text
            $imgbox = $a->getElementsByTagName('div')->item(0);
                        
            // Extract text
            $heading = $imgbox->getElementsByTagName('div')->item(0);
            $textbox = $heading->getElementsByTagName('div')->item(1);
            $item['caption'] = $textbox->getElementsByTagName('h3')->item(0)->nodeValue;
            
            $result[] = $item;
		}

		return $result;
	}
 ?>
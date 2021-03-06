<?php

	require_once dirname(__FILE__).'/../lib/util.php';

    libxml_use_internal_errors(true);
    
    // Vara functions

	function getConfigXml($configXmlUrl)
	{
		$doc = loadHtmlAsDom($configXmlUrl);
        
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
		$xml = curlGet($videoUrl);
		$xml = '<?xml version="1.0"?>'."\n".$xml;

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
        if($image != null && $image->childNodes->length > 0)
        {
            $result['image'] =  trim($image->childNodes->item(0)->nodeValue);
        }

		return $result;
	}
    
    function getDezeWeek()
	{
		$doc = new DOMDocument();
        $url = 'http://omroep.vara.nl/gemist';
		$doc->loadHTMLFile($url) || error($url);
        $xpath = new DOMXpath($doc);
        $divs = $xpath->query("//div[@class='ankeiler']");
        
        $result = array();
		foreach($divs as $ankeiler)
		{
			$item = array();
            // Extract media id
            $a = $ankeiler->getElementsByTagName('a')->item(0);
            $href = $a->getAttribute('href');
            if(startsWith($href,'/media/'))
            {
                $item['id'] = substr($href, 7);
                
                // Extract text
                $imgbox = $a->getElementsByTagName('div')->item(0);
                
                if($imgbox instanceof DOMElement)
                {
                    // Extract text
                    $heading = $imgbox->getElementsByTagName('div')->item(0);
                    if($heading instanceof DOMElement)
                    {
                        $textbox = $heading->getElementsByTagName('div')->item(1);
                        if($textbox instanceof DOMElement)
                        {
                            $h3 = $textbox->getElementsByTagName('h3')->item(0);
							if($h3)
							{
								$item['caption'] = $h3->nodeValue;
							}
							else $item['caption'] = "$h3=null";
                        }
                    }
                }
                
                $result[] = $item;
            }
		}

		return $result;
	}
    
	# Get decoded JSON document descrbing VARA program list
	function getVaraProgramList()
	{
		return json_decode(  file_get_contents('http://omroep.vara.nl/typo3conf/ext/vara_media/class.tx_varamedia_programpicker.php', "r") );
	}
	
	function getVaraProgramFragments($program_url)
	{
		$doc = new DOMDocument();
        $url = 'http://omroep.vara.nl/gemist';
		$doc->loadHTMLFile($program_url) || error($url);
        $xpath = new DOMXpath($doc);
        $xpres = $xpath->query("//a/div[@class='textbox']");
        
        $result = array();
		foreach($xpres as $textbox)
		{
			$item = array();
            // Extract media id
			$a = $textbox->parentNode;
			$href = $a->getAttribute('href');
                
			if(startsWith($href,'/media/'))
            {
				$item['id'] = substr($href, 7);
                
                $item['caption'] = $textbox->nodeValue == '' ? "_no_name_" : $textbox->nodeValue;
                
                $result[] = $item;
            }
		}

		return $result;
	}
	
	function write_vara_play_table_row($title, $mediaid)
	{
       echo  '<td>'.$title.'</td>';
        echo '<td><a href="../vara_stream.php?type=asx&mediaid='.$mediaid.'"><img alt="play" src="../../html/img/windows_media_player_32.png" title="Play using Windows Media Player"/></a></td>';
		echo '<td><a href="../vara_stream.php?type=m3u&mediaid='.$mediaid.'"><img alt="play" src="../../html/img/media-playback-start_32.png" title="M3U Playlist"/></a></td>';
		echo '<td><a href=http://omroep.vara.nl/media/'.$mediaid.'>omroep.vara.nl</a></td>';
		echo '<td><a href="../vara_stream.php?type=dune&mediaid='.$mediaid.'"><img src="../../html/img/dune_hd_logo.png" alt="Dune HD" title="Show DuneHD data"/></a></td>';
 	}
	
	/*
	INSERT INTO `ug`.`favorite` (`duneSerial`, `provider`, `type`, `refid`, `title`, `img`) VALUES ('8C50-5FE6-146A-F7E7-1D1A-C163-EF74-F494', 'vara', 'media', '206277', 'Live@Giel: Will and the People: Sensimilla', 'http://media.vara.nl/files/thumbnails/297412_WillandthePeopleLIVESensimilla_680x383.jpg');
	INSERT INTO `ug`.`favorite` (`duneSerial`, `provider`, `type`, `refid`, `title`) VALUES ('8C50-5FE6-146A-F7E7-1D1A-C163-EF74-F494', 'vara', 'media', '84673', 'Live@Giel: Triggerfinger: I Follow Rivers, maar dan met kopjes!');
	INSERT INTO `ug`.`favorite` (`duneSerial`, `provider`, `type`, `refid`, `title`) VALUES ('8C50-5FE6-146A-F7E7-1D1A-C163-EF74-F494', 'vara', 'media', '187251', 'Live@Giel: Beth Hart: Bang Bang Boom Boom');
	INSERT INTO `ug`.`favorite` (`duneSerial`, `provider`, `type`, `refid`, `title`) VALUES ('8C50-5FE6-146A-F7E7-1D1A-C163-EF74-F494', 'vara', 'media', '187252', 'Live@Giel: Beth Hart - I need a dollar');
	INSERT INTO `ug`.`favorite` (`duneSerial`, `provider`, `type`, `refid`, `title`) VALUES ('8C50-5FE6-146A-F7E7-1D1A-C163-EF74-F494', 'vara', 'media', '187258', 'Live@Giel: Beth Hart - Baddest');
	INSERT INTO `ug`.`favorite` (`duneSerial`, `provider`, `type`, `refid`, `title`) VALUES ('8C50-5FE6-146A-F7E7-1D1A-C163-EF74-F494', 'vara', 'media', '198932', 'Live@Giel: Chilly Gonzales LIVE: Knight Moves');
	INSERT INTO `ug`.`favorite` (`duneSerial`, `provider`, `type`, `refid`, `title`) VALUES ('8C50-5FE6-146A-F7E7-1D1A-C163-EF74-F494', 'vara', 'media', '197809', 'Live@Giel: Jake Bugg: Country Song');
	INSERT INTO `ug`.`favorite` (`duneSerial`, `provider`, `type`, `refid`, `title`) VALUES ('8C50-5FE6-146A-F7E7-1D1A-C163-EF74-F494', 'vara', 'media', '197882', 'Live@Giel: Qeaux Qeaux Joans LIVE: While The Whole World\'s Asleep');
	*/
 ?>
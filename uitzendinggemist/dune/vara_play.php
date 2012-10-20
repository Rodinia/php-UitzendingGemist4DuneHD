<?php
	#Enable display errors
	ini_set('display_errors',1);
	error_reporting(E_ERROR);

	include 'dune.php';

	header('Content-type: text/plain');

	$mediaid = $_GET['mediaid'];
	echo "# mediaid: $mediaid\n";

	$configXmlUrl = makeConfigXmlUrl($mediaid);
	echo "# url config.xml: $configXmlUrl\n";

	$configXml = getConfigXml($configXmlUrl);
	$videoConfigUrl = $configXml['file'];
	echo "# url config_video.xml: $videoConfigUrl\n";

	$configVideo = getVideoConfigXml($videoConfigUrl);
	$mediaLocation = $configVideo['location'];

	echo "# url media location: $mediaLocation\n";

	// Check for redirects
	$mediaLocation = followRedirects($mediaLocation, $contentType, 1);

	dunePlay($mediaLocation, 'video/mp4');

	// Vara functions

	function getConfigXml($configXmlUrl)
	{
		$dom = new DOMDocument();
		$html = $dom->loadHTMLFile($configXmlUrl);
		$result =  array();

		foreach($dom->documentElement->getElementsByTagName('file') as $file)
		{
			$result['file'] =  trim($file->nodeValue);
		}

		return $result;
	}

	function makeConfigXmlUrl($mediaid)
	{
		// http://media.vara.nl/player/config.xml.php?mediaid=187252&autostart=false
		return 'http://media.vara.nl/player/config.xml.php?mediaid='. $mediaid .'/'. '&autostart=fals';
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


 ?>
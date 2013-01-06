<?php
	#Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);

	include_once '../lib_ugemist.php';
	include_once 'dune.php';

	header('Content-type: text/plain');

	$epiid = $_GET['epiid'];

	$sessionKey = getSessionKey();
	
	$streams = getStreams($epiid, $sessionKey);

	if(count($streams) == 0)
	{
		duneError("No streams found");
		exit;
	}

		// Check for redirects
	foreach($streams as $stream)
	{
		$format =  $stream->getAttribute('compressie_formaat');
		$quality = $stream->getAttribute('compressie_kwaliteit');
		$streamurl = trim($stream->getElementsByTagName('streamurl')->item(0)->nodeValue);
		
		echo "# checking: [$format/$quality] $streamurl\n";
		// Eliminate redirects
		$streamurl = followRedirects($streamurl, $contentType);
		
		if($contentType == 'application/smil')
		{
			echo "# Skip Apple (application/smil) stream, cotinue with next best stream";
			continue;
		}

		if($streamurl == null)
		{
			duneError("To many redirects.");
			exit;
		}
		
		dunePlay($streamurl, $contentType);
		break;
	}

 ?>
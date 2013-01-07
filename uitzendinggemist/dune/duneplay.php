<?php
	#Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);

	include_once '../lib_ugemist.php';
	include_once 'dune.php';

	header('Content-type: text/plain');

	$epiid = isset($_GET['epiid']) ? $_GET['epiid'] : wgetEpisodeId($_GET['localepiid']);
	echo "# remote-episode-ID: $epiid\n";

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
		
		echo "# Checking: [$format/$quality] $streamurl\n";
		// Eliminate redirects
		$streamurl = followRedirects($streamurl, $contentType);
		
		if($contentType == 'application/smil')
		{
			echo "#   Retrieve SMIL file from: $streamurl\n";
            //$streamurl = wgetVideoSrcFromSmil($streamurl);
            //echo "#   SMIL stream URL = $streamurl\n";
                        
            echo "#   Skip MP4/rtsp stream, not support by Dune HD, content-Type: $contentType, url: $streamurl\n";
            echo "#   Continue with next best stream.\n";
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
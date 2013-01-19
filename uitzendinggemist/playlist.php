<?php
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);
    include_once 'lib/playlist.php';
	
	include_once 'lib/lib_ugemist.php';
	
	$type = isset($_GET['type']) ? $_GET['type'] : 'asx';

	$streamurl = null;
	if( isset($_GET['streamurl']) )
	{
		$streamurl = $_GET['streamurl'];
	}
	else
	{
		$epiid = isset($_GET['epiid']) ? $_GET['epiid'] : wgetEpisodeId($_GET['localepiid']);
		
		$streams = getStreams($epiid, getSessionKey());
		
		$streamurl = null;
		// Check for redirects
		foreach($streams as $stream)
		{
			$format =  $stream->getAttribute('compressie_formaat');
			$quality = $stream->getAttribute('compressie_kwaliteit');
			$streamurl = trim($stream->getElementsByTagName('streamurl')->item(0)->nodeValue);
			
			$streamurl = followRedirects($streamurl, $contentType);
			
			if($contentType == 'application/smil')
			{
				// Skip Apple (application/smil) stream, cotinue with next best stream";
				continue;
			}
			break;
		}
	}
	
	writePlaylist($streamurl, $type);
	
?>

<?php
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);
	
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
	
	if($streamurl)
	{
		echo "#type: $type\n";
		switch($type)
		{
			case 'm3u': write_m3u($streamurl); break;
			case 'asx':
			default:    writeAsx($streamurl);  break;
		}
	}
	else
	{
		writeError("No supported stream found.");
	}

	function writeAsx($href)
	{
		header('Content-type: video/x-ms-asf');
		echo "<ASX version=\"3\">\n";
		echo "<Entry>\n";
		echo "	<ref href=\"$href\" />\n";
		echo "</Entry>\n";
		echo "</ASX>\n";
	}
	
	function write_m3u($url)
	{
		header('Content-type: audio/x-mpegurl'); // audio/x-mpegurl, audio/mpeg-url, application/x-winamp-playlist, audio/scpls, audio/x-scpls
		echo $url."\n";
	}

	function writeError($error)
	{
		echo "<html>\n";
		echo "<head>\n";
		echo "  <title>Error</title>\n";
		echo "</head>\n";
		echo "<body>\n";
		echo "  <h1>$error</h1>\n";
		echo "</body>\n";
		echo "</html>\n";
	}
?>

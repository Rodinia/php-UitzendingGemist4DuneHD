<?php
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);
	
	include_once 'lib_ugemist.php';

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

	if($streamurl)
	{
		writeAsx($streamurl);
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

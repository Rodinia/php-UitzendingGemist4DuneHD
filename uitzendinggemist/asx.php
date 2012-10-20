<?php
	include 'common.php';

	$sessionKey = getSessionKey();

	$epiid = $_GET['epiid'];

	foreach(array("mov|std", "mov|bb", "wmv|bb") as $fq)
	{
		$fq = explode('|', $fq);
		$streamurl=getStreamUrl($epiid, $sessionKey, $fq[0], $fq[1]);
		if($streamurl) break;
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

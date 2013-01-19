<?php
	//ini_set('display_errors',1);
	error_reporting(E_ALL);
	
	include_once '../../lib/playlist.php';
	include_once '../lib_vara.php';
	
	$type = isset($_GET['type']) ? $_GET['type'] : 'asx';

	$streamurl = null;
	if( isset($_GET['mediaid']) )
	{
		$mediaid = $_GET['mediaid'];

		$configXmlUrl = makeConfigXmlUrl($mediaid);
        $configXml = getConfigXml($configXmlUrl);
		$videoConfigUrl = $configXml['file'];
		$configVideo = getVideoConfigXml($videoConfigUrl);
		$mediaLocation = $configVideo['location'];
		
		// Switch to HQ stream (720x400 1.5 MBit/sec)
		$mediaLocation = str_replace('.mp4', '-hq.mp4', $mediaLocation);
		
        // Generate playlist (playlist.php)
		writePlaylist($mediaLocation, $type);
	}
    else die("mediaid missing");
    
?>

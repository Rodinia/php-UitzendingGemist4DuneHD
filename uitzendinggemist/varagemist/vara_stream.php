<?php
	error_reporting(E_WARNING);
	
	require_once dirname(__FILE__).'/../lib/playlist.php';
    require_once dirname(__FILE__).'/lib_vara.php';
	
	$type = isset($_GET['type']) ? $_GET['type'] : 'asx';

	$streamurl = null;
	if( isset($_GET['mediaid']) )
	{
		$mediaid = $_GET['mediaid'];

		$configXmlUrl = makeConfigXmlUrl($mediaid);
        $configXml = getConfigXml($configXmlUrl);
		$videoConfigUrl = $configXml['file'];
		$configVideo = getVideoConfigXml($videoConfigUrl);
		$streamurl = $configVideo['location'];
		
		// Switch to HQ stream (720x400 1.5 MBit/sec)
		$streamurl = str_replace('.mp4', '-hq.mp4', $streamurl);
        
        if($type == 'dune')
        {
            echo "# mediaid: $mediaid\n";
            echo "# url config.xml: $configXmlUrl\n";
            echo "# url config_video.xml: $videoConfigUrl\n";
            echo "# url media location: $streamurl\n";
        }
		
        if(!$streamurl) die('streamurl cannot be null');
        
        // Check for redirects
        $streamurl = followRedirects($streamurl, $contentType, 2);
        
        // Generate playlist (playlist.php)
		writePlaylist($streamurl, $type, $contentType);
	}
    else die("mediaid missing");
    
?>

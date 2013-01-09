<?php
	#Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);

	require_once '../../lib/dune.php';
    require_once '../../lib_ugemist.php';
    require_once '../lib_vara.php';

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
    
    // Set to HQ
    if(isset($_GET['hq']))
    {
        // Change to H.264 720x400, 1.4 MBit
        $mediaLocation = str_replace('.mp4', '-hq.mp4', $mediaLocation);
        echo "# url HQ media location: $mediaLocation\n";
    }

	// Check for redirects
	$mediaLocation = followRedirects($mediaLocation, $contentType, 1);

	dunePlay($mediaLocation, 'video/mp4');

 ?>
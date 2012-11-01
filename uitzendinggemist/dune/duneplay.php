<?php
	#Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ERROR);

	include '../common.php';
	include 'dune.php';

	header('Content-type: text/plain');

	$epiid = $_GET['epiid'];

	$sessionKey = getSessionKey();
	// wvc1:std 1.1 MBit MP4/H.264 640x360
	// mov|std 1.0 MBit MP4/H.264 640x360
	// wmv|bb 500 kbit WMV 320x180
	foreach(array("mov|std", "wvc1:std", "wmv|std", "mov|bb", "wvc1:bb", "wmv|bb", "mov|sb", "wvc1:sb", "wmv|sb") as $fq)
	{
		$fq = explode('|', $fq);
		$streamurl = getStreamUrl($epiid, $sessionKey, $fq[0], $fq[1]);
		if($streamurl) break;
	}

	if($streamurl == null)
	{
		duneError("No valid stream found");
		exit;
	}

	// Eliminate redirects

	// Check for redirects
	$streamurl = followRedirects($streamurl, $contentType);

	if($streamurl == null)
	{
		duneError("To many redirects.");
		exit;
	}

	dunePlay($streamurl, $contentType);

 ?>
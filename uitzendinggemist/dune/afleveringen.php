<?php
	#Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ERROR);

	header('Content-type: text/plain; charset=utf-8'); // is this the best charset?

    include '../common.php';
	include 'dune.php';

	echo "use_icon_view = no\n";
    	
    $programma = $_GET['program']; // eg: '244-huisje-boompje-beestje';
	$pageOffset = $_GET['page'];
	if(!$pageOffset) $pageOffset = 1;

	$max_pages = 3;

	$episodes = wgetEpisodes($programma, $max_pages, $pageOffset);
	$num=0;

	$sessionKey = getSessionKey();


	foreach($episodes as $episode)
	{
		$epiid = $episode['epiid'];
		//$url = 'http://dune-gemist.xoomsite.com/?epiid='.$epiid.'&stap=3&dune=true';
		$url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/duneplay.php?epiid='.$epiid;
		writeItem($num++, $episode['caption'], $url, 'play');
		echo "\n";
	}

	$pageOffset += $max_pages;
	$nextPageUrl = 'dune_http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?program='.urlencode($programma).'&page='.$pageOffset;
	writeItem($num++, 'Meer...', $nextPageUrl, 'browse');

	//var_dump($_SERVER);

?>
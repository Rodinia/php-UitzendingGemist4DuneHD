use_icon_view = exlist
paint_captions = yes
async_icon_loading = yes
paint_help_line = no
paint_path_box = no
paint_icon_selection_box = yes
paint_content_box_background = no
paint_scrollbar = no

<?php
	#Enable display errors
	ini_set('display_errors',1);
	error_reporting(E_ERROR);

	header('Content-type: text/plain; charset=utf-8'); // is this the best charset?

	include '../common.php';
	include 'dune.php';

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
		$url = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/duneplay?epiid='.$epiid;
		writeItem($num++, $episode['caption'], 'dune_'.$url, 'play');
		echo "\n";
	}

	$pageOffset += $max_pages;
	$nextPageUrl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?program='.urlencode($programma).'&page='.$pageOffset;
	writeItem($num++, 'Next...', $nextPageUrl, 'browse');

	//var_dump($_SERVER);

?>
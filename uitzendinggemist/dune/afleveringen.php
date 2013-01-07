<?php
	#Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);

	header('Content-type: text/plain; charset=utf-8'); // is this the best charset?
    
    include_once '../lib_ugemist.php';
	include 'dune.php';

	echo "use_icon_view = no\n";
    	
    $program_id = $_GET['programid']; // eg: '244-huisje-boompje-beestje';
	$when = $_GET['when'];
	
	$pageOffset = $_GET['page'];
	if(!$pageOffset) $pageOffset = 1;

	$max_pages = 3;


	function writeEpisodes($url_ug, $baseurl, $max_pages, $pageOffset)
	{
		$episodes = wgetEpisodes($url_ug, $max_pages, $pageOffset);
		
        $num = 0;
		foreach($episodes as $episode)
		{
			$epiid = $episode['epiid'];
			//$url = 'http://dune-gemist.xoomsite.com/?epiid='.$epiid.'&stap=3&dune=true';
			$url = 'dune_'.$baseurl.'/duneplay.php?epiid='.$epiid;
			echo "\n";
			writeItem($num++, $episode['caption'], $url, 'play');
		}
	}

    $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    $imgdir=dirname($baseurl).'/img';
	echo "background_order=before_all\n";
	echo "background_path=$imgdir/background.jpg\n";

	if($program_id)
	{
		$url_ug = 'http://www.uitzendinggemist.nl/programmas/'.urlencode($program_id);
		
		writeEpisodes($url_ug, $baseurl, $max_pages, $pageOffset);

		$pageOffset += $max_pages;
		$nextPageUrl = 'dune_'.$baseurl.'?program='.urlencode($program_id).'&page='.$pageOffset;
		writeItem($num++, 'Meer...', $nextPageUrl, 'browse');
	}
    else if($when)
    {
        $url_ug = "http://www.uitzendinggemist.nl/weekarchief/$when?display_mode=detail";
		writeEpisodes($url_ug, $baseurl, $max_pages, $pageOffset);
    }

?>
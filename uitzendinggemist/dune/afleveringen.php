<?php
	#Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);

	header('Content-type: text/plain; charset=utf-8'); // is this the best charset?
    
    require_once '../lib_ugemist.php';
	require_once '../lib/dune.php';

	echo "use_icon_view = no\n";
    	
    $program_id = $_GET['programid']; // eg: '244-huisje-boompje-beestje';
	$when = $_GET['when'];
	
	$pageOffset = $_GET['page'];
	if(!$pageOffset) $pageOffset = 1;

	$max_pages = 3;


	function writeEpisodes($url_ug, $baseurl, $max_pages, $pageOffset)
	{
		$episodes = wgetEpisodes($url_ug, $max_pages, $pageOffset);
		echo "# url_ug = $url_ug\n";
		echo "# pageoffset = $pageOffset\n";
		
        $num = 0;
		foreach($episodes as $episode)
		{
			$localepiid = $episode['localepiid'];
			//$url = 'http://dune-gemist.xoomsite.com/?epiid='.$epiid.'&stap=3&dune=true';
			$url = 'dune_'.$baseurl.'/duneplay.php?localepiid='.$localepiid;
			echo "\n";
			writeItem($num++, $episode['caption'], $url, 'play');
		}
		return $num;
	}

    $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    $imgdir=dirname($baseurl).'/img';
	echo "background_order=before_all\n";
	echo "background_path=$imgdir/background.jpg\n";

	if($program_id)
	{
		$url_ug = 'http://www.uitzendinggemist.nl/programmas/'.urlencode($program_id);
		
		$num = writeEpisodes($url_ug, $baseurl, $max_pages, $pageOffset);

		$pageOffset += $max_pages;
		$nextPageUrl = 'dune_'.$baseurl.'/afleveringen.php?programid='.urlencode($program_id).'&page='.$pageOffset;
		echo "\n";
		writeItem($num++, 'Meer...', $nextPageUrl, 'browse');
	}
    else if($when)
    {
        $url_ug = "http://www.uitzendinggemist.nl/weekarchief/$when?display_mode=detail";
		writeEpisodes($url_ug, $baseurl, $max_pages, $pageOffset);
    }

?>
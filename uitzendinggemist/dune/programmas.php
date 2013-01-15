<?php
    header('Content-type: text/plain; charset=utf-8');

    require_once '../lib/lib_ugemist.php';
	require_once '../lib/dune.php';
        
    $pageOffset = isset($_GET['page']) ? $_GET['page'] : 1;
    $maxPages = 3;
    $num = 0;
    
    error_reporting(E_ALL);
        
	function listSeries($series)
	{
	//echo "listSeries() num: $num\n";
        $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
		$num = 0;
        foreach ($series as $serie)
		{
			$programId = substr($serie['href'], 12);
			$title = $serie['name'];
			$imgsrc =trim($serie['data-images'], '[]"');
            $imgsrc = str_replace('140x79','250x80', $imgsrc);
            $url = $baseurl.'/afleveringen.php?programid='.urlencode($programId);
			
			echo "\n";
			writeIcon($num++, $title, 'dune_'.$url, $imgsrc);
			//echo "num: $num\n";
		}
        return $num;
	//echo "num: $num\n";
	}

	//echo "Uit listSeries() num: $num\n";
	
	$baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    $imgdir=dirname($baseurl).'/img';
	echo "background_order=before_all\n";
	echo "background_path=$imgdir/background.jpg\n";
	echo "use_icon_view = yes\n";
	echo "paint_captions = yes\n";
	echo "media_action = browse\n";
	echo "num_cols = 3\n";
	echo "async_icon_loading = yes\n";
    
    if( isset($_GET['suffix']) )
    {
        $suffix = $_GET['suffix'];
	    echo "# Programma's: $suffix\n";
        $series = wgetProgramsAZ($suffix, $maxPages, $pageOffset);
	$num = listSeries($series);
        
        echo "\n";
	//echo "(if series) num: $num\n";
        $pageOffset += $maxPages;
		$nextPageUrl = 'dune_'.$baseurl.'/programmas.php?suffix='.$suffix.'&page='.$pageOffset;
		writeItem($num++, 'Meer...', $nextPageUrl, 'browse');
		//echo "num: $num\n";
    }
	else if( isset($_GET['type']) )
	{
        $type = $_GET['type'];
        echo "# Programma's op $type\n";
		$urlug = 'http://www.uitzendinggemist.nl/'.$type.'?display_mode=detail';
        $series = wgetPrograms($urlug, $maxPages, $pageOffset);
        $num = listSeries($series);
        
        echo "\n";
        $pageOffset += $maxPages;
		$nextPageUrl = 'dune_'.$baseurl.'/programmas.php?type='.$type.'&page='.$pageOffset;
		writeItem($num++, 'Meer...', $nextPageUrl, 'browse');
	}
	else if( isset($_GET['omroep']) )
	{
		$omroep = $_GET['omroep'];
        echo "# Omroep: $omroep\n";
        $ugurl = 'http://www.uitzendinggemist.nl/omroepen/'.$omroep.'?display_mode=detail-selected';
		$series = wgetPrograms($ugurl, $maxPages, $pageOffset);
		$num = listSeries($series);
        
        echo "\n";
        $pageOffset += $maxPages;
		$nextPageUrl = 'dune_'.$baseurl.'/programmas.php?omroep='.$omroep.'&page='.$pageOffset;
		writeItem($num++, 'Meer...', $nextPageUrl, 'browse');
	}
	else if(isset($_GET['genre']))
    {
        $genre = $_GET['genre'];
        echo "# Genre: $genre\n";
        $ugurl = 'http://www.uitzendinggemist.nl/genres/'.$genre.'?display_mode=detail-selected';
        $series = wgetPrograms($ugurl, $maxPages, $pageOffset);
        $num = listSeries($series);

        echo "\n";
        $pageOffset += $maxPages;
                $nextPageUrl = 'dune_'.$baseurl.'/programmas.php?genre='.$genre.'&page='.$pageOffset;
                writeItem($num++, 'Meer...', $nextPageUrl, 'browse');
    }
?>

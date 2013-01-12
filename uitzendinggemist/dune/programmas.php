<?php
	header('Content-type: text/plain; charset=utf-8');
    
    require_once '../lib_ugemist.php';
	require_once '../lib/dune.php';
	 
    header('Content-type: text/plain; charset=utf-8');
    $pageOffset = isset($_GET['page']) ? $_GET['page'] : 1;
    $maxPages = 3;
    
    error_reporting(E_ALL);
        
	function listSeries($series)
	{
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
		}
        return $num;
	}
	
	$baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    $imgdir=dirname($baseurl).'/img';
	echo "background_order=before_all\n";
	echo "background_path=$imgdir/background.jpg\n";
?>use_icon_view = yes
paint_captions = yes
media_action = browse
num_cols = 3
async_icon_loading = yes
<?php	
    
    if( isset($_GET['suffix']) )
    {
        $suffix = $_GET['suffix'];
        echo "# Programma's: $suffix\n";
        $series = wgetProgramsAZ($suffix, $maxPages, $pageOffset);
		listSeries($series);
        
        echo "\n";
        $pageOffset += $maxPages;
		$nextPageUrl = 'dune_'.$baseurl.'/programmas.php?suffix='.$suffix.'&page='.$pageOffset;
		writeItem($num++, 'Meer...', $nextPageUrl, 'browse');
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

	


?>

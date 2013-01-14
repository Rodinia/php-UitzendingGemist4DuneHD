use_icon_view = no
<?php

	header('Content-type: text/plain; charset=utf-8');

    #Enable display errors
	error_reporting(E_WARNING);
	
	require_once '../../lib/dune.php';
	
    $what = $_GET['what'];
    
    $nr = 0;
    
    $baseurl = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    
    if(!isset($_GET['what']))
    {
            writeItem($nr++, "Deze week", $baseurl.'/?what=dezeweek', 'item');
            writeItem($nr++, "Favorieten", $baseurl.'/?what=favo', 'item');
            writeItem($nr++, "Recente programma's", $baseurl.'/?what=recprog', 'item');
            writeItem($nr++, "Giel Rubrieken", $baseurl.'/?what=giel.rubrieken', 'item');
            exit;
    }

    require_once '../lib_vara.php';
    
    $what = $_GET['what'];
    
    if($what=='dezeweek')
    {
        foreach(getDezeWeek() as $item)
        {
            vara_play($nr++, $item['caption'], $item['id']);
        }
        exit;
    }
    if($what=='favo')
    {
        require_once '../../lib/lib_store_xml.php';
        
        foreach(readFavorites('vara') as $programma)
        {
            vara_play($nr++, $programma['caption'], $programma['id']);
        }
    }
    else if($what=='recprog')
    {
        foreach(getVaraProgramList()->allProgramsAndSites as $program)
        {
            $url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/?what=program&url='.urlencode($program->url);
            writeItem($nr++, $program->title, $url, 'item');
        }
    }
	else if($what=='program')
	{
		$url = $_GET['url'];
		foreach(getVaraProgramFragments($url) as $fragment)
		{
			vara_play($nr++, $fragment['caption'], $fragment['id'], 'play');
		}
	}
    else if($what=='giel.rubrieken')
	{
		require_once('../lib_giel.php');
        
        foreach(getGielRubrieken() as $rubriek)
		{
			$caption = $rubriek->getAttribute('title');
            $href = $rubriek->getAttribute('href');
            $rubriek = trim(substr($href, 10), "/");
            $url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/giel.php?rubriek='.urlencode($rubriek);
            echo "\n";
            writeItem($nr++, $caption, $url, 'item');
		}
	}

	function vara_play($nr, $title, $mediaid)
	{
		$url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/vara_play.php?mediaid='.$mediaid.'&hq=1';
        echo "\n";
		writeItem($nr, $title, $url, 'play');
 	}

?>
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
            writeItem($nr++, "Giel Rubrieken", $baseurl.'/giel.php', 'item');
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
            vara_play($nr++, $programma['title'], $programma['refid']);
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
    
	function vara_play($nr, $title, $mediaid)
	{
		$url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname(dirname($_SERVER['PHP_SELF'])).'/vara_stream.php?type=dune&mediaid='.$mediaid;
        echo "\n";
		writeItem($nr, $title, $url, 'play');
 	}

?>
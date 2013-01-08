use_icon_view = exlist
paint_captions = yes
async_icon_loading = yes
paint_help_line = no
paint_path_box = no
paint_icon_selection_box = yes
paint_content_box_background = no
paint_scrollbar = no

<?php

	header('Content-type: text/plain; charset=utf-8');

    #Enable display errors
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);
	
	require_once '../../lib/dune.php';
    require_once '../../lib/lib_favorites.php';
	require_once '../lib_vara.php';
	
    $what = $_GET['what'];
    
    $nr = 0;
    
    $baseurl = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    
    if(!$what)
    {
            writeItem($nr++, "Deze week", $baseurl.'/?what=dezeweek', 'item');
            writeItem($nr++, "Favorieten", $baseurl.'/?what=favo', 'item');
            writeItem($nr++, "Recente programma's", $baseurl.'/?what=recprog', 'item');
            exit;
    }
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
        foreach(readFavorites('../favorieten_vara.xml') as $programma)
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

	function vara_play($nr, $title, $mediaid)
	{
		$url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/?mediaid='.$mediaid;
        writeItem($nr, $title, $url, 'play');
 	}

?>
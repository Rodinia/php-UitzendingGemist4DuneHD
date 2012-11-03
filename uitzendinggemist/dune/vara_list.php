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
	error_reporting(E_ALL);
	
	include 'dune.php';
    include '../common.php';
    include '../lib_vara.php';

    $nr = 0;
    
    foreach(getRecent() as $item)
    {
        vara_play($nr++, $item['caption'], $item['id']);
    }
    
	foreach(readFavorites('../favorieten_vara.xml') as $programma)
	{
		vara_play($nr++, $programma['caption'], $programma['id']);
	}

	function vara_play($nr, $title, $mediaid)
	{
		$url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/vara_play.php?mediaid='.$mediaid;
        writeItem($nr, $title, $url, 'play');
 	}

?>
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

	header('Content-type: text/plain; charset=utf-8');

	include 'dune.php';

	$nr = 0;
	vara_play($nr++, 'Bang Bang Boom Boom', 187251);
	vara_play($nr++, 'Beth Hart - I need a dollar', 187252);
	vara_play($nr++, 'Beth Hart - Baddest', 187258);

	function vara_play($nr, $title, $mediaid)
	{
		$url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/vara_play.php?mediaid='.$mediaid;
        writeItem($nr, $title, $url, 'play');
 	}

?>
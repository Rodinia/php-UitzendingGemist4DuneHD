use_icon_view = no
<?php

	header('Content-type: text/plain; charset=utf-8');

    #Enable display errors
	
	require_once '../../lib/dune.php';
	
    
    $nr = 0;
    
    $baseurl = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
    
	if( isset($_POST['do']) )
    {
        $do = $_POST['do'];
        $programId = $_POST['programid'];
        if($do == 'delete')
        {
            deleteFromFavorite('vara', $programId);
        }
        else if($do == 'save')
        {
            echo "do=$do\n";
        }
        header( 'Location: '.$_POST['URL'] ) ;
    }
	
    if(!isset($_GET['what']))
    {
		$what = $_GET['what'];
		writeItem($nr++, "Deze week", $baseurl.'/?what=dezeweek', 'item');
		writeItem($nr++, "Favorieten", $baseurl.'/?what=favo', 'item');
		writeItem($nr++, "Recente programma's", $baseurl.'/?what=recprog', 'item');
		writeItem($nr++, "Giel", $baseurl.'/giel.php', 'item');
		exit;
    }
	$what = $_GET['what'];
	
    require_once '../lib_vara.php';
    
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
        require_once '../../lib/lib_storage.php';
        echo "use_icon_view = exlist\n";
        echo "async_icon_loading = yes\n";
    
        foreach(readFavorites('vara', 'media') as $favo)
        {
            vara_play($nr++, $favo['title'], $favo['refid'], $favo['img']);
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
    
	function vara_play($nr, $title, $mediaid, $imgsrc = false)
	{
		$url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname(dirname($_SERVER['PHP_SELF'])).'/vara_stream.php?type=dune&mediaid='.$mediaid;
        echo "\n";
		echo "# img=$imgsrc\n";
        writeItem($nr, $title, $url, 'play', $imgsrc);
 	}

?>
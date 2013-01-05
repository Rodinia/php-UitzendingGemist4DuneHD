<?php
	include_once '../lib_ugemist.php';
    include_once 'dune.php';
    
        #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);

    header('Content-type: text/plain; charset=utf-8');

    echo "use_icon_view = no\n";
        
	function listSeries($elements)
	{
        $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
		$num = 0;
        foreach ($elements as $element)
		{
			$href=$element->getAttribute('href');
			$programId=substr($href, 12);
			$url = $baseurl.'/afleveringen.php?program='.urlencode($programId);
			writeItem($num++, $element->nodeValue, 'dune_'.$url);
			
		}
	}
	
	$suffix = $_GET['suffix'];
	$type = $_GET['type'];
	$omroep = $_GET['omroep'];

	if($suffix)
    {
        echo "# $suffix\n";
        $elements = wgetProgramsAZ($suffix);
		listSeries($elements);
    }
	else if($type)
	{
 
		if($type == "zapp")
		{
			echo "# Programma's op Zapp\n";
			$elements = wgetPrograms('http://www.uitzendinggemist.nl/zapp', 'category-series');
			listSeries($elements);
		}
		else if($type == "zappelin")
		{
			echo "# Programma's op Zappelin\n";
			$elements = wgetPrograms('http://www.uitzendinggemist.nl/zappelin', 'category-series');
			listSeries($elements);
		}
	}
	else if($omroep)
	{
		echo "# Omroep: $omroep\n";
		$elements = wgetPrograms('http://www.uitzendinggemist.nl/omroepen/'.$omroep, 'category-series');
		listSeries($elements);
	}

	


?>

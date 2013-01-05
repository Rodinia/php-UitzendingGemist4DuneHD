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
			$nlimg=$element->getElementsByTagName('img');
			$title=$element->getAttribute('title');
			$imgsrc='';
			if($nlimg->length>0)
			{
				$dataimages = $nlimg->item(0)->getAttribute('data-images');
				$imgsrc=trim($dataimages, '[]"');
				$imgsrc= str_replace('140x79','280x100', $imgsrc);
				//echo "<p>dataimages=$imgsrc</p>\n";
			}
			$url = $baseurl.'/afleveringen.php?program='.urlencode($programId);
			
			echo "\n";
			writeIcon($num++, $title, 'dune_'.$url, $imgsrc);
		}
	}
	
	$suffix = $_GET['suffix'];
	$type = $_GET['type'];
	$omroep = $_GET['omroep'];

?>use_icon_view = yes
paint_captions = yes
media_action = browse
num_cols = 3
async_icon_loading = yes
<?php	
	
	if($suffix)
    {
        echo "# Programma's: $suffix\n";
        $elements = wgetProgramsAZ($suffix);
		listSeries($elements);
    }
	else if($type)
	{
 
		if($type == "zapp")
		{
			echo "# Programma's op Zapp\n";
			$elements = wgetPrograms('http://www.uitzendinggemist.nl/zapp?display_mode=detail', 'category-series');
			listSeries($elements);
		}
		else if($type == "zappelin")
		{
			echo "# Programma's op Zappelin\n";
			$elements = wgetPrograms('http://www.uitzendinggemist.nl/zappelin?display_mode=detail', 'category-series');
			listSeries($elements);
		}
	}
	else if($omroep)
	{
		echo "# Omroep: $omroep\n";
		$elements = wgetPrograms('http://www.uitzendinggemist.nl/omroepen/'.$omroep, 'category-series', 'series series-image');
		listSeries($elements);
	}

	


?>

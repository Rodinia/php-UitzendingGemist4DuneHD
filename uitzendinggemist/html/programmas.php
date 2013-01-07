<!DOCTYPE html>
<html>
<head>
  <title>Programmas</title>
  <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	include_once '../lib_ugemist.php';

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);
		
	//header('Content-type: text/plain');
	
  	function showDuneLink()
	{
		echo '<a href="../dune/programmas?'.$_SERVER["QUERY_STRING"].'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>'."\n";
	}
	
	function listSeries($elements)
	{
		echo "<table class=\"touch\">\n";
        //echo "<tr><th>Programma</th><th>ID</th><th>Externe link</th></tr>\n";

		$cols = 3;
		$nr = 0;
		
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

			if($nr++%$cols == 0)
			{
				if($nr>1)
					echo "</tr>\n";
				echo "<tr>\n";
			}			
			
			echo '<td><a href="afleveringen.php?programid='.urlencode($programId).'">';
            if(strlen($imgsrc)>0)
				echo '<img src="'.$imgsrc.'" />';
            echo $title;
			echo '</a></td>';
        }

        echo '<tr>';
        echo "</table>\n";
	}
	
	$suffix = $_GET['suffix'];
	$type = $_GET['type'];
	$omroep = $_GET['omroep'];

	if($suffix)
    {
        echo "<h1>Programma lijst $suffix</h1>\n";
		showDuneLink();
        $elements = wgetProgramsAZ($suffix);
        listSeries($elements);
    }
	else if($type)
	{
 
		if($type == "zapp")
		{
			echo "<h1><img src=\"http://assets.www.uitzendinggemist.nl/assets/header/zapp-header.jpg\" alt=\"Programma's op Zapp\"/></h1>\n";
			showDuneLink();
			$elements = wgetPrograms('http://www.uitzendinggemist.nl/zapp?display_mode=detail', 'category-series');
			listSeries($elements);
		}
		else if($type == "zappelin")
		{
			echo "<h1><img src=\"http://assets.www.uitzendinggemist.nl/assets/header/zappelin-header.jpg\" alt=\"Programma's op Zappelin\"/></h1>\n";
			showDuneLink();
			$elements = wgetPrograms('http://www.uitzendinggemist.nl/zappelin?display_mode=detail', 'category-series');
			listSeries($elements);
		}
	}
	else if($omroep)
	{
		echo "<h1>Omroep $omroep</h1>\n";
		showDuneLink();
		$elements = wgetPrograms('http://www.uitzendinggemist.nl/omroepen/'.$omroep, 'category-series', 'series series-image');
		listSeries($elements);
	}


?>

</body>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>Programmas</title>
  <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	include_once '../lib_ugemist.php';

    #Enable display errors
	error_reporting(E_ALL);
		
	$pageOffset = isset($_GET['page']) ? $_GET['page'] : 1;
    $maxPages = 3;
	
  	function showLinks($url_ug)
	{
		echo '<a href="'.$url_ug.'"><img src="img/ug-header-logo.png" alt="Uitzending Gemist: $program_id"/></a>'."\n";
		echo '<a href="../dune/programmas?'.$_SERVER["QUERY_STRING"].'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>'."\n";
	}
	
	function listSeries($series, $nextQuery)
	{
		echo "<table class=\"touch\">\n";
        //echo "<tr><th>Programma</th><th>ID</th><th>Externe link</th></tr>\n";

		$cols = 3;
		$nr = 0;
		
		foreach ($series as $serie)
        {
            $programId = substr($serie['href'], 12);
			$title = $serie['name'];
			
            $imgsrc =trim($serie['data-images'], '[]"');
            $imgsrc = str_replace('140x79','280x100', $imgsrc);
            
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
        echo '<tr><td colspan="3"><a href="?'.$nextQuery.'">Next Page</a></td></tr>';

        echo "</table>\n";
	}
	
	$suffix = isset($_GET['suffix']) ? $_GET['suffix'] : null;
	$type = isset($_GET['type']) ? $_GET['type'] : null;
	$omroep = isset($_GET['omroep']) ? $_GET['omroep'] : null;

	if($suffix)
    {
        echo "<h1>Programma lijst $suffix</h1>\n";
		showLinks('http://www.uitzendinggemist.nl/programmas/'.$suffix);
        $series = wgetProgramsAZ($suffix, $maxPages, $pageOffset);
        $pageOffset += $maxPages;
        listSeries($series, 'suffix='.$suffix.'&page='.$pageOffset);
    }
	else if($type)
	{
		echo "<h1><img src=\"http://assets.www.uitzendinggemist.nl/assets/header/$type-header.jpg\" alt=\"Programma's op $type\"/></h1>\n";
        $urlug = 'http://www.uitzendinggemist.nl/'.$type.'?display_mode=detail';
        showLinks($urlug);
        $series = wgetPrograms($urlug, $maxPages, $pageOffset);
        $pageOffset += $maxPages;
        listSeries($series, 'type='.$type.'&page='.$pageOffset);
	}
	else if($omroep)
	{
		echo "<h1>Omroep $omroep</h1>\n";
		$urlug = 'http://www.uitzendinggemist.nl/omroepen/'.$omroep.'?display_mode=detail-selected';
		showLinks($urlug);
		$elements = wgetPrograms($urlug, $maxPages, $pageOffset);
		listSeries($elements, 'omroep='.$omroep.'&page='.$pageOffset);
	}


?>

</body>
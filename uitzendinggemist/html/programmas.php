<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>Programmas</title>
  <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>
<form name="favorite" method="post" target="formresult" action="post_favorite.php">
    <input type="hidden" name="do" value="add"/>
    <input type="hidden" name="provider" value="uitzendinggemist"/>
	<input type="hidden" name="type" value="programma"/>
	<input type="hidden" name="refid"/>
    <input type="hidden" name="title"/>
    <input type="hidden" name="img"/>
</form>
<script type="text/javascript">
    function addToFavorites(refid, title, img)
    {
        var form = document.favorite;
        form.refid.value = refid;
        form.title.value = title;
        form.img.value = img;
        
        window.open('', 'formresult', 'scrollbars=no,menubar=no,height=200,width=400,resizable=yes,toolbar=no,status=no');
        
        form.submit();
    }
</script> 
<?php
	require_once '../lib/lib_ugemist.php';
    require_once '../lib/lib_storage.php';


    #Enable display errors
	error_reporting(E_ALL);
		
	$pageOffset = isset($_GET['page']) ? $_GET['page'] : 1;
    $maxPages = 6;
    
    $duneSerial = findSerialByIP();
	
  	function showLinks($url_ug)
	{
		global $duneSerial, $useMySQL;
		
 		echo '<a href="'.$url_ug.'"><img src="img/ug-header-logo.png" alt="Uitzending Gemist: $program_id"/></a>'."\n";
		echo '<a href="../dune/programmas.php?'.$_SERVER["QUERY_STRING"].'"><img src="img/dune_hd_logo.png" alt="Dune HD" class="favorite"/></a>'."\n";
	
        if($duneSerial)
		{
			echo '<div class="success"><tr><td>Dune HD Gevonden, serie nummer: '.$duneSerial.'</div>'."\n";
		}
		else if($useMySQL)
		{
			echo '<div class="error">Je kunt pas favorieten aanmaken, nadat je eerst, met je Dune HD Media player, verbonden bent geweest met deze Uitzending Gemist App.</div>';
			echo 'Mogelijk moet je je browser <a href="register.php">registreren</a>.';
		}
    }
	
	function listSeries($series, $nextQuery)
	{
		global $duneSerial, $useMySQL;
        
        //echo "<p>Series:".var_dump($series)."</p>\n";

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
			echo '</a>';
            if(!$useMySQL || $duneSerial)
            {
                echo '<a href="#" id="bottle" onclick="addToFavorites(\''.$programId.'\',\''.$title.'\',\''.$imgsrc.'\');return false;" >';
                echo '<img src="img/add_to_favorite_22.png" alt="Add to favorite" class="actionIcon"/>';
                echo '</a>';
            }
        }
        echo '<tr><td colspan="3"><a href="?'.$nextQuery.'">Next Page</a></td></tr>';

        echo "</table>\n";
	}

    if( isset($_GET['search']) )
    {
        $search = $_GET['search'];
        echo '<h1><img src="img/uitzendinggemist_60.png"/>Uitzending Gemist</h1>';
        echo '<div id="searchbar">';
		echo '<form method="get" action="programmas.php" id="wideZoek">';
		echo '<input type="text" name="search" class="zoekterm" value="'.$search.'" /> <input type="image" src="img/zoekbutton.png" class="zoekbutton" alt="Zoek" />';
		echo '</form>';
        echo '</div>';
        
        $urlug = 'http://www.uitzendinggemist.nl/zoek?q='.urlencode($search);
        
        $maxPages = 1;
        $series = wgetSearchPrograms($urlug, $maxPages, $pageOffset);
        $pageOffset += $maxPages;
        listSeries($series, '&page='.$pageOffset);
        
        showLinks($urlug);
    }
	else if( isset($_GET['suffix']) )
    {
        $suffix = $_GET['suffix'];
        echo "<h1>Programma lijst $suffix</h1>\n";
		showLinks('http://www.uitzendinggemist.nl/programmas/'.$suffix);
        $series = wgetProgramsAZ($suffix, $maxPages, $pageOffset);
        $pageOffset += $maxPages;
        listSeries($series, 'suffix='.$suffix.'&page='.$pageOffset);
    }
	else if( isset($_GET['type']) )
	{
		$type = $_GET['type'];
        echo "<h1><img src=\"http://assets.www.uitzendinggemist.nl/assets/header/$type-header.jpg\" alt=\"Programma's op $type\"/></h1>\n";
        $urlug = 'http://www.uitzendinggemist.nl/'.$type.'?display_mode=detail';
        showLinks($urlug);
        $series = wgetPrograms($urlug, $maxPages, $pageOffset);
        $pageOffset += $maxPages;
        listSeries($series, 'type='.$type.'&page='.$pageOffset);
	}
	else if( isset($_GET['omroep']) )
	{
		$omroep = $_GET['omroep'];
        echo "<h1>Omroep $omroep</h1>\n";
		$urlug = 'http://www.uitzendinggemist.nl/omroepen/'.$omroep.'?display_mode=detail-selected';
		showLinks($urlug);
		$elements = wgetPrograms($urlug, $maxPages, $pageOffset);
        $pageOffset += $maxPages;
		listSeries($elements, 'omroep='.$omroep.'&page='.$pageOffset);
	}
    else if( isset($_GET['genre']) )
    {
        $genre = $_GET['genre'];
        echo "<h1>Genre $genre</h1>\n";
        $urlug = 'http://www.uitzendinggemist.nl/genres/'.$genre.'?display_mode=detail-selected';
        //$urlug = 'http://www.uitzendinggemist.nl/genres/comedy';
        showLinks($urlug);
        $elements = wgetPrograms($urlug, $maxPages, $pageOffset);
        listSeries($elements, 'genre='.$genre.'&page='.$pageOffset);
    }
?>
</body>
</html>

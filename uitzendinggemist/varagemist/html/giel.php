<?php

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ALL);

    require_once '../lib_giel.php';
    require_once '../lib_vara.php';
  
    
    function dune_url($mediaid)
	{
		return '../dune/vara_play.php?mediaid='.$mediaid;
	}
    
?><html>
<head>
  <title>GIEL</title>
  <link href="varagemist.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
   <h1><img src="../img/giel-logo.png" alt="GIEL"></h1>
   
<?php

    function writeDuneLink($rubriek = null)
    {
        if($rubriek)
		{
			echo '<a href="../dune/giel.php?rubriek='.$rubriek.'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>';
			echo '<a href="http://giel.vara.nl/rubrieken/'.$rubriek.'/">giel.vara.nl/rubrieken/'.$rubriek.'/</a>';
		}
		else
		{
			echo '<a href="../dune/giel.php"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>';
			echo '<a href="http://giel.vara.nl/rubrieken/">giel.vara.nl/rubrieken/</a>';
		}
		echo "\n";
	}
	
	if( isset($_GET['rubriek']) )
	{
		// GIEL Rubriek
		
		$rubriek = $_GET['rubriek'];
    
		writeDuneLink($rubriek );
		echo "<table>\n";
		foreach(getCarouselItems($rubriek) as $li)
		{
			$a = $li->getElementsByTagName('a')->item(0);
			$href = $a->getAttribute('href');
			$caption = $li->getElementsByTagName('div')->item(0)->nodeValue;
			$media_id=explode( "/", $href);
			$media_id=$media_id[3];
			write_vara_play_table_row($caption, $media_id);
		}
	   echo "</table>\n";
	}
	else
	{
		// GIEL Rubrieken
		
		require_once('../lib_giel.php');
		
		writeDuneLink();
		echo "<ul>\n";
		foreach(getGielRubrieken() as $rubriek)
		{
			$caption = $rubriek->getAttribute('title');
			$href = $rubriek->getAttribute('href');
			$rubriek = trim(substr($href, 10), "/");
			$url = 'giel.php?rubriek='.urlencode($rubriek);
			echo '<li><a href="'.$url. '">'.$caption.'</a></li>'."\n";
		}
		echo "</ul>\n";
	}
    
    
 ?> 
 </body>
</html>




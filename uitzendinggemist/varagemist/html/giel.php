<?php

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);

    require_once '../lib_giel.php';
    require_once '../lib_vara.php';
  
    function vara_play($title, $mediaid)
	{
		$configXmlUrl = makeConfigXmlUrl($mediaid);
        $configXml = getConfigXml($configXmlUrl);
		$videoConfigUrl = $configXml['file'];
		$configVideo = getVideoConfigXml($videoConfigUrl);
		$mediaLocation = $configVideo['location'];
		
		// Switch to HQ stream (720x400 1.5 MBit/sec)
		$mediaLocation = str_replace('.mp4', '-hq.mp4', $mediaLocation);
		
		$asxUrl = '../../asx.php?streamurl='.urlencode($mediaLocation);
		
		echo "<tr>\n";
        echo '<td>'.$title.'</td>';
        echo '<td><a href="'.$asxUrl.'"><img alt="play" src="../../html/img/button-play-icon_32.png"/></a></td>';
        echo '<td><a href=http://omroep.vara.nl/media/'.$mediaid.'>omroep.vara.nl</a></td>';
		echo '<td><a href="'.dune_url($mediaid).'"><i>Dune</i></a></td>';
        echo "</tr>\n";
	}
   
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
			vara_play($caption, $media_id, 'play');
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




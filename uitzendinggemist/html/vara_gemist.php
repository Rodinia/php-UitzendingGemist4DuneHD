<?php

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ALL);

    include_once '../lib_vara.php';
 	include_once '../lib_ugemist.php';
   	include_once '../lib_favorites.php';
  
    function vara_play($title, $mediaid)
	{
		$configXmlUrl = makeConfigXmlUrl($mediaid);
        $configXml = getConfigXml($configXmlUrl);
		$videoConfigUrl = $configXml['file'];
		$configVideo = getVideoConfigXml($videoConfigUrl);
		$mediaLocation = $configVideo['location'];
		
		echo "<tr>\n";
        echo '<td><a href="'.$mediaLocation.'">'.$title.'</a></td>';
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
  <title>VARA Gemist</title>
  <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
   <h1>VARA Gemist</h1>
   <a href="../dune/vara_list.php"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>
   
   <h2>Deze Week</h2>
   <a href="../dune/vara_list.php?what=dezeweek"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>
   <table>
 <?php
    
    foreach(getDezeWeek() as $item)
    {
        vara_play($item['caption'], $item['id']);
    }
 ?>
   </table>
   
   <h2>Favorieten</h2>
   <a href="../dune/vara_list.php?what=favo"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>
   <p>Configureer met <a href="../favorieten_vara.xml">favorieten_vara.xml</a>.</p>
   <table>
 <?php
    foreach(readFavorites('../favorieten_vara.xml') as $programma)
	{
		vara_play($programma['caption'], $programma['id']);
	}
 ?>
   </table>
   
   <h2>Programma's</h2>
   <a href="../dune/vara_list.php?what=recprog"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>
   <table>
 <?php
 	
	$json = getVaraProgramList();
    echo "<pre>";
	//var_dump($json);
    echo "</pre>";
	
	foreach(getVaraProgramList()->allProgramsAndSites as $program)
    {
		echo "<pre>";
		//var_dump($program);
		echo "</pre>";
		$id=substr($program->url, 28);str_replace('http://omroep.vara.nl/media/', '', $program->url);
        echo "<tr>\n";
        echo '<td><a href="vara_programma.php?url='.urlencode($program->url).'">'.$program->title.'</a></td>';
        //echo '<td>'.$program->tvChannelIndex || "".'</td>';
        //echo '<td>'.$program->radioChannelIndex || "".'</td>';
		echo '<td><a href="'.$program->url.'">omroep.vara.nl</a></td>';
        echo "</tr>\n";
    }
	
	
 ?>
   </table>
   
   <h2>Dune</h2>  
   <p>
        <a href="../dune/vara_list.php">VARA Gemist Index for Dune</a>
   </p>  
  
  </body>
</html>




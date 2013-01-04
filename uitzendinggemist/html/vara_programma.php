<?php

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ALL);

    include_once '../lib_vara.php';
 	include_once '../common.php';
    
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
   
    $programma = $_GET['url'];
    
?><html>
<head>
  <title>VARA Gemist</title>
  <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
   <h1>VARA Gemist</h1>
   
   <h2>???</h2>
   <table>
 <?php
    
    foreach(getVaraProgramFragments($programma) as $fragment)
    {
        vara_play($fragment['caption'], $fragment['id']);
    }
 ?>
 
  </body>
</html>




<?php

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ERROR);

    include '../lib_vara.php';
    
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
</head>

<body>

   <h1>VARA Gemist</h1>
   
   <h2>Dune</h2>
   <table>
 <?php
 	include '../common.php';
    
    foreach(getRecent() as $item)
    {
        vara_play($item['caption'], $item['id']);
    }
 ?>
   </table>
   
   <h2>Favorieten</h2>
   <p>Configureer met <a href="../favorieten_vara.xml">favorieten_vara.xml</a>.</p>
   <table>
 <?php
    foreach(readFavorites('../favorieten_vara.xml') as $programma)
	{
		vara_play($programma['caption'], $programma['id']);
	}
 ?>
   </table>
   <h2>Dune</h2>  
   <p>
        <a href="../dune/vara_list.php">VARA Gemist Index for Dune</a>
   </p>  
  
  </body>
</html>




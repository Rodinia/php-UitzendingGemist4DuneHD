<?php

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ERROR);

    include '../lib_vara.php';
    
	function vara_play($nr, $title, $mediaid)
	{
		$configXmlUrl = makeConfigXmlUrl($mediaid);
        $configXml = getConfigXml($configXmlUrl);
		$videoConfigUrl = $configXml['file'];
		$configVideo = getVideoConfigXml($videoConfigUrl);
		$mediaLocation = $configVideo['location'];
		
		echo '<li>';
        echo '<a href="'.$mediaLocation.'">'.$title.'</a>';
		echo ' <a href="'.dune_url($mediaid).'">(Dune)</a>';
		echo "</li>\n";
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
   
   <p>
     <i>Word nog aan gewerkt...</i>
   </p>
   
   <h2>Giel</h2>
   <ul>
 <?php
 	vara_play($nr++, 'Bang Bang Boom Boom', 187251);
	vara_play($nr++, 'Beth Hart - I need a dollar', 187252);
	vara_play($nr++, 'Beth Hart - Baddest', 187258);
 ?>
    </ul>
     
   <p>
        <a href="../dune/vara_list.php">VARA Gemist Index for Dune</a>
   </p>  
  
  </body>
</html>




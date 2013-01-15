<?php

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ALL);

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
  <title>VARA Gemist</title>
  <link href="varagemist.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
   <h1><img src="../img/vara-logo.png" alt="VARA">Gemist</h1>
<?php

    function writeDuneLink($what = null, $url = null)
    {
        $href='../dune/';
        if($what)
        {
            $href .='?what='.$what;
            if($url)
                $url .='&what='.$url;
        }
        echo '<a href="'.$href.'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>';
    }
    
    if(!isset($_GET['what']))
    {
        ?>
        <a href="../dune/"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>
		<a href="http://omroep.vara.nl/gemist">omroep.vara.nl/gemist</a>
        <ul>
            <li><a href="?what=dezeweek">Deze week</a></li>
            <li><a href="?what=favo">Favorieten</a></li>
            <li><a href="?what=recprog">Recente programma's</a></li>
            <li><a href="giel.php">Giel Rubrieken</a></li>
        <?php
    }
    else
    {
        require_once '../lib_vara.php';
        
        $what = $_GET['what'];
        if($what=='dezeweek')
        {
            echo "<h2>Deze Week</h2>\n";
            writeDuneLink($what);
            echo "<table>\n";
            foreach(getDezeWeek() as $item)
            {
                vara_play($item['caption'], $item['id']);
            }
            echo "</table>\n";
            exit;
        }
        
        if($what=='favo')
        {
            require_once '../../lib/lib_store_xml.php';
            
            echo "<h2>Favorieten</h2>\n";
            writeDuneLink($what);
            
            ?><p>Configureer met <a href="../favorieten_vara.xml">favorieten_vara.xml</a>.</p>
            <?php
           
            echo "<table>\n";
            foreach(readFavorites('vara') as $programma)
            {
                vara_play($programma['title'], $programma['id']);
            }
            echo "</table>\n";
        }
        else if($what=='recprog')
        {
            echo "<h2>Recente programma's</h2>\n";
            writeDuneLink($what);

            echo "<table>\n";
            foreach(getVaraProgramList()->allProgramsAndSites as $program)
            {
                $url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/?what=program&url='.urlencode($program->url);
                vara_play($program->title, $url, 'item');
            }
            echo "</table>\n";
        }
        else if($what=='program')
        {
            $url = $_GET['url'];
            writeDuneLink($what, $url);

            echo "<table>\n";
            foreach(getVaraProgramFragments($url) as $fragment)
            {
                vara_play($fragment['caption'], $fragment['id'], 'play');
            }
            echo "</table>\n";
        }
    }
?>

 <?php
 	/*
	$json = getVaraProgramList();
    echo "<pre>";
	//var_dump($json);
    echo "</pre>";
	
	foreach(getVaraProgramList()->allProgramsAndSites as $program)
    {
		echo "<pre>";
		//var_dump($program);
		echo "</pre>";
		$id=substr($program->url, 28);
		str_replace('http://omroep.vara.nl/media/', '', $program->url);
        echo "<tr>\n";
        echo '<td><a href="vara_programma.php?url='.urlencode($program->url).'">'.$program->title.'</a></td>';
        //echo '<td>'.$program->tvChannelIndex || "".'</td>';
        //echo '<td>'.$program->radioChannelIndex || "".'</td>';
		echo '<td><a href="'.$program->url.'">omroep.vara.nl</a></td>';
        echo "</tr>\n";
    }*/
	
	
 ?>

  </body>
</html>




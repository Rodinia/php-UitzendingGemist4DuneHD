<?php

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ALL & ~E_NOTICE);

    require_once '../lib_giel.php';
    require_once '../lib_vara.php';
  
    
    function dune_url($mediaid)
	{
		return '../dune/vara_play.php?mediaid='.$mediaid;
	}
    
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>GIEL</title>
  <link href="varagemist.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
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
	<form name="favorite" method="post" target="formresult" action="../../html/post_favorite.php">
		<input type="hidden" name="do" value="add"/>
		<input type="hidden" name="provider" value="vara"/>
		<input type="hidden" name="type" value="media"/>
		<input type="hidden" name="refid"/>
		<input type="hidden" name="title"/>
		<input type="hidden" name="img"/>
	</form>	
   <h1><img src="../img/giel-logo.png" alt="GIEL"></h1>
   
<?php

    function writeDuneLink($vara_path)
    {
        echo "<div>\n";
        echo '<a href="../dune/giel.php?'.$_SERVER['QUERY_STRING'].'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>'."\n";
        echo '<a href="http://giel.vara.nl/'.$vara_path.'">giel.vara.nl/'.$vara_path.'</a>'."\n";
        echo "</div>\n";
	}

    function writeCarouselItems($path)
    {
        echo "<table>\n";
        foreach(getCarouselItems('http://giel.vara.nl/'.$path) as $item)
        {
            $mediaid = $item['mediaid'];
            
            if($useMySQL)
            {
                require_once '../../lib/lib_storage.php';
                $duneSerial = findSerialByIP();
            }

            echo "<tr>\n";
            echo '<td>'.$item['title'].'</td>';
            echo '<td><a href="../vara_stream.php?type=asx&mediaid='.$mediaid.'"><img alt="play" src="../../html/img/windows_media_player_32.png" title="Play using Windows Media Player"/></a></td>';
            echo '<td><a href="../vara_stream.php?type=m3u&mediaid='.$mediaid.'"><img alt="play" src="../../html/img/media-playback-start_32.png" title="M3U Playlist"/></a></td>';
            echo '<td><a href="../vara_stream.php?type=redirect&mediaid='.$mediaid.'"><img alt="play" src="../../html/img/download.png" title="Direct link to media stream"/></a></td>';
            if(!$useMySQL || $duneSerial)
            {
                echo '<td>';
                echo '<a href="#" id="bottle" onclick="addToFavorites(\''.$mediaid.'\',\''.$item['title'].'\',null);return false;" >';
                echo '<img src="../../html/img/add_to_favorite_22.png" alt="Add to favorite" class="actionIcon" title="Voeg to aan favorieten"/>';
                echo '</a>';
                echo '</td>';
            }
            echo '<td><a href=http://omroep.vara.nl/media/'.$mediaid.'>omroep.vara.nl</a></td>';
            echo '<td><a href="../vara_stream.php?type=dune&mediaid='.$mediaid.'"><img src="../../html/img/dune_hd_logo.png" alt="Dune HD" title="Show DuneHD data"/></a></td>';
        }
        echo "</table>\n";
    }
	
	if( isset($_GET['rubriek']) ) // GIEL Rubriek
	{
		$rubriek = $_GET['rubriek'];
        $path = 'rubrieken/'.$rubriek.'/';
		writeDuneLink($path);
        writeCarouselItems($path);
	}
    else if( isset($_GET['artiest']) )
    {
        $artiest = $_GET['artiest'];
        writeDuneLink('artiesten/artiest-detail/artikel/'.$artiest);
        echo "<h3>$artiest</h3>\n"; // 'http://giel.vara.nl/artiesten/artiest-detail/artikel/'.$artiest
        writeCarouselItems('artiesten/artiest-detail/artikel/'.$artiest.'/');
    }
    else if( isset($_GET['artiesten']) )
    {
        writeDuneLink('artiesten');
        echo "<h3>Artiesten</h3>\n"; // 'http://giel.vara.nl/artiesten'
        echo "<ul>\n";
        foreach(getArtiesten() as $link)
        {
            $title = $link->nodeValue;
            $href = $link->getAttribute('href');
            $artiest = trim(substr($href, 33), "/");
            $url = 'giel.php?artiest='.urlencode( $artiest);
            echo '<li><a href="'.$url. '">'.$title.'</a></li>'."\n";
        }
        echo "</ul>\n";
    }
   	else if( isset($_GET['rubrieken']) )
	{
		// GIEL Rubrieken
		
		writeDuneLink('rubrieken/');
		echo "<h3>Rubrieken</h3>\n";
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

        echo '<li><a href="giel.php?artiesten">Artiesten</a></li>'."\n";
    }
    else
    {
       // GIEL main
       writeDuneLink('');
       echo "<ul>\n";
       echo '<li><a href="giel.php?rubrieken">Rubrieken</a></li>'."\n";
       echo '<li><a href="giel.php?artiesten">Artiesten</a></li>'."\n";
       echo "</ul>\n";
    }
 ?>
 </body>
</html>




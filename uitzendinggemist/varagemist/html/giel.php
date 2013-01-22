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
			$title = $li->getElementsByTagName('div')->item(0)->nodeValue;
			$mediaid=explode( "/", $href);
			$mediaid=$mediaid[3];
			//write_vara_play_table_row($caption, $media_id);
			
			if($useMySQL)
			{
				require_once '../../lib/lib_storage.php';
				$duneSerial = findSerialByIP();
			}
			
			echo "<tr>\n";
			echo '<td>'.$title.'</td>';
			echo '<td><a href="../vara_stream.php?type=asx&mediaid='.$mediaid.'"><img alt="play" src="../../html/img/windows_media_player_32.png" title="Play using Windows Media Player"/></a></td>';
			echo '<td><a href="../vara_stream.php?type=m3u&mediaid='.$mediaid.'"><img alt="play" src="../../html/img/media-playback-start_32.png" title="M3U Playlist"/></a></td>';
			if(!$useMySQL || $duneSerial)
			{
				echo '<td>';
				echo '<a href="#" id="bottle" onclick="addToFavorites(\''.$mediaid.'\',\''.$title.'\',null);return false;" >';
				echo '<img src="../../html/img/add_to_favorite_22.png" alt="Add to favorite" class="actionIcon" title="Voeg to aan favorieten"/>';
				echo '</a>';
				echo '</td>';
			}
			echo '<td><a href=http://omroep.vara.nl/media/'.$mediaid.'>omroep.vara.nl</a></td>';
			echo '<td><a href="../vara_stream.php?type=dune&mediaid='.$mediaid.'"><img src="../../html/img/dune_hd_logo.png" alt="Dune HD" title="Show DuneHD data"/></a></td>';
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




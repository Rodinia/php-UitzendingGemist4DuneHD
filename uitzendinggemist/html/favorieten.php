<?php
    
    #Enable display errors
	error_reporting(E_WARNING);
    require_once '../lib/lib_storage.php';
	
	if(isset($_POST['do']))
    {
		$message = "Do: $do";
        $do = $_POST['do'];
		require_once dirname(__FILE__).'/../lib/lib_storage.php';
        
		$provider = $_POST['provider'];
        $type = $_POST['type'];
        $refid = $_POST['refid'];
		
        if($do == 'delete')
		{
			if($do == 'delete')
			{
				deleteFromFavorite($provider, $type, $refid);
			}
			else if($do == 'save')
			{
				echo "do=$do\n";
			}
			$message = "Deleted: $provider/$type/$refid";
			header( 'Location: '.$_POST['URL'] ) ;
		}
    }
    
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Favoriete Programma's</title>
	<link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>
	<form name="favorite" method="post">
        <input type="hidden" name="do"/>
        <input type="hidden" name="provider" value="uitzendinggemist"/>
        <input type="hidden" name="type" value="programma"/>
        <input type="hidden" name="refid"/>
    </form>
    <script type="text/javascript">
    function removeFromFavorite(refid)
    {
        var form = document.favorite;
        form.do.value = 'delete';
        form.refid.value = refid;
        form.submit();
    }
    
    function save(programId)
    {
        var form = document.favorite;
        form.do.value = 'save';
        form.programid.value = programId;
        form.submit();
    }
    </script>
    <div id="header"><h1>Favoriete Programma's</h1></div>
	<a href="../dune/favorites.php"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>
<?php
	//echo "<p>Message: $message</p>";
    if($useMySQL)
    {
		$duneSerial = findSerialByIP();
        
		if($duneSerial)
        {
            echo '<p>Dune HD media speler gevonden: '.$duneSerial.'</p>'."\n";
        }
        else
        {
            echo "<p>Je kunt pas favorieten aanmaken, nadat je eerst verbinding met je Dune HD Media player verbonden bent geweest met deze Uitzending Gemist App.";
        }        
    }
  
    echo '<p>Klik op het <img src="img/add_to_favorite_22.png" alt="rode hartje"/> bij de programma vermelding, om dat programma toe te voegen aan deze favorieten lijst.</p>'."\n";
	echo "<table class=\"touch\">\n";
	
    foreach(readFavorites('uitzendinggemist', 'programma') as $programma)
    {
        writeProgramma($programma['title'], $programma['img'], $programma['refid']);
    }
    echo "</table>\n";
    
	function writeProgramma($caption, $url_icon, $programId)
	{
		$url = 'afleveringen.php?programid='.urlencode($programId);
        // bin-icon-32.png
		echo '<tr>';
		echo '<td><a href="'.$url.'">';
        if($url_icon)
            echo '<img alt="'.$caption.'" src="'.$url_icon.'"/>';
        echo $caption.'</a>';
        echo '<a href="#" id="bottle" onclick="removeFromFavorite(\''.$programId.'\');return false;" >';
        echo '<img src="img/bin-icon-32.png" alt="Verwijder" class="actionIcon" title="Verwijderen"/>';
        echo '</a></td>';
        echo '<td>Titel: <input type="text" name="title" value="'.$caption.'" size="80" maxlength="80"/>';
        echo '<br/>URL banner: <input type="text" name="img" value="'.$url_icon.'" size="80" maxlength="255"/>';
 		// echo '<a href="#" id="bottle" onclick="save(this);return false;" ><img src="img/save-icon-32.png" alt="Opslaan" class="actionIcon" title="Opslaan"/></a>';
        echo '</td>';
        echo "</tr>\n";
	}

?>
</body>
</html>
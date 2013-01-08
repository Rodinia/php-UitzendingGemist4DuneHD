<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Programma's</title>
	<link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>
	<div id="content">
	<div id="header"><h1>Uitzending Gemist</h1></div>
	<a href="../dune/favorites.php"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>
	<table class="touch">
<?php
	#Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);
    
    require_once '../lib/lib_favorites.php';

	foreach(readFavorites('../favorieten_uitzendinggemist.xml') as $programma)
	{
		writeProgramma($programma['caption'], $programma['banner'], $programma['id']);
	}

	function writeProgramma($caption, $url_icon, $programma)
	{
		$url = 'afleveringen.php?programid='.urlencode($programma);

		echo '<tr>';
		echo '<td class="touch"><a href="'.$url.'"><img alt="'.$caption.'" src="'.$url_icon.'"/>'.$caption.'</a></td>';
		echo "<tr>\n";
	}

?>
		</div>
	</table>
</body>

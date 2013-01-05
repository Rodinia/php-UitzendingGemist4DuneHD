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
    
    include_once '../lib_favorites.php';

	foreach(readFavorites('../favorieten_uitzendinggemist.xml') as $programma)
	{
		writeProgramma($programma['caption'], $programma['banner'], $programma['id']);
	}

	function writeProgramma($caption, $url_icon, $programma)
	{
		$url = 'afleveringen.php?programma='.urlencode($programma);

		echo '<tr>';
		echo '<td class="touch"><a href="'.$url.'"><img alt="'.$caption.'" src="'.$url_icon.'"/>'.$caption.'</a></td>';
		echo "<tr>\n";
	}

?>
		</div>
	</table>
</body>

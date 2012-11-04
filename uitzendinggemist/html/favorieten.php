<html>
<head>
	<link href="application.css" media="screen" rel="stylesheet" type="text/css" />
	<title>Programma's</title>
</head>
<body>
	<div id="content">
	<div id="header"><h1>Uitzending Gemist</h1></div>
	<table>
<?php
	#Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);
    
    include '../common.php';

	foreach(readFavorites('../favorieten_uitzendinggemist.xml') as $programma)
	{
		writeProgramma($programma['caption'], $programma['banner'], $programma['id']);
	}

	function writeProgramma($caption, $url_icon, $programma)
	{
		$url = 'afleveringen.php?programma='.urlencode($programma);

		echo '<tr>';
		echo '<td><a href="'.$url.'"><img alt="'.$caption.'" src="'.$url_icon.'"/></a></td>';
		echo '<td><a href="'.$url.'"><h3>'.$caption.'</h3></a></td>';
		echo "<tr>\n";
	}



?>
		</div>
	</table>
</body>

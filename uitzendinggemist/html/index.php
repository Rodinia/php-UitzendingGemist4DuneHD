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
	ini_set('display_errors',1);
	error_reporting(E_ERROR);

	$dom = new DOMDocument();
	$xml = $dom->loadHtmlFile('../ugconfig.xml');
	if(!xml)
		error('Failed to load Uitzending Gemist XML configuration: ugconfig.xml');

	$num = 0;
	foreach($dom->documentElement->getElementsByTagName('programma') as $programma)
	{
		$caption = getSingleElement($programma, 'caption')->nodeValue;
		$url_icon = getSingleElement($programma, 'banner')->nodeValue;
		$id = getSingleElement($programma, 'id')->nodeValue;

		writeProgramma($num++, $caption, $url_icon, $id);
	}

	function getSingleElement($parent, $name)
	{
		foreach($parent->getElementsByTagName($name) as $element)
			return $element;
		return 0;
	}

	function writeProgramma($num, $caption, $url_icon, $programma)
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

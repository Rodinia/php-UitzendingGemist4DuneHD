<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>Omroepen</title>
  <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	require_once '../lib/lib_ugemist.php';

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ALL);
	
	//header('Content-type: text/plain');
	
	echo "<h1>Omroepen</h1>\n";
	echo '<a href="../dune/omroepen.php" alt="Dune HD"/><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>'."\n";
	
	$elements = wgetBroadcasters();
		
	echo "<table class=\"touch\">\n";
	
	$cols = 4;
	$nr = 0;

	foreach ($elements as $element)
	{
		$href=$element->getAttribute('href');
		$title=$element->getAttribute('title');
		$omroepId=substr($href, 10);
		$imgsrc=$element->getElementsByTagName('img')->item(0)->getAttribute('src');
		
		if($nr++%$cols == 0)
		{
			if($nr>1)
				echo "</tr>\n";
			echo "<tr>\n";
		}
		
		echo '<td class="touch"><a href="programmas.php?omroep='.urlencode($omroepId).'">'.'<img src="http:'.$imgsrc.'" alt="'.$title.'"/></a></td>';
		//echo '<td><a href="http://www.uitzendinggemist.nl'.$href.'">Naar Uitzending Gemist</a></td>';
		//echo "</tr>\n";
	}

	echo "</table>\n";


?>

</body>
<html>
<head>
  <title>Programmas</title>
  <link href="application.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	include '../common.php';

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_WARNING);
	
	//header('Content-type: text/plain');

    $suffix = $_GET['suffix'];
	

    if(is_null($suffix))
    {
		$cols = 8;
        $nr = 0;
        echo "<h1>Programma Index</h1>\n";
        
        echo "<table class=\"touch\">\n";
        echo "<tr>\n";
		#
        foreach (wgetProgramPrefixLinks() as $prefix)
		{
			if($nr++%$cols == 0)
			{
				echo "</tr>\n";
				echo "<tr>\n";
			}
			echo '<td><a href="?suffix='.urlencode($prefix).'">'.strtoupper($prefix)."</a></td>\n";
		}
        echo "</tr>\n";
        echo "</table>\n";
    }
    else
    {
        //header('Content-type: text/plain');

        echo "<h1>Programma lijst $suffix</h1>\n";
        echo "<i>Pagina nog in ontwikkeling...</i>\n";

        $elements = wgetPrograms($suffix);

        echo "<table>\n";

        echo "<tr><th>Programma</th><th>ID</th><th>Externe link</th></tr>\n";

		foreach ($elements as $element)
        {
            $href=$element->getAttribute('href');
            $programId=substr($href, 12);

            echo '<tr>';
            echo '<td><a href="afleveringen.php?programma='.urlencode($programId).'">'.$element->nodeValue.'</a></td>';
            echo '<td>'.$programId.'</td>';
            echo '<td><a href="http://www.uitzendinggemist.nl'.$href.'">Naar Uitzending Gemist</a></td>';
            echo "</tr>\n";
        }
        echo "</table>\n";

    }



?>

</body>
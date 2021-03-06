<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
 <head>
   <title>Dune HD Uitzending Gemist</title>
   <link href="html/application.css" media="screen" rel="stylesheet" type="text/css" />
 </head>

  <body>
    <h1><img src="html/img/ug-header-logo.png" width="50" title="DuneHD data (DUNE_FOLDER.TXT mechanism)"/>Uitzending Gemist</h1>
    <h2>HTML Pagina's: <a href="html/">Uitzending Gemist</a/></h2>

     <h2>Dune Installatie</h2>
     <p>Maak in op bron, waar de Dune media speler toegang toe heeft, een folder aan 'Uitzending gemist'.
        In die folder plaats je het bestand: '<a href="dune/dunefolder.php">dune_folder.txt</a>'.
        Deze ziet er als volg uit:
     </p>
	<pre># Redirected to 'Uitzending Gemist' PHP index
media_url = dune_<?php

	print('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'dune/');

   ?>
   </pre>
   <p>Voor meer informatie over dune_folder.txt zie <a href="http://dune-hd.com/firmware/misc/dune_folder_howto.txt">DUNE_FOLDER.TXT MECHANISM</a>

	<p>Pas '<a href="favorieten_uitzendinggemist.xml">favorieten_uitzendinggemist.xml</a>' config handmatig aan om gewenste programma's in favorieten te plaatsen.</p>
  </body>
</html>
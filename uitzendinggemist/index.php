<html>
   <head>
      <title>
         Uitzending Gemist
      </title>
   </head>

   <body>
      <h1><img src="html/png/ug-header-logo.png" width="50" />Uitzending Gemist</h1>
      <h2>HTML Pages</h2>
      <table>
		<tr><td><a href="html/">Uitzending Gemist: Favoriete Programma's</a/></td></tr>
		<tr><td><a href="html/programmas.php">Uitzending Gemist: Programma's A-Z</a/></td></tr>
		<tr><td><a href="html/vara_gemist.php">VARA Gemist</a/></td></tr>
	  </table>

      <h2>Dune HD pagina's</h2>
      <table>
		<tr><td><a href="dune/">Dune-HD: Home</a></td></tr>
		<tr><td><a href="dune/favorites.php">Dune-HD: Favoriete Programma's</a></td></tr>
		<tr><td><a href="dune/programmas.php">Dune-HD: Programma's A-Z</a></td></tr>
	  </table>

   </body>

   <h2>Dune Installatie</h2>
   <p>Maak in op bron, waar de Dune media speler toegang toe heeft, een folder aan 'Uitzending gemist'.
   In die folder maak je een text-bestand aan 'dune_folder.txt'.
   Kopieer de volgende text in 'dune_folder.txt':
   </p>
	<pre># Redirected to 'Uitzending Gemist' PHP index
media_url = dune_<?php

	print('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'dune/');

   ?>
   </pre>
<p>Voor meer informatie over dune_folder.txt zie <a href="http://dune-hd.com/firmware/misc/dune_folder_howto.txt">DUNE_FOLDER.TXT MECHANISM</a>

	<p>Pas '<a href="ugconfig.xml">ugconfig.xml</a>' config handmatig aan om gewenste programma's in favorieten te plaatsen.</p>

</html>
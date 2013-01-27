<?php

    #Enable display errors
    //ini_set('display_errors',1);
    error_reporting(E_ALL);

    if(isset($_POST['do']))
    {
        $message = "Do: $do";
        $do = $_POST['do'];
        require_once dirname(__FILE__).'/../../lib/lib_storage.php';

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

    function dune_url($mediaid)
	{
		return '../dune/vara_play.php?mediaid='.$mediaid;
	}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>VARA Gemist</title>
  <link href="varagemist.css" media="screen" rel="stylesheet" type="text/css" />
</head>

<body>
   <h1><img src="../img/vara-logo.png" alt="VARA">Gemist</h1>
   <form name="favorite" method="post">
       <input type="hidden" name="do"/>
       <input type="hidden" name="provider" value="vara"/>
       <input type="hidden" name="type" value="media"/>
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
   </script>
<?php

function writeDuneLink($what = null, $url = null)
{
    $href='../dune/';
    if($what)
    {
        $href .='?what='.$what;
        if($url)
            $href .='&what='.$url;
    }
    echo '<a href="'.$href.'"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>';
}

if(!isset($_GET['what']))
{
    ?>
    <a href="../dune/"><img src="img/dune_hd_logo.png" alt="Dune HD"/></a>
    <a href="http://omroep.vara.nl/gemist">omroep.vara.nl/gemist</a>
    <ul>
        <li><a href="?what=dezeweek">Deze week</a></li>
        <li><a href="?what=favo">Favorieten</a></li>
        <li><a href="?what=recprog">Recente programma's</a></li>
        <li><a href="giel.php">Giel</a></li>
    <?php
}
else
{
    require_once '../lib_vara.php';

    $what = $_GET['what'];
    if($what=='dezeweek')
    {
        echo "<h2>Deze Week</h2>\n";
        writeDuneLink($what);
        echo "<table>\n";
        foreach(getDezeWeek() as $item)
        {
            echo '<tr>';
            write_vara_play_table_row($item['caption'], $item['id']);
            echo '</tr>';
        }
        echo "</table>\n";
        exit;
    }

    if($what=='favo')
    {
        require_once '../../lib/lib_storage.php';

        echo "<h2>Favorieten</h2>\n";
        writeDuneLink($what);

        echo "<table>\n";
        foreach(readFavorites('vara', 'media') as $programma)
        {
            echo '<tr>';
            write_vara_play_table_row($programma['title'], $programma['refid']);
            echo '<td><a href="#" id="bottle" onclick="removeFromFavorite(\''.$programma['refid'].'\');return false;" >';
            echo '<img src="../../html/img/bin-icon-32.png" alt="Verwijder" class="actionIcon" title="Verwijderen"/></td>';
            echo '</a></td>';
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    else if($what=='recprog')
    {
        echo "<h2>Recente programma's</h2>\n";
        writeDuneLink($what);

        echo "<table>\n";
        foreach(getVaraProgramList()->allProgramsAndSites as $program)
        {
            $url = 'dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/?what=program&url='.urlencode($program->url);
            echo '<tr>';
            write_vara_play_table_row($program->title, $url, 'item');
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    else if($what=='program')
    {
        $url = $_GET['url'];
        writeDuneLink($what, $url);

        echo "<table>\n";
        foreach(getVaraProgramFragments($url) as $fragment)
        {
            echo '<tr>';
            write_vara_play_table_row($fragment['caption'], $fragment['id']);
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
}
?>

  </body>
</html>




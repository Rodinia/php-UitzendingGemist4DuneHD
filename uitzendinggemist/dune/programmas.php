use_icon_view = exlist
paint_captions = yes
async_icon_loading = yes
paint_help_line = no
paint_path_box = no
paint_icon_selection_box = yes
paint_content_box_background = no
paint_scrollbar = no

<?php
	include '../common.php';
    include 'dune.php';

    #Enable display errors
	ini_set('display_errors',1);
	error_reporting(E_ERROR);

    header('Content-type: text/plain; charset=utf-8');

    $suffix = $_GET['suffix'];

    $num = 0;

    if(is_null($suffix))
    {
        $baseurl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

        $elements = wgetProgramPrefixLinks();

        echo "# A-Z list\n";
        foreach ($elements as $element)
        {
            $suffix = $element->getAttribute('href');

            $url = $baseurl.'?suffix='.urlencode($suffix);
            writeItem($num++, $element->nodeValue, 'dune_'.$url);
        }
    }
    else
    {
        $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/afleveringen.php?program=';

        echo "# $suffix \n";
        $elements = wgetPrograms($suffix);

        foreach ($elements as $element)
        {
            $href=$element->getAttribute('href');
            $programId=substr($href, 12);
            $url = $baseurl.urlencode($programId);
            writeItem($num++, $element->nodeValue, 'dune_'.$url);
        }
    }

?>

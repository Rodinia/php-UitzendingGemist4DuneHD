<?php
	include '../common.php';
    include 'dune.php';

    #Enable display errors
	//ini_set('display_errors',1);
	error_reporting(E_ERROR);

    header('Content-type: text/plain; charset=utf-8');

    $suffix = $_GET['suffix'];

    $num = 0;

    if(is_null($suffix))
    {
?>use_icon_view = yes
num_cols = 9
num_rows = 4
async_icon_loading = yes
<?php      
        $baseurl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

        echo "# A-Z list\n";
        foreach (wgetProgramPrefixLinks() as $prefix)
        {
            $url = $baseurl.'?suffix='.urlencode($prefix);
            writeItem($num++, strtoupper($prefix), 'dune_'.$url);
        }
    }
    else
    {
        echo "use_icon_view = no\n";
        
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

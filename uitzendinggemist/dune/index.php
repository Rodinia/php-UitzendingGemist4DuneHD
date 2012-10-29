use_icon_view = exlist
paint_captions = yes
async_icon_loading = yes
paint_help_line = no
paint_path_box = no
paint_icon_selection_box = yes
paint_content_box_background = no
paint_scrollbar = no

# A-Z list

<?php
	include 'dune.php';
    
    header('Content-type: text/plain; charset=utf-8');
    
    $num=0;
    
    $baseurl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/';
    
    writeItem($num++, 'Favorieten', 'dune_'.$baseurl.'favorites.php');
    writeItem($num++, 'Programm\'s A..Z', 'dune_'.$baseurl.'programmas.php');
    
?>

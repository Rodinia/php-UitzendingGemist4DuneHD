<?php
    print("# Reference to Dune HD PHP script for Uitzending Gemist\n");
    # media_url = dune_http://uitzendinggemist.zxq.net/dune/  

    header('Content-type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="dune_folder.txt"');
    print('media_url = dune_http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/');
?>
<?php
 /*
  # Standard Apache logs the following: "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-agent}i\""
  # Here's our version of it.
  */
  $h = getenv('REMOTE_ADDR');
  $l = "-"; # should really be user from ident
  $u = getenv('REMOTE_USER')?getenv('REMOTE_USER'):"-";
  $t = date("m/M/Y:H:i:s O");
  $r = getenv('REQUEST_METHOD')." ".getenv('REQUEST_URI');
  $s = "-"; # We can't tell what the status was
  $b = 0;   # or the size of the page
  $referer = getenv('HTTP_REFERER');
  $useragent = getenv('HTTP_USER_AGENT');
  
  //X-Dune-Serial-Number: 8C50-5FE6-146A-F7E7-1D1A-C163-EF74-F494
  //X-Dune-Interface-Language: dutch
  
  $headers = apache_request_headers();
  $duneSerial = isset($headers['X-Dune-Serial-Number']) ? $headers['X-Dune-Serial-Number'] : "";
  
  $logentry = "$h $l $u [$t] \"$r\" $s $b \"$referer\" \"$useragent\" \"$duneSerial\"\r\n";

  # Note the log file below needs to be writeable by user "apache"
  error_log($logentry, 3, dirname(dirname(__FILE__)).'/log.txt');
?>
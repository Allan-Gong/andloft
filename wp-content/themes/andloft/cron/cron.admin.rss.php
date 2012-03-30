<?php

// prevent cron script to be accessed from outside this server

$LOCALHOSTS   = array('localhost', '127.0.0.1');

$HTTP_HOSTS   = array('www.loft.andshop.com.au');
$REMOTE_ADDRS = array('110.232.142.51');
$SERVER_ADDRS = array('110.232.142.51');

$can_execute_cron_job = (
	   in_array($_SERVER['HTTP_HOST'], $LOCALHOSTS)
	or  ( 
		    	in_array($_SERVER['REMOTE_ADDR'], $REMOTE_ADDRS) 
			and in_array($_SERVER['SERVER_ADDR'], $SERVER_ADDRS)
			and in_array($_SERVER['HTTP_HOST'],   $HTTP_HOSTS)
		)
);

if( !$can_execute_cron_job ) {
    die('Permission denied.'); 
}

set_time_limit(0);

// error_reporting(-1);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

?>
<?php 
error_reporting(E_ALL ^ E_WARNING); 

$intSLADate="27/Nov/2017";
$date = str_replace('/', '-', $intSLADate);
echo "$date\n";

$hiddenintSLADate = date("Y-m-d", strtotime($date));
echo "$hiddenintSLADate \n";

$hiddenintSLATime = strtotime($hiddenintSLADate);
echo "$hiddenintSLATime\n";


#$timestamp = strtotime('22-09-2008');
#echo "$timestamp \n";
#$timestamp = strtotime('23-09-2008');
#echo "$timestamp \n";
#$timestamp = strtotime('23-09-2009');
#echo "$timestamp \n";

?>


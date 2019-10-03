<?php

/* Default database settings*/
$database_type = "sqlsrv";
$database_default = "medialog";
$database_hostname = "localhost";
$database_username = "sa";
$database_password = "password";
$database_port = "";

$debug=0;
/* display ALL errors */
error_reporting(E_ALL);

/* Include configuration */
include("../config.php");

include("../client_roistat.php");
include_once("../fields_mappings.php");
include_once("../func.php");

if (isset($_REQUEST['phpinfo']))
{
	phpinfo();
	die( "exit!" );
}

if (isset($_REQUEST['debug']))
{
	$debug=1;
}

//-----------------------------------------------
if(0){
$filename='calls.json';
$fields_array=$fields_roistat_call;
}else{
	$filename='visits.json';
	$fields_array=$fields_roistat_visit;
}

$file = file_get_contents($filename);
echo "input file:\n";
echo $file;

$json = json_decode($file, true);

$data=$json['data'];

$i=0;
// loop through the array
foreach ($data as $row) {
	echo "===========\n";
	echo "row: $i: input:\n";
	print_r($row);
	$r=array();
	sql_row_array($row, $r, $fields_array);
	echo "\noutput:\n";
	print_r($r);
	unset($r);
	$i++;
}


?>

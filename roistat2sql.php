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
include("config.php");

include("client_roistat.php");
include_once("fields_mappings.php");
include_once("func.php");

if (isset($_REQUEST['phpinfo']))
{
	phpinfo();
	die( "exit!" );
}
if (isset($_REQUEST['debug']))
{
	$debug=1;
}
//----------------------------
if( strlen(trim($api_key))==0 ||
		strlen(trim($api_project))==0
){
	die("Config is incorrect!\n");
}

//------------------
//Подключение к SQL-базе
$stage="DB connect";

if($database_type=="sqlsrv")
	$dsn = "$database_type:server=$database_hostname;database=$database_default";
else
	$dsn = "$database_type:host=$database_hostname;dbname=$database_default;charset=$database_charset";

$opt = array(
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

try {
	$conn = new PDO($dsn, $database_username, $database_password, $opt);
}
catch(PDOException $e) {
	die($e->getMessage());
}
user_log($stage." OK");

//----------------------------
$api=client_api_init($api_url);

//----------------------------
//$date=date("Y-m-d");

$date = new DateTime();
$date->modify('-1 day');
$date=$date->format('Y-m-d');

//echo $date."\n";

$date_from=$date.'T00:00:00';
$date_till=$date.'T23:59:59';

//$date_from='2019-03-01T00:00:00';
//$date_till='2019-03-01T00:00:00';

//----------------------------
if(1){
	//проверка от повторного импорта
	$table_name="US_WEB_ROISTAT_CALLS";
	$stage="Calls DB exist check in $table_name:";

	user_log($stage." period $date_from - $date_till");

	$sql="select count(*) as cnt
	 from $table_name
	 where
	 call_date> convert(datetime, :date_from, 127) and
	 call_date< convert(datetime, :date_till, 127) ";

	$r=array('date_from' =>$date_from
			,'date_till' =>$date_till);

	$st = $conn->prepare($sql);
	$st -> execute($r);
	$data=$st->fetchAll();
	//print_r($data);

	if(count($data)>0 && $data[0]['cnt']>0)
	{
		user_log($stage." already imported for period $date_from - $date_till.");
		user_log($stage." Count=".$data[0]['cnt']);
		die( $stage." Exit\n");
	}

	user_log($stage." OK");
}
//-------------------
if(1){
	//получение звонков
	$stage="Calls client:";
	user_log($stage);

	$offset=0;
	$data=client_roistat_call_list($api,$api_project,$api_key
							,$date_from,$date_till
							,$offset,$api_query_limit);
	//print_r($data);

	user_log($stage." Data from client count=".count($data));
	if(count($data)==0)
		user_log($stage."WARNING: Zero data from client!");
}
//-----------------------------------------------
if(count($data)>0){
	$fields_array=$fields_roistat_call;
	$table_name="US_WEB_ROISTAT_CALLS";

	$count=db_insert2($conn,$data,$fields_array,$table_name);

	user_log($stage." inserted to ".$table_name." ".$count." rows.");

	//-----------------------------------------------
	//обработка новых записей в SQL
	$stage="Calls SQL Update call_date from timezone format";
	//input format "2016-05-22T19:32:22+0000"

	$sql="update $table_name set
	call_date=convert(datetime,left(date,19),127)
		+dateadd(hour,$timezone_offset-convert(int,substring(date,20,3)),0)
		+dateadd(minute,0-convert(int,substring(date,23,2)),0)
	from $table_name
	where call_date is null";

	$st = $conn->prepare($sql);
	$st -> execute();
	$count = $st->rowCount();
	user_log($stage." count=".$count);

	//-----------------------------------------------
	$stage="Calls SQL Update MEDIALOG_CALL_ID";

	$sql="update $table_name set MEDIALOG_CALL_ID=CALLS.CALLS_ID
	FROM $table_name as web
	join calls on calls.phone=web.callee and abs(datediff(MINUTE,web.call_date,CALLS.CALL_DATETIME))<2
	where
	 web.MEDIALOG_CALL_ID is null and
	 call_date> convert(datetime, :date_from, 127) and
	 call_date< convert(datetime, :date_till, 127) ";

	$r=array('date_from' =>$date_from
			,'date_till' =>$date_till);

	$st = $conn->prepare($sql);
	$st -> execute($r);
	$count = $st->rowCount();
	user_log($stage." count=".$count);
}
//-----------------------------------------------
if(1){
	//проверка от повторного импорта визитов
	$table_name="US_WEB_ROISTAT_VISITS";
	$stage="DB exist check in $table_name:";

	$sql="select count(*) as cnt
	 from $table_name
	 where
	 visit_date> convert(datetime, :date_from, 127) and
	 visit_date< convert(datetime, :date_till, 127) ";

	$r=array('date_from' =>$date_from
			,'date_till' =>$date_till);

	$st = $conn->prepare($sql);
	$st -> execute($r);
	$data=$st->fetchAll();
	//print_r($data);

	if(count($data)>0 && $data[0]['cnt']>0)
	{
		user_log($stage." already imported for period $date_from - $date_till.");
		user_log($stage." Count=".$data[0]['cnt']);
		die( $stage." Exit\n");
	}

	user_log($stage." OK");
}
//-------------------
if(1){
	//получение визитов
	$stage="Visits client:";
	user_log($stage);

	$offset=0;
	$try_query=1;
	while($try_query)
	{
		$data=client_roistat_visit_list($api,$api_project,$api_key
						,$date_from,$date_till
						,$offset,$api_query_limit);
		//print_r($data);
		$datasize=count($data);
		user_log($stage." Data from client count=".$datasize." offset=".$offset);

		if($datasize==0){
			user_log($stage."WARNING: Zero data from client!");
		}
		//-----------------------------------------------
		if($datasize>0){
			//разрешенные столбцы для вставки
			$fields_array=$fields_roistat_visit;
			$table_name="US_WEB_ROISTAT_VISITS";

			$count=db_insert2($conn,$data,$fields_array,$table_name);
			user_log($stage." inserted to ".$table_name." ".$count." rows.");
		}

		if($datasize==$api_query_limit){
			$offset+=$datasize;
		}
		else
			$try_query=0;
	}
}
else {
	$data=null;
}
//-----------------------------------------------
if(1){
	//обработка новых записей в SQL
	$stage="SQL Update visit_date from timezone format";
	//input format "2016-05-22T19:32:22+0000"

	$sql="update $table_name set
	visit_date=convert(datetime,left(date,19),127)
		+dateadd(hour,$timezone_offset-convert(int,substring(date,20,3)),0)
		+dateadd(minute,0-convert(int,substring(date,23,2)),0)
	from $table_name
	where visit_date is null";

	$st = $conn->prepare($sql);
	$st -> execute();
	$count = $st->rowCount();
	user_log($stage." count=".$count);
}
//-----------------------------------------------
if(1){
	//проверка от повторного импорта заказов
	$table_name="US_WEB_ROISTAT_ORDERS";
	$stage="DB exist check in $table_name:";

	$sql="select count(*) as cnt
	 from $table_name
	 where
	 creation_date> convert(datetime, :date_from, 127) and
	 creation_date< convert(datetime, :date_till, 127) ";

	$r=array('date_from' =>$date_from
			,'date_till' =>$date_till);

	$st = $conn->prepare($sql);
	$st -> execute($r);
	$data=$st->fetchAll();
	//print_r($data);

	if(count($data)>0 && $data[0]['cnt']>0)
	{
		user_log($stage." already imported for period $date_from - $date_till.");
		user_log($stage." Count=".$data[0]['cnt']);
		die( $stage." Exit\n");
	}

	echo $stage." OK\n";
}
//-----------------------------------------------
if(1){
	//получение заказов
	$stage="Orders client:";
	user_log($stage);

	$offset=0;
	$data=client_roistat_order_list($api,$api_project,$api_key
							,$date_from,$date_till
							,$offset,$api_query_limit);
	//print_r($data);

	user_log($stage." Data from client count=".count($data));
	if(count($data)==0)
		user_log($stage."WARNING: Zero data from client!");
}
else {
	$data=null;
}
//-----------------------------------------------
if(count($data)>0){
	//разрешенные столбцы для вставки
	$fields_array=$fields_roistat_order;
	$table_name="US_WEB_ROISTAT_ORDERS";

	$count=db_insert2($conn,$data,$fields_array,$table_name);

	user_log($stage." inserted to ".$table_name." ".$count." rows.");

	//-----------------------------------------------
	//обработка новых записей в SQL
	$stage="SQL Update date columns from timezone format";
	//input format "2016-05-22T19:32:22+0000"

	$sql="update $table_name set
	creation_date=convert(datetime,left(creation_date_text,19),127)
		+dateadd(hour,$timezone_offset-convert(int,substring(creation_date_text,20,3)),0)
		+dateadd(minute,0-convert(int,substring(creation_date_text,23,2)),0)
	,update_date=convert(datetime,left(update_date_text,19),127)
			+dateadd(hour,$timezone_offset-convert(int,substring(update_date_text,20,3)),0)
			+dateadd(minute,0-convert(int,substring(update_date_text,23,2)),0)
	from $table_name
	where creation_date is null";

	$st = $conn->prepare($sql);
	$st -> execute();
	$count = $st->rowCount();
	user_log($stage." count=".$count);
}

//-----------------------------------------------
if(1){
	//получение список рекламных каналов, которые используются в проекте.
	$stage="source client:";
	user_log($stage);

	$data=client_roistat_source_list($api,$api_project,$api_key);
	//print_r($data);

	user_log($stage." Data from client count=".count($data));
	if(count($data)==0)
		user_log($stage."WARNING: Zero data from client!");
}
else {
	$data=null;
}
//-----------------------------------------------
if(count($data)>0){
	//очистка временной таблицы
	$table_name="US_WEB_ROISTAT_SOURCES_tmp";
	$fields_array=$fields_roistat_source;
	$stage="delete from $table_name";

	$sql="delete from $table_name";

	$st = $conn->prepare($sql);
	$st -> execute();
	$count = $st->rowCount();
	user_log($stage." count=".$count);

	//-----------------------------------------------
	$stage="$table_name insert";
	$count=db_insert2($conn,$data,$fields_array,$table_name);
	user_log($stage." inserted to ".$table_name." ".$count." rows.");

	//-----------------------------------------------
	//вставка отсутствующих записей
	$stage="US_WEB_ROISTAT_SOURCES insert from TMP";

	$sql="insert into US_WEB_ROISTAT_SOURCES
		([source],[name],[type]
		,[level_id],[icon])
	select
		[source],[name],[type]
		,[level_id],[icon]
	from US_WEB_ROISTAT_SOURCES_tmp
	where source not in(
		select source
		from US_WEB_ROISTAT_SOURCES
	)";

	$st = $conn->prepare($sql);
	$st -> execute();
	$count = $st->rowCount();
	user_log($stage." count=".$count);

	//-----------------------------------------------
	//обновление названий
	$stage="US_WEB_ROISTAT_SOURCES Update from TMP";

	$sql="update old set
		NAME=tmp.NAME
		,icon=tmp.icon
	from US_WEB_ROISTAT_SOURCES old
	join US_WEB_ROISTAT_SOURCES_TMP tmp on old.source=tmp.source
	where isnull(old.NAME,'')<>isnull(tmp.NAME,'')
		or isnull(old.icon,'')<>isnull(tmp.icon,'')";

	$st = $conn->prepare($sql);
	$st -> execute();
	$count = $st->rowCount();
	user_log($stage." count=".$count);
}
//-------------------

?>

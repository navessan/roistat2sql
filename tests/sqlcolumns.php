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
//разрешенные столбцы для вставки
$fields_array=array(
'id'=>array("sql"=>"int"),					// ID звонка в Roistat
'callee'=>array("sql"=>"varchar"),	//набранный номер
'caller'=>array("sql"=>"varchar"),	//номер клиента
'duration'=>array("sql"=>"int"),		//продолжительность звонка (в секундах)
'status'=>array("sql"=>"varchar"),	//статус звонка
'date'=>array("sql"=>"varchar"),		//дата и время создания записи о звонке (в формате UTC0)
'visit_id'=>array("sql"=>"varchar"),	//номер визита
'order_id'=>array("sql"=>"varchar"),	//номер заказа из CRM
'static_source'=>array(//"sql"=>"text"
		"type"=>"array",	//подробные данные о рекламном канале для звонка. Будут отображены, если используется статический коллтрекинг. В обратном случае будет null.
		"inner"=>array('system_name'=>array("sql"=>"varchar"),	//системное название канала в Roistat
			'display_name'=>array("sql"=>"varchar"),	//человекочитаемое название канала
			'icon_url'=>array("sql"=>"varchar"),	//ссылка на иконку канала
			'utm_source'=>array("sql"=>"text"),	//источник перехода (из метки utm)
			'utm_medium'=>array("sql"=>"text"),	//тип трафика (из метки utm)
			'utm_campaign'=>array("sql"=>"text"),	//название рекламной кампании (из метки utm)
			'utm_term'=>array("sql"=>"text"),	//ключевая фраза (из метки utm)
			'utm_content'=>array("sql"=>"text"),	//дополнительная информация по объявлению (из метки utm)
			'openstat'=>array("sql"=>"varchar"))	//значение метки openstat
		),
'comment'=>array("sql"=>"text"),	//комментарий к звонку
'visit'=>array("sql"=>"text","type"=>"array"),	//подробные данные о визите звонка. Будут отображены, только если при запросе было указано "extend": ["visit"]. В противном случае будет null. Подробнее о данных визита читайте в методе /project/site/visit/list
'order'=>array("sql"=>"text","sqlname"=>"order_text","type"=>"array"),	//подробные данные о соответствующем заказе. Будут отображены, только если при запросе было указано "extend": ["order"]. В противном случае будет null. Подробнее о данных заказа читайте в методе /project/integration/order/list
'link'=>array("sql"=>"text"),	//ссылка на аудиозапись разговора в другом сервисе коллтрекинга
'waiting_time'=>array("sql"=>"int"),	//время ожидания (в секундах)
'answer_duration'=>array("sql"=>"int") 	//продолжительность разговора (в секундах)
);

$table_name="US_WEB_ROISTAT_CALLS";

echo "count(\$fields_array)=".count($fields_array)."\n";

//заполнение столбцов в SQL-запросе
$sql_columns=sql_columns_string($fields_array, '[', ']');
$sql_vals=sql_columns_string($fields_array, ':', '');

$sql="INSERT INTO [".$table_name."] (\n"
	.$sql_columns
	.") VALUES (\n"
	.$sql_vals
	.")";

echo "sql=".$sql;


?>

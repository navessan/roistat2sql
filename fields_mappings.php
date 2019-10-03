<?php

//истории звонков
$fields_roistat_call=array(
'id'=>array("sql"=>"varchar"),					// ID звонка в Roistat
'callee'=>array("sql"=>"varchar"),			//набранный номер
'caller'=>array("sql"=>"varchar"),			//номер клиента
'duration'=>array("sql"=>"int"),				//продолжительность звонка (в секундах)
'status'=>array("sql"=>"varchar"),			//статус звонка
'date'=>array("sql"=>"varchar"),				//дата и время создания записи о звонке (в формате UTC0)
'visit_id'=>array("sql"=>"varchar"),		//номер визита
'order_id'=>array("sql"=>"varchar"),		//номер заказа из CRM
'static_source'=>array(//"sql"=>"text"
		"type"=>"array",	//подробные данные о рекламном канале для звонка. Будут отображены, если используется статический коллтрекинг. В обратном случае будет null.
		"inner"=>array(
			'system_name'=>array("sql"=>"varchar"),	//системное название канала в Roistat
			'display_name'=>array("sql"=>"varchar"),	//человекочитаемое название канала
			'icon_url'=>array("sql"=>"varchar"),			//ссылка на иконку канала
			'utm_source'=>array("sql"=>"text"),				//источник перехода (из метки utm)
			'utm_medium'=>array("sql"=>"text"),				//тип трафика (из метки utm)
			'utm_campaign'=>array("sql"=>"text"),			//название рекламной кампании (из метки utm)
			'utm_term'=>array("sql"=>"text"),					//ключевая фраза (из метки utm)
			'utm_content'=>array("sql"=>"text"),			//дополнительная информация по объявлению (из метки utm)
			'openstat'=>array("sql"=>"varchar"))			//значение метки openstat
		),
'comment'=>array("sql"=>"text"),				//комментарий к звонку
'visit'=>array("sql"=>"text","type"=>"json"),	//подробные данные о визите звонка. Будут отображены, только если при запросе было указано "extend": ["visit"]. В противном случае будет null. Подробнее о данных визита читайте в методе /project/site/visit/list
'order'=>array("sql"=>"text","sqlname"=>"order_text","type"=>"json"),	//подробные данные о соответствующем заказе. Будут отображены, только если при запросе было указано "extend": ["order"]. В противном случае будет null. Подробнее о данных заказа читайте в методе /project/integration/order/list
'link'=>array("sql"=>"text"),						//ссылка на аудиозапись разговора в другом сервисе коллтрекинга
'waiting_time'=>array("sql"=>"int"),		//время ожидания (в секундах)
'answer_duration'=>array("sql"=>"int") 	//продолжительность разговора (в секундах)
);

//----------------------------------
//Визиты
$fields_roistat_visit=array(
'id'=>array("sql"=>"varchar"),
	'first_id'=>array("sql"=>"varchar"),
	'date'=>array("sql"=>"varchar"),
	'landing_page'=>array("sql"=>"varchar"),
	'host'=>array("sql"=>"varchar"),
	'google_client_id'=>array("sql"=>"text"),
	'metrika_client_id'=>array("sql"=>"text"),
	'ip'=>array("sql"=>"varchar"),
	'roistat_param1'=>array("sql"=>"varchar"),
	'roistat_param2'=>array("sql"=>"varchar"),
	'roistat_param3'=>array("sql"=>"varchar"),
	'roistat_param4'=>array("sql"=>"varchar"),
	'roistat_param5'=>array("sql"=>"varchar"),
	'device'=>array("sql"=>"text","type"=>"json"),
	'source'=>array(
		"type"=>"array",
		"inner"=>array(
			'system_name'=>array("sql"=>"text"),	//системное название канала в Roistat
			'display_name'=>array("sql"=>"text"),//человекочитаемое название канала
			'icon_url'=>array("sql"=>"varchar"),	//ссылка на иконку канала
			'utm_source'=>array("sql"=>"text"),		//источник перехода (из метки utm)
			'utm_medium'=>array("sql"=>"text"),		//тип трафика (из метки utm)
			'utm_campaign'=>array("sql"=>"text"),	//название рекламной кампании (из метки utm)
			'utm_term'=>array("sql"=>"text"),			//ключевая фраза (из метки utm)
			'utm_content'=>array("sql"=>"text"),	//дополнительная информация по объявлению (из метки utm)
			'openstat'=>array("sql"=>"varchar"))		//значение метки openstat
	),
	'geo'=>array(
		"type"=>"array",
		"inner"=>array(
			'country'=>array("sql"=>"varchar"),
			'region'=>array("sql"=>"varchar"),
			'city'=>array("sql"=>"varchar"),
			'icon_url'=>array("sql"=>"varchar","sqlname"=>"geo_icon_url"),
			'country_iso'=>array("sql"=>"varchar"))
	),
	'order_ids'=>array("sql"=>"text","type"=>"array")
);

//----------------------------------
//Заказы
$fields_roistat_order=array(
	'id'=>array("sql"=>"varchar"),	//ID заказа из CRM
	'url'=>array("sql"=>"varchar"),	//URL заказа в CRM
	'source_type'=>array("sql"=>"varchar"),	//тип созданной заявки
	'creation_date'=>array("sql"=>"varchar","sqlname"=>"creation_date_text"),	//дата и время создания заказа в формате UTC0
	'update_date'=>array("sql"=>"varchar","sqlname"=>"update_date_text"),	//дата и время последнего изменения данных о заказе, в формате UTC0
	'revenue'=>array("sql"=>"int"),	//выручка по заказу
	'cost'=>array("sql"=>"int"),			//себестоимость
	'visit_id'=>array("sql"=>"varchar"),	//номер визита клиента
	'custom_fields'=>array(		//дополнительные поля
		"type"=>"array",
		"inner"=>array(
		'Менеджер'=>array("sql"=>"varchar","sqlname"=>"custom_manager"),
		'roistat'=>array("sql"=>"varchar","sqlname"=>"custom_roistat"),
		'status_name'=>array("sql"=>"varchar","sqlname"=>"custom_status_name"))
	),
	'status'=>array(		//данные о статусе заказа
		"type"=>"array",
		"inner"=>array(
				'id'=>array("sql"=>"varchar","sqlname"=>"status_id"),		//уникальный номер статуса в системе Roistat
				'type'=>array("sql"=>"varchar","sqlname"=>"status_type"),	//тип статуса в системе Roistat, соответствует одной из 3-х групп, по которым вы распределяли статусы, загрузившиеся из вашей CRM в Roistat:
/*
    unused - группа "Не учитываются"
    in progress - группа "В работе"
    paid - группа "Оплаченные"
    canceled - группа "Отмененные"
*/
		'name'=>array("sql"=>"varchar","sqlname"=>"status_name"))//человекочитаемое название статуса в CRM
	),
	'visit'=>array("sql"=>"text","type"=>"array")	//Подробные данные о визите заказа. Будут отображены, только если при запросе было указано "extend": ["visit"]. В противном случае будет null. Подробнее о данных визита читайте в методе /project/site/visit/list
);

//----------------------------------
//Рекламные каналы
$fields_roistat_source=array(
	'source'=>array("sql"=>"varchar"),	//системное название рекламного канала
	'name'=>array("sql"=>"varchar"),		//человекочитаемое название рекламного канала
	'type'=>array("sql"=>"varchar"),		//тип рекламного канала: system - подключаемые каналы; custom - размеченные вручную.
	'level'=>array("sql"=>"int","sqlname"=>"level_id"),		//уровень вложенности канала
	'icon'=>array("sql"=>"varchar")		//ссылка на иконку канала
);

?>

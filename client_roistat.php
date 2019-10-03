<?php

include_once("restclient.php");


function client_api_init($api_url)
{
	$api = new RestClient([
		'base_url' => $api_url
		,'headers' =>['Accept'=>"application/json",
									"Content-type"=>"application/json"
									]
		,'curl_options'=>array(CURLOPT_SSL_VERIFYPEER=>false)
		//,'curl_options'=>array(CURLOPT_FOLLOWLOCATION=>true)
		//,'format' => "json"
	]);

	//allows you to receive decoded JSON data as an array.
	$api->register_decoder('json', function($data){
		return json_decode($data, TRUE);
	});

	return $api;
}

function client_roistat_call_list($api,$project,$key,$date_from,$date_till,$offset=0,$limit=10000)
{
	if($api==null)
		return null;

	/*
	post /project/calltracking/call/list

С помощью данного метода можно выгрузить все записи из истории звонков в проекте Roistat.
Если вместе со звонком вам нужна подробная информация по соответствующему визиту и заказу,
то в теле запроса укажите "extend": ["visit", "order"] .

	https://cloud.roistat.com/api/v1/project/calltracking/call/list

*/
	$timezone="+0300";
	$body ='{"filters":{
				"and":[
						 ["date",">","'.$date_from.$timezone.'"]
						,["date","<","'.$date_till.$timezone.'"]]
					}
	,"extend":["visit", "order"]
	,"sort":["date"]
	,"limit":'.$limit.'
	,"offset":'.$offset.'}';

	$parameters=['key' => $key
						,'project'=>$project
						];
	$parameters_string = http_build_query($parameters);
	$url="/project/calltracking/call/list"."?".$parameters_string;

	return client_roistat_post($api, $url, $body);
}

function client_roistat_visit_list($api,$project,$key,$date_from,$date_till,$offset=0,$limit=10000)
{
	if($api==null)
		return null;

	/*
	post/project/site/visit/list
	Этот метод используется для получения информации о всех визитах.
	*/

	$timezone="+0300";
	$body ='{"filters":{
				"and":[
						 ["date",">","'.$date_from.$timezone.'"]
						,["date","<","'.$date_till.$timezone.'"]]
					}
	,"sort":["date"]
	,"limit":'.$limit.'
	,"offset":'.$offset.'}';

	$parameters=['key' => $key
						,'project'=>$project
						];
	$parameters_string = http_build_query($parameters);
	$url="/project/site/visit/list"."?".$parameters_string;

	return client_roistat_post($api, $url, $body);
}

function client_roistat_order_list($api,$project,$key,$date_from,$date_till,$offset=0,$limit=10000)
{
	if($api==null)
		return null;

	/*
	post/project/integration/order/list
	Данный метод используется для получения списка заказов из проекта Roistat.
	Если вместе с заказом вам нужна подробная информация по его визиту, то в теле запроса укажите "extend": ["visit"] .
	*/

	$timezone="+0300";
	$body ='{"filters":{
				"and":[
						 ["creation_date",">","'.$date_from.$timezone.'"]
						,["creation_date","<","'.$date_till.$timezone.'"]]
					}
	,"sort":["date"]
	,"limit":'.$limit.'
	,"offset":'.$offset.'}';

	$parameters=['key' => $key
						,'project'=>$project
						];
	$parameters_string = http_build_query($parameters);
	$url="/project/integration/order/list"."?".$parameters_string;

	return client_roistat_post($api, $url, $body);
}

function client_roistat_source_list($api,$project,$key)
{
	if($api==null)
		return null;

	/*
	post/project/analytics/source/list
	С помощью этого метода можно узнать полный список рекламных каналов, которые используются в проекте.
	*/

	$timezone="+0300";
	$body ='';

	$parameters=['key' => $key
						,'project'=>$project
						];
	$parameters_string = http_build_query($parameters);
	$url="/project/analytics/source/list"."?".$parameters_string;

	return client_roistat_post($api, $url, $body);
}

function client_roistat_post($api, $url, $body){
	$result = $api->post($url, $body);

	if($result->info->http_code != 200)
	{
		var_dump($result);
		echo( "API Call Error\n" );
	}

	$res=$result->decode_response();

	if(json_last_error()!=JSON_ERROR_NONE){
		echo "JSON Decode error: ".json_last_error_msg()."\n";
	}

	echo 'API Call: status='.$res['status']." with total=".$res['total']."\n";

	return $res['data'];
}

?>

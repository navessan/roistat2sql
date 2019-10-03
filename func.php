<?php

//-----------------------------------------------
function array_flatten($array = null) {
    $result = array();

    if (!is_array($array)) {
        $array = func_get_args();
    }

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $result = array_merge($result, array_flatten($value));
        } else {
            $result = array_merge($result, array($key => $value));
        }
    }

    return $result;
}

//-----------------------------------------------
function sql_columns_string($fields_array, $prefix='',$sufix='')
{
  if($fields_array==null)
    return null;

	$sql_columns="";
  foreach ($fields_array as $key => $value)
	{
    $columns_add="";
    //check for inner arrays
    if(isset($value['type']) && $value['type']=='array' && isset($value['inner'])){
      $fields_inner=$value['inner'];
      $columns_add= sql_columns_string($fields_inner, $prefix, $sufix);
    }
    else if (strlen($value['sql'])>0){
      if(isset($value['sqlname']) && strlen($value['sqlname'])>0)
        $columns_add = $prefix.$value['sqlname'].$sufix."\n";
      else $columns_add = $prefix.$key.$sufix."\n";
    }
    //echo $columns_add."\n";

    $sql_columns.= "\t";
    if (strlen(ltrim($sql_columns))>0)
      $sql_columns.= ",";

    if (strlen(ltrim($columns_add))>0)
        $sql_columns.= $columns_add;
  }
  return  $sql_columns;
}

//-----------------------------------------------
function sql_row_array($in, &$out, $fields_array)
{
  foreach ($fields_array as $key => $value)
  {
    //check for inner arrays
    if(isset($value['type']) && $value['type']=='array' && isset($value['inner'])){
      $fields_inner=$value['inner'];
      if(isset($in[$key]))
        $input_inner=$in[$key];
      else
        $input_inner=null;
      sql_row_array($input_inner, $out, $fields_inner);
    }
    else if (strlen($value['sql'])>0){
      if(isset($in[$key])){
        if(isset($value['type']) && $value['type']=='json')
          $v=json_encode($in[$key]);
        else
          $v=$in[$key];
      }
      else {
        $v=null;
      }

      if(is_array($v)){
        $v=array_flatten($v);
        $v=implode(", ",$v);
      }

      if(isset($value['sqlname']) && strlen($value['sqlname'])>0)
        $out[$value['sqlname']] = $v;
      else $out[$key] = $v;
    }
  } //foreach
  //no return values
}

//-----------------------------------------------
function db_insert2($conn,$data,$fields_array,$table_name)
{
	if( $conn==null ||
		$data==null ||
		$fields_array==null ||
		count($data)==0 ||
		count($fields_array)==0 ||
		strlen($table_name)==0
		)
		return false;

	//заполнение столбцов в SQL-запросе

	$sql_columns=sql_columns_string($fields_array, '[', ']');
	$sql_vals=sql_columns_string($fields_array, ':', '');

	$sql="INSERT INTO [".$table_name."] (\n"
		.$sql_columns
		.") VALUES ("
		.$sql_vals
		.")";

	//echo "sql=".$sql;

	$st = $conn->prepare($sql);

	$i=0;

  // loop through the array
  foreach ($data as $row) {
    //print_r($row);
    $r=array();
    sql_row_array($row, $r, $fields_array);
		//print_r($r);
    $st->execute($r);
    //$i=$i+$st->rowCount(); //doesn't work for insert
    unset($r);
		$i++;
  }
	return $i;
}

//-----------------------------------------------
function db_insert($conn,$data,$fields_array,$table_name)
{
	if( $conn==null ||
		$data==null ||
		$fields_array==null ||
		count($data)==0 ||
		count($fields_array)==0 ||
		strlen($table_name)==0
		)
		return false;

	//заполнение столбцов в SQL-запросе

	$sql_columns="";
	$sql_vals="";

	foreach ($fields_array as $key => $value)
	{
		//print "$key :\n";
		//print_r($value);
		if (strlen($value['sql'])>0)
		{
			if (strlen($sql_columns)>0)
				$sql_columns.= "\t,";
			else $sql_columns.= "\t";

			if(isset($value['sqlname']) && strlen($value['sqlname'])>0)
				$sql_columns.= "[".$value['sqlname']."]\n";
			else $sql_columns.= "[$key]\n";

			if (strlen($sql_vals)>0)
				$sql_vals.= "\t,";
			else $sql_vals.= "\t";

			$sql_vals.= ":$key\n";
		}
	}

	$sql="INSERT INTO [".$table_name."] (\n"
		.$sql_columns
		.") VALUES ("
		.$sql_vals
		.")";

	//echo "sql=".$sql;

	$st = $conn->prepare($sql);

	$i=0;

    // loop through the array
    foreach ($data as $row) {
		//print_r($row);
		foreach ($fields_array as $key => $value)
		{
			if (strlen($value['sql'])>0)
			{
				if(isset($row[$key]))
				{
					if(isset($value['type']) && strlen($value['type'])>0)
					{
						if($value['type']=='json')
							$r[$key]=json_encode($row[$key]);

						else if($value['type']=='array'){
							$ar=$row[$key];
							$r[$key]=implode(", ",$row[$key]);
						}
					}
					else
						$r[$key]=$row[$key];
				}
				else
					$r[$key]=null;
			}
		}

		//print_r($r);
		$st->execute($r);
		$i=$i+$st->rowCount();
		//$i++;
    }
	return $i;
}

function validate_json($str=NULL) {
    if (is_string($str)) {
        @json_decode($str);
        return (json_last_error() === JSON_ERROR_NONE);
    }
    return false;
}

/* sanitize_search_string - cleans up a search string submitted by the user to be passed
     to the database. NOTE: some of the code for this function came from the phpBB project.
   @arg $string - the original raw search string
   @returns - the sanitized search string */
function sanitize_search_string($string) {
	static $drop_char_match =   array('^', '$', '<', '>', '`', '\'', '"', '|', ',', '?', '~', '+', '[', ']', '{', '}', '#', ';', '!', '=');
	static $drop_char_replace = array(' ', ' ', ' ', ' ',  '',   '', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ');

	/* Replace line endings by a space */
	$string = preg_replace('/[\n\r]/is', ' ', $string);
	/* HTML entities like &nbsp; */
	$string = preg_replace('/\b&[a-z]+;\b/', ' ', $string);
	/* Remove URL's */
	$string = preg_replace('/\b[a-z0-9]+:\/\/[a-z0-9\.\-]+(\/[a-z0-9\?\.%_\-\+=&\/]+)?/', ' ', $string);

	/* Filter out strange characters like ^, $, &, change "it's" to "its" */
	for($i = 0; $i < count($drop_char_match); $i++) {
		$string =  str_replace($drop_char_match[$i], $drop_char_replace[$i], $string);
	}

	$string = str_replace('*', ' ', $string);

	return $string;
}


function user_log($message){
	echo date('Y-m-d H:i:s ').$message."\n";
	//$filename="/tmp/api.log";
	//file_put_contents($filename,date('Y-m-d H:i:s ').$message."\n",FILE_APPEND);
}

?>

<?php 
class _connect_{
	private static $CON=NULL, $server, $user, $pass, $name;
	public static $affected_rows, $error, $insert_id, $num_rows;
	public static function get(){
		return _connect_::$CON;
	}
	
	public static function set($db_server, $db_user, $db_pass, $db_name){
		_connect_::$server = $db_server;
		_connect_::$user = $db_user;
		_connect_::$pass = $db_pass;
		_connect_::$name = $db_name;
	}
	
	public static function query($sql){
		_connect_::$CON = mysql_pconnect(_connect_::$server, _connect_::$user, _connect_::$pass) or die ('Not connect');
		mysql_select_db(_connect_::$name, _connect_::$CON);
		@mysql_query('SET character_set_results=utf8', _connect_::$CON);
		@mysql_query('SET names=utf8', _connect_::$CON);
		@mysql_query('SET character_set_client=utf8', _connect_::$CON);
		@mysql_query('SET character_set_connection=utf8', _connect_::$CON);
		@mysql_query('SET character_set_results=utf8', _connect_::$CON);
		@mysql_query('SET collation_connection=utf8_unicode_ci', _connect_::$CON);
		$rs = mysql_query($sql, _connect_::$CON);
		if($rs){
			_connect_::$insert_id = preg_match('/^insert/i', trim($sql)) ? mysql_insert_id(_connect_::$CON) : 0;
			_connect_::$affected_rows = preg_match('/^(update|delete)/i', trim($sql)) ? mysql_affected_rows(_connect_::$CON) : 0;
			_connect_::$num_rows = preg_match('/^select/i', trim($sql)) ? mysql_num_rows($rs) : 0;
			mysql_close(_connect_::$CON);
			return $rs;
		}
		else{
			_connect_::$error = mysql_errno(_connect_::$CON) . ": " . mysql_error(_connect_::$CON);
			mysql_close(_connect_::$CON);
			return false;
		}
	}
	
	public static function escape_string($string){
		return @mysql_escape_string($string);
	}
	public static function fetch_assoc($result_query){
		return @mysql_fetch_assoc($result_query);
	}
}
?>
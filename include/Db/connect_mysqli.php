<?php 
class _connect_{
	private static $CON=NULL, $server, $user, $pass, $name;	
	public static $affected_rows, $error, $insert_id, $num_rows;
	public static function get(){
		return self::$CON;	
	}
	public static function set($db_server, $db_user, $db_pass, $db_name){
		self::$server = $db_server;
		self::$user = $db_user;
		self::$pass = $db_pass;
		self::$name = $db_name;
		self::$CON = new mysqli();	
	}
	public static function close(){
		@self::$CON->close();
	}
	public static function connect(){
		self::$CON->connect(self::$server, self::$user, self::$pass, self::$name);	
		self::$CON->query('SET character_set_results=utf8');
		self::$CON->query('SET names=utf8');
		self::$CON->query('SET character_set_client=utf8');
		self::$CON->query('SET character_set_connection=utf8');
		self::$CON->query('SET character_set_results=utf8');
		self::$CON->query('SET collation_connection=utf8_unicode_ci');
	}
	public static function query($sql){
		self::connect();
		$rs = self::$CON->query($sql);
		if($rs){
			@self::$insert_id = self::$CON->insert_id;
			@self::$affected_rows = self::$CON->affected_rows;
			@self::$num_rows = $rs->num_rows;
			return $rs;
		}
		else{
			self::$error = _connect_::$CON->errno . ": " . self::$CON->error;	
			return false;
		}
	}
	public static function escape_string($string){
		return @self::$CON->escape_string($string);	
	}
	public static function fetch_assoc($result_query){
		return $result_query ? $result_query->fetch_assoc() : NULL;
	}
}
?>
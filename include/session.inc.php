<?php 
class session{ 	
	private static $_ssn, $sb, $se;
	private static $a = array(0,1,2,3,4,5,6,7,8,9,'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'); /**36**/
	public static function start($timeout = 3600){
		@session_start(); 
		self::$_ssn = abs(crc32($_SERVER['HTTP_HOST'])) + filemtime(__FILE__);
		self::$sb = '_'.dechex(self::$_ssn);
		self::$se = '__'.dechex(self::$_ssn);
		$_SESSION[self::$sb]=time();
		$_SESSION[self::$se]=$timeout;
	}
	private static function convertnumber($num=0){
		if($num == 0)
			return '';
		$s = '';
		while($num){
			$m = $num % 36;
			$s = self::$a[$m].$s;
			$num = floor($num / 36);
		}
		return $s;
	}      
	private static function mencr($id){
		if(preg_match('/^[a-z][a-z0-9\-_]*$/i', $id)){
			$id = md5($id.self::$_ssn);
			$s = self::convertnumber('0x'.substr($id, 0, 8));
			$s.= self::convertnumber('0x'.substr($id, 8, 8));
			$s.= self::convertnumber('0x'.substr($id, 16, 8));
			$s.= self::convertnumber('0x'.substr($id, 24));
			return $s;
		}
	}
	public static function set($id, $value, $avf=false){
		$id = self::mencr($id);
		if($value && $id){
			$_SESSION[$id] = $value;
			$_SESSION[self::$sb]=time();
		}
		elseif($avf && $id){
			$_SESSION[$id] = "$value";
			$_SESSION[self::$sb]=time();
		}

	}

	public static function exist($id){
		if(isset($_SESSION[self::$sb]) && isset($_SESSION[self::$se]) && $_SESSION[self::$sb] + $_SESSION[self::$se] > time()){
			$_SESSION[self::$sb]=time();
			$id = self::mencr($id);
			return isset($_SESSION[$id]);
		}
		return false;
	}

	public static function get($id){
		if(isset($_SESSION[self::$sb]) && isset($_SESSION[self::$se]) && $_SESSION[self::$sb] + $_SESSION[self::$se] > time()){
			$_SESSION[self::$sb]=time();
			$id = self::mencr($id);
			return @$_SESSION[$id];
		}
		return NULL;
	}
	
	public static function delete($id){
		if(isset($_SESSION[self::$sb]) && isset($_SESSION[self::$se]) && $_SESSION[self::$sb] + $_SESSION[self::$se] > time()){
			$_SESSION[self::$sb]=time();
			$id = self::mencr($id);
			unset($_SESSION[$id]);
		}
	}
	
	public static function destroy(){
		session_destroy();
	}
}
?>
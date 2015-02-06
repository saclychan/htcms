<?php 
/* global function */
class gf{
	const EMAIL = '/^[A-Za-z0-9\._]+@[A-Za-z0-9]+[A-Za-z0-9\-\.]*\.[A-Za-z]{2,4}$/';
	
	private static 
		$_match=null,
		$_args_default,
		$_args=null,
		$_valid
		;
	
	/**
	 * match char /
	 */
	public static function url_match($match, array $default_value=NULL)
	{
		self::$_match = explode('/', $match);
		self::$_args = null;
		self::$_args_default = $default_value;
	}
	/**
	 * 
	 * @param array $value
	 */
	public static function set_default_value(array $value)
	{
		foreach ($value as $k=>$v)
			self::$_args_default[$k] = $v;
	}
	/**
	 * 
	 * @param string $key
	 * @return Ambigous <NULL, string, multitype:>
	 */
	public static function args($key='')
	{
		if(!self::$_args){
			$a = explode('?', $_SERVER['REQUEST_URI']); 
			$a = explode('/', @$a[0]); 
			if(self::$_match && is_array(self::$_match)) {
				$len = count($a) > count(self::$_match) ? count($a) : count(self::$_match);
				for($i=0; $i < $len; $i++) {
					@$value = $a[$i];
					isset(self::$_match[$i]) ? (self::$_args[self::$_match[$i]] = "$value") : (self::$_args[$i] = "$value");
				}
			}
			else
				self::$_args = $a;
		}
		return $key ? (isset(self::$_args[$key]) ? (self::$_args[$key] ? self::$_args[$key] : @self::$_args_default[$key]) : NULL) : self::$_args; 
	}
	public static function isset_match($key)
	{
		return (is_array(@self::$_match) && in_array($key, self::$_match));
	}
	/**
	 * 
	 */
	public static function url(array $array=null)
	{
		if(self::$_match && is_array(self::$_match)) {
			if(!self::$_args) {
				$b = explode('?', $_SERVER['REQUEST_URI']); 
				$a = explode('/', @$b[0]); 
				$len = count($a) > count(self::$_match) ? count($a) : count(self::$_match);
				for($i=0; $i < $len; $i++) {
					@$value = $a[$i];
					isset(self::$_match[$i]) ? (self::$_args[self::$_match[$i]] = "$value") : (self::$_args[$i] = "$value");
				}
				if(preg_match('/\-([a-z]+)([0-9]+)(\.[a-z]+)$/i', $a[count($a)-1], $a)){ 
					self::$_args['a']=@$a[1]; 
					self::$_args['b']=@$a[2]; 
					self::$_args['c']=@$a[3]; 
				}
			}
			$tmp = array();
			foreach (self::$_args as $key => $value)
				$tmp[$key] = isset($array[$key]) ? $array[$key] : ($value ? $value : @self::$_args_default[$key]);
			if(@$b[1])
				$tmp = implode('/', $tmp).'?'.$b[1];
			else
				$tmp = implode('/', $tmp);
			return preg_replace('/[\/]*$/', '', $tmp);
		}
		else 
			return $_SERVER['REQUEST_URI'];
	}

	public static function set_validate_post($array){
		if(is_array($array))
			foreach ($array as $key => $value) {
				if(is_array($value))
					self::$_valid[$key] = array(@$value[0], @$value[1]);
				else
					self::$_valid[$key] = array($value, "<font color='#FF0000'>invalid $key</font>");
			}
	}

	public static function invalid_post(){
		if($_SERVER['REQUEST_METHOD']=='POST' && is_array($_POST))
			foreach ($_POST as $key => $value) {
				if(isset(self::$_valid[$key]) && !preg_match(self::$_valid[$key][0], $value)){
					return self::$_valid[$key][1];
				}
			}
		return '';
	}
	
	public static function convert_str($str){
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
		$str = preg_replace("/(đ)/", 'd', $str);
		$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
		$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
		$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
		$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
		$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
		$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
		$str = preg_replace("/(Đ)/", 'D', $str);
	
		$str = preg_replace('/[^a-z0-9\-]+/i','-',$str);
		$str = preg_replace('/[\-]{1,}/','-',$str);
		return $str;
	}

	/*** ***/
	private static $is_mobile=NULL;
	public static function mobile_user_agent_switch(){
		if(!is_null(self::$is_mobile))
			return self::$is_mobile;
		else
		{
			$device = '';
			self::$is_mobile = 0;

			if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
				$device = "ipad";
				self::$is_mobile = 1;
			} else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
				$device = "iphone";
				self::$is_mobile = 1;
			} else if( stristr($_SERVER['HTTP_USER_AGENT'],'iPod') ) {
				$device = "iPod";
				self::$is_mobile = 1;
			} else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
				$device = "blackberry";
				self::$is_mobile = 1;
			} else if( stristr($_SERVER['HTTP_USER_AGENT'],'Nokia') ) {
				$device = "Nokia";
				self::$is_mobile = 1;
			} else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
				$device = "android";
				self::$is_mobile = 1;
			}
			return self::$is_mobile; 
		}
	}
}
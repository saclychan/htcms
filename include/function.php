<?php 
function L($s, $e=''){
	global $_lang;
	return isset($_lang[$s.$e])?$_lang[$s.$e]:$s;
}

function truncate($text, $chars = 25) {
    $text = $text." ";
    $text = substr($text,0,$chars);
    $text = substr($text,0,strrpos($text,' '));
    $text = $text."...";
    return $text;
}

/**
 * 
 * array1('a'=>1, 'b'=>2, 3)
 * array2('b'=>'bb', 'c'=>3, 'd'=>4, 0=>'cc')
 * return array('a'=>1, 'bb'=>2, 'cc'=>3)
 */
function array_replace_key(array $array1 , array $array2){
	$array = array();
	foreach ($array1 as $key=>$value)
	if(isset($array2[$key]))
		$array[$array2[$key]] = $value;
	else
		$array[$key] = $value;
	return $array;
}
function parseInt($value)
{
	if(!$value)
		return 0;
	if(preg_match('/^0*(\d+).*$/', $value, $array_match))
		return $array_match[1];
	else
		return 0;
}
function convert_str($str){
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
function paging($total=0, $maxitemonpage=10, $c=1, $_Maxpage=10){
	$total = (int)$total;
	$maxitemonpage = (int)$maxitemonpage;
	$c = ((int)$c<1) ? 1 : $c;
	$_Maxpage = (int)$_Maxpage;
	if($total<$maxitemonpage)
		return array('first'=>0, 'prev'=>0, 'from'=>1, 'to'=>1, 'next'=>0, 'last'=>0, 'total'=>1);
	$total = ceil($total/$maxitemonpage);
	if($total<=$_Maxpage)
		return array('first'=>(($c==1)?0:1), 'prev'=>(($c>1)?($c-1):1), 'from'=>1, 'to'=>$total, 'next'=>(($c<$total) ? ($c+1) : $total), 'last'=>(($c==$total)?0:$total), 'total'=>$total);
	$from = round($c - $_Maxpage/2);
	$from = ($from<1) ? 1 : $from;
	$to = $from + $_Maxpage - 1;
	if($to>$total){
		$to = $total;
		$from = $to - $_Maxpage + 1;
	}
	return array('first'=>1, 'prev'=>(($c>1)?($c-1):1), 'from'=>$from, 'to'=>$to, 'next'=>(($c<$total) ? ($c+1) : $total), 'last'=>$total, 'total'=>$total);
}
function array2string($array){
	if(!is_array($array))
		return '';
	$st = "array(";
	foreach($array as $k=>$v){
		if(is_array($v))
			$st.= "'".$k."'=>".array2string($v).", ".PHP_EOL;
		else
		if(is_string($v))
			$st.= "'".$k."'=>\"".addcslashes($v, '"')."\", ".PHP_EOL;
		else
			$st.= "'".$k."'=>\"".$v."\", ".PHP_EOL;
	}
	$st.= ')';
	return $st;
}
function get_web_page( $url ){
	$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle compressed
			CURLOPT_USERAGENT      => "t", 		// who am i
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	);
	$ch      = curl_init( $url );
	curl_setopt_array( $ch, $options );
	$content = curl_exec( $ch );
	$err     = curl_errno( $ch );
	$errmsg  = curl_error( $ch );
	$header  = curl_getinfo( $ch );
	curl_close( $ch );
	$header['errno']   = $err;
	$header['errmsg']  = $errmsg;
	$header['content'] = $content;
	return $header;
}
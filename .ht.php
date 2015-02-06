<?php 
if(!function_exists('__autoload') && !defined('__autoload')){
	function __autoload($cls){ 
		if(file_exists("include/$cls.inc.php") && is_file("include/$cls.inc.php")){
			include_once("include/$cls.inc.php");
		}
		else {
			$lib = explode(PATH_SEPARATOR, get_include_path());
			foreach($lib as $p){ 
				if(file_exists("$p/$cls.inc.php") && is_file("$p/$cls.inc.php")){
					include_once("$p/$cls.inc.php");
					break;
				} 
			}
		}
	} 
	spl_autoload_register('__autoload'); 
}
if(defined('__autoload')){
	@$d = dir('.');
	$f = __autoload;
	while (false !== ($entry = @$d->read())){
		is_dir($entry) && file_exists("$entry/$f") && is_file("$entry/$f") && include_once("$entry/$f");
	}
	@$d->close();
}

function __construct($URI=NULL){ 
	$d = defined('module') ? module : 'module'; 
	$df = defined('index') ? index : 'index'; 
	$URI || ($URI = $_SERVER['REQUEST_URI']); 
	if(preg_match('/^\/([a-z]+)\/([a-z0-9\-]*)\/?([a-z0-9_\-]*)/i', $URI, $a) && is_dir($dir = "$d/". $a[1])){ 
		$module = file_exists("$dir/$d.php") && is_file("$dir/$d.php") ? include("$dir/$d.php") : NULL;
		if(isset($module) && is_array($module)){
			$arg = isset($module[$d]) ? $module[$d] : (isset($module["regex$d"])&&!is_callable($module["regex$d"]) ? $module["regex$d"] : NULL);
			$result = isset($module["call$d"]) && is_callable($module["call$d"]) ? call_user_func($module["call$d"], $arg, @$a[1], @$a[2], @$a[3]) : NULL;
			$permission = isset($module["permission$d"]) ? (is_array($module["permission$d"]) ? $module["permission$d"] : is_callable($module["permission$d"]) ? call_user_func($module["permission$d"], $result) : array()):array();
			if(@$permission===false || @$permission[@$a[2]] === false || @$permission[@$a[2].'/'.@$a[3]] === false){
				return;
			}
			@$inc = isset($a[2])&&$a[2] ? (isset($module[$d][$a[2].'/'.$a[3]]) ? $module[$d][$a[2].'/'.$a[3]] : $module[$d][$a[2]]) : (isset($module[$d][$df]) ? $module[$d][$df] : NULL);
			if((!$inc || !is_array($inc)) && isset($module["regex$d"])){
				if(is_callable($module["regex$d"])){
					$module["regex$d"] = call_user_func($module["regex$d"], @$a[2], @$a[3]);
				}
				if(is_array($module["regex$d"])){
					foreach ($module["regex$d"] as $key => $value){
						if(preg_match($key, $a[2]) && is_array($value)){
							$inc = $value;
							break;
						}
					}
				}
			}
			if($inc && isset($inc['file'])){
				call_user_func(function($file, $call, $arg, $class, $permission){
					$file && include_once($file);
					$call && is_callable($call) && call_user_func($call, $arg, $permission);
					$class && class_exists($clsName = $class[0]) && (new $clsName($class[1], $class[2], $class[3], $permission));
				}, (file_exists($dir.'/'.$inc['file'].'.php') && is_file($dir.'/'.$inc['file'].'.php') ? ($dir.'/'.$inc['file'].'.php') : ''), @$inc['call'], @$inc['arg'], array(@$inc['class'], @$a[1], @$a[2], @$a[3]), $permission);
			}
		}
		else{
			file_exists("$dir/$df.php") && is_file("$dir/$df.php") && include_once("$dir/$df.php");
		}
	} 
	else{
		if(preg_match('/^\/([^\/]*)\/?([^\/]*)/', $URI, $a)){ 
			$dir = defined('moduledefault') ? ("$d/".moduledefault): "$d/$df";
			$module = file_exists("$dir/$d.php") && is_file("$dir/$d.php") ? include("$dir/$d.php") : NULL;
			if(isset($module) && is_array($module)){
				$arg = isset($module[$d]) ? $module[$d] : (isset($module["regex$d"])&&!is_callable($module["regex$d"]) ? $module["regex$d"] : NULL);
				$result = isset($module["call$d"]) && is_callable($module["call$d"]) ? call_user_func($module["call$d"], $arg, '', @$a[1], @$a[2]) : NULL;
				$permission = isset($module["permission$d"]) ? (is_array($module["permission$d"]) ? $module["permission$d"] : is_callable($module["permission$d"]) ? call_user_func($module["permission$d"], $result) : array()):array();
				if(@$permission===false || @$permission[@$a[1]] === false || @$permission[@$a[1].'/'.@$a[2]] === false){
					return;
				}
				@$inc = isset($a[1])&&$a[1] ? (isset($module[$d][$a[1].'/'.$a[2]]) ? $module[$d][$a[1].'/'.$a[2]] : $module[$d][$a[1]]) : (isset($module[$d][$df]) ? $module[$d][$df] : NULL);

				if((!$inc || !is_array($inc)) && isset($module["regex$d"])){
					if(is_callable($module["regex$d"])){
						$module["regex$d"]=call_user_func($module["regex$d"], @$a[1], @$a[2]);
					}
					if(is_array($module["regex$d"])){
						foreach ($module["regex$d"] as $key => $value){ 
							if(preg_match($key, $a[1]) && is_array($value)){
								$inc = $value;
								break;
							}
						}
					}
				}
				if($inc && isset($inc['file'])){
					call_user_func(function($file, $call, $arg, $class, $permission){
						$file && include_once($file);
						$call && is_callable($call) && call_user_func($call, $arg, $permission);
						$class && class_exists($clsName = $class[0]) && (new $clsName($class[1], $class[2], $class[3], $permission));
					}, (file_exists($dir.'/'.$inc['file'].'.php') && is_file($dir.'/'.$inc['file'].'.php') ? ($dir.'/'.$inc['file'].'.php') : ''), @$inc['call'], @$inc['arg'], array(@$inc['class'], '', @$a[1], @$a[2]), $permission);
				}
			}
			else{
				file_exists("$dir/$df.php") && is_file("$dir/$df.php") && include_once("$dir/$df.php");
			}
		}
	}
}?>
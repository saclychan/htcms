<?php 
if(!function_exists('__autoload')){
    function __autoload($cls){ 
        if(file_exists(dirname(__FILE__)."/$cls.inc.php") && is_file(dirname(__FILE__)."/$cls.inc.php"))
        {
            include_once(dirname(__FILE__)."/$cls.inc.php");
        }
        else 
        if(file_exists(dirname(__FILE__)."/sb-admin/$cls.inc.php") && is_file(dirname(__FILE__)."/sb-admin/$cls.inc.php"))
        {
            include_once(dirname(__FILE__)."/sb-admin/$cls.inc.php");
        }
        else 
        {
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
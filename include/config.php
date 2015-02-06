<?php 
/**
 */
define('ReportError', true);
date_default_timezone_set('Asia/Ho_Chi_Minh');

/* Document Root */
define('DR', dirname(__FILE__).'/../');
define('__autoload', '.htautoload.php');

define('time_install', strtotime(date('m/d/Y', filemtime(__FILE__))));

define('db_server', '127.0.0.1');
define('db_user', 'root');
define('db_pass', '');
define('db_name','htphp');
/* test.appnet.vn */
/*define('db_server', 'db.hostvn.net');
define('db_user', 'thanhapp_test');
define('db_pass', 'Jf9sQc8szhu');
define('db_name','thanhapp_test');*/

define('FolderUpload', DR.'public/upload/');
define('UrlUploadBase', '/public/upload/');
define('CacheDir', DR.'public/cache/');
define('SYS_LANG', 'vi');
define('Max_item_admin', 10);

//define('HOST', preg_replace(array('/^www\./i', '/([^a-z0-9\.\-])/i'), array('',''), $_SERVER['HTTP_HOST']));
//define('SMARTY_LIB', DR.'vendor/smarty');
//define('SMARTY_C', DR.'public/smarty');
#define('SMARTY_COMPILE_DIR', __DIR__.'/public/smarty_c');
#define('SMARTY_CACHE_DIR', __DIR__.'/public/smarty_cache');
global $config;
$config = array();
//define('moduledefault', 'default');
define('default_lang', 'vi');
$config['language']['vi'] = 'Tieng Viet';
//$config['language']['en'] = 'Tieng Anh';
<?php 
class View
{
    private static 
    $_view_dir='', 
    $_sub_dir, 
    $_msg, 
    $_urlredirect, 
    $V, 
    $_msg_type='',
    $_layout='',
    $_head='',
    $_postsession='', 
    $_dashboard = '', 
    $_foot='';
    /** 
    * set static value
    */  
    public static function set($name, $value, $overwrite=false){
        ($overwrite || !isset(self::$V[$name])) && (self::$V[$name] = $value);
    }
    /**
    * get static value
    */
    function get($name){
        if(isset(self::$V[$name]))
            return self::$V[$name];
        else
            foreach (self::$V as $match => $value) {
                if(@preg_match($match, $name))
                    return $value;
            }
            return NULL;
        }
    /** 
    * set view directory path
    */  
    public static function set_dir($path){
        self::$_view_dir = $path;
    }
    /** 
    * set sub view directory path
    */
    public static function set_sub_dir($dirname){
        self::$_sub_dir = $dirname; 
    }

    function message($mesage, $url=''){
        self::$_msg = $mesage;
        self::$_urlredirect = urlencode($url);
        self::$_msg_type = '';
    }
    function message_error($mesage, $url=''){
        self::$_msg = $mesage;
        self::$_urlredirect = urlencode($url);
        self::$_msg_type = 'error';
    }
    function message_warning($mesage, $url=''){
        self::$_msg = $mesage;
        self::$_urlredirect = urlencode($url);
        self::$_msg_type = 'warning';
    }
    /**
     * if not null $path2 set head and foot
     * @param string $path1 path to file
     * @param string $path2 path to file
     */
    public static function layout($path1=NULL, $path2=NULL){
        if(!is_null($path2)){
            self::$_head = $path1;
            self::$_foot = $path2;
        }
        elseif(!is_null($path1)){
            self::$_layout = $path1;
        }
        else{
            self::$_head = '';
            self::$_foot = '';
            self::$_layout = '';
        }
    }
    public static function head($path=''){
        self::$_head = $path;
    }
    public static function foot($path=''){
        self::$_foot=$path;
    }
    function POSTSESSION($postsession=''){
        if($postsession)
            self::$_postsession = $postsession;
        else 
            echo '<input type="hidden" name="POSTSESSION" value="'.self::$_postsession.'" />';
    }
    function dashboard($url='', $text='Dashboard')
    {
        if($url)
            self::$_dashboard = '<a href="'.$url.'">'.$text.'</a>';
        else
            echo self::$_dashboard;
    }
    private static $_role;
    /**
    * @param array $role
    */
    public static function set_role(array $role)
    {
        self::$_role = $role ? $role : array();
    }
    /**
    * @return boolean
    */
    private function role($role_name){
        if($role_name){
            $ar = explode('/', $role_name);
            foreach ($ar as $r) {
                if(isset(self::$_role["/$r"]) && self::$_role["/$r"])
                    return true;
            }
        }
        return false;
    }
    /** 
    * load view .html | .tpl.php | .phtml
    */  
    public function load_html($filename, $file_replace='', $layoutOff=false, $headOff=false, $footOff=false){
        if(!$layoutOff && self::$_layout && file_exists(self::$_layout) && is_file(self::$_layout))
        {
            self::$contentfilename = $filename;
            self::$contentfile_replace = $file_replace;
            include_once(self::$_layout);
        }
        else
        {
            !$headOff && self::$_head && file_exists(self::$_head) && is_file(self::$_head) && include_once(self::$_head);
            @$inc = self::$_view_dir .'/'. self::$_sub_dir . preg_replace('/^([a-z0-9_]+)$/i', "/$1", $filename);
            is_file(self::$_view_dir .'/'. self::$_sub_dir . '/' . $file_replace) ? include_once(self::$_view_dir .'/'. self::$_sub_dir . '/' . $file_replace) : (is_file("$inc.html") ? include_once("$inc.html") : (is_file("$inc.tpl.php") ? include_once("$inc.tpl.php") : (is_file("$inc.phtml") && include_once("$inc.phtml"))));
            !$footOff && self::$_foot && file_exists(self::$_foot) && is_file(self::$_foot) && include_once(self::$_foot);
        }
    }
    private static $contentfilename, $contentfile_replace;
    private function content()
    {
        @$inc = self::$_view_dir .'/'. self::$_sub_dir . preg_replace('/^([a-z0-9_]+)$/i', "/$1", self::$contentfilename);
        is_file(self::$_view_dir .'/'. self::$_sub_dir . '/' . self::$contentfile_replace) ? include_once(self::$_view_dir .'/'. self::$_sub_dir . '/' . self::$contentfile_replace) : (is_file("$inc.html") ? include_once("$inc.html") : (is_file("$inc.tpl.php") ? include_once("$inc.tpl.php") : (is_file("$inc.phtml") && include_once("$inc.phtml"))));
    }
}
?>
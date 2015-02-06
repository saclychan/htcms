<?php 
/**
 * Controller(module_dir, module, action)
 */
abstract class Controller{
    private $V, $R, $layoutOff=false, $headOff=false, $footOff=false;
    protected $view_dir, $d, $m, $a;
    public static $VIEW_TYPE='';
    abstract protected function init();
    
    function __construct($d='', $m='', $a='')
    {
        $this->d = $d; 
        $this->m = $m; 
        $this->a = str_ireplace('-', '_', $a);
        $this->V = new View();
        $this->R = array();
        $cls = get_class($this);
        $this->view_dir = $cls;
        if($this->init()!==false){
            $action = preg_match('/^([a-z]+[a-z0-9_]*)$/i', $this->a) ? ($this->a.'_action') : '_action';
            
            if(method_exists($this, $action)){
                $ref = new ReflectionMethod($cls, $action);
                $view = $ref->isPublic() ? $ref->invoke($this) : false;
                View::set_sub_dir($this->view_dir);
                if($view !==false)
                {
                    $key = 'p'.filemtime(__FILE__);
                    session::set($key, dechex(time()));
                    $this->V->POSTSESSION(session::get($key));
                    $this->d ? $this->V->dashboard('/'.$this->d.'/', L('dashboard')) : $this->V->dashboard('/', L('dashboard'));
                    $this->V->set_role($this->R);
                    $this->V->load_html($action, $view, $this->layoutOff, $this->headOff, $this->footOff);
                }
            }
        }
    }
    protected function set_layout_off($status=true)
    {
        $this->layoutOff = $status;
    }
    protected function set_head_off($status=true)
    {
        $this->headOff = $status;
    }
    protected function set_foot_off($status=true)
    {
        $this->footOff = $status;
    }
    protected function role($role_name)
    {
        $this->R["/$role_name"] = true;
    }
    /**
     * assign variables to view
     * @param string $name
     * @param mixed $value
     */
    protected function add($name, $value)
    {
        if(preg_match('/^[a-z_]+[a-z0-9_]*$/i', $name))
            $this->V->$name = $value;
    }
    /**
     * return true if POST
     * not POST when reload page
     */
    protected function request_POST()
    {
        /* true is session post */
        if(false)
        {
            $ssn = dechex(filemtime(__FILE__));
            if(session::exist("a$ssn")){
                $_POST = session::get("a$ssn");
                session::delete("a$ssn");
                return true;
            }
            if($_SERVER['REQUEST_METHOD']=='POST'){
                session::set("a$ssn", $_POST);
                header('location: '.$_SERVER['REQUEST_URI']);
                exit();
            }
        }
        else
        {
            if($_SERVER['REQUEST_METHOD']=='POST')
            {
                $key = 'p'.filemtime(__FILE__);
                if(isset($_POST['POSTSESSION']) && session::exist($key) && $_POST['POSTSESSION']!=session::get($key))
                    return false;
                else
                    session::set($key, '');
                return true;
            }
        }
        return false;
    }
    protected function clear_POST(array $status=null)
    {
        $tmp = dechex(filemtime(__FILE__));
        if(session::exist("a$tmp")){
            $tmp = session::get("a$tmp");
            session::delete("a$tmp");
            return $tmp;
        }
        if(is_array($status)){
            session::set("a$tmp", $status);
            header('location: '.$_SERVER['REQUEST_URI']);
            exit();
        }
    }
    protected function message($mesage, $url='')
    {
        $this->V->message($mesage, $url);
    }
    protected function message_error($mesage, $url='')
    {
        $this->V->message_error($mesage, $url);
    }
    protected function message_warning($mesage, $url='')
    {
        $this->V->message_warning($mesage, $url);
    }
    protected function header_json()
    {
        header('Content-type: application/json');
    }
    protected function header_xml()
    {
        header("Content-Type: application/xml; charset=utf-8");
    }
    protected function header_download($attach_file_name, $file_size=0)
    {
        header("Content-Disposition: attachment; filename=" . $attach_file_name);
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Description: File Transfer");
        if($file_size){
            header("Content-Length: " . $file_size);
        }
    }
}
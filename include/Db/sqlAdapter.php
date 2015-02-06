<?php 
defined('PREFIX')||define('PREFIX', '');
defined('ReportError')||define('ReportError', false);
class sqlAdapter
{
    private 
        $SQLcmd = '', 
        $target,
        $resource;
    public  
        $affected_rows, 
        $lastid, 
        $error
    ;
    
    function __construct($sqlcmd){
        if(is_null(_connect_::get()) && defined('db_server') && defined('db_user') && defined('db_pass') && defined('db_name')){
            _connect_::set(db_server, db_user, db_pass, db_name);
        }
        if(is_a($sqlcmd, 'SqlSelectAdapter'))
            $sqlcmd = $sqlcmd->__toString();
        else
        if(is_object($sqlcmd)){
            $tmp = key($sqlcmd);
            $this->target = $tmp;
            $sqlcmd = $sqlcmd->$tmp;
        }
        $this->SQLcmd = preg_replace('/`\{([a-z][a-z0-9_]*)\}`/i',"`".PREFIX."$1`",trim($sqlcmd));
        if(preg_match('/^select/i',$this->SQLcmd)){
            $rs = _connect_::query($this->SQLcmd);
            if(!$rs && ReportError){
                printf(_connect_::$error);
            }
            if(_connect_::$num_rows>0)
                $this->resource = $rs;
        }
        else
            return $this->Execute();
    }
    
    private function Execute()
    {
        if(preg_match('/^(insert|update|delete)/i',$this->SQLcmd))
        {
            $rs = _connect_::query($this->SQLcmd);
            if($rs)
            {
                $this->affected_rows = _connect_::$affected_rows;
                $this->lastid = _connect_::$insert_id;
                $this->error = NULL;
            }
            elseif(ReportError) 
            {
                $this->error = _connect_::$error;
            }
        }
        return $this;
    }
    
    function __toString()
    {
        return $this->SQLcmd;
    }
    /**
     * 
     * @param string $feild_key 
     * @param string $group
     * @return multitype:unknown NULL
     * key is not null
     */
    function fetch($feild_key='', $group=false){
        if($this->resource){
            $rt = array();
            $row = _connect_::fetch_assoc($this->resource);
            if(!is_null($feild_key) && isset($row[$feild_key]) && $row[$feild_key]!=''){
                if($group){
                    $rt[$row[$feild_key]][] = $row;
                    while ($row = _connect_::fetch_assoc($this->resource))
                        $rt[$row[$feild_key]][] = $row;
                }
                else{
                    $rt[$row[$feild_key]] = $row;
                    while ($row = _connect_::fetch_assoc($this->resource))
                        $rt[$row[$feild_key]] = $row;
                }
            }
            else{
                $rt[] = $row;
                while ($row = _connect_::fetch_assoc($this->resource))
                    $rt[] = $row;
            }
            if($this->target && isset($rt[0][$this->target]))
                return $rt[0][$this->target];
            return $rt;
        }
    }
    /**
     * 
     * @param string $feild_value
     * @return multitype:
     */
    function fetch_array_key($feild_value='')
    {
        if($this->resource)
        {
            $rt = array();
            $point = NULL;
            while ($row = _connect_::fetch_assoc($this->resource))
            {
                $i = 1;
                $c = count($row);
                foreach ($row as $k=>$v)
                {
                    if(is_null($point) || $i==1)
                    {
                        if(!isset($rt["$v"]))
                            $rt["$v"] = $i==$c ? ($k == $feild_value ? $v : 1) : array();
                        $point = &$rt[$v];
                    }
                    else
                    {
                        if(!isset($point["$v"]))
                            $point["$v"] = $i==$c ? ($k == $feild_value ? $v : 1) : array();
                        $point = &$point[$v];
                    }
                    $i++;
                }
            }
            return $rt;
        }
    }
    
    function fetch_once($column, $key='', $group=false)
    {
        if($this->resource){
            $rt = array();
            if($key){
                if($group){
                    while ($row = _connect_::fetch_assoc($this->resource)){
                        if(isset($row[$column]) && isset($row[$key])){
                            $rt[$row[$key]][] = $row[$column];
                        }
                    }
                }
                else{
                    while ($row = _connect_::fetch_assoc($this->resource)){
                        if(isset($row[$column]) && isset($row[$key])){
                            $rt[$row[$key]] = $row[$column];
                        }
                    }
                }
            }
            else{
                while ($row = _connect_::fetch_assoc($this->resource)){
                    if(isset($row[$column])){
                        $rt[] = $row[$column];
                    }
                }
            }
            return $rt;
        }
    }
}
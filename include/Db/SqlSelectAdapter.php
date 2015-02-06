<?php 
defined('PREFIX')||define('PREFIX', '');
defined('ReportError')||define('ReportError', false);
class SqlSelectAdapter
{
    private $feild, $table, $_distinct='', $_sql_cache='', $_select, $_SQL_CALC_FOUND_ROWS='';
    function __construct($feild, $table)
    {
        $this->_select = array(
                'HIGH_PRIORITY'=>0, 
                'STRAIGHT_JOIN'=>0,
                'SQL_SMALL_RESULT'=>0,
                'SQL_BIG_RESULT'=>0,
                'SQL_BUFFER_RESULT'=>0,
            );
      //[SQL_CACHE | SQL_NO_CACHE] [SQL_CALC_FOUND_ROWS)
        $this->feild = array();
        $this->table = '';
        if(!is_array($feild))
            $feild = explode(',', str_ireplace('`', '', $feild));
        if(is_array($table))
        {
            $key = key($table);
            $value = $table[$key];
            if(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) && preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $value)){
                $tb = '`'.$key.'`';
                $this->table = '`'.PREFIX.$value.'` AS `'.$key.'`';
                $table = PREFIX.$value;
            }
        }
        elseif(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $table))
        {
            $tb = $this->table.= "`".PREFIX."$table`";
            $table = PREFIX.$table;
        }
        else
            $tb = '';
        if($tb)
        {
            if(!ReportError){
                $lsf = dbtable::get_table_schema($table);
            }            
            foreach ($feild as $key => $value) {
                $key = trim($key);
                $tmp = '';
                if(is_object($value) && isset($value->scalar)){
                    $tmp = $value->scalar;
                    $this->feild[] = preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) ? "$tmp AS `$key`" : $tmp;
                }
                elseif(!is_array($value)){
                    $value = trim($value);
                    if(isset($lsf[$value]) || $value=='*' || !isset($lsf)) {
                        if(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $value))
                            $tmp.= "$tb.`$value`";
                        elseif($value=='*')
                            $tmp.= "$tb.*";
                        elseif($value!='')
                            $tmp.= $value;
                        if($tmp)
                            $this->feild[] = preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) ? "$tmp AS `$key`" : $tmp;
                    }
                }
            }
        }
    }
    /**
     * ALL | DISTINCT | DISTINCTROW
     */
    function distinct($distinct='DISTINCT'){
        $this->_distinct = $distinct;
        return $this;
    }
    /**
     * SQL_CACHE | SQL_NO_CACHE
     */
    function sql_cache($status=true){
        $this->_sql_cache = $status ? 'SQL_CACHE' : 'SQL_NO_CACHE';
        return $this;
    }
    function HIGH_PRIORITY(){
        $this->_select['HIGH_PRIORITY'] = 1;
        return $this;
    }
    function STRAIGHT_JOIN(){
        $this->_select['STRAIGHT_JOIN'] = 1;
        return $this;
    }
    function SQL_SMALL_RESULT(){
        $this->_select['SQL_SMALL_RESULT'] = 1;
        return $this;
    }
    function SQL_BIG_RESULT(){
        $this->_select['SQL_BIG_RESULT'] = 1;
        return $this;
    }
    function SQL_BUFFER_RESULT(){
        $this->_select['SQL_BUFFER_RESULT'] = 1;
        return $this;
    }
    function SQL_CALC_FOUND_ROWS()
    {
        $this->_SQL_CALC_FOUND_ROWS = 'SQL_CALC_FOUND_ROWS';
        return $this;
    }


    private $join;
    function innerjoin($table, $on, $feild=NULL)
    {
        if(!is_null($feild) && !is_array($feild))
            $feild = explode(',', str_ireplace('`', '', $feild));
        if(is_array($table))
        {
            $key = key($table);
            $value = $table[$key];
            if(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) && preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $value)){
                $tb = '`'.$key.'`';
                $this->join[] = 'INNER JOIN `'.PREFIX.$value.'` AS `'.$key."` ON $on";
                $table = PREFIX.$value;
            }
        }
        elseif(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $table))
        {
            $tb = "`".PREFIX."$table`";
            $this->join[].= "INNER JOIN `".PREFIX."$table` ON $on";
            $table = PREFIX.$table;
        }
        else
            $tb = '';
        if($tb && $feild)
        {
            if(!ReportError){
                $lsf = dbtable::get_table_schema($table);
            }
            foreach ($feild as $key => $value) {
                $key = trim($key);
                $tmp = '';
                if(is_object($value) && isset($value->scalar)){
                    $tmp = $value->scalar;
                    $this->feild[] = preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) ? "$tmp AS `$key`" : $tmp;
                }
                elseif(!is_array($value)){
                    $value = trim($value);
                    if(isset($lsf[$value]) || $value=='*' || !isset($lsf)) {
                        if(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $value))
                            $tmp.= "$tb.`$value`";
                        elseif($value=='*')
                            $tmp.= "$tb.*";
                        elseif($value!='')
                            $tmp.= $value;
                        if($tmp)
                            $this->feild[] = preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) ? "$tmp AS `$key`" : $tmp;
                    }
                }
            }
        }
        return $this;
    }
    function leftjoin($table, $on, $feild=NULL)
    {
        if(!is_null($feild) && !is_array($feild))
            $feild = explode(',', str_ireplace('`', '', $feild));
        if(is_array($table))
        {
            $key = key($table);
            $value = $table[$key];
            if(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) && preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $value)){
                $tb = '`'.$key.'`';
                $this->join[] = 'LEFT JOIN `'.PREFIX.$value.'` AS `'.$key."` ON $on";
                $table = PREFIX.$value;
            }
        }
        elseif(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $table))
        {
            $tb = "`".PREFIX."$table`";
            $this->join[].= "LEFT JOIN `".PREFIX."$table` ON $on";
            $table = PREFIX.$table;
        }
        else
            $tb = '';
        if($tb && $feild)
        {
            if(!ReportError){
                $lsf = dbtable::get_table_schema($table);
            }
            foreach ($feild as $key => $value) {
                $key = trim($key);
                $tmp = '';
                if(is_object($value) && isset($value->scalar)){
                    $tmp = $value->scalar;
                    $this->feild[] = preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) ? "$tmp AS `$key`" : $tmp;
                }
                elseif(!is_array($value)){
                    $value = trim($value);
                    if(isset($lsf[$value]) || $value=='*' || !isset($lsf)) {
                        if(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $value))
                            $tmp.= "$tb.`$value`";
                        elseif($value=='*')
                            $tmp.= "$tb.*";
                        elseif($value!='')
                            $tmp.= $value;
                        if($tmp)
                            $this->feild[] = preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) ? "$tmp AS `$key`" : $tmp;
                    }
                }
            }
        }
        return $this;
    }
    function rightjoin($table, $on, $feild=NULL)
    {
        if(!is_null($feild) && !is_array($feild))
            $feild = explode(',', str_ireplace('`', '', $feild));
        if(is_array($table))
        {
            $key = key($table);
            $value = $table[$key];
            if(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) && preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $value)){
                $tb = '`'.$key.'`';
                $this->join[] = 'RIGHT JOIN `'.PREFIX.$value.'` AS `'.$key."` ON $on";
                $table = PREFIX.$value;
            }
        }
        elseif(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $table))
        {
            $tb = "`".PREFIX."$table`";
            $this->join[].= "RIGHT JOIN `".PREFIX."$table` ON $on";
            $table = PREFIX.$table;
        }
        else
            $tb = '';
        if($tb && $feild)
        {
            if(!ReportError){
                $lsf = dbtable::get_table_schema($table);
            }
            foreach ($feild as $key => $value) {
                $key = trim($key);
                $tmp = '';
                if(is_object($value) && isset($value->scalar)){
                    $tmp = $value->scalar;
                    $this->feild[] = preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) ? "$tmp AS `$key`" : $tmp;
                }
                elseif(!is_array($value)){
                    $value = trim($value);
                    if(isset($lsf[$value]) || $value=='*' || !isset($lsf)) {
                        if(preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $value))
                            $tmp.= "$tb.`$value`";
                        elseif($value=='*')
                            $tmp.= "$tb.*";
                        elseif($value!='')
                            $tmp.= $value;
                        if($tmp)
                            $this->feild[] = preg_match('/^([a-z_]+[a-z0-9_]*)$/i', $key) ? "$tmp AS `$key`" : $tmp;
                    }
                }
            }
        }
        return $this;
    }
    private $where;
    /**
     *
     */
    function where($condition, $value=NULL, $and=true)
    {
        /*if("'$condition'" == "'`a`.`article_type`='"){
            echo (int)is_null($value);
            echo strlen(trim($value));
            die("'$value'\n");
        }*/
        if(!is_null($value)){
            if(strlen(trim($value))>0){
                $this->where[] = array("$condition".(is_numeric($value) ? $value : ("'"._connect_::escape_string($value)."'")), $and);
            }
        }
        elseif(is_array($condition))
        {
            $tmp = array();
            foreach ($condition as $key => $value) {
                if(strlen(trim($value))>0){
                    $tmp[] = "$key=".(is_numeric($value) ? $value : ("'"._connect_::escape_string($value)."'"));
                }
            }
            $this->where[] = array(implode(' AND ', $tmp), $and);
        }
        elseif(strlen(trim($condition))>0){
            $this->where[] = array("$condition", $and);
        }
        return $this;
    }
    private $groups;
    function group($spec){
        $this->groups[] = $spec;
        return $this;
    }
    private $having_str='';
    function having($condition){
        $this->having_str = " HAVING $condition";
        return $this;
    }
    private $orders;
    function order($feild, $asc=true)
    {
        $this->orders[] = $asc ? "$feild" : "$feild DESC";
        return $this;
    }
    private $limitoffset;
    function limit($limit, $page)
    {
        if($limit)
            $this->limitoffset = " LIMIT $limit";
        if(is_numeric($page) && $page>0)
            $this->limitoffset.= " OFFSET ".(($page-1)*$limit);
        return $this;
    }
    function __toString()
    {
        $sql = "SELECT ".$this->_distinct;
        /**/
        foreach ($this->_select as $key => $value) {
            $sql.= $value ? " $key" : '';
        }
        $sql.= $this->_sql_cache;
        $sql.= $this->_SQL_CALC_FOUND_ROWS;
        
        $sql.= " ".implode(', ', $this->feild);
        $sql.= " FROM ".$this->table;
        $sql.= $this->join ? (" ".implode(' ', $this->join)) : '';
        if($this->where){
            $sql.= " WHERE ";
            for($i=0; $i<count($this->where); $i++){
                $sql.= $i ? ($this->where[$i][1] ? " AND " : " OR ") : "";
                $sql.= '('.$this->where[$i][0].')';
            }
//              $sql.= $i ? (" WHERE ".implode(")AND(", $this->where)) : "";
        }
//      $sql.= $this->where ? (" WHERE ".implode(")AND(", $this->where)) : "";
        if($this->groups){
            $sql.= " GROUP BY ".implode(', ', $this->groups);
            $sql.= $this->having_str;
        }
        $sql.= $this->orders ? (" ORDER BY ".implode(', ', $this->orders)) : "";
        return $sql. $this->limitoffset;
    }
    function getCount(){
        $sql = "SELECT COUNT(*) `counter` ";
        $sql.= " FROM ".$this->table;
        if(is_array($this->join))
            foreach ($this->join as $join) {
                if(!preg_match('/^left[ ]+join/i', $join))
                    $sql.= " $join";
            }
        if($this->where){
            $sql.= " WHERE ";
            for($i=0; $i<count($this->where); $i++){
                $sql.= $i ? ($this->where[$i][1] ? " AND " : " OR ") : "";
                $sql.= '('.$this->where[$i][0].')';
            }
        }
        return (object)array('counter' => $sql." LIMIT 1");
    }
}
<?php 
defined('PREFIX')||define('PREFIX', '');
defined('ReportError')||define('ReportError', false);
include_once 'Db/connect_mysqli.php';
include_once 'Db/sqlAdapter.php';
include_once 'Db/SqlSelectAdapter.php';

abstract class dbtable{
	private $_cmd;
	private static $_table_schema;
	protected $_name = '';
	protected $lastid, $error, $affected_rows;
	
	function __construct(){
		if(is_null(_connect_::get()) && defined('db_server') && defined('db_user') && defined('db_pass') && defined('db_name')){
			_connect_::set(db_server, db_user, db_pass, db_name);
		}
	}
	
	function table_schema($tbname, $data=NULL){
		$tbname = PREFIX.$tbname;
		if(!isset(self::$_table_schema[$tbname])){
			$rs = _connect_::query("SELECT `COLUMN_NAME`, `COLUMN_TYPE`, `COLUMN_DEFAULT`, `COLUMN_KEY` FROM information_schema.columns WHERE table_schema = '".db_name."' AND table_name = '$tbname'");
			while ($r = _connect_::fetch_assoc($rs))
				self::$_table_schema[$tbname][$r['COLUMN_NAME']] = array('type'=>$r['COLUMN_TYPE'], 'default'=>$r['COLUMN_DEFAULT'], 'key'=>$r['COLUMN_KEY']);
		}
		if(is_null($data)){
			return self::$_table_schema[$tbname];
		}
		elseif(is_array($data)){
			$a = array();
			foreach($data as $key=>$value)
				if(isset(self::$_table_schema[$tbname][$key]))
					$a[$key] = $value;
			return $a;
		}
	}

	public static function get_table_schema($tbname){
		$tbname = str_ireplace('`', '', $tbname);
		if(!isset(self::$_table_schema[$tbname])){
			$rs = _connect_::query("SELECT `COLUMN_NAME`, `COLUMN_TYPE`, `COLUMN_DEFAULT`, `COLUMN_KEY` FROM information_schema.columns WHERE table_schema = '".db_name."' AND table_name = '$tbname'");
			while ($r = _connect_::fetch_assoc($rs))
				self::$_table_schema[$tbname][$r['COLUMN_NAME']] = array('type'=>$r['COLUMN_TYPE'], 'default'=>$r['COLUMN_DEFAULT'], 'key'=>$r['COLUMN_KEY']);
		}
		return isset(self::$_table_schema[$tbname]) ? self::$_table_schema[$tbname] : array();
	}

	function get_num($mixed){
		if(!$mixed)
			return 0;
		if(preg_match('/^(\d+\.?\d+).*$/', $mixed, $array_match))
			return preg_replace('/^0*/', '', $array_match[1]);
		else
			return 0;
	}
	/**
	 * 
	 * @param unknown $sql
	 * @return sqlAdapter
	 */
	function query($sql, $param=NULL){
		if(is_array($param) && !is_object($sql) && !is_array($sql)){
			foreach ($param as $key => $value) {
				if(is_object($value) && isset($value->scalar))
					str_ireplace("{$key}", $value->scalar, $sql);
				elseif(!is_null($value) && !is_array($value) && !is_object($value) && trim($value)!=''){
					$value = is_numeric($v) ? $v :("'"._connect_::escape_string($v)."'");
					str_ireplace("{$key}", $value->scalar, $sql);
				}
			}
		}
		return new sqlAdapter($sql);
	}
	
	function affected_rows(){
		return _connect_::$affected_rows;
	}
	
	function error(){
		return _connect_::$error;
	}
	
	function lastid(){
		return _connect_::$insert_id;
	}
	
	function __toString(){
		return $this->_cmd;
	}

	function escape_string($value){
		return _connect_::escape_string($value);
	}
	
	function insert($table, $data){
		if(!ReportError){
			$data = $this->table_schema($table, $data);
		}
		$this->_cmd = "INSERT INTO `".PREFIX."$table`";
		$feild = $value = array();
		if(is_array($data))
		foreach($data as $f=>$v){
			if(is_object($v) && isset($v->scalar)){
				$feild[] = "`$f`";
				$value[] = $v->scalar;	
			}
			elseif(!is_null($v) && !is_array($v) && !is_object($v) && trim($v)!=''){
				$feild[] = "`$f`";
				$value[] = is_numeric($v)? $v : ("N'"._connect_::escape_string($v)."'");
			}
		}
		$this->_cmd.= " (".implode(', ', $feild).") VALUES (".implode(", ", $value).")";
		$return = _connect_::query($this->_cmd);
		$this->lastid = _connect_::$insert_id;
		if(!$return && ReportError){
			printf(_connect_::$error);
		}
		return $return;
	}
	
	function update($table, $data, $where=''){
		if(!ReportError){
			$data = $this->table_schema($table, $data);
		}
		$this->_cmd = "UPDATE `".PREFIX."$table` SET ";
		$set = array();
		if(is_array($data))
		foreach($data as $f=>$v){
			if(is_object($v) && isset($v->scalar))
				$set[] = "`$f`=".$v->scalar;
			elseif(!is_null($v) && !is_array($v) && !is_object($v))
				$set[] = "`$f`=".(is_numeric($v) ? $v :("N'"._connect_::escape_string($v)."'"));
		}

		$where = preg_replace('/`\{([a-z][a-z0-9_]*)\}`/i', "`".PREFIX."$1`",trim($where));
		$this->_cmd.= implode(', ', $set)." WHERE $where";
		/* */
		$rs = _connect_::query($this->_cmd);
		if(!$rs && ReportError){
			printf(_connect_::$error);
		}
		$this->affected_rows = _connect_::$affected_rows;
		return $rs;
	}
	
	function delete($table, $where=''){
		$where = preg_replace('/`\{([a-z][a-z0-9_]*)\}`/i',"`".PREFIX."$1`",trim($where));
		$this->_cmd = "DELETE FROM `".PREFIX."$table` WHERE $where";
		$rs = _connect_::query($this->_cmd);
		$this->affected_rows = _connect_::$affected_rows;
		return $rs;
	}
	
	function select($feild, $table)
	{
		return new SqlSelectAdapter($feild, $table);
	}
}	
?>
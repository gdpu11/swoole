<?php
namespace DB;
use Utils\Common;
use Utils\ArrayUtil;

class PdoOperateBase
{
    private static $_instance = null;

    private static $DB_FIELDS = null;

    private static function connect($db)
    {
        try { 
            $pdo = new \PDO($db['dbType'].':host='.$db['dbHost'].';port='.$db['dbPort'].';dbname='.$db['dbName'],$db['dbUser'],$db['dbPassword'],
            array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8;")
            ); 
        } catch (\PDOException $e) {  
            throw new \Exception($e->getMessage()); 
            return false; 
        }

        if(!$pdo) { 
            throw new \Exception('\PDO CONNECT ERROR'); 
            return false; 
        } 
 
        return $pdo;
    }

    /**
     * 得到操作数据库对象
     * @param string $params 对应的数据库配置
     * return false说明给定的数据库不存在
     */
    public static function getInstance($dbName = null)
    {
        if (!isset(self::$_instance) || !is_object(self::$_instance)|| $dbName){
            $db = empty($dbName)?$GLOBALS['CONFIG']['db']:$GLOBALS['CONFIG'][$dbName];
            self::$_instance = self::connect($db);
        }
        return self::$_instance;
    }

    public static function getTbFields(){

        $tbName = get_called_class();

        if (isset(self::$DB_FIELDS[$tbName])) {
            return isset(self::$DB_FIELDS[$tbName]);
        }else{
            if (isset($tbName::$SCHEMA)) {
                $SCHEMA = $tbName::$SCHEMA;
                if (isset($tbName::$SCHEMA['PARTITION'])) {
                    $tbName = $tbName::$SCHEMA['TABLENAME'].$tbName::$SCHEMA['PARTITION']['suffix'].$tbName::$SCHEMA['PARTITION']['default'];
                }else{
                    $tbName = $tbName::$SCHEMA['TABLENAME'];
                }
            }else{
                $tbName = substr($tbName,strripos($tbName,'\\')+1);
            }
            if (isset($SCHEMA['DBNAME'])) {
                $pdo = self::getInstance($SCHEMA['DBNAME']);
            }else{
                $pdo = self::getInstance();
            }
            $sql = 'DESC '.$tbName;
            $ps = $pdo->prepare($sql);
            $ps->execute();
            $result = $ps->fetchAll(\PDO::FETCH_ASSOC);
            $data = array();
            foreach ($result as $key => $value) {
                $data[$value['Field']] = substr($value['Type'],0,strpos($value['Type'],'('));
            }
            self::$DB_FIELDS[get_called_class()] = $data;
            return self::$DB_FIELDS[get_called_class()];
        }
    }

    /**
     * 添加记录
     * @author liqiang<qianglee@kugou.net>
     * @date   2017-04-17
     * @return [type]          [description]
     */
    public static function add($data) {
        self::getTbFields();
        $tbName = self::_getTableName($data);
        $addFields = self::_getAddFields($data);
        $sql = "INSERT IGNORE INTO `{$tbName}` {$addFields}";
        $pdo = self::getInstance();
        $ps = $pdo->prepare($sql);
        return $ps->execute();
    }
    
    /**
     * [update 根据条件更新记录]
     * @author qianglee@kugou.net
     * @date   2017-04-17     
     * @param  [type] $where  [查询条件]
     * @param  array  $data   [更新的字段]
     * @param  [type] $SCHEMA [表结构]
     * @return [type]         [description]
     */
    public static function update($where,$data=array()) {
        if (empty($where)) {
            return false;
        }    
        self::getTbFields();
        $tbName = self::_getTableName($where);
        $update = self::_getUpdateFields($data);
        $whe = self::_getWhereFields($where);
        $sql = "UPDATE {$tbName} set {$update} {$whe}";
        $pdo = self::getInstance();
        $ps = $pdo->prepare($sql);
        foreach ($data as $key => $value) {
            if ((!empty($value)||(is_numeric($value)&&$value==0))) {
                if ($SCHEMA['FIELDS'][$key]['type'] == 'varchar') {
                    $ps->bindValue($key, $value, \PDO::PARAM_STR);
                }else{
                    $ps->bindValue($key, $value);    
                }
            }   
        }
        foreach ($where as $key => $value) {    
            if ($value['type'] == 'varchar') {
                $ps->bindValue($key, $value['value'], \PDO::PARAM_STR);
            }else{
                $ps->bindValue($key, $value['value']);    
            }
        }
        return $ps->execute();
    }

    /**
     * [getOne 根据条件获取一行记录]
     * @author qianglee@kugou.net
     * @date   2017-04-17     
     * @param  [type] $where  [查询条件]
     * @param  array  $fields [需要的字段]
     * @param  [type] $SCHEMA [表结构]
     * @return [type]         [一行记录]
     */
    public static function getOne($where,$fields=array(),$ifCache = 0) {
        if (empty($where)) {
            return false;
        }
        self::getTbFields();
        $tbName = self::_getTableName($where);
        $fields = self::_getFieldsStr($fields);
        $whe = self::_getWhereFields($where);

        $CacheKey = $tbName.'ONE:'.md5($tbName.$fields.$whe.implode('', array_keys($where)).implode('', ArrayUtil::getArrayColumn($where,'value')));
        if ($ifCache) {
            $one = Common::getRedisByKey($CacheKey);
            if ($one) {
                return $one;
            }
        }

        $sql = "SELECT {$fields} FROM {$tbName} {$whe}";
        
        $pdo = self::getInstance();
        $ps = $pdo->prepare($sql);
        if (isset($_GET['showSql'])&&$_GET['showSql']=='sql') {
            echo $sql;
        }
        foreach ($where as $key => $value) {    
            if ($value['type'] == 'varchar') {
                $ps->bindValue($key, $value['value'], \PDO::PARAM_STR);
            }else{
                $ps->bindValue($key, $value['value']);    
            }
        }
        $ps->execute();
        $one = $ps->fetch(\PDO::FETCH_ASSOC);
        if ($ifCache&&$one) {
            Common::setRedisByKey($CacheKey,$one);
        }
        return $one;
    }

    /**
     * 获取记录列表
     * @author qianglee@kugou.net
     * @date   2017-04-17     
     * @param  [array]     $where [条件]
     * @param  [int]     $page [分页]
     * @param  [type] $SCHEMA [表结构]
     * @return [type]          [description]
     */
    public static function getAll($where = array(),$page=1,$pageszie=20,$fields = array(),$ifCache = 0) {
        self::getTbFields();
        $tbName = self::_getTableName($where);
        $fields = self::_getFieldsStr($fields);
        $whe = self::_getWhereFields($where);
        $page = intval($page-1)*$pageszie;

        $CacheKey = $tbName.'_ALL:'.md5($tbName.'_'.$fields.'_'.$whe.'_'.$page.'_'.$pageszie.'_'.implode('', array_keys($where)).'_'.implode('', ArrayUtil::getArrayColumn($where,'value')));
        if ($ifCache) {
            $all = Common::getRedisByKey($CacheKey);
            if ($all) {
                return $all;
            }
        }
        $sql = "SELECT {$fields} FROM {$tbName} {$whe} limit {$page},{$pageszie}";
        $pdo = self::getInstance();
        $ps = $pdo->prepare($sql);
        if (isset($_GET['showSql'])&&$_GET['showSql']=='sql') {
            echo $sql;
        }
        foreach ($where as $key => $value) {    
            if ($value['type'] == 'varchar') {
                $ps->bindValue($key, $value['value'], \PDO::PARAM_STR);
            }else{
                $ps->bindValue($key, $value['value']);    
            }
        }
        $ps->execute();
        $all = $ps->fetchAll(\PDO::FETCH_ASSOC);
        if ($ifCache&&$all) {
            Common::setRedisByKey($CacheKey,$all);
        }
        return $all;
    }
    /**
     * 获取记录总数
     * @author liqiang<qianglee@kugou.net>
     * @date   2017-04-17     
     * @param  [type] $SCHEMA [表结构]
     * @param  [array]     $where [条件]
     * @return [type]          [description]
     */
    public static function getCount($where = array()) {
        self::getTbFields();
        $tbName = self::_getTableName($where);
        $whe = self::_getWhereFields($where);
        $sql = "SELECT count(*) as sum FROM {$tbName} {$whe}";
        $pdo = self::getInstance();
        $ps = $pdo->prepare($sql);
        foreach ($where as $key => $value) {    
            if ($value['type'] == 'varchar') {
                $ps->bindValue($key, $value['value'], \PDO::PARAM_STR);
            }else{
                $ps->bindValue($key, $value['value']);    
            }
        }
        $ps->execute();
        $count = $ps->fetchColumn();
        return $count;
    }

    /**
     * 获取某字段总数
     * @author liqiang<qianglee@kugou.net>
     * @date   2017-04-17     
     * @param  [type] $SCHEMA [表结构]
     * @param  [array]     $where [条件]
     * @return [type]          [description]
     */
    public static function getSums($where,$fields) {
        self::getTbFields();
        $tbName = self::_getTableName($where);
        $whe = self::_getWhereFields($where);
        $sql = "SELECT sum({$fields}) as sum FROM {$tbName} {$whe}";
        $pdo = self::getInstance();
        $ps = $pdo->prepare($sql);
        foreach ($where as $key => $value) {    
            if ($value['type'] == 'varchar') {
                $ps->bindValue($key, $value['value'], \PDO::PARAM_STR);
            }else{
                $ps->bindValue($key, $value['value']);    
            }
        }
        $ps->execute();
        $count = $ps->fetchColumn();
        return $count;
    }

    /**
     * 删除记录
     * @author liqiang<qianglee@kugou.net>
     * @date   2017-04-17     
     * @param  [type] $SCHEMA [表结构]
     * @param  [array]     $where [条件]
     * @return [type]          [description]
     */
    public static function delete($where) {
        if (empty($where)) {
            return false;
        }
        self::getTbFields();
        $tbName = self::_getTableName($where);
        $whe = self::_getWhereFields($where);
        $sql = "DELETE FROM {$tbName} {$whe}";
        $pdo = self::getInstance();
        $ps = $pdo->prepare($sql);
        if (empty($where)) {
            return false;
        }
        foreach ($where as $key => $value) {    
            if ($value['type'] == 'varchar') {
                $ps->bindValue($key, $value['value'], \PDO::PARAM_STR);
            }else{
                $ps->bindValue($key, $value['value']);    
            }
        }
        return $ps->execute();
    }

    /**
     * [getLastId 获取当前自增id]
     * @return [type]        [description]
     */
    public static function getLastId($where) {
        self::getTbFields();
        $tbName = self::_getTableName($where);
        $sql = 'select  LAST_INSERT_ID() as autoid from "'.$tbName.'"';
        // $sql = 'select  AUTO_INCREMENT as autoid from information_schema.tables where table_name="'.$tbName.'"';
        $pdo = self::getInstance();
        $ps = $pdo->prepare($sql);
        $ps->execute();
        $result = $ps->fetch(\PDO::FETCH_ASSOC);
        return $result['autoid']?:1;
    }

    /**
     *获取所有联表
     * @author leeqiang@kugou.net
     * @date   2016-12-14
     * @return [type]             [string]
     */
    protected static function _getAllTable($fields,$where,$SCHEMA) {
        $tableName = '(';
        $whe = ' 1';
        if (!empty($where))$whe .=self::_getWhere($where);
        for ($i=0; $i < 2; $i++) { 
            $tableName.="SELECT $fields FROM  ".$SCHEMA."_$i  where {$whe} UNION ";
        }
        $tableName = rtrim($tableName,',UNION ').')';
        return $tableName;
    }

    /**
     * [_getTableIndex 获取表名，有分表要配置]
     * 配置分表下标
     * @author leeqiang@kugou.net
     * @date   2017-04-17
     * @param  [type] $rule [分表规则]
     * @param  [type] $SCHEMA    [表名]
     * @return [type]               [返回下标]
     */
    protected static function _getTableName(&$data = array()){
        $tbName = get_called_class();
        $SCHEMA = $tbName::$SCHEMA;
        //是否有分表
        if (!isset($SCHEMA['PARTITION'])) {
            return $SCHEMA['TABLENAME'];
        }
        if (isset($_GET['showSql'])&&$_GET['showSql']=='sql') {
            print_r($data);
            print_r($SCHEMA['TABLENAME'].$SCHEMA['PARTITION']['suffix'].$index);
        }

        $value = isset($data[$SCHEMA['PARTITION']['field']])?$data[$SCHEMA['PARTITION']['field']]:$SCHEMA['PARTITION']['default'];
        if (!isset($data[$SCHEMA['PARTITION']['field']])) {
            $data1 = current($data);
            $value = isset($data1[$SCHEMA['PARTITION']['field']])?$data1[$SCHEMA['PARTITION']['field']]:$SCHEMA['PARTITION']['default'];
        }

        $type  = $SCHEMA['PARTITION']['type'];
        
        //直接指定分表下标
        if (isset($data['PARTITION_INDEX'])) {
            $index = $data['PARTITION_INDEX'];
            unset($data['PARTITION_INDEX']);
            return $SCHEMA['TABLENAME'].$SCHEMA['PARTITION']['suffix'].$index;
        }

        switch ($type) {
            case 'id':
                // 按照id范围分表
                $index  = floor($value / $SCHEMA['PARTITION']['num'])+1;
                return $SCHEMA['TABLENAME'].$SCHEMA['PARTITION']['suffix'].$index;
            case 'year':
                // 按照年份分表
                $index = date('Y', $value);
                return $SCHEMA['TABLENAME'].$SCHEMA['PARTITION']['suffix'].$index;
            case 'mod':
                // 按照id的模数分表
                $index = ($value % $SCHEMA['PARTITION']['num'])+1;
                return $SCHEMA['TABLENAME'].$SCHEMA['PARTITION']['suffix'].$index;
            case 'md5':
                // 按照md5的序列分表
                $index = (hexdec(substr(md5($value ), 0, 2)) % $SCHEMA['PARTITION']['num'])+1;
                return $SCHEMA['TABLENAME'].$SCHEMA['PARTITION']['suffix'].$index;
            default:
                    return $SCHEMA['TABLENAME'];
        }
    }

    /**
     * [_getWhereFields 拼接条件查询]
      *leeqiang@kugou.net
     * @date   2017-04-17
     * @param  [type] $where [查询条件引用，并返回pdo对应字段key跟value]
     * @return [type]         [返回字符串]
     */
    protected static function _getWhereFields(&$where){
        $tbName = get_called_class();
        $whe = '';
        $bindValue = array();
        $GroupBy = '';
        $OrderBy = '';
        if (!empty($where)&&is_array($where)) {
            $i = 0;
            foreach ($where as $key => $value) {
                if (isset(self::$DB_FIELDS[$tbName][$key])&&(!empty($value)||(is_numeric($value)&&$value==0))) {
                    if (is_array($value)) {
                        foreach ($value as $key1 => $value1) {
                            if (count($value)>1) {
                                if (self::$DB_FIELDS[$tbName][$key]=='varchar') {
                                    $whe .= " AND {$key} IN ('".implode("','", $value)."')";//$where['status'] = array(0,1,2,3,4);IN 查询 字符串
                                }else{
                                    $whe .= " AND {$key} IN (".implode(',', $value).")";//$where['status'] = array(0,1,2,3,4);IN 查询 整形
                                }
                                break;
                            }else{
                                if (is_numeric($key1)) {
                                    $whe .= " AND {$key} = :{$key}".$i;//$where['status'] = array(0);
                                }else{
                                    if (stripos('xlikex',$key1)) {
                                        $whe .= " AND {$key} like :{$key}".$i;//$where['status'] = array('like'=>0);
                                    }else{
                                        $whe .= " AND {$key} {$key1} :{$key}".$i;//$where['status'] = array('!='=>0);
                                    }
                                }  
                            }
                            if (strtolower($key1)=='xlikex') {
                                $value1 = '%'.$value1.'%';//$where['status'] = array('%like%'=>0);
                            }
                            elseif (strtolower($key1)=='likex') {
                                $value1 = $value1.'%';//$where['status'] = array('like%'=>0);
                            }
                            elseif (strtolower($key1)=='xlike') {
                                $value1 = '%'.$value1;//$where['status'] = array('%like'=>0);
                            }
                            elseif (strtolower($key1)=='like') {
                                $value1 = '%'.$value1.'%';//$where['status'] = array('%like'=>0);
                            }
                            $bindValue[':'.$key.$i]['type'] = self::$DB_FIELDS[$tbName][$key];
                            $bindValue[':'.$key.$i++]['value'] = $value1;
                        }
                    }else{
                        $whe .= " AND {$key} = :{$key}";//$where['status'] = 0;
                        $bindValue[':'.$key]['type'] = self::$DB_FIELDS[$tbName][$key];
                        $bindValue[':'.$key]['value'] = $value;
                    }
                }elseif (strtolower($key)=='group') {
                    if (is_array($value)) {
                        $GroupBy = ' GROUP BY '.implode(',', $value);//$where['group'] = array('status','type');
                    }else{
                        $GroupBy = ' GROUP BY '.$value;//$where['group'] = 'status';
                    }
                }elseif (strtolower($key)=='order') {
                    if (is_array($value)) {
                        $OrderBy = ' ORDER BY '.implode(',', $value);//$where['order'] = array('status desc',' id asc');
                    }else{
                        $OrderBy = ' ORDER BY '.$value;//$where['order'] = 'status desc';
                    }
                }
            }
        }
        $where = $bindValue;
        //查询条件为空时
        if (empty($whe)) {
            return $GroupBy.$OrderBy;
        }
        return 'WHERE '.preg_replace('/AND/', '', $whe, 1).$GroupBy.$OrderBy;
    }
    /**
     * [_getAddFields 拼接添加字段]
     * @author leeqiang@kugou.net
     * @date   2017-04-17
     * @param  [type] $fields [更新字段]
     * @return [type]         [返回字符串]
     */
    protected static function _getAddFields($data){
        $tbName = get_called_class();
        $add_key = '';
        $add_value = ' value';
        //判断是否为批量插入
        if (count($data) == count($data, 1)) {
            $add_key = self::_getAddKey($data);
            $add_value .= self::_getAddValue($data);
        }else{
            $add_key = self::_getAddKey(current($data));
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $add_value .= self::_getAddValue($value).',';
                }
            }
            $add_value =  rtrim($add_value,',');
        }
        return $add_key.$add_value;
    }

    /**
     * [_getAddKey 拼接添加字段]
     * @author leeqiang@kugou.net
     * @date   2017-04-17
     * @param  [type] $data [添加字段]
     * @return [type]         [返回字符串]
     */
    protected static function _getAddKey($data){
        $tbName = get_called_class();
        $fieldKey = array_keys(self::$DB_FIELDS[$tbName]);
        $dataKey = array_keys($data);
        $saveKey = array_intersect($dataKey,$fieldKey);
        $str = '('.implode(',', $saveKey).')';
        return $str;
    }
    /**
     * [_getAddValue 拼接添加字段值]
     * @author leeqiang@kugou.net
     * @date   2017-04-17
     * @param  [type] $data   [字段]
     * @param  array  $SCHEMA [表结构]
     * @param  string $Prefix [前缀]
     * @return [type]         [description]
     */
    protected static function _getAddValue($data){
        $tbName = get_called_class();
        $fieldKey = array_keys(self::$DB_FIELDS[$tbName]);
        $dataKey = array_keys($data);
        $saveKey = array_intersect($dataKey,$fieldKey);
        $saveValue = array();
        foreach ($saveKey as $key => $value) {
            $saveValue[] = "'".$data[$value]."'";
        }
        $str = '('.implode(',', $saveValue).')';
        return $str;
    }

    /**
     * [_getUpdateFields 拼接更新字段]
     * @author leeqiang@kugou.net
     * @date   2017-04-17
     * @param  [type] $fields [更新字段]
     * @return [type]         [返回字符串]
     */
    protected static function _getUpdateFields($data){
        $tbName = get_called_class();
        $str = '';
        foreach ($data as $key => $value) {
            if (isset(self::$DB_FIELDS[$tbName][$key])&&(!empty($value)||(is_numeric($value)&&$value==0))) {
                $str.="{$key} = :{$key},";
            }
        }
        return rtrim($str,',');
    }

    /**
     * [_getFieldsStr 获取sql查询的数据库字段]
     * @author qianglee@kugou.net
     * @date   2017-04-17
     * @param  array  $needFields [需要的字段]
     * @param  array  $SCHEMA     [表结构]
     * @return [string]           [字段名称]
     */
    protected static function _getFieldsStr($needFields = array()) {
        $tbName = get_called_class();
        $fieldStr = '';
        if (!empty($needFields)) {
            foreach ($needFields as $key => $value) {
                if (isset(self::$DB_FIELDS[$tbName][$key])) {
                    $fieldStr .= " {$key} as {$value},";
                }
            }
        } else {
            foreach (self::$DB_FIELDS[$tbName] as $key => $value) {
                $fieldStr .= " {$key} as {$key},";
            }
        }
        return trim($fieldStr, ",").' ';
    }
}
?>
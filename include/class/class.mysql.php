<?php
//  ==================================================================================================
//  MYSQL CONNECT CLASS
//  ==================================================================================================

abstract class MySQL
{
    private static $con;

    const fmIndex = MYSQL_NUM;
    const fmAssoc = MYSQL_ASSOC;
    const fmBoth = MYSQL_BOTH;

    public static function connect($host, $user, $pass, $db)
    {
        self::$con = mysql_pconnect($host, $user, $pass, true) or die ("Unable to connect to DB");
        if ($db)    mysql_select_db($db, self::$con) or die("Unable to connect to DB and select database");
        mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", self::$con) or die ("Unable to connect to DB");
        mysql_query("SET NAMES 'utf8'", self::$con) or die ("Unable to connect to DB - setnames utf8");
    }

    public static function changeDB($db)
    {
        mysql_select_db($db, self::$con) or die("Unable to change DB");
    }

    /**
     * @return string
     */
    public static function getSelectedDB()
    {
        $r = mysql_query($s='SELECT DATABASE()',self::$con);
        self::tryLogMySQLError($s);
        return mysql_result($r,0);
    }

    public static function disconnect()
    {
        @mysql_close(self::$con);
        self::$con = NULL;
    }

    /**
     * @param $instanceName
     * @return bool
     */
    public static function isExistsInstance($instanceName)
    {
        $res = self::getInstanceList();
        return (in_array($instanceName, $res));
    }

    /**
     * Returns the last AutoIncrement value that has been inserted with this database link.
     *
     * @return int The last AutoIncrement value that has been inserted.
     */
    public static function getLastId()
    {
        return mysql_insert_id(self::$con);
    }

    /**
     * @return array
     */
    public static function getInstanceList()
    {
        $retVal = array();

        $rows = self::resultArray(self::executeQuery('show databases'), MySQL::fmAssoc);
        foreach ($rows as $row) {
            if (substr($row['Database'], 0, strlen(DB_PREFIX)) == DB_PREFIX)
                $retVal[] = substr($row['Database'], strlen(DB_PREFIX));
        }
        return $retVal;
    }

    /**
     * @param $sql
     * @return resource
     * @throws MySQLException
     */
    public static function executeQuery($sql)
    {
        if (CONFIG__SQL_EXCEPTION === true)
        {
            if (!($result = mysql_query($sql, self::$con)))     throw new MySQLException($sql);
            return $result;

        }
        else
        {
            if (!($result = mysql_query($sql, self::$con)))     self::tryLogMySQLError($sql);
            return $result;
        }
    }

    /**
     * @param resource $set
     * @param int $mode
     * @return array
     */
    public static function fetchRecord($set, $mode = self::fmIndex)
    {
        return @mysql_fetch_array($set, $mode);
    }

    /**
     * @param $result
     * @param int $mode
     * @return array
     */
    public static function resultArray($result, $mode = self::fmIndex)
    {
        $res = array();
        if (is_resource($result)) {
            while (is_array($res[] = self::fetchRecord($result, $mode))) ;
            unset($res[count($res) - 1]);
        }
        return $res;
    }

    /**
     * @param $result
     * @param int $mode
     * @param $primaryField
     * @param array $fields
     * @return array
     */
    public static function resultArrayId($result, $mode = self::fmIndex,$primaryField,array $fields=null)
    {
        $res = array();
        foreach (self::resultArray($result,$mode) as $row)
        {
            if (is_array($fields))
            {
                $_row=array();
                foreach ($fields as &$field)
                {
                    $_row[$field] = $row[$field];
                }
                $res[$row[$primaryField]] = $_row;
            }
            else
                $res[$row[$primaryField]] = $row;
        }
        return $res;
    }

    /**
     * @param $sql
     * @return int|null (hiba eseten nullal ter vissza!!!)
     */
    public static function runCommand($sql)
    {
        $ret = null;
        if (self::executeQuery($sql))   $ret = self::rowsAffected();
        return $ret;
    }

    /**
     * @return int
     */
    public static function rowsAffected()
    {
        return mysql_affected_rows(self::$con);
    }

    /**
     * @param string $s
     * @param bool $rows
     * @param bool $organize
     * @return array|bool
     */
    public static function query($s = '', $rows = false, $organize = true)
    {
        if (!$q = mysql_query($s, self::$con))
        {
            self::tryLogMySQLError($s);
            return false;
        } //return mysql_error(); //return false;
        if ($rows !== false) $rows = intval($rows);
        $rez = array();
        $count = 0;
        $type = $organize ? MYSQL_NUM : MYSQL_ASSOC;
        while (($rows === false || $count < $rows) && $line = mysql_fetch_array($q, $type)) {
            if ($organize) {
                foreach ($line as $field_id => $value) {
                    $table = mysql_field_table($q, $field_id);
                    if ($table === '') $table = 0;
                    $field = mysql_field_name($q, $field_id);
                    $rez[$count][$table][$field] = $value;
                }
            } else {
                $rez[$count] = $line;
            }
            ++$count;
        }
        if (!mysql_free_result($q)) return false;
        return $rez;
    }

    /**
     * @param $data
     * @return string
     */
    public static function filter($data)
    {
        $data = mysql_real_escape_string($data, self::$con);
        return $data;
    }

    /**
     * @param $data
     * @param bool $allowNull
     * @return string
     */
    public static function filterQ($data,$allowNull=false)
    {
        if ($data === null and $allowNull === true)
        {
            $data = 'NULL';
        }
        else
        {
            $data = '"'.mysql_real_escape_string($data, self::$con).'"';
        }

        return $data;
    }

    /**
     * @param $data
     * @return string
     */
    public static function toHtml($data)
    {
        $data = mysql_real_escape_string(stripslashes(trim($data, self::$con)));
        return $data;
    }

    /**
     * @param $s string
     */
    public static function tryLogMySQLError($s)
    {
        if (DEVMODE !== true or mysql_errno()==0) return;

        $logFileName=$_SERVER['DOCUMENT_ROOT'].'/../log/phplog/log_'.date('Ymd').'.txt';

        file_put_contents($logFileName,"---------".date('Y-m-d H:i:s')."\r\n".trim(str_replace("\t","",$s))."\r\n".mysql_error(self::$con)."\r\n",FILE_APPEND);
    }

    /**
     * @param string $s
     * @return bool
     */
    public static function execute($s = '')
    {
        if (mysql_query($s, self::$con)) return true;
        self::tryLogMySQLError($s);
        return false;
    }

    /**
     * @param string $s
     * @return bool|string
     */
    private static function executeQueryAndReturnMySqlErrorString($s = '')
    {
        if (mysql_query($s, self::$con)) return true;
        self::tryLogMySQLError($s);
        return mysql_error(self::$con); //false;
    }

    /**
     * @param $options
     * @return array|bool
     */
    public static function select($options)
    {
        $default = array(
            'table' => '',
            'fields' => '*',
            'condition' => '1',
            'conditionExtra' => '',
            'order' => '1',
            'limit' => 50
        );
        $options = array_merge($default, $options);

        if (is_array($options))
            $cond = self::createCondition($options['condition']) . ($options['conditionExtra'] == '' ? '' : ' AND ' . $options['conditionExtra']);
        else
            $cond = $options['condition'];

        $sql = "SELECT {$options['fields']} FROM {$options['table']} WHERE {$cond} ORDER BY {$options['order']} LIMIT {$options['limit']}";
//return print_r($sql);

        return self::query($sql, false, false);
    }

    /**
     * @param $options
     * @return bool
     */
    public static function row($options)
    {
        $default = array(
            'table' => '',
            'fields' => '*',
            'condition' => '1',
            'order' => '1'
        );
        $options = array_merge($default, $options);
        $sql = "SELECT {$options['fields']} FROM {$options['table']} WHERE {$options['condition']} ORDER BY {$options['order']}";
        $result = self::query($sql, true, false);
        if (empty($result[0])) return false;
        return $result[0];
    }

    /**
     * @param null $table
     * @param null $field
     * @param string $conditions
     * @return bool
     */
    public static function get($table = null, $field = null, $conditions = '1')
    {
        if ($table === null || $field === null) return false;
        $result = self::row(array(
            'table' => $table,
            'condition' => $conditions,
            'fields' => $field
        ));
        if (empty($result[$field])) return false;
        return $result[$field];
    }

    /**
     * @param null $table
     * @param array $array_of_values
     * @param $conditions
     * @return bool|string
     */
    public static function update($table = null, $array_of_values = array(), $conditions)
    {
        if ($table === null || empty($array_of_values)) return false;
        $what_to_set = array();
        foreach ($array_of_values as $field => $value) {
            if (is_array($value) && !empty($value[0])) $what_to_set[] = "`$field`='{$value[0]}'";
            else $what_to_set [] = "`$field`='" . self::filter($value) . "'";
        }
        $what_to_set_string = implode(',', $what_to_set);
        //return "UPDATE $table SET $what_to_set_string WHERE $conditions";
        //check if $condition is an array
        if (is_array($conditions) && !empty($conditions)) {
            $conditionsString = self::createCondition($conditions);
        } else {
            $conditionsString = 'FALSE';
        }
        return self::executeQueryAndReturnMySqlErrorString("UPDATE $table SET $what_to_set_string WHERE $conditionsString");
    }

    /**
     * @param null $table
     * @param array $array_of_values
     * @return bool|int|string
     */
    public static function insert($table = null, $array_of_values = array())
    {
        if ($table === null || empty($array_of_values) || !is_array($array_of_values)) return false;
        $fields = array();
        $values = array();
        foreach ($array_of_values as $id => $value) {
            $fields[] = $id;
            if (is_array($value) && !empty($value[0])) $values[] = $value[0];
            else $values[] = "'" . self::filter($value) . "'";
        }
        $s = "INSERT INTO $table (" . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
        if (mysql_query($s, self::$con)) return mysql_insert_id(self::$con);
        self::tryLogMySQLError($s);
        return mysql_error();
    }

    /**
     * @param null $table
     * @param array $array_of_values
     * @return bool|int|string
     */
    public static function insertIgnore($table = null, $array_of_values = array())
    {
        if ($table === null || empty($array_of_values) || !is_array($array_of_values)) return false;
        $fields = array();
        $values = array();
        foreach ($array_of_values as $id => $value) {
            $fields[] = $id;
            if (is_array($value) && !empty($value[0])) $values[] = $value[0];
            else $values[] = "'" . mysql_real_escape_string($value, self::$con) . "'";
        }
        $s = "INSERT IGNORE INTO $table (" . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
//return $s;
        if (mysql_query($s, self::$con)) return mysql_insert_id(self::$con);
        self::tryLogMySQLError($s);
        return mysql_error();
    }

    public static function insert2($query)
    {
        if ($query === null) return false;
        if (mysql_query($query, self::$con)) return mysql_insert_id(self::$con);
        self::tryLogMySQLError($query);
        return mysql_error(self::$con); //false;
    }

    public static function delete($table = null, $conditions = 'FALSE')
    {
        if ($table === null) return false;

        if (is_array($conditions) && !empty($conditions)) {
            $conditionsString = self::createCondition($conditions);
        } else {
            $conditionsString = 'FALSE';
        }
        return self::execute("DELETE FROM $table WHERE $conditionsString");
    }

    /**
     * @return int
     */
    public static function affected_rows()
    {
        return mysql_affected_rows(self::$con);
    }

    /**
     * @param null $table_name
     * @return array|bool
     */
    public static function mysqlFetch($table_name = null)
    {
        $arrFields = array();
        if ($table_name === null) return false;
        $sql = "DESCRIBE " . $table_name; //"SHOW COLUMNS FROM ".$_GET['table_name'];
        $fields = mysql_query($sql, self::$con);
        self::tryLogMySQLError($sql);
        while ($arrRow = mysql_fetch_object($fields)) {
            //Array ( [Field] => id [Type] => int(10) [Null] => NO [Key] => PRI [Default] => [Extra] => auto_increment )
            $arrField = $arrRow->Field;
            $arrType = $arrRow->Type;
            $arrNull = $arrRow->Null;
            $arrKey = $arrRow->Key;
            $arrDefault = $arrRow->Default;
            $arrExtra = $arrRow->Extra;
            $arrMaxSize = 0;
            if (strpos($arrRow->Type, '(') != '') {
                $pos = strpos($arrRow->Type, '(') + 1;
                # get the position of the number before the ')'
                $pos2 = strpos($arrRow->Type, ')');
                #get the number if digits for the length
                $length = $pos2 - $pos;
                # set maxlength to the number inside the '()'
                $iMaxsize = substr($arrRow->Type, $pos, $length);
                # if the field has a max length then place the field $key and $value into the array;
                $arrMaxSize = $iMaxsize;
                $arrType = substr($arrRow->Type, 0, $pos - 1);
            }
            $arrFields[] = array("fieldname" => $arrField, "fieldtype" => $arrType, "fieldsize" => $arrMaxSize, "default" => $arrDefault, "extra" => $arrExtra);
        }
        mysql_free_result($fields);
        return $arrFields;
    }

    //  ==================================================================================================
    //  create condition string from array
    //  ==================================================================================================
    /**
     * @param array $pair
     * @return string
     */
    public static function createCondition(array $pair)
    {
        //just init cond array:
        $condition = array();

        foreach ($pair as $key => $value) {
            //oh yeah, you can also automatically prevent SQL injection
            $value = mysql_real_escape_string($value, self::$con);

            $condition[] = "{$key} = '{$value}'";
        }

        //Prepare for WHERE clause:
        $condition = join(' AND ', $condition);
        //Return prepared string:
        return $condition;
    }

    /**
     * Error code of the most recent operation.
     *
     * @return int Error code of the most recent operation.
     */
    public static function errorCode()
    {
        if(self::$con===null) return mysql_errno();
        else return mysql_errno(self::$con);
    }

    /**
     * Error message of the most recent operation.
     *
     * @return string Error message of the most recent operation.
     */
    public static function errorMessage()
    {
        if(self::$con===null) return mysql_error();
        else return mysql_error(self::$con);
    }
}

class DBTransaction
{
    private static $transactionNestinglevel=0;

    private $transactionLevel;

    public function __construct()
    {
        if (($this->transactionLevel=++self::$transactionNestinglevel)===1)
        {
            MySQL::runCommand('BEGIN');
        }

    }

    public function destroy()
    {
        if ($this->transactionLevel === null)
            throw new Exception('DBTransaction::destroy called more than once!');
        if (self::$transactionNestinglevel !== $this->transactionLevel)
        {
            MySQL::runCommand('ROLLBACK');
            throw new Exception('DBTransaction nesting violation occured!');
        }
        $this->transactionLevel = NULL;
        if (--self::$transactionNestinglevel === 0)
            MySQL::runCommand('COMMIT');
    }
}

class MySQLException extends Exception
{
    public function __construct($sql)
    {
        MySQL::tryLogMySQLError($msg='Cannot execute query.'.MySQL::errorMessage().':'.MySQL::errorCode());
        parent::__construct($msg);
    }
}

//connect to db
MySQL::connect(DB_HOST, DB_USER, DB_PASS, ((isset($_SESSION['database'])) and $_SESSION['database']) ? $_SESSION['database'] : null);
?>
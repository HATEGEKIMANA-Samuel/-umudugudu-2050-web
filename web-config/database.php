<?php
class mysqldatabase
{
    public $connection;
    public $db;
    private $last_query;
    private $magic_quotes_active;
    private $new_enough_php;

    function __construct($d)
    {
        // Turn on query error reporting to throw exeptions instead of just returning false
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        //
        $this->db = $d;
        $this->open_connection();
        $this->magic_quotes_active = get_magic_quotes_gpc();
        $this->new_enough_php = function_exists("mysql_real_escape_string");
    }

    /**
     * Begin a database transaction to a current connection
     */
    public function beginTransaction()
    {
        mysqli_begin_transaction($this->connection);
    }


    /**
     * Commit a transaction
     */
    public function commit()
    {
        mysqli_commit($this->connection);
    }

    /**
     * Roll back a transaction
     */
    public function rollBack()
    {
        mysqli_rollback($this->connection);
    }

    public function open_connection()
    {
        $this->connection = mysqli_connect(DB_SERVE, DB_USER, DB_PASS, $this->db);
        if (!$this->connection)
            die("User do not match!" . mysqli_connect_error());
        //selecting database
        $select = mysqli_select_db($this->connection, $this->db);
        if (!$select)
            die("Database do not match!" . mysqli_connect_error());
    }

    public function close_connection()
    {
        if (isset($this->connection)) {
            mysqli_close($this->connection);
            unset($this->connection);
        }
    }


    public function query($sql, $action = '', $status = '')
    {
        $this->last_query = $sql;
        $result = mysqli_query($this->connection, $sql);
        $conf = $this->comfirm($result);
        if ($action == 'Login') {
            if ($conf) {
                $status = 'SUCCESS';
            } else {
                $status = 'FAILED';
            }
        }
        // $this->LogMe($sql,$action,$status);
        return $result;
    }

    //this function is used for a simple select query that returns atmost one row in object
    public function get($columns, $table, $condition)
    {
        $this->last_query = "SELECT $columns FROM $table WHERE $condition LIMIT 1";
        $result = mysqli_query($this->connection, $this->last_query);
        return (object) mysqli_fetch_array($result);
    }

    // this function is used to count result
    public function resultCount($query)
    {
        $this->last_query = "SELECT COUNT(1) as total FROM ($query) AS total";
        $result = mysqli_query($this->connection, $this->last_query);
        return (object) mysqli_fetch_array($result);
    }
    //this function is used for a simple select query that returns atmost one row in object
    public function getAll($table)
    {
        $this->last_query = "SELECT * FROM $table";
        $result = mysqli_query($this->connection, $this->last_query);
        return (object) mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    //this function is used for a simple select query that returns atmost one row in array
    public function getArray($columns, $table, $condition)
    {
        $this->last_query = "SELECT $columns FROM $table WHERE $condition LIMIT 1";
        $result = mysqli_query($this->connection, $this->last_query);
        return mysqli_fetch_array($result);
    }

    public function create($table, $fields = array())
    {
        $keys = array_keys($fields);
        $values = '';
        $x = 1;
        $fieldsCount = count($fields);

        $sql = "INSERT INTO {$table} (`" . implode('`,`', $keys) . "`) VALUES 
			 ('" . implode('\',\'', $fields) . "')";
        $this->query($sql);
        if ($this->affected_rows()) {
            return true;
        }
        return false;
    }

    public function implode($array)
    {
        $rtn = "";
        foreach ($array as $key => $value) {
            $rtn .= (is_string($value) ? "\'" . $value . "\'" : $value) . ($key < count($array) - 1 ? "," : null);
        }
        return $rtn;
    }

    public function update($table, $where, $fields = array())
    {
        $sql = "UPDATE $table SET ";
        foreach ($fields as $key => $value) {
            $sql .= "`$key` = '$value',";
        }
        $sql = rtrim($sql, ',');
        $sql .= " WHERE " . $where;

        $this->query($sql);
        if ($this->affected_rows()) {
            return true;
        }
        return false;
    }


    public function incrementColumn($table, $where, $column)
    {
        $sql = "UPDATE $table SET $column = $column +1 WHERE " . $where;
        $this->query($sql);
        if ($this->affected_rows()) {
            return true;
        }
        return false;
    }

    public function fetch_array($query)
    {
        return mysqli_fetch_array($query);
    }

    public function fetch($query)
    {
        return mysqli_fetch_all(mysqli_query($this->connection, $query), MYSQLI_ASSOC);
    }


    public function num_rows($result)
    {
        return mysqli_num_rows($result);
    }


    public function inset_id()
    {
        return mysqli_insert_id($this->connection);
    }

    public function affected_rows()
    {
        return mysqli_affected_rows($this->connection);
    }


    public function escape_value($value)
    {
        if ($this->new_enough_php) {
            if ($this->magic_quotes_active) {
                $value = stripslashes($value);
            }
            $value = htmlspecialchars($value, ENT_QUOTES);
            $value = mysqli_real_escape_string($this->connection, $value);
        } else {
            if (!$this->magic_quotes_active) {
                $value = addslashes($value);
            }
        }
        return $value;
    }

    private function comfirm($result)
    {
        if (!$result) {
            $output = "Query Failed.  " . mysqli_error($this->connection);
            $output .= $this->last_query;
            //die($output);
        }
    }

    public function count_all($table)
    {
        $sql = "SELECT COUNT(*) FROM {$table}";
        $result = $this->query($sql);
        $this->comfirm($result);
        $number = $this->fetch_array($result);
        return array_shift($number);
    }

    public function count_alls($table)
    {
        $sql = "SELECT SUM(1) AS total FROM {$table}";
        $result = $this->query($sql);
        $this->comfirm($result);
        $number = $this->fetch_array($result);
        return $number['total'];
    }

    public function fetch_ass($results)
    {
        return mysqli_fetch_all($results, MYSQLI_ASSOC);
    }

    function get_item($table, $column, $value, $answer)
    {
        $st = $this->query("SELECT * from $table where $column='$value'");
        $row = $this->fetch_array($st);
        return $row[$answer];
    }
    public function getError()
    {
        return mysqli_error($this->connection);
    }

    public function insert($table, $data)
    {
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values  = '';
            $i = 0;
            // if (!array_key_exists('date_created', $data)) {
            //     $data['date_created'] = date("Y-m-d H:i:s");
            // }

            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $columns .= $pre . $key;
                $values  .= $pre . "'" . $val . "'";
                $i++;
            }
            $query = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
            $insert = $this->query($query);
            $this->comfirm($insert);
            return $this->inset_id();
        } else {
            return false;
        }
    }

    function LogMe($value = '', $action = '', $status = '')
    {
        $reff = '';
        $checksum = '';
        $id = '';

        if (empty($action)) {
            $action = explode(' ', $value);
        }
        if (isset($_SESSION['id'])) {
            $id = $_SESSION['id'];
        }
        $qry = "INSERT INTO logs (date, user, action, comments, reff, checksum,status) VALUES (CURRENT_TIMESTAMP, '{$id}', '{$action}', '" . $this->escape_valueLog($value) . "', '{$reff}', '{$checksum}','{$status}')";
        $this->last_query = $qry;
        $result = mysqli_query($this->connection, $qry);
        $this->comfirm($result);
    }

    function escape_valueLog($value)
    {

        if ($this->new_enough_php) {
            if ($this->magic_quotes_active) {
                $value = stripslashes($value);
            }
            // $value = htmlspecialchars($value,ENT_QUOTES);
            $value = mysqli_real_escape_string($this->connection, $value);
        } else {
            if (!$this->magic_quotes_active) {
                $value = addslashes($value);
            }
        }
        return $value;
    }
}
$database  = new mysqldatabase(DB_NAME);

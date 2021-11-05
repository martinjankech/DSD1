<?php
/*
 * DB Class
 * This class is used for database related (connect, insert, update, and delete) operations
 * @author    CodexWorld.com
 * @url        http://www.codexworld.com
 * @license    http://www.codexworld.com/license
 */
class DB{
    private $conn;
    private $conn1;
    private $conn2;
    private $dbHost     = "localhost";
    private $dbUsername = "root";
    private $dbPassword = "";
    private $dbName    = "restaurant";

    
    private $dbHost1     = "25.35.50.147";
    private $dbUsername1 = "velkykomp";
    private $dbPassword1 = "123";
    private $dbName1     = "restaurant";


    private $dbHost2     = "25.42.132.140";
    private $dbUsername2 = "velkykomp";
    private $dbPassword2 = "123";
    private $dbName2    = "restaurant";
    
    
    
    
    public function __construct(){
        
            // Connect to the database
            //$conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
            //$conn1 = new mysqli($this->dbHost1, $this->dbUsername1, $this->dbPassword1, $this->dbName1);
            //$conn2 = new mysqli($this->dbHost2, $this->dbUsername2, $this->dbPassword2, $this->dbName2);

            $this->conn = $this->connectToDBS($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName );  
            $this->conn1 = $this->connectToDBS($this->dbHost1, $this->dbUsername1, $this->dbPassword1, $this->dbName1);
            $this->conn2 = $this->connectToDBS($this->dbHost2, $this->dbUsername2, $this->dbPassword2, $this->dbName2);      
           
            
            
            //if($conn->connect_error){
               // die("Failed to connect with MySQL: " . $conn->connect_error);
            //}else{
               // $this->conn = $conn;
                //$this->conn1 = $conn1;
                //$this->conn2 = $conn2;
            //}
    }
    
    
    public function connectToDBS($servername, $username, $password, $dbname) {
        try {
            $conn = new mysqli($servername,$username,$password,$dbname);
    
            return $conn;
        } catch (mysqli_sql_exception $e) {
            echo "Connection failed: " . $e->getMessage();
            return 0;
        }
    }


  
     /*
     * Returns rows from the database based on the conditions
     * @param string name of the table
     * @param array select, where, order_by, limit and return_type conditions
     */
    public function getRows($table, $conditions = array()){
        $sql = 'SELECT ';
        $sql .= array_key_exists("select",$conditions)?$conditions['select']:'*';
        $sql .= ' FROM '.$table;
        if(array_key_exists("where",$conditions)){
            $sql .= ' WHERE ';
            $i = 0;
            foreach($conditions['where'] as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $sql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }
        
        if(array_key_exists("order_by",$conditions)){
            $sql .= ' ORDER BY '.$conditions['order_by']; 
        }else{
            $sql .= ' ORDER BY id DESC '; 
        }
        
        if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit']; 
        }elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['limit']; 
        }
        
        $result = $this->conn->query($sql);
        
        if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){
            switch($conditions['return_type']){
                case 'count':
                    $data = $result->num_rows;
                    break;
                case 'single':
                    $data = $result->fetch_assoc();
                    break;
                default:
                    $data = '';
            }
        }else{
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $data[] = $row;
                }
            }
        }
        return !empty($data)?$data:false;
    }
    
    /*
     * Insert data into the database
     * @param string name of the table
     * @param array the data for inserting into the table
     */
    public function insert($table, $data){
        if(!empty($data) && is_array($data)){
            $columns = '';
            $values  = '';
            $i = 0;
            if(!array_key_exists('created',$data)){
                $data['created'] = date("Y-m-d H:i:s");
            }
            if(!array_key_exists('modified',$data)){
                $data['modified'] = date("Y-m-d H:i:s");
            }
            foreach($data as $key=>$val){
                $pre = ($i > 0)?', ':'';
                $columns .= $pre.$key;
                $values  .= $pre."'".$this->conn->real_escape_string($val)."'";
               
                $i++;
            }
            $query = "INSERT INTO ".$table." (".$columns.") VALUES (".$values.")";
            $insert = $this->conn->query($query)&&$this->conn1->query($query)&&$this->conn2->query($query);
            return $insert?$this->conn->insert_id:false;
        }else{
            return false;
        }
    }
    
    /*
     * Update data into the database
     * @param string name of the table
     * @param array the data for updating into the table
     * @param array where condition on updating data
     */
    public function update($table, $data, $conditions){
        if(!empty($data) && is_array($data)){
            $colvalSet = '';
            $whereSql = '';
            $i = 0;
            if(!array_key_exists('modified',$data)){
                $data['modified'] = date("Y-m-d H:i:s");
            }
            foreach($data as $key=>$val){
                $pre = ($i > 0)?', ':'';
                $colvalSet .= $pre.$key."='".$this->conn->real_escape_string($val)."'";
                $i++;
            }
            if(!empty($conditions)&& is_array($conditions)){
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach($conditions as $key => $value){
                    $pre = ($i > 0)?' AND ':'';
                    $whereSql .= $pre.$key." = '".$value."'";
                    $i++;
                }
            }
            $query = "UPDATE ".$table." SET ".$colvalSet.$whereSql;
            $update =  $this->conn->query($query)&&$this->conn1->query($query)&&$this->conn2->query($query);
            return $update?$this->conn->affected_rows:false;
        }else{
            return false;
        }
    }
    
    /*
     * Delete data from the database
     * @param string name of the table
     * @param array where condition on deleting data
     */
    public function delete($table, $conditions){
        $whereSql = '';
        if(!empty($conditions)&& is_array($conditions)){
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach($conditions as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $whereSql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }
        $query = "DELETE FROM ".$table.$whereSql;
        $delete =  $this->conn->query($query)&&$this->conn1->query($query)&&$this->conn2->query($query);
        return $delete?true:false;
    }
}


?>
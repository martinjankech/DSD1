<?php
ini_set('display_errors', 0);
 ini_set('display_startup_errors', 0);

error_reporting(0);






/*
 * DB Class
 * This class is used for database related (connect, insert, update, and delete) operations
 * @author    CodexWorld.com
 * @url        http://www.codexworld.com
 * @license    http://www.codexworld.com/license
 */
class DB{
    //velky komp
    private $conn;
    private $conn1;
    private $conn2;
    private $aviableconnection=[];
    private $notaviableconnection=[];
    private $dbHost     = "127.0.0.1";
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
   
    // notebook 1
    // private $conn;
    // private $conn1;
    // private $conn2;
    // private $dbHost     = "localhost";
    // private $dbUsername = "root";
    // private $dbPassword = "";
    // private $dbName    = "restaurant";


    // private $dbHost1     = "25.69.87.199";
    // private $dbUsername1 = "notebook1";
    // private $dbPassword1 = "123";
    // private $dbName1     = "restaurant";


    // private $dbHost2     = "25.42.132.140";
    // private $dbUsername2 = "notebook1";
    // private $dbPassword2 = "123";
    // private $dbName2    = "restaurant";

    //notebook2
    // private $conn;
    // private $conn1;
    // private $conn2;
    // private $dbHost     = "localhost";
    // private $dbUsername = "root";
    // private $dbPassword = "";
    // private $dbName    = "restaurant";

    
    // private $dbHost1     = "25.35.50.147";
    // private $dbUsername1 = "notebook2";
    // private $dbPassword1 = "123";
    // private $dbName1     = "restaurant";


    // private $dbHost2     = "25.69.87.199";
    // private $dbUsername2 = "notebook2";
    // private $dbPassword2 = "123";
    // private $dbName2    = "restaurant";
    
    
    
    public function __construct(){              
}


public function connect(){


    $this->conn = $this->connectToDBS($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName);
    $this->conn1 = $this->connectToDBS($this->dbHost1, $this->dbUsername1, $this->dbPassword1, $this->dbName1);
    $this->conn2 = $this->connectToDBS($this->dbHost2, $this->dbUsername2, $this->dbPassword2, $this->dbName2);
    $connection = [
        'conn' => $this->conn,
        'conn1' => $this->conn1,
        'conn2' => $this->conn2
    ];
    foreach($connection as $value){
        if($value instanceof mysqli){
         array_push($this->aviableconnection ,$value);
      }
  else if(is_string($value)){
  array_push($this->notaviableconnection,$value);

  }}
  $this->synchronize();

}
// synhronizuje databazu ktora bola odpojena a naskocila/precita vsetky sql prikazy z notavaiblenodes.txt a vykona ich na danej databaze
public function synchronize (){
    $deletedrows=[];
if(file_exists("notaviablenodes.txt")){
    $myfile = "notaviablenodes.txt";
    // nacitanie celeho textoveho suboru do pola $lines
    $lines = File($myfile,FILE_SKIP_EMPTY_LINES);
    
    for ($i=0; $i<sizeof($lines);$i++){
        // oddeli ip od sql prikazu
        $boderOfIP=strpos($lines[$i],":");
        $ip= substr($lines[$i],0,$boderOfIP); 
        $sqlcommand=substr($lines[$i],$boderOfIP+1); 
        
        // pokusy sa pripojit na databazu ktorej ip nasiel v textovom subore
    $db=$this->connectToDBS($ip,$this->dbUsername1, $this->dbPassword1, $this->dbName1);
   // ak je objekt mzsqly pripojenie bolo uspesne  a vykona mysqli prikaz na danaj databaze
    if($db instanceof mysqli)
{
        $db->query($sqlcommand);
    echo "uzol".$ip."bol synchronizavany s ostatnymi uzlami".PHP_EOL;
    array_push($deletedrows, $i);
    
}
    else {//echo "synchronize with".$ip. "was not sucessful".PHP_EOL;
    }
}
    // zmaze vsetky riadky v poli ktore boli vykonane
     foreach($deletedrows as $value)
    { unset($lines[$value]);
        
    }
    // prepise subor]
    file_put_contents("notaviablenodes.txt", implode("", $lines));
    

    
    
}
}
    
    
    public function connectToDBS($servername, $username, $password, $dbname) {    
            try
{ $timeout = 1;  
$link = mysqli_init( );
$link->options( MYSQLI_OPT_CONNECT_TIMEOUT, $timeout ); 
if($link->real_connect($servername, $username, $password, $dbname) )
    {
       return $link;
    }
    else
    { 
        throw new Exception('Unable to connect to noid '.$servername);
    }
}
catch(Exception $e)
{
    //echo $e->getMessage();
    return $servername;
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
             foreach($this->aviableconnection as $value)
            {
                 $insert =  $value->query($query);   
                 //var_dump($insert);
             }
             if (!empty($this->notaviableconnection)){

                $myfile = fopen("notaviablenodes.txt", "a+") or die("Unable to open file!");
              
                foreach($this->notaviableconnection as $this->value)
                //$current= file_get_contents($myfile);
                fwrite($myfile, $this->value.":".$query.PHP_EOL);
                fclose($myfile);

             }
            
            
           // $insert = $this->conn->query($query)&&$this->conn1->query($query)&&$this->conn2->query($query);
          
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
            foreach($this->aviableconnection as $this->value)
            {
                 $update =  $this->value->query($query);   
             }
             if (!empty($this->notaviableconnection)){

                $myfile = fopen("notaviablenodes.txt", "a+") or die("Unable to open file!");
              
                foreach($this->notaviableconnection as $this->value)
                //$current= file_get_contents($myfile);
                fwrite($myfile, $this->value.":".$query.PHP_EOL);
                fclose($myfile);

             }
            //$update =  $this->conn->query($query)&&$this->conn1->query($query)&&$this->conn2->query($query);
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
        foreach($this->aviableconnection as $this->value)
        {
             $delete =  $this->value->query($query);   
         }
         if (!empty($this->notaviableconnection)){

            $myfile = fopen("notaviablenodes.txt", "a+") or die("Unable to open file!");
          
            foreach($this->notaviableconnection as $this->value)
            //$current= file_get_contents($myfile);
            fwrite($myfile, $this->value.":".$query.PHP_EOL);
            fclose($myfile);

         }
        //$delete =  $this->conn->query($query)&&$this->conn1->query($query)&&$this->conn2->query($query);
        return $delete?true:false;
    }
}


?>
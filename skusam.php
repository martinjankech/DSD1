<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<body>
<?php
ini_set('mysql.connect_timeout', 2);
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
        
        }
        public function connectToDBS($servername, $username, $password, $dbname) {
  
            // $conn = mysqli_connect($servername,$username,$password,$dbname);
            // //$conn -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 2);
            // if ($conn -> connect_errno) {
            //     echo "Failed to connect to MySQL: " . $conn -> connect_error;
            //     exit();
            // }     
        
            //     else{

            //         return $conn;
            //     }

            // if($conn = mysqli_connect($servername,$username,$password,$dbname)){
            //     return $conn;}
            //     else 

            //      {;
                     
            //         return 0;}
            try
{
    if ($db = @mysqli_connect($servername, $username, $password, $dbname))
    {echo "connect sucessfull".PHP_EOL;
       return $db;
    }
    else
    { ;
        throw new Exception('Unable to connect to noid '.$servername);
    }
}
catch(Exception $e)
{
    //echo $e->getMessage();
    return $servername;
}
            }
            
  
        }
  ?>

<?php
$db=new DB;
$db->connect();

?>

</body>
</html>
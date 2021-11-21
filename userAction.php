<?php
//error_reporting(E_ERROR | E_PARSE);
// Include and initialize DB class
error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL);

require_once 'classes/DB.class.php';
$db = new DB();
$db->connect();


// Database table name
$tblName = 'users';

// If the form is submitted
if(!empty($_POST['action_type'])){
    if($_POST['action_type'] == 'data'){
        // Fetch data based on row ID
        $conditions['where'] = array('id' => $_POST['id']);
        $conditions['return_type'] = 'single';
        $user = $db->getRows($tblName, $conditions);
        
        // Return data as JSON format
        echo json_encode($user);
    }elseif($_POST['action_type'] == 'view'){
        // Fetch all records
        $users = $db->getRows($tblName);
        
        // Render data as HTML format
        if(!empty($users)){
            foreach($users as $row){
                echo '<tr>';
                echo '<td>#'.$row['id'].'</td>';
                echo '<td>'.$row['name'].'</td>';
                echo '<td>'.$row['email'].'</td>';
                echo '<td>'.$row['phone'].'</td>';
                echo '<td>'.$row['node_created'].'</td>';
               
                echo '<td><a href="javascript:void(0);" class="btn btn-warning" rowID="'.$row['id'].'" data-type="edit" data-toggle="modal" data-target="#modalUserAddEdit">edit</a>
                <a href="javascript:void(0);" class="btn btn-danger" onclick="return confirm(\'Are you sure to delete data?\')?userAction(\'delete\', \''.$row['id'].'\'):false;">delete</a></td>';
                echo '</tr>';
            }
        }else{
            echo '<tr><td colspan="5">No user(s) found...</td></tr>';
        }
    }elseif($_POST['action_type'] == 'add'){
        $msg = '';
        $status = $verr = 0;
        
        // Get user's input
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        
        // Validate form fields
        if(empty($name)){
            $verr = 1;
            $msg .= 'Please enter your name.<br/>';
        }
        if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $verr = 1;
            $msg .= 'Please enter a valid email.<br/>';
        }
        if(empty($phone)){
            $verr = 1;
            $msg .= 'Please enter your phone num.<br/>';
        }
        
        if($verr == 0){
            // Insert data in the database
            $userData = array(
                'name'  => $name,
                'email' => $email,
                'phone' => $phone
            );
            $insert = $db->insert($tblName, $userData);
            //$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            //fwrite($myfile, $insert);
            //fclose($myfile);
            
            if($insert){
                $status = 1;
                $msg .= 'User data has been inserted successfully.';
            }else{
                $msg .= 'Some problem occurred, please try again.';
            }
        }
        
        // Return response as JSON format
        $alertType = ($status == 1)?'alert-success':'alert-danger';
        $statusMsg = '<p class="alert '.$alertType.'">'.$msg.'</p>';
        $response = array(
            'status' => $status,
            'msg' => $statusMsg
        );
        echo json_encode($response);
    }elseif($_POST['action_type'] == 'edit'){
        $msg = '';
        $status = $verr = 0;
        
        if(!empty($_POST['id'])){
            // Get user's input
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            
            // Validate form fields
            if(empty($name)){
                $verr = 1;
                $msg .= 'Please enter your name.<br/>';
            }
            if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
                $verr = 1;
                $msg .= 'Please enter a valid email.<br/>';
            }
            if(empty($phone)){
                $verr = 1;
                $msg .= 'Please enter your phone no.<br/>';
            }
            
            if($verr == 0){
                // Update data in the database
                $userData = array(
                    'name'  => $name,
                    'email' => $email,
                    'phone' => $phone
                );
                $condition = array('id' => $_POST['id']);
                $update = $db->update($tblName, $userData, $condition);
                
                if($update){
                    $status = 1;
                    $msg .= 'User data has been updated successfully.';
                }else{
                    $msg .= 'Some problem occurred, please try again.';
                }
            }
        }else{
            $msg .= 'Some problem occurred, please try again.';
        }
        
        // Return response as JSON format
        $alertType = ($status == 1)?'alert-success':'alert-danger';
        $statusMsg = '<p class="alert '.$alertType.'">'.$msg.'</p>';
        $response = array(
            'status' => $status,
            'msg' => $statusMsg
        );
        echo json_encode($response);
    }elseif($_POST['action_type'] == 'delete'){
        $msg = '';
        $status = 0;
        
        if(!empty($_POST['id'])){
            // Delate data from the database
            $condition = array('id' => $_POST['id']);
            $delete = $db->delete($tblName, $condition);
            
            if($delete){
                $status = 1;
                $msg .= 'User data has been deleted successfully.';
            }else{
                $msg .= 'Some problem occurred, please try again.';
            }
        }else{
            $msg .= 'Some problem occurred, please try again.';
        }  

        // Return response as JSON format
        $alertType = ($status == 1)?'alert-success':'alert-danger';
        $statusMsg = '<p class="alert '.$alertType.'">'.$msg.'</p>';
        $response = array(
            'status' => $status,
            'msg' => $statusMsg
        );
        echo json_encode($response);
    }
}

exit;
?>
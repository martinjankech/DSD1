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
//error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('mysql.connect_timeout', 2);
error_reporting(E_ALL);
?>
    
<script>
// Update the users data list
function getUsers(){
    $.ajax({
        type: 'POST',
        url: 'userAction.php',
        data: 'action_type=view',
        success:function(html){
            $('#userData').html(html);
        }
    });
}

// Send CRUD requests to the server-side script
function userAction(type, id){
    id = (typeof id == "undefined")?'':id;
    var userData = '', frmElement = '';
    if(type == 'add'){
        frmElement = $("#modalUserAddEdit");
        userData = frmElement.find('form').serialize()+'&action_type='+type+'&id='+id;
       console.log(userData)
    }else if (type == 'edit'){
        frmElement = $("#modalUserAddEdit");
        userData = frmElement.find('form').serialize()+'&action_type='+type;
        console.log(userData)
    }else{
        frmElement = $(".row");
        userData = 'action_type='+type+'&id='+id;
       console.log(userData)
    }
    frmElement.find('.statusMsg').html('');
    $.ajax({
        type: 'POST',
        url: 'userAction.php',
        dataType: 'JSON',
        data: userData,
        beforeSend: function(){
            frmElement.find('form').css("opacity", "0.5");
        },
        success:function(resp){
            frmElement.find('.statusMsg').html(resp.msg);
           // alert('ajax was called')
            console.log(resp.msg)
            if(resp.status == 1){
                console.log(resp.status)
                if(type == 'add'){
                    frmElement.find('form')[0].reset();
                }
                getUsers();
            }
            frmElement.find('form').css("opacity", "");
        }
    });
}

// Fill the user's data in the edit form
function editUser(id){
    $.ajax({
        type: 'POST',
        url: 'userAction.php',
        dataType: 'JSON',
        data: 'action_type=data&id='+id,
        success:function(data){
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#phone').val(data.phone);
        }
    });
}

// Actions on modal show and hidden events
$(function(){
    $('#modalUserAddEdit').on('show.bs.modal', function(e){
        var type = $(e.relatedTarget).attr('data-type');
        console.log(type)
        var userFunc = "userAction('add');";
        if(type == 'edit'){
            userFunc = "userAction('edit');";
            var rowId = $(e.relatedTarget).attr('rowID');
            console.log(rowId)
            editUser(rowId);
        }
        $('#userSubmit').attr("onclick", userFunc);
    });
    
    $('#modalUserAddEdit').on('hidden.bs.modal', function(){
        $('#userSubmit').attr("onclick", "");
        $(this).find('form')[0].reset();
        $(this).find('.statusMsg').html('');
    });
});
</script>
<?php
// Include and initialize DB class
require_once 'classes/DB.class.php';
session_start();
$_SESSION["dbs"]= $db = new DB();
$db->connect();

if(!empty($db->getNotaviableconnection())){

    foreach($db->getNotaviableconnection() as $value){

        echo '<div class="alert alert-danger" role="alert">
         uzol '.$value.' je odpojený !
     </div>';

    }


}

// Fetch the users data
$users = $db->getRows('users');
?>
 <div class="container">
    <div class="row">
        <div class="col-md-12 head">
            <h5>Users Big PC (25.69.87.199)</h5>
            <!-- Add link -->
            <div class="float-right">
                <a href="javascript:void(0);" class="btn btn-success" data-type="add" data-toggle="modal" data-target="#modalUserAddEdit"><i class="plus"></i> New User</a>
            </div>
        </div>
        <div class="statusMsg"></div>
        <!-- List the users -->
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="userData">
                <?php if(!empty($users)){ foreach($users as $row){ ?>
                <tr>
                    <td><?php echo '#'.$row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td>
                        <a href="javascript:void(0);" class="btn btn-warning" rowID="<?php echo $row['id']; ?>" data-type="edit" data-toggle="modal" data-target="#modalUserAddEdit">edit</a>
                        <a href="javascript:void(0);" class="btn btn-danger" onclick="return confirm('Are you sure to delete data?')?userAction('delete', '<?php echo $row['id']; ?>'):false;">delete</a>
                    </td>
                </tr>
                <?php } }else{ ?>
                <tr><td colspan="5">No user(s) found...</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>



<!-- Modal Add and Edit Form -->
<div class="modal fade" id="modalUserAddEdit" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Add New User</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                <div class="statusMsg"></div>
                <form role="form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter phone no">
                    </div>
                    <input type="hidden" class="form-control" name="id" id="id"/>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="userSubmit">SUBMIT</button>
            </div>
        </div>
    </div>
</div>
    
</body>
</html>
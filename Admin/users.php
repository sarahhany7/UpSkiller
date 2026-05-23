<?php
session_start();
include('init.php');
/** @var PDO $connect */


if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

$statement = $connect->prepare("SELECT * FROM users");
$statement->execute();
$userCount = $statement->rowCount();
$result = $statement->fetchAll();

$page = "All";
if (isset($_GET['request'])) {
    $page = $_GET['request'];
}

if ($page == "All") {
 ?>
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-10 m-auto text-center">
                <?php
                if (isset($_SESSION['message'])) {
                    echo "<h3 class='alert alert-success'>" . $_SESSION['message'] . "</h3>";
                    unset($_SESSION['message']);
                    header("Refresh:3;url=users.php");
                }
                ?>
                <h2>Users <span class="badge badge-primary"><?php echo $userCount ?> </span> <a class="btn btn-success" href="users.php?request=Create">Create New User</a></h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Operations</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result as $item) {
                        ?>
                            <tr>
                                <td><?php echo $item['User_Id'] ?></td>
                                <td><?php echo $item['User_Name'] ?></td>
                                <td><?php echo $item['Email'] ?></td>
                                <td>
                                    <a class="btn btn-success" href="users.php?request=Show&id=<?php echo $item['User_Id'] ?>"><i class="fa-solid fa-eye fa-lg"></i></a> 
                                    <a class="btn btn-primary" href="users.php?request=Edit&id=<?php echo $item['User_Id'] ?>"><i class="fa-solid fa-pen-to-square fa-lg"></i></a> 
                                    <a class="btn btn-danger" href="users.php?request=Delete&id=<?php echo $item['User_Id'] ?>"><i class="fa-solid fa-trash fa-lg"></i></a> 
                                </td>
                            </tr>

                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php
} elseif ($page == "Show") {
    $userId = $_GET['id'];

    $statement = $connect->prepare("SELECT * FROM users WHERE User_Id = ?");
    $statement->execute(array($userId));
    $result = $statement->fetch();


    if (isset($_GET['deleteid'])) {
        $deleteUser = $_GET['deleteid'];

        $statement = $connect->prepare("DELETE FROM users WHERE User_Id = ?");
        $statement->execute(array($deleteUser));
        header('Location:users.php?request=All');
    }


 ?>
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-12 m-auto text-center">
                <h2>User Details
                    <a href="users.php?request=All" class="btn btn-success"><i class="fa-solid fa-house fa-lg"></i></a>
                    <a href="users.php?request=Show&deleteid=<?php echo $result['User_Id'] ?>" class="btn btn-danger"><i class="fa-solid fa-trash fa-lg"></i></a>
                </h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Birthdate</th>
                        <th>Gender</th>
                        <th>University Name</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>

                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $result['User_Id'] ?></td>
                            <td><?php echo $result['User_Name'] ?></td>
                            <td><?php echo $result['Birthdate'] ?></td>
                            <td><?php echo $result['Gender'] ?></td>
                            <td><?php echo $result['University_Name'] ?></td>
                            <td><?php echo $result['Email'] ?></td>
                            <td><?php echo $result['Password'] ?></td>
                            <td><?php echo $result['Role'] ?></td>
                            <td><?php echo $result['Status'] ?></td>
                            <td><?php echo $result['Created_At'] ?></td>
                            <td><?php echo $result['Updated_At'] ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

<?php

} elseif ($page == "Delete") {
    $userId = $_GET['id'];

    $statement = $connect->prepare("DELETE FROM users WHERE User_Id = ?");
    $statement->execute(array($userId));
    $_SESSION['message'] = "Deleted Successfully";
    header('Location:users.php?request=All');
} elseif ($page == "Create") {

    $Errid = $Errname = $Errbirth = $Errgender = $Erruniname = $Erremail = $Errpass = $Errrole = $Errstatus = "";
    $id = $name = $birthdate = $gender = $uniname = $uniname = $email = $pass = $role = $status = "";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $id = $_POST['id'];
        $name = $_POST['username'];
        $birthdate = $_POST['birthdate'];
        $gender = $_POST['gender'];
        $uniname = $_POST['uniname'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $role = $_POST['role'];
        $status = $_POST['status'];

        $statement = $connect -> prepare("SELECT * FROM users WHERE User_Id = ?");
        $statement -> execute(array($id));
        $checkId = $statement -> rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($name)) {
            $Errname = "Please Enter Name";
        }
        if (empty($birthdate)) {
            $Errbirth = "Please Enter Birthdate";
        }
        if (empty($gender)) {
            $Errgender = "Please Enter Gender";
        }
        if (empty($uniname)) {
            $Erruniname = "Please Enter University Name";
        }
        if (empty($email)) {
            $Erremail = "Please Enter Email";
        }
        if (empty($pass)) {
            $Errpass = "Please Enter Password";
        }
        if (empty($role)) {
            $Errrole = "Please Enter Role";
        }
        if ($status == "") {
            $Errstatus = "Please Enter Status";
        }
        if (empty($Errid) && empty($Errname) && empty($Errbirth) && empty($Errgender) && empty($Erruniname) && empty($Erremail) && empty($Errpass) && empty($Errrole) && empty($Errstatus)) {

            if($checkId == 0){
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['birthdate'] = $birthdate;
                $_SESSION['gender'] = $gender;
                $_SESSION['uniname'] = $uniname;
                $_SESSION['email'] = $email;
                $_SESSION['pass'] = $pass;
                $_SESSION['role'] = $role;
                $_SESSION['status'] = $status;
    
                header('Location:users.php?request=Store');
            }else{
                $_SESSION['message_error'] = "Duplicated ID Please Enter Another One";
            }
        }
    }

 ?>
    <div class="container mt-2 mb-3">
        <div class="row">
            <div class="col-md-8 m-auto">
                <?php 
                if(isset($_SESSION['message_error'])){
                    echo "<h3 class='alert alert-danger text-center'>". $_SESSION['message_error'] ."</h3>";
                    unset($_SESSION['message_error']);
                }
                ?>
                <h2 class="text-center">Create New User</h2>
                <form action="users.php?request=Create" method="post">
                    <label>ID <span class="text-danger">*</span></label>
                    <input type="number" name="id" class="form-control" value="<?php echo $id ?>">
                    <h6 class="text-danger"><?php echo $Errid ?></h6>

                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" value="<?php echo $name ?>">
                    <h6 class="text-danger"><?php echo $Errname ?></h6>

                    <label>Birthdate <span class="text-danger">*</span></label>
                    <input type="date" name="birthdate" class="form-control" value="<?php echo $birthdate ?>">
                    <h6 class="text-danger"><?php echo $Errbirth ?></h6>

                    <label>Gender <span class="text-danger">*</span></label>
                    <select class="form-control" name="gender">
                        <option value="">Select</option>
                        <option value="Male" <?php echo ($gender == "Male") ? "selected" : "" ?>>Male</option>
                        <option value="Female" <?php echo ($gender == "Female") ? "selected" : "" ?>>Female</option>
                    </select>
                    <h6 class="text-danger"><?php echo $Errgender ?></h6>

                    <label>University Name <span class="text-danger">*</span></label>
                    <input type="text" name="uniname" class="form-control" value="<?php echo $uniname ?>">
                    <h6 class="text-danger"><?php echo $Erruniname ?></h6>

                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?php echo $email ?>">
                    <h6 class="text-danger"><?php echo $Erremail ?></h6>

                    <label>Password <span class="text-danger">*</span></label>
                    <input type="password" name="pass" class="form-control" value="<?php echo $pass ?>">
                    <h6 class="text-danger"><?php echo $Errpass ?></h6>

                    <label>Role <span class="text-danger">*</span></label>
                    <select class="form-control" name="role">
                        <option value="">Select</option>
                        <option value="Admin" <?php echo ($role == "Admin") ? "selected" : "" ?>>Admin</option>
                        <option value="User" <?php echo ($role == "User") ? "selected" : "" ?>>User</option>
                    </select>
                    <h6 class="text-danger"><?php echo $Errrole ?></h6>

                    <label>Status <span class="text-danger">*</span></label>
                    <select class="form-control" name="status">
                        <option value="">Select</option>
                        <option value="1" <?php echo ($status == "1" ? "selected" : "") ?>>Active</option>
                        <option value="0" <?php echo ($status == "0" ? "selected" : "") ?>>Block</option>
                    </select>
                    <h6 class="text-danger"><?php echo $Errstatus ?></h6>
                    <br>
                    <button type="submit" class="btn-block btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

<?php
} elseif ($page == "Store") {

    $id = $_SESSION['id'];
    $name = $_SESSION['name'];
    $birthdate = $_SESSION['birthdate'];
    $gender = $_SESSION['gender'];
    $uniname = $_SESSION['uniname'];
    $email = $_SESSION['email'];
    $pass = $_SESSION['pass'];
    $role = $_SESSION['role'];
    $status = $_SESSION['status'];

    $statement = $connect -> prepare("INSERT INTO users
    (`User_Id`,`User_Name`,Birthdate,Gender,University_Name,Email,`Password`,`Role`,`Status`,Created_At)
    VALUES (?,?,?,?,?,?,?,?,?,now())
    ");
    $statement -> execute(array($id,$name,$birthdate,$gender,$uniname,$email,$pass,$role,$status));
    header('Location:users.php?request=All');

    $_SESSION['message'] = "Created Successfully";
}elseif($page == "Edit"){
    $Errid = $Errname = $Errbirth = $Erruniname = $Erremail = $Errpass = "";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $old_id = $_POST['old_id'];
        $id = $_POST['id'];
        $name = $_POST['username'];
        $birthdate = $_POST['birthdate'];
        $gender = $_POST['gender'];
        $uniname = $_POST['uniname'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $role = $_POST['role'];
        $status = $_POST['status'];

        $statement = $connect -> prepare("SELECT * FROM users WHERE User_Id = ? AND User_Id != ?");
        $statement -> execute(array($id , $old_id));
        $checkId = $statement -> rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($name)) {
            $Errname = "Please Enter Name";
        }
        if (empty($birthdate)) {
            $Errbirth = "Please Enter Birthdate";
        }
        if (empty($uniname)) {
            $Erruniname = "Please Enter University Name";
        }
        if (empty($email)) {
            $Erremail = "Please Enter Email";
        }
        if (empty($pass)) {
            $Errpass = "Please Enter Password";
        }
        if (empty($Errid) && empty($Errname) && empty($Errbirth) && empty($Erruniname) && empty($Erremail) && empty($Errpass)) {

            if($checkId == 0){
                $_SESSION['old_id'] = $old_id;
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['birthdate'] = $birthdate;
                $_SESSION['gender'] = $gender;
                $_SESSION['uniname'] = $uniname;
                $_SESSION['email'] = $email;
                $_SESSION['pass'] = $pass;
                $_SESSION['role'] = $role;
                $_SESSION['status'] = $status;
    
                header('Location:users.php?request=SaveUpdate');
            }else{
                $_SESSION['message_error'] = "Duplicated ID Please Enter Another One";
            }
        }
    }



    $userId = $_GET['id'];

    $statement = $connect -> prepare('SELECT * FROM users WHERE User_Id = ?');
    $statement -> execute(array($userId));
    $result = $statement -> fetch();

    ?>
       <div class="container">
        <div class="row">
            <div class="col-md-8 m-auto">
                <?php
                if(isset($_SESSION['message_error'])){
                    echo "<h4 class='alert alert-danger text-center'>".$_SESSION['message_error']."</h4>";
                    unset($_SESSION['message_error']);
                }
                ?>

                <form action="users.php?request=Edit&id=<?php echo $result['User_Id'] ?>" method="post">
                    <input type="hidden" name="old_id" value="<?php echo $result['User_Id'] ?>">

                    <label>ID</label>
                    <input type="number" name="id" class="form-control" value="<?php echo $result['User_Id'] ?>">
                    <?php echo "<h6 class='text-danger'>".$Errid."</h6>" ?>

                    <label>Name</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $result['User_Name'] ?>">
                    <?php echo "<h6 class='text-danger'>".$Errname."</h6>" ?>

                    <label>Birthdate</label>
                    <input type="date" name="birthdate" class="form-control" value="<?php echo $result['Birthdate'] ?>">
                    <?php echo "<h6 class='text-danger'>".$Errbirth."</h6>" ?>

                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <?php
                        if($result['gender'] == "Male"){
                            echo "<option value = 'Male' selected >Male</option>";
                            echo "<option value = 'Female' >Female</option>";
                        }else{
                            echo "<option value = 'Male' >Male</option>";
                            echo "<option value = 'Female' selected >Female</option>";
                        }
                        ?>
                    </select>

                    <label>University Name</label>
                    <input type="text" name="uniname" class="form-control" value="<?php echo $result['University_Name'] ?>">
                    <?php echo "<h6 class='text-danger'>".$Erruniname."</h6>" ?>

                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $result['Email'] ?>">
                    <?php echo "<h6 class='text-danger'>".$Erremail."</h6>" ?>

                    <label>Password</label>
                    <input type="password" name="pass" class="form-control" value="<?php echo $result['Password'] ?>">
                    <?php echo "<h6 class='text-danger'>".$Errpass."</h6>" ?>

                    <label>Role</label>
                    <select name="role" class="form-control">
                        <?php
                        if($result['role'] == "Admin"){
                            echo "<option value = 'Admin selected >Admin</option>";
                            echo "<option value = 'Female' >Female</option>";
                        }else{
                            echo "<option value = 'Admin' >Admin</option>";
                            echo "<option value = 'User' selected >User</option>";
                        }
                        ?>
                    </select>

                    <label>Status</label>
                    <select name="status" class="form-control">
                        <?php
                        if($result['status'] == "Block"){
                            echo "<option value = '0' selected >Block</option>";
                            echo "<option value = '1' >Active</option>";
                        }else{
                            echo "<option value = '0' >Block</option>";
                            echo "<option value = '1' selected >Active</option>";
                        }
                        ?>
                    </select>

                    <button type="submit" class="btn-block btn btn-primary mb-3 mt-3">Submit</button>
                </form>
            </div>
        </div>
       </div>
    
    <?php
}elseif($page == "SaveUpdate"){

    $old_id = $_SESSION['old_id'];
    $id = $_SESSION['id'];
    $name = $_SESSION['name'];
    $birthdate = $_SESSION['birthdate'];
    $gender = $_SESSION['gender'];
    $uniname = $_SESSION['uniname'];
    $email = $_SESSION['email'];
    $pass = $_SESSION['pass'];
    $role = $_SESSION['role'];
    $status = $_SESSION['status'];

    $statement = $connect -> prepare("UPDATE  users SET 
    `User_Id` = ?,
    `User_Name` = ?,
    Birthdate = ?,
    Gender = ?,
    University_Name = ?,
    Email = ?,
    `Password` = ?,
    `Role` = ?,
    `Status` = ?,
    Updated_AT = now()
    WHERE User_Id = ?
    ");

    $statement -> execute(array($id,$name,$birthdate,$gender,$uniname,$email,$pass,$role,$status,$old_id));
    header('Location:users.php?request=All');

    $_SESSION['message'] = "Updated Successfully";
}



?>







<?php
include('includes/temp/footer.php');
?>
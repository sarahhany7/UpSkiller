<?php
session_start();
include('init.php');
/** @var PDO $connect */


if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

$statement = $connect->prepare("SELECT * FROM platforms");
$statement->execute();
$platformCount = $statement->rowCount();
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
                    header("Refresh:3;url=platforms.php");
                }
                ?>
                <h2>Platforms <span class="badge badge-primary"><?php echo $platformCount ?> </span> <a class="btn btn-success" href="platforms.php?request=Create">Create New Platform</a></h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Operations</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result as $item) {
                        ?>
                            <tr>
                                <td><?php echo $item['Platform_Id'] ?></td>
                                <td><?php echo $item['Platform_Name'] ?></td>
                                <td>
                                    <a class="btn btn-success" href="platforms.php?request=Show&id=<?php echo $item['Platform_Id'] ?>"><i class="fa-solid fa-eye fa-lg"></i></a> 
                                    <a class="btn btn-primary" href="platforms.php?request=Edit&id=<?php echo $item['Platform_Id'] ?>"><i class="fa-solid fa-pen-to-square fa-lg"></i></a> 
                                    <a class="btn btn-danger" href="platforms.php?request=Delete&id=<?php echo $item['Platform_Id'] ?>"><i class="fa-solid fa-trash fa-lg"></i></a> 
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
    $platformId = $_GET['id'];

    $statement = $connect->prepare("SELECT * FROM platforms WHERE Platform_Id = ?");
    $statement->execute(array($platformId));
    $result = $statement->fetch();


    if (isset($_GET['deleteid'])) {
        $deleteUser = $_GET['deleteid'];

        $statement = $connect->prepare("DELETE FROM platforms WHERE Platform_Id = ?");
        $statement->execute(array($deleteUser));
        header('Location:platforms.php?request=All');
    }


 ?>
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-12 m-auto text-center">
                <h2>Platform Details
                    <a href="platforms.php?request=All" class="btn btn-success"><i class="fa-solid fa-house fa-lg"></i></a>
                    <a href="platforms.php?request=Show&deleteid=<?php echo $result['Platform_Id'] ?>" class="btn btn-danger"><i class="fa-solid fa-trash fa-lg"></i></a>
                </h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Created</th>
                        <th>Updated</th>

                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $result['Platform_Id'] ?></td>
                            <td><?php echo $result['Platform_Name'] ?></td>
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
    $platformId = $_GET['id'];

    $statement = $connect->prepare("DELETE FROM platforms WHERE Platform_Id = ?");
    $statement->execute(array($platformId));
    $_SESSION['message'] = "Deleted Successfully";
    header('Location:platforms.php?request=All');
} elseif ($page == "Create") {

    $Errid = $Errname = "";
    $id = $name = "";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $id = $_POST['id'];
        $name = $_POST['username'];

        $statement = $connect -> prepare("SELECT * FROM platforms WHERE Platform_Id = ?");
        $statement -> execute(array($id));
        $checkId = $statement -> rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($name)) {
            $Errname = "Please Enter Name";
        }
        if (empty($Errid) && empty($Errname)) {

            if($checkId == 0){
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
    
                header('Location:platforms.php?request=Store');
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
                <h2 class="text-center">Create New Platform</h2>
                <form action="platforms.php?request=Create" method="post">
                    <label>ID <span class="text-danger">*</span></label>
                    <input type="number" name="id" class="form-control" value="<?php echo $id ?>">
                    <h6 class="text-danger"><?php echo $Errid ?></h6>

                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" value="<?php echo $name ?>">
                    <h6 class="text-danger"><?php echo $Errname ?></h6>
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

    $statement = $connect -> prepare("INSERT INTO platforms
    (Platform_Id,Platform_Name,Created_At)
    VALUES (?,?,now())
    ");
    $statement -> execute(array($id,$name));
    header('Location:platforms.php?request=All');

    $_SESSION['message'] = "Created Successfully";
}elseif($page == "Edit"){
    $Errid = $Errname = "";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $old_id = $_POST['old_id'];
        $id = $_POST['id'];
        $name = $_POST['username'];

        $statement = $connect -> prepare("SELECT * FROM platforms WHERE Platform_Id = ? AND Platform_Id != ?");
        $statement -> execute(array($id , $old_id));
        $checkId = $statement -> rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($name)) {
            $Errname = "Please Enter Name";
        }

        if (empty($Errid) && empty($Errname) ) {

            if($checkId == 0){
                $_SESSION['old_id'] = $old_id;
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
    
                header('Location:platforms.php?request=SaveUpdate');
            }else{
                $_SESSION['message_error'] = "Duplicated ID Please Enter Another One";
            }
        }
    }



    $platformId = $_GET['id'];

    $statement = $connect -> prepare('SELECT * FROM platforms WHERE Platform_Id = ?');
    $statement -> execute(array($platformId));
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

                <form action="platforms.php?request=Edit&id=<?php echo $result['Platform_Id'] ?>" method="post">
                    <input type="hidden" name="old_id" value="<?php echo $result['Platform_Id'] ?>">

                    <label>ID</label>
                    <input type="number" name="id" class="form-control" value="<?php echo $result['Platform_Id'] ?>">
                    <?php echo "<h6 class='text-danger'>".$Errid."</h6>" ?>

                    <label>Name</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $result['Platform_Name'] ?>">
                    <?php echo "<h6 class='text-danger'>".$Errname."</h6>" ?>

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

    $statement = $connect -> prepare("UPDATE  platforms SET 
    Platform_Id = ?,
    Platform_Name = ?,
    Updated_AT = now()
    WHERE Platform_Id = ?
    ");

    $statement -> execute(array($id,$name,$old_id));
    header('Location:platforms.php?request=All');

    $_SESSION['message'] = "Updated Successfully";
}
<?php
session_start();
include('init.php');
/** @var PDO $connect */


if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

$statement = $connect->prepare("SELECT * FROM tracks");
$statement->execute();
$trackCount = $statement->rowCount();
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
                    header("Refresh:3;url=tracks.php");
                }
                ?>
                <h2>Tracks <span class="badge badge-primary"><?php echo $trackCount ?> </span> <a class="btn btn-success" href="tracks.php?request=Create">Create New Track</a></h2>
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
                                <td><?php echo $item['Track_Id'] ?></td>
                                <td><?php echo $item['Track_Name'] ?></td>
                                <td>
                                    <a class="btn btn-success" href="tracks.php?request=Show&id=<?php echo $item['Track_Id'] ?>"><i class="fa-solid fa-eye fa-lg"></i></a>
                                    <a class="btn btn-primary" href="tracks.php?request=Edit&id=<?php echo $item['Track_Id'] ?>"><i class="fa-solid fa-pen-to-square fa-lg"></i></a>
                                    <a class="btn btn-danger" href="tracks.php?request=Delete&id=<?php echo $item['Track_Id'] ?>"><i class="fa-solid fa-trash fa-lg"></i></a>
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
    $trackId = $_GET['id'];

    $statement = $connect->prepare("SELECT * FROM tracks WHERE track_Id = ?");
    $statement->execute(array($trackId));
    $result = $statement->fetch();


    if (isset($_GET['deleteid'])) {
        $deleteUser = $_GET['deleteid'];

        $statement = $connect->prepare("DELETE FROM tracks WHERE Track_Id = ?");
        $statement->execute(array($deleteUser));
        header('Location:tracks.php?request=All');
    }


 ?>
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-12 m-auto text-center">
                <h2>Track Details
                    <a href="tracks.php?request=All" class="btn btn-success"><i class="fa-solid fa-house fa-lg"></i></a>
                    <a href="tracks.php?request=Show&deleteid=<?php echo $result['Track_Id'] ?>" class="btn btn-danger"><i class="fa-solid fa-trash fa-lg"></i></a>
                </h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>

                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $result['Track_Id'] ?></td>
                            <td><?php echo $result['Track_Name'] ?></td>
                            <td><?php echo $result['Description'] ?></td>
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
    $trackId = $_GET['id'];

    $statement = $connect->prepare("DELETE FROM tracks WHERE Track_Id = ?");
    $statement->execute(array($trackId));
    $_SESSION['message'] = "Deleted Successfully";
    header('Location:tracks.php?request=All');
} elseif ($page == "Create") {

    $Errid = $Errname = $Errdescription = $Errstatus = "";
    $id = $name = $description = $status = "";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $id = $_POST['id'];
        $name = $_POST['username'];
        $description = $_POST['description'];
        $status = $_POST['status'];

        $statement = $connect->prepare("SELECT * FROM tracks WHERE Track_Id = ?");
        $statement->execute(array($id));
        $checkId = $statement->rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($name)) {
            $Errname = "Please Enter Name";
        }
        if (empty($description)) {
            $Errdescription = "Please Enter Description";
        }
        if ($status == "") {
            $Errstatus = "Please Enter Status";
        }
        if (empty($Errid) && empty($Errname) && empty($Errdescription) && empty($Errstatus)) {

            if ($checkId == 0) {
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['description'] = $description;
                $_SESSION['status'] = $status;

                $imagePath = null;

                if (isset($_FILES['track_image']) && $_FILES['track_image']['error'] == 0) {
                    $ext = pathinfo($_FILES['track_image']['name'], PATHINFO_EXTENSION);
                    $newFileName = "track_" . $id . "." . $ext; // اسم جديد للصورة
                    $uploadDir = "uploads/tracks/"; // المكان اللي الصور هتتحط فيه
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true); // لو المجلد مش موجود
                    $imagePath = $uploadDir . $newFileName;
                    move_uploaded_file($_FILES['track_image']['tmp_name'], $imagePath);
                }

                $_SESSION['imagePath'] = $imagePath;




                header('Location:tracks.php?request=Store');
            } else {
                $_SESSION['message_error'] = "Duplicated ID Please Enter Another One";
            }
        }
    }

 ?>
    <div class="container mt-2 mb-3">
        <div class="row">
            <div class="col-md-8 m-auto">
                <?php
                if (isset($_SESSION['message_error'])) {
                    echo "<h3 class='alert alert-danger text-center'>" . $_SESSION['message_error'] . "</h3>";
                    unset($_SESSION['message_error']);
                }
                ?>
                <h2 class="text-center">Create New Track</h2>
                <form action="tracks.php?request=Create" method="post" enctype="multipart/form-data">
                    <label>ID <span class="text-danger">*</span></label>
                    <input type="number" name="id" class="form-control" value="<?php echo $id ?>">
                    <h6 class="text-danger"><?php echo $Errid ?></h6>

                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" value="<?php echo $name ?>">
                    <h6 class="text-danger"><?php echo $Errname ?></h6>

                    <label>Description <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control" value="<?php echo $description ?>">
                    <h6 class="text-danger"><?php echo $Errdescription ?></h6>

                    <label>Track Image</label>
                    <input type="file" name="track_image" class="form-control">

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
    $description = $_SESSION['description'];
    $status = $_SESSION['status'];
    $imagePath = $_SESSION['imagePath'];

    $statement = $connect->prepare("INSERT INTO tracks
    (Track_Id,Track_Name,`Description`,Image_Path,`Status`,Created_At)
    VALUES (?,?,?,?,?,now())
    ");
    $statement->execute(array($id, $name, $description, $imagePath, $status));
    header('Location:tracks.php?request=All');

    $_SESSION['message'] = "Created Successfully";
} elseif ($page == "Edit") {
    $Errid = $Errname = $Errdescription = "";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $old_id = $_POST['old_id'];
        $id = $_POST['id'];
        $name = $_POST['username'];
        $description = $_POST['description'];
        $status = $_POST['status'];

        $statement = $connect->prepare("SELECT * FROM tracks WHERE Track_Id = ? AND Track_Id != ?");
        $statement->execute(array($id, $old_id));
        $checkId = $statement->rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($name)) {
            $Errname = "Please Enter Name";
        }
        if (empty($description)) {
            $Errdescription = "Please Enter Description";
        }
        if (empty($Errid) && empty($Errname) && empty($Errdescription)) {

            if ($checkId == 0) {
                $_SESSION['old_id'] = $old_id;
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['description'] = $description;
                $_SESSION['status'] = $status;

                header('Location:tracks.php?request=SaveUpdate');
            } else {
                $_SESSION['message_error'] = "Duplicated ID Please Enter Another One";
            }
        }
    }



    $trackId = $_GET['id'];

    $statement = $connect->prepare('SELECT * FROM tracks WHERE Track_Id = ?');
    $statement->execute(array($trackId));
    $result = $statement->fetch();

 ?>
    <div class="container">
        <div class="row">
            <div class="col-md-8 m-auto">
                <?php
                if (isset($_SESSION['message_error'])) {
                    echo "<h4 class='alert alert-danger text-center'>" . $_SESSION['message_error'] . "</h4>";
                    unset($_SESSION['message_error']);
                }
                ?>

                <form action="tracks.php?request=Edit&id=<?php echo $result['Track_Id'] ?>" method="post">
                    <input type="hidden" name="old_id" value="<?php echo $result['Track_Id'] ?>">

                    <label>ID</label>
                    <input type="number" name="id" class="form-control" value="<?php echo $result['Track_Id'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errid . "</h6>" ?>

                    <label>Name</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $result['Track_Name'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errname . "</h6>" ?>

                    <label>Description</label>
                    <input type="text" name="description" class="form-control" value="<?php echo $result['Description'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errdescription . "</h6>" ?>

                    <label>Status</label>
                    <select name="status" class="form-control">
                        <?php
                        if ($result['status'] == "Male") {
                            echo "<option value = '0' selected >Block</option>";
                            echo "<option value = '1' >Active</option>";
                        } else {
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
} elseif ($page == "SaveUpdate") {

    $old_id = $_SESSION['old_id'];
    $id = $_SESSION['id'];
    $name = $_SESSION['name'];
    $description = $_SESSION['description'];
    $status = $_SESSION['status'];

    $statement = $connect->prepare("UPDATE  tracks SET 
    Track_Id = ?,
    Track_Name = ?,
    `Description` = ?,
    `Status` = ?,
    Updated_AT = now()
    WHERE Track_Id = ?
    ");

    $statement->execute(array($id, $name, $description, $status, $old_id));
    header('Location:Tracks.php?request=All');

    $_SESSION['message'] = "Updated Successfully";
}






?>




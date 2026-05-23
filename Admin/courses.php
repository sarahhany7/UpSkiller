<?php
session_start();
include('init.php');
/** @var PDO $connect */


if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

$statement = $connect->prepare("
    SELECT courses.*, 
    tracks.Track_Name
    FROM courses
    INNER JOIN tracks 
    ON courses.Track_Id = tracks.Track_Id
");
$statement->execute();
$courseCount = $statement->rowCount();
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
                    header("Refresh:3;url=courses.php");
                }
                ?>
                <h2>Courses <span class="badge badge-primary"><?php echo $courseCount ?> </span> <a class="btn btn-success" href="courses.php?request=Create">Create New Course</a></h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Track Name</th>
                        <th>Operations</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result as $item) {
                        ?>
                            <tr>
                                <td><?php echo $item['Course_Id'] ?></td>
                                <td><?php echo $item['Course_Name'] ?></td>
                                <td><?php echo $item['Track_Name'] ?></td>
                                <td>
                                    <a class="btn btn-success" href="courses.php?request=Show&id=<?php echo $item['Course_Id'] ?>"><i class="fa-solid fa-eye fa-lg"></i></a>
                                    <a class="btn btn-primary" href="courses.php?request=Edit&id=<?php echo $item['Course_Id'] ?>"><i class="fa-solid fa-pen-to-square fa-lg"></i></a>
                                    <a class="btn btn-danger" href="courses.php?request=Delete&id=<?php echo $item['Course_Id'] ?>"><i class="fa-solid fa-trash fa-lg"></i></a>
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
    $courseId = $_GET['id'];

    $statement = $connect->prepare("
    SELECT courses.*, 
    tracks.Track_Name
    FROM courses
    INNER JOIN tracks 
    ON courses.Track_Id = tracks.Track_Id
    WHERE Course_Id = ?
 ");

    $statement->execute(array($courseId));
    $result = $statement->fetch();


    if (isset($_GET['deleteid'])) {
        $deleteUser = $_GET['deleteid'];

        $statement = $connect->prepare("DELETE FROM courses WHERE Course_Id = ?");
        $statement->execute(array($deleteUser));
        header('Location:courses.php?request=All');
    }


 ?>
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-12 m-auto text-center">
                <h2>Course Details
                    <a href="courses.php?request=All" class="btn btn-success"><i class="fa-solid fa-house fa-lg"></i></a>
                    <a href="courses.php?request=Show&deleteid=<?php echo $result['Course_Id'] ?>" class="btn btn-danger"><i class="fa-solid fa-trash fa-lg"></i></a>
                </h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Track ID</th>
                        <th>Track Name</th>
                        <th>Created</th>
                        <th>Updated</th>

                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $result['Course_Id'] ?></td>
                            <td><?php echo $result['Course_Name'] ?></td>
                            <td><?php echo $result['Status'] ?></td>
                            <td><?php echo $result['Track_Id'] ?></td>
                            <td><?php echo $result['Track_Name'] ?></td>
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
    $courseId = $_GET['id'];

    $statement = $connect->prepare("DELETE FROM courses WHERE Course_Id = ?");
    $statement->execute(array($courseId));
    $_SESSION['message'] = "Deleted Successfully";
    header('Location:courses.php?request=All');
} elseif ($page == "Create") {

    $Errid = $Errname = $Errtrackid = $Errstatus = "";
    $id = $name = $trackid =  $status = "";

    $stmtTracks = $connect->prepare("SELECT Track_Id, Track_Name FROM tracks ORDER BY Track_Name ASC");
    $stmtTracks->execute();
    $tracks = $stmtTracks->fetchAll();


    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $id = $_POST['id'] ?? '';
        $name = $_POST['username'] ?? '';
        $trackid = $_POST['trackid'] ?? '';
        $status = $_POST['status'] ?? '';

        $statement = $connect->prepare("SELECT * FROM courses WHERE Course_Id = ?");
        $statement->execute(array($id));
        $checkId = $statement->rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($name)) {
            $Errname = "Please Enter Name";
        }
        if ($status == "") {
            $Errstatus = "Please Enter Status";
        }
        if (empty($trackid)) {
            $Errtrackid = "Please Enter Track ID";
        }
        if (!empty($trackid)) {
            $checkTrack = $connect->prepare("SELECT * FROM tracks WHERE Track_Id = ?");
            $checkTrack->execute([$trackid]);
            $trackExists = $checkTrack->rowCount();

            if ($trackExists == 0) {
                $Errtrackid = "Track ID Not Found in Tracks Table";
            }
        }

        if (empty($Errid) && empty($Errname) && empty($Errtrackid) && empty($Errstatus)) {

            if ($checkId == 0) {
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['status'] = $status;
                $_SESSION['trackid'] = $trackid;

                header('Location:courses.php?request=Store');
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
                <h2 class="text-center">Create New Course</h2>
                <form action="courses.php?request=Create" method="post">
                    <label>ID <span class="text-danger">*</span></label>
                    <input type="number" name="id" class="form-control" value="<?php echo $id ?>">
                    <h6 class="text-danger"><?php echo $Errid ?></h6>

                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" value="<?php echo $name ?>">
                    <h6 class="text-danger"><?php echo $Errname ?></h6>

                    <label>Track <span class="text-danger">*</span></label>
                    <select name="trackid" class="form-control">
                        <option value="">Select Track</option>
                        <?php foreach ($tracks as $t) { ?>
                            <option value="<?= $t['Track_Id'] ?>" <?= ($trackid == $t['Track_Id'] ? "selected" : "") ?>>
                                <?= $t['Track_Name'] ?>
                            </option>
                        <?php } ?>
                    </select>

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
    $trackid = $_SESSION['trackid'];
    $status = $_SESSION['status'];
    $platformid = $_SESSION['platformid'];

    $statement = $connect->prepare("INSERT INTO courses
    (Course_Id, Course_Name, Track_Id, `Status`, Created_At)
    VALUES (?, ?, ?, ?, now())");
    $statement->execute([$id, $name, $trackid, $status]);


    header('Location:courses.php?request=All');

    $_SESSION['message'] = "Created Successfully";
} elseif ($page == "Edit") {
    $Errid = $Errname = $Errtrackid =  "";

    $courseId = $_GET['id'] ?? '';
    $statement = $connect->prepare('SELECT * FROM courses WHERE Course_Id = ?');
    $statement->execute([$courseId]);
    $result = $statement->fetch();
    if (!$result) {
        $_SESSION['message_error'] = "Course Not Found";
        header('Location:courses.php?request=All');
        exit;
    }


    $trackid = $_POST['trackid'] ?? $result['Track_Id'];

    $stmtTracks = $connect->prepare("SELECT Track_Id, Track_Name FROM tracks ORDER BY Track_Name ASC");
    $stmtTracks->execute();
    $tracks = $stmtTracks->fetchAll();



    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $old_id = $_POST['old_id'] ?? '';
        $id = $_POST['id'] ?? '';
        $name = $_POST['username'] ?? '';
        $trackid = $_POST['trackid'] ?? '';
        $status = $_POST['status'] ?? '';

        $statement = $connect->prepare("SELECT * FROM courses WHERE Course_Id = ? AND Course_Id != ?");
        $statement->execute(array($id, $old_id));
        $checkId = $statement->rowCount();



        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($name)) {
            $Errname = "Please Enter Name";
        }
        if (empty($trackid)) {
            $Errtrackid = "Please Select Track";
        } else {
            $checkTrack = $connect->prepare("SELECT * FROM tracks WHERE Track_Id = ?");
            $checkTrack->execute([$trackid]);
            if ($checkTrack->rowCount() == 0) {
                $Errtrackid = "Selected Track Not Found";
            }
        }


        if (empty($Errid) && empty($Errname) && empty($Errtrackid)) {

            if ($checkId == 0) {
                $_SESSION['old_id'] = $old_id;
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['trackid'] = $trackid;
                $_SESSION['status'] = $status;



                header('Location:courses.php?request=SaveUpdate');
            } else {
                $_SESSION['message_error'] = "Duplicated ID Please Enter Another One";
            }
        }
    }

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

                <form action="courses.php?request=Edit&id=<?php echo $result['Course_Id'] ?>" method="post">
                    <input type="hidden" name="old_id" value="<?php echo $result['Course_Id'] ?>">

                    <label>ID</label>
                    <input type="number" name="id" class="form-control" value="<?php echo $result['Course_Id'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errid . "</h6>" ?>

                    <label>Name</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $result['Course_Name'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errname . "</h6>" ?>

                    <label>Track <span class="text-danger">*</span></label>
                    <select name="trackid" class="form-control">
                        <option value="">Select Track</option>
                        <?php foreach ($tracks as $t) { ?>
                            <option value="<?= $t['Track_Id'] ?>" <?= ($trackid == $t['Track_Id'] ? "selected" : "") ?>>
                                <?= $t['Track_Name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                    <h6 class='text-danger'><?php echo $Errtrackid ?></h6>


                    <label>Status</label>
                    <select name="status" class="form-control">
                        <?php
                        if ($result['status'] == 0) {
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
    $trackid = $_SESSION['trackid'];
    $status = $_SESSION['status'];


    $statement = $connect->prepare("UPDATE courses SET 
 Course_Id = ?, 
 Course_Name = ?, 
 Track_Id = ?, 
 `Status` = ?, 
 Updated_At = now()
 WHERE Course_Id = ?");
    $statement->execute(array($id, $name, $trackid, $status, $old_id));

    header('Location:courses.php?request=All');

    $_SESSION['message'] = "Updated Successfully";
}






?>




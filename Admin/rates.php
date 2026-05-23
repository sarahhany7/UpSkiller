<?php
session_start();
include('init.php');
/** @var PDO $connect */


if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

$statement = $connect->prepare("
    SELECT rates.*, users.User_Name, courses.Course_Name 
    FROM rates
    INNER JOIN users ON rates.User_Id = users.User_Id
    INNER JOIN courses ON rates.Course_Id = courses.Course_Id
");
$statement->execute();
$rateCount = $statement->rowCount();
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
                    header("Refresh:3;url=rates.php");
                }
                ?>
                <h2>Rates <span class="badge badge-primary"><?php echo $rateCount ?> </span> <a class="btn btn-success" href="rates.php?request=Create">Create New Rate</a></h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Rate</th>
                        <th>Course Name</th>
                        <th>Operations</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result as $item) {
                        ?>
                            <tr>
                                <td><?php echo $item['Rate_Id'] ?></td>
                                <td><?php echo $item['User_Name'] ?></td>
                                <td><?php echo $item['Rate'] ?></td>
                                <td><?php echo $item['Course_Name'] ?></td>
                                <td>
                                    <a class="btn btn-success mb-1" href="rates.php?request=Show&id=<?php echo $item['Rate_Id'] ?>"><i class="fa-solid fa-eye fa-lg"></i></a>
                                    <a class="btn btn-primary mb-1" href="rates.php?request=Edit&id=<?php echo $item['Rate_Id'] ?>"><i class="fa-solid fa-pen-to-square fa-lg"></i></a>
                                    <a class="btn btn-danger mb-1" href="rates.php?request=Delete&id=<?php echo $item['Rate_Id'] ?>"><i class="fa-solid fa-trash fa-lg"></i></a>
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
    $rateId = $_GET['id'];

    $statement = $connect->prepare("
    SELECT rates.*, users.User_Name, courses.Course_Name 
    FROM rates
    INNER JOIN users ON rates.User_Id = users.User_Id
    INNER JOIN courses ON rates.Course_Id = courses.Course_Id
    WHERE rates.Rate_Id = ?
 ");
    $statement->execute(array($rateId));
    $result = $statement->fetch();



    if (isset($_GET['deleteid'])) {
        $deleteUser = $_GET['deleteid'];

        $statement = $connect->prepare("DELETE FROM rates WHERE Rate_Id = ?");
        $statement->execute(array($deleteUser));
        header('Location:rates.php?request=All');
    }


 ?>
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-12 m-auto text-center">
                <h2>Rates Details
                    <a href="rates.php?request=All" class="btn btn-success"><i class="fa-solid fa-house fa-lg"></i></a>
                    <a href="rates.php?request=Show&deleteid=<?php echo $result['Rate_Id'] ?>" class="btn btn-danger"><i class="fa-solid fa-trash fa-lg"></i></a>
                </h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>User Name</th>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Rate</th>
                        <th>Created</th>
                        <th>Updated</th>

                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $result['Rate_Id'] ?></td>
                            <td><?php echo $result['User_Id'] ?></td>
                            <td><?php echo $result['User_Name'] ?></td>
                            <td><?php echo $result['Course_Id'] ?></td>
                            <td><?php echo $result['Course_Name'] ?></td>
                            <td><?php echo $result['Rate'] ?></td>
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
    $rateId = $_GET['id'];

    $statement = $connect->prepare("DELETE FROM rates WHERE Rate_Id = ?");
    $statement->execute(array($rateId));
    $_SESSION['message'] = "Deleted Successfully";
    header('Location:rates.php?request=All');
} elseif ($page == "Create") {

    $Errid = $Erruserid = $Errcourseid = $Errcoursename  = $Errrate = "";
    $id = $userid  = $courseid = $coursename = $rate = "";

    $stmtUsers = $connect->prepare("SELECT User_Id, User_Name FROM users ORDER BY User_Name ASC");
    $stmtUsers->execute();
    $users = $stmtUsers->fetchAll();

    $stmtCourses = $connect->prepare("SELECT Course_Id, Course_Name FROM courses ORDER BY Course_Name ASC");
    $stmtCourses->execute();
    $courses = $stmtCourses->fetchAll();


    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $id = $_POST['id'];
        $userid  = $_POST['userid'];
        $courseid = $_POST['courseid'];
        $rate = $_POST['rate'];

        $statement = $connect->prepare("SELECT * FROM rates WHERE Rate_Id = ?");
        $statement->execute(array($id));
        $checkId = $statement->rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($userid)) {
            $Erruserid = "Please Enter User ID";
        }
        if (!empty($userid)) {
            $checkuser = $connect->prepare("SELECT * FROM users WHERE User_Id = ?");
            $checkuser->execute([$userid]);
            $userExists = $checkuser->rowCount();

            if ($userExists == 0) {
                $Erruseid = "User ID Not Found in Users Table";
            }
        }
        if (empty($courseid)) {
            $Errcourseid = "Please Enter Course ID";
        }
        if (!empty($courseid)) {
            $checkcourse = $connect->prepare("SELECT * FROM courses WHERE Course_Id = ?");
            $checkcourse->execute([$courseid]);
            $courseExists = $checkcourse->rowCount();

            if ($courseExists == 0) {
                $Errcourseid = "Course ID Not Found in Courses Table";
            }
        }
        if ($rate == "") {
            $Errrate = "Please Enter Rate";
        }


        if (empty($Errid) && empty($Erruserid) && empty($Errcourseid) && empty($Errrate)) {

            if ($checkId == 0) {
                $_SESSION['id'] = $id;
                $_SESSION['userid'] = $userid;
                $_SESSION['courseid'] = $courseid;
                $_SESSION['rate'] = $rate;

                header('Location:rates.php?request=Store');
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
                <h2 class="text-center">Create New Rate</h2>
                <form action="rates.php?request=Create" method="post">
                    <label>ID <span class="text-danger">*</span></label>
                    <input type="number" name="id" class="form-control" value="<?php echo $id ?>">
                    <h6 class="text-danger"><?php echo $Errid ?></h6>

                    <label>User <span class="text-danger">*</span></label>
                    <select name="userid" class="form-control">
                        <option value="">Select User</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['User_Id'] ?>" <?= ($userid == $u['User_Id'] ? 'selected' : '') ?>>
                                <?= $u['User_Name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <h6 class="text-danger"><?= $Erruserid ?></h6>

                    <label>Course <span class="text-danger">*</span></label>
                    <select name="courseid" class="form-control">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['Course_Id'] ?>" <?= ($courseid == $c['Course_Id'] ? 'selected' : '') ?>>
                                <?= $c['Course_Name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <h6 class="text-danger"><?= $Errcourseid ?></h6>



                    <label>Rate <span class="text-danger">*</span></label>
                    <select class="form-control" name="rate">
                        <option value="">Select</option>
                        <option value="0" <?php echo ($rate == "0" ? "selected" : "") ?>>0</option>
                        <option value="1" <?php echo ($rate == "1" ? "selected" : "") ?>>1</option>
                        <option value="2" <?php echo ($rate == "2" ? "selected" : "") ?>>2</option>
                        <option value="3" <?php echo ($rate == "3" ? "selected" : "") ?>>3</option>
                        <option value="4" <?php echo ($rate == "4" ? "selected" : "") ?>>4</option>
                        <option value="5" <?php echo ($rate == "5" ? "selected" : "") ?>>5</option>
                    </select>

                    <h6 class="text-danger"><?php echo $Errrate ?></h6>


                    <br>
                    <button type="submit" class="btn-block btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

<?php
} elseif ($page == "Store") {

    $id = $_SESSION['id'];
    $userid = $_SESSION['userid'];
    $courseid = $_SESSION['courseid'];
    $rate = $_SESSION['rate'];

    $statement = $connect->prepare("INSERT INTO rates
    (Rate_Id,`User_Id`,Course_ID,Rate,Created_At)
    VALUES (?,?,?,?,now())
    ");
    $statement->execute(array($id, $userid, $courseid, $rate));
    header('Location:rates.php?request=All');

    $_SESSION['message'] = "Created Successfully";
} elseif ($page == "Edit") {
    $Errid = $Erruserid = $Errusername = $Errcourseid = $Errcoursename =  "";

    $stmtUsers = $connect->prepare("SELECT User_Id, User_Name FROM users ORDER BY User_Name ASC");
    $stmtUsers->execute();
    $users = $stmtUsers->fetchAll();

    $stmtCourses = $connect->prepare("SELECT Course_Id, Course_Name FROM courses ORDER BY Course_Name ASC");
    $stmtCourses->execute();
    $courses = $stmtCourses->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $old_id = $_POST['old_id'];
        $id = $_POST['id'];
        $userid  = $_POST['userid'];
        $courseid = $_POST['courseid'];
        $rate = $_POST['rate'];

        $statement = $connect->prepare("SELECT * FROM rates WHERE Rate_Id = ? AND Rate_Id != ?");
        $statement->execute(array($id, $old_id));
        $checkId = $statement->rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($userid)) {
            $Erruserid = "Please Enter User ID";
        }
        if (!empty($userid)) {
            $checkuser = $connect->prepare("SELECT * FROM users WHERE User_Id = ?");
            $checkuser->execute([$userid]);
            $userExists = $checkuser->rowCount();

            if ($userExists == 0) {
                $Erruseid = "User ID Not Found in Users Table";
            }
        }
        if (empty($courseid)) {
            $Errcourseid = "Please Enter Course ID";
        }
        if (!empty($courseid)) {
            $checkcourse = $connect->prepare("SELECT * FROM courses WHERE Course_Id = ?");
            $checkcourse->execute([$courseid]);
            $courseExists = $checkcourse->rowCount();

            if ($courseExists == 0) {
                $Errcourseid = "Course ID Not Found in Courses Table";
            }
        }


        if (empty($Errid) && empty($Erruserid)  && empty($Errcourseid)) {

            if ($checkId == 0) {
                $_SESSION['old_id'] = $old_id;
                $_SESSION['id'] = $id;
                $_SESSION['userid'] = $userid;
                $_SESSION['courseid'] = $courseid;
                $_SESSION['rate'] = $rate;

                header('Location:rates.php?request=SaveUpdate');
            } else {
                $_SESSION['message_error'] = "Duplicated ID Please Enter Another One";
            }
        }
    }



    $rateId = $_GET['id'];

    $statement = $connect->prepare('SELECT * FROM rates WHERE Rate_Id = ?');
    $statement->execute(array($rateId));
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

                <form action="rates.php?request=Edit&id=<?php echo $result['Rate_Id'] ?>" method="post">
                    <input type="hidden" name="old_id" value="<?php echo $result['Rate_Id'] ?>">

                    <label>ID</label>
                    <input type="number" name="id" class="form-control" value="<?php echo $result['Rate_Id'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errid . "</h6>" ?>

                    <label>User <span class="text-danger">*</span></label>
                    <select name="userid" class="form-control">
                        <option value="">Select User</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['User_Id'] ?>" <?= ($result['User_Id'] == $u['User_Id'] ? 'selected' : '') ?>>
                                <?= $u['User_Name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Course <span class="text-danger">*</span></label>
                    <select name="courseid" class="form-control">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['Course_Id'] ?>" <?= ($result['Course_Id'] == $c['Course_Id'] ? 'selected' : '') ?>>
                                <?= $c['Course_Name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>


                    <label>Rate</label>
                    <select name="rate" class="form-control">
                        <?php
                        if ($result['rate'] == "0") {
                            echo "<option value = '0' selected >0</option>";
                            echo "<option value = '1' >1</option>";
                            echo "<option value = '2' >2</option>";
                            echo "<option value = '3' >3</option>";
                            echo "<option value = '4' >4</option>";
                            echo "<option value = '5' >5</option>";
                        } elseif ($result['rate'] == "1") {
                            echo "<option value = '0' >0</option>";
                            echo "<option value = '1' selected>1</option>";
                            echo "<option value = '2' >2</option>";
                            echo "<option value = '3' >3</option>";
                            echo "<option value = '4' >4</option>";
                            echo "<option value = '5' >5</option>";
                        } elseif ($result['rate'] == "2") {
                            echo "<option value = '0' >0</option>";
                            echo "<option value = '1' >1</option>";
                            echo "<option value = '2'selected >2</option>";
                            echo "<option value = '3' >3</option>";
                            echo "<option value = '4' >4</option>";
                            echo "<option value = '5' >5</option>";
                        } elseif ($result['rate'] == "3") {
                            echo "<option value = '0' >0</option>";
                            echo "<option value = '1' >1</option>";
                            echo "<option value = '2' >2</option>";
                            echo "<option value = '3'selected >3</option>";
                            echo "<option value = '4' >4</option>";
                            echo "<option value = '5' >5</option>";
                        } elseif ($result['rate'] == "4") {
                            echo "<option value = '0' >0</option>";
                            echo "<option value = '1' >1</option>";
                            echo "<option value = '2' >2</option>";
                            echo "<option value = '3' >3</option>";
                            echo "<option value = '4'selected >4</option>";
                            echo "<option value = '5' >5</option>";
                        } else {
                            echo "<option value = '0' >0</option>";
                            echo "<option value = '1' >1</option>";
                            echo "<option value = '2' >2</option>";
                            echo "<option value = '3' >3</option>";
                            echo "<option value = '4' >4</option>";
                            echo "<option value = '5' selected>5</option>";
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
    $userid = $_SESSION['userid'];
    $courseid = $_SESSION['courseid'];
    $rate = $_SESSION['rate'];


    $statementUser = $connect->prepare("SELECT User_Name FROM users WHERE User_Id = ?");
    $statementUser->execute([$userid]);
    $username = $statementUser->fetchColumn();

    $statementCourse = $connect->prepare("SELECT Course_Name FROM courses WHERE Course_Id = ?");
    $statementCourse->execute([$courseid]);
    $coursename = $statementCourse->fetchColumn();


    $statement = $connect->prepare("UPDATE rates SET 
    Rate_Id = ?,
    `User_Id` = ?,
    Course_Id = ?,
    Rate = ?,
    Updated_At = NOW()
    WHERE Rate_Id = ?");
    $statement->execute([$id, $userid, $courseid, $rate, $old_id]);

    header('Location:rates.php?request=All');

    $_SESSION['message'] = "Updated Successfully";
}






?>









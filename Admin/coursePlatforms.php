<?php
session_start();
include('init.php');
/** @var PDO $connect */


if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

$statement = $connect->prepare("
    SELECT cp.*, c.Course_Name, p.Platform_Name
    FROM courseplatforms cp
    INNER JOIN courses c ON cp.course_id = c.Course_Id
    INNER JOIN platforms p ON cp.platform_id = p.Platform_Id
");
$statement->execute();
$courseplatformCount = $statement->rowCount();
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
                    header("Refresh:3;url=coursePlatforms.php");
                }
                ?>
                <h2>Course Platforms <span class="badge badge-primary"><?php echo $courseplatformCount ?> </span> <a class="btn btn-success" href="coursePlatforms.php?request=Create">Create New Course Platform</a></h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Course</th>
                        <th>Platform</th>
                        <th>URL</th>
                        <th>Operations</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result as $item) {
                        ?>
                            <tr>
                                <td><?php echo $item['CoursePlatform_Id'] ?></td>
                                <td><?php echo $item['Course_Name'] ?></td>
                                <td><?php echo $item['Platform_Name'] ?></td>
                                <td><?php echo $item['Url'] ?></td>
                                <td>
                                    <a class="btn btn-success" href="coursePlatforms.php?request=Show&id=<?php echo $item['CoursePlatform_Id'] ?>"><i class="fa-solid fa-eye fa-lg"></i></a>
                                    <a class="btn btn-primary" href="coursePlatforms.php?request=Edit&id=<?php echo $item['CoursePlatform_Id'] ?>"><i class="fa-solid fa-pen-to-square fa-lg"></i></a>
                                    <a class="btn btn-danger mt-1" href="coursePlatforms.php?request=Delete&id=<?php echo $item['CoursePlatform_Id'] ?>"><i class="fa-solid fa-trash fa-lg"></i></a>
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
    $courseplatformId = $_GET['id'];

    $statement = $connect->prepare("
    SELECT cp.*, c.Course_Name, p.Platform_Name
    FROM courseplatforms cp
    INNER JOIN courses c ON cp.course_id = c.Course_Id
    INNER JOIN platforms p ON cp.platform_id = p.Platform_Id
    WHERE cp.CoursePlatform_Id = ?
 ");
    $statement->execute(array($courseplatformId));
    $result = $statement->fetch();


    if (isset($_GET['deleteid'])) {
        $deleteUser = $_GET['deleteid'];

        $statement = $connect->prepare("DELETE FROM courseplatforms WHERE CoursePlatform_Id = ?");
        $statement->execute(array($deleteUser));
        header('Location:coursePlatforms.php?request=All');
    }


 ?>
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-12 m-auto text-center">
                <h2>Course Platform Details
                    <a href="coursePlatforms.php?request=All" class="btn btn-success"><i class="fa-solid fa-house fa-lg"></i></a>
                    <a href="coursePlatforms.php?request=Show&deleteid=<?php echo $result['CoursePlatform_Id'] ?>" class="btn btn-danger"><i class="fa-solid fa-trash fa-lg"></i></a>
                </h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Course</th>
                        <th>Platform</th>
                        <th>URL</th>
                        <th>Created</th>
                        <th>Updated</th>

                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $result['CoursePlatform_Id'] ?></td>
                            <td><?php echo $result['Course_Name'] ?></td>
                            <td><?php echo $result['Platform_Name'] ?></td>
                            <td><?php echo $result['Url'] ?></td>
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
    $courseplatformId = $_GET['id'];

    $statement = $connect->prepare("DELETE FROM courseplatforms WHERE CoursePlatform_Id = ?");
    $statement->execute(array($courseplatformId));
    $_SESSION['message'] = "Deleted Successfully";
    header('Location:coursePlatforms.php?request=All');
} elseif ($page == "Create") {

    $Errid = $Errurl = $Errcourseid = $Errplatformid = "";
    $id = $url = $courseid = $platformid = "";

    $courses = $connect->query("SELECT * FROM courses")->fetchAll();
    $platforms = $connect->query("SELECT * FROM platforms")->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $id = $_POST['id'];
        $url = $_POST['url'];
        $courseid = $_POST['courseid'];
        $platformid = $_POST['platformid'];



        $statement = $connect->prepare("SELECT * FROM courseplatforms WHERE CoursePlatform_Id = ?");
        $statement->execute([$id]);
        $checkId = $statement->rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($url)) {
            $Errurl = "Please Enter URL";
        }
        if (empty($courseid)) {
            $Errcourseid = "Please Enter Course ID";
        }
        if (!empty($courseid)) {
            $checkcourse = $connect->prepare("SELECT * FROM courses WHERE Course_Id = ?");
            $checkcourse->execute([$courseid]);
            $courseExists = $checkcourse->rowCount();

            if ($courseExists == 0) {
                $Errcourseid = "Course ID Not Found in Course Links Table";
            }
        }
        if (empty($platformid)) {
            $Errplatformid = "Please Enter Platform ID";
        }
        if (!empty($platformid)) {
            $checkplatform = $connect->prepare("SELECT * FROM platforms WHERE Platform_Id = ?");
            $checkplatform->execute([$platformid]);
            $platformExists = $checkplatform->rowCount();

            if ($platformExists == 0) {
                $Errplatformid = "Platform ID Not Found in Course Links Table";
            }
        }

        if (empty($Errid) && empty($Errurl) && empty($Errcourseid) && empty($Errplatformid)) {

            if ($checkId == 0) {
                $_SESSION['id'] = $id;
                $_SESSION['url'] = $url;
                $_SESSION['courseid'] = $courseid;
                $_SESSION['platformid'] = $platformid;


                header('Location:coursePlatforms.php?request=Store');
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
                <h2 class="text-center">Create New Course Link</h2>
                <form action="coursePlatforms.php?request=Create" method="post">
                    <label>ID <span class="text-danger">*</span></label>
                    <input type="number" name="id" class="form-control" value="<?php echo $id ?>">
                    <h6 class="text-danger"><?php echo $Errid ?></h6>

                    <label>URL <span class="text-danger">*</span></label>
                    <input type="text" name="url" class="form-control" value="<?php echo $url ?>">
                    <h6 class="text-danger"><?php echo $Errurl ?></h6>

                    <label>Course</label>
                    <select name="courseid" class="form-control">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['Course_Id'] ?>"><?= $c['Course_Name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <h6 class="text-danger"><?= $Errcourseid ?></h6>

                    <label>Platform</label>
                    <select name="platformid" class="form-control">
                        <option value="">Select Platform</option>
                        <?php foreach ($platforms as $p): ?>
                            <option value="<?= $p['Platform_Id'] ?>"><?= $p['Platform_Name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <h6 class="text-danger"><?= $Errplatformid ?></h6>
                    <button type="submit" class="btn-block btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

<?php
} elseif ($page == "Store") {

    $id = $_SESSION['id'];
    $url = $_SESSION['url'];
    $courseid = $_SESSION['courseid'];
    $platformid = $_SESSION['platformid'];

    $statement = $connect->prepare("INSERT INTO courseplatforms
     (CoursePlatform_Id, Url, Course_Id, Platform_Id, Created_At)
     VALUES (?,?,?,?,now())");

    $statement->execute([$id, $url, $courseid, $platformid]);
    header('Location:coursePlatforms.php?request=All');

    $_SESSION['message'] = "Created Successfully";
} elseif ($page == "Edit") {
    $Errid = $Errurl = $Errcourseid = $Errplatformid = "";

    $courses = $connect->query("SELECT * FROM courses ORDER BY Course_Name ASC")->fetchAll();
    $platforms = $connect->query("SELECT * FROM platforms ORDER BY Platform_Name ASC")->fetchAll();


    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $old_id = $_POST['old_id'];
        $id = $_POST['id'];
        $url = $_POST['url'];
        $courseid = $_POST['courseid'];
        $platformid = $_POST['platformid'];


        $statement = $connect->prepare("SELECT * FROM courseplatforms WHERE CoursePlatform_Id = ? AND CoursePlatform_Id != ?");
        $statement->execute([$id, $old_id]);
        $checkId = $statement->rowCount();
        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($url)) {
            $Errurl = "Please Enter URL";
        }
        if (empty($courseid)) {
            $Errcourseid = "Please Enter Course ID";
        }
        if (!empty($courseid)) {
            $checkCourse = $connect->prepare("SELECT * FROM courses WHERE Course_Id = ?");
            $checkCourse->execute([$courseid]);
            $courseExists = $checkCourse->rowCount();

            if ($courseExists == 0) {
                $Errcourseid = "Course ID Not Found in Courses Table";
            }
        }
        if (empty($platformid)) {
            $Errplatformid = "Please Enter Platform ID";
        }
        if (!empty($platformid)) {
            $checkPlatform = $connect->prepare("SELECT * FROM platforms WHERE Platform_Id = ?");
            $checkPlatform->execute([$platformid]);
            $platformExists = $checkPlatform->rowCount();

            if ($platformExists == 0) {
                $Errplatformid = "PLatform ID Not Found in Platforms Table";
            }
        }
        if (empty($Errid) && empty($Errurl) && empty($Errcourseid) && empty($Errplatfromid)) {

            if ($checkId == 0) {
                $_SESSION['old_id'] = $old_id;
                $_SESSION['id'] = $id;
                $_SESSION['url'] = $url;
                $_SESSION['courseid'] = $courseid;
                $_SESSION['platformid'] = $platformid;


                header('Location:coursePlatforms.php?request=SaveUpdate');
            } else {
                $_SESSION['message_error'] = "Duplicated ID Please Enter Another One";
            }
        }
    }



    $courseplatformId = $_GET['id'];
    $statement = $connect->prepare("SELECT * FROM courseplatforms WHERE CoursePlatform_Id = ?");
    $statement->execute([$courseplatformId]);
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

                <form action="coursePlatforms.php?request=Edit&id=<?php echo $result['CoursePlatform_Id'] ?>" method="post">
                    <input type="hidden" name="old_id" value="<?php echo $result['CoursePlatform_Id'] ?>">

                    <label>ID</label>
                    <input type="number" name="id" class="form-control" value="<?php echo $result['CoursePlatform_Id'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errid . "</h6>" ?>


                    <label>URL</label>
                    <input type="text" name="url" class="form-control" value="<?php echo $result['Url'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errurl . "</h6>" ?>

                    <label>Course</label>
                    <select name="courseid" class="form-control">
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['Course_Id'] ?>" <?= $c['Course_Id'] == $result['Course_Id'] ? 'selected' : '' ?>>
                                <?= $c['Course_Name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <h6 class="text-danger"><?= $Errcourseid ?></h6>

                    <label>Platform</label>
                    <select name="platformid" class="form-control">
                        <?php foreach ($platforms as $p): ?>
                            <option value="<?= $p['Platform_Id'] ?>" <?= $p['Platform_Id'] == $result['Platform_Id'] ? 'selected' : '' ?>>
                                <?= $p['Platform_Name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <h6 class="text-danger"><?= $Errplatformid ?></h6>



                    <button type="submit" class="btn-block btn btn-primary mb-3 mt-3">Submit</button>
                </form>
            </div>
        </div>
    </div>

<?php
} elseif ($page == "SaveUpdate") {

    $old_id = $_SESSION['old_id'];
    $id = $_SESSION['id'];
    $url = $_SESSION['url'];
    $courseid = $_SESSION['courseid'];
    $platformid = $_SESSION['platformid'];


    $statement = $connect->prepare("UPDATE  courseplatforms SET 
    CoursePlatform_Id = ?,
    `Url` = ?,
    Course_Id = ?,
    Platform_Id = ?,
    Updated_AT = now()
    WHERE CoursePlatform_Id = ?
    ");

    $statement->execute(array($id, $url, $courseid, $platformid, $old_id));
    header('Location:coursePlatforms.php?request=All');

    $_SESSION['message'] = "Updated Successfully";
}






?>
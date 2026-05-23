<?php
session_start();
include('init.php');
/** @var PDO $connect */


if (!isset($_SESSION['Role']) || $_SESSION['Role'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

$statement = $connect->prepare("SELECT * FROM contactmessages");
$statement->execute();
$messageCount = $statement->rowCount();
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
                    header("Refresh:3;url=messages.php");
                }
                ?>
                <h2>Messages <span class="badge badge-primary"><?php echo $messageCount ?> </span> <a class="btn btn-success" href="messages.php?request=Create">Create New Message</a></h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Operations</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result as $item) {
                        ?>
                            <tr>
                                <td><?php echo $item['Id'] ?></td>
                                <td><?php echo $item['Name'] ?></td>
                                <td><?php echo $item['Subject'] ?></td>
                                <td>
                                    <a class="btn btn-success" href="messages.php?request=Show&id=<?php echo $item['Id'] ?>"><i class="fa-solid fa-eye fa-lg"></i></a>
                                    <a class="btn btn-primary" href="messages.php?request=Edit&id=<?php echo $item['Id'] ?>"><i class="fa-solid fa-pen-to-square fa-lg"></i></a>
                                    <a class="btn btn-danger" href="messages.php?request=Delete&id=<?php echo $item['Id'] ?>"><i class="fa-solid fa-trash fa-lg"></i></a>
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
    $messageId = $_GET['id'];

    $statement = $connect->prepare("SELECT * FROM contactmessages WHERE Id = ?");
    $statement->execute(array($messageId));
    $result = $statement->fetch();


    if (isset($_GET['deleteid'])) {
        $deleteUser = $_GET['deleteid'];

        $statement = $connect->prepare("DELETE FROM contactmessages WHERE Id = ?");
        $statement->execute(array($deleteUser));
        header('Location:messages.php?request=All');
    }


 ?>
    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-md-12 m-auto text-center">
                <h2>Track Details
                    <a href="messages.php?request=All" class="btn btn-success"><i class="fa-solid fa-house fa-lg"></i></a>
                    <a href="messages.php?request=Show&deleteid=<?php echo $result['Id'] ?>" class="btn btn-danger"><i class="fa-solid fa-trash fa-lg"></i></a>
                </h2>
                <table class="table table-dark">
                    <thead>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Created</th>

                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $result['Id'] ?></td>
                            <td><?php echo $result['Name'] ?></td>
                            <td><?php echo $result['Email'] ?></td>
                            <td><?php echo $result['Subject'] ?></td>
                            <td><?php echo $result['Message'] ?></td>
                            <td><?php echo $result['Created_At'] ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

<?php

} elseif ($page == "Delete") {
    $messageId = $_GET['id'];

    $statement = $connect->prepare("DELETE FROM contactmessages WHERE Id = ?");
    $statement->execute(array($messageId));
    $_SESSION['message'] = "Deleted Successfully";
    header('Location:messages.php?request=All');
} elseif ($page == "Create") {

    $Errid = $Errname = $Erremail= $Errsubject = $Errmessage = "";
    $id = $name = $email= $subject = $message = "";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $id = $_POST['id'];
        $name = $_POST['username'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        $statement = $connect->prepare("SELECT * FROM contactmessages WHERE Id = ?");
        $statement->execute(array($id));
        $checkId = $statement->rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($name)) {
            $Errname = "Please Enter Name";
        }
        if (empty($email)) {
            $Erremail = "Please Enter Email";
        }
        if (empty($subject)) {
            $Errsubject = "Please Enter Subject";
        }
        if (empty($message)) {
            $Errmessage = "Please Enter Message";
        }

        if (empty($Errid) && empty($Errname) && empty($Erremail) && empty($Errsubject) && empty($Errmessage)) {

            if ($checkId == 0) {
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['subject'] = $subject;
                $_SESSION['message'] = $message;

                header('Location:messages.php?request=Store');
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
                <h2 class="text-center">Create New Message</h2>
                <form action="messages.php?request=Create" method="post">
                    <label>ID <span class="text-danger">*</span></label>
                    <input type="number" name="id" class="form-control" value="<?php echo $id ?>">
                    <h6 class="text-danger"><?php echo $Errid ?></h6>

                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" value="<?php echo $name ?>">
                    <h6 class="text-danger"><?php echo $Errname ?></h6>

                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?php echo $email ?>">
                    <h6 class="text-danger"><?php echo $Erremail?></h6> 

                    <label>Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control" value="<?php echo $subject ?>">
                    <h6 class="text-danger"><?php echo $Errsubject?></h6>

                    <label>Message <span class="text-danger">*</span></label>
                    <input type="text" name="message" class="form-control" value="<?php echo $message ?>">
                    <h6 class="text-danger"><?php echo $Errmessage?></h6>

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
    $email = $_SESSION['email'];
    $subject = $_SESSION['subject'];
    $message = $_SESSION['message'];

    $statement = $connect->prepare("INSERT INTO contactmessages
    (Id,Name,Email,Subject,Message,Created_At)
    VALUES (?,?,?,?,?,now())
    ");
    $statement->execute(array($id, $name, $email, $subject, $message));
    header('Location:messages.php?request=All');

    $_SESSION['message'] = "Created Successfully"; 

} elseif ($page == "Edit") {
    $Errid = $Errname = $Erremail = $Errsubject = $Errmessage =  "";
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $old_id = $_POST['old_id'];
        $id = $_POST['id'];
        $name = $_POST['username'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        $statement = $connect->prepare("SELECT * FROM contactmessages WHERE Id = ? AND Id != ?");
        $statement->execute(array($id, $old_id));
        $checkId = $statement->rowCount();

        if (empty($id)) {
            $Errid = "Please Enter ID";
        }
        if (empty($name)) {
            $Errname = "Please Enter Name";
        }
        if (empty($email)) {
            $Erremail = "Please Enter Email";
        }
        if (empty($subject)) {
            $Errsubject = "Please Enter Subject";
        }
        if (empty($message)) {
            $Errmessage= "Please Enter Message";
        }
        if (empty($Errid) && empty($Errname) && empty($Erremail) && empty($Errsubject) && empty($Errmessage)) {

            if ($checkId == 0) {
                $_SESSION['old_id'] = $old_id;
                $_SESSION['id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['subject'] = $subject;
                $_SESSION['message'] = $message;

                header('Location:messages.php?request=SaveUpdate');
            } else {
                $_SESSION['message_error'] = "Duplicated ID Please Enter Another One";
            }
        }
    }



    $messageId = $_GET['id'];

    $statement = $connect->prepare('SELECT * FROM contactmessages WHERE Id = ?');
    $statement->execute(array($messageId));
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

                <form action="messages.php?request=Edit&id=<?php echo $result['Id'] ?>" method="post">
                    <input type="hidden" name="old_id" value="<?php echo $result['Id'] ?>">

                    <label>ID</label>
                    <input type="number" name="id" class="form-control" value="<?php echo $result['Id'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errid . "</h6>" ?>

                    <label>Name</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $result['Name'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errname . "</h6>" ?>

                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $result['Email'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Erremail. "</h6>" ?>

                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control" value="<?php echo $result['Subject'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errsubject. "</h6>" ?>

                    <label>Message</label>
                    <input type="text" name="message" class="form-control" value="<?php echo $result['Message'] ?>">
                    <?php echo "<h6 class='text-danger'>" . $Errmessage. "</h6>" ?>



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
    $email = $_SESSION['email'];
    $subject = $_SESSION['subject'];
    $message = $_SESSION['message'];

    $statement = $connect->prepare("UPDATE  contactmessages SET 
    Id = ?,
    Name = ?,
    Email = ?,
    Subject = ?,
    Message = ?
    WHERE Id = ?
    ");

    $statement->execute(array($id, $name, $email, $subject ,$message , $old_id));
    header('Location:messages.php?request=All');

    $_SESSION['message'] = "Updated Successfully";
}






?>
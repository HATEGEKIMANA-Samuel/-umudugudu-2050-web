<?php
session_start();
require_once("../web-config/config.php");
require_once("../web-config/database.php");
if (isset($_SESSION["id"]) && isset($_SESSION["username"])  && isset($_SESSION["level"])) {
    //updating last login
    $sql = "SELECT id, username, level FROM user WHERE username ='{$_SESSION["username"]}' AND id='{$_SESSION["id"]}' AND active='1' LIMIT 1";
    $query = $database->query($sql);
    $n = $database->num_rows($query);
    if ($n > 0) {
        $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
        $sql = "UPDATE user SET ip='$ip', lastlogin=now() WHERE username='{$_SESSION['username']}' LIMIT 1";
        $query = $database->query($sql);
        header("location:home");
    }
}
?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="">
<!--<![endif]-->

<head>
    <?php require_once("includes/head.php"); ?>
    <style type="text/css">
        .srslogo {
            width: 30%;
        }
    </style>
</head>

<body class="" style="background-color: #f1f1f1">


    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-logo">
                    <!--<a href="index.php">
                        <img class="align-content" src="images/logo.png" alt="">
                    </a>-->
                    <h2 style='color:white;margin-top:100px;'></h2>
                </div>
                <div class="login-form">


                    <div class="row">
                        <div class="col-lg-offset-4 bg-white box-shadow p-20 col-lg-4">
                            <form>
                                <div class="col-lg-12 mb-10 pt-30 text-center">
                                    <img src="./images/srs.PNG" width="180" class="srslogo">
                                    <hr class="mt-20 mb-20">
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="fs-14">Username</label>
                                        <input type="text" id="username" class="form-control" placeholder="Username">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="fs-14">Password</label>
                                        <input type="password" id="password" class="form-control" placeholder="Password">
                                    </div>
                                </div>


                                <div class="col-lg-12 mt-10 pb-30">
                                    <hr class="mb-20">
                                    <button type="button" id="loginbtn" onclick="log()" class="btn w-100p fs-14 p-10 btn-success btn-flat">Sign in</button>
                                    <span class="pt-10 text-center fs-14" id="status"></span>
                                </div>
                            </form>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="js/ajax.js"></script>
    <script src="js/login.js"></script>
</body>

</html>
<?php
    require_once("includes/validate_credentials.php");

$sql = "SELECT * FROM user WHERE status='1' AND username ='{$_SESSION["username"]}' AND id='{$_SESSION["id"]}' LIMIT 1";
$query = $database->query($sql);
$user=$database->fetch_array($query);


?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <?php require_once("includes/head.php"); ?>
</head>
<body>
<!-- Left Panel -->
<?php require_once 'includes/left_nav.php'; ?>
<!-- Left Panel -->

<!-- Right Panel -->

<div id="right-panel" class="right-panel">

    <!-- Header-->
    <?php require_once 'includes/top_nav.php'; ?>
    <!-- Header-->

<!--     <div class="breadcrumbs">
        <div class="col-sm-4">
            <div class="page-header float-left">
                <div class="page-title">
                    <h1>Dashboard</h1>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="page-header float-right">
                <div class="page-title">
                    <ol class="breadcrumb text-right">
                        <li><a href="home">Dashboard</a></li>
                        <li class="active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div> -->

    <div class="content mt-3">

        <div class="animated fadeIn">


            <div class="row m-20">
                <div class="col-lg-offset-1 mt-30 col-lg-10">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">Update Your Profile</strong>
                        </div>

                        <div class="card-body">
                            <div id="register_div">
                                <div class="card-body">

                                    <form id="update_profile"   onsubmit="return false" method="post" novalidate="novalidate" enctype="multipart/form-data">


                                                <div class="row form-group">
                                                    <div class=" col-md-3">
                                                        <label class="form-label">First Name<span class="required-mark">*</span></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" name="firstname" minlength="2" autocomplete="off" required id="firstname" value="<?php echo $user["fname"]; ?>">

                                                    </div>
                                                </div>

                                                    <div class=" row form-group">
                                                        <div class=" col-md-3">
                                                            <label class="form-label">Middle name</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="text" class="form-control" name="middlename" minlength="2" autocomplete="off" id="middlename" value="<?php echo $user["mname"]; ?>">

                                                        </div>
                                                    </div>

                                                <div class="row form-group">
                                                    <div class=" col-md-3">
                                                        <label class="form-label">Last Name<span class="required-mark">*</span></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" name="lastname" required minlength="2" id="lastname" value="<?php echo $user["lname"]; ?>">

                                                    </div>


                                                </div>
                                                <div class="row form-group">
                                                    <div class=" col-md-3">
                                                        <label class="form-label">Email<span class="required-mark">*</span></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="email" class="form-control" required  name="email" id="email" value="<?php echo $user["email"]; ?>"  >
                                                    </div>
                                                </div>

                                                <div class="row form-group">
                                                    <div class=" col-md-3">
                                                        <label class="form-label">Username<span class="required-mark">*</span></label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" class="form-control" autocomplete="off" required minlength="4"   name="username"  id="username" value="<?php echo $user["username"]; ?>" onblur="check_username_update(<?php echo $_SESSION["id"]; ?>)">
                                                        <span  id="username_status"></span>
                                                    </div>
                                                    <input type="hidden" id="hash" name="hash" value="<?php echo $_SESSION['id']; ?>">
                                                </div>
                                                <hr class="mt-20 mb-20">
                                                <div>
                                                    <button id="updatebtn" name="update" type="submit" class="btn btn-md btn-info float-right ml-10 w-100 fs-13" onclick="update_profile()">Update</button>
                                                  <a href="profile" id="cancelbtn" name="cancel" type="submit" class="btn btn-md float-right ml-10 w-100 fs-13 btn-danger">Cancel</a>
                                                </div>

                                    </form>
                                </div>
                            </div>

                        </div>
                    </div> <!-- .card -->

                </div><!--/.col-->



            </div>


        </div><!-- .animated -->
    </div><!-- .content -->


</div><!-- /#right-panel -->

<!-- Right Panel -->


<script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/vendor/jquery-1.11.3.min.js"></script>
<script src="assets/js/jquery.validate.js"></script>
<script src="js/ajax.js"></script>
<script src="js/user.js"></script>
<script src="assets/js/sweetalert.min.js"></script>


</body>
</html>

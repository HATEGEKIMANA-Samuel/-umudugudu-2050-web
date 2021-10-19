<?php require_once("includes/validate_credentials.php"); ?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
<?php require_once("includes/head.php"); ?>
    <style type="text/css">

        .form-control {
            border-color: #eee;
            min-height: 41px;
            box-shadow: none !important;
        }
        .form-control:focus {
            border-color: #5cd3b4;
        }
        .form-control, .btn {
            border-radius: 3px;
        }
        .register-form {
            width: 70%;
            margin: 0 auto;
            padding: 30px 0;
        }
        .register-form .form-header {
            background: #435d7d;
            border-bottom: none;
            position: relative;
            text-align: center;
            font-size: 36px;
            margin: -20px -20px 15px;
            border-radius: 5px 5px 5px 5px;
            padding: 10px;
            color: #fff;
        }
        .seePassword{
            position: absolute;
            top: 40px;
            right: 30px;
            z-index: 1000;
            cursor: pointer;
        }
        .register-form h3{
            font-family: Roboto;
            font-style: italic;
        }
        .register-form form {
            color: #999;
            border-radius: 3px;
            margin-bottom: 15px;
            background: #fff;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            padding: 30px;
        }
        .register-form .form-group {
            margin-bottom: 20px;
        }
        .register-form label {
            font-weight: normal;
            font-size: 13px;
        }
        .register-form input[type="checkbox"] {
            margin-top: 2px;
        }
        .register-form .btn {
            font-size: 16px;
            font-weight: bold;
            background: #435d7d;
            border: none;
            margin-top: 20px;
            min-width: 140px;
        }
        .register-form .btn:hover, .register-form .btn:focus {
            background: #41cba9;
            outline: none !important;
        }

    </style>
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

        <!-- <div class="breadcrumbs">
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
                            <li><a href="users">Users</a></li>
                            <li class="active">Update</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div> -->
        <?php
        if (isset($_GET['id'])){
        $id = encrypt_decrypt('decrypt',$_GET['id']);
        $stmt = $database->query("SELECT *  FROM user WHERE id = '$id' AND status='1'");
        $row = $database->fetch_array($stmt);
        ?>
        <div class="content mt-3">

            <div class="animated fadeIn">

                <div class="register-form">
                    <form id="updatefrm" class="form-horizontal"   onsubmit="return false" method="post" novalidate="novalidate" enctype="multipart/form-data">
                        <div class="form-header">
                            <h3>Update user</h3>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mt-10">
                                <div class="">
                                    <label class="form-label">First Name<span class="required-mark">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter First name" autocomplete="off" minlength="2" name="firstname" id="firstname"  required value="<?php echo $row['fname']; ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mt-10">
                                <div class="">
                                    <label class="form-label">Middle name</label>
                                    <input type="text" class="form-control" placeholder="Enter Middle name" name="middlename" autocomplete="off" id="middlename"   value="<?php echo $row['mname']; ?>">
                                </div>
    
                            </div>
                            <div class="col-md-6 mt-10">
                                <label class="form-label">Last Name<span class="required-mark">*</span></label>
                                <input type="text" class="form-control" name="lastname" placeholder="Enter Last name" minlength="2" autocomplete="off" id="lastname" required value="<?php echo $row['lname']; ?>">
                            </div>
                            
                            <div class="col col-md-6 mt-10">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="Enter email"  name="email" id="email" autocomplete="off"   value="<?php echo $row['email']; ?>">
                            </div>
                            <div class="col col-md-6 mt-10">
                                <label class="form-label">Access Level<span class="required-mark">*</span></label>
                                <select class="form-control" id="level" name="level" required>
                                    <option value="">Choose Level</option>
                                    <?php
                                    $query= "SELECT * FROM `level`";
                                    $result1 = $database->query($query);
                                    while ($level=$database->fetch_array($result1)) {
                                        if($level["id"]==$row['level']){ ?>
                                            <option selected value="<?php echo $level["id"]; ?>"><?php echo $level["name"]; ?></option>
                                        <?php }
                                        else{
                                            ?>
                                            <option value="<?php echo $level["id"]; ?>"><?php echo $level["name"]; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col col-md-6 mt-10">
                                <label class="form-label">Institution<span class="required-mark">*</span></label>
                                <select class="form-control show-tick" id="institution" name="institution" required>
                                    <option value="">Choose Institution</option>
                                    <?php


                                    $q= "SELECT * FROM institutions ";
                                    $rlt = $database->query($q);
                                    while ($inst=$database->fetch_array($rlt)) {
                                        if($inst["id"]==$row['id_institution']){ ?>
                                            <option selected value="<?php echo $inst["id"]; ?>"><?php echo $inst["name"]; ?></option>
                                        <?php }
                                        else{
                                            ?>
                                            <option value="<?php echo $inst["id"]; ?>"><?php echo $inst["name"]; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col col-md-12 mt-10">
                                <label class="form-label">Username<span class="required-mark">*</span></label>
                                <input type="text" class="form-control" minlength="4"  name="username"  placeholder="Enter username" autocomplete="off" id="username" onblur="check_username_update(<?php echo $id; ?>)"  value="<?php echo $row['username']; ?>">
                                <span  id="username_status"></span>
                            </div>
                            <div class="col-md-12 mt-10">
                            <span class="float-left">
                                
                                <button type="submit" class="btn btn-primary btn-lg border-radius-0" id="updatebtn"  onclick="editUser(<?php echo $id; ?>)" data-id="<?php echo $id; ?>">Update</button>
                                <a class="btn btn-lg border-radius-0" style="color: white; background: #FE5F55" href="users" >Cancel</a>
                                
                            </span>
                            <span class="float-right">
                                <a class="btn btn-lg border-radius-0" style="color: white; background: #6a005b" href="users" data-toggle="modal" data-target="#editUserPasswordModal" >Edit Password</a>
                            </span>
                        </div>
                            
                        </div>
                        

                    
                            
                        <!--<div class="row form-group">-->
                        <!--    <div class="col col-md-3">-->
                        <!--        <label class="form-label">Password</label>-->
                        <!--    </div>-->
                        <!--    <div class="col-12 col-md-9">-->
                        <!--        <input type="text" class="form-control"  minlength="4" placeholder="Enter password" autocomplete="off" name="password"  id="password"   value="">-->

                        <!--    </div>-->
                        <!--</div>-->
                        




                    </form>
                    

                </div>



            </div><!-- .animated -->
        </div><!-- .content -->
<?php } ?>

    </div><!-- /#right-panel -->

    <!-- Right Panel -->
    
    
    <!-- Modal -->
  <div class="modal fade" id="editUserPasswordModal" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-body">
            <form id="changePasswordForm">
                <div class="row p-20">
                    <div class="col-md-12 resp"></div>
                    <div class="col-md-12 position-relative">
                        <label for="p2 fs-13">New Password</label>
                        <input type="password" class="form-control" name ="password_check" id="password">
                        <i class="fa fa-eye seePassword"></i>
                    </div>
                    <div class="col-md-12 mt-10 position-relative">
                        <label for="p2 fs-13 mt-10">Confirm Password</label>
                        <input type="password" class="form-control" name ="p_check" id="p_check">
                        <i class="fa fa-eye seePassword"></i>
                    </div>
                    <div class="col-md-12 mt-20">
                        <button type="submit" class="btn btn-primary border-radius-0 p-10">UPDATE</button>
                        <button type="button" class="btn btn-danger p-10" data-dismiss="modal">CANCEL</button>
                    </div>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>


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
    <script src="assets/js/main.js"></script>
    </body>
</html>

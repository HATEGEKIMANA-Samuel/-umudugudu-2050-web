<?php require_once("includes/validate_credentials.php");
$rlt = $database->query("SELECT id,name FROM `institutions`");

?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
<?php require_once("includes/head.php"); ?>
    <link rel="stylesheet" href="assets/css/font-roboto-varela.css">
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
            /*font-size: 13px;*/
        }
        .register-form input[type="checkbox"] {
            margin-top: 2px;
        }
        .register-form .btn {
            /*font-size: 16px;*/
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

        <div class="content mt-3">

            <div class="animated fadeIn">


                <div class="register-form">
                    <form id="registerfrm" class="form-horizontal"   onsubmit="return false" method="post" novalidate="novalidate" enctype="multipart/form-data">
                         <div class="form-header">
                             <h3>Register New User</h3>
                         </div>


                        <div class="row form-group">
                            <div class="col col-md-3">
                            <label class="form-label">First Name<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <input type="text" class="form-control" placeholder="Enter First name" name="firstname" autocomplete="off" minlength="2" id="firstname" onblur="check_firstname()"  required >
                            <label class="error" id="firstname_status"></label>
                            </div>
                        </div>
                       
                        <div class="row form-group">
                            <div class="col col-md-3">
                            <label class="form-label">Last Name<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <input type="text" class="form-control" name="lastname" placeholder="Enter Last name" autocomplete="off" minlength="2" id="lastname" onblur="check_lastname()" required >
                            <label class="error" id="lastname_status"></label>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                            <label class="form-label">Middle name</label>
                            </div>
                            <div class="col-12 col-md-9">
                            <input type="text" class="form-control" placeholder="Enter Middle name" minlength="2" autocomplete="off" name="middlename" id="middlename">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3">
                            <label class="form-label">Email</label>
                            </div>
                            <div class="col-12 col-md-9">
                            <input type="text" class="form-control" placeholder="Enter a username" autocomplete="off"  name="email" id="email" >
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                            <label class="form-label">Access Level<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <select class="form-control" id="level" name="level" required>
                                <option value="">Choose Level</option>
                                <?php
                                $query= "SELECT * FROM `level`";
                                $result1 = $database->query($query);
                                while ($row=mysqli_fetch_assoc($result1)) {?>
                                <option value="<?php echo $row["id"];?>"><?php echo $row["name"];}?></option>
                            </select>
                            </div>
                        </div>
                        <div class="row form-group ">
                            <div class="col col-md-3">
                            <label class="form-label">Institution<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <select class="form-control show-tick" id="institution" name="institution" required>
                                <option value="">Choose Institution</option>
                                <?php while ($row=$database->fetch_array($rlt)) {?>
                                <option value="<?php echo $row["id"];?>"><?php echo $row["name"]; ?></option>
                                <?php } ?>
                            </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                            <label class="form-label">Username<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <input type="text" class="form-control"  name="username" placeholder="Enter username" minlength="3" autocomplete="off" id="username" onblur="check_username()" required >
                            <label class="error"  id="username_status"></label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                            <label class="form-label">Password<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <input type="text" class="form-control" placeholder="Enter password" name="password"  id="password"  autocomplete="off" minlength="4" onblur="check_password()" required >
                            <label class="error" id="password_status"></label>
                            </div>
                        </div>
                       <!-- user location  -->
                       <h3 style='text-align: center;margin-bottom:10px;color:#435d7d;' id="location-text">User location</h3>
                       <div class="row form-group" id="province-group">
                            <div class="col col-md-3">
                            <label class="form-label">Province<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <select class="form-control " name="current_province" id="current_province">
                            <option  value="">Province *</option>
                                <?php
                                $sql = "SELECT id,province FROM provinces LIMIT 5";
                                $query = $database->query($sql);
                                while($row = $database->fetch_array($query)){
                                echo "<option value=\"{$row['id']}\">{$row['province']}</option>";
                                }
                                ?>
                            </select>
                            </div>
                        </div>
                        
                        <div class="row form-group" id="district-group">
                            <div class="col col-md-3">
                            <label class="form-label">District<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <select class="form-control " name="current_district" id="current_district">
                              <option value="">Select</option>
                            </select>
                            </div>
                        </div>

                        <div class="row form-group" id="sector-group">
                            <div class="col col-md-3">
                            <label class="form-label">Sectors<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <select class="form-control " name="current_sectors" id="current_sectors">
                                  <option  value="">Select</option>
                            </select>
                            </div>
                        </div>

                        <div class="row form-group" id="cell-group">
                            <div class="col col-md-3">
                            <label class="form-label">Cell<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <select class="form-control " name="current_cells" id="current_cells">
                              <option value="">Select</option>
                            </select>
                            </div>
                        </div>

                        <div class="row form-group" id="village-group">
                            <div class="col col-md-3">
                            <label class="form-label">Village<span class="required-mark">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                            <select class="form-control " name="current_villages" id="current_villages">
                              <option value="">Select</option>
                            </select>
                            </div>
                        </div>

                       <!-- End of user location -->
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg" onclick="register()">Register</button>
                            <a class="btn btn-lg" style="color: white; background: #FE5F55" href="users" >Cancel</a>
                        </div>
                    </form>

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
    <script src="js/register-user.js"></script>
    <script src="assets/js/sweetalert.min.js"></script>

    </body>
</html>
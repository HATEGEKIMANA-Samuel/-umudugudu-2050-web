<?php
require_once("includes/validate_credentials.php");
?>
<!doctype html>
<html class="no-js" lang="">
<!--<![endif]-->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/metisMenu.min.css" rel="stylesheet">
<!-- Timeline CSS -->
<link href="css/timeline.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="css/startmin.css" rel="stylesheet">
<!-- Morris Charts CSS -->
<link href="css/morris.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<!-- Custom Fonts -->
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">

<head>
    <?php require_once("includes/head.php"); ?>
    <style type="text/css">
        a {
            color: #0487cc;
            text-decoration: none;
        }

        .tab-container {
            background-color: #fff;
            border: 1px solid #eee;
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
        <!-- Main content -->
        <div class="content mt-3">

            <div class="tab-container box-shadow m-20 p-20">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-content-body">
                        <div class="row m-10 ">
                            <div class="col-md-12 pl-0 fw-500 mb-20">
                                <button class="btn btn-secondary text-uppercase dropdown-toggle the-notification-area" type="button" id="notification1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    amamenyesha

                                </button>
                                <!-- <small class="fs-18 fw-600 text-uppercase the-notification-area">
                                    amamenyesha <span class="count bg-danger "><?= input::sanitize("count") ?></span>
                                </small> -->
                            </div>
                            <?= getNotif($_SESSION['id'], 'getreal', $d_location, ""); ?>
                            <!-- notification data -->
                            <!-- <div class="col-md-12 mb-15" style="background-color: #f4f7fd; padding: 10px;border-left: 4px solid #82bed9">
                                <a href="#" class="text-dark navigate" style="display: flex; justify-content: space-between; align-items: center">
                                    <h3 class="fs-14 fw-300">kjhkjdshjkhfjd</h3>
                                    <small>Ibindi &rarr;</small>
                                </a>
                            </div>

                            <div class="col-md-12 mb-15" style="background-color: #f4f7fd; padding: 10px;border-left: 4px solid #82bed9">
                                <a href="#" class="text-dark navigate" style="display: flex; justify-content: space-between; align-items: center">
                                    <h3 class="fs-14 fw-300">kjhkjdshjkhfjd</h3>
                                    <small>Ibindi &rarr;</small>
                                </a>
                            </div> -->
                            <?php
                            ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addmodal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="mediumModalLabel">Add Institution Category</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action='' method='post' name="form">
                                <label for="Name">Name</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" id="Name" class="form-control" name="category" value='<?php if (isset($error)) {
                                                                                                                        echo $_POST['category'];
                                                                                                                    } ?>' required>
                                    </div>


                                </div>

                                <input type='submit' name='save' value='Save' class="btn btn-primary btn-lg">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cancel</button>

                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- end of main content -->
    </div><!-- /#right-panel -->

    <!-- Right Panel -->

    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/lib/chart-js/Chart.bundle.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/widgets.js"></script>
    <script src="assets/js/lib/vector-map/jquery.vmap.js"></script>
    <script src="assets/js/lib/vector-map/jquery.vmap.min.js"></script>
    <script src="assets/js/lib/vector-map/jquery.vmap.sampledata.js"></script>
    <script src="assets/js/lib/vector-map/country/jquery.vmap.world.js"></script>

</html>
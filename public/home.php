<?php
require_once("includes/validate_credentials.php");
require_once("model/user.php");
require_once("model/family.php");
// require_once "routes/queries/db_connection.php";
$numericLocation = input::enc_dec('d', session::get('userLocation'));
$location = user::decodeLocation($database, rtrim($numericLocation, '#'));
if (!session::exists('CL')) {
    switch (session::get('level')) {
        case 2: // village
            session::put('CL', array(
                'text' => 'Rwanda / ' . $location[0]['name'] . ' / ' . $location[1]['name'] . ' / '
                    . $location[2]['name'] . ' / ' . $location[3]['name'] . ' / ' . $location[4]['name'],
                'numeric' => $location[0]['id'] . '#' . $location[1]['id'] . '#' . $location[2]['id']
                    . '#' . $location[3]['id'] . '#' . $location[4]['id']
            ));
            break;
        case 3: // cell
            session::put('CL', array(
                'text' => 'Rwanda / ' . $location[0]['name'] . ' / ' . $location[1]['name'] . ' / '
                    . $location[2]['name'] . ' / ' . $location[3]['name'],
                'numeric' => $location[0]['id'] . '#' . $location[1]['id'] . '#' . $location[2]['id']
                    . '#' . $location[3]['id']
            ));
            break;
        case 4: // sector
            session::put('CL', array(
                'text' => 'Rwanda / ' . $location[0]['name'] . ' / ' . $location[1]['name'] . ' / '
                    . $location[2]['name'],
                'numeric' => $location[0]['id'] . '#' . $location[1]['id'] . '#' . $location[2]['id']
            ));
            break;
        case 5: // district
            session::put('CL', array(
                'text' => 'Rwanda / ' . $location[0]['name'] . ' / ' . $location[1]['name'],
                'numeric' => $location[0]['id'] . '#' . $location[1]['id']
            ));
            break;
        case 6: // province
            session::put('CL', array(
                'text' => 'Rwanda / ' . $location[0]['name'],
                'numeric' => $location[0]['id']
            ));
            break;
        default: // HQ & Admin
            session::put('CL', array(
                'text' => 'Rwanda',
                'numeric' => 0
            ));
            break;
    }
}
$text = "";
if (session::exists("CL")) {
    $location = session::get("CL")["numeric"] . "#";
    $text = "<span class='text-primary'>/" . session::get("CL")["text"] . '</span>';
} else {
    $level = session::get("level");
    if ($level != 7 && $level != 1) $location = input::enc_dec("d", session::get("userLocation"));
    $location = "#";
}
$loc = rtrim($location, "#");
?>
<!doctype html>
<html class="no-js" lang="">

<head>
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
    <?php require_once("includes/head.php"); ?>
    <link rel="stylesheet" href="css/customize.css">
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
        <div class="content mt-10">
            <!-- Diplomats -->
            <!-- </div> -->
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="mb-15 p-20 box-shadow bg-white">
                            <span id="current-location" class="fs-15 fw-500 d-block">Repuburika y'u Rwanda</span>
                        </h2>
                    </div>
                </div>
                <!-- ABATURAGE -->
                <div class="bg-white padding-desk mb-15 box-shadow">
                    <div class="row" id="summaryHolder">
                        <!-- display home data from people -->
                        <div class="col-12">
                            <span class="d-block fw-500 fs-15 border-bottom-1 pb-20 mb-30">
                                <i class="fa fa-users fw-500 pr-5"></i> ABATURAGE <span id="loaderh" class="text-warning"></span>
                            </span>
                        </div>
                        <!-- summary holder here via ajax response -->
                    </div>
                </div>

                <!-- UMUTEKANO -->
                <div class="bg-white padding-desk mb-15 box-shadow">
                    <div class="row" id="securityData">
                        <h1 id="loader"></h1>
                        <!--  security data come here-->
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
    <script src="js/ajax.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
        getTotalSummaryInHome();
        loadSecurityData();
    </script>
    <script src="assets/js/lib/chart-js/Chart.bundle.js"></script>
    <!-- <script src="assets/js/dashboard.js"></script> -->
    <script src="assets/js/widgets.js"></script>
    <!-- <script src="js/home.js"></script> -->
</body>

</html>
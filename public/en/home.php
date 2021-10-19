<?php
require_once("includes/validate_credentials.php");
require_once("model/user.php");
require_once("model/family.php");

$numericLocation = input::enc_dec('d', session::get('userLocation'));
$location = user::decodeLocation($database, $numericLocation);

if (! session::exists('CL')) {
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
    <link href="css/home.css" rel="stylesheet" type="text/css">
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
            <div class="col-lg-12">
                <div class="card card-statistics">
                    <div class="card-body">
                        <h2>
                            <span id="current-location">Republic of Rwanda</span>
                            <hr>
                        </h2>
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between p-10">
                            <div class="statistics-item">
                                <p>
                                    <i class="icon-sm fa fa-user mr-2"></i>
                                    Families
                                </p>
                                <h2 id="family-count"><?= family::getHeadOfFamily($database, $location) ?></h2>
                                <label class="badge badge-outline-success badge-pill" onclick="document.location.href='diplomats-list'" id="view-family">view all of them</label>
                            </div>
                            <div class="statistics-item">
                                <p>
                                    <i class="icon-sm fa fa-user mr-2"></i>
                                    Population
                                </p>
                                <h2 id="member-count"><?= family::getAllPeople($database, $location) ?></h2>
                                <label class="badge badge-outline-danger badge-pill" onclick="document.location.href='population-list'" id="view-member">view all of them</label>
                            </div>
                            <div class="statistics-item">
                                <p>
                                    <i class="icon-sm fa fa-user mr-2"></i>
                                    Beneficiaries
                                </p>
                                <h2 class="Fdiplomats"><?= family::getAllSupport($database, $location) ?></h2>
                                <label class="badge badge-outline-success badge-pill" onclick="document.location.href='help-list'">
                                    view all of them
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Institutions -->
            <?php if (session::exists("level")) : ?>
                <!-- start: province card -->
                <?php if (session::get('level') == 7 or session::get('level') == 1) : ?>
                <div class="col-lg-6 mt-20 col-md-6" id="province-card">
                    <div class="panel panel box-shadow">
                        <div class="panel-heading ">
                            <div class="row">
                                <div class="col-xs-12 text-right">
                                    <h4 class="text-left mb-15 fw-700"><i class="menu-icon fa fa-group mr-10"></i> Province</h4>
                                    <table class="table">
                                        <tbody id="province-list"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <!-- end: province card -->

                <!-- start: district card -->
                <?php if (session::get('level') >= 6 or session::get('level') == 1) : ?>
                <div class="col-lg-6 mt-20 col-md-6" id="district-card">
                    <div class="panel panel box-shadow ">
                        <div class="panel-heading ">
                            <div class="row">
                                <div class="col-xs-12 text-right">
                                    <h4 class="text-left mb-15 fw-700"><i class="menu-icon fa fa-tasks mr-10"></i> Districts <span id="district-name"></span></h4>
                                    <table class="table">
                                        <tbody id="district-list"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <!-- end: disctrict card -->

                <!-- start: sector card -->
                <?php if (session::get('level') >= 5 or session::get('level') == 1) : ?>
                <div class="col-lg-6 mt-20 col-md-6" id="sector-card">
                    <div class="panel panel box-shadow ">
                        <div class="panel-heading ">
                            <div class="row">
                                <div class="col-xs-12 text-right">
                                    <h4 class="text-left mb-15 fw-700"><i class="menu-icon fa fa-tasks mr-10"></i> Sectors form part of the district <span id="sector-name"></span></h4>
                                    <table class="table">
                                        <tbody id="sector-list"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <!-- end: sector card -->

                <!-- start: cell card -->
                <?php if (session::get('level') >= 4 or session::get('level') == 1) : ?>
                <div class="col-lg-6 mt-20 col-md-6" id="cell-card">
                    <div class="panel panel box-shadow ">
                        <div class="panel-heading ">
                            <div class="row">
                                <div class="col-xs-12 text-right">
                                    <h4 class="text-left mb-15 fw-700"><i class="menu-icon fa fa-tasks mr-10"></i> Cells form part of <span id="cell-name"></span></h4>
                                    <table class="table">
                                        <tbody id="cell-list"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <!-- end: cell card -->

                <!-- start: village card -->
                <?php if (session::get('level') >= 3 or session::get('level') == 1) : ?>
                <div class="col-lg-6 mt-20 col-md-6" id="village-card">
                    <div class="panel panel box-shadow ">
                        <div class="panel-heading ">
                            <div class="row">
                                <div class="col-xs-12 text-right">
                                    <h4 class="text-left mb-15 fw-700"><i class="menu-icon fa fa-tasks mr-10"></i> Villages form a cell <span id="village-name"></span></h4>
                                    <table class="table">
                                        <tbody id="village-list"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <!-- end: cell card -->
            <?php endif; ?>

            </center>
            <!-- <div> </div>           -->

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
    <!-- <script src="assets/js/dashboard.js"></script> -->
    <script src="assets/js/widgets.js"></script>
    <script src="js/ajax.js"></script>
    <script src="js/home.js"></script>

</body>
</html>
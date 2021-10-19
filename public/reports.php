<?php
require_once("includes/validate_credentials.php");
require_once("model/user.php");
require_once("model/umutekano.php");
$text = "";
if (session::exists("CL")) {
    $location = session::get("CL")["numeric"] . "#";
    // $text = "<span class='text-primary'>/" . session::get("CL")["text"] . '</span>';
} else {
    $level = session::get("level");
    if ($level != 7 && $level != 1) $location = input::enc_dec("d", session::get("userLocation"));
    $location = "#";
}
// $current_office = getLocationNameFromCode($database, rtrim($location, "#"));
$issue_id = input::enc_dec('d', input::get('case'));
// return $issue_id;
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
            <div class="col-lg-12 text-muted">
                <h2 class="mb-20 p-20 bg-white box-shadow">
                    <span class="fs-18 text-uppercase">
                        <?php
                        // $location = $database->fetch_array(
                        //     $database->query("SELECT (
                        //     SELECT name FROM village WHERE id = village LIMIT 1 ) AS village_name,village 
                        //     FROM user WHERE id ={$_SESSION["id"]} LIMIT 1 ")
                        // );
                        ?>
                        Raporo y'umutekano -
                        <?= session::get("CL")["text"] ?>
                    </span>
                </h2>

            </div>
            <div class="container">
                <div class="row">
                    <?php
                    // $sql = "SELECT (SELECT issue_name FROM issue WHERE issue_id = s.issue_id LIMIT 1) AS issue_type,
                    //         (SELECT icyabaye_name FROM icyabaye WHERE icyabaye_id = s.icyabaye_id LIMIT 1) 
                    //         AS icyabaye_data,s.uruhare_gabo,
                    //         s.uruhare_gore,s.abahohotewe_gabo,
                    //       s.abahohotewe_gore,s.location,s.comments,
                    //       s.security_date,s.security_id 
                    //       FROM security AS s 
                    //       WHERE s.village = {$location['village']}
                    //        ORDER BY s.security_id DESC";
                    $loc = rtrim($location, "#");
                    $loc = $loc == 0 ? "" : " AND s.location LIKE'$loc%'";
                    $cond = "1";
                    if (input::required(array('case'))) {
                        $cond = "s.issue_id='$issue_id'";
                    }
                    if (input::required(array("issue"))) {
                        $issue_id = input::enc_dec('d', input::get('issue'));
                        $cond = " s.security_id=$issue_id";
                        $loc = "";
                    } else if (input::required(array("notify"))) {
                        $issue_id = input::enc_dec('d', input::get('notify'));
                        $cond = " s.security_id=$issue_id";
                        $loc = "";
                    }
                    $sql = "SELECT s.*,
                            (select icyabaye.icyabaye_name FROM icyabaye 
                                where icyabaye.icyabaye_id=s.icyabaye_id LIMIT 1) as icyabaye
                                    FROM security s WHERE $cond $loc ORDER BY s.security_id DESC";
                    $query = $database->query($sql);
                    if ($database->num_rows($query) == 0) echo "<h1 class='text-danger ml-20'>Nta Raporo ibonetse</h1>";
                    while ($security_data = $database->fetch_array($query)) {
                    ?>

                        <div class="col-md-4">
                            <div class="box-shadow bordered mb-10 bg-white">
                                <div class="title d-flex justify-content-between align-items-center border-bottom-1 bg-gradient-purple text-white">
                                    <h3 class="text-uppercase  fs-13 pl-20 pr-20 pt-10 pb-10 bordered">
                                        IBITEKEREZO (<?= umutekano::getTotal($database, "issue_id={$security_data['security_id']}", "security_feedback") ?>);
                                    </h3>
                                    <a class="fs-13 text-gray" href="report-details?issue=<?php echo input::enc_dec('e', $security_data['security_id']) . '#feedback'; ?>">Tanga igitekereze &rarr;</a>
                                </div>
                                <div class="pl-20 pr-20 pt-20 pb-5">
                                    <span class="fw-600 fs-18 d-block mb-5">Icyabaye</span>
                                    <span class=""><?php echo $security_data['icyabaye']; ?></span>
                                </div>

                                <div class="pl-20 d-flex j-space pr-20 pt-15 pb-5">
                                    <span class="fs-15 d-block mb-5"><i class="fa fa-user-o pr-10"></i> Ababigizemo uruhare</span>
                                    <span class="fw-600"><?php $uruhare = $security_data['uruhare_gabo'] + $security_data['uruhare_gore'];
                                                            echo $uruhare; ?></span>
                                </div>

                                <div class="pl-20 pr-20 d-flex j-space pb-5">
                                    <span class="fs-15 d-block mb-5"><i class="fa fa-user-o pr-10"></i> Abahohotewe</span>
                                    <span class="fw-600 "><?php $abahohotewe = $security_data['abahohotewe_gabo'] + $security_data['abahohotewe_gore'];
                                                            echo $abahohotewe; ?></span>
                                </div>

                                <div class="pl-20 pr-20 pb-20">
                                    <span class="fs-15 d-block mb-5"><i class="fa fa-clock-o pr-10"></i> Igihe byabereye</span>
                                    <span class="pr-30 fs-15 d-block"><?php echo $security_data['security_date']; ?></span>
                                </div>
                                <div class="border-top-1">
                                    <a href="report-details?issue=<?php echo input::enc_dec('e', $security_data['security_id']); ?>" class="d-block fs-15 pr-20 pl-20 pt-10 pb-10">&rarr; Reba Ibindi</a>
                                </div>
                            </div>
                        </div>

                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- end of main content -->
    </div><!-- /#right-panel -->
    <!-- update notification -->
    <?php
    if (input::required(array("notify", "nt"))) {
        $nt = input::enc_dec("d", input::get("nt"));
        $database->create(
            "sec_notification_user",
            array(
                "notification_id" => $nt,
                "user_id" => session::get("id")
            )
        );
    }
    ?>
    <!-- end -->
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

    <script>
        $('.toggleMenu, .closeMenu').click(function() {
            $('aside').toggleClass('left-0');
            $('.main-menu ').addClass('show')
        })
    </script>

</body>

</html>
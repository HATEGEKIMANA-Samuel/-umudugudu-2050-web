<?php require_once("includes/validate_credentials.php");
require_once("model/family.php");
$level = session::get("level");
if (session::exists("CL")) {
    $location = session::get("CL")["numeric"] . "#";
    $locname = "<span class='text-primary'>/" . session::get("CL")["text"] . '</span>';
} else {
    if ($level != 7 && $level != 1) $location = input::enc_dec("d", session::get("userLocation"));
    $location = "#";
    $locname = "";
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
    <?php
    $text = '';
    $found_result = 0;
    $cond = "";
    if (!empty($_GET['searchany'])) {
        $text = strtolower(htmlentities($database->escape_value($_GET['searchany'])));
        //  $text = "%" . $text . "%";
        $cond = " key_words LIKE '%$text%'";
        //  $cond = ["m" => "(concat(m.family_name,m.given_name,m.document_id)) LIKE '%$text%'", "d" => "(concat(d.family_name,d.given_name,d.document_id)) LIKE '%$text%' AND 1"];
        $found_result = family::find($database, $cond, $location, "counting");
    }
    ?>
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

                                <small class="fs-14">
                                    Ishakisha <?= $found_result ?> ryabonetse(<?= $locname ?>)</small>
                            </div>
                            <?php
                            if ($found_result != 0) {
                                $limit = 0;
                                $currentPageNumber = 1;
                                $perPage = 30;
                                $link = '';
                                if (isset($_GET['pn']) && is_numeric($_GET['pn']) && $_GET['pn'] > 0 && ($_GET['pn'] - 1) * $perPage < $found_result) {
                                    $currentPageNumber = $_GET['pn'];
                                    $limit = ($_GET['pn'] - 1) * $perPage;
                                }
                                $limit = " LIMIT $limit, $perPage";
                                $people = family::find($database, $cond, $location, "all", $limit);
                                foreach ($people as $key => $diplomat) {
                                    $table = $diplomat["tb"] == "0" ? "kids?kd=" : "family?dpl=";
                                    $href = "$table" . rawurlencode(encrypt_decrypt('encrypt', $diplomat['id']));
                                    if (input::required(array('searchany'))) {
                                        $keyword = input::sanitize("searchany");
                                        $diplomat['givenName'] = preg_replace(
                                            '/(' . $keyword . ')/i',
                                            "<b class='text-primary fs--16'>$1</b>",
                                            $diplomat['givenName']
                                        );
                                        if ($level == 2) {
                                            $diplomat['documentNumber']
                                                = preg_replace(
                                                    '/(' . $keyword . ')/i',
                                                    "<b class='text-primary fs--16'>$1</b>",
                                                    $diplomat['documentNumber']
                                                );
                                        } else {
                                            $diplomat['documentNumber'] = " ";
                                        }

                                        $diplomat['familyName']
                                            = preg_replace(
                                                '/(' . $keyword . ')/i',
                                                "<b class='text-primary fs--16'>$1</b>",
                                                $diplomat['familyName']
                                            );
                                    }

                            ?>

                                    <div class="col-md-12 mb-10" style="background-color: #f4f7fd; padding: 20px;border-left: 4px solid #6a005b">
                                        <a href="<?= $href ?>" class="text-dark navigate">
                                            <h3><?= $diplomat['givenName'] . ' ' . $diplomat['familyName'] . ' ' . $diplomat['otherName'] . ' ' . $diplomat['dob'] ?></h3>
                                            <small><?= $diplomat['documentNumber'] ?></small>
                                        </a>
                                    </div>
                                <?php

                                }
                                // <!-- Pagination here -->
                                $params = "&searchany=" . trim($text, '%');
                                $currentPageName = "search";
                                pagination::template(
                                    $currentPageName,
                                    $currentPageNumber,
                                    $perPage,
                                    $found_result,
                                    $params
                                );
                                // <!-- Pagination ending-->
                            } else {
                                ?>
                                <div class="col-md-12 mb-3 text-center" style="background-color: #fff; color: #ef2b2b">
                                    <img src="images/no_results_v3-1x (1).png" class="mb-30" />
                                    <a href="#" class="text-dark">
                                        <!-- <h3>NO RESULTS FOUND</h3> -->
                                        <h3>Ntabisubizo bibonetse</h3>
                                    </a>
                                    <small class="text-dark">Mwongere mugerageze</small>
                                </div>
                            <?php
                            }
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
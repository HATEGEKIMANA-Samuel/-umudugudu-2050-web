<?php require_once("includes/validate_credentials.php");
require_once("model/family.php");
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

<head>
    <?php require_once("includes/head.php"); ?>
    <link rel="stylesheet" href="assets/css/font-roboto-varela.css">
    <link rel="stylesheet" href="assets/css/material-icon.css">
    <link rel="stylesheet" href="css/diplomat-list.css">
</head>

<body>
    <?php require_once 'includes/left_nav.php'; ?>

    <div id="right-panel" class="right-panel">
        <!-- Header-->
        <?php require_once 'includes/top_nav.php'; ?>
        <div class="container">
            <div class="tab-container mt-10 pl-20 pr-20 pt-0">
                <div class="tab-content">

                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                            $_SESSION['locals'] = '0';
                            $main_title = "<h5 class='text-left fs-18 fw-500'>All population $text </h5>";
                            // $total_found  = $database->num_rows($database->query($sql));
                            $total_found = family::getAllPeople($database, $location);
                            ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <h3 style="font-family: georgia;text-align: center;margin-bottom: 20px;"><?php echo $main_title; ?></h3>
                        </div>
                        <div class="col-sm-3 pt-10">
                            <span class="pt-15" style="margin-left: 1%;"><small class="fs-14">Total: <span class="badge bg-primary"><?php echo $total_found; ?></span></small></span>
                        </div>
                        <div class="col-sm-9 mb-10">
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                                <div class="search-box">
                                    <div class="input-group" id="igroup">
                                        <input type="hidden" name="location" value="<?= $location ?>" />
                                        <input type="hidden" name="table" value="all" />
                                        <input id="search_diplomat" type="text" name="search_people" autocomplete="off" autofocus onkeyup="autoSearch(this);" class="form-control" placeholder="search names/ID/passport&hellip;">
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                    <table class="table table-striped">

                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" style="width: 10%;">#</th>
                                <th scope="col" style="width: 40%;">Names</th>
                                <th scope="col" style="width: 40%;">ID/Passport</th>
                                <th scope="col" style="width: 10%;">More</th>
                            </tr>
                        </thead>
                        <tbody class="search_content"></tbody>
                        <tbody class="old_content">
                            <?php
                            $limit = 0;
                            $currentPageNumber = 1;
                            $perPage = 30;
                            $i = 1;
                            $link = '';
                            if (isset($_GET['pn']) && is_numeric($_GET['pn']) && $_GET['pn'] > 0 && ($_GET['pn'] - 1) * $perPage < $total_found) {
                                $currentPageNumber = $_GET['pn'];
                                $limit = ($_GET['pn'] - 1) * $perPage;
                                $i = $limit + 1;
                            }
                            $limit = "LIMIT $limit, $perPage";
                            $people = family::getAllPeople($database, $location, $limit);

                            while ($diplomat = $database->fetch_array($people)) { ?>
                                <tr>
                                    <td scope='row' class='numbering'><?= $i ?></td>
                                    <td><?= $diplomat['given_name'] . ' ' . $diplomat['family_name'] . ' ' . $diplomat['other_name'] . ' ' . $diplomat['dob'] ?></td>
                                    <td><?= $diplomat['document_id'] ?></td>
                                    <?php
                                    $table = $diplomat["tb"] == "m" ? "kids?kd=" : "family?dpl=";
                                    $href = "$table" . rawurlencode(encrypt_decrypt('encrypt', $diplomat['id']))
                                    ?>
                                    <td><a style href='<?= $href ?>'>
                                            <button type='button' class='btn btn-default btn-sm'>
                                                <i class="ti-user"></i> More
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            <?php
                                $i++;
                            }
                            ?>

                        </tbody>
                    </table>
                    <!-- Pagination here -->
                    <?php
                    $currentPageName = "population-list";
                    pagination::template(
                        $currentPageName,
                        $currentPageNumber,
                        $perPage,
                        $total_found
                    );
                    ?>
                </div>
                <!-- Pagination ending-->

            </div>
        </div>
    </div>
    </div>
    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="js/ajax.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="js/search.js"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>

</body>

</html>
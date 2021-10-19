<?php require_once("includes/validate_credentials.php");
require_once("model/family.php");
$text = "";
$search = "";
// $search_togle = "";
// $toggle = '';
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

                            $main_title = "<h5 class='text-left fs-18 fw-500'>Abaturage Bose $text</h5>";
                            $sql .= $search;
                            if (isset($toggle))
                                $toggle .= isset($search_togle) ? $search_togle : '';
                            // $total_found  = $database->num_rows($database->query($sql));
                            $total_found = family::getAllPeople($database, $location, "0", "1");
                            ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 mt-10 mb-20">
                            <h3 style="font-family: georgia;text-align: center;margin-bottom: 20px;"><?php echo $main_title; ?></h3>
                        </div>
                        <div class="col-sm-3 pt-10">
                            <span class="pt-15" style="margin-left: 1%;"><small class="fs-14">Igiteranyo: <span class="badge bg-primary text-danger" id='total-found'></span></small></span>
                        </div>
                        <div class="col-sm-9 mb-10">
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                                <div class="search-box">
                                    <div class="input-group" id="igroup">
                                        <input type="hidden" name="location" value="<?= $location ?>" />
                                        <input type="hidden" name="table" value="all" />
                                        <input id="search_diplomat" type="text" name="search_people" autocomplete="off" autofocus onkeyup="autoSearch(this);" class="form-control" placeholder="shaka amazina/indangamuntu/pasiporo&hellip;">
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                    <table class="table mt-20 table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Amakuru</th>
                                <th scope="col" class="hide-on-mobile">Indangamuntu/pasiporo</th>
                                <th scope="col">Ibindi</th>
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
                            // $people = family::getAllPeople($database, $location, $limit);
                            $l = family::searchLocation($location);
                            $column = empty($l["column"]) ? ",' 'AS l" : "," . $l["column"];
                            $query = "SELECT id,given_name,family_name,other_name,
                                    document_id,dob,l,tb from (SELECT 
                                    d.citizenId as id,d.givenName as given_name,d.familyName as family_name,d.otherName as other_name,
                                    d.documentNumber as document_id,d.dob $column,is_family_heading as tb from citizen d where  d.gender='male'
                                    ) as data WHERE {$l['value']}  ORDER BY id DESC  $limit";
                            $people = $database->query($query);
                            $is_village = family::checkIfIsVillage($location);
                            if ($is_village) {
                                $query = "SELECT 
                                    d.citizenId as id,d.givenName as given_name,d.familyName as family_name,d.otherName as other_name,
                                    d.documentNumber as document_id,d.dob,is_family_heading as tb from citizen d where d.currentLocation='$is_village' AND  d.gender='male'  $limit";
                            }
                            ob_start();
                            while ($diplomat = $database->fetch_array($people)) { ?>
                                <tr>
                                    <td scope='row' class='numbering'><?= $i ?></td>
                                    <td><?= $diplomat['given_name'] . ' ' . $diplomat['family_name'] . ' ' . $diplomat['other_name'] . ' ' . $diplomat['dob'] ?></td>
                                    <td class="hide-on-mobile"><?= $diplomat['document_id'] ?></td>
                                    <?php
                                    $table = $diplomat["tb"] == "0" ? "kids?kd=" : "family?dpl=";
                                    $href = "$table" . rawurlencode(encrypt_decrypt('encrypt', $diplomat['id']))
                                    ?>
                                    <td><a style href='<?= $href ?>' class="navigate">
                                            <button type='button' class='btn btn-default btn-sm'>
                                                <i class="ti-user"></i> Byinshi
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            <?php
                                $i++;
                            }
                            ob_end_flush();
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
        $('#total-found').text('<?= --$i ?>')
    </script>

</body>

</html>
<?php require_once("includes/validate_credentials.php");
require_once("model/family.php");
require_once "model/user.php";
// check dpl exist

if (isset($_GET['dpl'])) {
    $dpl_data = explode('%', $_GET['dpl']);
    $found = count($dpl_data);
    if ($found > 1) {
        $dpl = encrypt_decrypt('decrypt', $dpl_data[0]);
    } else {
        $dpl = encrypt_decrypt('decrypt', $_GET['dpl']);
    }
} else {
    header("location:404");
}
if (!isset($dpl) || !is_numeric($dpl)) {
    header("location:404");
} else {
    $diplomat = family::getMember($database, $dpl);
    if (count($diplomat) == 0) {
        header("location:404");
    }
}
function getReason($reason)
{
    switch (strtolower($reason)) {
        case 'kid':
            return "kwimuka";
            break;
        case 'visitor':
            return "gusura";
            break;
        default:
            return $reason;
            break;
    }
}
// get citizen  info
$citzen = $database->fetch_array($database->query("SELECT citizenId as id,familyName,givenName,otherName,
documentNumber,location FROM citizen WHERE citizenId='$dpl'"));
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
                            $toggle = "";
                            $main_title = "";
                            $search = "";
                            $search_togle = "";
                            if (isset($_GET['search_people']) && !empty($_GET['search_people'])) {
                                $search_keyword = strtolower(htmlentities($database->escape_value($_GET['search_people'])));
                                // $search = " AND ((LOWER(d.given_name) LIKE '%$search_keyword%')  OR (LOWER(d.family_name) LIKE '%$search_keyword%') OR (LOWER(d.other_name) LIKE '%$search_keyword%') OR (LOWER(d.document_id) LIKE '%$search_keyword%')) ";
                                $search = " AND d.key_words LIKE  '%$search_keyword%'";
                                $search_togle = "&search_people=" . rawurlencode($_GET['search_people']);
                            }
                            $main_title = "<h4 class='text-center mt-20 fs-18'>Iyimuka ryakozwe na<span class='text-success'> " . $citzen["familyName"] . ' ' . $citzen["otherName"] . ' ' . $citzen["givenName"] . '/' . $citzen["documentNumber"] . " </span>  <hr class='mt-20 mb-20'></h4>";
                            $sql .= $search;
                            $toggle .= $search_togle;
                            // $total_found  = $database->num_rows($database->query($sql));
                            $total_found = family::getTotal($database, "history", " where citizenId='$dpl'");
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 mt-10 mb-20">
                            <h3 style="font-family: georgia;text-align: center;margin-bottom: 20px;"><?php echo $main_title; ?></h3>
                        </div>
                        <div class="col-sm-3 pt-10">
                            <span class="pt-15" style="margin-left: 1%;"><small class="fs-14">Igiteranyo: <span class="badge bg-primary text-danger"><?php echo $total_found; ?></span></small></span>
                        </div>
                        <div class="col-sm-9 mb-10 d-none">
                            <!-- <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                                <div class="search-box">
                                    <div class="input-group" id="igroup">
                                        <input type="hidden" name="location" value="<?= $location ?>" />
                                        <input type="hidden" name="table" value="all" />
                                        <input id="search_diplomat" type="text" name="search_people" autocomplete="off" autofocus onkeyup="autoSearch(this);" class="form-control" placeholder="shaka amazina/indangamuntu/pasiporo&hellip;">
                                    </div>
                                </div>
                            </form> -->
                        </div>
                    </div>
                    <table class="table mt-20 table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">italiki</th>
                                <th scope="col">Ahantu</th>
                                <th scope="col" class="hide-on-mobile">impanvu</th>
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
                            $people = family::getMovementById($database, $dpl, $limit);
                            foreach ($people as $key => $diplomat) {
                                $loc = user::getLocationName($database, $diplomat['location']);
                            ?>
                                <tr>
                                    <td scope='row' class='numbering'>E-<?= $i ?></td>
                                    <td><?= $diplomat['time']  ?></td>
                                    <td><?= $loc['province'] . '/' . $loc['district'] . '/' . $loc['sector'] . '/' . $loc['cell'] . '/' . $loc['village'] ?></td>
                                    <td class="hide-on-mobile"><?= getReason($diplomat['reason']) ?></td>
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
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>

</body>

</html>
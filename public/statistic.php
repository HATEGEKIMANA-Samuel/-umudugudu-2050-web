<?php
require_once("includes/validate_credentials.php");
if (isset($_GET['dpl'])) {
    $diplomat = encrypt_decrypt('decrypt', $_GET['dpl']);
}
?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="">
<!--<![endif]-->

<head>
    <?php require_once("includes/head.php"); ?>
    <link rel="stylesheet" href="css/statistic.css">
</head>

<body>

    <?php require_once 'includes/left_nav.php'; ?>

    <div id="right-panel" class="right-panel">
        <!-- Header-->
        <?php require_once 'includes/top_nav.php'; ?>
        <div class="container">
            <div class="tab-container">
                <div class="tab-content mt-10" id="nav-tabContent">

                    <div class="tab-content-body">
                        <h4 class="text-center fs-16 mt-20"> <span style="color:green;">

                            </span>Ibarurishamibare
                            <hr class="mt-20 mb-20">
                        </h4>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="hidden" name="action" value="view_statistic_numbering" />
                                    <div class="form-group">
                                        <label>Imibare</label>
                                        <select value="" name="counting" class="form-control" onchange="checkChanges(this,'education','.dvedu')">
                                            <option value="people">Abaturage</option>
                                            <option value="education">Uburezi</option>
                                            <option value="help">Gufashwa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 dvedu display-none ">
                                    <div class="form-group">
                                        <label>urwego rw'uburezi</label>
                                        <select name="level_education" class="form-control" value="">
                                            <option value="">hitamo</option>
                                            <option value="abanza">Amashuri abanza </option>
                                            <option value="rusange ">icyiciro rusange</option>
                                            <option value="ayisumbuye">Amashuri yisumbuye </option>
                                            <option value="imyuga"> Amashuri y' imyuga </option>
                                            <option value="kaminuza">Amashuri makuru na kaminuza </option>
                                            <option value="abatarize">kutajya mwishuri</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="check">Kureba</label>
                                        <select class="form-control" value="" name="view" onchange="checkChanges(this,'range','.dvRange')">
                                            <option value="all">Bose</option>
                                            <option value="range">Intera y'imyaka</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-12 dvRange display-none">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">

                                                <label for="checkfrom">kuva</label>
                                                <input type="number" name="start_year" placeholder="urugero:16" class="form-control" id="checkfrom" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="checkto">Kugeza</label>
                                                <input type="number" name="end_year" placeholder="urugero:25" class="form-control" id="checkto" value="0" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 card-view display-none">
                                    <div class="card">
                                        <div class="card-head">
                                            <h4 class="card-title"><span class="waiting  text-warning "> <i class='fa fa-spinner fa-spin  text-warning' style="font-size:20px"></i> Tegereza... </span></h4>
                                        </div>
                                        <div class="card-body response text-center font-weight-bold">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group view-all">
                                        <button type="button" class="btn btn-primary btn-lg btnStatistic"> Emeza</button>
                                    </div>
                                </div>
                                <!-- <div class="col-lg-6"></div> -->
                            </div>
                        </form>
                    </div>
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
    <script src="js/statistics.js"></script>
    <script>
        $('.datepicker').datepicker({
            endDate: '0d',
            format: 'yyyy-mm-dd'
        });

        $('.datepicker1').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>

</body>

</html>
<?php
require_once("includes/validate_credentials.php");
if (isset($_GET['dpl'])) {
  $diplomat = encrypt_decrypt('decrypt', $_GET['dpl']);
}
require_once "model/family.php";
$hData = array();
if (input::required(array("cr"))) {
  require_once "model/family.php";
  $eHelp = input::enc_dec("d", input::get("cr"));
  $hData = family::getHelpById($database, $eHelp);
  $text = "Guhindura amakuru  ";
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
  <link rel="stylesheet" href="css/add-help.css">
</head>

<body>
  <?php require_once 'includes/left_nav.php'; ?>

  <div id="right-panel" class="right-panel">
    <!-- Header-->
    <?php require_once 'includes/top_nav.php'; ?>
    <div class="container">
      <div class="tab-container">
        <div class="tab-content mt-10" id="nav-tabContent">
          <?php
          if (isset($diplomat) && is_numeric($diplomat)) {
            $row = family::getMember($database, $diplomat);
          }
          $text = "Ubufasha buhabwa ";
          if (isset($car) && is_numeric($car)) {
            $query = $database->query("SELECT * FROM cars WHERE id = '$car' AND status='1' LIMIT 1");
            $row  = $database->fetch_array($query);
            $text = "Guhindura Amakuru y'ubufasha ya ";
          }
          ?>
          <div class="tab-content-body">
            <h4 class="text-center fs-16 mt-20"><?= $text ?> <span style="color:green;">
                <?php echo "{$row['given_name']} {$row['family_name']}"; ?>
              </span>
              <hr class="mt-20 mb-20">
            </h4>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form">
              <div class="errors col-md-12"></div>
              <?php if (isset($eHelp) && is_numeric($eHelp)) { ?>
                <input type="hidden" name="id_to_edit" value="<?php echo $eHelp; ?>" />
              <?php } ?>

              <?php if (isset($diplomat) && is_numeric($diplomat)) { ?>
                <input type="hidden" name="diplomat" value="<?php echo $diplomat; ?>" />
              <?php } ?>
              <input type="hidden" name="action" value="help" />
              <div class="row">
                <div class="col-md-6 date">
                  <div class="form-group">
                    <?php $sponsor = output::print("giver", $hData, ""); ?>
                    <label>Umuterankunga <span class="required-mark">*</span></label>
                    <select name="giver" class="form-control" value="" class="form-control" onchange=" if($(this).val()=='ikindi'){$('.dvsponsor').removeClass('display-none')}else{
                $('.dvsponsor').addClass('display-none')
              }">
                      <option value="">Hitamo</option>
                      <option value="FARG" <?= $sponsor == "FARG" ? "selected" : "" ?>>FARG</option>
                      <option value="VUP" <?= $sponsor == "VUP" ? "selected" : "" ?>>VUP</option>
                      <option value="INGOBOKA" <?= $sponsor == "INGOBOKA" ? "selected" : "" ?>>ingoboka</option>
                      <option value="ikindi" <?= $sponsor == "ikindi" ? "selected" : "" ?>>Undi muterankunga</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 date dvsponsor <?= $sponsor == "ikindi" ? "" : "display-none" ?>">
                  <div class="form-group">
                    <label> Izina ry'umuterankunga<span class="required-mark">*</span></label>
                    <input type="text" name="other_sponsor" value="<?= output::print("other_giver", $hData, "") ?>" class="form-control" placeholder="Izina ry'umuterankunga" />
                  </div>
                </div>
                <div class="col-md-6 date">
                  <div class="form-group">
                    <label>Icyo ahabwa<span class="required-mark">*</span></label>
                    <input type="text" name="what_help" value="<?= output::print("help", $hData, "") ?>" class="form-control" placeholder="urugero:amafaranga" />
                  </div>
                </div>
                <div class="col-md-6 date">
                  <div class="form-group">
                    <label>Incuro ahabwa ubufasha</label>
                    <input type="text" name="count" value="<?= output::print("count_help", $hData, "") ?>" class="form-control" placeholder="urugero:burikwezi" />
                  </div>
                </div>
                <div class="col-md-6 date">
                  <div class="form-group">
                    <label>Icyo avuga kubufasha ahabwa<span class="required-mark">*</span></label>
                    <input type="text" name="comment" value="<?= output::print("comment", $hData, "") ?>" class="form-control" placeholder="urugero:nyibonera kugihe" />
                  </div>
                </div>
                <div class="col-lg-4 col-lg-offset-4">
                  <button type="button" name="save_car" class=" btnSaveHelp btn w-100p fs-15 btn-primary mt-10 p-10 pull-right">Emeza</button>
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
  <script src="js/member.js"></script>
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
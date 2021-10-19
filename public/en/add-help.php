<?php
require_once("includes/validate_credentials.php");
if (isset($_GET['dpl'])) {
  $diplomat = encrypt_decrypt('decrypt', $_GET['dpl']);
}
$hData = array();
if (input::required(array("cr"))) {
  require_once "model/family.php";
  $eHelp = input::enc_dec("d", input::get("cr"));
  $hData = family::getHelpById($database, $eHelp);
  $text = "Editing Help Information ";
}
require_once "model/family.php";
?>
<!doctype html>
<html class="no-js" lang="">

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
          $text = "Help given to  ";
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
                    <label>Sponsor <span class="required-mark">*</span></label>
                    <select name="giver" class="form-control" value="" class="form-control" onchange=" if($(this).val()=='ikindi'){$('.dvsponsor').removeClass('display-none')}else{
                $('.dvsponsor').addClass('display-none')
              }">
                      <?php $sponsor = output::print("giver", $hData, ""); ?>
                      <option value="">Select</option>
                      <option value="FARG" <?= $sponsor == "FARG" ? "selected" : "" ?>>FARG</option>
                      <option value="VUP" <?= $sponsor == "VUP" ? "selected" : "" ?>>VUP</option>
                      <option value="INGOBOKA" <?= $sponsor == "INGOBOKA" ? "selected" : "" ?>>ingoboka</option>
                      <option value="ikindi" <?= $sponsor == "ikindi" ? "selected" : "" ?>>Other</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 date dvsponsor <?= $sponsor == "ikindi" ? "" : "display-none" ?>">
                  <div class="form-group">
                    <label> Sponsor names<span class="required-mark">*</span></label>
                    <input type="text" name="other_sponsor" value="<?= output::print("other_giver", $hData, "") ?>" class="form-control" placeholder="
                    Sponsor " />
                  </div>
                </div>
                <div class="col-md-6 date">
                  <div class="form-group">
                    <label>Help<span class="required-mark">*</span></label>
                    <input type="text" name="what_help" class="form-control" placeholder="Eg:money" value="<?= output::print("help", $hData, "") ?>" />
                  </div>
                </div>
                <div class="col-md-6 date">
                  <div class="form-group">
                    <label>Often he/she is helped</label>
                    <input type="text" name="count" class="form-control" placeholder="Eg:Monthly" value="<?= output::print("count_help", $hData, "") ?>" />
                  </div>
                </div>
                <div class="col-md-6 date">
                  <div class="form-group">
                    <label>Comment on given help<span class="required-mark">*</span></label>
                    <input type="text" name="comment" class="form-control" placeholder="comment" value="<?= output::print("comment", $hData, "") ?>" />
                  </div>
                </div>
                <div class="col-lg-4 col-lg-offset-4">
                  <button type="button" name="save_car" class=" btnSaveHelp btn w-100p fs-15 btn-primary mt-10 p-10 pull-right">Confirm</button>
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
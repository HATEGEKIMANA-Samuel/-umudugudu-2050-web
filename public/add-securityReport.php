<?php
require_once("includes/validate_credentials.php");
$message = "";
if (isset($_POST['umutekanoo'])) {
  $umutekanoo = htmlentities($database->escape_value($_POST['umutekanoo']));
  $icyabaye = htmlentities($database->escape_value($_POST['icyabaye']));
  $uruhare_gabo = htmlentities($database->escape_value($_POST['uruhare_gabo']));
  $uruhare_gore = htmlentities($database->escape_value($_POST['uruhare_gore']));
  $a_gabo = htmlentities($database->escape_value($_POST['a_gabo']));
  $a_gore = htmlentities($database->escape_value($_POST['a_gore']));
  $comments = htmlentities($database->escape_value($_POST['comments']));
  //for inzego zabimenye
  $inzego_zumutekano = "";
  if (!empty($_POST['inzego_u'])) {
    // Loop to store and display values of individual checked checkbox.
    foreach ($_POST['inzego_u'] as $selected) {
      if ($inzego_zumutekano == "") {
        $inzego_zumutekano .= $selected;
      } else {
        $inzego_zumutekano .= "#" . $selected;
      }
    }
  }
  if ($umutekanoo == '' || $icyabaye == '' || $uruhare_gabo == '' || $uruhare_gore == '' || $a_gabo == '' || $a_gore == ''  || $comments == '' ||  $inzego_zumutekano == '') {
    $message = "Injiza Amakuru yose akenewe(*)";
  } else {
    $location = $database->fetch_array($database->query("SELECT location,village FROM user WHERE id ={$_SESSION["id"]} LIMIT 1 "));
    $sql = "INSERT INTO security 
    (issue_id,icyabaye_id,uruhare_gabo,uruhare_gore,abahohotewe_gabo,abahohotewe_gore,location,comments,security_date,user,village,security_org)       
    VALUES
    ('{$umutekanoo}','{$icyabaye}','{$uruhare_gabo}','{$uruhare_gore}','{$a_gabo}','$a_gore','{$location['location']}','$comments',NOW(),{$_SESSION["id"]},'{$location['village']}','$inzego_zumutekano')";
    $database->query($sql);
    $sec_id = $database->inset_id();
    if ($sec_id) {
      $id = input::enc_dec('e', $sec_id);
      // push notification
      $database->create(
        "sec_notification",
        array(
          "location" => $location['location'],
          "action" => input::sanitize("action") . '->'
            . session::get("CL")["text"],
          'link' => "reports?notify=$id"
        )
      );
      $nt = $database->inset_id();
      if ($nt) {
        $database->create(
          "sec_notification_user",
          array(
            "notification_id" => $nt,
            "user_id" => session::get("id")
          )
        );
      }
      header("Location: reports?issue=$id");
    } else {
      $message = "Database error!";
    }
  }
}

?>
<!doctype html>
<html class="no-js" lang="">

<head>
  <?php require_once("includes/head.php"); ?>
  <link rel="stylesheet" href="css/add-family.css">
  <link rel="stylesheet" href="css/customize.css">
</head>

<body oncontextmenu="return false">
  <?php require_once 'includes/left_nav.php'; ?>

  <div id="right-panel" class="right-panel">
    <!-- Header-->
    <?php require_once 'includes/top_nav.php'; ?>
    <div class="container">
      <div class="tab-container mt-10">
        <div class="tab-content" id="nav-tabContent">

          <div class="tab-content-body">
            <h4 class="fs-18 mt-10 fw-500 text-center ">Gutanga Amakuru - Umutekano
              <hr class="mt-20 mb-20">
              <span class="waiting  text-warning display-none"> <i class='fa fa-spinner fa-spin  text-warning' style="font-size:20px"></i> Tegereza... </span>
            </h4>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form" class="formAddMember">
              <div class="row">
                <div class="col-md-6 doctypes">
                  <div class="form-group">
                    <label for="doctype">Icyahungabanyije Umutekano <span class="required-mark">*</span></label>
                    <select name="umutekanoo" class="form-control required" id="umutekano_id">
                      <option value="0"> -- Hitamo --</option>
                      <?php
                      $sql = "SELECT * FROM `issue` WHERE status=1";
                      $issue_query = $database->query($sql);
                      while ($issue_row = $database->fetch_array($issue_query)) {
                        $selected = "";
                        if (input::get("umutekanoo") == $issue_row['issue_id']) {
                          $selected = " selected";
                        }
                        echo "
                        <option  value=\"{$issue_row['issue_id']}\" $selected >
                        {$issue_row['issue_name']}</option>
                        ";
                      }

                      ?>

                    </select>
                  </div>
                </div>

                <div class="col-md-6 ">
                  <div class="form-group">
                    <label for="gender">Icyabaye<span class="required-mark">*</span></label>
                    <select name="icyabaye" class="form-control required" id="icyabaye_id">
                    </select>
                    <input type="hidden" name="action" id="action_id">
                  </div>
                </div>
                <div class="col-md-12 divpp ">
                  <fieldset class="fiedset-type pb-20">
                    <legend class="fieldset-legend"><span class="legendTitle"> Abigizemo Uruhare</span></legend>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="doctype">Gabo<span class="required-mark">*</span></label>
                        <input type="number" maxlength="20" autocomplete="off" placeholder="Umubare W'Abagabo" class="form-control required" name="uruhare_gabo" value="<?= input::get("uruhare_gabo") ?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="gender">Gore<span class="required-mark">*</span></label>
                        <input type="number" axlength="20" autocomplete="off" placeholder="Umubare W'Abagore" class="form-control required" name="uruhare_gore" value="<?= input::get("uruhare_gore") ?>">
                      </div>
                    </div>
                  </fieldset>
                </div>

                <div class="col-md-12 divpp ">
                  <fieldset class="fiedset-type pb-20">
                    <legend class="fieldset-legend"><span class="legendTitle"> Abahohotowe</span></legend>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="doctype">Gabo<span class="required-mark">*</span></label>
                        <input type="number" maxlength="20" autocomplete="off" placeholder="Umubare W'Abagabo" class="form-control required" name="a_gabo" value="<?= input::get("a_gabo") ?>">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="gender">Gore<span class="required-mark">*</span></label>
                        <input type="number" axlength="20" autocomplete="off" placeholder="Umubare W'Abagore" class="form-control required" name="a_gore" value="<?= input::get("a_gore") ?>">
                      </div>
                    </div>

                  </fieldset>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="doctype">Amakuru yinyongera kucyabaye<span class="required-mark">*</span></label>
                      <textarea type="text" autocomplete="off" placeholder="Amakuru" class="form-control required" name="comments"><?= input::get("comments") ?></textarea>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="doctype">Inzego zamenye icyabaye<span class="required-mark">*</span></label><br />
                      <!-- Inzego here -->
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="inzego_u[]" value="Inagabo">
                        <label class="form-check-label" for="inlineCheckbox1">Ingabo</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="inzego_u[]" value="Polisi">
                        <label class="form-check-label" for="inlineCheckbox2">Polisi</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="inzego_u[]" value="RIB">
                        <label class="form-check-label" for="inlineCheckbox3">RIB</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="inzego_u[]" value="DASSO">
                        <label class="form-check-label" for="inlineCheckbox3">DASSO</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="inzego_u[]" value="Akagali">
                        <label class="form-check-label" for="inlineCheckbox3">Akagali</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="inzego_u[]" value="Umurenge">
                        <label class="form-check-label" for="inlineCheckbox3">Umurenge</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="inzego_u[]" value="Akarere">
                        <label class="form-check-label" for="inlineCheckbox3">Akarere</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="inzego_u[]" value="Intara/Umujyi wa Kigali">
                        <label class="form-check-label" for="inlineCheckbox3">Intara/Umujyi wa Kigali</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="inzego_u[]" value="MINALOC">
                        <label class="form-check-label" for="inlineCheckbox3">MINALOC</label>
                      </div>
                      <!-- End inzego -->
                    </div>
                  </div>
                </div>
                <span class="text-danger"> <?= $message ?></span>
                <div class="col-lg-12 text-center mt-20 mb-50 ">
                  <input type="submit" name="umutekano" class="btn fs-15 pt-5 pb-5 w-50p btn-primary navigate" value="Emeza">
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- modal used for showing people movement -->


  <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
  <script>
    $("#icyabaye_id").on("change", function(e) {
      $("#action_id").val($("#icyabaye_id option:selected").text());
    })
  </script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/bootstrap-datepicker.js"></script>
  <script src="assets/js/plugins.js"></script>
  <script src="js/ajax.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/custom.js"></script>
</body>

</html>
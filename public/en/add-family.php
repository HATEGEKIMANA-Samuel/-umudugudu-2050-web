<?php
require_once("includes/validate_credentials.php");
require_once "model/family.php";
require_once "model/country.php";
$hashedLocation = input::enc_dec("d", session::get("userLocation"));
$issued_country = 0;

if (isset($_GET['dpl'])) {
  $diplomat = encrypt_decrypt('decrypt', $_GET['dpl']);
}
?>
<!doctype html>
<html class="no-js" lang="">

<head>
  <?php require_once("includes/head.php"); ?>
  <link rel="stylesheet" href="css/add-family.css">
</head>

<body>
  <?php require_once 'includes/left_nav.php'; ?>

  <div id="right-panel" class="right-panel">
    <!-- Header-->
    <?php require_once 'includes/top_nav.php'; ?>
    <div class="container">
      <div class="tab-container mt-10">
        <div class="tab-content" id="nav-tabContent">

          <?php if (isset($diplomat) && is_numeric($diplomat)) { ?>
          <?php
            $row  = family::getMember($database, $diplomat);
          }
          ?>
          <div class="tab-content-body">
            <h4 class="fs-18 mt-10 fw-500 text-center ">Profile -
              The head of the family
              <hr class="mt-20 mb-20">
              <span class="waiting  text-warning display-none"> <i class='fa fa-spinner fa-spin  text-warning' style="font-size:20px"></i> Tegereza... </span>
            </h4>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form" class="formAddMember">
              <div class="row">
                <div class="col-md-12"></div>
              </div>
              <input type="hidden" name="action" value="head_of_family" class="txtAction" />
              <input type="hidden" name="transfer" value="no" />
              <input type="hidden" name="tid" value="0" />
              <input type="hidden" name="table" value="diplomats" />
              <?php if (isset($diplomat) && is_numeric($diplomat)) { ?> <input type="hidden" name="id_to_edit" value="<?php echo $diplomat; ?>" />
              <?php } ?>
              <input type="hidden" name="user_loc" value="<?= $hashedLocation ?>">
              <div class="row">
                <div class="col-md-6 doctypes">
                  <div class="form-group">
                    <label for="doctype">Documents<span class="required-mark">*</span></label>
                    <select name="doctype" class="form-control required" id="doctype" onchange="checkDocType(this);">
                      <option value=""> -- Choose --</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['type'] == 'ID') {
                                echo "selected";
                              } else {
                                echo "selected";
                              } ?> value="ID">ID</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['type'] == 'PASSPORT') {
                                echo "selected";
                              } ?> value="PASSPORT">passport</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 divid">
                  <div class="form-group">
                    <?php
                    if (isset($row['type']) && $row['type'] == 'ID') {
                      $rwanda_id  = $row['document_id'];
                    } else {
                      $rwanda_id = "";
                    }
                    ?>
                    <label for="location">ID <span class="required-mark">*</span></label>
                    <input type="text" name="rwandan_id" class="form-control rwandanId" onkeyup="onlyNumber(this);
                    checkMember($(this).val().trim(),'ID');" placeholder="Rwandan identity card " maxlength="16" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                          echo $rwanda_id;
                                                                                                                        } ?>">
                  </div>
                </div>
                <div class="col-md-12 divpp display-none">
                  <fieldset class="fiedset-type pb-20">
                    <legend class="fieldset-legend"><span class="legendTitle"> passport</span></legend>
                    <div class="col-md-6">
                      <div class="form-group">
                        <?php
                        if (isset($row['type']) && $row['type'] == 'PASSPORT') {
                          $pass_port = $row['document_id'];
                        } else {
                          $pass_port = "";
                        }
                        ?>

                        <input type="text" class="form-control required" name="passport" maxlength="30" onkeyup="validateIDorPassport($(this).val().trim(),'PASSPORT');" placeholder="passport Number *" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                                                                                  echo $pass_port;
                                                                                                                                                                                                                } elseif (isset($_POST['passport'])) {
                                                                                                                                                                                                                  echo $_POST['passport'];
                                                                                                                                                                                                                } ?>">
                      </div>
                    </div>
                    <div class="col-md-6 ">
                      <div class="form-group">
                        <?php $countries = Country::getAllCountry($database); ?>
                        <select class="form-control required" name="issued_country">
                          <option value="">--Issued Country--</option>
                          <?php
                          foreach ($countries as $key => $country) {
                          ?>
                            <option value="<?= $country['id'] ?>" <?= $country['id'] == output::print("issued_country", $row) ? "selected" : '' ?>>
                              <?= $country['name'] ?></option>
                          <?php
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <input type="text" maxlength="20" autocomplete="off" placeholder="Issued Date" class="form-control datepickerGP required" name="issued_date" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                                              echo $row['issued_date'];
                                                                                                                                                                            } ?>">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <input type="text" axlength="20" autocomplete="off" placeholder="expired Date" class="form-control datepickerEP required" name="expiry_date" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                                              echo $row['expiry_date'];
                                                                                                                                                                            }  ?>">
                      </div>
                    </div>

                  </fieldset>
                </div>
                <div class="col-md-6 opacity">

                  <div class="form-group">
                    <label for="name">Given name<span class="required-mark">*</span></label>
                    <input type="text" class="form-control required" maxlength="100" placeholder="Given name " name="given_name" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                          echo $row['given_name'];
                                                                                                                                        } elseif (isset($_POST['given_name'])) {
                                                                                                                                          echo $_POST['given_name'];
                                                                                                                                        } ?>">
                  </div>

                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Family name<span class="required-mark">*</span></label>
                    <input type="text" class="form-control required" maxlength="100" placeholder="Family name" name="family_name" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                            echo $row['family_name'];
                                                                                                                                          } ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Other name</label>
                    <input type="text" class="form-control" maxlength="100" placeholder="Other name" name="other_name" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                echo $row['other_name'];
                                                                                                                              } ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="gender">Gender<span class="required-mark">*</span></label>
                    <select name="gender" class="form-control required" id="gender">
                      <option value=""> -- Hitamo --</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['gender'] == 'Male') {
                                echo "selected";
                              } ?> value="Male">Male</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['gender'] == 'Female') {
                                echo "selected";
                              } ?> value="Female">Female</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['gender'] == 'Other') {
                                echo "selected";
                              } ?> value="Female">Other</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group date">
                    <label>Date of birth<span class="required-mark">*</span></label>
                    <input type="text" maxlength="20" autocomplete="off" placeholder="YYYY-mm-dd" class="form-control datepickerDOB required" name="dob" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                                  echo $row['dob'];
                                                                                                                                                                } ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Status<span class="required-mark">*</span></label>
                    <select class="form-control required" name="marital_status">
                      <option value=""> -- choose --</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['marital_status'] == 'Single') {
                                echo "selected";
                              }  ?> value="Single">Single</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['marital_status'] == 'Married') {
                                echo "selected";
                              } ?> value="Married">Married</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['marital_status'] == 'Divorced') {
                                echo "selected";
                              }  ?> value="Divorced">Divorced</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="contact_name">Place of birth<span class="required-mark">*</span></label>
                    <input type="text" class="form-control required" maxlength="255" placeholder="Enter Place Of Birth" name="birth_place" value=" <?= output::print("birth_place", $row, '') ?>">

                  </div>
                </div>

                <div class=" col-md-6 opacity">
                  <div class="form-group">
                    <label for="country">Nationality<span class="required-mark">*</span></label>
                    <select class="form-control required" name="birth_nationality">
                      <option value="">--Choose country--</option>
                      <option value="178" selected>
                        RWANDA</option>
                      <?php
                      foreach ($countries as $key => $country) {
                      ?>

                        <option value="<?= $country['id'] ?>" <?= $country['id'] == output::print("birth_nationality", $row) ? "selected" : '' ?>>
                          <?= $country['name'] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 otherIfAny opacity">
                  <div class="form-group">
                    <label for="country">Other Nationality</label>
                    <select class="form-control" name="other_nationality">
                      <option value="0">--Choose country--</option>
                      <?php
                      foreach ($countries as $key => $country) {
                      ?>
                        <option value="<?= $country['id'] ?>" <?= $country['id'] == output::print("other_nationality", $row) ? "selected" : '' ?>>
                          <?= $country['name'] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4 opacity ">
                  <div class="form-group">
                    <label for="location">Isibo <span class="required-mark">*</span></label>
                    <input type="text" name="isibo" class="form-control " maxlength="50" placeholder="isibo" value="<?= output::print("isibo", $row, "") ?>">
                  </div>
                </div>
                <div class="col-md-4 opacity">
                  <div class="form-group">
                    <label for="location">Profession <span class="required-mark">*</span></label>
                    <input type="text" name="occupation" class="form-control" maxlength="50" value="<?= output::print("occupation", $row, "") ?>">
                  </div>
                </div>
                <div class="col-md-4 opacity ">
                  <div class="form-group">
                    <label for="location">Level of education <span class="required-mark">*</span></label>
                    <select name="level_education" class="form-control" value="">
                      <option value="">Choose</option>
                      <option value="abanza" <?= isset($diplomat) && "abanza" == output::print("level_education", $row, "") ? 'selected' : '' ?>>Primary schools </option>
                      <option value="rusange " <?= isset($diplomat) && "abatarize" == output::print("level_education", $row, "") ? 'selected' : '' ?>>General education schools</option>
                      <option value="ayisumbuye" <?= isset($diplomat) && "ayisumbuye" == output::print("level_education", $row, "") ? 'selected' : '' ?>>High School </option>
                      <option value="imyuga" <?= isset($diplomat) &&  "imyuga" == output::print("level_education", $row, "") ? 'selected' : '' ?>>Vocational schools</option>
                      <option value="kaminuza" <?= isset($diplomat) &&  "kaminuza" == output::print("level_education", $row, "") ? 'selected' : '' ?>>Colleges and universities </option>
                      <option value="abatarize" <?= isset($diplomat) &&  "abatarize" == output::print("level_education", $row, "") ? 'selected' : '' ?>>Not going to school</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12 opacity">
                  <div class="row">
                    <div class="col-md-6 ">
                      <div class="form-group">
                        <label for="location">House rent <span class="required-mark">*</span></label>
                        <select name="rent_house" class="form-control" onchange="checkRent(this);">
                          <option value="">Choose</option>
                          <option value="yego" <?= isset($diplomat) && "yego" == output::print("rent_house", $row) ? 'selected' : '' ?>>Yes</option>
                          <option value="hoya" <?= isset($diplomat) && "hoya" == output::print("rent_house", $row) ? 'selected' : '' ?>>No</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 display-none owner_house ">
                      <div class="form-group">
                        <label for="location">Number of rents <span class="required-mark">*</span></label>
                        <input type="number" placeholder="Eg:1" name="number_house" class="form-control" value="<?= output::print("number_house", $row, "") ?>" id="nh" />
                      </div>
                    </div>
                    <div class="col-md-6 display-none owner_house_info">
                      <div class="form-group">
                        <label for="location">
                          Owner (ID / passport / names / phone)<span class="required-mark">*</span></label>
                        <input type="text" class="form-control ownerInfo" name="house_info" placeholder="Eg:1199608123820" value="<?= output::print("house_info", $row, "") ?>" id="ni" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="location">Email</label>
                    <input type="text" name="email" class="form-control required" maxlength="50" placeholder="Email" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                              echo $row['email'];
                                                                                                                            }  ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Phone</label>
                    <input type="text" class="form-control" name="phone" maxlength="20" placeholder="Phone number" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                            echo $row['phone'];
                                                                                                                          } ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Number of family members<span class="required-mark">*</span></label>
                    <input type="number" class="form-control" name="members" maxlength="20" placeholder="family members" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                  echo $row['members'];
                                                                                                                                }  ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Ubudehe<span class="required-mark">*</span></label>
                    <input type="number" class="form-control ubudehe" name="ubudehe" maxlength="1" min="1" max="4" placeholder="Ikiciro cy'ubudehe " value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                              echo $row['ubudehe'];
                                                                                                                                                            }  ?>">
                  </div>
                </div>

                <div class="col-lg-12 text-center mt-20 mb-50 ">
                  <div class="errors"></div>
                  <button type="button" name="save_diplomat" class="btn fs-15 pt-5 pb-5 w-50p btn-primary save_diplomat">Confirm</button>
                </div>
                <div class="col-md-6 text-center mt-20 mb-50 responseHolder">

                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- modal used for showing people movement -->
  <div class="modal " tabindex="-1" role="dialog" id="modalMovement" data-backdrop="false">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"> Migration <b id="mtitle"></b></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body contentHolder">
          <!-- <p>Modal body text goes here.</p> -->
        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
          <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('contentHolder').html('')">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- end of modal -->
  <script type="text/javascript">
    $('.datepicker').datepicker({
      endDate: '0d',
      format: 'yyyy-mm-dd'
    });

    $('.date').datepicker({
      singleDatePicker: true,
    });
  </script>


  <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/bootstrap-datepicker.js"></script>
  <script src="assets/js/plugins.js"></script>
  <script src="js/ajax.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="js/generic.js"></script>
  <script>
    $(".datepickerDOB").datepicker({
      format: 'yyyy-mm-dd',
      beforeShowDay: function(date) {
        var dte = new Date().getFullYear();
        if (Number(date.getFullYear()) > Number(dte) - 16) {
          return false;
        } else {
          return true;

        }
      }
    });
    $(".datepickerEP").datepicker({
      format: 'yyyy-mm-dd',
      beforeShowDay: function(date) {
        var dte = new Date().getFullYear();
        if (Number(date.getFullYear()) < Number(dte)) {
          return false;
        } else {
          return true;

        }
      }
    });
    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd'
    });
    $('.datepicker1').datepicker({
      format: 'yyyy-mm-dd'
    });
    $(".datepickerGP").datepicker({
      format: 'yyyy-mm-dd',
      beforeShowDay: function(date) {
        var dte = new Date().getFullYear();
        if (Number(date.getFullYear()) > Number(dte)) {
          return false;
        } else {
          return true;

        }
      }
    });
    $('#modalMovement').on('hidden.bs.modal', function(e) {
      $(".contentHolder").html("");
    })
  </script>


</body>

</html>
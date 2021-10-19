<?php
require_once("includes/validate_credentials.php");
if (!input::required(array('dpl'))) {
  redirect::to("404");
}
$diplomat = encrypt_decrypt('decrypt', $_GET['dpl']);
if (!isset($diplomat) || !is_numeric($diplomat)) {
  redirect::to("404");
}
require_once "model/family.php";
require_once "model/country.php";
$hashedLocation = input::enc_dec("d", session::get("userLocation"));
if (isset($_GET['kd'])) {
  $kid = encrypt_decrypt('decrypt', $_GET['kd']);
}
// if (isset($kid) && !is_numeric($kid)) {
//   $dpl = rawurlencode(encrypt_decrypt('encrypt', $diplomat));
//   header("location:add-spouse?dpl=$dpl");
// }

?>
<!doctype html>
<html class="no-js" lang="">
<!--<![endif]-->

<head>
  <?php require_once("includes/head.php"); ?>
  <link rel="stylesheet" href="css/add-member.css">
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
          $text = "Registration in the ";
          if (isset($kid) && is_numeric($kid)) {
            $row = family::getMember($database, $kid, "members");
            $text = "Editing information related to ";
          }
          ?>

          <div class="tab-content-body">
            <h4 class="text-center mt-20 fs-18"><?= $text ?> <span style="color:green;">
                <?php echo " {$row['given_name']} {$row['family_name']} "; ?></span>
              's family

              <hr class="mt-20 mb-20">
            </h4>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form">
              <?php if (isset($kid) && is_numeric($kid)) { ?>
                <input type="hidden" name="id_to_edit" value="<?php echo $kid; ?>" />
              <?php } ?>

              <?php if (isset($diplomat) && is_numeric($diplomat)) { ?>
                <input type="hidden" name="diplomat" value="<?php echo $diplomat; ?>" />
                <!-- <input type="hidden" name="action" value="addmember_to_family" id="txtAction"> -->
              <?php } ?>
              <input type="hidden" name="action" value="member_to_family" id="txtAction">
              <input type="hidden" name="user_loc" value="<?= $hashedLocation ?>">
              <input type="hidden" name="members" value="<?= output::print("members", $row, "0") ?>" />
              <input type="hidden" name="pid" value="<?= $diplomat ?>" />
              <input type="hidden" name="transfer" value="no" />
              <input type="hidden" name="table" value="members" />
              <input type="hidden" name="tid" value="0" />
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="relation">Relationship<span class="required-mark">*</span></label>
                    <select class="form-control" name="relationship" onchange="checkRelationShip(this)" id="s_relation">
                      <option value="">--Choose--</option>>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['relationship'] == 'Wife') {
                                echo "selected";
                              }  ?> value="Wife">spouse</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['relationship'] == 'Wife') {
                                echo "selected";
                              } ?> value="Kid">Kid</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['relationship'] == 'brother') {
                                echo "selected";
                              } ?> value="brother">Brother</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['relationship'] == 'friend') {
                                echo "selected";
                              } ?> value="friend">Friend</option>

                      <option value="Umubyeyi">Mother/Father</option>

                      <option <?php if (isset($kid) && is_numeric($kid) && $row['relationship'] == 'House worker') {
                                echo "selected";
                              } ?> value="House worker">House worker</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['relationship'] == 'Visitor') {
                                echo "selected";
                              }  ?> value="Visitor">Visitor</option>
                      <!-- <option <?php if (isset($kid) && is_numeric($kid) && $row['relationship'] == 'Partner') {
                                      echo "selected";
                                    } ?> value="Partner">Undi muntu</option> -->
                    </select>
                  </div>
                </div>
                <div class="col-md-6 doctypes">
                  <div class="form-group">
                    <label for="doctype">Documents<span class="required-mark">*</span></label>
                    <select name="doctype" class="form-control required" id="doctype" disabled onchange="checkDocType(this);">
                      <option value=""> -- Choose --</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['type'] == 'ID') {
                                echo "selected";
                              } ?> value="ID">ID</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['type'] == 'PASSPORT') {
                                echo "selected";
                              } ?> value="PASSPORT">Passport</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['type'] == 'NONE') {
                                echo "selected";
                              } ?>value="NONE">No Documents</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4 what_relation display-none">
                  <div class="form-group">
                    <label for="what_rel"> How do you live together?<span class="required-mark">*</span></label>
                    <input type="text" class="form-control" maxlength="100" placeholder="urugero:umuvandimwe" id="what_rel" name="what_relationship" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                              echo $row['what_relationship'];
                                                                                                                                                            } ?>">
                  </div>
                </div>

                <div class="col-lg-4 hide-all">
                  <div class="form-group">
                    <label for="name">Given name<span class="required-mark">*</span></label>
                    <input type="text" class="form-control" maxlength="100" placeholder="Given name" name="given_name" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                echo $row['given_name'];
                                                                                                                              } ?>">
                  </div>
                </div>

                <div class="col-lg-4 hide-all">
                  <div class="form-group">
                    <label for="name">Family Name<span class="required-mark">*</span></label>
                    <input type="text" class="form-control" maxlength="100" placeholder="Family Name" name="family_name" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                  echo $row['family_name'];
                                                                                                                                }  ?>">
                  </div>
                </div>
                <div class="col-lg-4 hide-all">
                  <div class="form-group">
                    <label for="name">Other Name</label>
                    <input type="text" class="form-control" maxlength="100" placeholder="Other names" name="other_name" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                  echo $row['other_name'];
                                                                                                                                }  ?>">
                  </div>
                </div>


                <div class="col-lg-4 hide-all">
                  <div class="form-group">
                    <label for="gender">Gender<span class="required-mark">*</span></label>
                    <select name="gender" class="form-control" id="gender">
                      <option value="">--SELECT--</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['gender'] == 'Male') {
                                echo "selected";
                              } ?> value="Male">Male</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['gender'] == 'Female') {
                                echo "selected";
                              } ?> value="Female">Female</option>

                      <option <?php if (isset($kid) && is_numeric($kid) && $row['gender'] == 'Other') {
                                echo "selected";
                              } ?> value="Other">Other</option>
                    </select>
                  </div>

                </div>
                <div class="col-lg-4 hide-all">

                  <div class="form-group date">
                    <label>
                      Date of birth<span class="required-mark">*</span></label>
                    <input type="text" maxlength="20" autocomplete="off" placeholder="YYYY-mm-dd" class="form-control datepicker" name="dob" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                      echo $row['dob'];
                                                                                                                                                    } ?>">
                  </div>

                </div>
                <div class="col-lg-4 hide-all">

                  <div class="form-group">
                    <label for="contact_name">Place of birth</label>
                    <input type="text" class="form-control" maxlength="255" placeholder="Enter Place Of Birth" name="birth_place" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                            echo $row['birth_place'];
                                                                                                                                          }  ?>">
                  </div>

                </div>

                <div class="col-md-6 hide-all">
                  <?php $countries = Country::getAllCountry($database); ?>
                  <div class="form-group">
                    <label for="country">Nationality<span class="required-mark">*</span></label>
                    <select class="form-control" name="birth_nationality">
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

                <div class="col-md-6 otherIfAny hide-all">
                  <div class="form-group">
                    <label for="country">Other nationality</label>
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

                <div class="col-lg-12 passport display-none">
                  <fieldset class="fiedset-type">
                    <legend class="fieldset-legend">Passport</legend>
                    <div class="col-lg-6">
                      <?php
                      if (isset($row['type']) && $row['type'] == 'PASSPORT') {
                        $pass_port = $row['document_id'];
                      } else {
                        $pass_port = "";
                      }
                      ?>
                      <div class="form-group">
                        <input type="text" class="form-control" name="passport" maxlength="30" onkeyup="validateIDorPassport(this,'PASSPORT')" placeholder="Nimero" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                                              echo $pass_port;
                                                                                                                                                                            }  ?>">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <select class="form-control" name="issued_country">
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
                    <div class="col-lg-6">
                      <div class="form-group">
                        <input type="text" maxlength="20" autocomplete="off" placeholder="Issued date" class="form-control datepicker" name="issued_date" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                                    echo $row['issued_date'];
                                                                                                                                                                  }  ?>">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <input type="text" maxlength="20" autocomplete="off" placeholder="Expired date" class="form-control datepicker1" name="expiry_date" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                                      echo $row['expiry_date'];
                                                                                                                                                                    } ?>">
                      </div>
                    </div>

                  </fieldset>

                </div>
                <div class="col-lg-12  nid display-none">
                  <div class="form-group">
                    <?php
                    if (isset($row['type']) && $row['type'] == 'ID') {
                      $rwanda_id  = $row['document_id'];
                    } else {
                      $rwanda_id = "";
                    }
                    ?>
                    <label for="location">ID</label>
                    <input type="text" name="rwandan_id" class="form-control" placeholder="ID number" maxlength="16" onkeyup="onlyNumber(this);validateIDorPassport(this,'ID')" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                                                          echo $rwanda_id;
                                                                                                                                                                                        }  ?>">
                  </div>
                </div>
                <div class="col-md-12 display-none dvVisitor">
                  <div class="row">
                    <div class="col-md-3">
                      <label>Arrival Date<span class="required-mark">*</span></label>
                      <div class="form-group">
                        <input type="text" maxlength="20" autocomplete="off" placeholder="Itariki yo kuhagera" class="form-control datepicker1" name="arrival_date" value="" />
                      </div>
                    </div>
                    <div class=" col-md-3">
                      <label>
                        Where he/she comes from<span class="required-mark">*</span></label>
                      <div class="form-group">
                        <select class="form-control" name="come_from" value="">
                          <option value="RWANDA"> RWANDA</option>
                          <option value="hanze">
                            Outside of Rwanda</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <label>Name of origin<span class="required-mark">*</span></label>
                      <div class="form-group">
                        <input type="text" maxlength="20" autocomplete="off" placeholder="urugero:Gasabo" class="form-control " name="place_name" value="" />
                      </div>
                    </div>
                    <div class="col-md-3">
                      <label>Date of departure<span class="required-mark">*</span></label>
                      <div class="form-group">
                        <input type="text" maxlength="20" autocomplete="off" placeholder="Date of departure" class="form-control datepicker1" name="departure_date" value="" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 hide-all">
                  <div class="form-group">
                    <label for="location">Profession <span class="required-mark">*</span></label>
                    <input type="text" name="occupation" class="form-control" maxlength="50" placeholder="Eg:to trade
" value="<?= isset($kid) ? output::print("occupation", $row, "") : '' ?>">
                  </div>
                </div>
                <div class="col-md-6 hide-all ">
                  <div class="form-group">
                    <label for="location">Level of education <span class="required-mark">*</span></label>
                    <select name="level_education" class="form-control" value="">
                      <option value="">Choose</option>
                      <option value="abanza" <?= isset($kid) && "abanza" == output::print("level_education", $row, "") ? 'selected' : '' ?>>Primary schools </option>
                      <option value="rusange " <?= isset($kid) && "abatarize" == output::print("level_education", $row, "") ? 'selected' : '' ?>>General education schools</option>
                      <option value="ayisumbuye" <?= isset($kid) && "ayisumbuye" == output::print("level_education", $row, "") ? 'selected' : '' ?>>High School </option>
                      <option value="imyuga" <?= isset($kid) &&  "imyuga" == output::print("level_education", $row, "") ? 'selected' : '' ?>>Vocational schools</option>
                      <option value="kaminuza" <?= isset($kid) &&  "kaminuza" == output::print("level_education", $row, "") ? 'selected' : '' ?>>Colleges and universities </option>
                      <option value="abatarize" <?= isset($kid) &&  "abatarize" == output::print("level_education", $row, "") ? 'selected' : '' ?>>Not going to school</option>
                    </select>
                  </div>
                </div>
                <div class="col-lg-6 hide-all">
                  <div class="form-group">
                    <label for="location">Email</label>
                    <input type="text" name="email" class="form-control" maxlength="50" placeholder="Email" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                      echo $row['email'];
                                                                                                                    }  ?>">
                  </div>
                </div>
                <div class="col-lg-6 hide-all">
                  <div class="form-group">
                    <label for="name">Phone</label>
                    <input type="text" class="form-control" name="phone" maxlength="20" placeholder="Phone" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                      echo $row['phone'];
                                                                                                                    }  ?>">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="errors"></div>
                </div>
                <div class="col-lg-4 col-lg-offset-4 mt-10">
                  <button type="button" name="save_kid" class="btn w-100p pt-10 pb-10 fs-15 pull-right btnSaveMember" disabled>Emeza</button>
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
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- end of modal -->
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
    $('#modalMovement').on('hidden.bs.modal', function(e) {
      $(".contentHolder").html("");
    })
  </script>

</body>

</html>
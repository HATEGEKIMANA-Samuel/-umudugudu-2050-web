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

<body oncontextmenu="return false">
  <?php require_once 'includes/left_nav.php'; ?>
  <!-- this div will be visible wheneever head og family want to migrate -->
  <div class="emeza-wrapper d-none">
    <div class="emeza-container">
      <span class="closeEmezaWrapper"> &larr; Subira Inyuma</span>
      <h1 class="q1">Wimukanye nande?</h1>
      <div class="mb-20 q1-options">
        <label class="radio radio-gradient" style="margin-right: 20px;">
          <span class="radio__input">
            <input type="radio" class="kwinuka" value="alone" name="kwimuka[]">
            <span class="radio__control"></span>
          </span>
          <span class="radio__label">Njyenyine gusa</span>
        </label>
        <label class="radio radio-gradient">
          <span class="radio__input">
            <input type="radio" class="kwinuka" value="all" name="kwimuka[]">
            <span class="radio__control"></span>
          </span>
          <span class="radio__label">Numuryango wanjye wose</span>
        </label>
      </div>
      <!-- hidden input -->
      <input type="hidden" name="selectedmember" id="selectedmember" value="all">
      <div class="q2-options">
        <!-- DATA ARE COMMING FROM SCRIPT AT THE END OF THIS PAGE -->
      </div>
      <div class="mt-40">
        <button disabled="disabled" class="emeza emeza-button w-100p b-radius-button" type="button">Komeza</button>
      </div>
    </div>
  </div>

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
            <h4 class="fs-18 mt-40 fw-500 text-center ">Umwirondoro - Umukuru w'umuryango
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
              <input type="hidden" name="added_remotely" id="added_remotely" value="no" readonly />
              <?php if (isset($diplomat) && is_numeric($diplomat)) { ?>
                <input type="hidden" name="id_to_edit" value="<?php echo $diplomat; ?>" />
              <?php } ?>
              <input type="hidden" name="user_loc" value="<?= $hashedLocation ?>">
              <div class="row">
                <div class="col-md-6 doctypes">
                  <div class="form-group">
                    <label for="doctype">Icyangobwa<span class="required-mark">*</span></label>
                    <select name="doctype" class="form-control required" id="doctype" onchange="checkDocType(this);">
                      <option value=""> -- Hitamo --</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['documentType'] == 'ID') {
                                echo "selected";
                              } else {
                                echo "selected";
                              } ?> value="ID">Indangamuntu</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['documentType'] == 'PASSPORT') {
                                echo "selected";
                              } ?> value="PASSPORT">Urupapuro rw'inzira</option>
                      <!-- <option value="unknown">Ntacyangobwa afite</option> -->
                    </select>
                  </div>
                </div>
                <div class="col-md-6 divid">
                  <div class="form-group">
                    <?php
                    if (isset($row['documentType']) && $row['documentType'] == 'ID') {
                      $rwanda_id  = $row['documentNumber'];
                    } else {
                      $rwanda_id = "";
                    }
                    ?>
                    <label for="location">Indangamuntu y'u Rwanda <span class="required-mark">*</span></label>
                    <input type="text" name="rwandan_id" class="form-control rwandanId" onkeyup="onlyNumber(this);
                    checkMember($(this).val().trim(),'ID');" placeholder="Andika Indangamuntu hano" maxlength="16" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                            echo $rwanda_id;
                                                                                                                          } ?>">
                  </div>
                </div>
                <div class="col-md-12 divpp display-none">
                  <fieldset class="fiedset-type pb-20">
                    <legend class="fieldset-legend"><span class="legendTitle"> Urupapuro rw'inzira</span></legend>
                    <div class="col-md-6">
                      <div class="form-group">
                        <?php
                        if (isset($row['documentType']) && $row['documentType'] == 'PASSPORT') {
                          $pass_port = $row['documentNumber'];
                        } else {
                          $pass_port = "";
                        }
                        ?>

                        <input type="text" class="form-control required" name="passport" maxlength="30" onchange="validateIDorPassport(this,'PASSPORT');" placeholder="Nomero ya pasiporo *" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
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
                          <option value="">--Igihugu cyayitanze--</option>
                          <?php
                          $issued_country = output::print("issuedCountry", $row);
                          foreach ($countries as $key => $country) {
                            $selected = $country['id'] == $issued_country ? "selected" : '';
                            if (!is_numeric($issued_country)) {
                              $selected = $country['name'] == $issued_country ? "selected" : '';
                            }
                          ?>
                            <option value="<?= $country['id'] ?>" <?= $selected ?>>
                              <?= $country['name'] ?></option>
                          <?php
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <input type="text" maxlength="20" autocomplete="off" placeholder="Igihe yatanzwe" class="form-control datepickerGP required" name="issued_date" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                                                  echo $row['issuedDate'];
                                                                                                                                                                                } ?>">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <input type="text" axlength="20" autocomplete="off" placeholder="Igihe izarangira" class="form-control datepickerEP required" name="expiry_date" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                                                  echo $row['expiryDate'];
                                                                                                                                                                                }  ?>">
                      </div>
                    </div>

                  </fieldset>
                </div>
                <div class="col-md-6 opacity">

                  <div class="form-group">
                    <label for="name">Izina<span class="required-mark">*</span></label>
                    <input type="text" class="form-control required" maxlength="100" placeholder="Izina " name="given_name" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                      echo $row['givenName'];
                                                                                                                                    } elseif (isset($_POST['given_name'])) {
                                                                                                                                      echo $_POST['given_name'];
                                                                                                                                    } ?>">
                  </div>

                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Izina ry'umuryango<span class="required-mark">*</span></label>
                    <input type="text" class="form-control required" maxlength="100" placeholder="Izina ry'umuryango" name="family_name" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                  echo $row['familyName'];
                                                                                                                                                } ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Andi mazina</label>
                    <input type="text" class="form-control" maxlength="100" placeholder="Andi mazina" name="other_name" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                  echo $row['otherName'];
                                                                                                                                } ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="gender">Igitsina<span class="required-mark">*</span></label>
                    <select name="gender" class="form-control required" id="gender">
                      <option value=""> -- Hitamo --</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['gender'] == 'Male') {
                                echo "selected";
                              } ?> value="Male">Gabo</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['gender'] == 'Female') {
                                echo "selected";
                              } ?> value="Female">Gore</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['gender'] == 'Other') {
                                echo "selected";
                              } ?> value="Female">Ibindi</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group date">
                    <label>Itariki yamavuko<span class="required-mark">*</span></label>
                    <input type="date" maxlength="20" autocomplete="off" placeholder="YYYY-mm-dd" pattern="\d{4}-\d{2}-\d{2}" class="form-control  required" name="dob" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                                                  echo $row['dob'];
                                                                                                                                                                                } ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Irangamimerere<span class="required-mark">*</span></label>
                    <select class="form-control required" name="marital_status">
                      <option value=""> -- Hitamo --</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['martialstatus'] == 'Ingaragu') {
                                echo "selected";
                              }  ?> value="Ingaragu">Ingaragu</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['martialstatus'] == 'Yarashatse') {
                                echo "selected";
                              } ?> value="Yarashatse">Yarashyingiwe</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['martialstatus'] == 'Yatse gatanya') {
                                echo "selected";
                              }  ?> value="Yatse gatanya">Yatse gatanya</option>
                      <option <?php if (isset($diplomat) && is_numeric($diplomat) && $row['martialstatus'] == 'Umupfakazi') {
                                echo "selected";
                              }  ?> value="Umupfakazi">Umupfakazi</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="contact_name">Aho yavukiye<span class="required-mark">*</span></label>
                    <input type="text" class="form-control required" maxlength="255" placeholder="Aho yavukiye" name="birth_place" value=" <?= output::print("birthplace", $row, '') ?>" onfocusout="recoverData();">
                  </div>
                </div>

                <div class=" col-lg-12" style="position: relative;">
                  <!-- To display add show-container class -->
                  <div class="suggestion-container">
                    <div class="suggestion-header">
                      <h1>Gushakisha</h1>
                      <span class="closeX close-suggestion">x</span>
                    </div>
                    <table class="table table-bordered table-condensed fs-14">
                      <thead>
                        <th>#</th>
                        <th>Amazina</th>
                        <th>
                          Uhagarariye umuryango</th>
                        <th>Aho Atuye</th>
                        <th>Ibindi</th>
                      </thead>
                      <tbody id="suggestionResult">
                        <!-- <td>Ask4Gilbert Niyonsaba</td>
                        <td>Kibuye</td>
                        <td><button class="btn btn-primary fs-13">Niwe &rarr;</button></td> -->
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class=" col-md-6 opacity">
                  <div class="form-group">
                    <label for="country">Ubwenegihugu<span class="required-mark">*</span></label>
                    <select class="form-control required" name="birth_nationality">
                      <option value="">--Hitamo Igihugu--</option>
                      <option value="178" selected>
                        RWANDA</option>
                      <?php
                      $issued_country = output::print("birthNationality", $row);
                      foreach ($countries as $key => $country) {
                        $selected = $country['id'] == $issued_country ? "selected" : '';
                        if (!is_numeric($issued_country)) {
                          $selected = $country['name'] == $issued_country ? "selected" : '';
                        }
                      ?>
                        <option value="<?= $country['id'] ?>" <?= $selected ?>>
                          <?= $country['name'] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 otherIfAny opacity">
                  <div class="form-group">
                    <label for="country">Ubundi Bwenegihugu</label>
                    <select class="form-control" name="other_nationality">
                      <option value="0">--Hitamo Igihugu--</option>
                      <?php
                      $issued_country = output::print("otherNationality", $row);
                      foreach ($countries as $key => $country) {
                        $selected = $country['id'] == $issued_country ? "selected" : '';
                        if (!is_numeric($issued_country)) {
                          $selected = $country['name'] == $issued_country ? "selected" : '';
                        }
                      ?>
                        <option value="<?= $country['id'] ?>" <?= $selected ?>>
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
                    <label for="location">Umwuga <span class="required-mark">*</span></label>
                    <input type="text" name="occupation" class="form-control" maxlength="50" value="<?= output::print("occupation", $row, "") ?>">
                  </div>
                </div>
                <div class="col-md-4 opacity ">
                  <div class="form-group">
                    <label for="location">Icyiciro cy'Amashuri <span class="required-mark">*</span></label>
                    <select name="level_education" class="form-control" value="">
                      <option value="">hitamo</option>
                      <option value="abanza" <?= "abanza" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>Amashuri abanza </option>
                      <option value="rusange " <?= "abatarize" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>icyiciro rusange</option>
                      <option value="ayisumbuye" <?= "ayisumbuye" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>Amashuri yisumbuye </option>
                      <option value="imyuga" <?= "imyuga" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>Amashuri y' imyuga </option>
                      <option value="kaminuza" <?= "kaminuza" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>Amashuri makuru na kaminuza </option>
                      <option value="abatarize" <?= "abatarize" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>Ntago yize</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12 opacity">
                  <div class="row">
                    <div class="col-md-6 ">
                      <div class="form-group">
                        <label for="location">Arakodesha <span class="required-mark">*</span></label>
                        <select name="rent_house" id="rent_house_selector" class="form-control" onchange="checkRent(this);">
                          <option value="">hitamo</option>
                          <option value="yego" <?= !empty($row['landLoard']) ? 'selected' : '' ?>>Yego</option>
                          <option value="hoya" <?= empty($row['landLoard']) ? 'selected' : '' ?>>Hoya</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 display-none owner_house ">
                      <div class="form-group">
                        <label for="location">Umubare w'inzu ukodesha <span class="required-mark">*</span></label>
                        <input type="number" placeholder="urugero:1" name="number_house" class="form-control" value="<?= output::print("number_of_rent_house", $row, "") ?>" id="nh" />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <!-- <span class="required-mark">*</span> -->
                        <label for="upi">Nimero y'ikibanza (UPI) </label>
                        <input placeholder="urugero:1" id="upi" name="upi" class="form-control" value="<?= output::print("upi", $row, "") ?>" />
                      </div>
                    </div>

                    <div class="col-md-6 display-none owner_house_info">
                      <div class="form-group">
                        <label for="location"> Nyirinzu(indangamuntu/pasiporo/amazina/telefone)<span class="required-mark">*</span></label>
                        <input type="text" class="form-control ownerInfo" name="house_info" placeholder="Urugero:1199608123820" value="<?= output::print("landLord", $row, "") ?>" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="location">Emeli</label>
                    <input type="text" name="email" class="form-control required" maxlength="50" placeholder="Email" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                              echo $row['email'];
                                                                                                                            }  ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Telefoni</label>
                    <input type="text" class="form-control" name="phone" maxlength="20" placeholder="Telephone" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                          echo $row['mobile'];
                                                                                                                        } ?>">
                  </div>
                </div>

                <div class="col-md-6 opacity">
                  <div class="form-group">
                    <label for="name">Ubudehe<span class="required-mark">*</span></label>
                    <select name="ubudehe" id="ubudehe-selector" class="form-control required">
                      <option value=""> -- Hitamo --</option>
                      <option <?= isset($diplomat) && is_numeric($diplomat) && $row['ubudehe'] == 'A' ? "selected" : null ?>>A</option>
                      <option <?= isset($diplomat) && is_numeric($diplomat) && $row['ubudehe'] == 'B' ? "selected" : null ?>>B</option>
                      <option <?= isset($diplomat) && is_numeric($diplomat) && $row['ubudehe'] == 'C' ? "selected" : null ?>>C</option>
                      <option <?= isset($diplomat) && is_numeric($diplomat) && $row['ubudehe'] == 'D' ? "selected" : null ?>>D</option>
                      <option <?= isset($diplomat) && is_numeric($diplomat) && $row['ubudehe'] == 'E' ? "selected" : null ?>>E</option>
                    </select>
                  </div>
                </div>

                <div class="col-lg-12 text-center mt-20 mb-50 ">
                  <div class="errors"></div>
                  <button type="button" name="save_diplomat" class="btn fs-15 pt-5 pb-5 w-50p btn-primary save_diplomat">Komeza</button>
                  <!-- JS TO SHOW DIV CONTAINER IS AT THE END OF THIS PAGE -->
                  <!-- <button type="button" class="btn showEmezaWrapperBtn fs-15 pt-5 pb-5 w-50p btn-primary confirm_move">Add</button> -->
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
  <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/bootstrap-datepicker.js"></script>
  <script src="assets/js/plugins.js"></script>
  <script src="js/ajax.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="js/generic.js"></script>
  <script>
    $('.close-suggestion').click(function() {
      $('.suggestion-container').removeClass('show-container')
    })
    // fillForm is located in member.js
    // check if member is already in system before getting his/her document
    function recoverData() {
      // check if it is transfer 
      var docType = $("#doctype").val();
      let docNumber = "";
      if (docType === "ID") {
        docNumber = $("input[name='rwandan_id']").val();
      } else if (docType === "PASSPORT") {
        docNumber = $("input[name='passport']").val();
      }
      let is_transfer = $("input[name='transfer'").val();
      let dob = $("input[name='dob'").val();
      let names = $("input[name='given_name']").val() + '' + $("input[name='family_name']").val() + $("input[name='other_name']").val();
      if (is_transfer == "no") {
        $(".btnSaveMember").attr("disabled", "disabled");
        if (names.length == 0) return;
        $('.suggestion-container').addClass('show-container');
        showWait("#suggestionResult");
        // + $("input[name='birth_place']").val()
        let keywords = names + '' + dob;
        $.post(
          "controller/familyController.php", {
            action: "check_new_document_in_system",
            key_words: keywords,
            documentNumber: docNumber,
          },
          function(res) {
            if (res.total > 0) {
              $("#suggestionResult").html(res.data);
              return;
            }
            $(".close-suggestion").click();
            $(".btnSaveMember").removeAttr("disabled");
          }, "json");
      }
    }
    // confirm suggestion
    function confirmSuggestion(data) {
      $("#txtAction").val("member_to_family");
      $("input[name='transfer'").attr("value", "yes");
      fillForm(data);
      $(".errors").html("");
      // close popup
      $(".close-suggestion").click();
      $(".btnSaveMember").removeAttr("disabled");
      // $(".btnSaveMember").click();
    }
    $(".datepickerDOB").datepicker({
      format: 'yyyy-mm-dd',
      beforeShowDay: function(date) {
        return true
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
        return true;
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
        return true;
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


    function validateIDorPassport(e, docType = "ID") {
      var value = $(e).val().trim();
      if (docType === "ID" && value.length === 16) {
        $(".btnSaveMember").removeAttr("disabled", "disabled");
      } else if (docType === "PASSPORT" && value.length > 0) {
        $(".save_diplomat").removeAttr("disabled", "disabled");
      } else {
        $(".btnSaveMember").attr("disabled", "disabled");
      }
    }
    var option = "";
    $('.kwinuka').change(function() {
      option = $(this).attr('value');
      if ($.trim(option) === 'alone') {
        option = "alone";
        // members  is global grobal  declared in generic.js
        let html = displayMembers(members);
        $('.q2-options').html(html);
        $("#selectedmember").val('');
        $('.emeza-button').removeAttr('disabled');
      } else {
        option = "family";
        $('.q2-options').html(' ')
        $('.emeza-button').removeAttr('disabled')
      }
    });

    // confirm 
    $(".emeza-button").click(function() {
      if (option == "alone") {
        let family = $("#selectedmember").val();
        let fam = family.split(",");
        if (typeof fam[2] === "undefined") return;
        if (confirm(` Uremezako ${fam[2]} aba umukuru y'umuryango`)) {
          showWait(this);
          $(this).attr("disabled", "disabled");
          $.post(
            "controller/familyController.php", {
              action: "changefamilyhead",
              head: fam[1],
              new_head: fam[0],
              migration: "yes"
            },
            function(res) {
              stopWait();
              if (res.status) {
                $(".errors").html("");
                $('.emeza-wrapper').addClass('d-none')
                $("#added_remotely").val("no");
                $(".opacity").removeClass("opacity").addClass("show-forminputs");
                $(".txtAction").val("head_of_family");
                $("input[name='transfer'").attr("value", "yes");
                return;
              }
              alert("Ibyo mushaka ntibikunze mwongere mugerageze");
            }, "json");
        }
      } else {
        if (confirm("Uremezako umuryango w'imutse")) {
          $(".errors").html(" ");
          $('.emeza-wrapper').addClass('d-none')
          $("#added_remotely").val("yes");
          $(".txtAction").val("head_of_family");
          $("input[name='transfer'").attr("value", "yes");
          $(".opacity").removeClass("opacity").addClass("show-forminputs");
        }
      }
    })
    //SHOW EMEZA WRAPPER
    $('.showEmezaWrapperBtn').click(function() {
      $('.emeza-wrapper').removeClass('d-none')
    })
    //CLOSE EMEZA WRAPPER
    $('.closeEmezaWrapper').click(function() {
      $('.emeza-wrapper').addClass('d-none')
    })
  </script>
</body>

</html>
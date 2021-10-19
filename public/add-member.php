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
      <div class="tab-container">
        <div class="tab-content mt-10" id="nav-tabContent">

          <?php
          if (isset($diplomat) && is_numeric($diplomat)) {
            $row = family::getMember($database, $diplomat);
          }
          $text = "Kwandika abagize umuryango wa ";
          if (isset($kid) && is_numeric($kid)) {
            $row = family::getMember($database, $kid, "mem");

            // list($visitorArrivingDate, $visitorOriginalCountry, $visitorOrigin, $visitorDepartureDate)
            //   = explode('#', $row["visitor_info"]);
            $text = "Guhindura  amakuru ajyanye na ";
          }
          ?>

          <div class="tab-content-body">
            <h4 class="text-center mt-20 fs-18"><?= $text ?> <span style="color:green;" id="head_family">
                <?php echo "{$row['givenName']} {$row['familyName']}"; ?></span>
              <!-- 's family member --><br />
              <small id="whoSelected"></small>
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
              <input type="hidden" name="table" value="members" readonly id="txtTable" />
              <input type="hidden" name="tid" value="<?= $diplomat ?>" />
              <input type="hidden" name="migration" value="no" readonly />
              <!-- added remotel -->
              <input type="hidden" name="added_remotely" id="added_remotely" value="no" readonly />
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="relation">Isano<span class="required-mark">*</span></label>
                    <select class="form-control" name="relationship" onchange="checkRelationShip(this)" id="s_relation">
                      <option value="">--Hitamo--</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['familyCategory'] == 'Uwo mwashakanye') {
                                echo "selected";
                              }  ?> value="Uwo mwashakanye">Uwo mwashakanye</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['familyCategory'] == 'Umwana') {
                                echo "selected";
                              } ?> value="Umwana">Umwana</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['familyCategory'] == 'Umushyitsi') {
                                echo "selected";
                              }  ?> value="Umushyitsi">Umushyitsi</option>
                      <?php
                      $rel = $row['familyCategory'];
                      if (isset($kid) && is_numeric($kid) && $rel != 'Uwo mwashakanye' && $rel != 'Umushyitsi' && $rel != 'Umwana') {
                      ?>
                        <option value="Other" selected>Irindi sano </option>
                      <?php
                      } else {
                        echo ' <option value="Other">Irindi sano </option>';
                      }
                      ?>

                    </select>
                  </div>
                </div>
                <div class="col-md-6 doctypes">
                  <div class="form-group">
                    <label for="doctype">Icyangobwa<span class="required-mark">*</span></label>
                    <select name="doctype" class="form-control required" id="doctype" disabled onchange="checkDocType(this);">
                      <option value=""> -- Hitamo --</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['documentType'] == 'ID') {
                                echo "selected";
                              } ?> value="ID">Indangamuntu</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['documentType'] == 'PASSPORT') {
                                echo "selected";
                              } ?> value="PASSPORT">Urupapuro rw'inzira</option>
                      <option <?php if ($row['documentType'] == 'NONE') {
                                echo "selected";
                              } ?> value="NONE"> Ntacyangobwa afite </option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 display-none" id="other_relationship">
                  <div class="form-group">
                    <label for="what_relationship">Irindi sano<span class="required-mark">*</span></label>
                    <input type="text" maxlength="20" autocomplete="off" placeholder="urugero:umuvandimwe" id="what_relationship" class="form-control" name="what_relationship" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                                                          echo $row['familyCategory'];
                                                                                                                                                                                        } ?>" />
                  </div>
                </div>


                <div class="col-lg-4 hide-all">
                  <div class="form-group">
                    <label for="name">Izina<span class="required-mark">*</span></label>
                    <input type="text" class="form-control" maxlength="100" placeholder="Izina" name="given_name" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                            echo $row['givenName'];
                                                                                                                          } ?>">
                  </div>
                </div>

                <div class="col-lg-4 hide-all">
                  <div class="form-group">
                    <label for="name">Izina ry'umuryango<span class="required-mark">*</span></label>
                    <input type="text" class="form-control" maxlength="100" placeholder="Izina ry'umuryango" name="family_name" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                          echo $row['familyName'];
                                                                                                                                        }  ?>">
                  </div>
                </div>
                <div class="col-lg-4 hide-all">
                  <div class="form-group">
                    <label for="name">Andi mazina</label>
                    <input type="text" class="form-control" maxlength="100" placeholder="Other names" name="other_name" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                  echo $row['otherName'];
                                                                                                                                }  ?>">
                  </div>
                </div>


                <div class="col-lg-4 hide-all">
                  <div class="form-group">
                    <label for="gender">Igitsina<span class="required-mark">*</span></label>
                    <select name="gender" class="form-control" id="gender">
                      <option value="">--SELECT--</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['gender'] == 'Male') {
                                echo "selected";
                              } ?> value="Male">Gabo</option>
                      <option <?php if (isset($kid) && is_numeric($kid) && $row['gender'] == 'Female') {
                                echo "selected";
                              } ?> value="Female">Gore</option>

                      <option <?php if (isset($kid) && is_numeric($kid) && $row['gender'] == 'Other') {
                                echo "selected";
                              } ?> value="Other">Ibindi</option>
                    </select>
                  </div>

                </div>
                <div class="col-lg-4 hide-all">

                  <div class="form-group date">
                    <label>Itariki yamavuko<span class="required-mark">*</span></label>
                    <input type="date" maxlength="20" autocomplete="off" placeholder="" class="form-control " name="dob" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                  echo $row['dob'];
                                                                                                                                } ?>">
                  </div>

                </div>
                <div class="col-lg-4 hide-all">

                  <div class="form-group">
                    <label for="contact_name">Aho yavukiye</label>
                    <input type="text" class="form-control" id="ahoYavukiye" maxlength="255" placeholder="Enter Place Of Birth" name="birth_place" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                            echo $row['birthplace'];
                                                                                                                                                          }  ?>" onfocusout="recoverData();">
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
                <div class="col-md-6 hide-all">
                  <?php $countries = Country::getAllCountry($database); ?>
                  <div class="form-group">
                    <label for="country">Ubwenegihugu<span class="required-mark">*</span></label>
                    <select class="form-control" name="birth_nationality">
                      <option value="">--Choose country--</option>
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

                <div class="col-md-6 otherIfAny hide-all">
                  <div class="form-group">
                    <label for="country">Ubundi Bwenegihugu</label>
                    <select class="form-control" name="other_nationality">
                      <option value="0">--Choose country--</option>
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

                <div class="col-lg-12 passport display-none">
                  <fieldset class="fiedset-type">
                    <legend class="fieldset-legend">Urupapuro rw'inzira</legend>
                    <div class="col-lg-6">
                      <?php
                      if (isset($row['documentType']) && $row['documentType'] == 'PASSPORT') {
                        $pass_port = $row['documentNumber'];
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
                    <div class="col-lg-6">
                      <div class="form-group">
                        <input type="date" maxlength="20" autocomplete="off" placeholder="Igihe yatanzwe" class="form-control" name="issued_date" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                            echo $row['issued_date'];
                                                                                                                                                          }  ?>">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <input type="date" maxlength="20" autocomplete="off" placeholder="Igihe izarangira" class="form-control" name="expiry_date" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                              echo $row['expiry_date'];
                                                                                                                                                            } ?>">
                      </div>
                    </div>

                  </fieldset>

                </div>
                <div class="col-lg-12  nid display-none">
                  <div class="form-group">
                    <?php
                    if (isset($row['documentType']) && $row['documentType'] == 'ID') {
                      $rwanda_id  = $row['documentNumber'];
                    } else {
                      $rwanda_id = "";
                    }
                    ?>
                    <label for="location">Indangamuntu</label>
                    <input type="text" name="rwandan_id" class="form-control rwandan_id" placeholder="ID number" maxlength="16" onkeyup="onlyNumber(this);validateIDorPassport(this,'ID')" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                                                                                                    echo $rwanda_id;
                                                                                                                                                                                                  }  ?>">
                  </div>
                </div>
                <div class="col-md-6 hide-all">
                  <div class="form-group">
                    <label for="location">Umwuga <span class="required-mark">*</span></label>
                    <input type="text" name="occupation" class="form-control" maxlength="50" placeholder="urugero:gucuruza" value="<?= isset($kid) ? output::print("occupation", $row, "") : '' ?>">
                  </div>
                </div>
                <!-- ubudehe -->
                <div class="col-md-6 hide-all">
                  <div class="form-group">
                    <label for="name">Ubudehe<span class="required-mark">*</span></label>
                    <select name="ubudehe" id="ubudehe-selector" class="form-control required">
                      <option value=""> -- Hitamo --</option>
                      <option <?= isset($kid) && is_numeric($kid) && $row['ubudehe'] == 'A' ? "selected" : null ?>>A</option>
                      <option <?= isset($kid) && is_numeric($kid) && $row['ubudehe'] == 'B' ? "selected" : null ?>>B</option>
                      <option <?= isset($kid) && is_numeric($kid) && $row['ubudehe'] == 'C' ? "selected" : null ?>>C</option>
                      <option <?= isset($kid) && is_numeric($kid) && $row['ubudehe'] == 'D' ? "selected" : null ?>>D</option>
                      <option <?= isset($kid) && is_numeric($kid) && $row['ubudehe'] == 'E' ? "selected" : null ?>>E</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 hide-all ">
                  <div class="form-group">
                    <label for="location">Icyiciro cy'amashuri <span class="required-mark">*</span></label>
                    <select name="level_education" class="form-control" value="">
                      <option value="">hitamo</option>
                      <option value="abanza" <?= isset($kid) && "abanza" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>Amashuri abanza </option>
                      <option value="rusange " <?= isset($kid) &&  "abatarize" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>icyiciro rusange</option>
                      <option value="ayisumbuye" <?= isset($kid) &&  "ayisumbuye" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>Amashuri yisumbuye </option>
                      <option value="imyuga" <?= isset($kid) &&  "imyuga" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>Amashuri y' imyuga </option>
                      <option value="kaminuza" <?= isset($kid) &&  "kaminuza" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>Amashuri makuru na kaminuza </option>
                      <option value="abatarize" <?= isset($kid) &&  "abatarize" == output::print("level_of_education", $row, "") ? 'selected' : '' ?>>Ntago yize</option>
                    </select>
                  </div>
                </div>
                <div class="col-lg-6 hide-all">
                  <div class="form-group">
                    <label for="location">Emeli</label>
                    <input type="text" name="email" class="form-control" maxlength="50" placeholder="Email" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                      echo $row['email'];
                                                                                                                    }  ?>">
                  </div>
                </div>
                <div class="col-lg-6 hide-all">
                  <div class="form-group">
                    <label for="name">Telefone</label>
                    <input type="text" class="form-control" name="phone" maxlength="20" placeholder="Telephone" value="<?php if (isset($kid) && is_numeric($kid)) {
                                                                                                                          echo $row['mobile'];
                                                                                                                        }  ?>">
                  </div>
                </div>
                <div class="col-md-12 display-none dvVisitor">
                  <div class="row">
                    <div class="col-md-4">
                      <label>Itariki yo kuhagera<span class="required-mark">*</span></label>
                      <div class="form-group">
                        <input type="date" maxlength="20" autocomplete="off" placeholder="Itariki yo kuhagera" class="form-control" name="arrival_date" value="<?= isset($visitorArrivingDate) ? $visitorArrivingDate : null ?>" />
                      </div>
                    </div>
                    <div class=" col-md-3 d-none">
                      <label>Aho aturuka<span class="required-mark">*</span></label>
                      <div class="form-group">
                        <select class="form-control" name="come_from">
                          <!-- $visitorOriginalCountry == 'RWANDA' ? 'selected' : null -->
                          <!-- $visitorOriginalCountry == 'hanze' ? 'selected' : null  -->
                          <option value="RWANDA" selected> mu Rwanda</option>
                          <option value="hanze">hanze y'urwanda</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <label>Izina ry'aho avuye<span class="required-mark">*</span></label>
                      <div class="form-group">
                        <input type="text" maxlength="20" autocomplete="off" placeholder="urugero:Gasabo" class="form-control " name="place_name" value="<?= isset($visitorOrigin) ? $visitorOrigin : null ?>" />
                      </div>
                    </div>
                    <div class="col-md-4">
                      <label>Itariki yo kugenda<span class="required-mark">*</span></label>
                      <div class="form-group">
                        <input type="date" maxlength="20" autocomplete="off" placeholder="Itariki yo kugenda" class="form-control" name="departure_date" value="<?= isset($visitorDepartureDate) ? $visitorDepartureDate : null ?>" />
                      </div>
                    </div>
                  </div>
                  <div class="row no_document">
                    <div class="form-group col-md-3">
                      <label for="head_document_type">Icyangobwa cy'umukuru<span class="required-mark">*</span></label>
                      <select name="head_document_type" class="form-control required" id="head_document_type" onchange="checkDocTypeForHeadOfFamily(this);" style="border: none; background-color: rgb(255, 255, 255);">
                        <option selected="" value=""> -- Hitamo --</option>
                        <option <?= $row["documentType"] == "ID" ? 'selected' : '' ?> value="ID">Indangamuntu</option>
                        <option <?= $row["documentType"] == "PASSPORT" ? 'selected' : '' ?> value="PASSPORT">Urupapuro rw'inzira</option>
                      </select>
                    </div>

                    <div class="form-group col-md-3 display-none" id="head_document_passport_container">
                      <label for="head_document_passport">Nimero<span class="required-mark">*</span></label>
                      <input name="head_document_passport" class="form-control required" id="head_document_passport" value="<?= $row["documentType"] == "PASSPORT" ? $row["documentNumber"] : null ?>" style="border: none; background-color: rgb(255, 255, 255);" />
                    </div>


                    <div class="form-group col-md-3 display-none" id="head_document_id_container">
                      <label for="location">Indangamuntu y'u Rwanda <span class="required-mark">*</span></label>
                      <input type="text" name="head_document_id" id="head_document_id" class="form-control rwandanId" onkeyup="onlyNumber(this);checkMember($(this).val().trim(),'ID');" placeholder="Andika Indangamuntu hano" maxlength="16" value="<?= $row["documentType"] == "ID" ? $row["documentNumber"] : null ?>" style="border: none; background-color: rgb(255, 255, 255);" />
                    </div>

                    <div class="form-group col-md-3 display-none" id="head_document_issue_country_container">
                      <label for="head_document_issue_country">Igihugu yatangiwemo<span class="required-mark">*</span></label>
                      <select class="form-control" name="head_document_issue_country">
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
                    <div class="form-group col-md-3 display-none" id="head_document_issue_date_container">
                      <label for="head_document_issue_date">Itariki yatangiweho<span class="required-mark">*</span></label>
                      <input name="head_document_issue_date" type="date" class="form-control required" id="head_document_issue_date" autocomplete="off" placeholder="" value="<?= $row["issuedDate"] ?>" style="border: none; background-color: rgb(255, 255, 255);" />
                    </div>

                    <div class="form-group col-md-3 display-none" id="head_document_expiry_date_container">
                      <label for="head_document_expiry_date">Itariki izarangiriraho<span class="required-mark">*</span></label>
                      <input name="head_document_expiry_date" type="date" class="form-control required" id="head_document_expiry_date" autocomplete="off" placeholder="YYYY-mm-dd" style="border: none; background-color: rgb(255, 255, 255);" value="<?= $row["expiryDate"] ?>" />
                    </div>

                    <div class="form-group col-md-3 display-none" id="head_to_member_relationship_container">
                      <label for="head_to_member_relationship">Isano bafitanye<span class="required-mark">*</span></label>
                      <select class="form-control" name="head_to_member_relationship" id="head_to_member_relationship" style="border: none; background-color: rgb(255, 255, 255);">
                        <option <?= $row["familyCategory"] == "Wife" ? 'selected' : '' ?> value="Wife">Uwo mwashakanye</option>
                        <option <?= $row["familyCategory"] == "Kid" ? 'selected' : '' ?> value="Kid">Umwana</option>
                        <option <?= $row["familyCategory"] == "Other" ? 'selected' : '' ?> value="Other">Abandi</option>
                      </select>
                    </div>
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
  <div class="the-wraper">
    <span class="data-search-close">X</span>
    <div class="container-data">
      <h1 style="margin-bottom: 30px;">Kwandika Umushyitsi mumuryango wa <span id="paste" class="text-primary"></span></h1>
      <div class="data-serch-container">
        <form action="">
          <div class="row">
            <div class="col-12 col-md-8">
              <label for="name">Icyangobwa cy'uhagarire umuryango abarizwamo &nbsp; <span class="text-danger">*</span></label>
              <input type="text" class="form-control " id="txtfindById" placeholder="Indangamuntu/pasiporo" maxlength="16">
            </div>
            <div class="col-12 col-md-4" onclick="getFamilyHead();">
              <label for="name">&nbsp; <span class="text-danger"></span></label>
              <button class="form-control btn  btn-primary text-dark" id="btnFindById" type="button">
                shaka</button>
            </div>
            <!-- <div class="col-12 col-md-4">
              <label for="name">Aho Yavukiye &nbsp; <span class="text-danger">*</span></label>
              <input type="text" class="form-control">
            </div> -->
          </div>
        </form>
      </div>
      <div class="suggestion-data d-none" style="margin-top: 40px; height: 400px; overflow:auto">
        <table class="table table-bordered table-condensed" style="font-size: 15px;">
          <thead>
            <tr>
              <td id="td_location" colspan="2" class=""></td>
            </tr>
            <tr>
              <td id="td_names" colspan="2" class=""></td>
            </tr>
            <tr>
              <th class="" id="add-member"><i class="ititle">Abagize umuryango</i>

              </th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td id="td_members"></td>
            </tr>
          </tbody>
        </table>
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
        // $("input[name='birth_place']").val()
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
    $('.datepicker').datepicker({
      endDate: '0d',
      format: 'yyyy-mm-dd'
    });

    $('.showModal').click(function() {
      $('.modalDetails').modal('show')
    })

    $('.data-search-close').click(function() {
      $('.the-wraper').removeClass('data-search-container')
    })

    $('.datepicker1').datepicker({
      format: 'yyyy-mm-dd'
    });
    $('#modalMovement').on('hidden.bs.modal', function(e) {
      $(".contentHolder").html("");
    })
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
                $(".errors").html(" ");
                $('.emeza-wrapper').addClass('d-none')
                $("#added_remotely").val("no");
                $(".opacity").removeClass("opacity").addClass("show-forminputs");
                return;
              }
              alert("Ibyo mushaka ntibikunze mwongere mugerageze");
            }, "json");
        }
      } else {
        if (confirm("Uremezako umuryango wose  w'imutse")) {
          $(".errors").html(" ");
          $('.emeza-wrapper').addClass('d-none')
          $("#added_remotely").val("yes");
          $(".txtAction").val("member_to_family");
          $("input[name='transfer'").attr("value", "yes");
          $(".hide-all").removeClass("hide-all").addClass("show-all");
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
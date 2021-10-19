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
                        <h4 class="fs-18 mt-40 fw-500 text-center ">Umwirondoro -Umuntu udafite icyangobwa
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
                                        <label for="doctype">Impamvu yo dutagira Icyangobwa<span class="required-mark">*</span></label>
                                        <select name="doctype" class="form-control required" id="doctype" onchange="checkDocType(this);">
                                            <option value=""> -- Hitamo --</option>
                                            <option value="kugitakaza">Kugitakaza</option>
                                            <option value="kudafata icyangobwa">kudafata icyangobwa</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="name">Izina<span class="required-mark">*</span></label>
                                        <input type="text" class="form-control required" maxlength="100" placeholder="Izina " name="given_name" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                            echo $row['givenName'];
                                                                                                                                                        } elseif (isset($_POST['given_name'])) {
                                                                                                                                                            echo $_POST['given_name'];
                                                                                                                                                        } ?>">
                                    </div>

                                </div>

                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="name">Izina ry'umuryango<span class="required-mark">*</span></label>
                                        <input type="text" class="form-control required" maxlength="100" placeholder="Izina ry'umuryango" name="family_name" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                                        echo $row['familyName'];
                                                                                                                                                                    } ?>">
                                    </div>
                                </div>

                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="name">Andi mazina</label>
                                        <input type="text" class="form-control" maxlength="100" placeholder="Andi mazina" name="other_name" value="">
                                    </div>
                                </div>

                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="gender">Igitsina<span class="required-mark">*</span></label>
                                        <select name="gender" class="form-control required" id="gender">
                                            <option value=""> -- Hitamo --</option>
                                            <option value="Male">Gabo</option>
                                            <option value="Female">Gore</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 ">
                                    <div class="form-group date">
                                        <label>Itariki yamavuko<span class="required-mark">*</span></label>
                                        <input type="text" maxlength="20" autocomplete="off" placeholder="YYYY-mm-dd" class="form-control datepickerDOB required" name="dob" value="">
                                    </div>
                                </div>

                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="name">Irangamimerere<span class="required-mark">*</span></label>
                                        <select class="form-control required" name="marital_status">
                                            <option value=""> -- Hitamo --</option>
                                            <option value="Single">Ingaragu</option>
                                            <option value="Married">Yarashyingiwe</option>
                                            <option value="Divorced">Baratandukanye</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6 ">
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
                                <?php $countries = Country::getAllCountry($database); ?>
                                <div class=" col-md-6 ">
                                    <div class="form-group">
                                        <label for="country">Ubwenegihugu<span class="required-mark">*</span></label>
                                        <select class="form-control required" name="birth_nationality">
                                            <option value="">--Hitamo Igihugu--</option>
                                            <option value="178" selected>
                                                RWANDA</option>
                                            <?php
                                            foreach ($countries as $key => $country) {
                                            ?>
                                                <option value="<?= $country['id'] ?>" <?= $country['id'] == output::print("birthNationality", $row) ? "selected" : '' ?>>
                                                    <?= $country['name'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 otherIfAny ">
                                    <div class="form-group">
                                        <label for="country">Ubundi Bwenegihugu</label>
                                        <select class="form-control" name="other_nationality">
                                            <option value="0">--Hitamo Igihugu--</option>
                                            <?php
                                            foreach ($countries as $key => $country) {
                                            ?>
                                                <option value="<?= $country['id'] ?>" <?= $country['id'] == output::print("otherNationality", $row) ? "selected" : '' ?>>
                                                    <?= $country['name'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4  ">
                                    <div class="form-group">
                                        <label for="location">Isibo <span class="required-mark">*</span></label>
                                        <input type="text" name="isibo" class="form-control " maxlength="50" placeholder="isibo" value="">
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <div class="form-group">
                                        <label for="location">Umwuga <span class="required-mark">*</span></label>
                                        <input type="text" name="occupation" class="form-control" maxlength="50" value="">
                                    </div>
                                </div>
                                <div class="col-md-4  ">
                                    <div class="form-group">
                                        <label for="location">Icyiciro cy'Amashuri <span class="required-mark">*</span></label>
                                        <select name="level_education" class="form-control" value="">
                                            <option value="">hitamo</option>
                                            <option value="abanza">Amashuri abanza </option>
                                            <option value="rusange">icyiciro rusange</option>
                                            <option value="ayisumbuye">Amashuri yisumbuye </option>
                                            <option value="imyuga">Amashuri y' imyuga </option>
                                            <option value="kaminuza">Amashuri makuru na kaminuza </option>
                                            <option value="abatarize">Ntago yize</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 ">
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
                                                <label for="upi">Nimero y'ikibanza (UPI) <span class="required-mark">*</span></label>
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
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="location">Emeli</label>
                                        <input type="text" name="email" class="form-control required" maxlength="50" placeholder="Email" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                    echo $row['email'];
                                                                                                                                                }  ?>">
                                    </div>
                                </div>

                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="name">Telefoni</label>
                                        <input type="text" class="form-control" name="phone" maxlength="20" placeholder="Telephone" value="<?php if (isset($diplomat) && is_numeric($diplomat)) {
                                                                                                                                                echo $row['mobile'];
                                                                                                                                            } ?>">
                                    </div>
                                </div>

                                <div class="col-md-6 ">
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
                                    <button type="button" name="save_diplomat" class="btn fs-15 pt-5 pb-5 w-50p btn-primary save_diplomat">Emeza</button>
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
                    <h5 class="modal-title"> Kwimuka <b id="mtitle"></b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body contentHolder">
                    <!-- <p>Modal body text goes here.</p> -->
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Funga</button>
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
    </script>
</body>

</html>
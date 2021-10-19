<?php
require_once("includes/validate_credentials.php");
require_once("model/user.php");
require_once("model/umutekano.php");
?>

<!doctype html>

<html class="no-js" lang="">
<!--<![endif]-->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/metisMenu.min.css" rel="stylesheet">
<!-- Timeline CSS -->
<link href="css/timeline.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="css/startmin.css" rel="stylesheet">
<!-- Morris Charts CSS -->
<link href="css/morris.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<!-- Custom Fonts -->
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">

<head>
    <?php require_once("includes/head.php"); ?>
    <link href="css/home.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/customize.css">
</head>

<body>
    <!-- Left Panel -->
    <?php require_once 'includes/left_nav.php'; ?>
    <!-- Left Panel -->
    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">
        <!-- Header-->
        <?php require_once 'includes/top_nav.php'; ?>
        <!-- Header-->


        <!-- Main content -->
        <div class="content mt-15">

            <!-- Diplomats -->

            <?php
            if (isset($_GET['issue'])) {
                $issue = input::enc_dec('d', $_GET['issue']);
            } else {
                $issue = 0;
            }
            $sql = "SELECT (SELECT name FROM village WHERE id =s.village) AS villa_name,
            (SELECT province FROM provinces WHERE id =SUBSTRING_INDEX(s.location,'#',1)) AS pro_name,
            (SELECT issue_name FROM issue WHERE issue_id = s.issue_id LIMIT 1) AS issue_type,
              (SELECT icyabaye_name FROM icyabaye WHERE icyabaye_id = s.icyabaye_id LIMIT 1) AS icyabaye_data,s.uruhare_gabo,s.uruhare_gore,s.abahohotewe_gabo,s.abahohotewe_gore,s.location,s.comments,s.security_date,s.security_id,s.security_org FROM security AS s WHERE s.security_id = $issue ";
            $security_data = $database->fetch_array($database->query($sql));
            $locadata = explode('#', $security_data['location']);
            $district = $database->fetch_array($database->query("SELECT district FROM districts WHERE id = {$locadata[1]} LIMIT 1 "));
            $sector = $database->fetch_array($database->query("SELECT sector FROM sectors WHERE id = {$locadata[2]} LIMIT 1 "));
            $cell = $database->fetch_array($database->query("SELECT name FROM cell WHERE id = {$locadata[3]} LIMIT 1 "));
            ?>

            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bg-gradient-purple">
                            <h1 class=" text-uppercase  fs-14 pl-20 pr-20 pt-15 pb-15 fs-14">AHO BYABEREYE</h1>
                        </div>

                        <div class="fs-15 bg-white mb-20 box-shadow">
                            <div class="row">
                                <di class="col-md-12"></di>
                                <div class="col-md-3">
                                    <div class="border-right-1 p-20">
                                        <span class="text-uppercase d-block  ">Akarere</span>
                                        <span class=""><?= $district['district'] ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-right-1 p-20">
                                        <span class="text-uppercase d-block ">Umurenge</span>
                                        <span class=""><?= $sector['sector'] ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-right-1 p-20">
                                        <span class="text-uppercase d-block ">Akagari</span>
                                        <span class=""><?= $cell['name'] ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-right-1 p-20">
                                        <span class="text-uppercase d-block ">Umudugudu</span>
                                        <span class=""><?= $security_data['villa_name'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-20">
                        <div class="bg-white fs-15 box-shadow bordered">
                            <div class="border-bottom-1 bg-gradient-purple">
                                <h1 class="text-uppercase  fs-14 pl-20 pr-20 pt-15 pb-15">ICYAHUNGABANYIJE UMUTEKANO</h1>
                            </div>
                            <span class="fs-18 d-block pl-20 pr-20 pt-15"><?= $security_data['issue_type'] ?></span>
                            <div>
                                <div class="pl-20 pr-20 pt-10 pb-20">
                                    <span class="fw-600 fs-15 d-block mb-5">Icyabaye</span>
                                    <span class="fs-15"><?= $security_data['icyabaye_data'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-20">
                        <div class="bg-white fs-15 box-shadow bordered">
                            <div class="border-bottom-1 bg-gradient-purple">
                                <h1 class="text-uppercase  fs-14 pl-20 pr-20 pt-15 pb-15">ABAGIZEMO URUHARE</h1>
                            </div>
                            <div class="row p-20">
                                <div class="col-md-4 mb-10">
                                    <div class="border-1 bordered">
                                        <span class="fs-18 border-bottom-1 d-block">
                                            <span class="d-block fs-16 p-10">Bose Hamwe</span>
                                        </span>
                                        <span class="fs-30 p-10"><?php $uruhare = $security_data['uruhare_gabo'] + $security_data['uruhare_gore'];
                                                                    echo $uruhare; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-10">
                                    <div class="border-1 bordered">
                                        <span class="fs-18 border-bottom-1 d-block">
                                            <span class="d-block fs-16 p-10">Abagabo</span>
                                        </span>
                                        <span class="fs-30 p-10 "><?= $security_data['uruhare_gabo'] ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-10">
                                    <div class="border-1 bordered">
                                        <span class="fs-18 border-bottom-1 d-block">
                                            <span class="d-block fs-16 p-10">Abagore</span>
                                        </span>
                                        <span class="fs-30 p-10"><?= $security_data['uruhare_gore'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-20">
                        <div class="bg-white fs-15 box-shadow bordered">
                            <div class="border-bottom-1 bg-gradient-purple">
                                <h1 class="text-uppercase  fs-14 pl-20 pr-20 pt-15 pb-15">ABAHOHOTEWE</h1>
                            </div>
                            <div class="row p-20">
                                <div class="col-md-4 mb-10">
                                    <div class="border-1 bordered">
                                        <span class="fs-18 border-bottom-1 d-block">
                                            <span class="d-block fs-16 p-10">Bose Hamwe</span>
                                        </span>
                                        <span class="fs-30 p-10"><?php $abahohotewe = $security_data['abahohotewe_gabo'] + $security_data['abahohotewe_gore'];
                                                                    echo $abahohotewe; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-10">
                                    <div class="border-1 bordered">
                                        <span class="fs-18 border-bottom-1 d-block">
                                            <span class="d-block fs-16 p-10">Abagabo</span>
                                        </span>
                                        <span class="fs-30 p-10 "><?= $security_data['abahohotewe_gabo'] ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-10">
                                    <div class="border-1 bordered">
                                        <span class="fs-18 border-bottom-1 d-block">
                                            <span class="d-block fs-16 p-10">Abagore</span>
                                        </span>
                                        <span class="fs-30 p-10"><?= $security_data['abahohotewe_gore'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-20">
                        <div class="bg-white fs-15 box-shadow bordered">
                            <div class="border-bottom-1 bg-gradient-purple">
                                <h1 class="text-uppercase  fs-14 pl-20 pr-20 pt-15 pb-15">AMAKURU Y'INYONGERA</h1>
                            </div>
                            <div class="p-20">
                                <p><?php
                                    $comment = nl2br($security_data['comments']);
                                    echo $comment;
                                    ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-20">
                        <div class="bg-white fs-15 box-shadow bordered">
                            <div class="border-bottom-1 bg-gradient-purple">
                                <h1 class="text-uppercase  fs-14 pl-20 pr-20 pt-15 pb-15">INZEGO ZAMENYE ICYABAYE</h1>
                            </div>
                            <div class="p-20">
                                <p>
                                    <?php
                                    $inzego = explode("#", $security_data['security_org']);
                                    $i = 0;
                                    foreach ($inzego as  $value) {
                                        $i++;
                                        if ($i == 1) {
                                            echo $value;
                                        } else {
                                            echo " , " . $value;
                                        }
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mb-20">
                        <div class="bg-white fs-15 box-shadow bordered" id="feedback">
                            <div class="border-bottom-1 bg-gradient-purple">
                                <!-- <h1 class="text-uppercase  fs-14 pl-20 pr-20 pt-15 pb-15">ICYAHUNGABANYIJE UMUTEKANO</h1> -->
                                <div class="feedback-container pt-15 pb-15">
                                    <h1 class="text-uppercase  fs-14">IBITEKEREZE</h1>
                                    <span>IMBUMBE: <?= $feed_total = umutekano::getTotal($database, "  issue_id=$issue", " security_feedback") ?> </span>
                                </div>
                            </div>
                            <div class="feedback-container__content pt-15 pb-15">
                                <form action="" class="w-100p position-relative mt-20" id="form_comment">
                                    <input type="hidden" name="issue" id="issue_id" value="<?= input::get("issue") ?>">
                                    <input type="hidden" name="location" id="loc_id" value="<?= $security_data["location"] ?>">
                                    <input type="hidden" name="action_name" id="action_id" value="<?= $security_data['issue_type'] . ':' . $security_data['icyabaye_data'] ?>">
                                    <textarea name="comment" id="txt_comment" placeholder="Tanga igitekerezo..." class="form-control w-100p"></textarea>
                                    <button class="emeza emeza_feedbak" type="submit">Emeza</button>
                                </form>
                                <div class="feedback_lists" id="commentHolder">
                                    <?php
                                    if ($feed_total > 0) {
                                        $feedbacks = umutekano::getFeedBack($database, " WHERE issue_id=$issue");
                                        foreach ($feedbacks as $comment) { ?>
                                            <div class="feedback_list" id="c<?= $comment['id'] ?>">
                                                <div class="user">
                                                    <span><?= $comment['author'][0] ?></span>
                                                </div>
                                                <div class="feedback_contents">
                                                    <div class="username"><?= $comment['author'] ?></div>
                                                    <div class="time"><?= $comment['created_at'] ?></div>
                                                    <div class="feedback"><?= nl2br($comment['feedback'])  ?></div>
                                                </div>
                                            </div>
                                    <?php  }
                                    }

                                    ?>
                                    <!--  -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php
        if (input::required(array("notify", "nt"))) {
            $nt = input::enc_dec("d", input::get("nt"));
            $database->create(
                "sec_notification_user",
                array(
                    "notification_id" => $nt,
                    "user_id" => session::get("id")
                )
            );
        }
        ?>
    </div>
    <!-- end of main content -->
    </div><!-- /#right-panel -->

    <!-- Right Panel -->

    <!-- <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/widgets.js"></script>
    <script src="js/ajax.js"></script> -->
    <!-- <script src="js/home.js"></script> -->

    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="js/ajax.js"></script>
    <script src="assets/js/custom.js"></script>

    <script>
        $("#form_comment").on("submit", function(e) {
            e.preventDefault();
            let data = $(this).serialize();
            data = data + "&action=make_feedback";
            if ($.trim($("#txt_comment").val()).length == 0) return;
            $.ajax({
                type: "POST",
                data: data,
                url: "controller/umutekanoController.php",
                dataType: "json",
                beforeSend: function() {
                    $(".emeza_feedbak").append(
                        `<i class='fa fa-spinner fa-spin gifWait text-warning' style="font-size:14px"></i>`
                    );
                    $(".emeza_feedbak").attr("disabled", "disabled");
                },
                success: function(res) {
                    if (res.status == "fail") {
                        alert("Igikorwa cyo gutanga igitekerezo Ntabwo gikunze mwongere mugerageze");
                        return;
                    }
                    $("#txt_comment").val("");
                    $(".emeza_feedbak").removeAttr("disabled");
                    $(".gifWait").remove();
                    let data = res.data;
                    if (res.status == "fail") {
                        alert("Igikorwa cyo gutanga igitekerezo Ntabwo gikunze mwongere mugerageze");
                        return;
                    }
                    let names = data.user_names;
                    let html_resp = `<div class="feedback_list">
                                        <div class="user">
                                            <span>${names.toUpperCase().charAt(0)}</span>
                                        </div>
                                        <div class="feedback_contents">
                                            <div class="username">${names}</div>
                                            <div class="time">${data.created_at}</div>
                                            <div class="feedback">${data.feedback}</div>
                                        </div>
                                    </div>`;
                    $("#commentHolder").prepend(html_resp);
                },
                error: function(xhr) {
                    alert("Igikorwa cyo gutanga igitekerezo Ntabwo gikunze mwongere mugerageze");
                    // alert(xhr.responseText);
                    // console.log(xhr.responseText);
                },
            });

        })
        $('.toggleMenu, .closeMenu').click(function() {
            $('aside').toggleClass('left-0');
            $('.main-menu ').addClass('show')
        })

        let UrlPath = window.location.href.split('#');
        if (UrlPath[1])
            $(`#${UrlPath[1]}`).css('background-color', "#eee")
    </script>

</body>

</html>
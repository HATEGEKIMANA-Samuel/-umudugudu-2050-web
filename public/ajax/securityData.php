 <?php
    require_once '../autoload.php';
    require_once("../model/user.php");
    require_once("../model/umutekano.php");
    // require_once "../routes/queries/db_connection.php";

    $numericLocation = input::enc_dec('d', session::get('userLocation'));
    $location = user::decodeLocation($database, rtrim($numericLocation, '#'));
    if (!session::exists('CL')) {
        switch (session::get('level')) {
            case 2: // village
                session::put('CL', array(
                    'text' => 'Rwanda / ' . $location[0]['name'] . ' / ' . $location[1]['name'] . ' / '
                        . $location[2]['name'] . ' / ' . $location[3]['name'] . ' / ' . $location[4]['name'],
                    'numeric' => $location[0]['id'] . '#' . $location[1]['id'] . '#' . $location[2]['id']
                        . '#' . $location[3]['id'] . '#' . $location[4]['id']
                ));
                break;
            case 3: // cell
                session::put('CL', array(
                    'text' => 'Rwanda / ' . $location[0]['name'] . ' / ' . $location[1]['name'] . ' / '
                        . $location[2]['name'] . ' / ' . $location[3]['name'],
                    'numeric' => $location[0]['id'] . '#' . $location[1]['id'] . '#' . $location[2]['id']
                        . '#' . $location[3]['id']
                ));
                break;
            case 4: // sector
                session::put('CL', array(
                    'text' => 'Rwanda / ' . $location[0]['name'] . ' / ' . $location[1]['name'] . ' / '
                        . $location[2]['name'],
                    'numeric' => $location[0]['id'] . '#' . $location[1]['id'] . '#' . $location[2]['id']
                ));
                break;
            case 5: // district
                session::put('CL', array(
                    'text' => 'Rwanda / ' . $location[0]['name'] . ' / ' . $location[1]['name'],
                    'numeric' => $location[0]['id'] . '#' . $location[1]['id']
                ));
                break;
            case 6: // province
                session::put('CL', array(
                    'text' => 'Rwanda / ' . $location[0]['name'],
                    'numeric' => $location[0]['id']
                ));
                break;
            default: // HQ & Admin
                session::put('CL', array(
                    'text' => 'Rwanda',
                    'numeric' => 0
                ));
                break;
        }
    }
    $text = "";
    if (session::exists("CL")) {
        $location = session::get("CL")["numeric"] . "#";
        $text = "<span class='text-primary'>/" . session::get("CL")["text"] . '</span>';
    } else {
        $level = session::get("level");
        if ($level != 7 && $level != 1) $location = input::enc_dec("d", session::get("userLocation"));
        $location = "#";
    }
    $loc = rtrim($location, "#");
    ?>
 <div class="col-12">
     <span class="d-block fw-500 fs-15 border-bottom-1 pb-20 mb-30">
         <i class="fa fa-wpforms fw-500 pr-5"></i> UMUTEKANO
     </span>
 </div>
 <!-- URUGOMO -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1 bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2">
                     Urugomo
                 </span>
             </p>
             <div class="">
                 <?php
                    $sql = $loc == 0
                        ? " issue_id='1'"
                        : " issue_id='1' AND location LIKE '$loc%'";
                    // $query = $connect->prepare($sql);
                    // $query->execute();
                    // $results = $query->fetch(PDO::FETCH_ASSOC);
                    // $case1 = $query->rowCount();
                    $case1 = umutekano::getTotal($database, $sql);
                    ?>
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2 updatec urugomoc">
                         <?php echo $case1; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-primary fa-user-secret fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer text-primary d-block fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '1') ?>'" id="view-family">Reba Bose &rarr;</label>

             </div>
         </div>
     </div>
 </div>
 <!-- UBUJURU -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1 bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2">
                     Ubujura
                 </span>
             </p>
             <div class="">
                 <?php
                    $sql = $loc == 0
                        ? " issue_id='2'"
                        : "issue_id='2' AND location LIKE '$loc%'";
                    // $query = $connect->prepare($sql);
                    // $query->execute();
                    // $results = $query->fetch(PDO::FETCH_ASSOC);
                    $case2 = umutekano::getTotal($database, $sql);
                    ?>
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2 updatec ubujurac">
                         <?php echo $case2; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-warning fa fa-unlock fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer text-warning d-block fs-13" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '2') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <!-- UBWICANYI -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1 bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2 ">
                     Ubwicanyi
                 </span>
             </p>
             <?php

                $sql = $loc == 0
                    ? " issue_id='3'"
                    : " issue_id='3' AND location LIKE '$loc%'";
                // $query = $connect->prepare($sql);
                // $query->execute();
                // $results = $query->fetch(PDO::FETCH_ASSOC);
                // $case3 = $query->rowCount();
                $case3 = umutekano::getTotal($database, $sql);
                ?>
             <div class="">
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2 updatec ubwicanyic">
                         <?php echo $case3; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-info fa-bed fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer d-block text-info fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '3') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <!-- IBIZA -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1 bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2">
                     Ibiza
                 </span>
             </p>
             <?php

                $sql = $loc == 0
                    ? " issue_id='4'"
                    : " issue_id='4' AND location LIKE '$loc%'";
                // $query = $connect->prepare($sql);
                // $query->execute();
                // $results = $query->fetch(PDO::FETCH_ASSOC);
                // $case4 = $query->rowCount();
                $case4 = umutekano::getTotal($database, $sql);
                ?>
             <div class="">
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2  updatec ibizac">
                         <?php echo $case4; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-danger fa-envira fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer text-danger d-block fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '4') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <!-- KWIYAHURA -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1 bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2">
                     Kwiyahura
                 </span>
             </p>
             <div class="">
                 <?php
                    $sql = $loc == 0
                        ? " issue_id='5'"
                        : " issue_id='5' AND location LIKE '$loc%'";
                    // $query = $connect->prepare($sql);
                    // $query->execute();
                    // $results = $query->fetch(PDO::FETCH_ASSOC);
                    $case5 = umutekano::getTotal($database, $sql);
                    ?>
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2  updatec kwiyahurac">
                         <?php echo $case5; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-seconary fa-users fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer d-block text-seconary  fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '5') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <!-- IBIYOBWABWENGE -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1  bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2 fs-13">
                     Ibiyobyabwenge
                 </span>
             </p>
             <div class="">
                 <?php
                    $sql = $loc == 0
                        ? " issue_id='6'"
                        : " issue_id='6' AND location LIKE '$loc%'";
                    // $query = $connect->prepare($sql);
                    // $query->execute();
                    // $results = $query->fetch(PDO::FETCH_ASSOC);
                    // $case6 = $query->rowCount();
                    $case6 = umutekano::getTotal($database, $sql);
                    ?>
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2  updatec drugc">
                         <?php echo $case6 ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-info fa fa-medkit fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer text-info d-block fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '6') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <!-- IBIYOBWABWENGE -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1  bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2 fs-13">
                     Magendu
                 </span>
             </p>
             <div class="">
                 <?php

                    $sql = $loc == 0
                        ? " issue_id='7'"
                        : "issue_id='7' AND location LIKE '$loc%'";
                    // $query = $connect->prepare($sql);
                    // $query->execute();
                    // $results = $query->fetch(PDO::FETCH_ASSOC);
                    // $case7 = $query->rowCount();
                    $case7 = umutekano::getTotal($database, $sql);
                    ?>
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2  updatec fraudc">
                         <?php echo $case7; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-warning fa fa-user-md fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer text-warning d-block fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '7') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <!-- AMAKIMBIRANE -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1  bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2 fs-13">
                     Amakimbirane Mumuryango
                 </span>
             </p>
             <div class="">
                 <?php

                    $sql = $loc == 0
                        ? " issue_id='8'"
                        : " issue_id='8' AND location LIKE '$loc%'";
                    // $query = $connect->prepare($sql);
                    // $query->execute();
                    // $results = $query->fetch(PDO::FETCH_ASSOC);
                    // $case8 = $query->rowCount();
                    $case8 = umutekano::getTotal($database, $sql);
                    ?>
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2  updatec conflictc">
                         <?php echo $case8; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 fa fa-question-circle fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer d-block fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '8') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <!-- UMUPAKA -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1  bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2 fs-13">
                     Kwambuka umupaka binyuranyije n'amategeko
                 </span>
             </p>
             <div class="">
                 <?php

                    $sql = $loc == 0
                        ? " issue_id='9'"
                        : " issue_id='9' AND location LIKE '$loc%'";
                    // $query = $connect->prepare($sql);
                    // $query->execute();
                    // $results = $query->fetch(PDO::FETCH_ASSOC);
                    // $case9 = $query->rowCount();
                    $case9 = umutekano::getTotal($database, $sql)
                    ?>
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2  updatec umupakac">
                         <?php echo $case9; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-primary fa-map-signs fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer text-primary d-block fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '9') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <!-- IHOTERWA -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1 bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2 fs-14">
                     Ihohoterwa rishingiye kugitsina
                 </span>
             </p>
             <div class="">
                 <?php

                    $sql = $loc == 0
                        ? " issue_id='10'"
                        : " issue_id='10' AND location LIKE '$loc%'";
                    // $query = $connect->prepare($sql);
                    // $query->execute();
                    // $results = $query->fetch(PDO::FETCH_ASSOC);
                    $case10 = umutekano::getTotal($database, $sql)
                    ?>
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2  updatec violancec">
                         <?php echo $case10; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-warning fa-building-o fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer d-block text-warning fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '10') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <!-- UBUGIZI BWA NABI  -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1 bordered-30 mb-15">
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2 fs-14">
                     Ubugizi bwa nabi bukoresheje intwaro
                 </span>
             </p>
             <div class="">
                 <?php

                    $sql = $loc == 0
                        ? "issue_id='11'"
                        : "issue_id='11' AND location LIKE '$loc%'";
                    // $query = $connect->prepare($sql);
                    // $query->execute();
                    // $results = $query->fetch(PDO::FETCH_ASSOC);
                    // $case11 = $query->rowCount();
                    $case11 = umutekano::getTotal($database, $sql)
                    ?>
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2  updatec intwaroc">
                         <?php echo $case11; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-info fa fa-cut fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer d-block text-info fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '11') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <!-- UBUGIZI BWA NABI  -->
 <div class="col-md-3 col-6">
     <div class="statistics-item p-15 border-1 bordered-30 mb-15">
         <?php
            ?>
         <div class="text-custom-dark">
             <p class="mb-0">
                 <span class="z-index-2 fs-14">
                     Kutubahiriza amabwiriza yo gukumira covid 19
                 </span>
             </p>
             <div class="">
                 <?php

                    $sql = $loc == 0
                        ? " issue_id='12'"
                        : "issue_id='12' AND location LIKE '$loc%'";
                    // $query = $connect->prepare($sql);
                    // $query->execute();
                    // $results = $query->fetch(PDO::FETCH_ASSOC);
                    // $case12 = $query->rowCount();
                    $case12 = umutekano::getTotal($database, $sql)
                    ?>
                 <h2 id="family-count" class="count-num mb-0 position-relative">
                     <span class="z-index-2  updatec covidc">
                         <?php echo $case12; ?>
                     </span>
                     <i class="fa icon-absolute z-index-1 text-danger fa fa-cut fw-500 pr-5"></i>
                 </h2>
                 <label class="pointer d-block text-danger fs-13 navigate" onclick="document.location.href='reports?case=<?= input::enc_dec('e', '12') ?>'" id="view-family">Reba Bose &rarr;</label>
             </div>
         </div>
     </div>
 </div>
 <script>
     $(".navigate").click(function() {
         $(this).append(
             `<i class='fa fa-spinner fa-spin gifWait text-warning' style='font-size:20px'></i>`
         );
         $("#page-loader").removeClass("d-none");
     });
 </script>
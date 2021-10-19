<?php
require_once '../autoload.php';
require_once("../model/user.php");
require_once("../model/family.php");
// require_once "routes/queries/db_connection.php";
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
$malePeople = family::getTotalPeople($database, $location, "male");
$femalePeople = family::getTotalPeople($database, $location, "female");
$allPeople = $malePeople + $femalePeople;
?>
<div class="col-12">
    <span class="d-block fw-500 fs-15 border-bottom-1 pb-20 mb-30">
        <i class="fa fa-users fw-500 pr-5"></i> ABATURAGE <span id="loaderh" class="text-warning"></span>
    </span>
</div>
<!-- ABATURAGE -->
<div class="col-md-3 col-6">
    <div class="statistics-item p-15 border-1 bg-gradient-1 bordered-30 mb-15">
        <div class="text-custom">
            <p class="mb-0">
                <span class="z-index-2">
                    Abaturage
                </span>
            </p>
            <div class="">
                <h2 id="family-count-total" class="count-num mb-0 position-relative">
                    <span class="z-index-2 peoplec">
                        <?php echo $allPeople ?>
                    </span>
                    <i class="fa icon-absolute z-index-1 fa-users fw-500 pr-5"></i>
                </h2>
                <label class="pointer d-block fs-13 navigate navigate" onclick="document.location.href='population-list'" id="view-family">Reba Bose &rarr;</label>
            </div>
        </div>
    </div>
</div>
<!-- Imiryango -->
<div class="col-md-3 col-6">
    <div class="statistics-item p-15 border-1 bg-gradient-2 bordered-30 mb-15">
        <div class="text-custom">
            <p class="mb-0">
                <span class="z-index-2">
                    Imiryango
                </span>
            </p>

            <div class="">
                <h2 id="family-count" class="count-num mb-0 position-relative">
                    <span class="z-index-2">
                        <?php echo $family = family::getHeadOfFamily($database, $location); ?>
                    </span>
                    <i class="fa icon-absolute z-index-1 fa-home fw-500 pr-5"></i>
                </h2>
                <label class="pointer d-block fs-13 navigate" onclick="document.location.href='diplomats-list'" id="view-family">Reba Bose &rarr;</label>
            </div>
        </div>
    </div>
</div>
<!-- ABASHITSI -->
<div class="col-md-3 col-6">
    <div class="statistics-item p-15 border-1 bg-gradient-3 bordered-30 mb-15">
        <div class="text-custom">
            <p class="mb-0">
                <span class="z-index-2">
                    Abashyitsi
                </span>
            </p>
            <div class="">
                <h2 id="family-count" class="count-num mb-0 position-relative">
                    <span class="z-index-2 updatec visitorc">
                        <?php
                        $today = date('Y-m-d');
                        echo $visitors = family::getVisitors($database, $location, "0") ?>
                    </span>
                    <i class="fa icon-absolute z-index-1 fa-home fw-500 pr-5"></i>
                </h2>
                <label class="pointer d-block fs-13 navigate" onclick="document.location.href='family-visitors'" id="view-family">Reba Bose &rarr;</label>
            </div>
        </div>
    </div>
</div>
<!-- ABAGABO -->
<div class="col-md-3 col-6">
    <div class="statistics-item p-15 border-1 bg-gradient-4 bordered-30 mb-15">
        <div class="text-custom">
            <p class="mb-0">
                <span class="z-index-2 ">
                    Igitsina Gabo
                </span>
            </p>
            <div class="">
                <h2 class="count-num mb-0 position-relative">
                    <span class="z-index-2 updatec malec">
                        <?php echo $malePeople; ?>
                    </span>
                    <i class="fa icon-absolute z-index-1 fa-male fw-500 pr-5"></i>
                </h2>
                <label class="pointer d-block fs-13 navigate" onclick="document.location.href='male-people'" id="view-family">Reba Bose &rarr;</label>
            </div>
        </div>
    </div>
</div>
<!-- ABAGORE -->
<div class="col-md-3 col-6">
    <div class="statistics-item p-15 border-1 bg-gradient-5 bordered-30 mb-15">
        <div class="text-custom">
            <p class="mb-0">
                <span class="z-index-2">
                    Igitsina Gore
                </span>
            </p>
            <div class="">
                <h2 class="count-num mb-0 position-relative">
                    <span class="z-index-2 updatec femalec">
                        <?php echo $femalePeople  ?>
                    </span>
                    <i class="fa icon-absolute z-index-1 fa-female fw-500 pr-5"></i>
                </h2>
                <label class="pointer d-block fs-13 navigate" onclick="document.location.href='female-people'" id="view-family">Reba Bose &rarr;</label>
            </div>
        </div>
    </div>
</div>
<!-- ABAKODESHA -->
<div class="col-md-3 col-6">
    <div class="statistics-item p-15 border-1 bg-gradient-6 bordered-30 mb-15">
        <div class="text-custom">
            <p class="mb-0">
                <span class="z-index-2">
                    Abakodesha
                </span>
            </p>
            <div class="">
                <h2 class="count-num mb-0 position-relative">
                    <span class="z-index-2 updatec rentc">
                        <?php
                        echo $rent_house = family::getAllPeople($database, $location, 0, "WHERE is_family_heading='1' AND  landLord  is not  null ");
                        ?>
                    </span>
                    <i class="fa icon-absolute z-index-1 fa-building-o fw-500 pr-5"></i>
                </h2>
                <label class="pointer d-block fs-13 navigate" onclick="document.location.href='landlords'" id="view-family">Reba Bose &rarr;</label>
            </div>
        </div>
    </div>
</div>
<!-- ABADAKODESHA -->
<div class="col-md-3 col-6">
    <div class="statistics-item p-15 border-1 bg-gradient-7 bordered-30 mb-15">
        <div class="text-custom">
            <p class="mb-0">
                <span class="z-index-2">
                    Abadakodesha
                </span>
            </p>
            <div class="">
                <h2 id="family-count" class="count-num mb-0 position-relative">
                    <span class="z-index-2 updatec notrentc">
                        <?php
                        echo  $notrent_house = $family - $rent_house;
                        //echo  $notrent_house = family::getAllPeople($database, $location, 0, "WHERE is_family_heading='1' AND  landLord  is  null ");
                        ?>
                    </span>
                    <i class="fa icon-absolute z-index-1 fa-building-o fw-500 pr-5"></i>
                </h2>
                <label class="pointer d-block fs-13 navigate" onclick="document.location.href='non-landlords'" id="view-family">Reba Bose &rarr;</label>
            </div>
        </div>
    </div>
</div>
<script>
    $(".navigate").click(function() {
        $("#page-loader").removeClass("d-none");
        $(this).append(
            `<i class='fa fa-spinner fa-spin gifWait text-warning' style='font-size:20px'></i>`
        );
    });
</script>
<header id="header" class="header">
    <div class="header-menu">

        <div class="col-sm-12 pl-0 ">
            <!-- MOBILE -->
            <div class="top-navigator hide">
                <button class="navbar-toggler toggleMenu" type="button">
                    <i class="fa fs-25 fa-bars"></i>
                </button>

                <div class="header-left">
                    <button class="search-trigger"><i class="fa fa-search"></i></button>
                    <div class="form-inline">
                        <form action="search" method="get" class="search-form">
                            <input class="form-control mr-sm-2" type="text" placeholder="Shaka ukoresheje izina ,Indangamuntu cg pasiporo" aria-label="Search" name="searchany">

                            <button class="pr-30" type="submit">
                                <i class="fa cool-blue-txt fs-14 fa-search"></i>
                            </button>

                            <i class="fa searchClose cool-red-txt fs-14 fa-close"></i>
                        </form>
                    </div>

                    <div class="dropdown for-notification">
                        <?php
                        $d_location = input::enc_dec('d', session::get("userLocation"));
                        if ($_SERVER["REQUEST_URI"] != "/notifications") { ?>
                            <button class="btn btn-secondary dropdown-toggle the-notification-area" type="button" id="notification1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>
                                <span class=" bg-danger">
                                    <?php
                                    $notify_total = getNotif($_SESSION['id'], 'numb', $d_location);
                                    echo  $notify_total;
                                    ?>

                                </span>
                            </button>
                        <?php
                        } else {
                            echo $notify_total = "";
                        }
                        ?>

                        <div class="dropdown-menu menuMenu p-10 w-250" aria-labelledby="notification" style="box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);">
                            <p class="text-left fs-13 mb-0"> <?php
                                                                if ($notify_total > 0) {
                                                                    echo "Ufite amamenyesha ";
                                                                    echo  $notify_total;
                                                                }
                                                                ?>
                                <hr class="mb-0 mt-0">
                            </p>
                            <!-- <a class="dropdown-item media bg-flat-color-1" href="#">
                                    <i class="fa fa-check"></i>
                                    <p>Server #1 overloaded.</p>
                                </a> -->
                            <!-- <a class="dropdown-item media bg-flat-color-4" href="#">
                                    <i class="fa fa-info"></i>
                                    <p>Server #2 overloaded.</p>
                                </a>
                                <a class="dropdown-item media bg-flat-color-5" href="#">
                                    <i class="fa fa-warning"></i>
                                    <p>Server #3 overloaded.</p>
                                </a> -->
                        </div>
                    </div>
                    <!-- <span class="mx-3">
                        <a href="#kinyarwanda" data-toggle="tooltip" data-placement="bottom" title="Kinyarwanda" id="aKiny">
                            <img src="images/rwanda.png" alt="rwanda" height="20" width="20">
                        </a>
                        <a href="#francais" data-toggle="tooltip" data-placement="bottom" title="Francais" id="aFr">
                            <img src="images/france.png" alt="francais" height="20" width="20">
                        </a>
                        <a href="#english" data-toggle="tooltip" data-placement="bottom" title="English" id="aEn">
                            <img src="images/uk.png" alt="english" height="20" width="20">
                        </a>
                    </span> -->
                </div>

                <div class="user-area dropdown float-right">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php
                        // $q = $database->query("SELECT avatar FROM user WHERE id={$_SESSION['id']}");
                        // $img = $database->fetch_array($q);
                        $avatar = session::get("userProfile");
                        $u = $_SESSION["username"];
                        if ($avatar == NULL || !file_exists("uploads/avatar/$avatar")) {
                            $profile_pic = "images/default_profile.jpg";
                        } else {
                            $profile_pic = 'uploads/avatar/' . $avatar . '" alt="' . $u;
                        } ?>
                        <img class="user-avatar rounded-circle" src="<?php echo $profile_pic; ?>" alt="User Avatar">
                    </a>

                    <div class="user-menu dropdown-menu">
                        <a class="nav-link navigate" href="profile"><i class="fa fa- user"></i>My Profile</a>
                        <a class="nav-link navigate" href="#"><i class="fa fa -cog"></i>Settings</a>
                        <a class="nav-link navigate" href="logout"><i class="fa fa-power -off"></i>Logout</a>
                    </div>
                </div>


            </div>

            <!-- <a id="menuToggle" class="menutoggle openCloseMenu pull-left"><i class="fa fa fa-tasks"></i></a> -->
            <div style="display: flex; justify-content: space-between; align-items: center" class>
                <div class="col-md-9 pl-0 mobile-hide">
                    <div class="header-left">
                        <button class="search-trigger"><i class="fa fa-search"></i></button>
                        <div class="form-inline">
                            <form action="search" method="get" class="search-form">
                                <input class="form-control mr-sm-2" type="text" placeholder="Shaka ukoresheje izina ,Indangamuntu cg pasiporo" aria-label="Search" name="searchany">

                                <button class="pr-30" type="submit">
                                    <i class="fa cool-blue-txt fs-14 fa-search"></i>
                                </button>

                                <i class="fa searchClose cool-red-txt fs-14 fa-close"></i>
                            </form>
                        </div>

                        <div class="dropdown for-notification p-0">
                            <?php if ($_SERVER["REQUEST_URI"] != "/notifications") { ?>
                                <button class="btn btn-secondary dropdown-toggle the-notification-area" type="button" id="notification1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bell"></i>
                                    <span class="count bg-danger"><?php echo $notify_total;
                                                                    ?></span>
                                </button>
                            <?php
                            } else {
                            }
                            ?>

                            <div class="dropdown-menu p-0 menuMenu" aria-labelledby="notification" style="box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);">
                                <p style="border-bottom: 1px solid #eee" class="text-left fs-13 mb-0">Ufite amamenyesha <?php echo $notify_total;
                                                                                                                        ?>

                                </p>
                                <?php
                                $notifications = "";
                                if ($_SERVER["REQUEST_URI"] != "/notifications") {

                                    if ($notify_total > 0)
                                        echo $notifications = getNotif($_SESSION['id'], 'getreal', $d_location);
                                }
                                ?>
                                <!-- <a style="border-bottom: 1px solid #eee" class="dropdown-item notification-notice mt-0 media fs-12" href="' . $ref . '" >
                                    <p class="fs-14" style="padding: 8px 15px 8px 0 !important; ">
                                    <img src="./images/bell.png" height="20" class="pr-10">  
                                    hghjsgfjhgsfhjgsfjhgfhdfgjhgfhjgf</p>
                                </a> -->
                                <?php if ($notify_total >= 10) : ?>
                                    <a class="dropdown-item mt-0 media" href="notifications?count=<?= $notify_total ?>">
                                        <p class="fs-13">Reba byose &rarr;</p>
                                    </a>
                                <?php endif ?>
                                <!-- <a class="dropdown-item media bg-flat-color-4" href="#">
                                    <i class="fa fa-info"></i>
                                    <p>Server #2 overloaded.</p>
                                </a>
                                <a class="dropdown-item media bg-flat-color-5" href="#">
                                    <i class="fa fa-warning"></i>
                                    <p>Server #3 overloaded.</p>
                                </a> -->
                            </div>
                        </div>

                        <!-- <span class="mx-3">
                            <a href="#kinyarwanda" data-toggle="tooltip" data-placement="bottom" title="Kinyarwanda" id="aKiny">
                                <img src="images/rwanda.png" alt="rwanda" height="20" width="20">
                            </a>
                            <a href="#francais" data-toggle="tooltip" data-placement="bottom" title="Francais" id="aFr">
                                <img src="images/france.png" alt="francais" height="20" width="20">
                            </a>
                            <a href="#english" data-toggle="tooltip" data-placement="bottom" title="English" id="aEn">
                                <img src="images/uk.png" alt="english" height="20" width="20">
                            </a>
                        </span> -->

                    </div>
                </div>

                <div class="col-md-3 mobile-hide">
                    <div class="user-area dropdown float-right mobile-hide">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php
                            // $q = $database->query("SELECT avatar FROM user WHERE id={$_SESSION['id']}");
                            // $img = $database->fetch_array($q);
                            // $avatar = $img[0];
                            $avatar = session::get("userProfile");
                            $u = $_SESSION["username"];
                            if ($avatar == NULL || !file_exists("uploads/avatar/$avatar")) {
                                $profile_pic = "images/default_profile.jpg";
                            } else {
                                $profile_pic = 'uploads/avatar/' . $avatar . '" alt="' . $u;
                            } ?>
                            <div style="display:flex; justify-content: flex-start; align-items: center">
                                <img class="user-avatar mr-10 rounded-circle" src="<?php echo $profile_pic; ?>" alt="User Avatar">
                                <span class="fs-14 text-secondary fw-600"><?= $u ?></span>
                            </div>
                        </a>

                        <div class="user-menu dropdown-menu">
                            <a class="nav-link" href="profile"><i class="fa fa- user"></i>My Profile</a>

                            <a class="nav-link" href="#"><i class="fa fa -cog"></i>Settings</a>

                            <a class="nav-link" href="logout"><i class="fa fa-power -off"></i>Logout</a>
                        </div>
                    </div>

                    <div class="language-select dropdown" id="language-select">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown" id="language" aria-haspopup="true" aria-expanded="true">
                            <i class="flag-icon flag-icon-us"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="language">
                            <!-- <div class="dropdown-item">
                                <span class="flag-icon flag-icon-fr"></span>
                            </div> -->
                            <!-- <div class="dropdown-item">
                                <i class="flag-icon flag-icon-es"></i>
                            </div> -->
                            <!-- <div class="dropdown-item">
                                <i class="flag-icon flag-icon-us"></i>
                            </div> -->
                            <div class="dropdown-item">
                                <i class="flag-icon flag-icon-it"></i>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- <div class="col-sm-3 pr-0 position-relative">
            <p class="pt-10 fs-14 " style=""> -->
        <?php
        // $sql = "SELECT fname FROM user WHERE username ='{$_SESSION["username"]}'  LIMIT 1";
        // $query = $database->query($sql);
        // $row = $database->fetch_array($query);
        $name = "<span class='mobile-hide' style='color:blue;'>" . session::get('userFirstName') . "</span>";
        ?></p>
        <!-- </div> -->
    </div>
    <script type="text/javascript">
        // var cpage = window.location.href;
        // cpage = cpage.split("/");
        // var page = cpage[cpage.length - 1];
        // var fr = document.getElementById("aFr");
        // var en = document.getElementById("aEn");
        // var kin = document.getElementById("aKiny");
        // fr.addEventListener("click", () => {
        //     // french here
        // });
        // en.addEventListener("click", () => {
        //     // english here
        //     window.location.href = "en/" + page;
        // });
        // kin.addEventListener("click", () => {

        // });
    </script>
</header><!-- /header -->
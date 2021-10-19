<header id="header" class="header">
    <div class="header-menu">

        <div class="col-sm-7">
            <a id="menuToggle" class="menutoggle openCloseMenu pull-left"><i class="fa fa fa-tasks"></i></a>
            <div class="header-left">
                <button class="search-trigger"><i class="fa fa-search"></i></button>
                <div class="form-inline">
                    <form action="search" method="get" class="search-form">
                        <input class="form-control mr-sm-2" type="text" placeholder="Search Names ,ID or Passport" aria-label="Search" name="searchany">

                        <button class="pr-30" type="submit">
                            <i class="fa cool-blue-txt fs-14 fa-search"></i>
                        </button>

                        <i class="fa searchClose cool-red-txt fs-14 fa-close"></i>
                    </form>
                </div>

                <div class="dropdown for-notification">

                    <button class="btn btn-secondary dropdown-toggle the-notification-area" type="button" id="notification1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <span class="count bg-danger"><?php echo '0'; //echo getNotif($_SESSION['id'],'numb');
                                                        ?></span>
                    </button>

                    <div class="dropdown-menu menuMenu p-10 w-250" aria-labelledby="notification" style="box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);">
                        <p class="text-left fs-13 mb-0">You have 0 <?php //echo getNotif($_SESSION['id'],'numb')." New Notifications";
                                                                    ?>
                            <hr class="mb-0 mt-0">
                        </p>
                        <?php //getNotif($_SESSION['id'],'getreal'); 
                        ?>
                    </div>
                </div>

                <span class="mx-3">
                    <a href="#kinyarwanda" data-toggle="tooltip" data-placement="bottom" title="Kinyarwanda" id="aKiny">
                        <img src="images/rwanda.png" alt="rwanda" height="20" width="20">
                    </a>
                    <a href="#francais" data-toggle="tooltip" data-placement="bottom" title="Francais" id="aFr">
                        <img src="images/france.png" alt="francais" height="20" width="20">
                    </a>
                    <a href="#english" data-toggle="tooltip" data-placement="bottom" title="English" id="aEn">
                        <img src="images/uk.png" alt="english" height="20" width="20">
                    </a>
                </span>
            </div>
        </div>

        <div class="col-sm-3 pr-0 position-relative">
            <p class="pt-10 position-absolute fs-14 pull-right" style="right: -117px;">
                <?php
                $sql = "SELECT fname FROM user WHERE username ='{$_SESSION["username"]}'  LIMIT 1";
                $query = $database->query($sql);
                $row = $database->fetch_array($query);
                echo "<span style='color:blue;'>{$row['fname']}</span>";
                ?></p>

        </div>

        <div class="col-sm-2 pl-0">
            <div class="user-area dropdown float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php
                    $q = $database->query("SELECT avatar FROM user WHERE id={$_SESSION['id']}");
                    $img = $database->fetch_array($q);
                    $avatar = $img[0];
                    $u = $_SESSION["username"];
                    if ($avatar == NULL || !file_exists("uploads/avatar/$avatar")) {
                        $profile_pic = "images/default_profile.jpg";
                    } else {
                        $profile_pic = 'uploads/avatar/' . $avatar . '" alt="' . $u;
                    } ?>
                    <img class="user-avatar rounded-circle" src="<?php echo $profile_pic; ?>" alt="User Avatar">
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
                    <div class="dropdown-item">
                        <span class="flag-icon flag-icon-fr"></span>
                    </div>
                    <div class="dropdown-item">
                        <i class="flag-icon flag-icon-es"></i>
                    </div>
                    <div class="dropdown-item">
                        <i class="flag-icon flag-icon-us"></i>
                    </div>
                    <div class="dropdown-item">
                        <i class="flag-icon flag-icon-it"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript">
        var cpage = window.location.href;
        cpage = cpage.split("/");
        var page = cpage[cpage.length - 1];
        var fr = document.getElementById("aFr");
        var en = document.getElementById("aEn");
        var kin = document.getElementById("aKiny");
        fr.addEventListener("click", () => {
            // french here
        });
        en.addEventListener("click", () => {
            // english  is now selected
            // window.location.href = "en/" + page;
        });
        kin.addEventListener("click", () => {
            window.location.href = "../" + page;
        });
    </script>

</header><!-- /header -->
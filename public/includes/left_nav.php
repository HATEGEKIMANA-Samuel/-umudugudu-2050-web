<aside id="left-panel" class="left-panel">
    <div class="page-loader d-none" id="page-loader" style="width: 100%; height: 100vh; background-color: #ffffffd4;
      position: fixed; z-index: 9999">
        <div class="loager-image" style="width: 100px; height: 100px; position: absolute; left: 50%; top: 45%">
            <h1>Loading...</h1>
        </div>
    </div>
    <nav class="navbar navbar-expand-sm navbar-default">
        <div class="navbar-header">
            <button class="navbar-toggler hide" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars"></i>
            </button>

            <img src="./images/srs.PNG" class="mt-20" style="border-radius: 3px;">

            <span class="closeMenu mobile-view"> <i class="fa fa-times text-muted"></i> </span>
            <a class="navbar-brand hidden " href="./"><img src="images/srs.PNG" alt="Logo"></a>
        </div>

        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav mt-30">
                <li class="">
                    <a href="home" class="pt-0 navigate"> <i class="menu-icon mt-0 fa fa-dashboard"></i>Dashboard </a>
                </li>

                <!-- <h3 class="menu-title">People</h3> -->
                <!-- /.menu-title -->
                <!-- <li class="menu-item-has-children dropdown">
                    <a href="statistic" class="" aria-expanded="false"> <i class="menu-icon ti-eye"></i>Ibarurishamibare</a>
                </li> -->

                <?php if (session::exists("level") && session::get("level") == "2") { ?>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-users"></i>Imiryango</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li>
                                <a href="add-family" class="navigate">
                                    <i class="fa fa-plus pr-5"></i>
                                    <span>Ongeramo Umukuru <br> w'umuryango</span>
                                </a>
                            </li>
                            <li>
                                <a href="choose-family" class="navigate">
                                    <i class="fa fa-users pr-5"></i>
                                    <span>Ongeramo Abagize <br> umuryango</span>
                                </a>
                            </li>
                            <li>
                                <a class="navigate" href="diplomats-list?l=all">
                                    <i class="fa fa-users pr-5"></i>
                                    <span>Reba Imiryango</span>
                                </a>
                            </li>
                            <!-- <li><a href="add-securityReport" class="navigate">Raparo y'umutekano</a></li> -->
                        </ul>
                    </li>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-lock"></i>Umutekano</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li>
                                <a href="reports" class="navigate">
                                    <i class="fa fa-file pr-5"></i>
                                    <span>Reba raport <br> y'umutekano</span>
                                </a>
                            </li>
                            <li><a href="add-securityReport" class="navigate">
                                    <i class="fa fa-plus"></i>
                                    <span>Tanga amakuru <br> y'umutekano</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="">
                        <a href="population-list" class=""> <i class="menu-icon fa fa-users"></i>Abaturage Bose</a>
                    </li>
                <?php } else {
                ?>
                    <li class="">
                        <a href="#" class="show-main-report-area "> <i class="menu-icon ti-list"></i>Ibiro &rarr;</a>
                    </li>
                    <li class="menu-item-has-children dropdown">
                        <a href="single1" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-tasks"></i>Kureba</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon ti-user pl-20 "></i><a class="pl-50 navigate" href="diplomats-list?l=all">Imiryango</a></li>
                            <li><i class="menu-icon ti-user pl-20 "></i><a class="pl-50 navigate" href="population-list">Abaturage</a></li>
                            <li><i class="menu-icon ti-user pl-20 "></i><a class="pl-50 navigate" href="reports">Raport y'umutekano</a></li>
                        </ul>
                    </li>
                <?php
                } ?>


                <?php if (session::exists("level") && session::get("level") == "1") { ?>
                    <h3 class="menu-title">Administration</h3><!-- /.menu-title -->
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti-user"></i>Abakoresha</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon ti-menu pl-20"></i><a class="pl-50" href="users" class="navigate">Urutonde</a></li>
                            <li><i class="menu-icon ti-plus pl-20"></i><a class="pl-50" href="register-user" class="navigate">kongeramo</a></li>
                        </ul>
                    </li>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti-list"></i>Logs</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon ti-menu pl-20"></i><a class="pl-50" href="login-attempts" class="navigate">Login Attempts</a></li>
                            <li><i class="menu-icon ti-menu pl-20"></i><a class="pl-50" href="user-log" class="navigate">Users Logs</a></li>
                            <li><i class="menu-icon ti-menu pl-20"></i><a class="pl-50" href="activity-log" class="navigate">Activities Logs</a></li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
</aside><!-- /#left-panel -->
<?php if (session::exists("level")) : ?>
    <div class="bg-white main-report-area">

        <h1 class="text-right  text-muted pr-15 pl-15 pt-15 fs-30"><i class="fa close-main-report-area fa-times"></i></h1>
        <!-- Institutions -->
        <!-- start: province card -->
        <?php if (session::get('level') == 7 or session::get('level') == 1) : ?>
            <div class=" mt-0" id="province-card">
                <div class="panel panel">
                    <div class="panel-heading ">
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <h4 class="text-left mb-15 fw-700"><i class="menu-icon fa fa-group mr-10"></i> Intara</h4>
                                <table class="table">
                                    <tbody id="province-list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- end: province card -->

        <!-- start: district card -->
        <?php if (session::get('level') >= 6 or session::get('level') == 1) : ?>
            <div class=" mt-20 01" id="district-card">
                <div class="panel panel scroll-div">
                    <div class="panel-heading ">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 class="text-left mb-15 d-flex  fw-700">
                                    <span class="pointer fw-normal"><i class="fa fa-arrow-left mr-10"></i> </span>
                                    <span>Uturere tugize <span id="district-name"></span></span>
                                </h4>
                                <table class="table">
                                    <tbody id="district-list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- end: disctrict card -->

        <!-- start: sector card -->
        <?php if (session::get('level') >= 5 or session::get('level') == 1) : ?>
            <div class=" mt-20" id="sector-card">
                <div class="panel panel scroll-div">
                    <div class="panel-heading ">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 class="text-left mb-15 d-flex fw-700 ">
                                    <span class="pointer fw-normal"><i class="fa mr-10 fa-arrow-left" id="district-back" onclick=""></i> </span>
                                    <span class="d-block fs-14 mt-0">Imirenge igize akarere ka <span id="sector-name"></span></span>
                                </h4>
                                <table class="table scrollTable">
                                    <tbody id="sector-list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- end: sector card -->
        <!-- start: cell card -->
        <?php if (session::get('level') >= 4 or session::get('level') == 1) : ?>
            <div class=" mt-20" id="cell-card">
                <div class="panel panel scroll-div">
                    <div class="panel-heading ">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 class="text-left d-flex mb-15 fw-700">
                                    <span class="pointer fw-normal"><i class="fa mr-10 fa-arrow-left" id='sector-back' onclick=""></i> </span>
                                    <span class="fs-14 mt-0">Utugari tugize umurenge wa <span id="cell-name"></span></span>
                                </h4>

                                <table class="table">
                                    <tbody id="cell-list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- end: cell card -->

        <!-- start: village card -->
        <?php if (session::get('level') >= 3 or session::get('level') == 1) : ?>
            <div class=" mt-20" id="village-card">
                <div class="panel panel scroll-div">
                    <div class="panel-heading ">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 class="text-left d-flex mb-15 fw-700">
                                    <span class="pointer fw-normal"><i class="fa mr-10 fa-arrow-left" id="cell-back" onclick=""></i> </span>
                                    <span class="d-block fs-14 mt-0">
                                        Imidugudu igize akagari ka <span id="village-name"></span></span>
                                </h4>
                                <table class="table">
                                    <tbody id="village-list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- end: village card -->
    <?php endif; ?>

    <!-- </center> -->
    <!-- <div> </div> -->
    </div>
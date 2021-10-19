<aside id="left-panel" class="left-panel">
    <nav class="navbar navbar-expand-sm navbar-default">
        <div class="navbar-header">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars"></i>
            </button>
            <!--<a class="navbar-brand" href="./"><img src="images/logo.png" alt="Logo"></a>-->
            <!-- <h2 style='color:white;margin-top:20px;'>PMS</h2> -->
            <img src="./images/srs.PNG" width="170" class="mt-20">
            <hr />
            <a class="navbar-brand hidden" href="./"><img src="images/logo2.png" alt="Logo"></a>
        </div>

        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active">
                    <a href="home" class="pt-0"> <i class="menu-icon fa fa-dashboard"></i>Dashboard </a>
                </li>
                <h3 class="menu-title">People</h3><!-- /.menu-title -->
                <li class="menu-item-has-children dropdown">
                    <a href="statistic" class="" aria-expanded="false"> <i class="menu-icon ti-eye"></i>statistics</a>
                </li>
                <li class="menu-item-has-children dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti-user"></i>Add</a>
                    <ul class="sub-menu children dropdown-menu">
                        <li>
                            <a href="add-family">Head of family </a>
                        </li>
                        <li>
                            <a href="choose-family">Family members</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item-has-children dropdown">
                    <a href="single1" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-tasks"></i>View</a>
                    <ul class="sub-menu children dropdown-menu">
                        <li><i class="menu-icon ti-user pl-20 "></i><a class="pl-50" href="diplomats-list?l=all">Families</a></li>
                        <li><i class="menu-icon ti-user pl-20 "></i><a class="pl-50" href="population-list">Population</a></li>
                        <li><i class="menu-icon ti-user pl-20 "></i><a class="pl-50" href="help-list">Beneficiaries</a></li>
                    </ul>
                </li>
                <?php if (session::exists("level") && session::get("level") == "1") { ?>
                    <h3 class="menu-title">Administration</h3><!-- /.menu-title -->
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti-user"></i>Users</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon ti-menu pl-20"></i><a class="pl-50" href="users">List</a></li>
                            <li><i class="menu-icon ti-plus pl-20"></i><a class="pl-50" href="register-user">Add</a></li>
                        </ul>
                    </li>

                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon ti-list"></i>Logs</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon ti-menu pl-20"></i><a class="pl-50" href="login-attempts">Login Attempts</a></li>
                            <li><i class="menu-icon ti-menu pl-20"></i><a class="pl-50" href="user-log">Users Logs</a></li>
                            <li><i class="menu-icon ti-menu pl-20"></i><a class="pl-50" href="activity-log">Activities Logs</a></li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
</aside><!-- /#left-panel -->
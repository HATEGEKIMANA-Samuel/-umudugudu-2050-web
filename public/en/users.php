<?php require_once("includes/validate_credentials.php"); ?>
<!doctype html>
<html lang="en">
<head>
    <?php require_once("includes/head.php"); ?>
    <link rel="stylesheet" href="assets/css/font-roboto-varela.css">
    <link rel="stylesheet" href="assets/css/material-icon.css">
    <style type="text/css">
        body {
            color: #566787;
            background: #f5f5f5;
            font-family: 'Varela Round', sans-serif;
            /*font-size: 13px;*/
        }
        .table-wrapper {
            background: #fff;
            padding: 20px 25px;
            margin: 30px 0;
            border-radius: 3px;
            box-shadow: 0 1px 1px rgba(0,0,0,.05);
        }
        .table-title {
            padding-bottom: 15px;
            background: #435d7d;
            color: #fff;
            padding: 16px 30px;
            margin: -20px -25px 10px;
            border-radius: 3px 3px 0 0;
        }
        .table-title h2 {
            margin: 5px 0 0;
            /*font-size: 24px;*/
        }
        .table-title .btn-group {
            float: right;
        }
        .table-title .btn {
            color: #fff;
            float: right;
            /*font-size: 13px;*/
            border: none;
            min-width: 50px;
            border-radius: 2px;
            border: none;
            outline: none !important;
            margin-left: 10px;
        }
        .table-title .btn i {
            float: left;
            /*font-size: 21px;*/
            margin-right: 5px;
        }
        .table-title .btn span {
            float: left;
            margin-top: 2px;
        }
        table.table tr th, table.table tr td {
            border-color: #e9e9e9;
            padding: 12px 15px;
            vertical-align: middle;
        }
        table.table tr th:first-child {
            width: 60px;
        }
        table.table tr th:last-child {
            width: 100px;
        }
        table.table-striped tbody tr:nth-of-type(odd) {
            background-color: #fcfcfc;
        }
        table.table-striped.table-hover tbody tr:hover {
            background: #f5f5f5;
        }
        table.table th i {
            /*font-size: 13px;*/
            margin: 0 5px;
            cursor: pointer;
        }
        table.table td:last-child i {
            opacity: 0.9;
            /*font-size: 22px;*/
            margin: 0 5px;
        }
        table.table td a {
            font-weight: bold;
            color: #566787;
            display: inline-block;
            text-decoration: none;
            outline: none !important;
        }
        table.table td a:hover {
            color: #2196F3;
        }
        table.table td a.edit {
            color: #FFC107;
        }
        table.table td a.delete {
            color: #F44336;
            cursor: pointer;
        }
        table.table td i {
            /*font-size: 19px;*/
        }

        .show-entries select.form-control {
            width: 60px;
            margin: 0 5px;
        }
        .table-filter .filter-group {
            float: right;
            margin-left: 15px;
        }
        .table-filter input, .table-filter select {
            height: 34px;
            border-radius: 3px;
            border-color: #ddd;
            box-shadow: none;
        }
        .table-filter {
            padding: 5px 0 15px;

            margin-bottom: 5px;
        }
        .table-filter .btn {
            height: 34px;
        }
        .table-filter label {
            font-weight: normal;
            margin-left: 10px;
        }
        .table-filter select, .table-filter input {
            display: inline-block;
            margin-left: 5px;
        }
        .table-filter input {
            width: 250px;
            display: inline-block;
        }
        .filter-group select.form-control {
            width: 110px;
        }
        .filter-icon {
            float: right;
            margin-top: 7px;
        }
        .filter-icon i {
            /*font-size: 18px;*/
            opacity: 0.7;
        }
        .search-box {
            position: relative;
            float: right;
            margin-bottom: 25px;
        }
        .search-box .input-group {
            min-width: 200px;
            position: absolute;
            right: 0;
        }
        .search-box .input-group-addon, .search-box input {
            border-color: #ddd;
            border-radius: 0;
        }
        .search-box .input-group-addon {
            border: none;
            border: none;
            background: transparent;
            position: absolute;
            z-index: 9;
        }
        .search-box input {
            height: 34px;
            padding-left: 28px;
            box-shadow: none !important;
            border-width: 0 0 1px 0;
        }
        .search-box input:focus {
            border-color: #3FBAE4;
        }
        .search-box i {
            color: #a0a5b1;
            /*font-size: 19px;*/
            position: relative;
            top: 2px;
            left: -10px;
        }


        table.table .avatar {
            border-radius: 50%;
            vertical-align: middle;
            margin-right: 10px;
            max-width: 30px;
        }
        .status {
            /*font-size: 14px;*/
            margin: 2px 2px 2px 2px;
            vertical-align: middle;
            line-height: 10px;
        }
        .text-success {
            color: #10c469;
        }
        .text-info {
            color: #62c9e8;
        }
        .text-warning {
            color: #FFC107;
        }
        .text-danger {
            color: #ff5b5b;
        }
        .pagination {
            float: right;
            margin: 0 0 5px;
        }
        .pagination li a {
            border: none;
            /*font-size: 13px;*/
            min-width: 30px;
            min-height: 30px;
            color: #999;
            margin: 0 2px;
            line-height: 30px;
            border-radius: 2px !important;
            text-align: center;
            padding: 0 6px;
        }
        .pagination li a:hover {
            color: #666;
        }
        .pagination li.active a, .pagination li.active a.page-link {
            background: #03A9F4;
        }
        .pagination li.active a:hover {
            background: #0397d6;
        }
        .pagination li.disabled i {
            color: #ccc;
        }
        .pagination li i {
            /*font-size: 16px;*/
            padding-top: 6px
        }
        .hint-text {
            float: left;
            margin-top: 10px;
            /*font-size: 13px;*/
        }
        /* Custom checkbox */
        .custom-checkbox {
            position: relative;
        }
        .custom-checkbox input[type="checkbox"] {
            opacity: 0;
            position: absolute;
            margin: 5px 0 0 3px;
            z-index: 9;
        }
        .custom-checkbox label:before{
            width: 18px;
            height: 18px;
        }
        .custom-checkbox label:before {
            content: '';
            margin-right: 10px;
            display: inline-block;
            vertical-align: text-top;
            background: white;
            border: 1px solid #bbb;
            border-radius: 2px;
            box-sizing: border-box;
            z-index: 2;
        }
        .custom-checkbox input[type="checkbox"]:checked + label:after {
            content: '';
            position: absolute;
            left: 6px;
            top: 3px;
            width: 6px;
            height: 11px;
            border: solid #000;
            border-width: 0 3px 3px 0;
            transform: inherit;
            z-index: 3;
            transform: rotateZ(45deg);
        }
        .custom-checkbox input[type="checkbox"]:checked + label:before {
            border-color: #03A9F4;
            background: #03A9F4;
        }
        .custom-checkbox input[type="checkbox"]:checked + label:after {
            border-color: #fff;
        }
        .custom-checkbox input[type="checkbox"]:disabled + label:before {
            color: #b8b8b8;
            cursor: auto;
            box-shadow: none;
            background: #ddd;
        }
        /* Modal styles */
        .modal .modal-dialog {
            max-width: 400px;
        }
        .modal .modal-header, .modal .modal-body, .modal .modal-footer {
            padding: 20px 30px;
        }
        .modal .modal-content {
            border-radius: 3px;
        }
        .modal .modal-footer {
            background: #ecf0f1;
            border-radius: 0 0 3px 3px;
        }
        .modal .modal-title {
            display: inline-block;
        }
        .modal .form-control {
            border-radius: 2px;
            box-shadow: none;
            border-color: #dddddd;
        }
        .modal textarea.form-control {
            resize: vertical;
        }
        .modal .btn {
            border-radius: 2px;
            min-width: 100px;
        }
        .modal form label {
            font-weight: normal;
        }
    </style>
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

<!--     <div class="breadcrumbs">
        <div class="col-sm-4">
            <div class="page-header float-left">
                <div class="page-title">
                    <h1>Dashboard</h1>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="page-header float-right">
                <div class="page-title">
                    <ol class="breadcrumb text-right">
                        <li><a href="home">Dashboard</a></li>
                        <li class="active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div> -->
    <div class="content ">
        <div class="animated p-10">
            <div class="table-wrapper mt-0">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-4">
                            <h2>User <b>Details</b></h2>
                        </div>
                        <div class="col-sm-8">
                            <a href="register-user" class="btn btn-primary"><i class="material-icons">add</i> <span>Add new User</span></a>
                        </div>
                    </div>
                </div>
                <div class="table-filter">
                    <div class="row">
                        <div class="col-sm-3">

                        </div>
                        <div class="col-sm-9">
                            <div class="search-box">
                                <div class="input-group" id="igroup">
                                    <span class="input-group-addon"><i class="material-icons">&#xE8B6;</i></span>
                                    <input id="search" type="text" autocomplete="off" onkeyup="search()" class="form-control" placeholder="Search&hellip;">
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <table class="table table-striped fs-13 table-hover" id="user-table">
                    <thead>
                    <tr>

                        <th>#</th>
                        <th>Names</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Institution</th>
                        <th>Access Level</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="test">
                    <?php $sql="SELECT * FROM user WHERE status = '1'";
                    $result=$database->query($sql);
                    $count=1;
                    while ($row=$database->fetch_array($result)) {?>
                        <tr>

                            <td><?=$count; ?></td>
                            <?php if ($row["avatar"] !="" && (file_exists("uploads/avatar/".$row["avatar"]))){
                                $src="uploads/avatar/".$row["avatar"];
                            }
                            else{
                                $src="images/default_profile.jpg";
                            }?>
                            <td><img src="<?php echo $src; ?>" class="avatar" alt="Avatar"><?php echo $row['fname']." ".$row['mname']." ".$row["lname"]; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <?php
                            if($row['active']==0){ ?>
                                <td><span class="status badge badge-danger">Inactive</td>
                            <?php }elseif($row['active']==1) { ?>
                                <td><span class="status badge badge-primary">Active</td>
                            <?php }?>

                            <?php if($row['id_institution']==0){ ?>
                                <td>MOFA</td> <?php }else{ ?>
                                <td><?php echo $database->get_item("institutions","id",$row['id_institution'],"name");?></td><?php }?>
                            <td><?php echo $database->get_item("level","id",$row['level'],"name");?></td>
                            <td>
                                <a href="edituser?id=<?php echo encrypt_decrypt('encrypt',$row['id']);?>" class="edit"><i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i></a>
                                <a class="delete" onclick="return confirm('Are you sure to delete data?')?deleteUser('<?php echo $row['id']; ?>',this):false;"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i></a>
                            </td>
                        </tr>
                        <?php  $count++; }
                    ?>

                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- .animated -->
</div><!-- .content -->

</div><!-- /#right-panel -->

<!-- Right Panel -->


<script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
<script src="assets/js/vendor/jquery-1.11.3.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/custom.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip;
        /*  var searchInput = _("search");
          var inputGroup = _("igroup");
          var boxWidth = inputGroup.width();
          searchInput.focus(function(){
              inputGroup.animate({
                  width: "300"
              });
          }).blur(function(){
              inputGroup.animate({
                  width: boxWidth
              });
          });*/


    });
    function deleteUser(id,r) {

        // AJAX Request
        var ajax = ajaxObj("POST", "userAction");
        ajax.onreadystatechange = function() {
            if(ajaxReturn(ajax) == true) {
                if(ajax.responseText == "deleted"){
                    var i = r.parentNode.parentNode.rowIndex;
                    document.getElementById("user-table").deleteRow(i);
                }
            }
        };
        ajax.send("id="+id+"&delete=delete");


    }

    function search() {
        var word=_('search').value;
        $.ajax({
            url:"userAction",
            method: "post",
            data:{
                word:word
            },
            success : function (data) {

                _("test").innerHTML=data;
            }
        });

    }



</script>
</body>
</html>



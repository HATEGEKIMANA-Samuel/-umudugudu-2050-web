<?php 
require_once("includes/validate_credentials.php");
?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js" lang=""> <!--<![endif]-->
<head>
<?php require_once("includes/head.php"); ?>
 
<link rel="stylesheet" href="assets/css/font-roboto-varela.css">
<link rel="stylesheet" href="assets/css/material-icon.css">
<style type="text/css">
	 .tab-container{
        background: #fff;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        padding-bottom: 20px;
       } 
       
      .search-box {
            position: relative;
            float: right;
            margin-bottom: 35px;
        }
        .search-box .input-group {
            min-width: 300px;
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
            font-size: 19px;
            position: relative;
            top: 2px;
            left: -10px;
        }
        
        .table{
        	width: 98%;
        	margin-left: 1%;
        	margin-right: 1%;
        }
        
        .table td{
        	    font-family: Lato-Regular;
			    font-size: 15px;
			    color: #808080;
			    line-height: 1.4;
        }
        .numbering{
          font-weight: bold;
        }
         .views{
        	
        	color: red;
        }
        

</style>
</head>
<body>
    <style type="text/css">
    </style>
    <?php require_once 'includes/left_nav.php'; ?>

    <div id="right-panel" class="right-panel">
        <!-- Header-->
     <?php require_once 'includes/top_nav.php'; ?>
    <div class="container">
        <div class="tab-container mt-10 p-20">
            <div class="tab-content">
                <h4 class="text-left fs-18 fw-500">User Logs <hr></h4>
                <div class="row">
                        <div class="col-sm-3">

                        </div>
                        <div class="col-sm-9">
                          <form  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                            <div class="search-box">
                                <div class="input-group" id="igroup">
                                    <span class="input-group-addon"><i class="material-icons">&#xE8B6;</i></span>
                                    <input id="search_institution" type="text" name="search_institution" autocomplete="off" autofocus class="form-control" placeholder="Search&hellip;">
                                </div>
                            </div>
                          </form>
                        </div>
                    </div>
                            
                
                  
                    </div>
                           

            </div> 
        </div>
 </div>
<script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="js/ajax.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/custom.js"></script>
<script>
     $('.datepicker').datepicker({
       format: 'yyyy-mm-dd'
     });
</script>

</body>
</html>
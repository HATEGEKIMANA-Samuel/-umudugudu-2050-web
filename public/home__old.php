<?php require_once("includes/validate_credentials.php"); ?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
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
<style type="text/css">
    a {
    color: #0487cc;
    text-decoration: none;
}
</style>
</head>
<body>
   <?php
    if(isset($_POST['category']) && !empty($_POST['category'])){
    	$institution_category = htmlentities($database->escape_value($_POST['category']));
    	$cat_check = strtolower($institution_category);
    	$found_category  = $database->num_rows($database->query("SELECT id FROM institution_categories WHERE LOWER(name)='$cat_check' AND status='1' LIMIT 2 "));
	    if ($found_category==0) {
			 $database->query("INSERT INTO institution_categories (Name) VALUES ('$institution_category')");
		}
    }
    ?>
    <!-- Left Panel -->
      <?php require_once 'includes/left_nav.php'; ?>
    <!-- Left Panel -->
    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">
        <!-- Header-->
     <?php require_once 'includes/top_nav.php'; ?>
        <!-- Header-->

       
<!-- Main content -->
        <div class="content mt-3">

            <!-- <div class="row p-20"> -->
                <div class="col-md-9 p-20">
                    <h3>Institution Categories</h3><small>Click on the pen icon to edit.</small>
                </div>
                <div class="col-md-3 p-20 text-right">
                    <button class="btn-primary btn fs-14 fw-500" data-toggle="modal" data-target="#addmodal"> Add Institution
                    </button>
                </div>
                <!-- <hr class="mb-30"> -->
            <!-- </div> -->
            <!-- <p>
                <span class="float-left">
                    <h3>Institutions</h3>
                </span>
                <span class="float-right">
                    
                </span>
                <hr class="mb-30">
            </p> -->

            <!-- diplaying main content -->
            <!-- <center class="mt-20"> -->
            <?php
          try {

        $stmt = $database->query("SELECT Id, Name FROM institution_categories  WHERE status ='1'  ORDER BY Id DESC");
         while($row = $database->fetch_array($stmt)){
            $stmtt = $database->query('SELECT COUNT(*)
                                FROM institutions
                                WHERE category_id = "'.$row['Id'].'"');
            $rowc = $database->fetch_array($stmtt);
            $value['id']= $row['Id'];
            
                                        
            echo '
                     <div class="col-lg-4 mt-20 col-md-6">
                        <div class="panel panel box-shadow ">
                            <div class="panel-heading ">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <a href="editinstitutioncat.php?id='.encrypt_decrypt('encrypt',$value['id']).'"><i class="fa fa-edit fs-14 p-10 cool-blue-bg" ></i></a>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge fs-20">'.$rowc[0].'</div>
                                        <div><p class="fs-14">'.$row['Name'].'</p></div>
                                    </div>
                                </div>
                            </div>
                            <a href="institutions.php?search_institution='.$row['Name'].'">
                                <div class="panel-footer">
                                    <span class="pull-left fs-13">View List</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>

                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>      
            ';
             }
            } catch(PDOException $e) {
             echo $e->getMessage();
             }
                                
          ?>
         </center>
     <!-- <div> </div>           -->
    
     <div class="modal fade" id="addmodal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="mediumModalLabel">Add Institution Category</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            <form action='' method='post' name="form">
                                <label for="Name">Name</label>
                                <div class="form-group">
                                <div class="form-line">
                                 <input type="text" id="Name" class="form-control" name="category" value='<?php if(isset($error)){ echo $_POST['category'];}?>' required>
                                </div>
                                

                                </div>

                                <input type='submit' name='save' value='Save' class="btn btn-primary btn-lg">
                            </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cancel</button>
                                
                            </div>
                        </div>
                    </div>
                </div>
    
        </div> 
        <!-- end of main content -->
    </div><!-- /#right-panel -->

    <!-- Right Panel -->

    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/lib/chart-js/Chart.bundle.js"></script>
    <!-- <script src="assets/js/dashboard.js"></script> -->
    <script src="assets/js/widgets.js"></script>
</body>
</html>

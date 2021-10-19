<?php require_once("includes/validate_credentials.php"); ?>
<?php 
$institution = encrypt_decrypt('decrypt', $_GET['institution']);
if(!isset($institution) && !is_numeric($institution)) {
    header("location:register-ngo?message=1");
}
$messages = '';
if (isset($_GET['messages']) && $_GET['messages'] ==2) {
	$messages = "<span style='color:red;'>Please fill out all required fields</span>";
}
if (isset($_POST['save'])){
    $date = $_POST['date'];
    $benefits = $database->escape_value($_POST['benefits']);
    $meeting = $database->escape_value($_POST['meeting']);
    $animal = $database->escape_value($_POST['animal']);
    $responsible_ministry = $database->escape_value($_POST['responsible_ministry']);
    $id = $_POST['id_to_check'];

    if ($date =='' || $responsible_ministry=='' || $animal=='') {
		$idd=encrypt_decrypt('encrypt', $id);
	    header("location:register-ngo-step2?institution=$idd&messages=2");
    } else {
        $database->query("UPDATE institutions SET payment_date='$date',anual_contribution='$animal',responsible_ministry='$responsible_ministry' WHERE id=$id");
        $id=encrypt_decrypt('encrypt', $id);
        header("location:display?inst=$id");
    }
}
?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
<?php require_once("includes/head.php"); ?>
</head>
<body>
 <!--    <style type="text/css">
    select {
    background-color: #F5F5F5;
    border: 1px double #15a6c7;
    color: #1d93d1;
    font-family: Georgia;
    font-weight: bold;
    font-size: 14px;
    height: 39px;
    padding: 7px 8px;
    width: 250px;
    outline: none;
    margin: 10px 0 10px 0;
}
select option, .form-control{
    font-family: Georgia;
    font-size: 14px;
}

label{
    font-weight: bold;
    font-family: serif;
}
       .tab-container{
        background: #fff;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        padding-bottom: 20px;
       }
       .tab-container nav{
        background: transparent;
        border: none;
       }
       .tab-content{
        padding: 0px 40px;
       }
       .tab-content legend{
            margin-bottom: 20px;
            margin-top:40px; 
            text-align: center;
            color: #272c33;
            font-family: georgia;
       }
       .tab-content-body{
        margin: 0 auto;
        width: 80%;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
       }
       .btn ,.btn-primary{background: #272c33;color: #fff}
       #nav-tab{width: 80%;margin:0 auto;}
       #nav-tab ul
        {
        margin: 0;
        background: #000;
        padding: 0;
        list-style-type: none;
        text-align: center;
        }

        #nav-tab ul li { display: inline;float: left;padding: 15px 18%;background:#272c33;border-right: 1px solid grey }
        #nav-tab ul li a{
            color: #fff;
        }
        #nav-tab ul li#active{background: #E74C3C !important}
    </style> -->

        <style type="text/css">
    .error{
      color: maroon;
    }
  
select option, .form-control{
    font-size: 14px;
}

label{
    /*font-weight: bold;*/
    /*font-family: serif;*/
    font-size: 13px !important;
}


       .tab-container{
        background: #fff;
        /*box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);*/
        transition: 0.3s;
        padding-bottom: 20px;
       }
       .tab-container nav{
        background: transparent;
        border: none;
       }
       .tab-content{
        padding: 0px 40px;
       }
       .tab-content legend{
            margin-bottom: 20px;
            margin-top:40px; 
            text-align: center;
            color: #272c33;
       }
       .tab-content-body{
        margin: 0 auto;
        width: 95%;
        padding: 20px;
        margin-bottom: 20px;
        /*box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);*/
        transition: 0.3s;
       }
       .btn ,.btn-primary{background: #272c33;color: #fff}
       #nav-tab{width: 80%;margin:0 auto;}
       #nav-tab ul
        {
        margin: 0;
        background: #000;
        padding: 0;
        list-style-type: none;
        text-align: center;
        }

        #nav-tab ul li { display: inline;float: left;padding: 8px 18%;background:#272c33;border-right: 1px solid grey }
        #nav-tab ul li a{
            color: #fff;
        }
        #nav-tab ul li#active{background: #E74C3C !important}
        
        .fiedset-type{
          border: 1px solid #eceff0;
          margin-bottom: 10px;
          background-color: #dbecfd57 !important;
          
        }
        .fiedset-type .fieldset-legend{
          width:inherit;
          padding:0 10px;
          border-bottom:none;
          text-align: left;
          margin-left: 10px;
          font-weight: 500
        }
       .fiedset-type .form-group{
        width: 98%;
        margin-left: 5px;
       }
    </style>


    <?php require_once 'includes/left_nav.php'; ?>

    <div id="right-panel" class="right-panel">              

        <!-- Header-->
     <?php require_once 'includes/top_nav.php'; ?>
    <div class="container">
        <div class="tab-container">
            <div class="tab-content mt-10" id="nav-tabContent">
                <div id="nav-tab">
                    <di class="row">
                        <div class="col-lg-12">
                            <ul>
                            <?php if (isset($institution) && is_numeric($institution)) {?>
                                <?php   
                                $query =$database->query("SELECT * FROM institutions WHERE id = '$institution' LIMIT 1");
                                $row  = $database->fetch_array($query);
                                ?>
                                  <li><a href="register-ngo?institution=<?php echo encrypt_decrypt('encrypt', $institution);?>">Basic Info</a></li>
                                  <li id="active"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Additional Info</a></li>
                                <?php }?>
                            </ul>
                        </div>
                    </di>
                </div>
                <div class="tab-content-body">
                    <div class="row">
                        <div class="col-lg-12 mt-10">
                            <h5 class="text-center fs-15 fw-500">Registration Form <?=$row['name'];?> <hr class="mt-20 mb-20"></h5>
                        </div>
                    </div>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" id="form">
                    	<di class="row">
                         <div class="col-lg-12 fs-14"><?php echo $messages;?></div>   
                        </di>
                        <di class="row">
                            <div class="col-lg-12">
                               <div class="form-group">
                                <label for="name">Line Ministry<span class="required-mark">*</span></label>
                                <input type="text" class="form-control" name="responsible_ministry" value="<?php if (isset($institution) && is_numeric($institution))echo $row['responsible_ministry'];?>"  placeholder="Line Ministry">
                              </div>
                                
                            </div>  
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="name">Government Contribution<span class="required-mark">*</span></label>
                                    <input type="text" class="form-control" name="animal" value="<?php if (isset($institution) && is_numeric($institution))echo $row['anual_contribution'];?>" id="animal" placeholder="Government Contribution">
                                  </div>  
                                  <input type="hidden" name="id_to_check" value="<?=$institution;?>">
                            </div> 
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="name">Deadline For Payment<span class="required-mark">*</span></label>
                                    <input type="date" class="form-control" id="date" value="<?php if (isset($institution) && is_numeric($institution))echo $row['payment_date'];?>" name="date" placeholder="eg: 01 - jan">
                                  </div>
                            </div>

                            <div class="col-lg-offset-3 col-lg-3 mt-10">
                                <a class="btn w-100p fs-15 mt-10" href="register-ngo?institution=<?=encrypt_decrypt('encrypt', $institution)?>" style="color: white">Previous</a>
                            </div>
                            <div class="col-lg-3 mt-10">
                                <button class="btn w-100p fs-15 mt-10" type="submit" name="save">Save</button>
                            </div>
                        </di>
		                    
                    </form>
                </div>
            </div> 
        </div>
    </div>
 </div>  
<script src="assets/js/vendor/jquery-1.9.1.js"></script>
<script src="assets/js/vendor/jquery-validate.min.js"></script>
<script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
<script src="assets/js/sweetalert.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/lib/vector-map/jquery.vmap.js"></script>
<script src="assets/js/lib/vector-map/jquery.vmap.min.js"></script>
<script src="assets/js/lib/vector-map/jquery.vmap.sampledata.js"></script>
<script src="assets/js/lib/vector-map/country/jquery.vmap.world.js"></script>
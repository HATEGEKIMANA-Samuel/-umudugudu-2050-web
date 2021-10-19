<?php 
require_once("includes/validate_credentials.php");
if (isset($_GET['dpl'])){
	$diplomat = encrypt_decrypt('decrypt', $_GET['dpl']);
}
if (isset($_POST['diplomat'])) {
	$diplomat = $_POST['diplomat'];
}
if (!isset($diplomat) || !is_numeric($diplomat)) {
	header("location:404");
}
if (isset($_GET['cr'])){
	$car = encrypt_decrypt('decrypt', $_GET['cr']);
}
if (isset($car) && !is_numeric($car)) {
    $dpl=rawurlencode(encrypt_decrypt('encrypt', $diplomat));
	header("location:family?dpl=$dpl");
}

$messages = '';
if (isset($_GET['messages']) && $_GET['messages'] ==1){
	$messages = "<span style='color:red;'></span>";
}
if (isset($_GET['messages']) && $_GET['messages'] ==2) {
	$messages = "<span style='color:red;'>Please fill out all required fields</span>";
}
if (isset($_POST['save_symptoms'])) {
	 

$symptoms = htmlentities($database->escape_value($_POST['symptoms']));
$type = htmlentities($database->escape_value($_POST['type']));
$family = htmlentities($database->escape_value($_POST['family']));
$action = htmlentities($database->escape_value($_POST['action']));
$diplomat = htmlentities($database->escape_value($_POST['diplomat']));
$time_tested = htmlentities($database->escape_value($_POST['time_tested']));

	if ($time_tested =='') {
		$messages = "<span style='color:red;'>Please fill out all required fields</span>";
	}else {
		
	    $sql= "INSERT INTO covid(person,role,type,action,symptoms,time,user)
	                       VALUES($diplomat,'$family','$type','$action','$symptoms',NOW(),{$_SESSION["id"]})";
	    if ($database->query($sql)) {
	        $id=rawurlencode(encrypt_decrypt('encrypt', $diplomat)).rawurlencode("%covid_19");
	        header("location:family?dpl=$id");
	    }
	}

}

?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js" lang=""> <!--<![endif]-->
<head>
<?php require_once("includes/head.php"); ?>
</head>
<body>

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

        #nav-tab ul li { display: inline;float: left;padding: 15px 18%;background:#272c33;border-right: 1px solid grey }
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
                
                    <?php 

                        if (isset($diplomat) && is_numeric($diplomat)) {
                            $diplomat_data =$database->fetch_array($database->query("SELECT given_name,family_name,other_name FROM diplomats WHERE id = '$diplomat' LIMIT 1"));
                                  
                          }
                          $text = "Adding";
                          
                     
                     ?>
                          
                <div class="tab-content-body">
                   <h4 class="text-center fs-16 mt-20"><?=$text?> <span style="color:green;"><?php echo "{$diplomat_data['given_name']} {$diplomat_data['family_name']}"; ?></span>'s Covid-19 symptoms <hr class="mt-20 mb-20"></h4>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form">
                	<?php echo $messages;?>
                	<?php if (isset($_GET['type'])) {?>
                		<input type="hidden" name="type" value="<?php echo $_GET['type']; ?>" />
                	<?php }?>

                  <?php if (isset($_GET['family'])) {?>
                		<input type="hidden" name="family" value="<?php echo $_GET['family']; ?>" />
                	<?php }?>
                	
                	<?php if (isset($diplomat) && is_numeric($diplomat)) {?>
                		<input type="hidden" name="diplomat" value="<?php echo $diplomat; ?>" />
                	<?php }?>

                  <div class="row">

                  <div class="col-md-6">
                      <div class="form-group date">
                        <label>Date<span class="required-mark">*</span></label>
                         <input type="text" maxlength="20"  autocomplete="off" placeholder="Date tested positive" class="form-control datepicker required"  name="time_tested" >
                       </div>
                    </div>
                  

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="name">Action taken<span class="required-mark">*</span></label>
                        <select class="form-control required" name="action">
                            <option value=""> -- Select --</option>
                            <option value='Quarantine'>Quarantine</option>
                              <option value='Self quarantine'>Self quarantine</option>
                        </select>
                      </div>

                    </div>

                    <div class="col-lg-12">
                      <div class="form-group">
                        <label for="name">Symptoms</label></label>
                        <textarea class="form-control" rows="5"  name="symptoms"><?php if(isset($_POST['symptoms'])){echo $_POST['symptoms'];}?></textarea>
                      </div>
                    </div>

                    <div class="col-lg-4 col-lg-offset-4">
                      <button  type="submit" name="save_symptoms" class="btn w-100p fs-15 btn-primary mt-10 p-10 pull-right">Save</button>
                    </div>

                    <!-- <div class="col-lg-6"></div> -->
                  </div>
                    </form>
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
       endDate: '0d',
       format: 'yyyy-mm-dd'
     });
     
     $('.datepicker1').datepicker({
       format: 'yyyy-mm-dd'
     });
  
</script>

</body>
</html>
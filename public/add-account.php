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
if (isset($_GET['bank'])){
	$bank = encrypt_decrypt('decrypt', $_GET['bank']);
}
if (isset($bank) && !is_numeric($bank)) {
    $dpl=rawurlencode(encrypt_decrypt('encrypt', $diplomat));
	header("location:add-account?dpl=$dpl");
}

$messages = '';
if (isset($_GET['messages']) && $_GET['messages'] ==1){
	$messages = "<span style='color:red;'>Found same account number for this person</span>";
}
if (isset($_GET['messages']) && $_GET['messages'] ==2) {
	$messages = "<span class='text-danger text-center'>Please fill out all required fields</span>";
}
if (isset($_POST['save_account'])) {
	 
$account = htmlentities($database->escape_value($_POST['account']));
$number = htmlentities($database->escape_value($_POST['number']));
$bank_name = htmlentities($database->escape_value($_POST['bank_name']));
$iban_number = htmlentities($database->escape_value($_POST['iban_number']));
$swift_code = htmlentities($database->escape_value($_POST['swift_code']));
$bank_address = htmlentities($database->escape_value($_POST['bank_address']));
$currency = htmlentities($database->escape_value($_POST['currency']));

if (!isset($_POST['id_to_edit'])){
	$number = strtolower($number);
	$bank_name = strtolower($bank_name);
	$query =$database->query("SELECT id FROM bank WHERE (LOWER(bank_name)='$bank_name' AND LOWER(number) = '$number') AND deleted='0' LIMIT 1 ");
	$found_account  = $database->num_rows($query);
	if ($account =='' || $number =='' || $bank_name =='') {
		$messages = "<span class='text-danger text-center'>Please fill out all required fields</span>";
	}else if ($found_account >0) {
		$messages = "<span style='color:red;'>Account number already registered</span>";
	} else {
		
	    echo $sql= "INSERT INTO bank(`name`,`number`,`iban_number`,`swift_code`,`bank_name`,`bank_address`,`currency`,`diplomat`,time,user) 
	                      VALUES('$account','$number','$iban_number','$swift_code','$bank_name','$bank_address','$currency','$diplomat',NOW(),{$_SESSION["id"]})";
	    if ($database->query($sql)) {
	        $id=rawurlencode(encrypt_decrypt('encrypt', $diplomat)).rawurlencode("%bank");
	        header("location:diplomats?dpl=$id");
	    }
	}
}else{
	$id =$_POST['id_to_edit'];
	$number = strtolower($number);
	$bank_name = strtolower($bank_name);

	$query =$database->query("SELECT id FROM bank WHERE (LOWER(number)='$number' AND LOWER(bank_name) = '$bank_name') AND id !=$id AND deleted !='0' LIMIT 1 ");
  echo "SELECT id FROM bank WHERE (LOWER(number)='$number' OR LOWER(bank_name) = '$bank_name') AND id !=$id AND deleted !='0' LIMIT 1 ";
	$found_account  = $database->num_rows($query);
	if ($account =='' || $number =='' || $bank_name ==''){
		$id=rawurlencode(encrypt_decrypt('encrypt', $id));
		$dpl = rawurlencode(encrypt_decrypt('encrypt', $diplomat));
		header("location:add-account?dpl=$dpl&bank=$id&messages=2");
	}else if ($found_account >0) {
		$id=rawurlencode(encrypt_decrypt('encrypt', $id));
		$dpl = rawurlencode(encrypt_decrypt('encrypt', $diplomat));
		header("location:add-account?dpl=$dpl&bank=$id&messages=1");
	}else{
    $sql = "UPDATE bank SET name='$account',number='$number',iban_number='$iban_number',swift_code='$swift_code',bank_name='$bank_name',bank_address='$bank_address',currency='$currency' WHERE id=$id";
    if ($database->query($sql)) {
        $dpl=rawurlencode(encrypt_decrypt('encrypt', $diplomat)).rawurlencode("%bank");
        header("location:diplomats?dpl=$dpl");
    }
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
<!--     <style type="text/css">
    .error{
      color: maroon;
    }
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
        
        .fiedset-type{
        	border: 1px solid gray;
        	margin-bottom: 10px;
        	
        }
        .fiedset-type .fieldset-legend{
        	width:inherit;
        	padding:0 10px;
        	border-bottom:none;
        	text-align: left;
        	margin-left: 10px;
        	font-family: serif;
        	font-size: inherit;
        	font-weight: bold;
        }
       .fiedset-type .form-group{
       	width: 98%;
       	margin-left: 5px;
       }
    </style> -->


      <style type="text/css">

      body{
        /*background-color: #fff !important;*/
      }
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
        width: 90%;
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
    <div class="container ">
        <div class="tab-container mt-10">
            <div class="tab-content" id="nav-tabContent" style="height: 100vh;">
                
                    <?php 
	                    if (isset($diplomat) && is_numeric($diplomat)) {
	                    		$diplomat_data =$database->fetch_array($database->query("SELECT given_name,family_name,other_name FROM diplomats WHERE id = '$diplomat' LIMIT 1"));
					          
            						}
            						$text = "New bank account for ";
            						if (isset($bank) && is_numeric($bank)) {
            	                    $query =$database->query("SELECT * FROM bank WHERE id = '$bank' AND deleted='0' LIMIT 1");
            					            $row  = $database->fetch_array($query);
            							$text ="Editting ";	
            						}
                     ?>
                          
                <div class="tab-content-body">
                   <h4 class="text-center  mt-20 fw-500 fs-18"><?=$text?> <span class="text-success"><?php echo "{$diplomat_data['given_name']} {$diplomat_data['family_name']}"; ?></span>'s account <hr class="mb-30 mt-30"> </h4>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $_SERVER['QUERY_STRING']; ?>" method="POST" id="form">
                	<div class="col-md-12 fs-14"> <?php echo $messages;?></div>
                	<?php if (isset($bank) && is_numeric($bank)) {?>
                		<input type="hidden" name="id_to_edit" value="<?php echo $bank; ?>" />
                	<?php }?>
                	
                	<?php if (isset($diplomat) && is_numeric($diplomat)) {?>
                		<input type="hidden" name="diplomat" value="<?php echo $diplomat; ?>" />
                	<?php }?>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Beneficially name<span class="required-mark">*</span></label>
                      <input type="text" class="form-control" maxlength="255"  name="account"  value="<?php if (isset($bank) && is_numeric($bank)){echo $row['name'];}elseif(isset($_POST['account'])){echo $_POST['account'];}?>">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Account number<span class="required-mark">*</span></label>
                      <input type="text" class="form-control" maxlength="100"  name="number"  value="<?php if (isset($bank) && is_numeric($bank)){echo $row['number'];}elseif(isset($_POST['number'])){echo $_POST['number'];}?>">
                    </div>
                  </div>


                  <div class="col-md-6">
                    
                    <div class="form-group">
                      <label for="name">IBAN number</label>
                      <input type="text" class="form-control" maxlength="50"  name="iban_number"  value="<?php if (isset($bank) && is_numeric($bank)){echo $row['iban_number'];}elseif(isset($_POST['iban_number'])){echo $_POST['iban_number'];}?>">
                    </div>

                  </div>
                  <div class="col-md-6">
                    
                      <div class="form-group">
                        <label for="name">Swift code</label>
                        <input type="text" class="form-control" maxlength="50"  name="swift_code"  value="<?php if (isset($bank) && is_numeric($bank)){echo $row['swift_code'];}elseif(isset($_POST['swift_code'])){echo $_POST['swift_code'];}?>">
                      </div>

                  </div>

                  <div class="col-md-6">
                    
                    <div class="form-group">
                      <label for="name">Bank name<span class="required-mark">*</span></label>
                      <input type="text" class="form-control" maxlength="255"  name="bank_name"  value="<?php if (isset($bank) && is_numeric($bank)){echo $row['bank_name'];}elseif(isset($_POST['bank_name'])){echo $_POST['bank_name'];}?>">
                    </div>

                  </div>

                  <div class="col-md-6"> 
                      <div class="form-group">
                        <label for="name">Bank address</label>
                        <input type="text" class="form-control" maxlength="255"  name="bank_address"  value="<?php if (isset($bank) && is_numeric($bank)){echo $row['bank_address'];}elseif(isset($_POST['bank_address'])){echo $_POST['bank_address'];}?>">
                      </div>
                  </div>

                  <div class="col-md-12"> 
                      <div class="form-group">
                        <label for="name">Currency</label>
                        <input type="text" class="form-control" maxlength="50"  name="currency"  value="<?php if (isset($bank) && is_numeric($bank)){echo $row['currency'];}elseif(isset($_POST['currency'])){echo $_POST['currency'];}?>">
                      </div>
                  </div>

                  <div class="col-md-12 mt-10"> 
                      <button  type="submit" name="save_account" class="btn btn-primary h-35 w-100p fs-15 pull-right">Save</button>
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
<script src="assets/js/main.js"></script>
<script src="assets/js/custom.js"></script>
<script src="js/ajax.js"></script>
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
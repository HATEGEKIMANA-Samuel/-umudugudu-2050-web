<?php 
require_once("includes/validate_credentials.php");
 $diplomat ='';
if (isset($_GET['dpl'])){
  $diplomat = encrypt_decrypt('decrypt', $_GET['dpl']);
}
if (isset($_POST['diplomat'])) {
  $diplomat = $_POST['diplomat'];
}
if (!isset($diplomat) || !is_numeric($diplomat)) {
  header("location:404");
}
if (isset($_GET['Emphistory'])){
 $history = encrypt_decrypt('decrypt', $_GET['Emphistory']);
}
if (isset( $history) && !is_numeric( $history)) {
    $dpl=rawurlencode(encrypt_decrypt('encrypt', $diplomat));
  header("location:add-employe-history?dpl=$dpl");
}


if (isset($_GET['messages']) && $_GET['messages'] ==2) {
  $messages = "<span style='color:red;'>Please fill out all required fields</span>";
}
if (isset($_POST['save_history'])) {
   $companyName = htmlentities($database->escape_value($_POST['companyName']));
   $function = htmlentities($database->escape_value($_POST['function']));
   $title=htmlentities($database->escape_value($_POST['title']));
   $from=htmlentities($database->escape_value($_POST['from_in_date']));
   $to=htmlentities($database->escape_value($_POST['to_in_date']));

if (!isset($_POST['id_to_edit'])){
  if ($companyName =='' || $function =='' || $title =='' || $from =='' ||  $to=='') {
    $messages = "<span style='color:red;'>Please fill out all required fields</span>";
  } else {
    
      $sql="INSERT INTO employement_history(company_name,title,duty,startFrom,endTo,diplomat_id,user_id)
         VALUES('$companyName','$title','$function','$from','$to',$diplomat,{$_SESSION["id"]})";
    
      if ($database->query($sql)) {
          $id=rawurlencode(encrypt_decrypt('encrypt', $diplomat)).rawurlencode("%employment");
          header("location:diplomats?dpl=$id");
      }
  }
}else{
  $id =$_POST['id_to_edit'];
   if ($companyName =='' || $function =='' || $title =='' || $from =='' ||  $to=='') {
    $id=rawurlencode(encrypt_decrypt('encrypt', $id));
    $dpl = rawurlencode(encrypt_decrypt('encrypt', $diplomat));
    header("location:add-employe-history?dpl=$dpl&cre=$id&messages=2");
  }else{
    $sql = "UPDATE employement_history SET company_name='$companyName',title='$title',
    duty='$function',endTo='$to', startFrom='$from' 
     WHERE id=$id";
    if ($database->query($sql)) {
        $dpl=rawurlencode(encrypt_decrypt('encrypt', $diplomat)).rawurlencode("%employment");
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
       #decriptilon{
        resize: none;
    min-width: 300px;
       }
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

        #nav-tab ul li { display: inline;float: left;padding: 10px 18%;background:#272c33;border-right: 1px solid grey }
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
        <div class="tab-container mt-10">
            <div class="tab-content" id="nav-tabContent">
               <?php 
                      if (isset($diplomat) && is_numeric($diplomat)) {
                          $diplomat_data =$database->fetch_array($database->query("SELECT given_name,family_name,other_name FROM diplomats WHERE id = '$diplomat' LIMIT 1"));
                    
            }
             if (isset($history) && is_numeric($history)) {
                          $query =$database->query("                            
                      SELECT id, company_name as cname,title,duty,startFrom,endTo FROM employement_history WHERE id='$history' AND status='1' LIMIT 1");
                      $row  = $database->fetch_array($query);
              $text ="Editing"; 
            }
            ?>
            <div class="tab-content-body">
                   <div class="row">
                     <div class="col-lg-12">
                       <h4 class="text-center fs-18 mt-20 fw-500"><?=isset($text)?$text:'Adding'?> <span style="color:green;">
                      <?php echo "{$diplomat_data['given_name']}
                       {$diplomat_data['family_name']}"; ?></span>'s employement History <hr class="mt-20 mb-20"></h4>
                     </div>
                   </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="form">
                  <div class="row">
                     <div class="col-lg-12 fs-13">
                       <?php  echo isset($messages) ? $messages:'';?>
                     </div>
                   </div>

                   <div class="row">
                     <div class="col-lg-6">
                        <div class="form-group">
                          <label for="name">Company Name<span class="required-mark">*</span></label>
                          <input type="text" class="form-control"  placeholder="company Name was worked on " name="companyName"  value="<?php if (isset($history) && is_numeric($history)){echo $row['cname'];}elseif(isset($_POST['companyName'])){echo $_POST['companyName'];}?>">
                        </div>
                     </div>
                     <div class="col-lg-6">
                        <div class="form-group">
                          <label for="name">title<span class="required-mark">*</span></label>
                          <input type="text" class="form-control"  placeholder="title was hold in the company" name="title" value="<?php if (isset($history) && is_numeric($history)){echo $row['title'];}elseif(isset($_POST['title'])){echo $_POST['title'];}?>">
                        </div>
                     </div>
                     <div class="col-lg-12">
                        <div class="form-group">
                          <label for="name">responsibility
                           <span class="required-mark">*</span></label>
                          <input type="text" class="form-control" maxlength="255" placeholder="Duty was hold in Company" name="function" value="<?php if (isset($history) && is_numeric($history)){echo $row['duty'];}elseif(isset($_POST['function'])){echo $_POST['function'];}?>">
                        </div>
                     </div>
                     <div class="col-lg-6">
                        <div class="form-group">
                          <label for="name"> From
                           <span class="required-mark">*</span></label>
                          <input type="date" class="form-control" name="from_in_date" value="<?php if (isset($history) && is_numeric($history)){echo $row['startFrom'];}elseif(isset($_POST['from_in_date'])){echo $_POST['from_in_date'];}?>">
                        </div>
                     </div>
                     <div class="col-lg-6">
                        <div class="form-group">
                          <label for="name">To
                           <span class="required-mark">*</span></label>
                          <input type="date" class="form-control" name="to_in_date" value="<?php if (isset($history) && is_numeric($history)){echo $row['endTo'];}elseif(isset($_POST['to_in_date'])){echo $_POST['to_in_date'];}?>">
                        </div>
                     </div>
                     <div class="col-lg-4 col-lg-offset-4">
                        <button  type="submit" name="save_history" class="btn btn-primary w-100p fs-15 h-35 mt-20">Save</button>
                     </div>
                   </div>
                  
                  <?php if (isset($history) && is_numeric($history)) {?>
                    <input type="hidden" name="id_to_edit" value="<?php echo $history ?>" />
                  <?php }?>
                  
                  <?php if (isset($diplomat) && is_numeric($diplomat)) {?>
                    <input type="hidden" name="diplomat" value="<?php echo $diplomat; ?>" />
                  <?php }?>
                 
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
       endDate: '0d',
       format: 'yyyy-mm-dd'
     });
     
     $('.datepicker1').datepicker({
       format: 'yyyy-mm-dd'
     });
  
</script>

</body>
</html>

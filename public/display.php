<?php 
require_once("includes/validate_credentials.php");
if (isset($_POST['comment_field']) && !empty($_POST['comment_field'])) {
      $comment = htmlentities($database->escape_value($_POST['comment_field']));
	  $sql = "INSERT INTO comments(owner,comment,owner_type,time,user) VALUES({$_POST['institution']},'$comment','inst',NOW(),{$_SESSION["id"]})";
	  $database->query($sql);
	  $inst= rawurlencode(encrypt_decrypt('encrypt', $_POST['institution']));
	  header("location:display?inst=$inst");
	  exit();
 }
if (isset($_GET['inst'])) {
	$inst_data = explode('%', $_GET['inst']);
	$found = count($inst_data);
	if ($found> 1) {
		$inst = encrypt_decrypt('decrypt', $inst_data[0]);
	} else {
		$inst = encrypt_decrypt('decrypt', $_GET['inst']);
	}
	
}else{
	header("location:404");
}
if (!isset($inst) || !is_numeric($inst)) {
	header("location:404");
}else{
	$query =$database->query("SELECT * FROM institutions WHERE id ='$inst' AND status='1' LIMIT 1 ");
	$found_institution  = $database->num_rows($query);
	if ($found_institution ==0) {
		header("location:404");
	}else{
		$institution  = $database->fetch_array($query);
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
	<style>
	#nav-tab{
		border-bottom: 1px solid green;
	}
	 .nav-item.nav-link.active{
	   	border-color: green;
	   	color: green;
	   	border-bottom: none;
	   }
	    .info-list{
	    	border-left: none;
	    	border-right: none;
	    	width: 95%;
	    	margin-left: 2%;
	    	font-size: 10pt;
	    	padding: 5px;
	    }
	    .first-child{
	    	border-top: none;
	    }
	    .last-child{
	    		border-bottom: none;
	    }
		.labels{
			display: inline-block ;
			width: 25%;
		    font-family:inherit;
		    color: #848482;
			
		}
		.datas{
			display: inline-block;
			width:75%;
			font-family: inherit;
			color: black;
			font-weight: 400;
		}
	.edit-link{
		margin-bottom: 30px;
		
	}
	.edit_btn {
            font-size: 12px;
            font-weight: bold;
            background: #435d7d;
            border: none;
            margin-top: 20px;
            margin-left: 2%;
            min-width: 50px;
        }
     .edit_btn:hover, .edit_btn:focus {
            background: #41cba9;
            outline: none !important;
      }	
	#comment_button{
		 /*max-width: 50px;*/
		 max-height: 38px;
		 margin-top: 20px;
		 padding-top:2px;
		 margin-left: 20px;
		 
	}
	#comments{
		 margin-left: 2%;
	}
	#comment_field{
		resize: none;
		min-width: 300px;
	}
	#submit_comment{
		display: block;
		font-size: 12px;
	}
	.comment{
		margin-left:2%;
		margin-right: 2%; 
		margin-bottom: 10px;
	}
   .comment	.comment_data{
   	font-size: 11pt;
   	font-family: inherit;
   }
   .comment .comment_u_t{
   	    color: #848482;
   	    font-size: 9pt;
   	    /*margin-left: 20px;*/
   }
	</style>
</head>
<body>
<!-- Left Panel -->
  <?php require_once 'includes/left_nav.php'; ?>
<!-- Right Panel -->
<div id="right-panel" class="right-panel">
<!-- Header-->
 <?php require_once 'includes/top_nav.php'; ?>
<!-- Header-->
        <div class="content mt-10">
        <!-- displaypersoning institution basic info -->
        <div class="col-lg-12">
                            <div style="display: inherit; padding-bottom: 30px;" class="card">
                                <div class="card-header">
                                	
                                  <h4>
                                  	<?php 
			                             if ($institution['category_id']== 4) {
											 $name = $institution['name'];
										 }else if ($institution['category_id']== 2) {
										 	$foreign  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$institution['country']}' LIMIT 1 "));
											 $name = $foreign['name']." Embassy";
										 }else if ($institution['category_id']== 3) {
										 	$location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$institution['country_loc']}' LIMIT 1 "));
											 $name = "Rwandan Embassy {$location['name']}";
										 }else{
										 	$name = $institution['name']." Kigali-Rwanda";
										 }
                                  	echo $name;?> 
                                  </h4>
                                </div>
                                <div class="card-body">
                                    <div class="default-tab">
                                    	<?php 
                                    	
                                    	  $tabs = array("info", "additional","cars", "assets", "diplomats", "local_staff", "dependent", "spouse");
										  if (isset($inst_data[1]) && (in_array($inst_data[1], $tabs))){
										
											  $va = $inst_data[1];
											  $$va =  "active";
										  }else{
										  	$info = "active";
										  }
                                    	?>
                                        <nav>
                                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                <a class="nav-item nav-link <?php if(isset($info))echo $info; ?>" href="display?inst=<?php echo rawurlencode($inst_data[0]).rawurlencode("%info");?>">Basic Info.</a>
                                                <?php if($institution['category_id'] ==4){?>
                                                <a class="nav-item nav-link <?php if(isset($additional))echo $additional; ?>"  href="display?inst=<?php echo  rawurlencode($inst_data[0]).rawurlencode("%additional"); ?>">Additional Info.</a>
                                                <?php } ?>
                                                <a class="nav-item nav-link <?php if(isset($cars))echo $cars; ?>"   href="display?inst=<?php echo rawurlencode($inst_data[0]).rawurlencode("%cars"); ?>">Assets</a>
                                                <a class="nav-item nav-link <?php if(isset($diplomats))echo $diplomats; ?>"   href="display?inst=<?php echo rawurlencode($inst_data[0]).rawurlencode("%diplomats"); ?>">Deplomats</a>
                                                <a class="nav-item nav-link <?php if(isset($local_staff))echo $local_staff; ?>"   href="display?inst=<?php echo rawurlencode($inst_data[0]).rawurlencode("%local_staff"); ?>">Local staff</a>
                                                <a class="nav-item nav-link <?php if(isset($dependent))echo $dependent; ?>"   href="display?inst=<?php echo rawurlencode($inst_data[0]).rawurlencode("%dependent"); ?>">Dependent</a>
												<a class="nav-item nav-link <?php if(isset($spouse))echo $spouse; ?>"   href="display?inst=<?php echo rawurlencode($inst_data[0]).rawurlencode("%spouse"); ?>">Spouse</a>
                                            </div>
                                        </nav>
                              </div>
                     </div>
					<!-- tab content here  -->
					<?php
					
					if (isset($info)){ /// for basic Info.
						echo '<ul class="list-group">';
						 echo "<li class='list-group-item info-list first-child'><div class='labels'>Name</div><div class='datas'>{$name}</div></li>";
						 if ($institution['category_id']== 2) {
							$foreign  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$institution['country']}' LIMIT 1 "));
							echo "<li class='list-group-item info-list'><div class='labels'>Country represented</div><div class='datas'>{$foreign['name']}</div></li>";				
						 }
						 $category  = $database->fetch_array($database->query("SELECT name FROM institution_categories WHERE id ='{$institution['category_id']}' LIMIT 1 "));
						 echo "<li class='list-group-item info-list'><div class='labels'>Category</div><div class='datas'>{$category['name']}</div></li>";  
						 $location  = $database->fetch_array($database->query("SELECT name FROM countries WHERE id ='{$institution['country_loc']}' LIMIT 1 "));
						 echo "<li class='list-group-item info-list'><div class='labels'>Country located</div><div class='datas'>{$location['name']}</div></li>";
						 echo "<li class='list-group-item info-list'><div class='labels'>Address</div><div class='datas'>{$institution['location']}</div></li>";
						 echo "<li class='list-group-item info-list'><div class='labels'>Tel</div><div class='datas'>{$institution['telephone']}</div></li>";
						 echo "<li class='list-group-item info-list'><div class='labels'>Email</div><div class='datas'>{$institution['email']}</div></li>";
						 if ($institution['category_id']== 4) {
							echo "<li class='list-group-item info-list'><div class='labels'>Line ministry</div><div class='datas'>{$institution['responsible_ministry']}</div></li>";	
							echo "<li class='list-group-item info-list'><div class='labels'>Government Contribution</div><div class='datas'>{$institution['anual_contribution']}</div></li>";
							echo "<li class='list-group-item info-list'><div class='labels'>Deadline For Paymen</div><div class='datas'>{$institution['payment_date']}</div></li>";			
						 }
						 
						 echo "<li class='list-group-item info-list'><div class='labels'>Contact person</div><div class='datas'>{$institution['contact_person']}</div></li>";
						 echo "<li class='list-group-item info-list'><div class='labels'>Contact person's phone</div><div class='datas'>{$institution['contact_phone']}</div></li>";
						 echo "<li class='list-group-item info-list'><div class='labels'>Registered time</div><div class='datas'>{$institution['time']}</div></li>";
						 $user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$institution['user']}' LIMIT 1 "));  
						 echo "<li class='list-group-item info-list last-child'><div class='labels'>Registered by</div><div class='datas'>{$user['fname']} {$user['lname']}</div></li>";
						
						echo '</ul>';
						
						$href = "register-foreign-embassy?institution=".rawurlencode($inst_data[0]);
						if ($institution['category_id'] ==4) {
								$href = "register-ngo?institution=".rawurlencode($inst_data[0]);
						}else if ($institution['category_id'] =="3") {
							  $href = "register-rwandan-embassy?institution=".rawurlencode($inst_data[0]);
							  
						}else if ($institution['category_id'] =="1") {
							  $href = "#".rawurlencode($inst_data[0]);
							  
						}
					
					?>
					<a href="<?=$href?>" class='edit-link'><button class='btn w-150 border-radius-0 mb-10 btn-primary btn-lg edit_btn'>Edit</button></a>
					<hr style="margin: 5px 2%;" />
					<h4 class="pt-20" style="font-family: inherit;margin-left: 20px;margin-bottom: 20px;">Comments</h4>
					<?php
					   $comment_query =$database->query("SELECT * FROM comments WHERE owner ='$inst' AND status='1' AND owner_type='inst' ORDER BY time ASC "); 
					   while ($comments  = $database->fetch_array($comment_query)){
					    	echo "<div class='comment p-15' style='background-color: #fafafa'> ";
						       $comment = nl2br($comments['comment']);
							   
							   $user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$comments['user']}' LIMIT 1 "));  
							   echo "<p class='comment_u_t'><b>{$user['fname']} {$user['lname']}</b> <b style='float:right; font-size: 11px'>{$comments['time']}</b> <hr><p>";
							   echo "<p class='comment_data mb-0 fs-14'>$comment </p>";
						    echo "</div>";
					    }
						
					?>
					
					<button class="btn btn-primary w-150 border-radius-0" id="comment_button" type="button" onclick="myFunction()" title='Write a comment' ><i class="fa fa-comments-o fa-2x" aria-hidden="true"></i> Write a comment</button>					    
				    
				   <center>	

				   		 <form id="comments" style="display: none" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" >
						<textarea id="comment_field" name="comment_field" class="form-controler mt-20 mb-10 w-50p" required rows="5" placeholder="Your comment"></textarea>
						<input type="hidden" name="institution" value="<?php echo $inst; ?>" />
                       <input type="submit" value="SAVE" id="submit_comment" name="submit_comment"  class="btn btn-success w-150 btn-xs" />																									
                    </form>

				   </center>
				 <?php } ?>
				 
			<!-- For cars -->
			 <?php
				 if (isset($cars)){
				 	$href_cars_add = "add-cars-inst.php?inst=".rawurlencode($inst_data[0]);
				 	echo "<a href=\"$href_cars_add\" class='edit-link'><button style='margin-bottom:20px' class='btn btn-primary btn-lg edit_btn'>Add</button></a>";
				 $i=1;
				 $sql= "SELECT id,model,plate,year,chassis FROM cars WHERE owner= '$inst' AND owner_type='inst' AND status ='1' ";
				 $car_query = $database->query($sql);
				 echo '
				 <table style="width:98%;margin-left:1%;" class="table table-striped">
				  <tbody>
				 ';
				 while($cars_data  = $database->fetch_array($car_query)){
							echo '<tr>';
						    echo "<td scope='row' class='numbering' style=\"width: 10%;\">$i</td>";
							echo "<td style=\"width: 40%;\">{$cars_data['model']} {$cars_data['year']} </td>";
							echo "<td style=\"width: 40%;\">{$cars_data['plate']}</td>";
							$href = "cars-inst?cr=".rawurlencode(encrypt_decrypt('encrypt', $cars_data['id']));
							echo "<td style=\"width: 10%;\"><a style href='$href'>
							           <button type='button' class='btn btn-default btn-sm'>
								          <i class=\"ti-car\"></i> More
								       </button>
							       </a>
							</td>";
							echo '</tr>';
							$i++;
						}
               echo " </tbody>
				</table>";
				}
			?>
		 <!-- For diplomats -->
		 <?php
				 if (isset($diplomats)){
					$sql= "SELECT id,title,other_title,given_name,family_name,other_name,gender,email,passport FROM diplomats WHERE institution= '$inst'  AND status ='1' ";
					$car_query = $database->query($sql);
					echo '<ul class="list-group">';
					$i=0;
					while($diplomats_data  = $database->fetch_array($car_query)){
						$i++;
						//for title
						if ($diplomats_data['title'] =="Other"){
							$title=$diplomats_data['other_title']; 
						 } else {
							$title=$diplomats_data['title'];  
						 }
						 $href = "diplomats?dpl=".rawurlencode(encrypt_decrypt('encrypt', $diplomats_data['id']));
						 echo "<a href=\"$href\"><li class='list-group-item info-list first-child'><div class='datas'>$i. {$title} {$diplomats_data['given_name']} {$diplomats_data['family_name']} {$diplomats_data['other_name']} {$diplomats_data['email']} {$diplomats_data['passport']}</div></li></a>";
                         
					}
					echo '</ul>';

				 }
		?>
			
         <!-- End of tab content  -->                
                 </div>         
               </div>
   
       </div> <!-- .content -->
  
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
     
function myFunction() {
    var x = document.getElementById("comments");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}

</script>

</body>
</html>
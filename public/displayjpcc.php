<?php 
require_once("includes/validate_credentials.php");
if (isset($_POST['submit_comment']) && !empty($_POST['comment_field'])) {
      $comment = htmlentities($database->escape_value($_POST['comment_field']));
	  $sql = "INSERT INTO comments(comment,owner,owner_type,time,user) VALUES('$comment','{$_POST['jpc']}','Action',NOW(),{$_SESSION["id"]})";
	  $database->query($sql);
	  $database->query("INSERT INTO notification (action_id,action_type,action_name,deadline,user) VALUES('{$_POST['jpc']}','jpc-action','{$comment}','{$_POST['time']}','{$_SESSION['id']}')");
	  $jpc= rawurlencode(encrypt_decrypt('encrypt', $_POST['jpc']));
	  header("location:displayjpcc?jpc=$jpc".'%info');
	  exit();
 }
 if (isset($_POST['submit_meeting']) && !empty($_POST['t_meet'])) {
      $t_meet = htmlentities($database->escape_value($_POST['t_meet']));
      $t_next_meet = htmlentities($database->escape_value($_POST['t_next_meet']));
      $place = htmlentities($database->escape_value($_POST['place']));
	  $sql = "INSERT INTO meetings(t_meeting,t_next_meeting,meeting_place,link,user,type) VALUES('{$t_meet}','{$t_next_meet}','{$place}','{$_POST['jpc']}','{$_SESSION["id"]}','jpc')";
	  $database->query($sql);
	  $owner = $database->inset_id();
	  // $database->query("INSERT INTO notification (action_id,action_type,action_name,deadline,user) VALUES('{$owner}','jpc-decision','{$comment}','{$_POST['time']}','{$_SESSION['id']}')");
	  $jpc= rawurlencode(encrypt_decrypt('encrypt', $_POST['jpc']));
	  header("location:displayjpcc?jpc=$jpc".'%meetings');
	  exit();
 }
if (isset($_GET['jpc'])) {
	$jpc_data = explode('%', $_GET['jpc']);
	$found = count($jpc_data);
	if ($found> 1) {
		$jpc = encrypt_decrypt('decrypt', $jpc_data[0]);
	} else {
		$jpc = encrypt_decrypt('decrypt', $_GET['jpc']);
	}
	
}else{
	header("location:404");
}
if (!isset($jpc) || !is_numeric($jpc)) {
	header("location:404");
}else{
	$query =$database->query("SELECT j.*,c.name AS cname FROM jpc AS j, countries AS c WHERE c.id=j.country AND j.id ='$jpc' AND j.status='1' LIMIT 1 ");
	$found_jpc  = $database->num_rows($query);
	if ($found_jpc ==0) {
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
	    	padding: 2px;
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
        <!-- displaypersoning jpcc basic info -->
        <div class="col-lg-12">
                            <div style="display: inherit; padding-bottom: 30px;" class="card">
                                <div class="card-header">
                                	
                                  <p style="font-size: 18px;color: black;">
                                  	<?php 
			                             
										$name = $institution['name'];
										 
                                  	echo 'JPC/JPCC: '.$name;?> 
                                  </p>
                                </div>
                                <div class="card-body">
                                    <div class="default-tab">
                                    	<?php 
                                    	
                                    	  $tabs = array("info", "decisions", "actions", "meetings");
										  if (isset($jpc_data[1]) && (in_array($jpc_data[1], $tabs))){
											 $tab = $jpc_data[1];
											 // $va = $jpc_data[1];
											 $active =  "active";

										  }else{
										  	// $tab = "info";
										  }
                                    	?>
                                        <nav>
                                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                <a class="nav-item nav-link <?php if(isset($tab) && $tab=='info')echo $active; ?>" href="displayjpcc?jpc=<?php echo rawurlencode($jpc_data[0]).rawurlencode("%info");?>">Basic Info.</a>
                                                <a class="nav-item nav-link <?php if(isset($tab) && $tab=='meetings')echo $active; ?>"   href="displayjpcc?jpc=<?php echo rawurlencode($jpc_data[0]).rawurlencode("%meetings"); ?>">Meetings</a>
                                                <a class="nav-item nav-link <?php if(isset($tab) && $tab=='decisions')echo $active; ?>"  href="displayjpcc?jpc=<?php echo  rawurlencode($jpc_data[0]).rawurlencode("%decisions"); ?>">Decisions</a>
                                                
                                            </div>
                                        </nav>
                              </div>
                     </div>
					<!-- tab content here  -->
					<?php
					
					if (isset($tab) && $tab=='info'){ /// for basic Info.
						echo '<ul class="list-group">';
						 echo "<li class='list-group-item info-list first-child'><div class='labels'>Name</div><div class='datas'>{$name}</div></li>";
						 echo "<li class='list-group-item info-list first-child'><div class='labels'>Country</div><div class='datas'>{$institution['cname']}</div></li>";
							echo "<li class='list-group-item info-list'><div class='labels'>Line ministry</div><div class='datas'>{$institution['lineministry']}</div></li>";
							echo "<li class='list-group-item info-list'><div class='labels'>Area Of Coorporation</div><div class='datas'>{$institution['area']}</div></li>";
							echo "<li class='list-group-item info-list'><div class='labels'>Meeting Schedule</div><div class='datas'>{$institution['t_next_meet']}</div></li>";
							echo "<li class='list-group-item info-list'><div class='labels'>Place</div><div class='datas'>{$institution['place']}</div></li>";	
							echo "<li class='list-group-item info-list'><div class='labels'>Timeframe</div><div class='datas'>{$institution['t_next_meet']}</div></li>";
						 
						 echo "<li class='list-group-item info-list'><div class='labels'>Contact person</div><div class='datas'>{$institution['cont_name']}</div></li>";
						 echo "<li class='list-group-item info-list'><div class='labels'> phone number</div><div class='datas'>{$institution['cont_phone']}</div></li>";
						 echo "<li class='list-group-item info-list'><div class='labels'>Email</div><div class='datas'>{$institution['cont_email']}</div></li>";
						 echo "<li class='list-group-item info-list'><div class='labels'>Registered time</div><div class='datas'>{$institution['time']}</div></li>";
						 $attach = $database->fetch_array($database->query("SELECT attachments,name FROM attachments WHERE id='{$institution['mou_files']}' LIMIT 1"));
						 echo "<li class='list-group-item info-list'><div class='labels'>Attachments</div><div class='datas'><a href='attachments/{$attach['attachments']}' target='_blank'>{$attach['name']}</a></div></li>";
						 $user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$institution['user']}' LIMIT 1 "));  
						 echo "<li class='list-group-item info-list last-child'><div class='labels'>Registered by</div><div class='datas'>{$user['fname']} {$user['lname']}</div></li>";
						
						echo '</ul>';
						
						
							  $href = "register-jpc?jpc=".rawurlencode($jpc_data[0]);
						
						 // }
					
					?>
					<a href="<?=$href?>" class='edit-link'><button class='btn btn-primary border-radius-0 w-150 mb-10 btn-lg edit_btn'>Edit</button></a>
					<hr style="margin: 5px 2%;" />
					<h4 class="pl-20 mt-10" style="font-family: inherit;margin-bottom: 20px;">Comments</h4>
					<?php
					   $comment_query =$database->query("SELECT * FROM comments WHERE owner ='$jpc' AND status='1' AND owner_type='jpc' ORDER BY time ASC "); 
					   while ($comments  = $database->fetch_array($comment_query)){
					    	echo "<div class='comment p-15' style='background-color: #fafafa'>";
						       $comment = nl2br($comments['comment']);
							   
							   $user  = $database->fetch_array($database->query("SELECT fname,lname FROM user WHERE id ='{$comments['user']}' LIMIT 1 "));  
							   echo "<p class='comment_u_t'><b>{$user['fname']} {$user['lname']}</b> <b style='float:right'>{$comments['time']}</b> <hr><p>";
							   echo "<span class='comment_data fs-14 mb-0'>$comment </span>";
						    echo "</div>";
					    }
						
					?>
					
					<button class="btn btn-primary mb-20 w-150" id="comment_button" type="button" onclick="myFunction()" title='Write a comment' ><i class="fa fa-comments-o fa-2x" aria-hidden="true"></i> Write a comment</button>					    
				    
				    <!-- <center> -->
				    	<form id="comments" style="display: none" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" >
							<textarea id="comment_field" class="form-control w-50p" rows="5" name="comment_field" required placeholder="Your comment"></textarea>
							<input type="hidden" name="jpc" value="<?php echo $jpc; ?>" />
	                       <input type="submit" value="SAVE" id="submit_comment" name="submit_comment"  class="btn mt-10 w-150 btn-primary btn-xs" />																									
	                    </form>
				    <!-- </center> -->
				 <?php } ?>
				 
			<!-- For cars -->
			 <?php
				 if (isset($tab) && $tab=='decisions'){
				 	// $href_cars_add = "displayjpcc.php?jpc=".rawurlencode($jpc_data[0].'%'.$tab.'%ad');
				 	// echo "<a href=\"$href_cars_add\" class='edit-link'><button style='margin-bottom:20px' class='btn btn-primary btn-lg edit_btn'>Add</button></a>";
				 	?>
				 	<p class="pl-20 pt-10"><a href="#" class="btn btn-info btn-lg" onclick="myFunction()">
				          <span class="glyphicon glyphicon-plus"></span> Add Decision
				        </a>
				      </p> 
					<!-- <button class="btn btn-info btn-lg" id="comment_button" type="button" onclick="myFunction()" title='Add A Decision' ><i class="glyphicon glyphicon-plus" aria-hidden="true"></i> ADD </button><br> -->				    
				    <br>
				    <div class="row">
				    	<form id="comments" style="display: none" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" >
						<input type="hidden" name="jpc" value="<?php echo $jpc; ?>" />
						<div class="form-group col-sm-12">
							<label class="col-sm-10 pl-0">Decisions</label>
							<textarea class="form-control col-sm-5" id="comment_field" name="comment_field" required placeholder="Enter Decisions" rows="5"></textarea>
						</div>
						<div class="form-group col-sm-12">
		                  	<label for="name" class="col-sm-10 pl-0">Working Period</label>
		                  	<input class="form-control col-sm-5" type="date" name="time" placeholder="">
		                 </div>
						<div class="form-group col-sm-12">
	                       <input type="submit" value="SAVE" id="submit_comment" name="submit_decision"  class="btn btn-primary w-150 mt-10 btn-xs" />																									
						</div>
                    </form><br>
				    </div>
				 	<?php
				 $i=1;
				 $sql= "SELECT d.id,d.decision,d.dtime,j.name FROM decisions AS d, jpc AS j WHERE d.jpc_meet= '$jpc' AND j.status ='1' AND J.id=d.jpc_meet ";
				 $car_query = $database->query($sql);
				 if ($database->num_rows($car_query)>0) {
					 echo '<table style="width:98%;margin-left:1%;" class="table table-striped">';

					 echo '<thead class="thead-dark">
						    <tr>
						      <th scope="col" style="width: 10%;">#</th>
						      <th scope="col" style="width: 40%;">Name</th>
						      <th scope="col" style="width: 40%;">Performed on</th>
						      <th scope="col" style="width: 40%;">More</th>
						    </tr>
						  </thead>';
					 echo ' <tbody> ';
				 }
				 while($jpc_data  = $database->fetch_array($car_query)){
							echo '<tr>';
						    echo "<td scope='row' class='numbering' style=\"width: 10%;\">$i</td>";
							echo "<td style=\"width: 40%;\">{$jpc_data['name']} {$jpc_data['dtime']} </td>";
							echo "<td style=\"width: 40%;\">{$jpc_data['decision']}</td>";
							$href = "jpc-d-actions?decs=".rawurlencode(encrypt_decrypt('encrypt', $jpc_data['id']));
							echo "<td style=\"width: 10%;\"><a style href='$href'>
							           <button type='button' class='btn btn-default btn-sm'>
								          <i class=\"ti-car\"></i> Actions
								       </button>
							       </a>
							</td>";
							echo '</tr>';
							$i++;
						}
               echo " </tbody>
				</table>";
				}elseif (isset($tab) && $tab=='meetings') { ?>
					<p class="pl-20 pt-10">
						<a href="#" class="btn btn-info btn-lg" onclick="myFunction()">
				          <span class="glyphicon glyphicon-plus"></span> Add Meetings
				        </a>
				      </p> 
				      <form id="comments" style="display: none " class="mt-20" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" >
						<input type="hidden" name="jpc" value="<?php echo $jpc; ?>" />
						<div class="form-group col-sm-12 pl-0">
							<label class="col-sm-10 pl-0 pb-3">Time Of Meeting</label>
							<input class="form-control col-sm-5" type="date" name="t_meet" placeholder="">
						</div>
						<div class="form-group col-sm-12 pl-0">
							<label class="col-sm-10 pl-0 pb-3">Next meeting Schedule</label>
							<input class="form-control col-sm-5" type="date" name="t_next_meet" placeholder="">
						</div>
						<div class="form-group col-sm-12 pl-0">
		                  	<label for="name" class="col-sm-10 pl-0 pb-3">Working Period</label>
		                  	<input class="form-control col-sm-5" type="text" name="place" placeholder="Place Of Meeting">
		                 </div>
						<div class="form-group col-sm-12 pl-0">
	                       <input type="submit" value="SAVE" id="submit_comment" name="submit_meeting"  class="btn btn-primary w-150 mt-10 btn-xs" />																									
						</div>
                    </form><br>
				<?php 
					$i=1;
					 $sql= "SELECT * FROM meetings WHERE link= '$jpc' AND type='jpc' AND status ='1' ";
					 $car_query = $database->query($sql);
					 echo '<table style="width:98%;margin-left:1%;" class="table table-striped">';
					 if ($database->num_rows($car_query)>0) {
						 echo '<thead class="thead-dark">
							    <tr>
							      <th scope="col" style="width: 10%;">#</th>
							      <th scope="col" style="width: 30%;">Meeting Date</th>
							      <th scope="col" style="width: 30%;">Next Meeting Date</th>
							      <th scope="col" style="width: 30%;">Meeting Place</th>
							      <th scope="col" style="width: 10%;">Decisions</th>
							    </tr>
							  </thead>';
					 }
					 echo ' <tbody> ';
					 while($jpc_data  = $database->fetch_array($car_query)){
								echo '<tr>';
							    echo "<td scope='row' class='numbering' style=\"width: 10%;\">$i</td>";
								echo "<td style=\"width: 30%;\">{$jpc_data['t_meeting']} </td>";
								echo "<td style=\"width: 30%;\">{$jpc_data['t_next_meeting']}</td>";
								$href = "jpc-d-actions?decs=".rawurlencode(encrypt_decrypt('encrypt', $jpc_data['id']));
								echo "<td style=\"width: 30%;\">{$jpc_data['meeting_place']}</td>";
								$href = "jpc-d-actions?decs=".rawurlencode(encrypt_decrypt('encrypt', $jpc_data['id'])).'%25decisions';
								echo "<td style=\"width: 10%;\"><a style href='$href'>
								           <button type='button' class='btn btn-default btn-sm'>
									          <i class=\"ti-car\"></i> Decisions
									       </button>
								       </a>
								</td>";
								echo '</tr>';
								$i++;
							}
	               echo " </tbody>
					</table>";
				} ?>
			
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
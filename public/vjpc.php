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
        <div class="tab-container">
            <div class="tab-content">
            	
            	<?php 
            	    $toggle ="";
					$main_title="";
					$search ="";
					$search_togle ="";
					if (isset($_GET['qr']) && !empty($_GET['qr'])) {
					   $search_keyword = strtolower(htmlentities($database->escape_value($_GET['qr'])));
					   $location1  = $database->fetch_array($database->query("SELECT id FROM countries WHERE LOWER(name) LIKE '%$search_keyword%' LIMIT 1 "));
					   if (!empty($location)) {
						   $search =" AND (LOWER(c.country) LIKE '%$search_keyword%' ";
					   	
							$search .=" OR j.country ='{$location['id']}' ";
					   }else{
					   	$search .= " AND j.name LIKE '%$search_keyword%' ";
					   }
					   $search_togle = "&qr=".rawurlencode($_GET['qr']);
					}
					
					$sql= "SELECT j.id,j.name,j.country AS ccc,c.nicename AS country FROM jpc AS j JOIN countries AS c ON j.country=c.id ";
					$sql .=$search;
					$toggle.=$search_togle;
                   $total_found  = $database->num_rows($database->query($sql));
            	?>
            	<h4 style="font-family: georgia;text-align: center;margin-bottom: 20px;font-size: 2.1rem;"><?php echo $main_title; ?></h4>
            	<div class="row">
                        <div class="col-sm-3">

                        </div>
                        <div class="col-sm-9 mt-30">
                          <form  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                            <div class="search-box">
                                <div class="input-group" id="igroup">
                                    <span class="input-group-addon"><i class="material-icons">&#xE8B6;</i></span>
                                    <input id="qr" type="text" name="qr" autocomplete="off" autofocus class="form-control" placeholder="Search&hellip;">
                                </div>
                            </div>
                          </form>
                        </div>
                    </div>
                    <span style="font-weight: bold;margin-left: 1%">Total: <span style="color:#007bff;"><?php echo $total_found; ?></span></span>
				<table class="table mt-10 table-striped">
				  <thead class="thead-dark">
				    <tr>
				      <th scope="col" style="width: 10%;">#</th>
				      <th scope="col" style="width: 40%;">Name</th>
				      <th scope="col" style="width: 40%;">Country</th>
				      <th scope="col" style="width: 10%;">More info</th>
				    </tr>
				  </thead>
				  <tbody>
				  	
				  	<?php 
                    $page = !empty($_GET['page']) ? (int)$_GET['page']:1;
					$per_page=25;
					$pagination = new pagination($page,$per_page,$total_found);
					$sql .=" LIMIT {$per_page} OFFSET {$pagination->offset()} ";
					$query =$database->query($sql);
					
					$i=1;
				    while($jpcc  = $database->fetch_array($query)){
							echo '<tr>';
						    echo "<td scope='row' class='numbering'>$i</td>";
							
							 
							echo "<td>{$jpcc['name']}</td>";
							echo "<td>{$jpcc['country']}</td>";
							$href = "displayjpcc?jpc=".rawurlencode(encrypt_decrypt('encrypt', $jpcc['id']));
							echo "<td><a style href='$href'>
							           <button type='button' class='btn btn-default btn-sm'>
								          <i class=\"ti-list\"></i> More
								       </button>
							       </a>
							</td>";
							echo '</tr>';
							$i++;
						}
					
					     
				  	?>

				  </tbody>
				</table>
				
				<!-- Pagination here -->
           
                    <nav style="margin-left: 20px;margin-right: 20px;font-size: 12px;" aria-label="Page navigation example">
						<ul  class="pagination">
						    <?php
					
								if($pagination->total_pages() > 1) {
											if($pagination->has_previous_page()) { 
									    	echo "<li class='page-item'><a class=\"page-link\" href=\"institutions?page=";
									  echo $pagination->previous_page().$toggle;
									  echo "\"> Previous</a> </li>"; 
									}
									
										for($i=1; $i <= $pagination->total_pages(); $i++) {
											if($i == $page) {
												echo "<li style='text-decoration:none;' class=\"page-item active\"> <span class=\"page-link\">{$i}</span> </li>";
											} else {
												echo "<li class='page-item'> <a class=\"page-link\" href=\"institutions?page={$i}$toggle\">{$i}</a> </li>"; 
											}
										}
									
										if($pagination->has_next_page()) { 
											echo "<li class='page-item'> <a class=\"page-link\" href=\"institutions?page=";
											echo $pagination->next_page().$toggle;
											echo "\">Next</a></li> "; 
									}
								 }
							 
						    ?>
					    </ul>
					    </nav>
					</div>
                           <!-- Pagination ending-->

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
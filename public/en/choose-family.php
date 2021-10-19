<?php
require_once("includes/validate_credentials.php");
?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="">
<!--<![endif]-->

<head>
	<?php require_once("includes/head.php"); ?>

	<link rel="stylesheet" href="assets/css/font-roboto-varela.css">
	<link rel="stylesheet" href="assets/css/material-icon.css">
	<link rel="stylesheet" href="css/choose-family.css">
</head>

<body>
	<style type="text/css">
	</style>
	<?php require_once 'includes/left_nav.php'; ?>

	<div id="right-panel" class="right-panel">
		<!-- Header-->
		<?php require_once 'includes/top_nav.php'; ?>
		<div class="container">
			<div class="tab-container mt-10 pl-20 pr-20 pt-0">
				<div class="tab-content">

					<div class="row">
						<div class="col-sm-12">
							<?php
							$_SESSION['locals'] = '0';
							$toggle = "";
							$main_title = "";
							$search = "";
							$search_togle = "";
							if (isset($_GET['search_diplomat']) && !empty($_GET['search_diplomat'])) {
								$search_keyword = strtolower(htmlentities($database->escape_value($_GET['search_diplomat'])));
								$search = " AND ((LOWER(d.given_name) LIKE '%$search_keyword%')  OR (LOWER(d.family_name) LIKE '%$search_keyword%') OR (LOWER(d.other_name) LIKE '%$search_keyword%') OR (LOWER(d.document_id) LIKE '%$search_keyword%')) ";
								$search_togle = "&search_diplomat=" . rawurlencode($_GET['search_diplomat']);
							}
							$sql = "SELECT d.id,d.given_name,d.family_name,d.other_name,document_id,d.birth_nationality,d.dob
							FROM diplomats AS d WHERE d.status='1' ";
							$main_title = "<h5 class='text-left fs-18 fw-500'>
30 new families in the system</h5>";
							$sql .= $search;
							$sql .= " ORDER BY d.time DESC ";
							$toggle .= $search_togle;
							$total_found  = $database->num_rows($database->query($sql));
							?>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<h3 style="font-family: georgia;text-align: center;margin-bottom: 20px;"><?php echo $main_title; ?></h3>
						</div>
						<div class="col-sm-3 pt-10">
							<span class="pt-15" style="margin-left: 1%;"><small class="fs-14">Total : <span class="badge bg-primary"><?php echo $total_found; ?></span></small></span>
						</div>
						<div class="col-sm-9 mb-10">
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
								<div class="search-box">
									<div class="input-group" id="igroup">
										<!--<span class="input-group-addon"><i class="material-icons">&#xE8B6;</i></span>-->
										<input id="search_diplomat" type="text" name="search_diplomat" autocomplete="off" autofocus onkeyup="search()" class="form-control" placeholder="Search&hellip;">
									</div>
								</div>
							</form>

						</div>
					</div>

					<table class="table table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col" style="width: 10%;">#</th>
								<th scope="col" style="width: 40%;">Names</th>
								<th scope="col" style="width: 40%;">ID/Passport</th>
								<th scope="col" style="width: 10%;">+</th>
							</tr>
						</thead>
						<tbody>

							<?php
							$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
							$per_page = 30;
							$pagination = new pagination($page, $per_page, $total_found);
							$sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()} ";
							$query = $database->query($sql);

							$i = 1;
							while ($diplomat  = $database->fetch_array($query)) {
								echo '<tr>';
								echo "<td scope='row' class='numbering'>$i</td>";
								echo "<td>{$diplomat['given_name']} {$diplomat['family_name']} {$diplomat['other_name']}; {$diplomat['dob']}</td>";
								echo "<td>{$diplomat['document_id']}</td>";
								$href = "add-member?dpl=" . rawurlencode(encrypt_decrypt('encrypt', $diplomat['id']));
								echo "<td><a style href='$href'>
							           <button type='button' class='btn btn-default btn-sm'>
								          <i class=\"ti-plus\"></i> Add
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
						<ul class="pagination">
							<?php

							if ($pagination->total_pages() > 1) {
								if ($pagination->has_previous_page()) {
									echo "<li class='page-item'><a class=\"page-link\" href=\"choose-family?page=";
									echo $pagination->previous_page() . $toggle;
									echo "\"> Previous</a> </li>";
								}

								for ($i = 1; $i <= $pagination->total_pages(); $i++) {
									if ($i == $page) {
										echo "<li style='text-decoration:none;' class=\"page-item active\"> <span class=\"page-link\">{$i}</span> </li>";
									} else {
										echo "<li class='page-item'> <a class=\"page-link\" href=\"choose-family?page={$i}$toggle\">{$i}</a> </li>";
									}
								}

								if ($pagination->has_next_page()) {
									echo "<li class='page-item'> <a class=\"page-link\" href=\"choose-family?page=";
									echo $pagination->next_page() . $toggle;
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
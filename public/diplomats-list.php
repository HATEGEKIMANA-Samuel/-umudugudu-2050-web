<?php
//$stime = microtime(true);
require_once("includes/validate_credentials.php");
require_once("model/family.php");
$text = "";
$level = session::get("level");
if (session::exists("CL")) {
	//$location = input::enc_dec("d", session::get("userLocation"));
	$location = session::get("CL")["numeric"] . "#";
	$text = "<span class='text-primary'>/" . session::get("CL")["text"] . '</span>';
} else {
	if ($level != 7 && $level != 1) $location = input::enc_dec("d", session::get("userLocation"));
	$location = "#";
}
$_SESSION['locals'] = '0';
$toggle = "";
$main_title = "";
$search = "";
$search_togle = "";
if (isset($_GET['search_diplomat']) && !empty($_GET['search_diplomat'])) {
	$search_keyword = strtolower(htmlentities($database->escape_value($_GET['search_diplomat'])));
	$search = " AND d.key_words LIKE  '%$search_keyword%'";
	$search_togle = "&search_diplomat=" . rawurlencode($_GET['search_diplomat']);
}
$main_title = "<h5 class='text-left fs-18 fw-500'>Umuryango wose $text </h5>";
$sql .= $search;
$toggle .= $search_togle;
$total_found = family::getHeadOfFamily($database, $location, 0, $search);

?>
<!doctype html>
<html class="no-js" lang="">

<head>
	<?php require_once("includes/head.php"); ?>
	<link rel="stylesheet" href="assets/css/font-roboto-varela.css">
	<link rel="stylesheet" href="assets/css/material-icon.css">
	<link rel="stylesheet" href="css/diplomat-list.css">
</head>

<body>
	<?php require_once 'includes/left_nav.php'; ?>

	<div id="right-panel" class="right-panel">
		<!-- Header-->
		<?php require_once 'includes/top_nav.php'; ?>
		<div class="container">
			<div class="tab-container mt-10 pl-20 pr-20 pt-0">
				<div class="tab-content">

					<div class="row">
						<div class="col-sm-12">

						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<h3 style="font-family: georgia;text-align: center;margin-bottom: 20px;"><?php echo $main_title; ?></h3>
						</div>
						<div class="col-sm-4 pt-10 " id="result-stats">
							<span class="pt-15" style="margin-left: 1%;"><small class="fs-14">Igiteranyo: <span class="badge bg-primary text-primary"><?php echo $total_found; ?></span></small></span>
							<span id="loadtime"></span>
						</div>

						<div class="col-sm-8 mt-20">
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
								<div class="search-box">
									<div class="input-group" id="igroup">
										<input type="hidden" name="location" value="<?= $location ?>" />
										<input type="hidden" name="table" value="diplomats" />
										<input id="search_diplomat" type="text" name="search_people" autocomplete="off" autofocus onkeyup="autoSearch(this);" class="form-control" placeholder="shaka amazina/indangamuntu/pasiporo&hellip;">
									</div>
								</div>
							</form>

						</div>
					</div>

					<table class="table mt-10 table-striped">

						<thead class="thead-dark">
							<tr>
								<th scope="col">#</th>
								<th scope="col">Amazina/amavuko</th>
								<?php if ($level == 2) : ?>
									<th scope="col" class="hide-on-mobile">Indangamuntu/Pasiporo</th>
								<?php endif ?>
								<th scope="col">Ibindi</th>
							</tr>
						</thead>
						<tbody class="search_content"></tbody>
						<tbody class="old_content">
							<?php
							$limit = 0;
							$currentPageNumber = 1;
							$perPage = 30;
							$i = 1;
							$link = '';
							if (isset($_GET['pn']) && is_numeric($_GET['pn']) && $_GET['pn'] > 0 && ($_GET['pn'] - 1) * $perPage < $total_found) {
								$currentPageNumber = $_GET['pn'];
								$limit = ($_GET['pn'] - 1) * $perPage;
								$i = $limit + 1;
							}
							$limit = "LIMIT $limit, $perPage";
							$people = family::getHeadOfFamily($database, $location, $limit, $search);
							ob_start();
							while ($diplomat = $database->fetch_array($people)) { ?>
								<tr>
									<td scope='row' class='numbering'><?= $i ?></td>
									<td><span class="text-capitalize"><?= $diplomat['givenName'] . ' ' . $diplomat['familyName'] . ' ' . $diplomat['otherName'] . ' '  ?></span></td>
									<?php if ($level == 2) : ?>
										<td class="hide-on-mobile"><?= $diplomat['documentNumber'] ?></td>
									<?php endif ?>
									<?php
									$href = "family?dpl=" . rawurlencode(encrypt_decrypt('encrypt', $diplomat['id']))
									?>
									<td><a style href='<?= $href ?>' class="navigate">
											<button type='button' class='btn btn-default btn-sm'>
												<i class="ti-user"></i> Byinshi
											</button>
										</a>
									</td>
								</tr>
							<?php
								$i++;
							}
							ob_end_flush();
							?>

						</tbody>

					</table>

					<!-- Pagination here -->
					<?php
					$currentPageName = "diplomats-list";
					pagination::template(
						$currentPageName,
						$currentPageNumber,
						$perPage,
						$total_found
					);
					?>
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
	<script src="js/search.js"></script>
	<script>
		$('.datepicker').datepicker({
			format: 'yyyy-mm-dd'
		});
	</script>
	<script type="text/javascript">
		// var loadtime = `print_r(//output::checkTime($stime))`;
		// var elem = document.getElementById("loadtime");
		// $(elem).text(loadtime);
	</script>
</body>


</html>
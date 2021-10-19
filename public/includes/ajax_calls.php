<?php
require_once '../autoload.php';
require_once '../model/user.php';
require_once '../model/family.php';

//get district by province 
if (isset($_POST["current_province"])) {
  if (is_numeric($_POST["current_province"])) {
    $province = $_POST["current_province"];
  } else {
    $province = 0;
  }
  echo '
   <option  value="">Select</option>
   ';
  $sql = "SELECT id,district FROM districts WHERE province_id = $province";
  $query = $database->query($sql);
  while ($row = $database->fetch_array($query)) {
    echo "<option value=\"{$row['id']}\">{$row['district']}</option>";
  }
}

//get sectors by district
if (isset($_POST["current_sectors"])) {
  if (is_numeric($_POST["current_sectors"])) {
    $district = $_POST["current_sectors"];
  } else {
    $district = 0;
  }
  echo '
    <option  value="">Select</option>
    ';
  $sql = "SELECT id,sector FROM sectors WHERE district_id = $district";
  $query = $database->query($sql);
  while ($row = $database->fetch_array($query)) {
    echo "<option value=\"{$row['id']}\">{$row['sector']}</option>";
  }
}

//get cells by sectors
if (isset($_POST["current_cells"])) {
  if (is_numeric($_POST["current_cells"])) {
    $sector = $_POST["current_cells"];
  } else {
    $sector = 0;
  }
  echo '
    <option  value="">Select</option>
    ';
  $sql = "SELECT id,name FROM cell WHERE sector_id = $sector";
  $query = $database->query($sql);
  while ($row = $database->fetch_array($query)) {
    echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
  }
}

//get villages by cell
if (isset($_POST["current_villages"])) {
  if (is_numeric($_POST["current_villages"])) {
    $cell = $_POST["current_villages"];
  } else {
    $cell = 0;
  }
  echo '
    <option  value="">Select</option>
    ';
  $sql = "SELECT id,name FROM village WHERE cell_id = $cell";
  $query = $database->query($sql);
  while ($row = $database->fetch_array($query)) {
    echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
  }
}

// README: home.php

if (isset($_POST['configure_dashboard'])) {
  $numericLocation = input::enc_dec('d', session::get('userLocation'));
  $location = user::decodeLocation($database, $numericLocation);

  if (session::exists('CL')) {
    switch (session::get('level')) {
      case 2: // village
        $location = $location[4]['id'] . '-' . $location[4]['name'];
        break;
      case 3: // cell
        $location = $location[3]['id'] . '-' . $location[3]['name'];
        break;
      case 4: // sector
        $location = $location[2]['id'] . '-' . $location[2]['name'];
        break;
      case 5: // district
        $location = $location[1]['id'] . '-' . $location[1]['name'];
        break;
      case 6: // province
        $location = $location[0]['id'] . '-' . $location[0]['name'];
        break;
      default: // HQ & Admin
        $location = '0-Rwanda';
        break;
    }

    echo 'loadFromSession' . '.' . session::get('level') . '-' . $location . '-'
      . $numericLocation . '.' . session::get('CL')['numeric'] . '-'
      . session::get('CL')['text'];
  }
}

if (isset($_POST['get_provinces'])) {
  $sql = "SELECT id, province FROM provinces";
  $query = $database->query($sql);
  $provinces = '';
  while ($row = $database->fetch_array($query)) {
    $provinces .= $row['id'] . ',' . $row['province'] . ','
      . family::getAllPeople($database, $row['id'] . '#') . '-';
  }

  session::put("CL", array(
    'text' => 'Rwanda',
    'numeric' => '0'
  ));

  echo $provinces;
}

if (isset($_POST['get_districts'])) {
  $province_id = explode('.', $_POST['get_districts'])[0];
  $province_name = explode('.', $_POST['get_districts'])[1];
  $sql = "SELECT id, district FROM districts WHERE province_id = {$province_id}";
  $query = $database->query($sql);
  $districts = '';
  while ($row = $database->fetch_array($query)) {
    $districts .= $row['id'] . ',' . $row['district'] . ','
      . family::getAllPeople($database, $province_id . '#' . $row['id'] . '#') . '-';
  }

  // update $_SESSION['CL']
  session::put('CL', array(
    'text' => 'Rwanda / ' . $province_name,
    'numeric' => $province_id
  ));

  echo $districts . '_' . family::getHeadOfFamily($database, $province_id . '#')
    . ',' . family::getAllPeople($database, $province_id . '#')
    . '_' . session::get('CL')['text'];
}

if (isset($_POST['get_sectors'])) {
  $district_location = explode('.', $_POST['get_sectors'])[0];
  $district_id = explode('#', $district_location)[1];
  $district_name = explode('.', $_POST['get_sectors'])[1];
  $sql = "SELECT id, sector FROM sectors WHERE district_id = {$district_id}";
  $query = $database->query($sql);
  $sectors = '';
  while ($row = $database->fetch_array($query)) {
    $sectors .= $row['id'] . ',' . $row['sector'] . ','
      . family::getAllPeople($database, $district_location . '#' . $row['id']) . '-';
  }

  // update session CL
  $CLtext = explode(' / ', session::get('CL')['text']);
  $CLnumeric = explode('#', session::get('CL')['numeric']);

  session::put('CL', array(
    'text' => $CLtext[0] . ' / ' . $CLtext[1] . ' / ' . $district_name,
    'numeric' => $CLnumeric[0] . '#' . $district_id
  ));

  echo $sectors . '_' . family::getHeadOfFamily($database, $district_location)
    . ',' . family::getAllPeople($database, $district_location)
    . '_' . session::get('CL')['text'];
}

if (isset($_POST['get_cells'])) {
  $sector_location = explode('.', $_POST['get_cells'])[0];
  $sector_id = explode('#', $sector_location)[2];
  $sector_name = explode('.', $_POST['get_cells'])[1];
  $sql = "SELECT id, name FROM cell WHERE sector_id = {$sector_id}";
  $query = $database->query($sql);
  $cells = '';
  while ($row = $database->fetch_array($query)) {
    $cells .= $row['id'] . ',' . $row['name'] . ','
      . family::getAllPeople($database, $sector_location . '#' . $row['id']) . '-';
  }

  // update $_SESSION['CL']
  $CLtext = explode(' / ', $_SESSION['CL']['text']);
  $CLnumeric = explode('#', $_SESSION['CL']['numeric']);

  session::put('CL', array(
    'text' => $CLtext[0] . ' / ' . $CLtext[1] . ' / ' . $CLtext[2] . ' / ' . $sector_name,
    'numeric' => $CLnumeric[0] . '#' . $CLnumeric[1] . '#' . $sector_id
  ));

  echo $cells . '_' . family::getHeadOfFamily($database, $sector_location)
    . ',' . family::getAllPeople($database, $sector_location)
    . '_' . session::get('CL')['text'];
}

if (isset($_POST['get_villages'])) {
  $cell_location = explode('.', $_POST['get_villages'])[0];
  $cell_id = explode('#', $cell_location)[3];
  $cell_name = explode('.', $_POST['get_villages'])[1];
  $sql = "SELECT id, name FROM village WHERE cell_id = {$cell_id}";
  $query = $database->query($sql);
  $villages = '';
  while ($row = $database->fetch_array($query)) {
    $villages .= $row['id'] . ',' . $row['name'] . ',' . family::getAllPeople($database, $cell_location . '#' . $row['id']) . '-';
  }

  // update $_SESSION['CL']
  $CLtext = explode(' / ', session::get('CL')['text']);
  $CLnumeric = explode('#', session::get('CL')['numeric']);

  session::put('CL', array(
    'text' => $CLtext[0] . ' / ' . $CLtext[1] . ' / ' . $CLtext[2] . ' / ' . $CLtext[3] . ' / ' . $cell_name,
    'numeric' => $CLnumeric[0] . '#' . $CLnumeric[1] . '#' . $CLnumeric[2] . '#' . $cell_id
  ));

  echo $villages . '_' . family::getHeadOfFamily($database, $cell_location)
    . ',' . family::getAllPeople($database, $cell_location) .
    '_' . session::get('CL')['text'];
}

if (isset($_POST['get_village_stats'])) {
  $village = $_POST['get_village_stats'];

  echo family::getHeadOfFamily($database, $village) . ',' . family::getAllPeople($database, $village)
    . '_' . session::get('CL')['text'];
}

if (input::required(array("get_encLyption"))) {
  echo input::enc_dec("e", input::sanitize("get_encLyption"));
}

if (isset($_POST['get_cl_session'])) {
  $value = $_POST['get_cl_session'];
  echo print_r(session::get('CL'));
}
////For issue reporting 
//get sectors by district
if (isset($_POST["issue_to_display"])) {
  if (is_numeric($_POST["issue_to_display"])) {
    $issue = $_POST["issue_to_display"];
  } else {
    $issue = 0;
  }
  echo '
    <option  value="0">Select</option>
    ';
  $sql = "SELECT icyabaye_id,icyabaye_name FROM icyabaye WHERE issue_id = $issue AND status=1";
  $query = $database->query($sql);
  while ($row = $database->fetch_array($query)) {
    echo "<option value=\"{$row['icyabaye_id']}\">{$row['icyabaye_name']}</option>";
  }
}

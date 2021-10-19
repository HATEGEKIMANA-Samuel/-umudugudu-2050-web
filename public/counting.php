<?php 
require_once("includes/validate_credentials.php");
$counting=[
'AllDiplomat'=>0,
'ambassadors'=>0,
'Fdiplomats'=>0,
'RwDiplomats'=>0,
'Lstaff'=>0,
'AllInsti'=>0,
'RWEmb'=>0,
'FEmb'=>0,
'IOrg'=>0,
'AllFDS'=>0,
'AllRWDS'=>0,
'AllEvents'=>0,
'AllJJ'=>0,
'visitors'=>0
];

$sql="
SELECT total,action from(
  SELECT count(d.id) as total,'AllDiplomat' as action FROM diplomats AS d LEFT JOIN institutions AS i ON d.institution = i.id WHERE  d.status='1'
  UNION
  SELECT count(d.id) as total,'ambassadors' as action FROM diplomats AS d  JOIN institutions AS i ON d.institution = i.id WHERE d.title='Ambassador' AND d.type='0' AND d.status='1'
  UNION
  SELECT count(d.id) as total,'Fdiplomats' as action FROM diplomats AS d  JOIN institutions AS i ON d.institution = i.id  WHERE d.title !='Ambassador' AND d.type='0' AND d.status='1' 
  UNION
  SELECT count(d.id) as total,'RwDiplomats' as action FROM diplomats AS d  JOIN institutions AS i ON d.institution = i.id  WHERE  d.type='1' AND d.status='1' 
   UNION
  SELECT count(d.id) as total,'visitors' as action FROM diplomats AS d  LEFT JOIN institutions AS i ON d.institution = i.id WHERE d.title='Other' AND  d.status='1'
    UNION
 SELECT count(d.id) as total,'visitors' as action FROM diplomats AS d LEFT JOIN institutions AS i ON d.institution = i.id WHERE d.title='Visitor' AND d.type='0' AND  d.institution =0 AND d.status='1'
   UNION
  SELECT count(d.id) as total,'Lstaff' as action FROM diplomats AS d  LEFT JOIN institutions AS i ON d.institution = i.id WHERE d.title='Other' AND  d.status='1'
  UNION
  SELECT count(i.id) as total,'RWEmb' as action  FROM institutions AS i JOIN institution_categories AS c ON i.category_id = c.id  WHERE category_id=3  AND i.status='1'
    UNION
  SELECT count(i.id) as total,'FEmb' as action  FROM institutions AS i JOIN institution_categories AS c ON i.category_id = c.id  WHERE category_id=2  AND i.status='1'
     UNION
  SELECT count(i.id) as total,'IOrg' as action  FROM institutions AS i JOIN institution_categories AS c ON i.category_id = c.id  WHERE category_id=4  AND i.status='1'
     UNION
  SELECT count(i.id) as total,'AllInsti' as action  FROM institutions AS i JOIN institution_categories AS c ON i.category_id = c.id  WHERE i.status='1'
   UNION
  SELECT count(j.id) as total,'AllJJ' as action FROM jpc AS j JOIN countries AS c ON j.country=c.id
   UNION
  SELECT count(j.id) as total,'AllEvents' as action FROM jpc AS j JOIN countries AS c ON j.country=c.id
) as data
";
$query=$database->query($sql);
 while($row = $database->fetch_array($query)){
 $number=$row['total'];
  switch (trim($row['action'])) {
    case 'AllDiplomat':
      $counting['AllDiplomat']=$number;
      break;
      case 'AllInsti':
      $counting['AllInsti']=$number;
      break;
     case 'ambassadors':
   $counting['ambassadors']=$number;
      break;
       case 'Fdiplomats':
      $counting['Fdiplomats']=$number;
      break;
        case 'Lstaff':
      $counting['Lstaff']=$number;
      break;
       case 'RwDiplomats':
       $counting['RwDiplomats']=$number;
      break;
       case 'RWEmb':
      $counting['RWEmb']=$number;
      break;
       case 'FEmb':
     $counting['FEmb']=$number;
      break;
       case 'IOrg':
       $counting['IOrg']=$number;
      break;
       case 'AllFDS':
       $counting['AllFDS']=$number;
      break;
       case 'AllRWDS':
      $counting['AllRWDS']=$number;
      break;
       case 'AllEvents':
      $counting['AllEvents']=$number;
      break;
      case 'AllJJ':
     $counting['AllJJ']=$number;
      break;
       case 'visitors':
      $counting['visitors']=$number;
      break;
    default:
      # code...
      break;
  }
}
echo json_encode($counting);

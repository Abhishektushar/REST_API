<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Registration.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate user object
  $registration = new Registration($db);

  // all user read query
  $result = $registration->read();

  // Get row count
  $num = $result->rowCount(); 
 
  // Check if any user registered
  if($num > 0) {
    // users array

    $register_arr['data'] = array();
    //$register_arr = array();
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $register_item = array(
        'id' => $id,
        'FirstName' => $FirstName,
        'LastName' => $LastName,
        'UserName' => $UserName,
        'Email' => $Email
      );

      // Push to "data"
      array_push($register_arr['data'], $register_item);
    }

    // Turn to JSON & output
    echo json_encode($register_arr);

  } else {
    // No Posts
    echo json_encode(
      array('message' => 'No user Found')
    );
  }
?>
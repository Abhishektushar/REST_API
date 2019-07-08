<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Registration.php';

  // Instantiate DB & connect 
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $registration = new Registration($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  // Set ID to update
  $registration->id = $data->id;

  // Delete Account
  if(!empty($registration->id)&&ctype_digit($registration->id)
                              &&$registration->delete()) {
    echo json_encode(
      array('message' => 'Account Deleted')
    );
  } else {
    echo json_encode(
      array('message' => 'Account Not Deleted')
    );
  }
?>

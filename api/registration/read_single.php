<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header("Access-Control-Max-Age: 3600");
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  
  include_once '../../config/Database.php';
  include_once '../../models/Registration.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate registered object
  $registration = new Registration($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  // Set ID to update
  $registration->id = $data->id;
  
  // Get registered user
  if(!empty($registration->id)
    &&ctype_digit($registration->id)){

       $registration->read_single();
      // Create array
        $register_arr = array( 
          'id' => $registration->id,
          'FirstName' => $registration->FirstName,
          'LastName' => $registration->LastName,
          'UserName' => $registration->UserName,
          'Email' => $registration->Email);
      // Make JSON
          print_r(json_encode($register_arr));
      }else{
        echo json_encode(array("message" => "failed to read"));
  }


 
  ?>
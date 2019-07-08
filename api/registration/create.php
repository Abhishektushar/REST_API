<?php 
  //Required Headers
  header('Access-Control-Allow-Origin: *');
  header("Content-Type: application/json; charset=UTF-8");
  header('Access-Control-Allow-Methods: POST');
  header("Access-Control-Max-Age: 3600");
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  //files needed to connect to database
  include_once '../../config/Database.php';
  include_once '../../models/Registration.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate registration object
  $registration = new Registration($db); 

  // Get raw data registered
   $data = json_decode(file_get_contents("php://input"));

  //set property values
  $registration->FirstName = $data->FirstName;
  $registration->LastName = $data->LastName;
  $registration->UserName = $data->UserName;
  $registration->Email = $data->Email;
  $registration->Password = $data->Password;
 
  //checking if email already exists 
  $Email_exists = $registration->EmailExists($data->Email);
  //checking if userName alredy exists
  $Username_exists = $registration->UsernameExists($data->UserName);
  
  if($Username_exists){
    echo json_encode(
      array('message' => 'Username already taken!!')
    );
  }

 if($Email_exists){
      echo json_encode(
        array('message' => 'Email already exists!!')
      );
    }

  if(!$Email_exists){
    if(!$Username_exists){
      // Create Account of  the User
      if(!empty($registration->FirstName)&&ctype_alpha($registration->FirstName)&&
          !empty($registration->LastName)&&ctype_alpha($registration->LastName)&&
          !empty($registration->UserName)&& !ctype_space($registration->UserName)&&
          !empty($registration->Email)&&filter_var($registration->Email, FILTER_VALIDATE_EMAIL)&&
          !empty($registration->Password)&&
                                           $registration->create()) {
                    // set response code
                    http_response_code(200);

                    //display message : Account created
                    echo json_encode(
                      array('message' => 'Account Created')
                    );

                   } 

     else {
                // set response code
                http_response_code(400);

                //display message : Unable to create your Account
                echo json_encode(
                  array('message' => 'Unable to create your Account :: some feild(s) missing')
                );
           }
    }
  }
?>

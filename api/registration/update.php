<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: PUT');
  header("Access-Control-Max-Age: 3600");
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  // files needed to connect to database
  include_once '../../config/Database.php';
  include_once '../../models/Registration.php';

  // required encode web token
  include_once '../../config/core.php';
  include_once '../../config/libs/php-jwt-master/src/BeforeValidException.php';
  include_once '../../config/libs/php-jwt-master/src/ExpiredException.php';
  include_once '../../config/libs/php-jwt-master/src/SignatureInvalidException.php';
  include_once '../../config/libs/php-jwt-master/src/JWT.php';
  use \Firebase\JWT\JWT;

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate User object
  $registration = new Registration($db);

  // Get raw data
  $data = json_decode(file_get_contents("php://input"));

  // get jwt
  $jwt=isset($data->jwt) ? $data->jwt : "";

  // if jwt is not empty
if($jwt){
              // if decode succeed, show user details
      try {

        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // Set ID to update
        $registration->id = $decoded->data->id;
        $registration->FirstName = $data->FirstName;
        $registration->LastName = $data->LastName;
        $registration->UserName = $data->UserName;
        $registration->Password = $data->Password;

      // Update Account
      if(!empty($registration->id)&&ctype_digit($registration->id)&&
         !empty($registration->FirstName)&&ctype_alpha($registration->FirstName)&&
         !empty($registration->LastName)&&ctype_alpha($registration->LastName)&&
         !empty($registration->UserName)&& !ctype_space($registration->UserName) &&
         !empty($registration->Password)&& $registration->update()) {

          // we need to re-generate jwt because user details might be different
              $token = array(
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "data" => array(
                                "id" => $registration->id,
                                "FirstName" => $registration->FirstName,
                                "LastName" => $registration->LastName,
                                "UserName" => $registration->UserName,
                                "Password" => $registration->Password,
                                )
              );
              $jwt = JWT::encode($token, $key);

              // set response code
              http_response_code(200);

              // response in json format
                  echo json_encode(
                    array(
                        "message" => "User details are updated.",
                        "jwt" => $jwt
                    )
                );
        } else {
                echo json_encode(
                  array('message' => 'Error in Updating your Account')
                );
            }
      }  

    // if decode fails, it means jwt is invalid 
  catch (PDOexception $e){
 
      // set response code
      http_response_code(401);

      // show error message
      echo json_encode(array(
          "message" => "Access denied.",
          "error" => $e->getMessage()
      ));
    }
  
    }

    // show error message if jwt is empty
    else{
    
      // set response code
      http_response_code(401);

      // tell the user access denied
      echo json_encode(array("message" => "Access denied."));
    }
?>

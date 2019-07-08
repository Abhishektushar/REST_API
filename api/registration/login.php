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

//Check email exists

//get posted data
$data = json_decode(file_get_contents("php://input"));

//set account property values

if($data->Email){
  $registration->Email = $data->Email;
  $login=$registration->Email;
}elseif ($data->UserName) {
  $registration->UserName = $data->UserName;
  $login=$registration->UserName;
}
$Authenticate_user = $registration->Authentication($login);

// generate json web token
include_once '../../config/core.php';
include_once '../../config/libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../config/libs/php-jwt-master/src/ExpiredException.php';
include_once '../../config/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../config/libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
//check if email exists and if password is correct
if($Authenticate_user && password_verify($data->Password, $registration->Password)){
  $token = array(
    "iss" => $iss,  //The iss (issuer) claim identifies the principal that issued the JWT.
    "aud" => $aud,  //The aud (audience) claim identifies the recipients that the JWT is intended for.
    "iat" => $iat,  //The iat (issued at) claim identifies the time at which the JWT was issued.
    "nbf" => $nbf,  //The nbf (not before) claim identifies the time before which the JWT MUST NOT be accepted for processing.
    "data" => array(
            "id" => $registration->id,
            "FirstName" => $registration->FirstName,
            "LastName" => $registration->LastName,
            "UserName" => $registration->UserName,
            "Email" => $registration->Email,
    )

  );
  // set response code
http_response_code(200);

// generate jwt
$jwt = JWT::encode($token, $key);
echo json_encode(
        array(
            "message" => "Successful login.",
            "jwt" => $jwt
        )
    );
  
}//login failed 
else{
  // set response code
  http_response_code(401);

  // tell the user login failed
  echo json_encode(array("message" => "Login failed : Invalid Email/Password."));

}

?>
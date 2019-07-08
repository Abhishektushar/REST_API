<?php
  //'User' Objects
  class Registration{
    // DB  connection & table name
      private $conn;
      private $table = 'registration';

        // Object Properties
        public $id;
        public $FirstName;
        public $LastName; 
        public $UserName;
        public $Email;
        public $Password;

        // Constructor with DB
        public function __construct($db) {
          $this->conn = $db;
        }

    // Get all user data 
  public function read() {
      // Create query
      $query = 'SELECT id,FirstName,LastName, UserName, Email FROM ' . $this->table . ' ORDER BY id ASC';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

    // Get Single user data
  public function read_single(){
      // Create query
      $query = 'SELECT id,FirstName,LastName, UserName, Email FROM ' . $this->table . ' WHERE id = :id LIMIT 0,1';
      
      //Prepare statement
        $stmt = $this->conn->prepare($query);

       // Bind data
        $stmt->bindParam(':id', $this->id);

        // Execute query
        if($stmt->execute()) {
          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          // set properties
          $this->id = $row['id'];
          $this->FirstName = $row['FirstName'];
          $this->LastName = $row['LastName'];
          $this->UserName = $row['UserName'];
          $this->Email = $row['Email']; 
        }
  }

  // Create a New USER
  public function create() {
    // Insert query
    $query = 'INSERT INTO ' . $this->table . ' SET
                    FirstName = :FirstName,
                    LastName = :LastName, 
                    UserName = :UserName, 
                    Email = :Email,
                    UserType = "user",
                    Password = :Password';

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Clean data
    $this->FirstName = htmlspecialchars(strip_tags($this->FirstName));
    $this->LastName = htmlspecialchars(strip_tags($this->LastName));
    $this->UserName = htmlspecialchars(strip_tags($this->UserName));
    $this->Email = htmlspecialchars(strip_tags($this->Email));
    $this->Password = htmlspecialchars(strip_tags($this->Password));

    // Bind data
    $stmt->bindParam(':FirstName', $this->FirstName);
    $stmt->bindParam(':LastName', $this->LastName);
    $stmt->bindParam(':UserName', $this->UserName);
    $stmt->bindParam(':Email', $this->Email);

    
    // hash the password before saving to database
    $password_hash = password_hash($this->Password, PASSWORD_BCRYPT);
    $stmt->bindParam(':Password', $password_hash);


      // Execute query
      if($stmt->execute()) {
        return true;
      }

      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
  
}

    //UserNmae already exists method
    public function UsernameExists($UserName){
      
      //query to check if Username exists
      $query = "SELECT id, FirstName, LastName, Email, UserName, Password FROM " . $this->table . " WHERE UserName = ? LIMIT 0,1";
 
      //prepare query 
      $stmt = $this->conn->prepare($query);

      //clean data
      $this->UserName = htmlspecialchars(strip_tags($this->UserName));
    
    // Bind given Username value
      $stmt->bindParam(1, $this->UserName);
    
    //execute the query
    $stmt->execute();

     // get number of rows
     $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
     // if Username exists, assign values to object properties for easy access and use for php sessions
     if($row>0){
      if ($UserName==$row['UserName'])
      {
         return true;
        }
      }
      // return false if Username does not exist in the database
      return false;
    }

    //emailexists() method will be here
    public function EmailExists($Email){

      //query to check if email exists
      $query = "SELECT id, FirstName, LastName,  Email, UserName, Password FROM " . $this->table . " WHERE Email = ? LIMIT 0,1";

      //prepare query 
      $stmt = $this->conn->prepare($query);

      //clean data
      $this->Email = htmlspecialchars(strip_tags($this->Email));
    
    // Bind given email value
      $stmt->bindParam(1, $this->Email);
    
    //execute the query
    $stmt->execute();

         // get number of rows
         $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
         // if Username exists, assign values to object properties for easy access and use for php sessions
         if($row>0){
          if ($Email==$row['Email'])
          {
             return true;
            }
          }
  
      // return false if email does not exist in the database
      return false;
    }
        
    public function Authentication($login){
      $query = "SELECT  * FROM `registration` WHERE ( UserName='$login' OR Email = '$login')";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if($row>0){
         // assign values to object properties
         $this->id = $row['id'];
         $this->FirstName = $row['FirstName'];
         $this->LastName = $row['LastName'];
         $this->UserName = $row['UserName'];
         $this->Password = $row['Password'];

         return true;
      }
      return false;
    }

    // Update user's data
    public function update() {
      // if password needs to be updated
      $password_set=!empty($this->Password) ? " Password = :Password" : "";

      // if no posted password, do not update the password
      $query = 'UPDATE ' . $this->table . '
                            SET 
                              FirstName = :FirstName, 
                              LastName = :LastName, 
                              UserName = :UserName,' . $password_set .'
                            WHERE id = :id';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->id = htmlspecialchars(strip_tags($this->id));
      $this->FirstName = htmlspecialchars(strip_tags($this->FirstName));
      $this->LastName = htmlspecialchars(strip_tags($this->LastName));
      $this->UserName = htmlspecialchars(strip_tags($this->UserName));
      
      // Bind data
      $stmt->bindParam(':id', $this->id);
      $stmt->bindParam(':FirstName', $this->FirstName);
      $stmt->bindParam(':LastName', $this->LastName);
      $stmt->bindParam(':UserName', $this->UserName);
  

      // hash the password before saving to database
      if(!empty($this->Password)){
        $this->Password=htmlspecialchars(strip_tags($this->Password));
        $password_hash = password_hash($this->Password, PASSWORD_BCRYPT);
        $stmt->bindParam(':Password', $password_hash);
        }

      // Execute query
      if($stmt->execute()) {
        return true;
      }

      // Print error if something goes wrong
      printf("Error: %s.\n", $stmt->error);

      return false;
  }

  // Delete user data
  public function delete() {
    // Create query
    $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Clean data
    $this->id = htmlspecialchars(strip_tags($this->id));

    // Bind data
    $stmt->bindParam(':id', $this->id);

    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: %s.\n", $stmt->error);

    return false;
}

}
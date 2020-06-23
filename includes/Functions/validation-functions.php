<?php
function validateUniqueness($value ,$field , $table){
  $c = connectDatabase();
  $errors =[];
  $query = "select {$field} from {$table}";
  $result = $c->query($query);
  $rows = $result->num_rows;
  for ($j = 0 ; $j < $rows ; ++$j){
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if($row[$field] === $value ){
      $errors[$field] = "{$field} already exists";
    }
  }
  $result->close();
  $c->close();
  return $errors;
}

function hasPresence($value){
  return isset($value) && $value !== "";
}

function validatePresences($requiredFields , $p){
  $errors =[];
  foreach($requiredFields as $field){
    $value = trim($p[$field]);
    if(!hasPresence($value)){
      $errors[$field] = "{$field} can't be blank";
    }
  }
  return $errors;
}

function validateFilePresence($f){
  $errors = [];
  if (!$f) {
    $errors["filePresence"] = "Please upload a profile photo";
  }else{
    switch($f['photo']['type']){
      case 'image/jpeg': $ext = 'jpg'; break;
      case 'image/gif': $ext = 'gif'; break;
      case 'image/png': $ext = 'png'; break;
      case 'image/tiff': $ext = 'tif'; break;
      default: $ext = ''; break;
    }
    if(!$ext){
      $errors["filleType"]= "Uploaded file is not a valid image";
    }
  }



  return $errors;
}

function validateLength($field , $min , $max , $p){
  $errors = [];
  $value = $p[$field];
  if (!(strlen($value) >= $min && strlen($value) <= $max)){
    $errors[$field] = "{$field} must be between {$min} and {$max} characters";
  }
  return $errors;
}

function validatePass($p){
  $errors =[];
  $pass = $p["pass"];
  $cpass = $p["cpass"];
  if(strlen($pass) <8){ $errors["pass"] = "password must be at least 8 charcacters"; }
  if($pass !== $cpass){$errors["cpass"] = "password does not match"; }
  return $errors;
}

function validateSignup($p ,$f){

  $errors =[];
  $valid = [];
  $errors[] = validatePresences(["username" , "email" , "pass" , "cpass" , "gender" ],$p);
  $errors[] = validateFilePresence($f);
  $errors[] = validateLength("username" , 5 , 50 , $p);
  $errors[] = validatePass($p);
  $errors[] = validateUniqueness($_POST["username"] , "username" , "users");

  foreach ($errors as $er) {
    if ($er == null) {
      $valid[] = true;
    } else {
      $valid[] = false;
    }
  }

  return [$errors , $valid];
}

?>

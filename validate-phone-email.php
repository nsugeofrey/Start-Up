//this code is regular expression to validate the user phone numbers. 
//this expression will only test numbers for the United Arab Emirates only

//Regular expression - the code considers two prefixes ie (058, 056)
^05[8|6]\s[0-9]{4}\s[0-9]{3}$
<?php
function validatePhone($val){
  if(preg_match("/^05[8|6]\s[0-9]{4}\s[0-9]{3}$/", $val)){
    echo $val;
  }
  else{
    echo "Enter Valid Number";
  }
}
  ?>


//this code is regular expression to validate email addresses

function validateEmail($val){
  if(preg_match("/^[a-zA-Z0-9_]+@+[0-9a-zA-Z_]+\.[a-zA-Z]{2,5}$/", $val)){
    echo $val;
  }
  else{
    echo "Enter Valid Email";
  }
}
  ?>

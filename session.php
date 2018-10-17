<?php
 
session_start();
echo $res = ($_POST['role_id']);
print_r($_SESSION);
    
if( isset($_POST['role_id']) ) {
    echo '<script> alert("hello");</script>';
    // save values from other page to session
    $_SESSION['role_id'] = $_POST['role_id'];
    $res = ($_POST['role_id']);
    
}

?>
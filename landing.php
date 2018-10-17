<?php
require "init.php";
include( 'include/config.php' );
session_start();

$db = mysqli_connect($servername, $username, $password, $db_name);
if (!isset($_SESSION['user']) ) {
    $user = $_SESSION['user'];
    header("location: index.php");
}
if (!isset($_SESSION['role_id']) ) {
    $user = $_SESSION['role_id'];
    header("location: index.php");
}
// 		while ($row1 = mysqli_fetch_array($record)) {
// 			 $all_skills[] = $row1['name'];
// 		}
// var_dump($user);
// print_r( $_SESSION['user']);
   
  $user = $_SESSION['user'];
  $role_id = $_SESSION['role_id'];
  $fname = $user->firstName ;
  $lname =  $user->lastName ;
  $namefile = $fname.$lname;
  $email = $user->emailAddress ;
  $phone1 = "";
  $location = $user->location->name; 
  $industry = $user->industry;
  $year1 = $user->positions->values[0]->startDate->year;
  $gensummary = $user->summary ;


   $query = "SELECT * FROM `roles` WHERE id = $role_id";
   $result = mysqli_query($db, $query);
   $row = mysqli_fetch_assoc($result);
   
   $db_role_id = $row[id];
   $db_role_name = $row[name];
   

   
  
//general Summary  fetching from json array. convert string to array.
  explode(" ",$gensummary);
//fetch multiple summary from linkid json array
  $summary = array();
  $title = array();
  for ($i = 0; $i < count($user->positions->values); $i++) {
       $summary[$i] = $user->positions->values[$i]->summary;
       $title[$i]  = $user->positions->values[$i]->title;
       
       }
       
       
   
//--------------main summary fetch array value convert into string -------------------------------------------------------
 $conv_summary = implode( ", ", $summary );
 $conv_title = implode( ", ", $title );
 
 if (strpos($conv_title, $db_role_name) == false) 
{
    	$_SESSION['message'] = "Sorry your profile not match.";
    header("location: index.php");
}
//main summary string to convert into array
//echo '<br>---------------------------------------------------------------------<br>';
//print_r (explode(" ",$conv_summary));
//print_r(array_values($summary));
//echo '<br>---------------------------------------------------------------------<br>';

    $skills = array();
	$all_skills = array();
    $record = mysqli_query($db, "SELECT name FROM keywords where role_id = $role_id");
		while ($row1 = mysqli_fetch_array($record)) {
			 $all_skills[] = $row1['name'];
		}
    
    	$skl = implode(' ',$all_skills);
        $mulstr = implode(' ', $summary);
    	$txt = $user->summary;
		$conv_summary = array();
		$combine_summarry = array();
        $conv_summary = implode( ", ", $summary );
    	$combine_summarry = explode(" ",$conv_summary);
    	
    //	print_r($combine_summarry);
    //	echo '<br>---------------------------------------------------<br>';
    	 $gen_summary = explode(" ",$gensummary);
       	//print_r($gen_summary);
      // 	echo '<br>---------------------------------------------------<br>';
       	
        $result=array_merge($combine_summarry,$gen_summary);
        //  print_r($result);
         
       //	echo '<br>-----------------------------unique array ----------------------<br>';
       	$unique_result = array_unique($result);
       	
      // print_r(array_unique($result));
         $txtstr = implode( ", ", $unique_result );
      
    	$total = 0;
	   foreach ($all_skills as $skill) {
	  
			if( stripos( $txtstr, $skill ) ) {
			    
				 $skills[] = $skill;
				  $total++;
				
			}
			
		}
		$matchskill = implode( ", ", $skills );
	
		
		if($total>0){
			 $percentage = ($total*100)/count($all_skills);
$insert = 'INSERT INTO result_data(role_id, filename, email, location, industry, number, percentage,status, skills,year,created_at) VALUES 
("'.$role_id.'","'.$namefile.'","'.$email.'","'.$location.'","'.$industry.'","'.$phone1.'","'.$percentage.'","success","'.$matchskill.'","'.$year1 .'","'.date('Y-m-d H:i:s').'")';
		mysqli_query($db,$insert);
		if(number_format($percentage,2)>=50){
		$_SESSION['message'] =   '“Congratulations! You are a shoe in for this role. We will send you a qualitative analysis of the spikes on your resume in an email with detailed suggestions in less than 24 hours”' ;
		 header("location: index.php");
		}
		else if(number_format($percentage,2)>=20 && number_format($percentage,2)<50){
				
				$_SESSION['message'] = '“As it stands, your profile is a 50-50  match for this role. We will help you figure out where you need to add experience in an email with detailed suggestions in less than 24 hours”';
		          header("location: index.php");
			}
		else
		{
		    	$_SESSION['message'] =  '“As it stands, your profile is a low match for this role. We will help you figure out where you need to add experience in an email with detailed suggestions in less than 24 hours”';
		          header("location: index.php");
		    
		}
		}
		
		else{
		$percentage = '0';
		$insert = 'INSERT INTO result_data(role_id, filename, email, location, industry, number, percentage,status, skills,year,created_at) VALUES
		("'.$role_id.'","'.$namefile.'","'.$email.'","'.$location.'","'.$industry.'","'.$phone1.'","'.$percentage.'","fail","'.$matchskill.'","'.$year1.'","'.date('Y-m-d H:i:s').'")';
			mysqli_query($db,$insert);
		$_SESSION['message'] = 'match in your profile. We are getting your in depth qualitative results to your email in the next 24 hours!';
		 header("location: index.php");
		}
		
	

?>


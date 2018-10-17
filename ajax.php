<?php
include( 'include/config.php' );
$db = mysqli_connect($servername, $username, $password, $db_name);

if (!file_exists('pdf')) {
	mkdir('pdf', 0777);
}
	$ountput = '';
	$year1 = '';
	$target_dir = "pdf/";
	$namefile = time().$_FILES["file"]["name"];
	$target_file = $target_dir .$namefile ;
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
	
	// Include Composer autoloader if not already done.
	include 'src/vendor/autoload.php';
	 
	// Parse pdf file and build necessary objects.
	$parser = new \Smalot\PdfParser\Parser();
	$pdf    = $parser->parseFile('pdf/'.$namefile);
	 
	$text = $pdf->getText();

	preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $text, $email);
	preg_match_all('(\d{3}[-\.\s]??\d{3}[-\.\s]??\d{4}|\(\d{3}\)\s*\d{3}[-\.\s]??\d{4}|\d{3}[-\.\s]??\d{4})',$text,$phone);

	$ph_no = array();
	foreach ($phone[0] as $ph) {
		if( strlen( $ph ) > 9 ) {
			$ph_no[] = $ph;
		}
	}
	$phone1 = '';
	if( !empty( $ph_no ) ) {
		
		/*$ountput .= " <div style='text-align:center'>Phone: " . implode( ', ', $ph_no )."</div>";*/
		
		$phone1 = implode( ', ', $ph_no );
		
	}
	
	if( !empty( $email[0] ) ) {
		
		/*$ountput .= "<div style='text-align:center'>Email: " . implode( ', ', array_unique( $email[0] ) )."</div>";*/
		$email = implode( ', ', array_unique( $email[0] ) );
	}
	
	$skills = array();
	$all_skills = array();
	
		$record1 = mysqli_query($db, "SELECT name FROM skill");
		while ($row1 = mysqli_fetch_array($record1)) {
			$all_skills[] = $row1['name'];
		}
		
		//$all_skills = array('Bilingual', 'Zendesk', 'Freshdesk', 'Kayako');
		foreach ($all_skills as $skill) {
			if( stripos( $text, $skill ) ) {
				$skills[] = $skill;
			}
		}
		if( !empty( $skills ) ) {
			
			/*$ountput .= "<div style='text-align:center'>Skills: " . implode( ', ', array_unique( $skills ) )."</div>";*/
			$skill = implode( ', ', array_unique( $skills ) );
		}

		$grad_year = array('education','university');
		foreach ($grad_year as $needle) {
			if( $pos = stripos( $text, $needle ) ) {
				$sub_str = substr( $text, $pos );
				preg_match_all("/[0-9]{4}/", $sub_str, $year);
				$yrs = array();
				if( !empty( $year[0] ) ) {
					foreach ($year[0] as $yr) {
						if( $yr > 1970 && $yr <= date('Y') ) {
							$yrs[] = $yr;
							break;
						}
					}
					
					/*$ountput .= "<div style='text-align:center'>Graduation Year: " . implode( ', ', array_unique( $yrs ) )."</div>";*/
					$year1 = implode( ', ', array_unique( $yrs ) );
					
					break;
				}
			}
		}
	
	if( !empty( $_REQUEST['role_id'] ) ) {

		$role_id = $_REQUEST['role_id'];
		$keyword1 = array();

		$record = mysqli_query($db, "SELECT name FROM keywords WHERE role_id=$role_id");
		while ($row = mysqli_fetch_array($record)) {
			$keyword1[] = $row['name'];
		}
		$kwrd = implode(',',$keyword1);
		$keyword = explode(',',$kwrd);
		//print_r($keyword);
		
		$total = 0;
		foreach ($keyword as $key) {
			if (strpos($text, $key) !== false) {
				$total ++;
			}
			
		}
		
		if($total>0){
			$percentage = ($total*100)/count($keyword);
			
			$insert = 'INSERT INTO result_data(role_id, filename, email, number, percentage,status, skills,year,created_at) VALUES ("'.$role_id.'","'.$namefile.'","'.$email.'","'.$phone1.'","'.$percentage.'","success","'.$skill.'","'.$year1 .'","'.date('Y-m-d H:i:s').'")';
			mysqli_query($db,$insert);
			
			if(number_format($percentage,2)>=50){
				/*$ountput .='<div style="text-align:center;color:green;">Congratulations! You are a shoe in for this role. We will send you a qualitative analysis of the spikes on your resume in an email with detailed suggestions in less than 24 hours ;</div>';*/
					$_SESSION['message'] = "Congratulations! You are a shoe in for this role. We will send you a qualitative analysis of the spikes on your resume in an email with detailed suggestions in less than 24 hours ";
				
			}
			else if(number_format($percentage,2)>=20 && number_format($percentage,2)<50){
              /* $ountput .='<div style="text-align:center;color:green;">'.number_format($percentage,2).'% match in your profile. We are getting your in depth qualitative results to your email in the next 24 hours!</div>';*/
               	$_SESSION['message'] = ' match in your profile. We are getting your in depth qualitative results to your email in the next 24 hours!';
			    
			}
			else{
				/*$ountput .='<div style="text-align:center;color:green;">'.number_format($percentage,2).'% As it stands, your profile is a low match for this role. We will help you figure out where you need to add experience in an email with detailed suggestions in less than 24 hours. </div>';*/
					$_SESSION['message'] = 'As it stands, your profile is a low match for this role. We will help you figure out where you need to add experience in an email with detailed suggestions in less than 24 hours. ';
			
			}
		}
		else{
			$percentage = '0';
			$insert = 'INSERT INTO result_data(role_id, filename, email, number, percentage,status, skills,year,created_at) VALUES ("'.$role_id.'","'.$namefile.'","'.$email.'","'.$phone1.'","'.$percentage.'","fail","'.$skill.'","'.$year1.'","'.date('Y-m-d H:i:s').'")';
			mysqli_query($db,$insert);
		//	$ountput .='<div style="text-align:center;color:red">your profile not match.</div>';
		$_SESSION['message'] = "your profile not match.";
		
		}
	}
	
	$selectmsg = mysqli_query($db,"SELECT massage FROM massage WHERE id=1");
	$rows = mysqli_fetch_array($selectmsg);
	$lastmsg = $rows['massage'];
	$ountput .= "<div style='text-align:center;color:green;'>".$_SESSION['message']."</div>";
	
	echo $ountput;
//$_SESSION['message'] = 	$lastmsg;
?>
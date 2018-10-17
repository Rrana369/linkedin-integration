<?php
	$output ='';
	$role = 'developer';
	if($role == 'structured')
	{
		$keyword = array('schedule','scoping','bandwidth','team leader');
	}
	elseif($role == 'developer')
	{
		$keyword = array('php','java','wordpress','ios','inr','till','Date');
	}
	else
	{
		$output = 'your role not macth in this job.';
	}
	
	
?>
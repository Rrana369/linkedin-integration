<?php
 
include( 'include/header.php' ); 
include( 'include/config.php' );
$db = mysqli_connect($servername, $username, $password, $db_name);
session_start(); // for show your session value 
//print_r($_SESSION); // remove this after check

?>

<?php
    require_once "init.php";
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    function update_session_value(value) {
      jQuery('#resultmsg').html('');
       jQuery('#resultmsg').html('');
       $('#resultmsg').css("display","none")
        $.ajax({
            type: 'POST',
            url: 'session.php', // change url as your 
            data: {role_id:value},
            dataType: 'json',
            success: function (data) {
                //alert(jQuery.parseJSON( data ));
            }
            
        });
    }

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>


<h1 style=" text-align: center;">Authenticate with LinkedIn or Upload your resume to see result. </h1>

<?php $roles = mysqli_query($db, "SELECT * FROM roles ORDER BY name ASC"); ?>
<div class="form">
       <div class="input-group">
				
				<select name="role_id" id="role_id" onchange="update_session_value(this.value)">
				<option value="0">Select Role</option>
				<?php if( !empty( $roles ) ) {
					while ($row = mysqli_fetch_array($roles)) { ?>
						<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
				<?php }
				} ?>
				</select>
		
		</div>
    <div class="logo">
	    <a class="logo_link" id="logo_link" style="font-family: 'Arial';" href="<?php  
echo "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id={$client_id}&redirect_uri={$redirect_uri}&state={$csrf_token}&scope={$scopes}"; ?>"><img src="/img/Sign-in-Large---Default.png" alt="Logo" width="300" ></a>
	</div>
<div id="drop_file_zone" ondrop="upload_file(event)" ondragover="return false">
	
	
	<div id="drag_upload_file">
		<p>Drop file here</p>
		<p>or</p>
		<p><input type="button" value="Select File" onclick="file_explorer();"></p>
		<input type="file" id="selectfile">
	</div>
	
</div>

   <!--div id="resultmsg" style="display:none;background:#EDEDED;color:gray">

   </div--> 
    <?php
        if(!empty($_SESSION['message'])) {
        
           echo   '<div id="resultmsg" style="display:block;background:#EDEDED;"><p style="color:green;"> ' .  $_SESSION['message'] . '</p></div>';
        }
        else{
           
           echo   '<div id="resultmsg" style="display:block;background:#EDEDED;">  <p  style="color:green;"> ' .  $_SESSION['message'] .'</p></div>';
            //echo $_GET['message'];
        }
        
    ?>


</div>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript">
	var fileobj;
	function upload_file(e) {
		e.preventDefault();
		fileobj = e.dataTransfer.files[0];
		ajax_file_upload(fileobj);
	}

	function file_explorer() {
		document.getElementById('selectfile').click();
		document.getElementById('selectfile').onchange = function() {
		    fileobj = document.getElementById('selectfile').files[0];
			ajax_file_upload(fileobj);
		};
	}

	function ajax_file_upload(file_obj) {
	 
		if(file_obj != undefined) {
			var role_id = $('#role_id').val();
			  
			if(role_id =="0"){
				alert('Please select role.');
				$('#role_id').focus();
				return false;
			}
		    var form_data = new FormData();                  
		    form_data.append('file', file_obj);
		    form_data.append('role_id', role_id);
			$.ajax({
				type: 'POST',
				url: 'ajax.php',
				contentType: false,
				processData: false,
				data: form_data,
				success:function(response) {
					//alert(response);
					$('#selectfile').val('');
					$('#resultmsg').html(response);
					$('#resultmsg').show();
				}
			});
		}
	}
</script>
<script type='text/javascript'>
$(document).ready(function() {
  
    $("#logo_link").click(function(){
  //this will find the selected website from the dropdown
  var role = $("#role_id").find(":selected").val();
  
  if(role == 0)
  {
      alert("please select first role :  ");
    //  window.location.reload();
   return false;
  }
  
  
  //this will redirect us in same window
  
});
 
 
});
</script>


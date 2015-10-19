<html>
	<head>
		<title>Login Form</title>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/style.css">

		<script type="text/javascript">

			onload=function(){ attachHandlers(); }

			function attachHandlers(){
			  var the_nums = document.getElementsByName("number");
			  for (var i=0; i < the_nums.length; i++) { 
			  		the_nums[i].onclick=inputNumbers; 
			  }
			}

			function inputNumbers() {
			  var the_field = document.getElementById('calcfield');
			  var the_value = this.value;
			  switch (the_value) {
			    case 'Clear' :
			      the_field.value = '';
			      break;
			    default : document.getElementById("calcfield").value += the_value;
			      break;
			  }
			  document.getElementById('calcfield').focus();
			  return true;
			}

		</script>

	</head>
<body>

<div id="wraper">
	<div id="container">		
		<?php echo $message_display; ?>

		<div id="login">
			<h2>User Login</h2>
				<?php echo form_open('user_authentication/user_login_process'); ?>
			
				<?php
					echo '<div class="error_msg">';
					
					if (isset($error_message)) 
					{
						echo $error_message;
					}
					
					echo validation_errors();
					echo "</div>";
				?>

			<?php echo form_dropdown('username', $users, '', 'id="drop_lst"'); ?> <a href="<?php echo base_url(); ?>index.php/user_authentication/user_registration_show" class="button">Sign Up</a><br>

			<input type="password" name="password" id="calcfield" value="" size="20" maxlength="4" placeholder="*******"/>
			
			<div id="keypad">
			    <div id="row1">
				    <input type="button" name="number" value="1" id="_1" class="btns">
				    <input type="button" name="number" value="2" id="_2" class="btns">
				    <input type="button" name="number" value="3" id="_3" class="btns">
			    </div>
			    <div id="row2">
				    <input type="button" name="number" value="4" id="_4" class="btns">
				    <input type="button" name="number" value="5" id="_5" class="btns">
				    <input type="button" name="number" value="6" id="_6" class="btns">
			    </div>
			    <div id="row3">
				    <input type="button" name="number" value="7" id="_7" class="btns">
				    <input type="button" name="number" value="8" id="_8" class="btns">
				    <input type="button" name="number" value="9" id="_9" class="btns">
			    </div>
			    <div id="row4">
			    	<input type="submit" name="submit" value="Login" id="_sub" class="btns">
				    <input type="button" name="number" value="0" id="_0" class="btns">
				    <input type="button" name="number" value="Clear" id="cls" class="btns">
			    </div>
			</div>
			
			<?php echo form_close(); ?>

		</div>
	</div>

<?php require_once('templates/footer.php'); ?>
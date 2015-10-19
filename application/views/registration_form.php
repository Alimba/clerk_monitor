<html>
	<head>
		<title>Registration Form</title>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/style.css">
	</head>

	<body>
		<div id="wraper">
			<div id="container">
				<div id="registration">
					<?php
						$data = array(
							'type' => 'email',
							'name' => 'email_value'
							);

						$form_elements = array(
							form_input('name'), 
							form_input('username'), 
							form_input($data), 
							form_password('password'),
							);
						
						echo "<div class='error_msg'>";
						echo validation_errors();
						if (isset($message_display))
						{
							echo $message_display;
						}
						echo "</div>";

						echo '<h2>Registration Form</h2>';

						echo form_open('user_authentication/new_user_registration');

						$this->table->set_heading(array('Name', 'User Name', 'e-mail', 'Password'));
						$this->table->add_row($form_elements);

						echo $this->table->generate();

						echo form_submit('submit', 'Sign Up', 'class="button"');

						echo form_close();
					?>
					<input type=button value="Go Back" class="button" onclick="location.href='<?php echo base_url() ?>'" />
			</div>
		</div>

<?php require_once('templates/footer.php'); ?>

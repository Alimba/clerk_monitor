<?php require_once('templates/header.php'); ?>
	<div id="container">
		<?php echo $message_display; ?>

		<div id="home_admin">
			<h4>Active Clerks</h4>
			<table id="active">
				<thead>
					<th>Name</th>
					<th>Passphrase</th>
					<th>Location</th>
					<th>Role</th>
					<th>Company</th>
					<th>Date Started</th>
					<th>Date Finished</th>
				</thead>
				<?php
					if (isset($file))
					{
						echo form_open('main/clerk_process');

						foreach ($file as $element => $value) 
						{
							if ($file[$element]['end_date'] == '0000-00-00')
							{
								echo '<tr><td>' . form_hidden('clerk_id'.$element, $value['clerk_id']) . $value['clerk_name'] .
									 '</td><td>' . $value['secret_no'] .
									 '</td><td>' . form_dropdown('loc'.$element, $loc_list, $value['location_id'], 'class="button_small"') . 
									 '</td><td>' . form_dropdown('role'.$element, $role_list, $value['role_id'], 'class="button_small"') . 
									 '</td><td>' . form_dropdown('comp'.$element, $comp_list, $value['company_id'], 'class="button_small"') . 
									 '</td><td><input type="date" name="' . 'dateS'.$element . '" value="'.$value['start_date'].'" id="item_date" class="button_small"></td>' .
									 '</td><td><input type="date" name="' . 'dateE'.$element . '" value="'.$value['end_date'].'" id="item_date" class="button_small"></td></tr>';
							}	
						}
						echo form_hidden('last_clerk', $element);
						echo form_submit('submit', 'Submit', 'id="admin_btn" class="button"');
						echo form_close();
					}
					
				?>

			</table>

		<hr>

			<h4>Inactive Clerks</h4>
				<table id="inactive">
					<thead>
						<th>Name</th>
						<th>Date Started</th>
						<th>Date Finished</th>
					</thead>
				<?php
					if (isset($file))
					{
						echo form_open('main/clerk_process');
						
						foreach ($file as $element => $value) 
						{
							if ($file[$element]['end_date'] > 0)
							{								
								echo '<tr><td>' . form_hidden('clerk_id'.$element, $value['clerk_id']) . $value['clerk_name'] .
									 '</td><td><input type="date" name="' . 'dateS'.$element . '" value="'.$value['start_date'].'" id="item_date" class="button_small"></td>' .
									 '</td><td><input type="date" name="' . 'dateE'.$element . '" value="'.$value['end_date'].'" id="item_date" class="button_small"></td></tr>';
							}	
						}
						echo form_hidden('last_clerk', $element);
						echo form_submit('submit', 'Submit', 'id="admin_btn" class="button"');
						echo form_close();
					}
					
				?>

				</table>
		</div>
	</div>

<?php require_once('templates/footer.php'); ?>


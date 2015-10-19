<?php require_once('templates/header.php'); ?>

<div id="container">
	
	<?php echo $message_display; ?>
	<div id="report_page">
		<h4>Reports</h4>

		<table>
			<thead>
				<th>Report Type</th>
				<th>Date From</th>
				<th>Date To</th>
				<th>Company</th>
				<th>Clerk</th>
			</thead>

			<?php
				
				echo '<h4 class="error_msg">' . validation_errors() . '</h4>';

	// settings for the date fields			
				$date_set = array(
					array(
		              'type' => 'date',
		              'name' => 'date_from',
		              'id'   => 'date',
		              ),
					array(
		              'type' => 'date',
		              'name' => 'date_to',
		              'id'   => 'date',
		              ),
		            );
				$rep_set = array(
					'Weekly',
					'Monthly Simple',
					'Monthly',
					);

				echo form_open('reports_controller/reports');

				echo '<tr>';

					echo '<td>' . form_dropdown('rep_type', $rep_set, 'id="item_rep_type"', 'class="button_med"') . '</td>' .
						 '<td>' . form_input($date_set[0], '', 'class="button_med"') . '</td>' .
						 '<td>' . form_input($date_set[1], '', 'class="button_med"') . '</td>' .
						 '<td>' . form_dropdown('comp', $comp_list, 'id="item_comp"', 'class="button_med"') . '</td>' .
						 '<td>' . form_dropdown('clerk', $clerk_list, 'id="item_clerk"', 'class="button_med"') . '</td>' .
						 '<td>' . form_submit('submit', 'Submit', 'class="button_med"') . '</td>';		
				
				echo '</tr>';

				echo form_close();
			?>
		</table>
	</div>
</div>

<?php require_once('templates/footer.php'); ?>
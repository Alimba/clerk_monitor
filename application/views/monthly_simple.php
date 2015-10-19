<?php require_once('templates/header.php'); ?>

<div id="container">
	<br>

	<input type="button" onclick="printDiv('monthly-simple')" value="Print Report" class="button" />

	<div id="monthly-simple">
		<h4>Monthly Simple Reports</h4>

		<?php

			foreach ($clerk_list as $key => $value) 
			{
				echo '<div class="each_table">';
				echo '<br>';

				$this->table->set_caption($clerk_list[$key]['clerk_name'] . ' - ' . $clerk_list[$key]['secret_no']);
			
				$this->table->set_heading('Sick Day', 'Sick Pay', 'Holidays Total', 'Holiday Entitlement', 'Holidays Took', 'Holidays Remain', 'Last Year Holidays');
			
				$this->table->add_row($sick_days[$key]['sick_days'], $sick_days[$key]['sick_pay'], '??', $holidays[$key]['total_holiday_to_take'], $holidays[$key]['holidays_took'], $holidays[$key]['holidays_left'], $holidays_last[$key]['holidays_left']);
			
				echo $this->table->generate();

				echo '<hr>';
	// OVERTIME SECTION ################################################################

				$this->table->set_caption('Overtime');
				
				$dates[] = 'Total';
				$time[] = sec_to_time($clerk_list[$key]['total_overtime']);
				$real_time[] = ' +' . $dayly_overtime[$key]['overtime'] . ' days ';

				foreach ($clerk_list[$key]['overtime'] as $key_over => $value2) 
				{
					if ($value2['date'] != NULL)
					{
						$dates[] = date('d-m-Y', strtotime($value2['date']));	
						$time[] = $value2['time'];
						$real_time[] = $value2['real_time'];
					}
				}

				$this->table->set_heading($dates);

				$this->table->add_row($time);	

				$this->table->add_row($real_time);	
				
				echo $this->table->generate();

				unset($dates);
				unset($time);
				unset($real_time);

				echo '<hr>';
	// DEDUCTIONS SECTION ################################################################

				$this->table->set_caption('Deductions');

				$dates[] = 'Total';
				$time[] = sec_to_time($clerk_list[$key]['total_deductions']);
				$real_time[] = '';

				foreach ($clerk_list[$key]['deductions'] as $key_over => $value2) 
				{
					if ($value2['date'] != NULL)
					{
						$dates[] = date('d-m-Y', strtotime($value2['date']));
						$time[] = $value2['time'];
						$real_time[] = $value2['real_time'];
					}
				}
			
				$this->table->set_heading($dates);
			
				$this->table->add_row($time);	

				$this->table->add_row($real_time);	

				echo $this->table->generate();

				echo '<br>';

				unset($dates);
				unset($time);
				unset($real_time);

				echo '</div>';
			}

		?>
	</div>
</div>

<?php require_once('templates/footer.php'); ?>
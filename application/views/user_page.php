<?php require_once('templates/header.php'); ?>
	<div id="container">
		<?php echo $message_display; ?>
		
		<div id="clock_in">
			<?php 
				echo '<a href="' . base_url('index.php/main/clock_in_user') . '" class="button">Clock In</a>';
				echo '<a href="' . base_url('index.php/main/clock_out_user') . '" class="button">Clock Out</a>';
			
			?>
		</div>

		<div id="rota_table">	

		<?php
			if ($rota != NULL)
			{
				$rota = array_chunk($rota, 7);

				array_unshift($dates, '');

				$this->table->set_heading($dates);

			
					foreach ($rota as $key => $clerk) 
					{	
						$this->table->add_row('Name', $clerk[0]['clerk_name'], $clerk[1]['clerk_name'], $clerk[2]['clerk_name'], $clerk[3]['clerk_name'], $clerk[4]['clerk_name'], $clerk[5]['clerk_name'], $clerk[6]['clerk_name'] );
						$this->table->add_row('Location', $clerk[0]['locations'], $clerk[1]['locations'], $clerk[2]['locations'], $clerk[3]['locations'], $clerk[4]['locations'], $clerk[5]['locations'], $clerk[6]['locations'] );
						$this->table->add_row('Status', $clerk[0]['status'], $clerk[1]['status'], $clerk[2]['status'], $clerk[3]['status'], $clerk[4]['status'], $clerk[5]['status'], $clerk[6]['status'] );
						$this->table->add_row('Start at', $clerk[0]['rota_from'], $clerk[1]['rota_from'], $clerk[2]['rota_from'], $clerk[3]['rota_from'], $clerk[4]['rota_from'], $clerk[5]['rota_from'], $clerk[6]['rota_from'] );
						$this->table->add_row('Finish at', $clerk[0]['rota_to'], $clerk[1]['rota_to'], $clerk[2]['rota_to'], $clerk[3]['rota_to'], $clerk[4]['rota_to'], $clerk[5]['rota_to'], $clerk[6]['rota_to'] );
						$this->table->add_row('','','','','','','','');
					}

				echo $this->table->generate();
			}
		?>
		</div>


	</div>

<?php require_once('templates/footer.php'); ?>
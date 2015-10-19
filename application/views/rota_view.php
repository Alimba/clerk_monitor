<?php require_once('templates/header.php'); ?>

<div id="container">
<?php echo $message_display; ?>
 	
 	<div id="rota_page">
	 	<?php
			if ($message_display == '')
			{
				echo '<h4>' . $location . ' Rota</h4>';

				echo form_open('rota/process_rota'); 

	/////////// This Week data Preperation ////////////////
				$this->table->set_heading($date_this);

				$this->table->set_caption('This Week');

				for ($i = 0; $i < $clerk_No; $i++)
				{			
					$this->table->add_row($this_data[$i]);
					$this->table->add_row('','','','','','','');
				}
				echo $this->table->generate();
				
				echo '<br><hr><br>';
	/////////// Next Week data Preperation ///////////////
					
				$this->table->set_caption('Next Week');
				
				$this->table->set_heading($date_next);

				for ($i = 0; $i < $clerk_No; $i++)
				{
					$this->table->add_row($next_data[$i]);
					$this->table->add_row('','','','','','','');
				}
					
				echo $this->table->generate();

				echo form_hidden('total_clerks', $clerk_No);

				echo '</div>';

				echo form_submit($location, 'Submit', 'id="admin_btn" class="button"');
			
				echo form_close();
			}
		?>

</div>

<?php require_once('templates/footer.php'); ?>


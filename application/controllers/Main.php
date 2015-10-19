<?php
	//session_start();

	Class Main extends CI_Controller
	{
		public function __construct() 
		{
			parent::__construct();
//load helper libraries
			$this->load->helper('url');
			$this->load->helper('form');
			$this->load->helper('file');
			$this->load->helper('security');
			$this->load->library('table');
			$this->load->library('session');
			$this->load->model('load_file');
			$this->load->model('login_database');
			$this->load->library('csvreader');
		}

		public function home_admin()
		{
// Load the file, from the following path
        	$filePath = './application/incoming/mytest.txt';
      
        	$csvData = $this->csvreader->parse_file($filePath);

        	$fileDate = $this->csvreader->get_file_date($filePath);

			$fileDate = explode('-', $fileDate);

        	$fileDate = date('Y-m-d', strtotime($fileDate[0].'-'.$fileDate[2].'-'.$fileDate[1]));

// Organise the array
			$organised_line = array(
                'till_reference'           ,
                'clerk_name'               ,
                'ibutton/mag_card_number'  ,
                'secret_sign_on_number'    ,
                'compulsions'              ,
                'allowed_functions1'       ,
                'mode_control'             ,
                'operation'                ,
                'allowed_functions2'       ,
                'allowed_functions3'       ,
                'commission1'              ,
                'commission2'              ,
                'commission3'              ,
                'commission4'              ,
                'start_clerk_range'        ,
                'end_clerk_range'          ,
                'start_table_range'        ,
                'end_table_range'          ,
                'default_price_level'      ,
                'default_menu_level'       ,
                'default_floor_plan_level' ,
                'reserved1'                ,
                'reserved2'                ,
                'reserved3'                ,
                'reserved4'                ,
                'reserved5'                ,
                'creation_date'			   ,
                );

            foreach ($csvData as $line => $value)
            {
            	$value[] = $fileDate;
               	$result[] = array_combine($organised_line, $value);
            }

// Call the model, check if the date is the same. insert the data only if they are different
            $query = $this->load_file->get_data_extend('raw_data', 'creation_date');

			$check = $this->load_file->get_data_extend('raw_data', '*');

			$check_clerk = $this->load_file->get_data_extend('clerk', '*');

			if (isset($check_clerk[0]))
			{
				foreach ($check_clerk as $key => $value) 
				{
					if ($check_clerk[$key]['end_date'] > 0)
					{
						$fired[] = array(
							'till_reference' => $check_clerk[$key]['till_reference'],
							'clerk_name' => $check_clerk[$key]['clerk_name'],
							'secret_no' => $check_clerk[$key]['secret_no'],
							);
					}
					else
					{
						$working[] = array(
							'till_reference' => $check_clerk[$key]['till_reference'],
							'clerk_name' => $check_clerk[$key]['clerk_name'],
							'secret_no' => $check_clerk[$key]['secret_no'],
							);
					}
				}
			}
			else
			{
				$init_data = $this->load_file->get_data_extend_where('raw_data', 'till_reference, clerk_name, secret_sign_on_number', 'secret_sign_on_number > 0');
				
				foreach ($init_data as $key => $value) 
				{
					$temp = array(
						'till_reference' => $init_data[$key]['till_reference'],
						'clerk_name' => $init_data[$key]['clerk_name'],
						'secret_no' => $init_data[$key]['secret_sign_on_number'],
						'start_date' => '0000-00-00',
						'end_date' => '0000-00-00',
						'role_id' => 0,
						'location_id' => 0,
						);

					$this->load_file->insert_data('clerk', $temp);
				}
			}


			if (isset($check[0]))
			{                                          
				if ( $query[$key]['creation_date'] != $result[$key]['creation_date'] ) // file date creation check 
					{						
						echo 'this is new file <br>';

						foreach ($result as $key => $value) 
						{
							$temp = array(
								'till_reference' => $result[$key]['till_reference'],
								);
							$this->load_file->update_data('raw_data', $result[$key], $temp);
						}

						$raw_data = $this->load_file->get_data('till_reference, clerk_name, secret_sign_on_number');

// compare the tables and we get the final result on what we need to insert as an $insert array						
						$tmpArray = array();
// at first we seperate the new data from the working clerks
						foreach($raw_data as $data1) 
						{
	  						$duplicate = false;

	  						foreach($working as $data2) 
	  						{
	    						if($data1['till_reference'] == $data2['till_reference'] && $data1['clerk_name'] == $data2['clerk_name']) 
	    						{
	    							$duplicate = true;
	    						}
	  						}

	  						if($duplicate == false) 
	  						{
	  							$tmpArray[] = $data1;
	  						}
						}

						$insert = array();
// on the second part we separate from the rest of the new data the people that fired
						foreach($tmpArray as $data1) 
						{
	  						$duplicate = false;

	  						foreach($fired as $data2) 
	  						{
	    						if($data1['till_reference'] == $data2['till_reference'] && $data1['clerk_name'] == $data2['clerk_name']) 
	    						{
	    							$duplicate = true;
	    						}
	  						}

	  						if($duplicate == false) 
	  						{
	  							$insert[] = $data1;
	  						}
						}
// now that everything is ready, we insert the data to the clerk table

						foreach ($insert as $key => $value) 
						{
							$temp = array(
								'till_reference' => $insert[$key]['till_reference'],
								'clerk_name' => $insert[$key]['clerk_name'],
								'secret_no' => $insert[$key]['secret_sign_on_number'],
								'start_date' => '0000-00-00',
								'end_date' => '0000-00-00',
								'role_id' => 0,
								'location_id' => 0,
								);
							
							$this->load_file->insert_data('clerk', $temp);

							echo 'Insert entry to clerk table<br>';
						}
					}
					else // normally this else is useless because if the file is old you don't need to update
					{
						echo 'this is old file -- debug message';
					}
			}
			else
			{
// insert the data only if the table raw_data is empty
				foreach ($result as $key => $value) 
				{
					$this->load_file->insert_data('raw_data', $result[$key]);
				}
			}

// query the database to get the data for the form			
			$result = $this->load_file->get_clerks();
			$locations = $this->load_file->get_data_extend('locations', 'locations');
			$roles = $this->load_file->get_data_extend('clerk_roles', 'role');
			$companies = $this->load_file->get_data_extend('companies', 'company');


// prepare the tables for the form			
			foreach ($locations as $row) 
			{
				 $loc_list[] = $row['locations'];
			}

			foreach ($roles as $row) 
			{
				 $role_list[] = $row['role'];
			}

			foreach ($companies as $row) 
			{
				 $comp_list[] = $row['company'];
			}

// prepere the data to sent to the admin page
           	$data = array(
				'name' => 'Admin',
				'file' => $result,
				'loc_list' => $loc_list,
				'role_list' => $role_list,
				'comp_list' => $comp_list,
				'message_display' => ''
				);
			
// Load the view as a result and check if the user is logged in
           	if ($this->login_database->is_logged())
           	{
           		$this->load->view('admin_page', $data);
           	}
		    else
		   	{
		    	$this->load->view('login_form', $data);
		    }
		}

		public function clerk_process()
		{
// Get the post variables
			for ($i = 0; $i <= $this->input->post('last_clerk'); $i++) 
			{
				$post_data_loc[] =  array(
						'clerk_id' =>	$this->input->post('clerk_id'.$i),
						'location_id' => $this->input->post('loc'.$i),
							);

				$post_data_role[] =  array(
						'clerk_id' =>	$this->input->post('clerk_id'.$i),
						'role_id' => $this->input->post('role'.$i),
							);

				$post_data_comp[] =  array(
						'clerk_id' =>	$this->input->post('clerk_id'.$i),
						'company_id' => $this->input->post('comp'.$i),
							);

				$post_data_date[] =  array(
						'clerk_id' =>	$this->input->post('clerk_id'.$i),
						'start_date' => $this->input->post('dateS'.$i),
							);

				$post_data_date_end[] =  array(
						'clerk_id' =>	$this->input->post('clerk_id'.$i),
						'end_date' => $this->input->post('dateE'.$i),
							);
			}

// Insert the data to the database
		foreach ($post_data_loc as $key => $value)
		{
			//update locations
			$this->load_file->update_data('clerk', $post_data_loc[$key], 'clerk_id ='.$post_data_loc[$key]['clerk_id']);

			//update roles
			$this->load_file->update_data('clerk', $post_data_role[$key], 'clerk_id ='.$post_data_role[$key]['clerk_id']);

			//update companies
			$this->load_file->update_data('clerk', $post_data_comp[$key], 'clerk_id ='.$post_data_comp[$key]['clerk_id']);

			//update start date
			$this->load_file->update_data('clerk', $post_data_date[$key], 'clerk_id ='.$post_data_date[$key]['clerk_id']);

			//update end date
			$this->load_file->update_data('clerk', $post_data_date_end[$key], 'clerk_id ='.$post_data_date[$key]['clerk_id']);
		}

// Prepare the data for the form
			$data = array(
				'name' => 'Admin',
				'message_display' => '<h4 class="norm_msg">Successful Update!</h4>', 
				);

// Load the view as a result and check if the user is logged in
			if ($this->login_database->is_logged())
           	{
           		$this->load->view('templates/back', $data);   
           	}
		    else
		   	{
		    	$this->load->view('login_form', $data);
		    }	
		}


		public function salons($salon)
		{
// Get the week dates
			$week_start = date('z', strtotime('this week'));
			$week_end = date('z', strtotime('next week'));

			for ($i = $week_start; $i < $week_end; $i++)
			{
				$date_this[] = date('D d M', strtotime("January 1st +".($i)." days"));
				$date_next[] = date('D d M', strtotime("January 1st +".($i+7)." days"));
			}
// presets for the droop down times
			for ($i = 0; $i < 24; $i++)
			{
				for ($j = 0; $j <= 30; $j=$j+30)
				{
					$times[str_pad($i, 2, '0', STR_PAD_LEFT) . ':' . str_pad($j, 2, '0', STR_PAD_LEFT)] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':' . str_pad($j, 2, '0', STR_PAD_LEFT);
				}
			}

// Get the data for the form
			$location = $this->load_file->get_data_extend_where('locations', 'locations', 'location_id =' . $salon);

			$clerks_to_process = $this->load_file->get_clerk_no($salon);
			
			$clerk_no = count($clerks_to_process); // data from the 'raw_data' table

// The following condition is in case you don't have any clerks assigned
		if ($clerk_no != 0)
		{

			foreach ($clerks_to_process as $key => $value) 
			{
				$clerks_to_process[$key] = $clerks_to_process[$key]['clerk_id'];
			}

			$columns = array(
				'clerks_list_rota.id',
				'clerks_list_rota.clerk_id',
				'clerks_list_rota.date',
				'clerks_list_rota.location_id',
				'clerks_list_rota.rota_from',
				'clerks_list_rota.rota_to',
				'clerks_list_rota.status_id',
				);

/// This Week calculations

			foreach ($clerks_to_process as $key => $value) 
			{
				$this_week[] = $this->load_file->get_clerks_date($columns, date('Y-m-d', strtotime($date_this[0])), date('Y-m-d', strtotime($date_this[6])), $salon, $clerks_to_process[$key]);
			}

			if (array_filter($this_week) == NULL) // no data on the clerks_list_rota table
			{
				for ($i = 0; $i < count($clerks_to_process); $i++)
				{
					for ($j = 0; $j < 7; $j++)
					{
						$this_week[$i][$j] = array(
						'clerk_id' => $clerks_to_process[$i],
						'date' => date('Y-m-d', strtotime($date_this[$j])),
						'location_id' => $salon,
						'rota_from' => '09:00:00',
						'rota_to' => '18:00:00',
						'status_id' => 1,
						);
					}
				}
			}
			else // when you have the data
			{
				for ($i = 0; $i < count($this_week); $i++)
				{
					for ($j = 0; $j < 7; $j++)
					{
						if (!isset($this_week[$i][$j]))
						{
							$this_week[$i][$j] = array(
								'clerk_id' => $clerks_to_process[$i],
								'date' => date('Y-m-d', strtotime($date_this[$j])),
								'location_id' => $salon,
								'rota_from' => '09:00:00',
								'rota_to' => '18:00:00',
								'status_id' => 1,
							);
						}
					}
				}
			}


/// Next Week calculations

			foreach ($clerks_to_process as $key => $value) 
			{
				$next_week[] = $this->load_file->get_clerks_date($columns, date('Y-m-d', strtotime($date_next[0])), date('Y-m-d', strtotime($date_next[6])), $salon, $clerks_to_process[$key]);
			}

			if (array_filter($next_week) == NULL)
			{
				for ($i = 0; $i < count($clerks_to_process); $i++)
				{
					for ($j = 0; $j < 7; $j++)
					{
						$next_week[$i][$j] = array(
						'clerk_id' => $clerks_to_process[$i],
						'date' => date('Y-m-d', strtotime($date_next[$j])),
						'location_id' => $salon,
						'rota_from' => '09:00:00',
						'rota_to' => '18:00:00',
						'status_id' => 1,
						);
					}
				}
			}
			else 
			{
				for ($i = 0; $i < count($next_week); $i++)
				{
					for ($j = 0; $j < 7; $j++)
					{
						if (!isset($next_week[$i][$j]))
						{
							$next_week[$i][$j] = array(
								'clerk_id' => $clerks_to_process[$i],
								'date' => date('Y-m-d', strtotime($date_next[$j])),
								'location_id' => $salon,
								'rota_from' => '09:00:00',
								'rota_to' => '18:00:00',
								'status_id' => 1,
							);
						}
					}
				}
			}
// We merge this_week with the next_week tables and then we insert the data
			$result_to_insert = array_merge($this_week, $next_week);

// Finally we loop and either we Update or Inserting the data to the database
			for ($i = 0; $i < count($result_to_insert); $i++)
				{
					for ($j = 0; $j < 7; $j++)
					{
						if (isset($result_to_insert[$i][$j]['id']))
						{
							$this->load_file->update_data_row('clerks_list_rota', $result_to_insert[$i][$j], $result_to_insert[$i][$j]['id']);
						}
						else
						{
							$this->load_file->insert_data('clerks_list_rota', $result_to_insert[$i][$j]);
						}
					}
				}

			$columns = array(
				'clerks_list_rota.clerk_id',
				'clerks_list_rota.date',
				'clerks_list_rota.location_id',
				'clerks_list_rota.rota_from',
				'clerks_list_rota.rota_to',
				'clerks_list_rota.status_id',
				'clerks_list_rota.id',
				'clerk.clerk_name',
				);

			$result_this = $this->load_file->get_clerks_date($columns, date('Y-m-d', strtotime($date_this[0])), date('Y-m-d', strtotime($date_this[6])), $salon );

			$result_next = $this->load_file->get_clerks_date($columns, date('Y-m-d', strtotime($date_next[0])), date('Y-m-d', strtotime($date_next[6])), $salon );

			$result_final = array_merge($result_this, $result_next);

// we count the result and we split it in half acording on how many clerks we have, plus the 2 rows for the table, Next week and This week
			$half = (count($result_final) / ($clerk_no * 2));

			$result_final = array_chunk($result_final, $half);

// Put the status and location lists
			$locations = $this->load_file->get_data_extend('locations', 'locations');
			$status = $this->load_file->get_data_extend('clerk_status', 'status');

			foreach ($locations as $key => $value) 
			{
				$loc_lst[] = $value['locations'];
			}
			
			foreach ($status as $key => $value) 
			{
				$status_lst[] = $value['status'];
			}

// Build the form's gutter			
			for ($i = 0; $i < $clerk_no; $i++)
			{
				for ($j = 0; $j < 7; $j++)
				{
					$this_data[$i][$j] = 

						form_hidden('id'.$i.$j, $result_final[$i][$j]['id']).
						form_hidden('clerk_id'.$i, $result_final[$i][$j]['clerk_id']).
						'<center>'.$result_final[$i][$j]['clerk_name'].'</center><br>'. // clerk names in an array
						form_hidden('date_t'.$j, $date_this[$j]).
						form_label('Location: ', 't,0,'.$i.','.$j).form_dropdown('t,0,'.$i.','.$j, $loc_lst, $result_final[$i][$j]['location_id'], 'class="button_small"').'<br>'.
						form_label('Time in: ', 't,1,'.$i.','.$j).form_dropdown('t,1,'.$i.','.$j, $times, date('H:i', strtotime($result_final[$i][$j]['rota_from'])), 'class="button_small"').'<br>'.
						form_label('Time out: ', 't,2,'.$i.','.$j).form_dropdown('t,2,'.$i.','.$j, $times, date('H:i', strtotime($result_final[$i][$j]['rota_to'])), 'class="button_small"').'<br>'.
						form_label('Status: ', 't,3,'.$i.','.$j).form_dropdown('t,3,'.$i.','.$j,	$status_lst, $result_final[$i][$j]['status_id'], 'class="button_small"');

					$next_data[$i][$j] = 
 
						form_hidden('id_n'.$i.$j, $result_final[$i+$clerk_no][$j]['id']).
						form_hidden('clerk_id_n'.$i, $result_final[$i][$j]['clerk_id']).
						'<center>'.$result_final[$i+$clerk_no][$j]['clerk_name'].'</center><br>'. // clerk names in an array
						form_hidden('date_n'.$j, $date_next[$j]).
						form_label('Location: ', 'n,0,'.$i.','.$j).form_dropdown('n,0,'.$i.','.$j, $loc_lst, $result_final[$i+$clerk_no][$j]['location_id'], 'class="button_small"').'<br>'.
						form_label('Time in: ', 'n,1,'.$i.','.$j).form_dropdown('n,1,'.$i.','.$j, $times, date('H:i', strtotime($result_final[$i+$clerk_no][$j]['rota_from'])), 'class="button_small"').'<br>'.
						form_label('Time out: ', 'n,2,'.$i.','.$j).form_dropdown('n,2,'.$i.','.$j, $times, date('H:i', strtotime($result_final[$i+$clerk_no][$j]['rota_to'])), 'class="button_small"').'<br>'.
						form_label('Status: ', 'n,3,'.$i.','.$j).form_dropdown('n,3,'.$i.','.$j, $status_lst, $result_final[$i+$clerk_no][$j]['status_id'], 'class="button_small"');
				}
			}

// Prepare data for the form

				$data = array(
					'name' => 'Admin',
					'message_display' => '', 
					'date_this' => $date_this,
					'date_next' => $date_next,
					'clerk_No' => $clerk_no,
					'this_data' => $this_data,
					'next_data' => $next_data,
					'location' => $location[0]['locations'],
				);	
		}
		else
		{
			$error = '';
			$data = array(
				'name' => 'Admin',
				'message_display' => "<h4 class='error_msg'>There aren't any assigned clerks for " . $location[0]['locations'] . " Rota!</h4>", 
			);
		}

// Load the view as a result and check if the user is logged in
			if ($this->login_database->is_logged())
           	{
           		$this->load->view('rota_view', $data);
           	}
		    else
		   	{
		    	$this->load->view('login_form', $data);
		    }
	}


public function home_user()
		{
// We are gathering the data for the insert
			$location = $this->session->userdata('logged_in');

			$messages = '';

			if ($this->input->post('calcvalues') != NULL)
			{
				$clerk = $this->input->post('calcvalues');

				$action = $this->input->post('action');

				$date = date('Y-m-d');

				$time = date('H:i:s');

				$clerk_id = $this->load_file->get_data_extend_where('clerk', 'clerk_id, clerk_name', 'secret_no =' . $clerk);

// Check if the clerk exists or not
				if ($clerk_id != NULL)
				{
					$temp = array(
						'locations' => $location['name'],
						);
					
					$location_id = $this->load_file->get_data_extend_where('locations', 'location_id', $temp);

// we get the time margins for the clock in and out of this particular clerk
					$temp = $this->load_file->get_clerks($clerk_id[0]['clerk_id']);

					$time_in_margin = $temp[0]['in_time_margin'];
					$time_out_margin = $temp[0]['out_time_margin'];

// We check if the database has any data for this clerk at this date
					$check = $this->load_file->get_data_extend_where('clerks_check', 'clerk_id, status, time', 'clerk_id =' . $clerk_id[0]['clerk_id'], $date);

// we build the array
					$data_for = array(
						'clerk_id' => $clerk_id[0]['clerk_id'],
						'location_id' => $location_id[0]['location_id'],
						'sign_time' => $time,
						'status' => $action,
						'date' => $date,
						'time' => $time,
						);

// set defaults for the check in case that they don't exist
						if ( !isset($check[0]) )
						{
							$check[0] = array(
								'clerk_id' => -1,
								'status' => 'not set',
								);
						}
						if ( !isset($check[1]) )
						{
							$check[1] = array(
								'clerk_id' => -1,
								'status' => 'not set',
								);
						}

					if ($data_for['status'] == 'out' && ($check[0]['status'] == 'out' || $check[0]['status'] == 'not set'))
					{
						$messages = '<h4 class="error_msg">You need to clock in first '. $clerk_id[0]['clerk_name'] .'!</h4>';
// with that condition we prevent someone to clock out first
					}
					else
					{
						if ($action == 'in')
						{
######################################################
##             clock IN condition                   ##
######################################################
							if ($check[0]['clerk_id'] == $clerk_id[0]['clerk_id'] && $check[0]['status'] == $action)
							{
								if ($check[0]['status'] == 'in' && $check[1]['status'] == 'out')
								{
									$messages = '<h4 class="error_msg">You have finished your shift for today '. $clerk_id[0]['clerk_name'] .'!</h4>';
								}
								else
								{
									$messages = '<h4 class="error_msg">You are already clocked in '. $clerk_id[0]['clerk_name'] .'!</h4>';
								}
							}
							else
							{
// check the rota first
								$cond = array(
									'clerk_id' => $clerk_id[0]['clerk_id'],
									);

								$cond3 = array(
									'status_id' => 1,
									);
// Get the rota from the database
								$time_check = $this->load_file->get_data_extend_where('clerks_list_rota', 'location_id, rota_from, status_id', $cond, $date);
// we check if it exists							
								if ($time_check != NULL)
								{
									// this is for testing later is gonna be from database
									$time_margin = '- ' . $time_in_margin . ' minutes';

// debug msg
echo 'the real time in is: ' . $data_for['sign_time'] . '<br>';

// we put the margin to the current time, and we move the time some minutes earlier
									$data_for['sign_time'] = date('H:i:s', strtotime($time_margin));

									$clock_in_time = time_to_sec($data_for['sign_time']);

									$rota_in_time = time_to_sec($time_check[0]['rota_from']);

// We check the times to see if we gonna add deductions
									if ( $clock_in_time <= $rota_in_time )
									{
##
## NOTE: You can use this in order to set early clock ins
##
										$messages = $messages . '<br><h4 class="norm_msg">Great! ' . $clerk_id[0]['clerk_name'] . ' are on time! ;) </h4><br>';
										$data_for['sign_time'] = '00:00:00';
									}
									else 
									{
										
// debug msg //
echo 'the calculated time in is: ' . $data_for['sign_time'] . '<br>';

// We calculate how many the deductions would be and we put them to the variable for the insert
										$deduction = time_filter(sec_to_time(abs($clock_in_time - $rota_in_time)));
										
										$data_for['sign_time'] = $deduction;

										$messages = $messages . '<br><h4 class="error_msg">You late for ' . $deduction . ' ' . $clerk_id[0]['clerk_name'] . '!</h4><br>';
									}

								}
								else
								{
									$messages = '<h4 class="error_msg">Rota for '. $clerk_id[0]['clerk_name'] .' not found!</h4><br>';
									$data_for['sign_time'] = '00:00:00';
								}
// insert to database
								$this->load_file->insert_data('clerks_check', $data_for);

								$data_temp = array(
											'clerk_id' => $clerk_id[0]['clerk_id'],
											'location_id' => $location_id[0]['location_id'],
											'sign_time' => '00:00:00',
											'status' => 'out',
											'date' => $date,
											'time' => '00:00:00',
										);
								$this->load_file->insert_data('clerks_check', $data_temp);

								$messages = $messages . '<h4 class="norm_msg">You clocked in Successfuly '. $clerk_id[0]['clerk_name'] .'!</h4>';
							}
						}
						else
						{
######################################################
##             clock OUT condition                  ##
######################################################
							if ($check[1]['clerk_id'] == $clerk_id[0]['clerk_id'] && $check[1]['status'] == $action && $check[1]['time'] != '00:00:00')
							{
								if ($check[0]['status'] == 'in' && $check[1]['status'] == 'out')
								{
									$messages = '<h4 class="error_msg">You have finished your shift for today '. $clerk_id[0]['clerk_name'] .'!</h4>';
								}
								else
								{
									$messages = '<h4 class="error_msg">You are already clocked out '. $clerk_id[0]['clerk_name'] .'!</h4>';
								}
							}
							else
							{
// check the rota first
								$cond = array(
									'clerk_id' => $clerk_id[0]['clerk_id'],
									);

								$cond3 = array(
									'status_id' => 1,
									);
// Get the rota from the database
								$time_check = $this->load_file->get_data_extend_where('clerks_list_rota', 'location_id, rota_to, status_id', $cond, $date);	
// we check if it exists
								if ($time_check != NULL)
								{
									// this is for testing later is gonna be from database
									$time_margin = '+ ' . $time_in_margin . ' minutes';
// debug msg
echo 'the real time in is: ' . $data_for['sign_time'] . '<br>';


// we put the margin to the current time, and we move the time some minutes earlier
									$data_for['sign_time'] = date('H:i:s', strtotime($time_margin));

									$clock_out_time = time_to_sec($data_for['sign_time']);

									$rota_out_time = time_to_sec($time_check[0]['rota_to']);



// debug msg //
echo 'the calculated time in is: ' . $data_for['sign_time'] . '<br>';
echo $clock_out_time . ' clock out time in sec <br>';
echo $rota_out_time . ' rota out time in sec <br>';



// We check the times to see if we gonna add overtime or not
									if ( $clock_out_time <= $rota_out_time )
									{
										$data_for['sign_time'] = '00:00:00';
##
## NOTE: You can use this in order to set early clock outs
##
										//$messages = $messages . '<br> Great! ' . $clerk_id[0]['clerk_name'] . ' are on time! ;) <br>';
									}
									else 
									{
										$overtime = time_filter(sec_to_time(abs($rota_out_time - $clock_out_time)));
										
										$data_for['sign_time'] = $overtime;

										$messages = $messages . '<br><h4 class="norm_msg">Your overtime is: ' . $overtime . ' ' . $clerk_id[0]['clerk_name'] . '!</h4><br>';
									}
								}
								else
								{
									$messages = '<h4 class="error_msg">Rota for '. $clerk_id[0]['clerk_name'] .' not found!</h4><br>';
									$data_for['sign_time'] = '00:00:00';
								}
// insert to database  
								$field = array(
										'clerk_id' => $clerk_id[0]['clerk_id'],
										'date' => $date,
										'status' => 'out',
									);

								$this->load_file->update_data('clerks_check', $data_for, $field);
								$messages = $messages . '<h4 class="norm_msg">You clocked out Successfuly '. $clerk_id[0]['clerk_name'] .'!</h4>';
							}
						}
					}
				}
				else
				{
// In case that the clerk doesn't exist
					$messages = "<h4 class='error_msg'>This Clerk doesn't exist!</h4>";
				}
			}

// Get the data for this week selected rota

// Get the week dates
			$week_start = date('z', strtotime('this week'));
			$week_end = date('z', strtotime('next week'));

			for ($i = $week_start; $i < $week_end; $i++)
			{
				$date_this[] = date('D d M', strtotime("January 1st +".($i)." days"));
			}

//query the database
			$this_week = $this->load_file->get_clerks_date('*', date('Y-m-d', strtotime($date_this[0])), date('Y-m-d', strtotime($date_this[6])), $location['id']);

// Prepare data for the form
			$data = array(
				'name' => $location['name'],
				'message_display' => $messages,
				'rota' => $this_week,
				'dates' => $date_this,
				'property' => '', 
				);

// Load the view as a result and check if the user is logged in
			if ($this->login_database->is_logged())
           	{
           		$this->load->view('user_page', $data);
           	}
		    else
		   	{
		    	$this->load->view('login_form', $data);
		    }
		}


public function clock_in_user()
		{
// Prepare data for the form
			$name = $this->session->userdata('logged_in');
			$data = array(
				'name' => $name['name'],
				'message_display' => '',
				'property' => 'in', 
				);

// Load the view as a result and check if the user is logged in
			if ($this->login_database->is_logged())
           	{
           		$this->load->view('keypad', $data);
           	}
		    else
		   	{
		    	$this->load->view('login_form', $data);
		    }
		}


public function clock_out_user()
		{
// Prepare data for the form
			$name = $this->session->userdata('logged_in');
			$data = array(
				'name' => $name['name'],
				'message_display' => '', 
				'property' => 'out',
				);

// Load the view as a result and check if the user is logged in
			if ($this->login_database->is_logged())
           	{
           		$this->load->view('keypad', $data);
           	}
		    else
		   	{
		    	$this->load->view('login_form', $data);
		    }
		}
	}
?>
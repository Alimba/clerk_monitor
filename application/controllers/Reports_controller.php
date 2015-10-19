<?php

	Class Reports_controller extends CI_Controller
	{
		public function __construct() 
		{
			parent::__construct();
//load helper libraries
			$this->load->helper('url');
			$this->load->helper('form');
			$this->load->helper('security');
			$this->load->library('form_validation');
			$this->load->library('table');
			$this->load->library('session');
			$this->load->model('load_file');
			$this->load->model('reports_model');
			$this->load->model('login_database');
		}

		public function reports()
		{
// Calculate the Holidays and update the database
			
			$clerks = $this->reports_model->get_clerks('clerk_id, start_date, end_date', 'clerk');
// we calculate the tax - holiday years
			$this_year = get_year(1);

			$last_year = get_year(0);

			foreach ($clerks as $clerk => $value) 
			{
				$clerk_status[$value['clerk_id']] = $this->reports_model->get_status('clerks_list_rota.date, clerk_status.status, clerks_list_rota.status_id, clerk.clerk_name, clerk.secret_no', $this_year['start'], $this_year['end'], $value['clerk_id'] );
				$clerk_status_last[$value['clerk_id']] = $this->reports_model->get_status('clerks_list_rota.date, clerk_status.status, clerks_list_rota.status_id, clerk.clerk_name, clerk.secret_no', $last_year['start'], $last_year['end'], $value['clerk_id'] );
			}

// we create 2 variables with the holiday calculations
			$holidays = holiday($clerks, $clerk_status, $this_year);

			$holidays_last = holiday($clerks, $clerk_status_last, $last_year);
		
// we calculate the sick days
			$sick_days = sick($clerk_status);

// we calculate the dayly overtimes
			$dayly_overtime = overtime($clerk_status);


// query the database to get the data for the form						
			$clerk = $this->load_file->get_data_extend('clerk', 'clerk_id, clerk_name');
			$companies = $this->load_file->get_data_extend('companies', 'company');

			$clerk_list[0] = 'Select Clerk';
			 
// prepare the tables for the form			
			foreach ($clerk as $row) 
			{
				 $clerk_list[$row['clerk_id']] = $row['clerk_name']; 		
			}

			foreach ($companies as $row) 
			{
				 $comp_list[] = $row['company'];
			}

//check validation for user input in sign up form
			$this->form_validation->set_rules('rep_type', 'Report Type', 'required|xss_clean');
			$this->form_validation->set_rules('date_from', 'Date From', 'trim|required|xss_clean');
			$this->form_validation->set_rules('date_to', 'Date To', 'trim|xss_clean');
			$this->form_validation->set_rules('comp', 'Company', 'trim|xss_clean');
			$this->form_validation->set_rules('clerk', 'Clerk', 'trim|xss_clean');

// We get the POST data 
			$post_data = array (
				'rep_type' => $this->input->post('rep_type'),
				'date_from' => $this->input->post('date_from'),
				'date_to' => $this->input->post('date_to'),
				'comp' => $this->input->post('comp'),
				'clerk' => $this->input->post('clerk')
				);	

// Do a check on the dates submited
			$date_from = date('z', strtotime($post_data['date_from']));

			$date_to = date('z', strtotime($post_data['date_to']));	

			if ($date_to == 0 && $date_from <= date('z'))
			{
				$post_data['date_to'] = date('Y-m-d');
				$error = FALSE;
				$msg = '';
			}
			elseif ($date_to < $date_from) 
			{
				$error = TRUE;
				$msg = '<h4 class="error_msg">You canot have earlier date than the starting date!</h4>';
			}
			else
			{
				$error = FALSE;
				$msg = '';
			}
			
// prepere the data to sent to the reports page
            $data = array(
				'name' => 'Admin',
				'message_display' => $msg,
				'clerk_list' => $clerk_list,
				'comp_list' => $comp_list,
			);

			if ($this->form_validation->run() == FALSE || $error == TRUE)
			{
// Load the view as a result and check if the user is logged in for resubmission
	           	if ($this->login_database->is_logged())
	           	{
	           		$this->load->view('reports_view', $data);
	           	}
			    else
			   	{
			    	$this->load->view('login_form', $data);
			    }
			}
			else
			{
// Validation successful and prepare the data for the result

				unset($clerk_list); // clear out this var, we need it later
				unset($clerks);
				if ( $post_data['comp'] != 0 )
				{
					$company = $post_data['comp'];
				}
				else 
				{
					$company = FALSE;
				}

				if ( $post_data['clerk'] != 0 )
				{
					$clerk = $post_data['clerk'];
				}
				else
				{
					$clerk = FALSE;
				}

// We query the database
				$clerk_data = $this->reports_model->get_weekly('clerks_check.clerk_id', $post_data['date_from'], $post_data['date_to'], $clerk, $company );

				if ($clerk_data != NULL)
				{
					foreach ($clerk_data as $key => $value) 
					{
						$clerks[] = $clerk_data[$key]['clerk_id'];
					}

					$clerks = array_unique($clerks);

					foreach ($clerks as $clerk => $value) 
					{
						$clerk_details[$value] = $this->reports_model->get_weekly('clerks_check.clerk_id, clerk.clerk_name, clerk.secret_no, clerks_check.status, clerks_check.date, clerks_check.time, clerks_check.sign_time, clerks_check.id, clerks_check.approval', $post_data['date_from'], $post_data['date_to'], $clerks[$clerk] );
					}

					switch ($post_data['rep_type']) 
					{
						case 0: // Weekly report page

							$clerk_list = overtime_deductions($clerk_details, FALSE);

	// prepere the data to sent to the reports page
				            $data = array(
								'name' => 'Admin',
								'message_display' => $msg,
								'clerk_list' => $clerk_list, //all the data in one
								'holidays' => $holidays,
								'holidays_last' => $holidays_last,
								'sick_days' => $sick_days,
								'dayly_overtime' => $dayly_overtime,
							);

	// Load the view as a result and check if the user is logged in
				           	if ($this->login_database->is_logged())
				           	{
				           		$this->load->view('weekly', $data);
				           	}
						    else
						   	{
						    	$this->load->view('login_form', $data);
						    }

							break;
						
						case 1: // Monthly Simple report page

							$clerk_list = overtime_deductions($clerk_details, TRUE);

	// prepere the data to sent to the reports page
				            $data = array(
								'name' => 'Admin',
								'message_display' => $msg,
								'clerk_list' => $clerk_list, //all the data in one
								'holidays' => $holidays,
								'holidays_last' => $holidays_last,
								'sick_days' => $sick_days,
								'dayly_overtime' => $dayly_overtime,
							);

	// Load the view as a result and check if the user is logged in
				           	if ($this->login_database->is_logged())
				           	{
				           		$this->load->view('monthly_simple', $data);
				           	}
						    else
						   	{
						    	$this->load->view('login_form', $data);
						    }
							break;

						case 2: // Monthly Analitic report page

							$clerk_list = overtime_deductions($clerk_details, TRUE);

	// prepere the data to sent to the reports page
				            $data = array(
								'name' => 'Admin',
								'message_display' => $msg,
								'clerk_list' => $clerk_list, //all the data in one
								'holidays' => $holidays,
								'holidays_last' => $holidays_last,
								'sick_days' => $sick_days,
								'dayly_overtime' => $dayly_overtime,
							);


	// Load the view as a result and check if the user is logged in
				           	if ($this->login_database->is_logged())
				           	{
				           		$this->load->view('monthly', $data);
				           	}
						    else
						   	{
						    	$this->load->view('login_form', $data);
						    }
							break;
						
						default:
							
							break;
					}
				}
				else
				{

// prepere the data to sent to the reports page
           	$data['message_display'] = '<h4 class="error_msg">No Data Available!</h4>';

	// Load the view as a result and check if the user is logged in				
					if ($this->login_database->is_logged())
		           	{
		           		$this->load->view('reports_view', $data);
		           	}
				    else
				   	{
				    	$this->load->view('login_form', $data);
				    }
				}
			}
		}

		public function approval()
		{
			$post_data = $this->input->post();	

			foreach ($post_data as $key => $value) 
			{
				$field[] = array(
					'id' => $key,
					); 

				if ($value == '')
				{
					$value = 0;
				}
				else
				{
					$value = 1;
				}

				$data[] = array(
					'approval' => $value,
					);
			}

			array_pop($field);
			array_pop($data);

			foreach ($data as $key => $value) 
			{
				$this->reports_model->update_data('clerks_check', $data[$key], $field[$key]);
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



	}
?>
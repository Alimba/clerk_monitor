<?php
	//session_start();

	Class User_Authentication extends CI_Controller 
	{
		public function __construct() 
		{
			parent::__construct();
//load helper libraries
			$this->load->helper('url');
			$this->load->helper('form');
			$this->load->helper('security');
			$this->load->library('form_validation');
			$this->load->library('session');
			$this->load->model('login_database');
			$this->load->model('load_file');
			$this->load->library('table');
		}
//show login page
		public function user_login_show()
		{
			$result = $this->load_file->get_data_extend('user_login', 'user_name');
				
			foreach ($result as $key => $value) 
			{
				$result_new[$value['user_name']] = $value['user_name'];
			}

			$data = array(
				'message_display' => '<h4 class="norm_msg">Welcome!</h4>',
				'users' => $result_new, 
				);

			$this->load->view('login_form', $data);
		}
//show registration page
		public function user_registration_show()
		{
			$this->load->view('registration_form');
		}
//Validate and store registration data in database
		public function new_user_registration()
		{
//check validation for user input in sign up form
			$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email_value', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|md5');
			
			if ($this->form_validation->run() == FALSE)
			{
				$this->load->view('registration_form');
			}
			else
			{
				$data = array (
					'name' => $this->input->post('name'),
					'user_name' => $this->input->post('username'),
					'user_email' => $this->input->post('email_value'),
					'user_password' => $this->input->post('password')
					);
				$result = $this->login_database->registration_insert($data);
				
				if ($result == TRUE)
				{
					$data['message_display'] = '<h4 class="norm_msg">Registration Successfully!</h4>';
					$this->load->view('login_form', $data);
				}
				else
				{
					$data['message_display'] = '<h4 class="error_msg">Already exist!</h4>';
					$this->load->view('registration_form', $data);
				}
			}
		}
// Check for user login progress
		public function user_login_process()
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|md5');

			if ($this->form_validation->run() == FALSE) 
			{
				$this->load->view('login_form');
			}
			else
			{
				$data = array(
					'username' => $this->input->post('username'),
					'password' => $this->input->post('password')
					);
				$result = $this->login_database->login($data);

				$salon_name = $this->login_database->get_data('user_login', 'name, id', $data['username']);

				if ($result == TRUE)
				{
					$sess_array = array(
						'username' => $this->input->post('username'),
						'name' => $salon_name[0]['name'],
						'id' => $salon_name[0]['id'],
						);
//add user data in the session
					$this->session->set_userdata('logged_in', $sess_array);
					$result = $this->login_database->read_user_information($sess_array);

					if ($result != FALSE)
					{
// query the database to get the data for the form	
						$clerks = $this->load_file->get_clerks();
						$locations = $this->load_file->get_data_extend('locations', 'locations');
						$roles = $this->load_file->get_data_extend('clerk_roles', 'role');
						$companies = $this->load_file->get_data_extend('companies', 'company');

// prepare the tables for the form			
						foreach ($roles as $row) 
						{
							 $role_list[] = $row['role'];
						}
						
						foreach ($locations as $row) 
						{
							 $loc_list[] = $row['locations'];
						}

						foreach ($companies as $row) 
						{
							 $comp_list[] = $row['company'];
						}

// Get the week dates
			$week_start = date('z', strtotime('this week'));
			$week_end = date('z', strtotime('next week'));

			for ($i = $week_start; $i < $week_end; $i++)
			{
				$date_this[] = date('D d M', strtotime("January 1st +".($i)." days"));
			}

//query the database
				$this_week = $this->load_file->get_clerks_date('*', date('Y-m-d', strtotime($date_this[0])), date('Y-m-d', strtotime($date_this[6])), $salon_name[0]['id']);

// prepere the data to sent to the admin page
						$data = array(
							'name' => $result[0]->name,
							'username' => $result[0]->user_name,
							'email' => $result[0]->user_email,
							'password' => $result[0]->user_password,
							'file' => $clerks,
							'locations' => $locations,
							'role_list' => $role_list,
							'loc_list' => $loc_list,
							'comp_list' => $comp_list,
							'rota' => $this_week,
							'dates' => $date_this,
							'message_display' => '<h4 class="norm_msg">Loged in Successfully!</h4>'
							);

						if ($data['name'] == 'Admin')
						{
							$this->load->view('admin_page', $data);
						}
						else
						{
							$this->load->view('user_page', $data);
						}
					}
				}
				else
				{
					$result = $this->load_file->get_data_extend('user_login', 'user_name');
				
					foreach ($result as $key => $value) 
					{
						$result_new[$value['user_name']] = $value['user_name'];
					}

					$data = array(
						'users' => $result_new, 
						'message_display' => '<h4 class="error_msg">Wrong Username or Password!</h4>',
						);
					
					$this->load->view('login_form', $data);
				}
			}
		}
		
// logout from admin page
		public function logout()
		{
//remove session data
			$sess_array = array(
				'username' => '' 
				);

			$this->session->unset_userdata('logged_in', $sess_array);
			
			$result = $this->load_file->get_data_extend('user_login', 'user_name');
				
			foreach ($result as $key => $value) 
			{
				$result_new[$value['user_name']] = $value['user_name'];
			}

			$data = array(
				'users' => $result_new, 
				'message_display' => '<h4 class="norm_msg">Successfully Logout!</h4>',
				);

			$this->load->view('login_form', $data);
		}
	}
?>

<?php

	Class Load_File extends CI_Model
	{
		public function __construct() 
		{
			parent::__construct();
			$this->load->database();
		}

		public function load_data_file($data)
		{
			foreach ($data as $lines => $line) 
			{				
				$this->db->insert('raw_data', $line);
			}
			
			if ($this->db->affected_rows() > 0)
				{
					return TRUE;
				}
			else
				{
					return FALSE;
				} 
		}

		public function insert_data($table, $data)
		{		
			
			$this->db->insert($table, $data);
			
			
			if ($this->db->affected_rows() > 0)
				{
					return TRUE;
				}
			else
				{
					return FALSE;
				} 
		}

		public function insert_batch_data($table, $data)
		{		
			$this->db->insert($table, $data);
			
			if ($this->db->affected_rows() > 0)
				{
					return TRUE;
				}
			else
				{
					return FALSE;
				} 
		}

		public function update_data($table, $data, $field)
		{ 	
		 	$this->db->where($field);

		 	$this->db->update($table, $data); 

		 	if ($this->db->affected_rows() > 0)
				{
					return TRUE;
				}
			else
				{
					return FALSE;
				} 
		}

		public function update_data_row($table, $data, $field)
		{ 	
			$this->db->where('id =', $field);

		 	$this->db->update($table, $data); 

		 	if ($this->db->affected_rows() > 0)
				{
					return TRUE;
				}
			else
				{
					return FALSE;
				} 
		}

		public function get_data($columns)
		{
			$this->db->select($columns);
			$this->db->from('raw_data');
			$this->db->where('secret_sign_on_number >', 0 );
			$this->db->order_by('creation_date', 'desc');

			$query = $this->db->get();

			return $query->result_array();
		}

		public function get_data_extend($table, $columns)
		{
			$this->db->select($columns);
			$this->db->from($table);
			
			$query = $this->db->get();

			return $query->result_array();
		}

		public function get_data_extend_where($table, $columns, $cond, $cond2 = FALSE, $cond3 = FALSE)
		{
			$this->db->select($columns);
			$this->db->from($table);
			$this->db->where($cond);
			if ($cond2 != FALSE)
			{
				$this->db->where('date =', $cond2);
			}

			if ($cond3 != FALSE)
			{
				$this->db->where($cond3);
			}
			$query = $this->db->get();

			return $query->result_array();
		}

		public function get_data_range_where($table, $columns, $cond1, $cond2, $till_reference)
		{
			$this->db->select($columns);
			$this->db->from($table);
			$this->db->where($till_reference);
			$this->db->where('date >=', $cond1);
			$this->db->where('date <=', $cond2);

			$this->db->group_by('till_reference');

			$query = $this->db->get();

			return $query->result_array();
		}

		public function get_clerks($clerk_id = false)
		{
			$this->db->select('clerk.clerk_id, clerk_roles.role_id, clerk_roles.in_time_margin, clerk_roles.out_time_margin, clerk.start_date, clerk.end_date, clerk.secret_no, clerk.clerk_name, locations.locations, clerk.location_id, clerk.company_id');
			$this->db->from('clerk');
			$this->db->join('locations', 'locations.location_id = clerk.location_id', 'inner');
			$this->db->join('clerk_roles', 'clerk.role_id = clerk_roles.role_id', 'inner');
			
			if ($clerk_id != FALSE)
			{
				$this->db->where('clerk.clerk_id =', $clerk_id);
			}

			$this->db->order_by('clerk.clerk_name', 'asc');

			$query = $this->db->get();

			return $query->result_array();
		}

		public function get_clerks_date($columns, $date_start, $date_end, $location, $clerk_id = false)
		{
			$this->db->select($columns);
			$this->db->from('clerks_list_rota');
			$this->db->join('clerk', 'clerks_list_rota.clerk_id = clerk.clerk_id', 'inner');
			$this->db->join('locations', 'locations.location_id = clerk.location_id', 'inner');
			$this->db->join('clerk_status', 'clerks_list_rota.status_id = clerk_status.status_id', 'inner');
			$this->db->where('clerk.location_id =', $location);
			if ($clerk_id != FALSE)
			{
				$this->db->where('clerks_list_rota.clerk_id =', $clerk_id);
			}
			$this->db->where('clerks_list_rota.date >=', $date_start);
			$this->db->where('clerks_list_rota.date <=', $date_end);

			$this->db->group_by('clerks_list_rota.id');	

			$query = $this->db->get();

			return $query->result_array();
		}

		public function get_clerk_no($salon)
		{
			$this->db->select('clerk.clerk_id');
			$this->db->from('clerk');
			$this->db->join('locations', 'locations.location_id = clerk.location_id', 'inner');
			$this->db->where('clerk.location_id =', $salon);
			
			$query = $this->db->get();

			return $query->result_array();
		}
	}

?>
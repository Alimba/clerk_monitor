<?php

	Class Reports_model extends CI_Model
	{
		public function __construct() 
		{
			parent::__construct();
			$this->load->database();
		}
/*
		public function data_first($data)
		{
			
			
			if ($this->db->affected_rows() > 0)
				{
					return TRUE;
				}
			else
				{
					return FALSE;
				} 
		}
*/

		public function get_weekly($columns, $date_start, $date_end, $clerk_id = false, $company = false)
		{
			$this->db->select($columns);
			$this->db->from('clerks_check');
			$this->db->join('clerk', 'clerks_check.clerk_id = clerk.clerk_id', 'inner');
			$this->db->join('companies', 'clerk.company_id = companies.company_id', 'inner');
			$this->db->join('locations', 'locations.location_id = clerks_check.location_id', 'inner');	
			$this->db->where('clerks_check.date >=', $date_start);
			$this->db->where('clerks_check.date <=', $date_end);

			if ($clerk_id != FALSE)
			{
				$this->db->where('clerks_check.clerk_id =', $clerk_id);
			}

			if ($company != FALSE)
			{
				$this->db->where('clerk.company_id =', $company);
			}

			$query = $this->db->get();

			return $query->result_array();
		}

		public function get_status($columns, $date_start, $date_end, $clerk_id = false)
		{
			$this->db->select($columns);
			$this->db->from('clerks_list_rota');
			$this->db->join('clerk', 'clerks_list_rota.clerk_id = clerk.clerk_id', 'inner');
			$this->db->join('clerk_status', 'clerk_status.status_id = clerks_list_rota.status_id', 'inner');	
			$this->db->where('clerks_list_rota.date >=', $date_start);
			$this->db->where('clerks_list_rota.date <=', $date_end);
			$this->db->order_by('date', 'ASC');

			if ($clerk_id != FALSE)
			{
				$this->db->where('clerks_list_rota.clerk_id =', $clerk_id);
			}

			$query = $this->db->get();

			return $query->result_array();			

		}
	
		public function get_clerks($columns, $table)
		{
			$this->db->select($columns);
			$this->db->from($table);

			$query = $this->db->get();

			return $query->result_array();	
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

	}	
?>
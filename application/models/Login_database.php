<?php

Class Login_Database extends CI_Model 
{
	public function __construct() 
		{
			parent::__construct();
			$this->load->database();
		}
//Insert registration data in database
	public function registration_insert($data)
	{
//Query to check whether username exist or not
		//$condition = "'user_name', $data['user_name']";
		$this->db->select('*');
		$this->db->from('user_login');
		$this->db->where('user_name', $data['user_name']);
		$this->db->limit(1);
		$query = $this->db->get();

		if ($query->num_rows() == 0)
		{
//Query to insert data in database
			$this->db->insert('user_login', $data);

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

//read data using username and password
	public function login($data)
	{
		$condition = array('user_name' => $data['username'], 'user_password' => $data['password']);
		$this->db->select('*');
		$this->db->from('user_login');
		$this->db->where($condition);
		$this->db->limit(1);
		$query = $this->db->get();

		if ($query->num_rows() == 1)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function is_logged()
	{
		if ($this->session->userdata('logged_in') == TRUE)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}


//read data from database to show data in admin page
	public function read_user_information($sess_array)
	{
		$condition = array('user_name' => $sess_array['username']);
		$this->db->select('*');
		$this->db->from('user_login');
		$this->db->where($condition);
		$this->db->limit(1);
		$query = $this->db->get();

		if ($query->num_rows() == 1)
		{
			return $query->result();
		}
		else
		{
			return FALSE;
		}
	}

	public function get_data($table, $columns, $cond)
		{
			$this->db->select($columns);
			$this->db->from($table);
			$this->db->where('user_name =', $cond);
			
			$query = $this->db->get();

			return $query->result_array();
		}
}

?>
<?php

	function time_to_sec($time) 
	{
// This function gets the time and returns the time in seconds

		list($h, $m, $s) = explode(':', $time); 
    								
    	$time = ($h * 3600) + ($m * 60) + $s; 

    	return $time; 
	}


	function sec_to_time($seconds)
	{
		$h = floor($seconds / 3600); 
    	$m = floor(($seconds % 3600) / 60); 
    	$s = $seconds - ($h * 3600) - ($m * 60);
    					
    	return sprintf('%02d:%02d:%02d', $h, $m, $s);
	}


	function time_filter($time)
	{
		list($h, $m, $s) = explode(':', $time);

		if ($m >= 30)
    	{
    		$m = 0;
    		$h += 1;
    	}
    	elseif ($m < 30)
    	{
    		$m = 30;
    	}
    	else
    	{
    		$m = 0;
    	}

    	$s = 0;
    	
    	return sprintf('%02d:%02d:%02d', $h, $m, $s);
	}


	function holiday($clerks, $clerk_status, $date)
	{	
		foreach ($clerk_status as $id => $array) 
		{
			$temp = 0;
			$dates_ocured = array();

			if (!isset($array[0]))
			{
				$holiday[$id] = array(
					'total_holiday_to_take' => '-',
					'holidays_left' => '-',
					'holidays_took' => '-',
					'holi_dates_ocured' => '-',
				);
			}
			else
			{	
				foreach ($array as $key => $value) 
				{
					if ($value['status_id'] == 4)
					{
						$temp = $temp + 1;
						$dates_ocured[] = date('d-m-Y', strtotime($value['date']));
					}
				}

				foreach ($clerks as $clerk => $entry) 
				{
					if ($entry['clerk_id'] == $id)
					{
						if ($entry['end_date'] == '0000-00-00')
						{
							$end_date = date('Y-m-d');
						}
						else
						{
							$end_date = $entry['end_date'];
						}

						if (strtotime($date['start']) > strtotime($entry['start_date']))
						{
							$start_date = $date['start'];
						}
						else
						{
							$start_date = $entry['start_date'];	
						}

						$date_difference = date_difference($start_date, $end_date);

						$hol_per_day = 28 / 365;

						$total_holiday_to_take = round(($date_difference * $hol_per_day), 0, PHP_ROUND_HALF_UP); 
					}
				}

				$holidays_left = 28 - $temp;

				$holiday[$id] = array(
					'total_holiday_to_take' => $total_holiday_to_take,
					'holidays_left' => $holidays_left,
					'holidays_took' => $temp,
					'holi_dates_ocured' => $dates_ocured,
				);
			
			unset($temp); 
			}
		}
		if (isset($holiday))
		{
			return $holiday;
		}
		else
		{
			return FALSE;
		}	
	}


	function sick($clerk_status)
	{
		foreach ($clerk_status as $id => $array) 
		{
			$temp = 0;
			$dates_ocured = array();
			$sick_pay = 0;
			$sick_count = 0;
			
			if (!isset($array[0]))
			{
				$sick[$id] = array(
						'sick_days' => '-',
						'sick_pay' => '-',
						'sick_dates_ocured' => '-',
					);
			}
			else
			{	
				foreach ($array as $key => $value) 
				{				
					if ($value['status_id'] == 3)
					{
						$temp = $temp + 1;
						$dates_ocured[] = $value['date'];
					}
				}

				if ($dates_ocured != NULL)
				{
					$current_date =	$dates_ocured[1];

					foreach ($dates_ocured as $key2 => $sick_date) 
					{
						$diff = date_difference($dates_ocured[$key2], $current_date);		

						if ( $diff == 1 )
						{
							$sick_count += 1;							
							
							if ($sick_count >= 3)
							{
								$sick_pay += 1;
							}
						}
						else
						{
							$sick_count = 0;
						}
						
						if (isset($dates_ocured[$key2 + 2]))
						{
							$current_date = $dates_ocured[$key2 + 2];
						}
					}
				}

				$sick[$id] = array(
						'sick_days' => $temp,
						'sick_pay' => $sick_pay,
						'sick_dates_ocured' => $dates_ocured,
					);

				unset($temp); 
				unset($sick_pay);
				unset($sick_count);
			}
		}

		if (isset($sick))
		{
			return $sick;
		}
		else
		{
			return FALSE;
		}	
	}


function overtime($clerk_status)
	{
		foreach ($clerk_status as $id => $array) 
		{	
			$dates_ocured = array();
			$temp = 0;
			$over = 0;
			$count = 0;

			if (!isset($array[0]))
			{
				$overtime[$id] = array(
						'overtime' => '-',
						'over_dates_ocured' => '-',
					);
			}
			else
			{	
				foreach ($array as $key => $value) 
				{	
					if ($value['status_id'] == 1)
					{
						if ($count >= 5 && $count <= 6)
						{
							$over += 1;
							$dates_ocured[] = $value['date'];	
						}

						$count += 1;
					}
					
					if ($temp == 6)
					{
						$temp = 0;
						$count = 0;
					}		
					else
					{
						$temp = $temp + 1;
					}
				}
			
				$overtime[$id] = array(
						'overtime' => $over,
						'over_dates_ocured' => $dates_ocured,
					);
				unset($over);
				unset($temp);
				unset($count);
				unset($dates_ocured);
			}
		}

		if (isset($overtime))
		{
			return $overtime;
		}
		else
		{
			return FALSE;
		}	
	}


	function get_year($year_index)
	{
		if ($year_index == 0)
		{ // last year
			$start = date('Y', strtotime('-1 years')) . '-04-01';
			$end = date('Y') . '-03-31';
		}
		else
		{ // this year
			$start = date('Y') . '-04-01';
			$end = date('Y', strtotime('1 years')) . '-03-31';
		}

		$year = array(
				'start' => $start, 
				'end' => $end,
			);

		return $year;
	}


	function date_difference($start_date, $end_date)
	{
		$start_date = date_create($start_date);
		
		$end_date = date_create($end_date);
		
		$diff=date_diff($start_date, $end_date);

		return $diff->format('%a');
	}

	function overtime_deductions($clerk_details, $extra)
	{
		$total_deductions = 0;

		$total_overtime = 0;

		// we calculate the overtime and the overal late times 
				foreach ($clerk_details as $id => $entry) 
				{	
					foreach ($entry as $key => $value) 
					{
						if ($id == $entry[$key]['clerk_id'] && $entry[$key]['status'] == 'in')
						{
// Late Deductions	
							if ($extra == TRUE)
							{
								$deductions[] = array(
										'date' => '',
										'time' => '',
										'real_time' => '',
									);

								if ($entry[$key]['approval'] == 1)
								{
									$deductions[] = array(
										'date' => $entry[$key]['date'],
										'time' => $entry[$key]['sign_time'],
										'real_time' => $entry[$key]['time'],
									);
								
								$total_deductions = $total_deductions + time_to_sec($entry[$key]['sign_time']);	
								}
							}
							else
							{
								$deductions[] = array(
										'id' => $entry[$key]['id'],
										'approval' => $entry[$key]['approval'],
										'date' => $entry[$key]['date'],
										'time' => $entry[$key]['sign_time'],
										'real_time' => $entry[$key]['time'],
									);
								
								$total_deductions = $total_deductions + time_to_sec($entry[$key]['sign_time']);		
							}
								
						}
						else
						{
// Overtime				
							if ($extra == TRUE)
							{
								$overtime[] = array(
										'date' => '',
										'time' => '',
										'real_time' => '',
									);

								if ($entry[$key]['approval'] == 1)
								{
									$overtime[] = array(
										'date' => $entry[$key]['date'],
										'time' => $entry[$key]['sign_time'],
										'real_time' => $entry[$key]['time'],
									);
								
									$total_overtime = $total_overtime + time_to_sec($entry[$key]['sign_time']);
								}
							}
							else
							{
								$overtime[] = array(
										'id' => $entry[$key]['id'],
										'approval' => $entry[$key]['approval'],
										'date' => $entry[$key]['date'],
										'time' => $entry[$key]['sign_time'],
										'real_time' => $entry[$key]['time'],
									);
								
								$total_overtime = $total_overtime + time_to_sec($entry[$key]['sign_time']);		
							}
							

							$clerk_list[$id] = array(
									'clerk_name' => $entry[$key]['clerk_name'],
									'secret_no' => $entry[$key]['secret_no'],
									'deductions' => $deductions,
									'total_deductions' => $total_deductions,
									'overtime' => $overtime, 
									'total_overtime' => $total_overtime,
								);
						}
					}

					unset($deductions);
					$total_deductions = 0;
					
					unset($overtime);
					$total_overtime = 0;
				}

		return $clerk_list;
	}















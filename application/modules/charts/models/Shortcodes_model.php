<?php
defined('BASEPATH') or exit('No direct script access allowed!');

/**
* 
*/
class Shortcodes_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}


	function request_trends($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($county==null || $county=='null') {
			$county = 0;
		}
		$sql = "CALL `proc_get_vl_shortcodes_requests`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['requests'][0]['name'] = lang('label.requests');

		$count = 0;
		
		$data["requests"][0]["data"][0]	= $count;
		$data['categories'][0]			= lang('label.no_data');
		foreach ($result as $key => $value) {
			$data['categories'][$key] 			= $value['monthname'].", ".$value['year'];
			$data["requests"][0]["data"][$key]	=  (int) $value['count'];
			
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}


	function facilities_requesting($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($county == NULL || $county == 'NULL') {
			$county = 0;
			$sql = "CALL `proc_get_vl_sites_shortcodes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_vl_sites_shortcodes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['requests'][0]['name'] = lang('label.requests');

		$count = 0;
		
		$data["requests"][0]["data"][0]	= $count;
		$data['categories'][0]					= 'No Data';
		$count = 0;
		foreach ($result as $key => $value) {
			if ($count<50) {
				$data['categories'][$key] 			= $value['name'];
				$data["requests"][0]["data"][$key]	=  (int) $value['count'];
			}
			$count++;
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function county_listings($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=null)
	{
		$li = '';
		$table = '';
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		$sql = "CALL `proc_get_vl_county_shortcodes`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($result);die();
		$count = 1;
		$listed = FALSE;

		if($result)
		{
			foreach ($result as $key => $value)
			{
				if ($count<16) {
					$li .= '<a href="javascript:void(0);" class="list-group-item" ><strong>'.$count.'.</strong>&nbsp;'.$value['name'].':&nbsp;'.number_format($value['counts']).'</a>';
				}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.number_format($value['counts']).'</td>';
					$table .= '</tr>';
					$count++;
			}
		}else{
			$li = 'No Data';
		}
		$data = array(
					'ul' => $li,
					'table' => $table,
					'requests' => true);
		return $data;
	}

	function subcounty_listings($year=null,$month=null,$county=null,$to_year=NULL,$to_month=null)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}
		if ($county==null || $county=='null') {
			$county = 0;
		}
		$sql = "CALL `proc_get_vl_subcounty_shortcodes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$li = '';
		$table = '';
		$count = 1;
		if($result)
			{
				foreach ($result as $key => $value) {
					if ($count<16) {
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].'.&nbsp;'.number_format($value['counts']).'</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.number_format($value['counts']).'</td>';
					$table .= '</tr>';
					$count++;
				}
			}else{
				$li = lang('label.no_data');
			}

		$data = array(
					'ul' => $li,
					'table' => $table,
					'requests' => true);
		return $data;
	}

	function partners($year=null,$month=null,$county=null,$to_year=NULL,$to_month=null)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}
		if ($county==null || $county=='null') {
			$county = 0;
		}

		$sql = "CALL `proc_get_vl_partner_shortcodes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$li = '';
		$table = '';
		$count = 1;
		if($result)
			{
				foreach ($result as $key => $value) {
					if ($count<16) {
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].'.&nbsp;'.number_format($value['counts']).'</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.number_format($value['counts']).'</td>';
					$table .= '</tr>';
				$count++;
				}
			}else{
				$li = lang('label.no_data');
			}

		$data = array(
					'ul' => $li,
					'table' => $table,
					'requests' => true);
		return $data;
	}

	function facility_listing($year=null,$month=null,$county=NULL,$to_year=NULL,$to_month=null)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		
		if ($county==null || $county=='null') {
			$county = 0;
		}
		$sql = "CALL `proc_get_vl_sites_shortcodes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$li = '';
		$table = '';
		$count = 1;
		if($result)
			{
				
				foreach ($result as $key => $value) {
					if ($count<16) {
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].'.&nbsp;'.number_format($value['count']).'</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.number_format($value['count']).'</td>';
					$table .= '</tr>';
					$count++;
				}
			}else{
				$li = lang('label.no_data');
			}
			$data = array(
						'ul' => $li,
						'table' => $table,
					'requests' => true);
		return $data;
	}
}
?>
<?php
defined("BASEPATH") or exit("No direct script access allowed!");

/**
* 
*/
class County_model extends MY_Model
{
	
	function __construct()
	{
		parent::__construct();
	}

	function subcounty_outcomes($year=NULL,$month=NULL,$county=NULL,$to_year=null,$to_month=null)
	{
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}


		$sql = "CALL `proc_get_vl_county_subcounty_outcomes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$data['county_outcomes'][0]['name'] = lang('label.not_suppressed_');
		$data['county_outcomes'][1]['name'] =  lang('label.suppressed_');

		$count = 0;
		
		$data["county_outcomes"][0]["data"][0]	= $count;
		$data["county_outcomes"][1]["data"][0]	= $count;
		$data['categories'][0]					= lang('label.no_data');

		$data['outcomes'][0]['name'] =  lang('label.not_suppressed_');
		$data['outcomes'][1]['name'] =  lang('label.suppressed_');
		$data['outcomes'][2]['name'] = lang('label.suppression');

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "spline";
		

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

		$data['title'] = "";


		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["county_outcomes"][0]["data"][$key]	=  (int) $value['nonsuppressed'];
			$data["county_outcomes"][1]["data"][$key]	=  (int) $value['suppressed'];
			
			$data['outcomes'][0]['data'][$key] = (int) $value['nonsuppressed'];
			$data['outcomes'][1]['data'][$key] = (int) $value['suppressed'];
			$data['outcomes'][2]['data'][$key] = round(@(((int) $value['suppressed']*100)/((int) $value['suppressed']+(int) $value['nonsuppressed'])),1);
		}



		// echo "<pre>";print_r($data);die();
		return $data;

	}

	function county_table($year=NULL,$month=NULL,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		$sql = "CALL `proc_get_vl_county_details`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($sql);die();
		foreach ($result as $key => $value) {
			$routine = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
			$routinesus = ((int) $value['less5000'] + (int) $value['above5000']);
			$table .= "<tr>
						<td>".($key+1)."</td>
						<td>".$value['county']."</td>
						<td>".number_format((int) $value['sitesending'])."</td>
						<td>".number_format((int) $value['received'])."</td>
						<td>".number_format((int) $value['rejected']) . " (" . 
							@round((($value['rejected']*100)/$value['received']), 1, PHP_ROUND_HALF_UP)."%)</td>
						<td>".number_format((int) $value['alltests'])."</td>
						<td>".number_format((int) $value['invalids'])."</td>

						<td>".number_format($routine)."</td>
						<td>".number_format($routinesus)."</td>
						<td>".number_format((int) $value['baseline'])."</td>
						<td>".number_format((int) $value['baselinesustxfail'])."</td>
						<td>".number_format((int) $value['confirmtx'])."</td>
						<td>".number_format((int) $value['confirm2vl'])."</td>
						<td>".number_format((int) $routine + (int) $value['baseline'] + (int) $value['confirmtx'])."</td>
						<td>".number_format((int) $routinesus + (int) $value['baselinesustxfail'] + (int) $value['confirm2vl'])."</td>
						
					</tr>";
			$count++;
		}
		

		return $table;
	}

	function download_county_table($year=NULL,$month=NULL,$to_year=null,$to_month=null)
	{
		
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}


		$sql = "CALL `proc_get_vl_county_details`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($sql);die();

		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array(lang('label.table_county'), lang('label.table_facilities_send_samp'), 
                lang('label.received'), lang('label.rejected'), lang('label.all_tests'),
                lang('label.redraws'), lang('label.undetected'), lang('label.less1000'), 
                lang('label.above1000_less5000'), lang('label.above5000'),lang('label.baseline_tests'),
                lang('label.baseline_gt1000'), lang('label.confirmatory_tests'), lang('label.confirmatory_gt1000'));

	    fputcsv($f, $b, $delimiter);

	    foreach ($result as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('Ymd H:i:s').'vl_counties_summary.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
	}

	function county_subcounties($year=NULL,$month=NULL,$county=NULL,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}


		$sql = "CALL `proc_get_vl_subcounty_details`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($sql);die();
		foreach ($result as $key => $value) {
			$routine = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
			$routinesus = ((int) $value['less5000'] + (int) $value['above5000']);
			$table .= "<tr>
						<td>".($key+1)."</td>
						<td>".$value['subcounty']."</td>
						<td>".number_format((int) $value['sitesending'])."</td>
						<td>".number_format((int) $value['received'])."</td>
						<td>".number_format((int) $value['rejected']) . " (" . 
							round((($value['rejected']*100)/$value['received']), 1, PHP_ROUND_HALF_UP)."%)</td>
						<td>".number_format((int) $value['alltests'])."</td>
						<td>".number_format((int) $value['invalids'])."</td>

						<td>".number_format($routine)."</td>
						<td>".number_format($routinesus)."</td>
						<td>".number_format((int) $value['baseline'])."</td>
						<td>".number_format((int) $value['baselinesustxfail'])."</td>
						<td>".number_format((int) $value['confirmtx'])."</td>
						<td>".number_format((int) $value['confirm2vl'])."</td>
						<td>".number_format((int) $routine + (int) $value['baseline'] + (int) $value['confirmtx'])."</td>
						<td>".number_format((int) $routinesus + (int) $value['baselinesustxfail'] + (int) $value['confirm2vl'])."</td>
						
					</tr>";
			$count++;
		}
		
		return $table;
	}

	function download_subcounty_table($year=NULL,$month=NULL,$county=NULL,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}


		$sql = "CALL `proc_get_vl_subcounty_details`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array(lang('label.table_county'), lang('label.subcounty'), lang('label.table_facilities_send_samp'),
                 lang('label.received'), lang('label.rejected'), lang('label.all_tests'),
                lang('label.redraws'), lang('label.undetected'), lang('label.less1000'),
                lang('label.above1000_less5000'), lang('label.above5000'),
                lang('label.baseline_tests'),
                lang('label.baseline_gt1000'), lang('label.confirmatory_tests'), lang('label.confirmatory_gt1000'));

	    fputcsv($f, $b, $delimiter);

	    foreach ($result as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('Ymd H:i:s').'vl_counties_subcounties.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
		
	}

	function county_partners($year=NULL,$month=NULL,$county=NULL,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}


		$sql = "CALL `proc_get_vl_county_partners`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($sql);die();
		foreach ($result as $key => $value) {
			if ($value['partner'] == NULL || $value['partner'] == 'NULL') {
				$value['partner'] = lang('label.no_partner');
			}
			$routine = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
			$routinesus = ((int) $value['less5000'] + (int) $value['above5000']);
			$table .= "<tr>
						<td>".($key+1)."</td>
						<td>".$value['partner']."</td>
						<td>".number_format((int) $value['received'])."</td>
						<td>".number_format((int) $value['rejected']) . " (" . 
							round((($value['rejected']*100)/$value['received']), 1, PHP_ROUND_HALF_UP)."%)</td>
						<td>".number_format((int) $value['alltests'])."</td>
						<td>".number_format((int) $value['invalids'])."</td>

						<td>".number_format($routine)."</td>
						<td>".number_format($routinesus)."</td>
						<td>".number_format((int) $value['baseline'])."</td>
						<td>".number_format((int) $value['baselinesustxfail'])."</td>
						<td>".number_format((int) $value['confirmtx'])."</td>
						<td>".number_format((int) $value['confirm2vl'])."</td>
						<td>".number_format((int) $routine + (int) $value['baseline'] + (int) $value['confirmtx'])."</td>
						<td>".number_format((int) $routinesus + (int) $value['baselinesustxfail'] + (int) $value['confirm2vl'])."</td>
						
					</tr>";
			$count++;
		}
		
		return $table;
	}

	function download_partners_county_table($year=NULL,$month=NULL,$county=NULL,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}


		$sql = "CALL `proc_get_vl_county_partners`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array(lang('label.table_county'), lang('label.partner'), lang('label.table_facilities_send_samp'),
                lang('label.received'), lang('label.rejected'), lang('label.all_tests'),
                 lang('label.redraws'), lang('label.undetected'), lang('label.less1000'),
                lang('label.above1000_less5000'), lang('label.above5000'), lang('label.baseline_tests'), 
                 lang('label.baseline_gt1000'), lang('label.confirmatory_tests'), lang('label.confirmatory_gt1000'));

	    fputcsv($f, $b, $delimiter);

	    foreach ($result as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('Ymd H:i:s').'vl_county_partners.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
		
	}

}
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
// echo "<pre>";print_r($result);echo "</pre>";
		$data['county_outcomes'][0]['name'] = lang('label.not_suppressed_');
		$data['county_outcomes'][1]['name'] =  lang('label.suppressed_');

		$count = 0;
		
		$data["county_outcomes"][0]["data"][0]	= $count;
		$data["county_outcomes"][1]["data"][0]	= $count;
		$data['categories'][0]					= lang('label.no_data');

		$data['outcomes'][0]['name'] =  lang('label.gt1000');
		$data['outcomes'][1]['name'] =  lang('label.lt1000');
		$data['outcomes'][2]['name'] =  lang('label.undetectable');
		$data['outcomes'][3]['name'] =  lang('label.invalids');
		$data['outcomes2'][0]['name'] =  lang('label.not_suppressed_');
		$data['outcomes2'][1]['name'] =  lang('label.suppressed_');
		$data['outcomes2'][2]['name'] = lang('label.suppression');

		$data['outcomes'][0]['type'] = "column";
		$data['outcomes'][1]['type'] = "column";
		$data['outcomes'][2]['type'] = "column";
		$data['outcomes'][3]['type'] = "column";
		$data['outcomes2'][0]['type'] = "column";
		$data['outcomes2'][1]['type'] = "column";
		$data['outcomes2'][2]['type'] = "spline";
		

		$data['outcomes'][0]['yAxis'] = 1;
		$data['outcomes'][1]['yAxis'] = 1;
		$data['outcomes'][2]['yAxis'] = 1;
		$data['outcomes'][3]['yAxis'] = 1;
		$data['outcomes2'][0]['yAxis'] = 1;
		$data['outcomes2'][1]['yAxis'] = 1;

		$data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes'][3]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes2'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes2'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['outcomes2'][2]['tooltip'] = array("valueSuffix" => ' %');

		$data['title'] = "";


		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["county_outcomes"][0]["data"][$key]	=  (int) $value['nonsuppressed'];
			$data["county_outcomes"][1]["data"][$key]	=  (int) $value['suppressed'];
			
			$data['outcomes'][0]['data'][$key] = (int) $value['all_nonsuppressed'];
			$data['outcomes'][1]['data'][$key] = (int) $value['all_less1000'];
			$data['outcomes'][2]['data'][$key] = (int) $value['all_undetected'];
			$data['outcomes'][3]['data'][$key] = (int) $value['all_invalids'];
			$data['outcomes2'][0]['data'][$key] = (int) $value['nonsuppressed'];
			$data['outcomes2'][1]['data'][$key] = (int) $value['suppressed'];
			$data['outcomes2'][2]['data'][$key] = round(@(((int) $value['suppressed']*100)/((int) $value['suppressed']+(int) $value['nonsuppressed'])),1);
		}
		 //echo "<pre>";print_r($data);die();
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
		foreach ($result as $key => $value) {
			$vl = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
			$vl_und = ((int) $value['undetected']);
			$suppressed = ((int) $value['undetected'] + (int) $value['less1000'] );
			$non_suppressed = ((int) $value['less5000'] + (int) $value['above5000']);
			$all_non_suppressed = ((int) $value['all_less5000'] + (int) $value['all_above5000']);
			$table .= "<tr>
						<td>".($key+1)."</td>
						<td>".$value['county']."</td>";
						$table .="<td>".number_format((int) $value['all_tests'])."</td>";
						$table .="<td>".number_format((int) $value['all_invalids'])."</td>";
						$table .="<td>".number_format((int) $value['all_undetected'])."</td>";
						$table .="<td>".number_format((int) $value['all_less1000'])."</td>";
						$table .="<td>".number_format((int) $all_non_suppressed)."</td>";
						$table .="<td>".number_format($vl)."</td>";
						$table .="<td>".number_format($vl_und)."</td>";
						$table .="<td>".number_format((int) $value['less1000'])."</td>";
						//$table .="<td>".number_format($suppressed)."</td>";	
						$table .="<td>".number_format($non_suppressed)."</td>";	
					$table .="</tr>";
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
			$vl = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
			$vl_und = ((int) $value['undetected']);
			$suppressed = ((int) $value['undetected'] + (int) $value['less1000'] );
			$non_suppressed = ((int) $value['less5000'] + (int) $value['above5000']);
			$all_non_suppressed = ((int) $value['all_less5000'] + (int) $value['all_above5000']);
			$table .= "<tr>
						<td>".($key+1)."</td>";
						$table .= "<td>".$value['subcounty']."</td>";
						$table .="<td>".number_format((int) $value['all_tests'])."</td>";
						$table .="<td>".number_format((int) $value['all_invalids'])."</td>";
						$table .="<td>".number_format((int) $value['all_undetected'])."</td>";
						$table .="<td>".number_format((int) $value['all_less1000'])."</td>";
						$table .="<td>".number_format((int) $all_non_suppressed)."</td>";
						$table .="<td>".number_format($vl)."</td>";
						$table .="<td>".number_format($vl_und)."</td>";
						$table .="<td>".number_format((int) $value['less1000'])."</td>";
						//$table .="<td>".number_format($suppressed)."</td>";	
						$table .="<td>".number_format($non_suppressed)."</td>";		
						
					$table .="</tr>";
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
			$vl = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
			$vl_und = ((int) $value['undetected']);
			$suppressed = ((int) $value['undetected'] + (int) $value['less1000'] );
			$non_suppressed = ((int) $value['less5000'] + (int) $value['above5000']);
			$all_non_suppressed = ((int) $value['all_less5000'] + (int) $value['all_above5000']);
			$table .= "<tr>
						<td>".($key+1)."</td>";
						$table .= "<td>".$value['partner']."</td>";
						$table .="<td>".number_format((int) $value['all_tests'])."</td>";
						$table .="<td>".number_format((int) $value['all_invalids'])."</td>";
						$table .="<td>".number_format((int) $value['all_undetected'])."</td>";
						$table .="<td>".number_format((int) $value['all_less1000'])."</td>";
						$table .="<td>".number_format((int) $all_non_suppressed)."</td>";
						$table .="<td>".number_format($vl)."</td>";
						$table .="<td>".number_format($vl_und)."</td>";
						$table .="<td>".number_format((int) $value['less1000'])."</td>";
						//$table .="<td>".number_format($suppressed)."</td>";	
						$table .="<td>".number_format($non_suppressed)."</td>";
						
					$table .="</tr>";
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
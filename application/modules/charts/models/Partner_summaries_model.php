<?php
defined('BASEPATH') or exit('No direct script access allowed!');

/**
* 
*/
class Partner_summaries_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}


	function partner_counties_outcomes($year=NULL,$month=NULL,$partner=NULL,$to_year=null,$to_month=null)
	{

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
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}

		$sql = "CALL `proc_get_vl_partner_county_details`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		// echo "<pre>";print_r($result);die();

		$data['outcomes'][0]['name'] =  lang('label.not_suppressed_');
		$data['outcomes'][1]['name'] =  lang('label.suppressed_');
		$data['outcomes'][2]['name'] =lang('label.suppression');

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
			$suppressed = (int)$value['undetected']+(int)$value['less1000'];
			$nonsuppressed = (int)$value['less5000']+(int)$value['above5000'];
			$data['categories'][$key] 					= $value['county'];
			$data['outcomes'][0]['data'][$key] = (int) $nonsuppressed;
			$data['outcomes'][1]['data'][$key] = (int) $suppressed;
			$data['outcomes'][2]['data'][$key] = round(@(((int) $suppressed*100)/((int) $suppressed+(int) $nonsuppressed)),1);
		}
		// echo "<pre>";print_r($data);die();
		return $data;

	}

	function partner_counties_table($year=NULL,$month=NULL,$partner=NULL,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
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
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}

		$sql = "CALL `proc_get_vl_partner_county_details`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($sql);die();
		foreach ($result as $key => $value) {
			$routine = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
			$routinesus = ((int) $value['less5000'] + (int) $value['above5000']);
			$table .= "<tr>
				<td>".$count."</td>";
				$table .= "<td>".$value['county']."</td>";
				$table .= "<td>".number_format((int) $value['facilities'])."</td>";
				$table .= "<td>".number_format((int) $value['received'])."</td>";
				//$table .= "<td>".number_format((int) $value['rejected']) . " (" . 
				//	round((($value['rejected']*100)/$value['received']), 1, PHP_ROUND_HALF_UP)."%)</td>";
				$table .= "<td>".number_format((int) $value['alltests'])."</td>";
				//$table .= "<td>".number_format((int) $value['invalids'])."</td>";

				$table .= "<td>".number_format($routine)."</td>";
				$table .= "<td>".number_format($routinesus)."</td>";
				//$table .= "<td>".number_format((int) $value['baseline'])."</td>";
				//$table .= "<td>".number_format((int) $value['baselinesustxfail'])."</td>";
				//$table .= "<td>".number_format((int) $value['confirmtx'])."</td>";
				//$table .= "<td>".number_format((int) $value['confirm2vl'])."</td>";
				$table .= "<td>".number_format((int) $routine + (int) $value['baseline'] + (int) $value['confirmtx'])."</td>";
				$table .= "<td>".number_format((int) $routinesus + (int) $value['baselinesustxfail'] + (int) $value['confirm2vl'])."</td>";
			$count++;
		}
		

		return $table;
	}

	function download_partner_counties($year=NULL,$month=NULL,$partner=NULL,$to_year=NULL,$to_month=NULL)
	{
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
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}

		$sql = "CALL `proc_get_vl_partner_county_details`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$data = $this->db->query($sql)->result_array();

		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */


	    $b = array(lang('label.table_county'), lang('label.facilities'), lang('label.tests'),
                lang('label.received'), lang('label.rejected'), lang('label.all_tests'),
                 lang('label.redraws'), lang('label.undetected'), lang('label.less1000'),
                lang('label.above1000_less5000'), lang('label.above5000'), lang('label.baseline_tests'), 
                 lang('label.baseline_gt1000'), lang('label.confirmatory_tests'), lang('label.confirmatory_gt1000'));

	    fputcsv($f, $b, $delimiter);

	    foreach ($data as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="eid_partner_sites.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);


	}
	
}
?>
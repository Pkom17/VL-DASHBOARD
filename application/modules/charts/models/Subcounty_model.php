<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
* 
*/
class Subcounty_model extends MY_Model
{
	
	function __construct()
	{
		parent::__construct();
	}

	function subcounty_outcomes($year=NULL,$month=NULL,$to_year=null,$to_month=null)
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

		$sql = "CALL `proc_get_vl_subcounty_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$res = $this->db->query($sql)->result_array();

		$result = array_splice($res, 0, 50);

		// echo "<pre>";print_r($result);die();


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
			$data['outcomes'][0]['data'][$key] = (int) $value['nonsuppressed'];
			$data['outcomes'][1]['data'][$key] = (int) $value['suppressed'];
			$data['outcomes'][2]['data'][$key] = @round((((int) $value['suppressed']*100)/((int) $value['suppressed']+(int) $value['nonsuppressed'])),1);
		}

		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function county_subcounties($year=NULL,$month=NULL,$to_year=null,$to_month=null)
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


		$sql = "CALL `proc_get_vl_subcounty_details`(0,'".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($sql);die();
		foreach ($result as $key => $value) {
			$routine = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
			$routinesus = ((int) $value['less5000'] + (int) $value['above5000']);
			$table .= "<tr>
						<td>".($key+1)."</td>
						<td>".$value['subcounty']."</td>
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

	function subcounty_vl_outcomes($year=NULL,$month=NULL,$subcounty=NULL,$to_year=null,$to_month=null)
	{
		if ($subcounty==null || $subcounty=='null') {
			$subcounty = $this->session->userdata('sub_county_filter');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
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

		$sql = "CALL `proc_get_vl_subcounty_vl_outcomes`('".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		$sql2 = "CALL `proc_get_vl_current_suppression`('2','".$subcounty."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$this->db->close();
		$current = $this->db->query($sql2)->row();
		$this->db->close();
		

		$color = array('#6BB9F0', '#e8ee1d', '#2f80d1', '#5C97BF');

		$data['vl_outcomes']['name'] = lang('label.tests');
		$data['vl_outcomes']['colorByPoint'] = true;
		$data['ul'] = '';

		$data['vl_outcomes']['data'][0]['name'] = lang('label.suppressed_');
		$data['vl_outcomes']['data'][1]['name'] = lang('label.not_suppressed_');

		$count = 0;

		$data['vl_outcomes']['data'][0]['y'] = $count;
		$data['vl_outcomes']['data'][1]['y'] = $count;

		foreach ($result as $key => $value) {
			$total = (int) ($value['undetected']+$value['less1000']+$value['less5000']+$value['above5000']);
			$less = (int) ($value['undetected']+$value['less1000']);
			$greater = (int) ($value['less5000']+$value['above5000']);
			$non_suppressed = $greater + (int) $value['confirm2vl'];
			$total_tests = (int) $value['confirmtx'] + $total + (int) $value['baseline'];

			$data['ul'] .= '
			<tr>
	    		<td>'.lang('label.total_vl_tests_done').'</td>
	    		<td>'.number_format($total_tests ).'</td>
	    		<td>'.lang('label.non_suppression').'</td>
	    		<td>'. number_format($non_suppressed) . ' (' . @round((($non_suppressed / $total_tests  )*100),1).'%</td>
	    	</tr>

			<tr>
	    		<td colspan="2">'.lang('label.routine_vl_tests_valid_outcomes').'</td>
	    		<td colspan="2">'.number_format($total).'</td>
	    	</tr>

	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.lang('label.valid_tests_gt1000').'</td>
	    		<td>'.number_format($greater).'</td>
	    		<td>'.lang('label.percentage_non_suppression').'</td>
	    		<td>'.@round((($greater/$total)*100),1).'%</td>
	    	</tr>

	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.lang('label.valid_tests_lt1000').'</td>
	    		<td>'.number_format($less).'</td>
	    		<td>'.lang('label.percentage_suppression').'</td>
	    		<td>'.@round((($less/$total)*100),1).'%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;'.lang('label.baseline_vl').'</td>
	    		<td>'.number_format($value['baseline']).'</td>
	    		<td>'.lang('label.non_suppression_gt_1000').'</td>
	    		<td>'.number_format($value['baselinesustxfail']). ' (' .@round(($value['baselinesustxfail'] * 100 / $value['baseline']), 1). '%)' .'</td>
	    	</tr>

	    	<tr>
	    		<td colspan="2">'.lang('label.confirmatory_repeat_tests').'</td>
	    		<td colspan="2">'.number_format($value['confirmtx']).'</td>
	    	</tr>

	    	<tr>
	    		<td>'.lang('label.rejected_samples').'</td>
	    		<td>'.number_format($value['rejected']).'</td>
	    		<td>'.lang('label.percentage_rejection_rate').'</td>
	    		<td>'. @round((($value['rejected']*100)/$value['alltests']), 1, PHP_ROUND_HALF_UP).'%</td>
	    	</tr>';
						
			$data['vl_outcomes']['data'][0]['y'] = (int) $value['undetected']+(int) $value['less1000'];
			$data['vl_outcomes']['data'][1]['y'] = (int) $value['less5000']+(int) $value['above5000'];

			$data['vl_outcomes']['data'][0]['color'] = '#2f80d1';
			$data['vl_outcomes']['data'][1]['color'] = '#e8ee1d';
		}
		$data['vl_outcomes']['data'][0]['sliced'] = true;
		$data['vl_outcomes']['data'][0]['selected'] = true;
		
		return $data;
	}

	function subcounty_gender($year=NULL,$month=NULL,$subcounty=NULL,$to_year=null,$to_month=null)
	{
		if ($subcounty==null || $subcounty=='null') {
			$subcounty = $this->session->userdata('sub_county_filter');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
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

		$sql = "CALL `proc_get_vl_subcounty_gender`('".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['gender'][0]['name'] = lang('label.not_suppressed_');
		$data['gender'][1]['name'] = lang('label.suppressed_');

		$count = 0;
		
		$data["gender"][0]["data"][0]	= $count;
		$data["gender"][0]["data"][1]	= $count;
		$data['categories'][0]			= lang('label.no_data');

		foreach ($result as $key => $value) {
			$data['categories'][$key] 			= $value['name'];
			$data['gender'][0]['data'][$key] = (int) $value['nonsuppressed'];
			$data['gender'][1]['data'][$key] = (int) $value['suppressed'];
		}
		$data['gender'][0]['drilldown']['color'] = '#913D88';
		$data['gender'][1]['drilldown']['color'] = '#96281B';
		
		return $data;
	}

	function subcounty_age($year=NULL,$month=NULL,$subcounty=NULL,$to_year=null,$to_month=null)
	{
		if ($subcounty==null || $subcounty=='null') {
			$subcounty = $this->session->userdata('sub_county_filter');
		}
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

		$sql = "CALL `proc_get_vl_subcounty_age`('".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		
		$data['ageGnd'][0]['name'] = lang('label.not_suppressed_');
		$data['ageGnd'][1]['name'] = lang('label.suppressed_');
 
		$count = 0;
		
		$data["ageGnd"][0]["data"][0]	= $count;
		$data["ageGnd"][1]["data"][0]	= $count;

		foreach ($result as $key => $value) {
			$data['categories'][$key] 			= $value['name'];
			$data['ageGnd'][0]['data'][$key] = (int) $value['nonsuppressed'];
			$data['ageGnd'][1]['data'][$key] = (int) $value['suppressed'];
		}
		$data['ageGnd'][0]['drilldown']['color'] = '#913D88';
		$data['ageGnd'][0]['drilldown']['color'] = '#913D88';
		return $data;
	}

	function get_sampletypesData($year=null,$subcounty=null)
	{
		$array1 = array();
		$array2 = array();
		$sql2 = NULL;

		if ($subcounty==null || $subcounty=='null') {
			$subcounty = $this->session->userdata('sub_county_filter');
		}
		
		if ($year==null || $year=='null') {
			$to = $this->session->userdata('filter_year');
		}else {
			$to = $year;
		}
		$from = $to-1;

		$sql = "CALL `proc_get_vl_subcounty_sample_types`('".$subcounty."','".$from."','".$to."')";
		// echo "<pre>";print_r($sql);die();
		$array1 = $this->db->query($sql)->result_array();
		return $array1;
		
		// if ($sql2) {
		// 	$this->db->close();
		// 	$array2 = $this->db->query($sql2)->result_array();
		// }
 
		// return array_merge($array1,$array2);
	}

	function subcounty_samples($year=NULL,$subcounty=NULL, $all=null)
	{
		$result = $this->get_sampletypesData($year,$subcounty);

		$data['sample_types'][0]['name'] = lang('label.sample_type_EDTA');
		$data['sample_types'][1]['name'] = lang('label.sample_type_DBS');
		$data['sample_types'][2]['name'] = lang('label.sample_type_plasma');
		// $data['sample_types'][3]['name'] = 'Suppression';

		// $data['sample_types'][0]['type'] = "column";
		// $data['sample_types'][1]['type'] = "column";
		// $data['sample_types'][2]['type'] = "column";
		// $data['sample_types'][3]['type'] = "spline";

		// $data['sample_types'][0]['yAxis'] = 1;
		// $data['sample_types'][1]['yAxis'] = 1;
		// $data['sample_types'][2]['yAxis'] = 1;

		$data['sample_types'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['sample_types'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['sample_types'][2]['tooltip'] = array("valueSuffix" => ' ');
		// $data['sample_types'][3]['tooltip'] = array("valueSuffix" => ' %');
 
		$count = 0;
		
		$data['categories'][0] = lang('label.no_data');
		$data["sample_types"][0]["data"][0]	= $count;
		$data["sample_types"][1]["data"][0]	= $count;
		$data["sample_types"][2]["data"][0]	= $count;
		// $data["sample_types"][3]["data"][0]	= $count;
 
		foreach ($result as $key => $value) {
			
			$data['categories'][$key] = $this->resolve_month($value['month']).'-'.$value['year'];

			if ($all == 1) {
				$data["sample_types"][0]["data"][$key]	= (int) $value['alledta'];
				$data["sample_types"][1]["data"][$key]	= (int) $value['alldbs'];
				$data["sample_types"][2]["data"][$key]	= (int) $value['allplasma'];
			}else{
				$data["sample_types"][0]["data"][$key]	= (int) $value['edta'];
				$data["sample_types"][1]["data"][$key]	= (int) $value['dbs'];
				$data["sample_types"][2]["data"][$key]	= (int) $value['plasma'];
			}
				// $data["sample_types"][3]["data"][$key]	= round($value['suppression'],1);
			
		}
		
		return $data;
	}

	function download_sampletypes($year=null,$subcounty=null)
	{
		$data = $this->get_sampletypesData($year,$subcounty);
		// echo "<pre>";print_r($result);die();
		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array(lang('date_months'), lang('date_year'),
                lang('label.sample_type_EDTA'), lang('label.sample_type_DBS'), lang('label.sample_type_plasma'),
                lang('label.sample_type_all_EDTA'), lang('label.sample_type_all_DBS'), lang('label.sample_type_all_plasma'),
                lang('label.suppressed_'), lang('label.tests'), lang('label.suppression'));

	    fputcsv($f, $b, $delimiter);

	    foreach ($data as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('YmdH:i:s').'vl_subcountysampleTypes.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
	}

	function subcounty_sites($year=NULL,$month=NULL,$subcounty=NULL,$to_year=null,$to_month=null)
	{
		if ($subcounty==null || $subcounty=='null') {
			$subcounty = $this->session->userdata('sub_county_filter');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
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

		$table = '';
		$count = 1;

		$sql = "CALL `proc_get_vl_subcounty_sites_details`('".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		foreach ($result as $key => $value) {
			$routine = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
			$routinesus = ((int) $value['less5000'] + (int) $value['above5000']);
			$table .= "<tr>
				<td>".$count."</td>
				<td>".$value['MFLCode']."</td>
				<td>".$value['name']."</td>
				<td>".$value['county']."</td>
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
				<td>".number_format((int) $routinesus + (int) $value['baselinesustxfail'] + (int) $value['confirm2vl'])."</td>";
			$count++;
		}
		
		return $table;
	}


	function download_subcounty_sites($year=NULL,$month=NULL,$subcounty=NULL,$to_year=null,$to_month=null)
	{
		if ($subcounty==null || $subcounty=='null') {
			$subcounty = $this->session->userdata('sub_county_filter');
		}
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

		$table = '';
		$count = 1;

		$sql = "CALL `proc_get_vl_subcounty_sites_details`('".$subcounty."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array( lang('label.code.MFL'),lang('label.facility'),  lang('label.table_county'),
                lang('label.sub_county'),  lang('label.received'), lang('label.rejected'),
                lang('label.all_tests'), lang('label.redraws'),  lang('label.undetected'), 
                 lang('label.less1000'),  lang('label.above1000_less5000'), lang('label.above5000'),
                  lang('label.baseline_tests'), lang('label.baseline_gt1000'), lang('label.confirmatory_tests'), 
                 lang('label.confirmatory_gt1000'));

	    fputcsv($f, $b, $delimiter);

	    foreach ($result as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('Ymd H:i:s').'vl_subcounty_sites.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
	}

	function justification_breakdown($year=null,$month=null,$subcounty=null,$to_year=null,$to_month=null)
	{
		
		if ($subcounty==null || $subcounty=='null') {
			$subcounty = $this->session->userdata('sub_county_filter');
		}

		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
 
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			$month = $this->session->userdata('filter_month');
		}
 

		$sql = "CALL `proc_get_vl_pmtct`('1','".$year."','".$month."','".$to_year."','".$to_month."','','','','".$subcounty."','')";
		$sql2 = "CALL `proc_get_vl_pmtct`('2','".$year."','".$month."','".$to_year."','".$to_month."','','','','".$subcounty."','')";

		
		
		$preg_mo = $this->db->query($sql)->result_array();
		$this->db->close();
		$lac_mo = $this->db->query($sql2)->result_array();
		// echo "<pre>";print_r($preg_mo);echo "</pre>";
		// echo "<pre>";print_r($lac_mo);die();
		$data['just_breakdown'][0]['name'] = lang('label.not_suppressed_');
		$data['just_breakdown'][1]['name'] = lang('label.suppressed_');
 
		$count = 0;
		
		$data["just_breakdown"][0]["data"][0]	= $count;
		$data["just_breakdown"][1]["data"][0]	= $count;
		$data['categories'][0]			= lang('label.no_data');
 
		foreach ($preg_mo as $key => $value) {
			$data['categories'][0] 			= lang('label.pregnant_mothers');
			$data["just_breakdown"][0]["data"][0]	=  (int) $value['less5000'] + (int) $value['above5000'];
			$data["just_breakdown"][1]["data"][0]	=  (int) $value['undetected'] + (int) $value['less1000'];
		}
 
		foreach ($lac_mo as $key => $value) {
			$data['categories'][1] 			= lang('label.lactating_mothers');
			$data["just_breakdown"][0]["data"][1]	=  (int) $value['less5000'] + (int) $value['above5000'];
			$data["just_breakdown"][1]["data"][1]	=  (int) $value['undetected'] + (int) $value['less1000'];
		}
 
		$data['just_breakdown'][0]['drilldown']['color'] = '#913D88';
		$data['just_breakdown'][1]['drilldown']['color'] = '#96281B';
				
		return $data;
	}

	
}
?>
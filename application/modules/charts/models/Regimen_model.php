<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
* 
*/
class Regimen_model extends MY_Model
{
	
	function __construct()
	{
		parent::__construct();
	}

	function regimens_outcomes($year=NULL,$month=NULL,$to_year=null,$to_month=null,$partner=NULL)
	{
		if ($partner==null || $partner=='null') {
			$partner = null;
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

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_regimen_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_regimen_outcomes`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['outcomes'][0]['name'] =  lang('label.not_suppressed_');
		$data['outcomes'][1]['name'] =  lang('label.suppressed_');
		$data['outcomes'][2]['name'] =  lang('label.suppression');

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
			$data['outcomes'][2]['data'][$key] = round(@(((int) $value['suppressed']*100)/((int) $value['suppressed']+(int) $value['nonsuppressed'])),1);
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function regimen_vl_outcomes($year=NULL,$month=NULL,$regimen=NULL,$to_year=null,$to_month=null,$partner=NULL)
	{
		if ($partner==null || $partner=='null') {
			$partner = null;
		}
		if ($regimen==null || $regimen=='null') {
			$regimen = $this->session->userdata('regimen_filter');
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

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_regimen_vl_outcomes`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_regimen_vl_outcomes`('".$partner."','".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
				
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$color = array('#6BB9F0', '#F2784B', '#1BA39C', '#5C97BF');

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
			$total_tests = (int) $value['confirmtx'] + $total;
			
			// 	<td colspan="2">Cumulative Tests (All Samples Run):</td>
	    	// 	<td colspan="2">'.number_format($value['alltests']).'</td>
	    	// </tr>
	    	// <tr>
			$data['ul'] .= '
			<tr>
	    		<td>'.lang('label.total_vl_tests_done').'</td>
	    		<td>'.number_format($total_tests ).'</td>
	    		<td>'.lang('label.non_suppression').'</td>
	    		<td>'. number_format($non_suppressed) . ' (' . @round((($non_suppressed / $total_tests  )*100),1).'%)</td>
	    	</tr>
 
			<tr>
	    		<td colspan="2">&nbsp;&nbsp;&nbsp;'.lang('label.routine_vl_tests_valid_outcomes').'</td>
	    		<td colspan="2">'.number_format($total).'</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.lang('label.valid_tests_gt1000').'</td>
	    		<td>'.number_format($greater).'</td>
	    		<td>'.  lang('label.percentage_non_suppression').'</td>
	    		<td>'.@round((($greater/$total)*100),1).'%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.lang('label.valid_tests_lt1000').'</td>
	    		<td>'.number_format($less).'</td>
	    		<td>'.  lang('label.percentage_suppression').'</td>
	    		<td>'.@round((($less/$total)*100),1).'%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;'.lang('label.baseline_vl').'</td>
	    		<td>'.number_format($value['baseline']).'</td>
	    		<td>'.lang('label.non_suppression_gt_1000').'</td>
	    		<td>'.number_format($value['baselinesustxfail']). ' (' .@round(($value['baselinesustxfail'] * 100 / $value['baseline']), 1). '%)' .'</td>
	    	</tr>
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;'.lang('label.confirmatory_repeat_tests').'</td>
	    		<td>'.number_format($value['confirmtx']).'</td>
	    		<td>'.lang('label.non_suppression_gt_1000').'</td>
	    		<td>'.number_format($value['confirm2vl']). ' (' .@round(($value['confirm2vl'] * 100 / $value['confirmtx']), 1). '%)' .'</td>
	    	</tr>
 
	    	<tr>
	    		<td>'.lang('label.rejected_samples').'</td>
	    		<td>'.number_format($value['rejected']).'</td>
	    		<td>'.lang('label.percentage_rejection_rate').'</td>
	    		<td>'. @round((($value['rejected']*100)/$value['alltests']), 1, PHP_ROUND_HALF_UP).'%</td>
	    	</tr>';
						
			$data['vl_outcomes']['data'][0]['y'] = (int) $value['undetected']+(int) $value['less1000'];
			$data['vl_outcomes']['data'][1]['y'] = (int) $value['less5000']+(int) $value['above5000'];

			$data['vl_outcomes']['data'][0]['color'] = '#1BA39C';
			$data['vl_outcomes']['data'][1]['color'] = '#F2784B';
		}
		$data['vl_outcomes']['data'][0]['sliced'] = true;
		$data['vl_outcomes']['data'][0]['selected'] = true;
		
		return $data;
	}

	function regimen_gender($year=NULL,$month=NULL,$regimen=NULL,$to_year=null,$to_month=null,$partner=NULL)
	{
		if ($partner==null || $partner=='null') {
			$partner = null;
		}
		if ($regimen==null || $regimen=='null') {
			$regimen = $this->session->userdata('regimen_filter');
		}
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_regimen_gender`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_regimen_gender`('".$partner."','".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$data['gender'][0]['name'] = lang('label.tests');

		$count = 0;
		
		$data["gender"][0]["data"][0]	= $count;
		$data["gender"][0]["data"][1]	= $count;
		$data['categories'][0]			=  lang('label.no_data');

		foreach ($result as $key => $value) {
			$data['categories'][0] 			=  lang('label.male');
			$data['categories'][1] 			=  lang('label.female');
			$data['categories'][2] 			=  lang('label.no_data');
			$data["gender"][0]["data"][0]	=  (int) $value['maletest'];
			$data["gender"][0]["data"][1]	=  (int) $value['femaletest'];
			$data["gender"][0]["data"][2]	= (int) $value['nodata'];
		}

		// $data['gender'][0]['drilldown']['color'] = '#913D88';
		// $data['gender'][0]['drilldown']['color'] = '#913D88';
		
		return $data;
	}

	function regimen_age($year=NULL,$month=NULL,$regimen=NULL,$to_year=null,$to_month=null,$partner=NULL)
	{
		if ($partner==null || $partner=='null') {
			$partner = null;
		}
		if ($regimen==null || $regimen=='null') {
			$regimen = $this->session->userdata('regimen_filter');
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

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_regimen_age`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_regimen_age`('".$partner."','".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$data['ageGnd'][0]['name'] =  lang('label.tests');

		$count = 0;
		
		$data["ageGnd"][0]["data"][0]	= $count;
		$data["ageGnd"][0]["data"][1]	= $count;
		$data['categories'][0]			=  lang('label.no_data');

		foreach ($result as $key => $value) {
			$data['categories'][0] 			= lang('label.no_age');
			$data['categories'][1] 			= lang('label.less2');
			$data['categories'][2] 			=  lang('label.less9');
			$data['categories'][3] 			=  lang('label.less14');
			$data['categories'][4] 			=  lang('label.less19');
			$data['categories'][5] 			=  lang('label.less24');
			$data['categories'][6] 			=  lang('label.over25');
			$data["ageGnd"][0]["data"][0]	=  (int) $value['noage'];
			$data["ageGnd"][0]["data"][1]	=  (int) $value['less2'];
			$data["ageGnd"][0]["data"][2]	=  (int) $value['less9'];
			$data["ageGnd"][0]["data"][3]	=  (int) $value['less14'];
			$data["ageGnd"][0]["data"][4]	=  (int) $value['less19'];
			$data["ageGnd"][0]["data"][5]	=  (int) $value['less24'];
			$data["ageGnd"][0]["data"][6]	=  (int) $value['over25'];
		}
		// $data['gender'][0]['drilldown']['color'] = '#913D88';
		// $data['gender'][0]['drilldown']['color'] = '#913D88';
		
		return $data;
	}

	function get_sampletypesData($year=NULL,$regimen=NULL,$partner=null)
	{
		$array1 = array();
		$array2 = array();
		$sql2 = NULL;

		if ($partner==null || $partner=='null') {
			$partner = null;
		}
		if ($regimen==null || $regimen=='null') {
			$regimen = $this->session->userdata('regimen_filter');
		}
		
		if ($year==null || $year=='null') {
			$to = $this->session->userdata('filter_year');
		}else {
			$to = $year;
		}
		$from = $to-1;

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_sample_types`('".$regimen."','".$from."')";
			$sql2 = "CALL `proc_get_vl_sample_types`('".$regimen."','".$to."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_regimen_sample_types`('".$partner."','".$regimen."','".$from."')";
			$sql2 = "CALL `proc_get_vl_partner_regimen_sample_types`('".$partner."','".$regimen."','".$to."')";
		}
		// echo "<pre>";print_r($sql);die();
		$array1 = $this->db->query($sql)->result_array();
		
		if ($sql2) {
			$this->db->close();
			$array2 = $this->db->query($sql2)->result_array();
		}
 
		return array_merge($array1,$array2);
	}

	function regimen_samples($year=NULL,$regimen=NULL,$partner=NULL)
	{
		$result = $this->get_sampletypesData($year,$regimen,$partner);

		$data['sample_types'][0]['name'] = lang('label.sample_type_EDTA');
		$data['sample_types'][1]['name'] = lang('label.sample_type_DBS');
		$data['sample_types'][2]['name'] = lang('label.sample_type_plasma');
		$data['sample_types'][3]['name'] = lang('label.sample_type_suppression');

		$data['sample_types'][0]['type'] = "column";
		$data['sample_types'][1]['type'] = "column";
		$data['sample_types'][2]['type'] = "column";
		$data['sample_types'][3]['type'] = "spline";

		$data['sample_types'][0]['yAxis'] = 1;
		$data['sample_types'][1]['yAxis'] = 1;
		$data['sample_types'][2]['yAxis'] = 1;

		$data['sample_types'][0]['tooltip'] = array("valueSuffix" => ' ');
		$data['sample_types'][1]['tooltip'] = array("valueSuffix" => ' ');
		$data['sample_types'][2]['tooltip'] = array("valueSuffix" => ' ');
		$data['sample_types'][3]['tooltip'] = array("valueSuffix" => ' %');
 
		$count = 0;
		
		$data['categories'][0] = lang('label.no_data');
		$data["sample_types"][0]["data"][0]	= $count;
		$data["sample_types"][1]["data"][0]	= $count;
		$data["sample_types"][2]["data"][0]	= $count;
		$data["sample_types"][3]["data"][0]	= $count;
 
		foreach ($result as $key => $value) {
			
				$data['categories'][$key] = $this->resolve_month($value['month']).'-'.$value['year'];
 
				$data["sample_types"][0]["data"][$key]	= (int) $value['edta'];
				$data["sample_types"][1]["data"][$key]	= (int) $value['dbs'];
				$data["sample_types"][2]["data"][$key]	= (int) $value['plasma'];
				$data["sample_types"][3]["data"][$key]	= round($value['suppression'],1);
			
		}
		
		return $data;
	}

	function download_sampletypes($year=NULL,$regimen=NULL,$partner=null)
	{
		$data = $this->get_sampletypesData($year,$regimen,$partner);
		// echo "<pre>";print_r($result);die();
		$this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array(lang('date_months'), lang('date_year'), lang('label.sample_type_EDTA'), lang('label.sample_type_DBS'), lang('label.sample_type_plasma'), lang('label.suppressed'), lang('label.tests'), lang('label.suppression'));

	    fputcsv($f, $b, $delimiter);

	    foreach ($data as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="'.Date('YmdH:i:s').'vl_regimensampleTypes.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
	}

	function county_outcomes($year=null,$month=null,$regimen=null,$to_year=null,$to_month=null,$partner=NULL)
	{
		
		if ($partner==null || $partner=='null') {
			$partner = null;
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
		//Assigning the value of the month or setting it to the selected value
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($regimen==null || $regimen=='null') {
			$regimen = $this->session->userdata('regimen_filter');
		}

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_county_regimen_outcomes`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_county_regimen_outcomes`('".$partner."','".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		
		$result = $this->db->query($sql)->result_array();
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
			$data['outcomes'][2]['data'][$key] = round(@(((int) $value['suppressed']*100)/((int) $value['suppressed']+(int) $value['nonsuppressed'])),1);
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function regimens_breakdowns($year=null,$month=null,$regimen=null,$to_year=null,$to_month=null,$county=null,$partner=null,$subcounty=null,$site=null)
	{
		$default = 0;
		$li = '';
		$table = '';
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		//Assigning the value of the month or setting it to the selected value
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($regimen==null || $regimen=='null') {
			$regimen = $this->session->userdata('regimen_filter');
		}

		if ($county == 1 || $county == '1') {
			$sql = "CALL `proc_get_vl_regimens_breakdowns_outcomes`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."','".$county."','".$default."','".$default."','".$default."')";
			$div_name = 'countyLising';
			$modal_name = 'countyModal';
		} elseif ($partner == 1 || $partner == '1') {
			$sql = "CALL `proc_get_vl_regimens_breakdowns_outcomes`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."','".$default."','".$partner."','".$default."','".$default."')";
			$div_name = 'partnerLising';
			$modal_name = 'partnerModal';
		} elseif ($subcounty == 1 || $subcounty == '1') {
			$sql = "CALL `proc_get_vl_regimens_breakdowns_outcomes`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."','".$default."','".$default."','".$subcounty."','".$default."')";
			$div_name = 'subcountyLising';
			$modal_name = 'subcountyModal';
		} elseif ($site == 1 || $site == '1') {
			$sql = "CALL `proc_get_vl_regimens_breakdowns_outcomes`('".$regimen."','".$year."','".$month."','".$to_year."','".$to_month."','".$default."','".$default."','".$default."','".$site."')";
			$div_name = 'siteLising';
			$modal_name = 'siteLising';
		}

		$result = $this->db->query($sql)->result_array();
		
		$count = 1;

		if($result)
		{
			foreach ($result as $key => $value)
			{
				if ($count<16) {
					$li .= '<a href="javascript:void(0);" class="list-group-item" ><strong>'.$count.'.</strong>&nbsp;'.$value['name'].':&nbsp;&nbsp;&nbsp;'.round($value['percentage'],1).'%&nbsp;&nbsp;&nbsp;('.number_format($value['total']).')</a>';
				}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.number_format((int) $value['total']).'</td>';
					$table .= '<td>'.number_format((int) $value['suppressed']).'</td>';
					$table .= '<td>'.number_format((int) $value['nonsuppressed']).'</td>';
					$table .= '<td>'.round($value['percentage'],1).'%</td>';
					$table .= '</tr>';
					$count++;
			}
		}else{
			$li = 'No Data';
		}
		
		$data = array(
						'ul' => $li,
						'table' => $table,
						'div_name' => $div_name,
						'modal_name' => $modal_name);
		return $data;
	}
}
?>
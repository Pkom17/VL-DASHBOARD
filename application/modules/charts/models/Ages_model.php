<?php
defined("BASEPATH") or exit("No direct script access allowed!");

/**
* 
*/
class Ages_model extends MY_Model
{
	
	function __construct()
	{
		parent::__construct();
	}

	function ages_outcomes($year=NULL,$month=NULL,$to_year=null,$to_month=null,$partner=null)
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
		if ($partner==null || $partner=='null') {
			$partner = null;
		}

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_age_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_age_outcomes`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$data['county_outcomes'][0]['name'] = 'Not Suppresed';
		$data['county_outcomes'][1]['name'] = 'Suppresed';

		$count = 0;
		
		$data["county_outcomes"][0]["data"][0]	= $count;
		$data["county_outcomes"][1]["data"][0]	= $count;
		$data['categories'][0]					= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["county_outcomes"][0]["data"][$key]	=  (int) $value['nonsuppressed'];
			$data["county_outcomes"][1]["data"][$key]	=  (int) $value['suppressed'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function ages_vl_outcomes($year=NULL,$month=NULL,$age_cat=NULL,$to_year=null,$to_month=null,$partner=null)
	{
		if ($age_cat==null || $age_cat=='null') {
			$age_cat = $this->session->userdata('age_category_filter');
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
		if ($partner==null || $partner=='null') {
			$partner = null;
		}

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_age_vl_outcomes`('".$age_cat."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_age_vl_outcomes`('".$partner."','".$age_cat."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$color = array('#6BB9F0', '#F2784B', '#1BA39C', '#5C97BF');

		$data['vl_outcomes']['name'] = 'Tests';
		$data['vl_outcomes']['colorByPoint'] = true;
		$data['ul'] = '';

		$data['vl_outcomes']['data'][0]['name'] = 'Suppresed';
		$data['vl_outcomes']['data'][1]['name'] = 'Not Suppresed';

		$count = 0;

		$data['vl_outcomes']['data'][0]['y'] = $count;
		$data['vl_outcomes']['data'][1]['y'] = $count;

		foreach ($result as $key => $value) {
			$total = (int) ($value['undetected']+$value['less1000']+$value['less5000']+$value['above5000']);
			$less = (int) ($value['undetected']+$value['less1000']);
			$greater = (int) ($value['less5000']+$value['above5000']);

			$data['ul'] .= '<tr>
	    		<td colspan="2">Tests With Valid Outcomes:</td>
	    		<td colspan="2">'.number_format($total).'</td>
	    	</tr>

	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Valid Tests &gt; 1000 copies/ml:</td>
	    		<td>'.number_format($greater).'</td>
	    		<td>Percentage Non Suppression</td>
	    		<td>'.round((($greater/$total)*100),2).'%</td>
	    	</tr>

	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Valid Tests &lt; 1000 copies/ml:</td>
	    		<td>'.number_format($less).'</td>
	    		<td>Percentage Suppression</td>
	    		<td>'.round((($less/$total)*100),2).'%</td>
	    	</tr>

	    	<tr>
	    		<td></td>
	    		<td></td>
	    		<td></td>
	    		<td></td>
	    	</tr>

	    	<tr>
	    		<td colspan="2">Confirmatory Repeat Tests:</td>
	    		<td colspan="2">'.number_format($value['confirmtx']).'</td>
	    	</tr>

	    	<tr>
	    		<td>Rejected Samples:</td>
	    		<td>'.number_format($value['rejected']).'</td>
	    		<td>Percentage Rejection Rate</td>
	    		<td>'. round((($value['rejected']*100)/$value['alltests']), 4, PHP_ROUND_HALF_UP).'%</td>
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

	function ages_gender($year=NULL,$month=NULL,$age_cat=NULL,$to_year=null,$to_month=null,$partner=null)
	{
		if ($age_cat==null || $age_cat=='null') {
			$age_cat = $this->session->userdata('age_category_filter');
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
		if ($partner==null || $partner=='null') {
			$partner = null;
		}

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_age_gender`('".$age_cat."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_age_gender`('".$partner."','".$age_cat."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$data['gender'][0]['name'] = 'Test';

		$count = 0;
		
		$data["gender"][0]["data"][0]	= $count;
		$data["gender"][0]["data"][1]	= $count;
		$data['categories'][0]			= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][0] 			= 'Male';
			$data['categories'][1] 			= 'Female';
			$data['categories'][2] 			= 'No Data';
			$data["gender"][0]["data"][0]	=  (int) $value['maletest'];
			$data["gender"][0]["data"][1]	=  (int) $value['femaletest'];
			$data["gender"][0]["data"][2]	= (int) $value['nodata'];
		}

		// $data['gender'][0]['drilldown']['color'] = '#913D88';
		// $data['gender'][0]['drilldown']['color'] = '#913D88';
		
		return $data;
	}

	function ages_samples($year=NULL,$age_cat=NULL,$partner=null)
	{
		$array1 = array();
		$array2 = array();
		$sql2 = NULL;

		if ($age_cat==null || $age_cat=='null') {
			$age_cat = $this->session->userdata('age_category_filter');
		}

		
		if ($year==null || $year=='null') {
			$to = $this->session->userdata('filter_year');
		}else {
			$to = $year;
		}
		if ($partner==null || $partner=='null') {
			$partner = null;
		}
		$from = $to-1;

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_age_sample_types`('".$age_cat."','".$from."')";
			$sql2 = "CALL `proc_get_vl_age_sample_types`('".$age_cat."','".$to."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_age_sample_types`('".$partner."','".$age_cat."','".$from."')";
			$sql2 = "CALL `proc_get_vl_partner_age_sample_types`('".$partner."','".$age_cat."','".$to."')";
		}
		
		// echo "<pre>";print_r($sql);die();
		$array1 = $this->db->query($sql)->result_array();
		
		if ($sql2) {
			$this->db->close();
			$array2 = $this->db->query($sql2)->result_array();
		}

		$result = array_merge($array1,$array2);

		$data['sample_types'][0]['name'] = 'EDTA';
		$data['sample_types'][1]['name'] = 'DBS';
		$data['sample_types'][2]['name'] = 'Plasma';

		$count = 0;
		
		$data['categories'][0] = 'No Data';
		$data["sample_types"][0]["data"][0]	= $count;
		$data["sample_types"][1]["data"][0]	= $count;
		$data["sample_types"][2]["data"][0]	= $count;

		foreach ($result as $key => $value) {
			
				$data['categories'][$key] = $this->resolve_month($value['month']).'-'.$value['year'];

				$data["sample_types"][0]["data"][$key]	= (int) $value['edta'];
				$data["sample_types"][1]["data"][$key]	= (int) $value['dbs'];
				$data["sample_types"][2]["data"][$key]	= (int) $value['plasma'];
			
		}
		
		return $data;
	}

	function county_outcomes($year=null,$month=null,$age_cat=null,$to_year=null,$to_month=null,$partner=null)
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
		//Assigning the value of the month or setting it to the selected value
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}
		if ($partner==null || $partner=='null') {
			$partner = null;
		}

		if ($age_cat==null || $age_cat=='null') {
			$age_cat = $this->session->userdata('age_category_filter');
		}

		if ($partner==null) {
			$sql = "CALL `proc_get_vl_county_age_outcomes`('".$age_cat."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_vl_partner_county_age_outcomes`('".$partner."','".$age_cat."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$data['county_outcomes'][0]['name'] = 'Not Suppresed';
		$data['county_outcomes'][1]['name'] = 'Suppresed';

		$count = 0;
		
		$data["county_outcomes"][0]["data"][0]	= $count;
		$data["county_outcomes"][1]["data"][0]	= $count;
		$data['categories'][0]					= 'No Data';

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["county_outcomes"][0]["data"][$key]	=  (int) $value['nonsuppressed'];
			$data["county_outcomes"][1]["data"][$key]	=  (int) $value['suppressed'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

}
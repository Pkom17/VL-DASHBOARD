<?php
(defined('BASEPATH') or exit('No direct script access allowed!'));
/**
* 
*/
class Pmtct_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}

	public function pmtct_outcomes($year=null,$month=null,$to_year=null,$to_month=null,$partner=null)
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
			$partner = $this->session->userdata('partner_filter');
		}
		$default = 0;
		$sql = "CALL `proc_get_vl_pmtct`('".$default."','".$year."','".$month."','".$to_year."','".$to_month."','".$default."','".$default."','".$partner."','".$default."')";

		$result = $this->db->query($sql)->result();

		$count = 0;
		$name = '';
		
		// echo "<pre>";print_r($result);die();
		$data['pmtct'][0]['name'] = 'Not Suppresed';
		$data['pmtct'][1]['name'] = 'Suppresed';
 
		$data["pmtct"][0]["data"][0]	= $count;
		$data["pmtct"][1]["data"][0]	= $count;
		$data['categories'][0]			= 'No Data';
 
		foreach ($result as $key => $value) {
			$data['categories'][$key] 		= $value->pmtcttype;
			$data["pmtct"][0]["data"][$key]	=  (int) ($value->less5000+$value->above5000);
			$data["pmtct"][1]["data"][$key]	=  (int) ($value->undetected+$value->less1000);
		}
		// die();
		$data['pmtct'][0]['drilldown']['color'] = '#913D88';
		$data['pmtct'][1]['drilldown']['color'] = '#96281B';
 
		// echo "<pre>";print_r($data);die();
		// $data['categories'] = array_values($data['categories']);
		// $data["pmtct"][0]["data"] = array_values($data["pmtct"][0]["data"]);
		// $data["pmtct"][1]["data"] = array_values($data["pmtct"][1]["data"]);
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	public function suppression($year=null,$month=null,$pmtcttype=null,$to_year=null,$to_month=null,$partner=null)
	{
		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = $year;
			$year -= 1;
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}

		if ($partner==null || $partner=='null') {
			$partner = $this->session->userdata('partner_filter');
		}
		$default = 0;
		$sql = "CALL `proc_get_vl_pmtct_suppression`('".$pmtcttype."','".$year."','".$default."','".$to_year."','".$default."','".$default."','".$default."','".$partner."','".$default."')";

		$result = $this->db->query($sql)->result();
		// echo "<pre>";print_r($result);die();
		$data['outcomes'][0]['name'] = "Not Suppressed";
		$data['outcomes'][1]['name'] = "Suppressed";
		$data['outcomes'][2]['name'] = "Suppression";

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
			$data['categories'][$key] 		   = $this->resolve_month($value->month)." - ".$value->year;
			$data['outcomes'][0]['data'][$key] = (int) $value->nonsuppressed;
			$data['outcomes'][1]['data'][$key] = (int) $value->suppressed;
			$data['outcomes'][2]['data'][$key] = round(@$value->suppression,1);
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}
	public function vl_outcomes($year=null,$month=null,$pmtcttype=null,$to_year=null,$to_month=null,$partner=null)
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
			$partner = $this->session->userdata('partner_filter');
		}
		$default = 0;
		$sql = "CALL `proc_get_vl_pmtct`('".$pmtcttype."','".$year."','".$month."','".$to_year."','".$to_month."','".$default."','".$default."','".$partner."','".$default."')";

		$result = $this->db->query($sql)->result();

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
			$total = (int) ($value->undetected+$value->less1000+$value->less5000+$value->above5000);
			$less = (int) ($value->undetected+$value->less1000);
			$greater = (int) ($value->less5000+$value->above5000);
			$non_suppressed = $greater + (int) $value->confirm2vl;
			$total_tests = (int) $value->confirmtx + $total + (int) $value->baseline;
			
			$data['ul'] .= '
			<tr>
	    		<td>Total VL tests done:</td>
	    		<td>'.number_format($total_tests ).'</td>
	    		<td>Non Suppression</td>
	    		<td>'. number_format($non_suppressed) . ' (' . round((($non_suppressed / $total_tests  )*100),1).'%)</td>
	    	</tr>
 
			<tr>
	    		<td>&nbsp;&nbsp;&nbsp;Routine VL Tests with Valid Outcomes:</td>
	    		<td>'.number_format($total).'</td>
	    		<td colspan="2"></td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Valid Tests &gt; 1000 copies/ml:</td>
	    		<td>'.number_format($greater).'</td>
	    		<td>Percentage Non Suppression</td>
	    		<td>'.round((($greater/$total)*100),1).'%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Valid Tests &lt; 1000 copies/ml:</td>
	    		<td>'.number_format($less).'</td>
	    		<td>Percentage Suppression</td>
	    		<td>'.round((($less/$total)*100),1).'%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;Baseline VLs:</td>
	    		<td>'.number_format($value->baseline).'</td>
	    		<td>Non Suppression ( &gt; 1000cpml)</td>
	    		<td>'.number_format($value->baselinesustxfail). ' (' .round(@($value->baselinesustxfail * 100 / $value->baseline), 1). '%)' .'</td>
	    	</tr>
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;Confirmatory Repeat Tests:</td>
	    		<td>'.number_format($value->confirmtx).'</td>
	    		<td>Non Suppression ( &gt; 1000cpml)</td>
	    		<td>'.number_format($value->confirm2vl). ' (' .round(@($value->confirm2vl * 100 / $value->confirmtx), 1). '%)' .'</td>
	    	</tr>
 
	    	<tr>
	    		<td>Rejected Samples:</td>
	    		<td>'.number_format($value->rejected).'</td>
	    		<td>Percentage Rejection Rate</td>
	    		<td>'. round(@(($value->rejected*100)/$total_tests), 1, PHP_ROUND_HALF_UP).'%</td>
	    	</tr>';
						
			$data['vl_outcomes']['data'][0]['y'] = (int) $value->undetected+(int) $value->less1000;
			$data['vl_outcomes']['data'][1]['y'] = (int) $value->less5000+(int) $value->above5000;
 
			$data['vl_outcomes']['data'][0]['color'] = '#1BA39C';
			$data['vl_outcomes']['data'][1]['color'] = '#F2784B';
		}

		return $data;
	}
	public function counties_outcomes($year=null,$month=null,$pmtcttype=null,$to_year=null,$to_month=null,$partner=null)
	{

	}
}
?>
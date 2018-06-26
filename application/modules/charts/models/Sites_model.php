<?php
defined('BASEPATH') or exit();

/**
* 
*/
class Sites_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}


	function sites_outcomes($year=null,$month=null,$partner=null,$to_year=null,$to_month=null)
	{
		
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($partner==null || $partner=='null') {
			$partner = $this->session->userdata('partner_year');
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

		if ($partner) {
			$sql = "CALL `proc_get_partner_sites_outcomes`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}  else {
			$sql = "CALL `proc_get_all_sites_outcomes`('".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		// $sql = "CALL `proc_get_all_sites_outcomes`('".$year."','".$month."')";
		
		// echo "<pre>";print_r($sql);die();
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

	function partner_sites_outcomes($year=NULL,$month=NULL,$partner=NULL,$to_year=null,$to_month=null)
	{
		$table = '';
		$count = 1;
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

		$sql = "CALL `proc_get_partner_sites_details`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($sql);die();
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
				<td>".number_format((int) $routinesus + (int) $value['baselinesustxfail'] + (int) $value['confirm2vl'])."</td>";
			$count++;
		}
		

		return $table;
	}

	function sites_trends($year=null,$month=null,$site=null,$to_year=null,$to_month=null)
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
		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}
		$data['year'] = $year;

		$sql = "CALL `proc_get_sites_trends`('".$site."','".$year."')";

		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		
		$months = array(1,2,3,4,5,6,7,8,9,10,11,12);
		$count = 0;


		$data['test_trends'][0]['name'] = lang('label.tests');
		$data['test_trends'][1]['name'] = lang('label.suppressed_');
		$data['test_trends'][2]['name'] = lang('label.non_suppressed_');
		$data['test_trends'][3]['name'] = lang('label.rejected');

		$data['test_trends'][0]['data'][0] = $count;
		$data['test_trends'][1]['data'][0] = $count;
		$data['test_trends'][2]['data'][0] = $count;
		$data['test_trends'][3]['data'][0] = $count;

		foreach ($months as $key1 => $value1) {
			foreach ($result as $key2 => $value2) {
				if ((int) $value1 == (int) $value2['month']) {
					$data['test_trends'][0]['data'][$count] = (int) $value2['alltests'];
					$data['test_trends'][1]['data'][$count] = (int) $value2['undetected']+(int) $value2['less1000'];
					$data['test_trends'][2]['data'][$count] = (int) $value2['less5000']+(int) $value2['above5000'];
					$data['test_trends'][3]['data'][$count] = (int) $value2['rejected'];

					$count++;
				}
				
			}
		}
		
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function site_outcomes_chart($year=null,$month=null,$site=null,$to_year=null,$to_month=null)
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
		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}
		
		$sql = "CALL `proc_get_sites_sample_types`('".$site."','".$year."')";
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$months = array(1,2,3,4,5,6,7,8,9,10,11,12);

		$data['sample_types'][0]['name'] = lang('label.sample_type_EDTA');
		$data['sample_types'][1]['name'] = lang('label.sample_type_DBS');
		$data['sample_types'][2]['name'] = lang('label.sample_type_plasma');

		$count = 0;
		
		$data['categories'] = array(lang('cal_jan'),lang('cal_feb'),lang('cal_mar'),lang('cal_apr'),lang('cal_may'),lang('cal_jun'),lang('cal_jul'),lang('cal_aug'),lang('cal_sep'),lang('cal_oct'),lang('cal_nov'),lang('cal_dev'));
		$data["sample_types"][0]["data"][0]	= $count;
		$data["sample_types"][1]["data"][0]	= $count;
		$data["sample_types"][2]["data"][0]	= $count;

		foreach ($months as $key => $value) {
			foreach ($result as $key1 => $value1) {
				if ((int) $value == (int) $value1['month']) {
					$data["sample_types"][0]["data"][$key]	= (int) $value1['edta'];
					$data["sample_types"][1]["data"][$key]	= (int) $value1['dbs'];
					$data["sample_types"][2]["data"][$key]	= (int) $value1['plasma'];

					$count++;
				}
				
			}
		}
		 // echo "<pre>";print_r($data);die();
		return $data;
	}

	function sites_vloutcomes($year=null,$month=null,$site=null,$to_year=null,$to_month=null)
	{
		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
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

		$sql = "CALL `proc_get_sites_vl_outcomes`('".$site."','".$year."','".$month."','".$to_year."','".$to_month."')";
		$sql2 = "CALL `proc_get_vl_current_suppression`('4','".$site."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();

		$this->db->close();
		
		// echo "<pre>";print_r($result);die();
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
			$total_tests = (int) $value['confirmtx'] + $total + (int) $value['baseline'];
			
			// 	<td colspan="2">Cumulative Tests (All Samples Run):</td>
	    	// 	<td colspan="2">'.number_format($value['alltests']).'</td>
	    	// </tr>
	    	// <tr>
			$data['ul'] .= '
			<tr>
	    		<td>'.lang('label.total_vl_tests_done').'</td>
	    		<td>'.number_format($total_tests ).'</td>
	    		<td>'.lang('label.non_suppression').'</td>
	    		<td>'. number_format($non_suppressed) . ' (' . round((($non_suppressed / $total_tests  )*100),1).'%)</td>
	    	</tr>
 
			<tr>
	    		<td colspan="2">&nbsp;&nbsp;&nbsp;'.lang('label.routine_vl_tests_valid_outcomes').'</td>
	    		<td colspan="2">'.number_format($total).'</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.lang('label.valid_tests_gt1000').'</td>
	    		<td>'.number_format($greater).'</td>
	    		<td>'.lang('label.percentage_non_suppression').'</td>
	    		<td>'.round((($greater/$total)*100),1).'%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.lang('label.valid_tests_lt1000').'</td>
	    		<td>'.number_format($less).'</td>
	    		<td>'.lang('label.percentage_suppression').'</td>
	    		<td>'.round((($less/$total)*100),1).'%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;'.lang('label.baseline_vl').'</td>
	    		<td>'.number_format($value['baseline']).'</td>
	    		<td>'.lang('label.non_suppression_gt_1000').'</td>
	    		<td>'.number_format($value['baselinesustxfail']). ' (' .round(($value['baseline'] * 100 / $value['baselinesustxfail']), 1). '%)' .'</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;'.lang('label.confirmatory_repeat_tests').'</td>
	    		<td>'.number_format($value['confirmtx']).'</td>
	    		<td>'.lang('label.non_suppression_gt_1000').'</td>
	    		<td>'.number_format($value['confirm2vl']). ' (' .round(($value['confirm2vl'] * 100 / $value['confirmtx']), 1). '%)' .'</td>
	    	</tr>
 
	    	<tr>
	    		<td>'.lang('label.rejected_samples').'</td>
	    		<td>'.number_format($value['rejected']).'</td>
	    		<td>'.lang('label.percentage_rejection_rate').'</td>
	    		<td>'. round((($value['rejected']*100)/$value['received']), 1, PHP_ROUND_HALF_UP).'%</td>
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

	function sites_age($year=null,$month=null,$site=null,$to_year=null,$to_month=null)
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
		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}

		$sql = "CALL `proc_get_sites_age`('".$site."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$count = 0;
		$loop = 0;
		$name = '';
		$nonsuppressed = 0;
		$suppressed = 0;
		
		// echo "<pre>";print_r($result);die();
		$data['ageGnd'][0]['name'] = lang('label.not_suppressed_');
		$data['ageGnd'][1]['name'] = lang('label.suppressed_');
 
		$count = 0;
		
		$data["ageGnd"][0]["data"][0]	= $count;
		$data["ageGnd"][1]["data"][0]	= $count;
		$data['categories'][0]			= lang('label.no_data');
 
		foreach ($result as $key => $value) {
			if ($value['name']==lang('label.no_data')) {
				$loop = $key;
				$name = $value['name'];
				$nonsuppressed = $value['nonsuppressed'];
				$suppressed = $value['suppressed'];
			} else if ($value['name']==lang('label.less2')) {
				$loop = $key;
				$name = $value['name'];
				$nonsuppressed = $value['nonsuppressed'];
				$suppressed = $value['suppressed'];
			} else if ($value['name']==lang('label.less9')) {
				$loop = $key;
				$name = $value['name'];
				$nonsuppressed = $value['nonsuppressed'];
				$suppressed = $value['suppressed'];
			} else if ($value['name']==lang('label.less14')) {
				$loop = $key;
				$name = $value['name'];
				$nonsuppressed = $value['nonsuppressed'];
				$suppressed = $value['suppressed'];
			} else if ($value['name']==lang('label.less19')) {
				$loop = $key;
				$name = $value['name'];
				$nonsuppressed = $value['nonsuppressed'];
				$suppressed = $value['suppressed'];
			} else if ($value['name']==lang('label.less24')) {
				$loop = $key;
				$name = $value['name'];
				$nonsuppressed = $value['nonsuppressed'];
				$suppressed = $value['suppressed'];
			} else if ($value['name']==lang('label.over25')) {
				$loop = $key;
				$name = $value['name'];
				$nonsuppressed = $value['nonsuppressed'];
				$suppressed = $value['suppressed'];
			}
			
			$data['categories'][$loop] 			= $name;
			$data["ageGnd"][0]["data"][$loop]	=  (int) $nonsuppressed;
			$data["ageGnd"][1]["data"][$loop]	=  (int) $suppressed;
		}
		// die();
		$data['ageGnd'][0]['drilldown']['color'] = '#913D88';
		$data['ageGnd'][1]['drilldown']['color'] = '#96281B';
 
		// echo "<pre>";print_r($data);die();
		$data['categories'] = array_values($data['categories']);
		$data["ageGnd"][0]["data"] = array_values($data["ageGnd"][0]["data"]);
		$data["ageGnd"][1]["data"] = array_values($data["ageGnd"][1]["data"]);
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function sites_gender($year=null,$month=null,$site=null,$to_year=null,$to_month=null)
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
		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}

		$sql = "CALL `proc_get_sites_gender`('".$site."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		$data['gender'][0]['name'] = lang('label.not_suppressed_');
		$data['gender'][1]['name'] = lang('label.suppressed_');
 
		$count = 0;
		
		$data["gender"][0]["data"][0]	= $count;
		$data["gender"][1]["data"][0]	= $count;
		$data['categories'][0]			= lang('label.no_data');
 
		foreach ($result as $key => $value) {
			$data['categories'][$key] 			= $value['name'];
			$data["gender"][0]["data"][$key]	=  (int) $value['nonsuppressed'];
			$data["gender"][1]["data"][$key]	=  (int) $value['suppressed'];
		}
 
		$data['gender'][0]['drilldown']['color'] = '#913D88';
		$data['gender'][1]['drilldown']['color'] = '#96281B';
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function partner_sites_outcomes_download($year=NULL,$month=NULL,$partner=NULL,$to_year=null,$to_month=null)
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

		$sql = "CALL `proc_get_partner_sites_details`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$data = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($data);die();
		

		// $this->load->helper('download');
  //       $this->load->library('PHPReport/PHPReport');

  //       ini_set('memory_limit','-1');
	 //    ini_set('max_execution_time', 900);


  //       $template = 'partner_sites.xlsx';

	 //    //set absolute path to directory with template files
	 //    $templateDir = __DIR__ . "/";
	    
	 //    //set config for report
	 //    $config = array(
	 //        'template' => $template,
	 //        'templateDir' => $templateDir
	 //    );


	 //      //load template
	 //    $R = new PHPReport($config);
	    
	 //    $R->load(array(
	 //            'id' => 'data',
	 //            'repeat' => TRUE,
	 //            'data' => $data   
	 //        )
	 //    );
	      
	 //      // define output directoy 
	 //    $output_file_dir = __DIR__ ."/tmp/";
	 //     // echo "<pre>";print_r("Still working");die();

	 //    $output_file_excel = $output_file_dir  . "partner_sites.xlsx";
	 //    //download excel sheet with data in /tmp folder
	 //    $result = $R->render('excel', $output_file_excel);
	 //    force_download($output_file_excel, null);	

        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

	    /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
	    $f = fopen('php://memory', 'w');
	    /** loop through array  */

	    $b = array(
                lang('label.code.MFL'), lang('label.table_name'), lang('label.table_county'), 
                lang('label.received'), lang('label.rejected'), lang('label.all_tests'), lang('label.redraws'),
                lang('label.undetected'), lang('label.less1000'), lang('label.above1000_less5000'), lang('label.above5000'),
                lang('label.baseline_tests'), lang('label.baseline_gt1000'), lang('label.confirmatory_tests'),
                lang('label.confirmatory_gt1000'));

	    fputcsv($f, $b, $delimiter);

	    foreach ($data as $line) {
	        /** default php csv handler **/
	        fputcsv($f, $line, $delimiter);
	    }
	    /** rewrind the "file" with the csv lines **/
	    fseek($f, 0);
	    /** modify header to be downloadable csv file **/
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachement; filename="vl_partner_sites.csv";');
	    /** Send file to browser for download */
	    fpassthru($f);
		
	}


	function justification($year=null,$month=null,$site=null,$to_year=null,$to_month=null)
	{
		
		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
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
				$month = $this->session->userdata('filter_month');
			}else {
				$month = 0;
			}
		}
 
		$sql = "CALL `proc_get_vl_site_justification`('".$site."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		
		$data['justification']['name'] = lang('label.tests');
		$data['justification']['colorByPoint'] = true;
 
		$count = 0;
 
		$data['justification']['data'][0]['name'] = lang('label.no_data');
 
		foreach ($result as $key => $value) {
			if($value['name'] == 'Routine VL'){
				$data['justification']['data'][$key]['color'] = '#5C97BF';
			}
			$data['justification']['data'][$key]['y'] = $count;
			
			$data['justification']['data'][$key]['name'] = $value['name'];
			$data['justification']['data'][$key]['y'] = (int) $value['justifications'];
		}
 
		$data['justification']['data'][0]['sliced'] = true;
		$data['justification']['data'][0]['selected'] = true;
		// echo "<pre>";print_r($data);die();
		return $data;
	}
 
	function justification_breakdown($year=null,$month=null,$site=null,$to_year=null,$to_month=null)
	{
		
		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
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
 

		$sql = "CALL `proc_get_vl_pmtct`('1','".$year."','".$month."','".$to_year."','".$to_month."','','','','','".$site."')";
		$sql2 = "CALL `proc_get_vl_pmtct`('2','".$year."','".$month."','".$to_year."','".$to_month."','','','','','".$site."')";

		
		
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



	function get_patients($site=null,$year=null,$month=null,$to_year=NULL,$to_month=NULL)
	{
		$type = 0;

		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
				$type = 1;
			}else {
				$month = $this->session->userdata('filter_month');
				$type = 3;
			}
		}
		
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}

		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}	

		$query = $this->db->get_where('facilitys', array('id' => $site), 1)->row();

		$facility = $query->facilitycode;

		$this->db->close();

		$sql = "CALL `proc_get_vl_site_patients`('".$site."','".$year."')";
		$res = $this->db->query($sql)->row();

		$this->db->close();
		

		$params = "patient/facility/{$facility}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
		// $params = "patient/national/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";

		$result = $this->req($params);

		// echo "<pre>";print_r($result);die();

		$data['outcomes'][0]['name'] = lang('label.patients_grouped_by_tests_received');

		$data['title'] = " ";

		$data['unique_patients'] = 0;
		$data['size'] = 0;
		$data['total_patients'] = $query->totalartmar;
		$data['total_tests'] = 0;

		foreach ($result as $key => $value) {

			$data['categories'][$key] = $value->tests;
		
			$data['outcomes'][0]['data'][$key] = (int) $value->totals;
		
			$data['outcomes'][0]['data'][$key] = (int) $value->totals;
			$data['unique_patients'] += (int) $value->totals;
			$data['total_tests'] += ($data['categories'][$key] * $data['outcomes'][0]['data'][$key]);
			$data['size']++;


		}

		$data['coverage'] = round(($data['unique_patients'] / $data['total_patients'] * 100), 2);

		// $data['stats'] = "<tr><td>" . $result->total_tests . "</td><td>" . $result->one . "</td><td>" . $result->two . "</td><td>" . $result->three . "</td><td>" . $result->three_g . "</td></tr>";

		// $unmet = $res->totalartmar - $result->total_patients;
		// $unmet_p = round((($unmet / (int) $res->totalartmar) * 100),2);

		// $data['tests'] = $result->total_tests;
		// $data['patients_vl'] = $result->total_patients;
		// $data['patients'] = $res->totalartmar;
		// $data['unmet'] = $unmet;
		// $data['unmet_p'] = $unmet_p;

		return $data;
	}

	function current_suppression($site=null){
		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}

		$sql = "CALL `proc_get_vl_current_suppression`('4','".$site."')";
		$result = $this->db->query($sql)->row();

		$this->db->close();
		
		// echo "<pre>";print_r($result);die();
		$color = array('#6BB9F0', '#F2784B', '#1BA39C', '#5C97BF');

		$data['vl_outcomes']['name'] = lang('label.tests');
		$data['vl_outcomes']['colorByPoint'] = true;
		$data['ul'] = '';

		$data['vl_outcomes']['data'][0]['name'] = lang('label.suppressed_');
		$data['vl_outcomes']['data'][1]['name'] = lang('label.not_suppressed_');

		$data['vl_outcomes']['data'][0]['y'] = (int) $result->suppressed;
		$data['vl_outcomes']['data'][1]['y'] = (int) $result->nonsuppressed;

		$data['vl_outcomes']['data'][0]['color'] = '#1BA39C';
		$data['vl_outcomes']['data'][1]['color'] = '#F2784B';
		

		$data['vl_outcomes']['data'][0]['sliced'] = true;
		$data['vl_outcomes']['data'][0]['selected'] = true;

		$data['total'][0] = (int) $result->suppressed;
		$data['total'][1] = (int) $result->nonsuppressed;
		
		return $data;

	}


	function get_patients_outcomes($site=null,$year=null,$month=null,$to_year=NULL,$to_month=NULL)
	{
		$type = 0;

		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
				$type = 1;
			}else {
				$month = $this->session->userdata('filter_month');
				$type = 3;
			}
		}
		
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}

		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}		

		$query = $this->db->get_where('facilitys', array('id' => $site), 1)->row();

		$facility = $query->facilitycode;

		$params = "patient/facility/{$facility}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";

		$result = $this->req($params);

		$data['categories'] = array(lang('label.total_patients'), lang('label.vl_done'));
		$data['outcomes']['name'] = lang('label.tests');
		$data['outcomes']['data'][0] = (int) $result->total_patients;
		$data['outcomes']['data'][1] = (int) $result->total_tests;
		$data["outcomes"]["color"] =  '#1BA39C';

		return $data;
	}

	function get_patients_graph($site=null,$year=null,$month=null,$to_year=NULL,$to_month=NULL)
	{
		$type = 0;

		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
				$type = 1;
			}else {
				$month = $this->session->userdata('filter_month');
				$type = 3;
			}
		}
		
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}

		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}		

		$query = $this->db->get_where('facilitys', array('id' => $site), 1)->row();

		$facility = $query->facilitycode;

		$params = "patient/facility/{$facility}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";

		$result = $this->req($params);

		$data['categories'] = array(lang('label.table_1_VL'), lang('label.table_2_VL'), lang('label.table_3_VL'), lang('label.table_m3_VL'));
		$data['outcomes']['name'] = lang('label.tests');
		$data['outcomes']['data'][0] = (int) $result->one;
		$data['outcomes']['data'][1] = (int) $result->two;
		$data['outcomes']['data'][2] = (int) $result->three;
		$data['outcomes']['data'][3] = (int) $result->three_g;
		$data["outcomes"]["color"] =  '#1BA39C';

		return $data;
	}
	

	// function get_patients($site=null,$year=null){
	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($site==null || $site=='null') {
	// 		$site = $this->session->userdata('site_filter');
	// 	}

	// 	$sql = "CALL `proc_get_vl_site_patients`('".$site."','".$year."')";
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->row();

	// 	$data['stats'] = "<tr><td>" . $result->alltests . "</td><td>" . $result->onevl . "</td><td>" . $result->twovl . "</td><td>" . $result->threevl . "</td><td>" . $result->above3vl . "</td></tr>";

	// 	$data['tests'] = $result->alltests;
	// 	$data['patients'] = $result->totalartmar;
	// 	$unmet = (int) $result->totalartmar - (int) $result->alltests;
	// 	$unmet_p = round((($unmet / (int) $result->totalartmar) * 100),2);
	// 	$data['unmet'] = $unmet_p;

	// 	return $data;
	// }

	// function get_patients_outcomes($site=null,$year=null){
	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($site==null || $site=='null') {
	// 		$site = $this->session->userdata('site_filter');
	// 	}

	// 	$sql = "CALL `proc_get_vl_site_patients`('".$site."','".$year."')";
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->row();
	// 	// echo "<pre>";print_r($result);die();
	// 	$data['categories'] = array('Total Patients', "VL's Done");
	// 	$data['outcomes']['name'] = 'Tests';
	// 	$data['outcomes']['data'][0] = (int) $result->totalartmar;
	// 	$data['outcomes']['data'][1] = (int) $result->alltests;
	// 	$data["outcomes"]["color"] =  '#1BA39C';

	// 	return $data;
	// }

	

	// function get_patients_graph($site=null,$year=null){
	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($site==null || $site=='null') {
	// 		$site = $this->session->userdata('site_filter');
	// 	}

	// 	$sql = "CALL `proc_get_vl_site_patients`('".$site."','".$year."')";
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->row();

	// 	$data['categories'] = array('1 VL', '2 VL', '3 VL', '> 3 VL');
	// 	$data['outcomes']['name'] = 'Tests';
	// 	$data['outcomes']['data'][0] = (int) $result->onevl;
	// 	$data['outcomes']['data'][1] = (int) $result->twovl;
	// 	$data['outcomes']['data'][2] = (int) $result->threevl;
	// 	$data['outcomes']['data'][3] = (int) $result->above3vl;
	// 	$data["outcomes"]["color"] =  '#1BA39C';

	// 	return $data;


	// }


	function site_patients($site=null,$year=null,$month=null,$to_year=NULL,$to_month=NULL)
	{
		$type = 0;

		if ($site==null || $site=='null') {
			$site = $this->session->userdata('site_filter');
		}

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}
		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
				$type = 1;
			}else {
				$month = $this->session->userdata('filter_month');
				$type = 3;
			}
		}
		
		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}

		if ($type == 0) {
			if($to_year == 0){
				$type = 3;
			}
			else{
				$type = 5;
			}
		}		

		$query = $this->db->get_where('facilitys', array('id' => $site), 1)->row();

		$facility = $query->facilitycode;

		$params = "patient/facility/{$facility}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";

		$data = $this->req($params);

		return $data;
	}
}
?>
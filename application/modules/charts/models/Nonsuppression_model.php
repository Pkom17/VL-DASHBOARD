<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
* 
*/
class Nonsuppression_model extends MY_Model
{
	
	function __construct()
	{
		parent:: __construct();
	}

	function notification_bar($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL,$sub_county=NULL)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}
		

		if ($year==null || $year=='null') {
			$year = $this->session->userdata('filter_year');
		}

		$data['year'] = $year;
		$data['month'] = '';

		if ($month==null || $month=='null') {
			if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
				$month = 0;
			}else {
				$month = $this->session->userdata('filter_month');
			}
		}else {
			$data['month'] = ' as of '.$this->resolve_month($month);
		}

		if ($to_year==null || $to_year=='null') {
			$to_year = 0;
		}
		if ($to_month==null || $to_month=='null') {
			$to_month = 0;
		}else {
			$data['month'] .= ' to '.$this->resolve_month($to_month).' of '.$to_year;
		}

		if ($county==null || $county=='null') {
			if ($sub_county==null || $sub_county=='null') {
				$sql = "CALL `proc_get_national_sustxfail_notification`('".$year."','".$month."','".$to_year."','".$to_month."')";
			} else {
				$sql = "CALL `proc_get_subcounty_sustxfail_notification`('".$sub_county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		} else {
			$sql = "CALL `proc_get_regional_sustxfail_notification`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			// $data['county'] = $county;
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();
		
		foreach ($result as $key => $value) {
			$data['rate'] = (int) $value['sustxfail_rate'];
			$data['sustxfail'] = (int) $value['sustxfail'];
			if ((int) $value['sustxfail_rate']=0) {
				$data['color'] = '#E4F1FE';
			} else if ($value['sustxfail_rate']>0 && $value['sustxfail_rate']<10) {
				$data['color'] = '#E4F1FE';
			} else if($value['sustxfail_rate']>=10 && $value['sustxfail_rate']<50) {
				$data['color'] = '#E4F1FE';
			} else if($value['sustxfail_rate']>=50 && $value['sustxfail_rate']<90) {
				$data['color'] = '#E4F1FE';
			} else if($value['sustxfail_rate']>=90 && $value['sustxfail_rate']<100) {
				$data['color'] = '#E4F1FE';
			}
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function gender_group_chart($year=NULL,$month=NULL,$county=NULL,$partner=NULL,$to_year=NULL,$to_month=null,$sub_county=NULL)
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

		if ($partner==null || $partner == 'null') {
			$partner = $this->session->userdata('partner_filter');
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
			$sql = "CALL `proc_get_partner_sustxfail_gender`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($county==null || $county=='null') {
				if ($sub_county==null || $sub_county=='null') {
					// $sql = "CALL `proc_get_national_sustxfail_gender`('".$year."','".$month."','".$to_year."','".$to_month."')";
					$sql = "CALL `proc_get_vl_national_sustxfail_gender`('".$year."','".$month."','".$to_year."','".$to_month."')";
				} else {
					// $sql = "CALL `proc_get_national_sustxfail_gender`('".$year."','".$month."','".$to_year."','".$to_month."')";
					$sql = "CALL `proc_get_vl_subcounty_sustxfail_gender`('".$sub_county."','".$year."','".$month."','".$to_year."','".$to_month."')";
				}
			} else {
				$sql = "CALL `proc_get_vl_county_sustxfail_gender`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
		
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();


		$data['gnd_gr']['name'] = lang('label.non_suppression');
		$data['gnd_gr']['colorByPoint'] = true;
		$female = NULL;
		$male = NULL;
		$nodata = NULL;

		foreach ($result as $key => $value) {
			if ($value['name']=='F') {
				$data['gnd_gr']['data'][$key]['name'] = lang('label.female');
				$data['gnd_gr']['data'][$key]['y'] = (int) $value['nonsuppressed'];
				$female = number_format((int) $value['nonsuppressed']).' ('.round((((int) $value['nonsuppressed'])/@((int) $value['nonsuppressed']+(int) $value['suppressed']))*100,1).'%)';
			} else if ($value['name']=='M') {
				$data['gnd_gr']['data'][$key]['name'] = lang('label.male');
				$data['gnd_gr']['data'][$key]['y'] = (int) $value['nonsuppressed'];
				$male = number_format((int) $value['nonsuppressed']).' ('.round((((int) $value['nonsuppressed'])/@((int) $value['nonsuppressed']+(int) $value['suppressed']))*100,1).'%)';
			} else {
				$data['gnd_gr']['data'][$key]['name'] = $value['name'];
				$data['gnd_gr']['data'][$key]['y'] = (int) $value['nonsuppressed'];
				$nodata = number_format((int) @$value['nonsuppressed']).' ('.round((((int) @$value['nonsuppressed'])/@((int) $value['nonsuppressed']+(int) $value['suppressed']))*100,1).'%)';
			}
		}

		$data['gnd_gr']['data'][0]['sliced'] = true;
		$data['gnd_gr']['data'][0]['selected'] = true;
		$data['gnd_gr']['data'][0]['color'] = '#F2784B';
		$data['gnd_gr']['data'][1]['color'] = '#1BA39C';

		$data['ul'] = '<tr>
		    		<td>'.lang('label.male').'</td>
		    		<td>'.$male.'</td>
		    	</tr>

		    	<tr>
		    		<td>'.lang('label.female').' </td>
		    		<td>'. $female.'</td>
		    	</tr>

		    	<tr>
		    		<td>'.lang('label.no_data').':</td>
		    		<td>'. $nodata.'</td>
		    	</tr>';
		
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function age_group_chart($year=NULL,$month=NULL,$county=NULL,$partner=NULL,$to_year=NULL,$to_month=NULL,$sub_county=NULL)
	{
		if ($county==null || $county=='null') {
			$county = $this->session->userdata('county_filter');
		}

		if ($partner==null || $partner=='null') {
			$partner = $this->session->userdata('partner_filter');
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

		if ($partner) {
			$sql = "CALL `proc_get_partner_sustxfail_age`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($county==null || $county=='null') {
				if ($sub_county==null || $sub_county=='null') {
					// $sql = "CALL `proc_get_national_sustxfail_age`('".$year."','".$month."','".$to_year."','".$to_month."')";
					$sql = "CALL `proc_get_vl_national_sustxfail_age`('".$year."','".$month."','".$to_year."','".$to_month."')";
				} else {
					// $sql = "CALL `proc_get_national_sustxfail_gender`('".$year."','".$month."','".$to_year."','".$to_month."')";
					$sql = "CALL `proc_get_vl_subcounty_sustxfail_age`('".$sub_county."','".$year."','".$month."','".$to_year."','".$to_month."')";
				}
			} else {
				$sql = "CALL `proc_get_vl_county_sustxfail_age`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['age_gr']['name'] = lang('label.non_suppression');
		$data['age_gr']['colorByPoint'] = true;
		$children = 0;
		$childrenTotal = 0;
		$adolescent = 0;
		$adolescentTotal = 0;
		$adults = 0;
		$adultsTotal = 0;
		$nodata = 0;
		$nodataTotal = 0;


		foreach ($result as $key => $value) {
			if ($value['name']=='Less 2'||$value['name']=='2-9') {
				$children += $value['nonsuppressed'];
				$childrenTotal += $value['nonsuppressed']+$value['suppressed'];
			} else if ($value['name']=='10-14'||$value['name']=='15-19') {
				$adolescent += $value['nonsuppressed'];
				$adolescentTotal += $value['nonsuppressed']+$value['suppressed'];
			} else if ($value['name']=='20-24'||$value['name']=='25+') {
				$adults += $value['nonsuppressed'];
				$adultsTotal += $value['nonsuppressed']+$value['suppressed'];
			} else {
				$nodata += $value['nonsuppressed'];
				$nodataTotal += $value['nonsuppressed']+$value['suppressed'];
			}
		}

		$data['age_gr']['data'][0]['name'] = lang('label.children_lt10');
		$data['age_gr']['data'][0]['y'] = (int) $children;
		$data['age_gr']['data'][1]['name'] = lang('label.adolescents_lt19');
		$data['age_gr']['data'][1]['y'] = (int) $adolescent;
		$data['age_gr']['data'][2]['name'] = lang('label.adults_gt20');
		$data['age_gr']['data'][2]['y'] = (int) $adults;
		$data['age_gr']['data'][3]['name'] = lang('label.no_data');
		$data['age_gr']['data'][3]['y'] = (int) $nodata;

		$data['age_gr']['data'][0]['sliced'] = true;
		$data['age_gr']['data'][0]['selected'] = true;
		$data['age_gr']['data'][0]['color'] = '#F2784B';
		$data['age_gr']['data'][1]['color'] = '#1BA39C';
                
                if($childrenTotal == 0){
                     $val_chil = number_format((int) $children).' ('.round(0,1).'%)';
                }else{
                    $val_chil = number_format((int) $children).' ('.round(((int) $children)/((int) $childrenTotal)*100,1).'%)';
                }
                if($adolescentTotal == 0){
                     $val_ado = number_format((int) $adolescent).' ('.round(0,1).'%)';
                }else{
                    $val_ado = number_format((int) $adolescent).' ('.round(((int) $adolescent)/((int) $adolescentTotal)*100,1).'%)';
                }
                if($adultsTotal == 0){
                     $val_adu = number_format((int) $adults).' ('.round(0,1).'%)';
                }else{
                    $val_adu = number_format((int) $adults).' ('.round(((int) $adults)/((int) $adultsTotal)*100,1).'%)';
                }
                if($nodataTotal == 0){
                     $val_nod = number_format((int) $nodata).' ('.round(0,1).'%)';
                }else{
                    $val_nod = number_format((int) $nodata).' ('.round(((int) $nodata)/((int) $nodataTotal)*100,1).'%)';
                }

		$data['ul'] = '<tr>
		    		<td>'.lang('label.children_lt10').': </td>
		    		<td>'.$val_chil.'</td>
		    	</tr>

		    	<tr>
		    		<td>'.lang('label.adolescents_lt19').': </td>
		    		<td>'.$val_ado.'</td>
		    	</tr>

		    	<tr>
		    		<td>'. lang('label.adults_gt20').': </td>
		    		<td>'.$val_adu.'</td>
		    	</tr>

		    	<tr>
		    		<td>'.lang('label.no_data').': </td>
		    		<td>'.$val_nod.'</td>
		    	</tr>';

		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function justifications($year=NULL,$month=NULL,$county=NULL,$partner=NULL,$to_year=NULL,$to_month=NULL,$sub_county=NULL)
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

		if ($partner==null || $partner=='null') {
			$partner = $this->session->userdata('partner_filter');
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
			$sql = "CALL `proc_get_partner_sustxfail_justification`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($county==null || $county=='null') {
				if ($sub_county==null || $sub_county=='null') {
					// $sql = "CALL `proc_get_national_sustxfail_justification`('".$year."','".$month."','".$to_year."','".$to_month."')";
					$sql = "CALL `proc_get_vl_national_sustxfail_justification`('".$year."','".$month."','".$to_year."','".$to_month."')";
				} else {
					// $sql = "CALL `proc_get_national_sustxfail_gender`('".$year."','".$month."','".$to_year."','".$to_month."')";
					$sql = "CALL `proc_get_vl_subcounty_sustxfail_justification`('".$sub_county."','".$year."','".$month."','".$to_year."','".$to_month."')";
				}
			} else {
				$sql = "CALL `proc_get_vl_county_sustxfail_justification`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['sup_justification'][0]['name'] =  lang('label.not_suppressed_');
		$data['sup_justification'][1]['name'] =  lang('label.suppressed_');

		$count = 0;
		
		$data["sup_justification"][0]["data"][0]	= $count;
		$data["sup_justification"][1]["data"][0]	= $count;
		$data['categories'][0]					= lang('label.no_data');

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["sup_justification"][0]["data"][$key]	=  (int) $value['nonsuppressed'];
			$data["sup_justification"][1]["data"][$key]	=  (int) $value['suppressed'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function gender($year=NULL,$month=NULL,$county=NULL,$partner=NULL,$to_year=NULL,$to_month=NULL,$sub_county=NULL)
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

		if ($partner==null || $partner=='null') {
			$partner = $this->session->userdata('partner_filter');
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
			$sql = "CALL `proc_get_partner_sustxfail_gender`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($county==null || $county=='null') {
				if ($sub_county==null || $sub_county=='null') {
					// $sql = "CALL `proc_get_national_sustxfail_gender`('".$year."','".$month."','".$to_year."','".$to_month."')";
					$sql = "CALL `proc_get_vl_national_sustxfail_gender`('".$year."','".$month."','".$to_year."','".$to_month."')";
				} else {
					// $sql = "CALL `proc_get_national_sustxfail_gender`('".$year."','".$month."','".$to_year."','".$to_month."')";
					$sql = "CALL `proc_get_vl_subcounty_sustxfail_gender`('".$sub_county."','".$year."','".$month."','".$to_year."','".$to_month."')";
				}
			} else {
				$sql = "CALL `proc_get_vl_county_sustxfail_gender`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['sup_gender'][0]['name'] =  lang('label.not_suppressed_');
		$data['sup_gender'][1]['name'] =  lang('label.suppressed_');

		$count = 0;
		
		$data["sup_gender"][0]["data"][0]	= $count;
		$data["sup_gender"][1]["data"][0]	= $count;
		$data['categories'][0]					= lang('label.no_data');

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["sup_gender"][0]["data"][$key]	=  (int) $value['nonsuppressed'];
			$data["sup_gender"][1]["data"][$key]	=  (int) $value['suppressed'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function age($year=NULL,$month=NULL,$county=NULL,$partner=NULL,$to_year=NULL,$to_month=NULL,$sub_county=NULL)
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

		if ($partner==null || $partner=='null') {
			$partner = $this->session->userdata('partner_filter');
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
			$sql = "CALL `proc_get_partner_sustxfail_age`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			if ($county==null || $county=='null') {
				if ($sub_county==null || $sub_county=='null') {
					// $sql = "CALL `proc_get_national_sustxfail_age`('".$year."','".$month."','".$to_year."','".$to_month."')";
					$sql = "CALL `proc_get_vl_national_sustxfail_age`('".$year."','".$month."','".$to_year."','".$to_month."')";
				} else {
					// $sql = "CALL `proc_get_national_sustxfail_gender`('".$year."','".$month."','".$to_year."','".$to_month."')";
					$sql = "CALL `proc_get_vl_subcounty_sustxfail_age`('".$sub_county."','".$year."','".$month."','".$to_year."','".$to_month."')";
				}
			} else {
				$sql = "CALL `proc_get_vl_county_sustxfail_age`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
			}
		}
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['sup_age'][0]['name'] =  lang('label.not_suppressed_');
		$data['sup_age'][1]['name'] =  lang('label.suppressed_');

		$count = 0;
		
		$data["sup_age"][0]["data"][0]	= $count;
		$data["sup_age"][1]["data"][0]	= $count;
		$data['categories'][0]					= lang('label.no_data');

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["sup_age"][0]["data"][$key]	=  (int) $value['nonsuppressed'];
			$data["sup_age"][1]["data"][$key]	=  (int) $value['suppressed'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	function county($year=NULL,$month=NULL,$county=NULL,$partner=NULL,$to_year=NULL,$to_month=NULL,$sub_county=NULL)
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

		if ($partner==null || $partner=='null') {
			$partner = $this->session->userdata('partner_filter');
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

		$sql = "CALL `proc_get_counties_sustxfail_stats`('".$year."','".$month."','".$to_year."','".$to_month."')";

		// if ($partner) {
		// 	$sql = "CALL `proc_get_partner_sustxfail_age`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// } else {
		// 	if ($county==null || $county=='null') {
		// 		if ($sub_county==null || $sub_county=='null') {
		// 			// $sql = "CALL `proc_get_national_sustxfail_age`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// 			$sql = "CALL `proc_get_vl_national_sustxfail_age`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// 		} else {
		// 			// $sql = "CALL `proc_get_national_sustxfail_gender`('".$year."','".$month."','".$to_year."','".$to_month."')";
		// 			$sql = "CALL `proc_get_vl_subcounty_sustxfail_age`('".$sub_county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// 		}
		// 	} else {
		// 		$sql = "CALL `proc_get_vl_county_sustxfail_age`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		// 	}
		// }
		// echo "<pre>";print_r($sql);die();
		$result = $this->db->query($sql)->result_array();
		// echo "<pre>";print_r($result);die();

		$data['sup_county'][0]['name'] =  lang('label.not_suppressed_');
		$data['sup_county'][1]['name'] =  lang('label.suppressed_');

		$count = 0;
		
		$data["sup_county"][0]["data"][0]	= $count;
		$data["sup_county"][1]["data"][0]	= $count;
		$data['categories'][0]					= lang('label.no_data');

		foreach ($result as $key => $value) {
			$data['categories'][$key] 					= $value['name'];
			$data["sup_county"][0]["data"][$key]	=  (int) $value['nonsuppressed'];
			$data["sup_county"][1]["data"][$key]	=  (int) $value['suppressed'];
		}
		// echo "<pre>";print_r($data);die();
		return $data;
	}

	// function sampletypes($year=NULL,$month=NULL,$county=NULL,$partner=NULL,$to_year=NULL,$to_month=NULL)
	// {
	// 	if ($county==null || $county=='null') {
	// 		$county = $this->session->userdata('county_filter');
	// 	}
	// 	if ($to_year==null || $to_year=='null') {
	// 		$to_year = 0;
	// 	}
	// 	if ($to_month==null || $to_month=='null') {
	// 		$to_month = 0;
	// 	}

	// 	if (!$partner) {
	// 		$partner = $this->session->userdata('partner_filter');
	// 	}

	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($month==null || $month=='null') {
	// 		if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
	// 			$month = 0;
	// 		}else {
	// 			$month = $this->session->userdata('filter_month');
	// 		}
	// 	}

	// 	if ($partner) {
	// 		$sql = "CALL `proc_get_partner_sustxfail_sampletypes`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
	// 	} else {
	// 		if ($county==null || $county=='null') {
	// 			$sql = "CALL `proc_get_national_sustxfail_sampletypes`('".$year."','".$month."','".$to_year."','".$to_month."')";
	// 		} else {
	// 			$sql = "CALL `proc_get_regional_sustxfail_sampletypes`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
	// 		}
	// 	}
	// 	echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->result_array();
	// 	// echo "<pre>";print_r($result);die();

	// 	$color = array('#6BB9F0', '#F2784B', '#1BA39C');
	// 	$data['sampletype']['name'] = lang('label.non_suppression');
	// 	$data['sampletype']['colorByPoint'] = true;

	// 	$count = 0;

	// 	$data['sampletype']['data'][0]['name'] = lang('label.no_data');

	// 	foreach ($result as $key => $value) {
	// 		$data['sampletype']['data'][$key]['y'] = $count;
			
	// 		$data['sampletype']['data'][$key]['name'] = $value['name'];
	// 		$data['sampletype']['data'][$key]['y'] = (int) $value['sustxfail'];
	// 		$data['sampletype']['data'][$key]['color'] = $color[$key];
	// 	}
		
	// 	$data['sampletype']['data'][0]['sliced'] = true;
	// 	$data['sampletype']['data'][0]['selected'] = true;
	// 	// echo "<pre>";print_r($data);die();
	// 	return $data;
	// }

	// function regimen($year=null,$month=null,$county=null,$partner=NULL,$to_year=NULL,$to_month=null)
	// {
	// 	if ($county==null || $county=='null') {
	// 		$county = $this->session->userdata('county_filter');
	// 	}
	// 	if ($to_year==null || $to_year=='null') {
	// 		$to_year = 0;
	// 	}
	// 	if ($to_month==null || $to_month=='null') {
	// 		$to_month = 0;
	// 	}

	// 	if (!$partner) {
	// 		$partner = $this->session->userdata('partner_filter');
	// 	}

	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($month==null || $month=='null') {
	// 		if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
	// 			$month = 0;
	// 		}else {
	// 			$month = $this->session->userdata('filter_month');
	// 		}
	// 	}

	// 	if ($partner) {
	// 		$sql = "CALL `proc_get_partner_sustxfail_regimen`('".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
	// 	} else {
	// 		if ($county==null || $county=='null') {
	// 			$sql = "CALL `proc_get_national_sustxfail_regimen`('".$year."','".$month."','".$to_year."','".$to_month."')";
	// 		} else {
	// 			$sql = "CALL `proc_get_regional_sustxfail_regimen`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
	// 		}
	// 	}
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->result_array();
	// 	$data['regimen']['name'] = lang('label.non_suppression');
	// 	$data['regimen']['colorByPoint'] = true;

	// 	$count = 0;
	// 	$color = array('#19B5FE', '#E26A6A', '#96281B', '#BE90D4', '#16A085', '#F2784B', '#26C281', '#C8F7C5', '#E9D460', '', '', '', '', '', '', '', '');

	// 	$data['li'] = '';
	// 	$data['regimen']['data'][0]['name'] = lang('label.no_data');
	// 	$data['regimen']['data'][0]['y'] = 0;

	// 	foreach ($result as $key => $value) {
	// 		$data['li'] .= '<a href="#" class="list-group-item"><strong>'.$value['name'].':-></strong>&nbsp;'.$value['sustxfail'].'</a>';
	// 		$data['regimen']['data'][$key]['y'] = $count;
			
	// 		$data['regimen']['data'][$key]['name'] = $value['name'];
	// 		$data['regimen']['data'][$key]['y'] = (int) $value['sustxfail'];
	// 		$data['regimen']['data'][$key]['color'] = $color[$key];
	// 	}

	// 	$data['regimen']['data'][0]['sliced'] = true;
	// 	$data['regimen']['data'][0]['selected'] = true;
	// 	// echo "<pre>";print_r($data);die();
	// 	return $data;
	// }

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

		$sql = "CALL `proc_get_counties_sustxfail`('".$year."','".$month."','".$to_year."','".$to_month."')";
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
					$li .= '<a href="javascript:void(0);" class="list-group-item" ><strong>'.$count.'.</strong>&nbsp;'.$value['name'].':&nbsp;'.round($value['sustxfail'],1).'%</a>';
				}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.round($value['sustxfail'],1).'%</td>';
					$table .= '</tr>';
					$count++;
			}
		}else{
			$li = lang('label.no_data');
		}
		
		// if($result)
		// 	{
		// 		if ($county==null || $county=='null') {
		// 			foreach ($result as $key => $value)
		// 				{
		// 					if ($count<16) {
		// 						$li .= '<a href="javascript:void(0);" class="list-group-item" onclick="county_filter('.$value['ID'].')"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].':&nbsp;'.(int)$value['sustxfail'].'%</a>';
		// 					}
		// 					// else {
		// 					// 	exit();
		// 					// }
		// 					$count++;
		// 				}
		// 		} else {
		// 			foreach ($result as $key => $value)
		// 				{
		// 					if ($count<16) {
		// 						if ($county == $value['ID']) {
		// 							$li .= '<pre><strong><a href="javascript:void(0);" class="list-group-item" onclick="county_filter('.$value['ID'].')">'.$count.'.&nbsp;'.$value['name'].':&nbsp;'.(int)$value['sustxfail'].'%</a></strong></pre>';
		// 							$listed = TRUE;
		// 						} else {
		// 							$li .= '<a href="javascript:void(0);" class="list-group-item" onclick="county_filter('.$value['ID'].')"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].':&nbsp;'.(int)$value['sustxfail'].'%</a>';
		// 						}
		// 					}
		// 					// else {
		// 					// 	exit();
		// 					// }
		// 					$count++;
		// 				}
		// 			if(!$listed)
		// 				{
		// 					$count = 1;
		// 					foreach ($result as $key => $value)
		// 						{
		// 							if ($county == $value['ID'])
		// 								{
		// 									$li .= '<pre><strong><a href="javascript:void(0);" class="list-group-item" onclick="county_filter('.$value['ID'].')">'.$count.'.&nbsp;'.$value['name'].':&nbsp;'.(int)$value['sustxfail'].'%</a></strong></pre>';
		// 								}
		// 							$count++;
		// 						}
		// 				}
					
		// 		}
		// 	}else{
		// 		$li = lang('label.no_data');
		// 	}
		
		// echo "<pre>";print_r($li);die();
		$data = array(
						'ul' => $li,
						'table' => $table);
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
			$sql = "CALL `proc_get_national_sustxfail_subcounty`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_county_sustxfail_subcounty`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
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
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].'.&nbsp;'.(int) $value['percentages'].'%</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.(int) $value['percentages'].'%</td>';
					$table .= '</tr>';
					$count++;
				}
			}else{
				$li = lang('label.no_data');
			}

		$data = array(
					'ul' => $li,
					'table' => $table);
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
			$sql = "CALL `proc_get_national_sustxfail_partner`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_regional_sustxfail_partner`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
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
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].'.&nbsp;'.(int) $value['percentages'].'%</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.(int) $value['percentages'].'%</td>';
					$table .= '</tr>';
				$count++;
				}
			}else{
				$li = lang('label.no_data');
			}

		$data = array(
					'ul' => $li,
					'table' => $table);
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
			$sql = "CALL `proc_get_sites_listing`('".$year."','".$month."','".$to_year."','".$to_month."')";
		} else {
			$sql = "CALL `proc_get_county_sites_listing`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
		}
		
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
						$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].'.&nbsp;'.(int) $value['non supp'].'%</a>';
					}
					$table .= '<tr>';
					$table .= '<td>'.$count.'</td>';
					$table .= '<td>'.$value['name'].'</td>';
					$table .= '<td>'.(int) $value['non supp'].'%</td>';
					$table .= '</tr>';
					$count++;
				}
			}else{
				$li = lang('label.no_data');
			}
			$data = array(
						'ul' => $li,
						'table' => $table);
		return $data;
	}


	// function regimen_listing($year=null,$month=null,$county=null,$to_year=NULL,$to_month=null)
	// {
	// 	if ($county==null || $county=='null') {
	// 		$county = $this->session->userdata('county_filter');
	// 	}
	// 	if ($to_year==null || $to_year=='null') {
	// 		$to_year = 0;
	// 	}
	// 	if ($to_month==null || $to_month=='null') {
	// 		$to_month = 0;
	// 	}

	// 	if ($year==null || $year=='null') {
	// 		$year = $this->session->userdata('filter_year');
	// 	}
	// 	if ($month==null || $month=='null') {
	// 		if ($this->session->userdata('filter_month')==null || $this->session->userdata('filter_month')=='null') {
	// 			$month = 0;
	// 		}else {
	// 			$month = $this->session->userdata('filter_month');
	// 		}
	// 	}
	// 	if ($county==null || $county=='null') {
	// 		$sql = "CALL `proc_get_national_sustxfail_rank_regimen`('".$year."','".$month."','".$to_month."')";
	// 	} else {
	// 		$sql = "CALL `proc_get_regional_sustxfail_rank_regimen`('".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
	// 	}
	// 	// echo "<pre>";print_r($sql);die();
	// 	$result = $this->db->query($sql)->result_array();

	// 	$li = '';
	// 	$count = 1;
	// 	if($result)
	// 		{
	// 			foreach ($result as $key => $value) {
	// 				$li .= '<a href="#" class="list-group-item"><strong>'.$count.'.</strong>&nbsp;'.$value['name'].':&nbsp;'.$value['sustxfail'].'</a>';
	// 				$count++;
	// 			}
	// 		}else{
	// 			$li = lang('label.no_data');
	// 		}

	// 	return $li;
	// }
}
?>
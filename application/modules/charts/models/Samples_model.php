<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 
 */
class Samples_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    function samples_outcomes($year = NULL, $month = NULL, $to_year = null, $to_month = null, $partner = null) {
        //return "this";

        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }
        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }
        if ($month == null || $month == 'null') {
            if ($this->session->userdata('filter_month') == null || $this->session->userdata('filter_month') == 'null') {
                $month = 0;
            } else {
                $month = $this->session->userdata('filter_month');
            }
        }

        if ($partner == null || $partner == 'null') {
            $sql = "CALL `proc_get_vl_samples_outcomes`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            $sql = "CALL `proc_get_vl_partner_samples_outcomes`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        }
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $data['county_outcomes'][0]['name'] = lang('label.not_suppressed_');
        $data['county_outcomes'][1]['name'] = lang('label.suppressed_');

        $count = 0;

        $data["county_outcomes"][0]["data"][0] = $count;
        $data["county_outcomes"][1]["data"][0] = $count;
        $data['categories'][0] = lang('label.no_data');

        foreach ($result as $key => $value) {
            $data['categories'][$key] = $value['name'];
            $data["county_outcomes"][0]["data"][$key] = (int) $value['nonsuppressed'];
            $data["county_outcomes"][1]["data"][$key] = (int) $value['suppressed'];
        }
        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function samples_vl_outcomes($year = NULL, $month = NULL, $sample = NULL, $to_year = null, $to_month = null, $partner = null) {

        if ($sample == null || $sample == 'null') {
            $sample = $this->session->userdata('sample_filter');
        }
        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }
        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }
        if ($month == null || $month == 'null') {
            if ($this->session->userdata('filter_month') == null || $this->session->userdata('filter_month') == 'null') {
                $month = 0;
            } else {
                $month = $this->session->userdata('filter_month');
            }
        }

        if ($partner == null || $partner == 'null') {
            $sql = "CALL `proc_get_vl_samples_vl_outcomes`('" . $sample . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            $sql = "CALL `proc_get_vl_partner_samples_vl_outcomes`('" . $partner . "','" . $sample . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
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
            $total = (int) ($value['undetected'] + $value['less1000'] + $value['less5000'] + $value['above5000']);
            $less = (int) ($value['undetected'] + $value['less1000']);
            $greater = (int) ($value['less5000'] + $value['above5000']);

            $data['ul'] .= '<tr>
	    		<td colspan="2">' . lang('label.tests_with_valid_outcomes') . '</td>
	    		<td colspan="2">' . number_format($total) . '</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . lang('label.valid_tests_gt1000') . '</td>
	    		<td>' . number_format($greater) . '</td>
	    		<td>' . lang('label.percentage_non_suppression') . '</td>
	    		<td>' . @round((($greater / $total) * 100), 1) . '%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . lang('label.valid_tests_lt1000') . '</td>
	    		<td>' . number_format($less) . '</td>
	    		<td>' . lang('label.percentage_suppression') . '</td>
	    		<td>' . @round((($less / $total) * 100), 1) . '%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;' . lang('label.baseline_vl') . '</td>
	    		<td>' . number_format($value['baseline']) . '</td>
	    		<td>' . lang('label.non_suppression_gt_1000') . '</td>
	    		<td>' . number_format($value['baselinesustxfail']) . ' (' . @round(($value['baselinesustxfail'] * 100 / $value['baseline']), 1) . '%)' . '</td>
	    	</tr>
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;Confirmatory Repeat Tests:</td>
	    		<td>' . number_format($value['confirmtx']) . '</td>
	    		<td>' . lang('label.non_suppression_gt_1000') . '</td>
	    		<td>' . number_format($value['confirm2vl']) . ' (' . @round(($value['confirm2vl'] * 100 / $value['confirmtx']), 1) . '%)' . '</td>
	    	</tr>
 
	    	<tr>
	    		<td>' . lang('label.rejected_samples') . '</td>
	    		<td>' . number_format($value['rejected']) . '</td>
	    		<td>' . lang('label.percentage_rejection_rate') . '</td>
	    		<td>' . @round((($value['rejected'] * 100) / $value['alltests']), 1, PHP_ROUND_HALF_UP) . '%</td>
	    	</tr>';

            $data['vl_outcomes']['data'][0]['y'] = (int) $value['undetected'] + (int) $value['less1000'];
            $data['vl_outcomes']['data'][1]['y'] = (int) $value['less5000'] + (int) $value['above5000'];

            $data['vl_outcomes']['data'][0]['color'] = '#1BA39C';
            $data['vl_outcomes']['data'][1]['color'] = '#F2784B';
        }
        $data['vl_outcomes']['data'][0]['sliced'] = true;
        $data['vl_outcomes']['data'][0]['selected'] = true;

        return $data;
    }

    function samples_gender($year = NULL, $month = NULL, $sample = NULL, $to_year = null, $to_month = null, $partner = null) {

        if ($sample == null || $sample == 'null') {
            $sample = $this->session->userdata('sample_filter');
        }
        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }
        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($month == null || $month == 'null') {
            if ($this->session->userdata('filter_month') == null || $this->session->userdata('filter_month') == 'null') {
                $month = 0;
            } else {
                $month = $this->session->userdata('filter_month');
            }
        }

        if ($partner == null || $partner == 'null') {
            $sql = "CALL `proc_get_vl_samples_gender`('" . $sample . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            $sql = "CALL `proc_get_vl_partner_samples_gender`('" . $partner . "','" . $sample . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        }
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $data['gender'][0]['name'] = lang('label.test');

        $count = 0;

        $data["gender"][0]["data"][0] = $count;
        $data["gender"][0]["data"][1] = $count;
        $data['categories'][0] = lang('label.no_data');

        foreach ($result as $key => $value) {
            $data['categories'][0] = lang('label.male');
            $data['categories'][1] = lang('label.female');
            $data["gender"][0]["data"][0] = (int) $value['maletest'];
            $data["gender"][0]["data"][1] = (int) $value['femaletest'];
        }

        // $data['gender'][0]['drilldown']['color'] = '#913D88';
        // $data['gender'][0]['drilldown']['color'] = '#913D88';

        return $data;
    }

    function samples_age($year = NULL, $month = NULL, $sample = NULL, $to_year = null, $to_month = null, $partner = null) {

        if ($sample == null || $sample == 'null') {
            $sample = $this->session->userdata('sample_filter');
        }
        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }
        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }
        if ($month == null || $month == 'null') {
            if ($this->session->userdata('filter_month') == null || $this->session->userdata('filter_month') == 'null') {
                $month = 0;
            } else {
                $month = $this->session->userdata('filter_month');
            }
        }

        if ($partner == null || $partner == 'null') {
            $sql = "CALL `proc_get_vl_samples_age`('" . $sample . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            $sql = "CALL `proc_get_vl_partner_samples_age`('" . $partner . "','" . $sample . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        }
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $data['ageGnd'][0]['name'] = lang('label.test');

        $count = 0;

        $data["ageGnd"][0]["data"][0] = $count;
        $data["ageGnd"][0]["data"][1] = $count;
        $data['categories'][0] = lang('label.no_data');

        foreach ($result as $key => $value) {
            $data['categories'][0] = lang('label.no_age');
            $data['categories'][1] = lang('label.less2');
            $data['categories'][2] = lang('label.less9');
            $data['categories'][3] = lang('label.less14');
            $data['categories'][4] = lang('label.less19');
            $data['categories'][5] = lang('label.less24');
            $data['categories'][6] = lang('label.over25');
            $data["ageGnd"][0]["data"][0] = (int) $value['noage'];
            $data["ageGnd"][0]["data"][1] = (int) $value['less2'];
            $data["ageGnd"][0]["data"][2] = (int) $value['less9'];
            $data["ageGnd"][0]["data"][3] = (int) $value['less14'];
            $data["ageGnd"][0]["data"][4] = (int) $value['less19'];
            $data["ageGnd"][0]["data"][5] = (int) $value['less24'];
            $data["ageGnd"][0]["data"][6] = (int) $value['over25'];
        }
        // $data['gender'][0]['drilldown']['color'] = '#913D88';
        // $data['gender'][0]['drilldown']['color'] = '#913D88';

        return $data;
    }

    function samples_suppression($year = NULL, $sample = NULL) {

        if ($sample == null || $sample == 'null') {
            $sample = $this->session->userdata('sample_filter');
        }

        if ($year == null || $year == 'null') {
            $to = $this->session->userdata('filter_year');
        } else {
            $to = $year;
        }
        $from = $to - 1;

        //if ($partner==null || $partner=='null') {
        $sql = "CALL `proc_get_vl_sample_summary`('" . $sample . "','" . $from . "','" . $to . "')";
        /* } else {
          $sql = "CALL `proc_get_vl_partner_samples_sample_types`('".$partner."','".$sample."','".$from."')";
          $sql2 = "CALL `proc_get_vl_partner_samples_sample_types`('".$partner."','".$sample."','".$to."')";
          } */

        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        $data['outcomes'][0]['name'] = lang('label.non_suppressed_');
        $data['outcomes'][1]['name'] = lang('label.suppressed_');
        $data['outcomes'][2]['name'] = lang('label.suppression');


        //$data['outcomes'][0]['drilldown']['color'] = '#913D88';
        //$data['outcomes'][1]['drilldown']['color'] = '#96281B';
        //$data['outcomes'][2]['color'] = '#257766';

        $data['outcomes'][0]['type'] = "column";
        $data['outcomes'][1]['type'] = "column";
        $data['outcomes'][2]['type'] = "spline";

        $data['outcomes'][0]['yAxis'] = 1;
        $data['outcomes'][1]['yAxis'] = 1;

        $data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

        $data['title'] = lang('label.outcomes');

        foreach ($result as $key => $value) {

            $data['categories'][$key] = $this->resolve_month($value['month']) . '-' . $value['year'];
            $data['outcomes'][0]['data'][$key] = (int) $value['nonsuppressed'];
            $data['outcomes'][1]['data'][$key] = (int) $value['suppressed'];

            $data['outcomes'][2]['data'][$key] = round(@(((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            //$data['outcomes'][2]['data'][$key] = round($value['percentage'], 2);
        }

        return $data;
    }

    function county_outcomes($year = null, $month = null, $sample = null, $to_year = null, $to_month = null, $partner = null) {


        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }
        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }
        //Assigning the value of the month or setting it to the selected value
        if ($month == null || $month == 'null') {
            if ($this->session->userdata('filter_month') == null || $this->session->userdata('filter_month') == 'null') {
                $month = 0;
            } else {
                $month = $this->session->userdata('filter_month');
            }
        }

        if ($sample == null || $sample == 'null') {
            $sample = $this->session->userdata('sample_filter');
        }

        if ($partner == null || $partner == 'null') {
            $sql = "CALL `proc_get_vl_county_samples_outcomes`('" . $sample . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            $sql = "CALL `proc_get_vl_partner_county_samples_outcomes`('" . $partner . "','" . $sample . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        }

        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $data['outcomes'][0]['name'] = lang('label.non_suppressed_');
        $data['outcomes'][1]['name'] = lang('label.suppressed_');
        $data['outcomes'][2]['name'] = lang('label.suppression');


        //$data['outcomes'][0]['drilldown']['color'] = '#913D88';
        //$data['outcomes'][1]['drilldown']['color'] = '#96281B';
        //$data['outcomes'][2]['color'] = '#257766';

        $data['outcomes'][0]['type'] = "column";
        $data['outcomes'][1]['type'] = "column";
        $data['outcomes'][2]['type'] = "spline";

        $data['outcomes'][0]['yAxis'] = 1;
        $data['outcomes'][1]['yAxis'] = 1;

        $data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

        $data['title'] = lang('label.outcomes');




        foreach ($result as $key => $value) {
            $data['categories'][$key] = $value['name'];

            $data['outcomes'][0]['data'][$key] = (int) $value['nonsuppressed'];
            $data['outcomes'][1]['data'][$key] = (int) $value['suppressed'];

            $data['outcomes'][2]['data'][$key] = round(@(((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            //$data['outcomes'][2]['data'][$key] = round($value['percentage'], 2);
        }
        // echo "<pre>";print_r($data);die();
        return $data;
    }

}

?>
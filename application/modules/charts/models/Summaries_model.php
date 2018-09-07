<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Summaries_model extends MY_Model {

    function __construct() {
        parent:: __construct();
    }

    function turnaroundtime($year = null, $month = null, $county = null, $to_year = null, $to_month = null) {
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

        $sql = "CALL `proc_get_national_tat`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $count = 0;
        $tat1 = 0;
        $tat2 = 0;
        $tat3 = 0;
        $tat4 = 0;
        $tat = array();

        foreach ($result as $key => $value) {
            if (($value['tat1'] != 0) || ($value['tat2'] != 0) || ($value['tat3'] != 0) || ($value['tat4'] != 0)) {
                $count++;

                $tat1 = $tat1 + $value['tat1'];
                $tat2 = $tat2 + $value['tat2'];
                $tat3 = $tat3 + $value['tat3'];
                $tat4 = $tat4 + $value['tat4'];
            }
        }
        $tat[] = array(
            'tat1' => $tat1,
            'tat2' => $tat2,
            'tat3' => $tat3,
            'tat4' => $tat4,
            'count' => $count
        );
        //echo "<pre>";print_r($tat);die();
        foreach ($tat as $key => $value) {
            if ($value['count'] != 0) {
                $data['tat1'] = round(($value['tat1'] / $value['count']));
                $data['tat2'] = round(($value['tat2'] / $value['count']) + $data['tat1']);
                $data['tat3'] = round(($value['tat3'] / $value['count']) + $data['tat2']);
                $data['tat4'] = round(($value['tat4'] / $value['count']));
            } else {
                $data['tat1'] = null;
                $data['tat2'] = null;
                $data['tat3'] = null;
                $data['tat4'] = null;
            }
        }
        //echo "<pre>";print_r($data);die();
        return $data;
    }

    function vl_coverage($type = null, $ID = null) {
        $sql = "CALL `proc_get_vl_current_suppression`('" . $type . "','" . $ID . "')";
        $result = $this->db->query($sql)->result_array();
        $uniquepts = 0;
        $totalasatmar = 0;
        $vl_coverage = 0;

        foreach ($result as $key => $value) {
            $data['coverage'] = @(int) ((($value['suppressed'] + $value['nonsuppressed']) / $value['totallstrpt']) * 100);
            if ($data['coverage'] < 51) {
                $data['color'] = 'rgba(255,0,0,0.5)';
            } else if ($data['coverage'] > 50 && $data['coverage'] < 71) {
                $data['color'] = 'rgba(255,255,0,0.5)';
            } else if ($data['coverage'] > 70) {
                $data['color'] = 'rgba(0,255,0,0.5)';
            }
        }
        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function county_outcomes($year = null, $month = null, $pfil = null, $partner = null, $county = null, $to_year = null, $to_month = null) {
        // echo "Year:".$year.":--: Month:".$month.":--: County:".$county.":--: Partner:".$partner.":--: pfil:".$pfil;die();
        //Initializing the value of the Year to the selected year or the default year which is current year
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

        // Assigning the value of the county
        if ($county == null || $county == 'null') {
            $county = $this->session->userdata('county_filter');
        }

        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_filter');
        }

        // echo "PFil: ".$pfil." --Partner: ".$partner." -- County: ".$county;
        if ($county) {
            $sql = "CALL `proc_get_county_sites_outcomes`('" . $county . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            if ($pfil == 1) {
                if ($partner) {
                    $sql = "CALL `proc_get_partner_sites_outcomes`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
                } else {
                    $sql = "CALL `proc_get_partner_outcomes`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
                }
            } else {
                $sql = "CALL `proc_get_county_outcomes`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            }
        }
        // echo "<pre>";print_r($sql);echo "</pre>";die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();

        $data['outcomes'][0]['name'] = lang('label.not_suppressed_');
        $data['outcomes'][1]['name'] = lang('label.suppressed_');
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
            $data['categories'][$key] = $value['name'];
            $data['outcomes'][0]['data'][$key] = (int) $value['nonsuppressed'];
            $data['outcomes'][1]['data'][$key] = (int) $value['suppressed'];
            $data['outcomes'][2]['data'][$key] = round(@(((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
        }
        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function vl_outcomes($year = null, $month = null, $county = null, $partner = null, $to_year = null, $to_month = null) {
        if ($county == null || $county == 'null') {
            $county = $this->session->userdata('county_filter');
        }
        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_filter');
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
                $month = $this->session->userdata('filter_month');
            } else {
                $month = 0;
            }
        }

        if ($partner) {
            $sql = "CALL `proc_get_partner_vl_outcomes`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            $sql2 = "CALL `proc_get_partner_sitessending`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            $sql3 = "CALL `proc_get_vl_current_suppression`('3','" . $partner . "')";
        } else {
            if ($county == null || $county == 'null') {
                $sql = "CALL `proc_get_national_vl_outcomes`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
                $sql2 = "CALL `proc_get_national_sitessending`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
                $sql3 = "CALL `proc_get_vl_current_suppression`('0','0')";
            } else {
                $sql = "CALL `proc_get_regional_vl_outcomes`('" . $county . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
                $sql2 = "CALL `proc_get_regional_sitessending`('" . $county . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
                $sql3 = "CALL `proc_get_vl_current_suppression`('1','" . $county . "')";
            }
        }
        // echo "<pre>";print_r($sql);echo "</pre>";
        // echo "<pre>";print_r($sql2);echo "</pre>";die();
        $result = $this->db->query($sql)->result_array();
        $this->db->close();
        $sitessending = $this->db->query($sql2)->result_array();
        $this->db->close();
        // echo "<pre>";print_r($result);echo "</pre>";
        // echo "<pre>";print_r($sitessending);echo "</pre>";die();
        //$color = array('#6BB9F0', '#F2784B', '#1BA39C', '#5C97BF');
        $color = array('#6BB9F0', '#f91d0f', '#96281B', '#5C97BF');

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
            $non_suppressed = $greater + (int) $value['confirm2vl'];
            $total_tests = (int) $value['confirmtx'] + $total + (int) $value['baseline'];

            // 	<td colspan="2">Cumulative Tests (All Samples Run):</td>
            // 	<td colspan="2">'.number_format($value['alltests']).'</td>
            // </tr>
            // <tr>
            /*
             * Test values to avoid PHP error
             */
            if ($total_tests == 0) {
                $val_ns = number_format($non_suppressed) . ' (' . round((0), 1) . '%)';
            } else {
                $val_ns = number_format($non_suppressed) . ' (' . round((($non_suppressed / $total_tests ) * 100), 1) . '%)';
            }
            if ($total == 0) {
                $val_ls = round((0), 1);
                $val_gt = round((0), 1);
            } else {
                $val_ls = round((($less / $total) * 100), 1);
                $val_gt = round((($greater / $total) * 100), 1);
            }
            if ($value['baseline'] == 0) {
                $val_bl = number_format($value['baselinesustxfail']) . ' (' . round((0), 1) . '%)';
            } else {
                $val_bl = number_format($value['baselinesustxfail']) . ' (' . round(($value['baselinesustxfail'] * 100 / $value['baseline']), 1) . '%)';
            }
            if ($value['confirmtx'] == 0) {
                $val_cvl = number_format($value['confirm2vl']) . ' (' . round((0), 1) . '%)';
            } else {
                $val_cvl = number_format($value['confirm2vl']) . ' (' . round(($value['confirm2vl'] * 100 / $value['confirmtx']), 1) . '%)';
            }
            if ($value['received'] == 0) {
                $val_rej = $val_gt = round((0), 1);
            } else {
                $val_rej = round((($value['rejected'] * 100) / $value['received']), 1, PHP_ROUND_HALF_UP);
            }
            /*
             * End Test
             */
            $data['ul'] .= '
			<tr>
	    		<td>' . lang('label.total_vl_tests_done') . '</td>
	    		<td>' . number_format($total_tests) . '</td>
	    		<td>' . lang('label.non_suppression') . '</td>
	    		<td>' . $val_ns . '</td>
	    	</tr>
 
			<tr>
	    		<td colspan="2">&nbsp;&nbsp;&nbsp;' . lang('label.routine_vl_tests_valid_outcomes') . '</td>
	    		<td colspan="2">' . number_format($total) . '</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . lang('label.valid_tests_gt1000') . '</td>
	    		<td>' . number_format($greater) . '</td>
	    		<td>' . lang('label.percentage_non_suppression') . '</td>
	    		<td>' . $val_gt . '%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . lang('label.valid_tests_lt1000') . '</td>
	    		<td>' . number_format($less) . '</td>
	    		<td>' . lang('label.percentage_suppression') . '</td>
	    		<td>' . $val_ls . '%</td>
	    	</tr>
 
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;' . lang('label.baseline_vl') . '</td>
	    		<td>' . number_format($value['baseline']) . '</td>
	    		<td>' . lang('label.non_suppression_gt_1000') . '</td>
	    		<td>' . $val_bl . '</td>
	    	</tr>
	    	<tr>
	    		<td>&nbsp;&nbsp;&nbsp;' . lang('label.confirmatory_repeat_tests') . '</td>
	    		<td>' . number_format($value['confirmtx']) . '</td>
	    		<td>' . lang('label.non_suppression') . '</td>
	    		<td>' . $val_cvl . '</td>
	    	</tr>
 
	    	<tr>
	    		<td>' . lang('label.rejected_samples') . '</td>
	    		<td>' . number_format($value['rejected']) . '</td>
	    		<td>' . lang('label.percentage_rejection_rate') . '</td>
	    		<td>' . $val_rej . '%</td>
	    	</tr>';

            $data['vl_outcomes']['data'][0]['y'] = (int) $value['undetected'] + (int) $value['less1000'];
            $data['vl_outcomes']['data'][1]['y'] = (int) $value['less5000'] + (int) $value['above5000'];

            //$data['vl_outcomes']['data'][0]['color'] = '#1BA39C';
            //$data['vl_outcomes']['data'][1]['color'] = '#F2784B';
            $data['vl_outcomes']['data'][0]['color'] = '#2f80d1 ';
            $data['vl_outcomes']['data'][1]['color'] = '#e8ee1d';
        }

        $count = 0;
        $sites = 0;
        foreach ($sitessending as $key => $value) {
            if ((int) $value['sitessending'] != 0) {
                $sites = (int) $sites + (int) $value['sitessending'];
                $count++;
            }
        }
        // echo "<pre>";print_r($sites);echo "<pre>";print_r($count);echo "<pre>";print_r(round(@$sites / $count));die();
        if ($count != 0) {
            $data['ul'] .= "<tr> <td colspan=2>" . lang('label.average_sites_sending') . "</td><td colspan=2>" . number_format(round($sites / $count)) . "</td></tr>";
        } else {
            $data['ul'] .= "<tr> <td colspan=2>" . lang('label.average_sites_sending') . "</td><td colspan=2>" . number_format(round(0)) . "</td></tr>";
        }
        $count = 1;
        $sites = 0;

        $data['vl_outcomes']['data'][0]['sliced'] = true;
        $data['vl_outcomes']['data'][0]['selected'] = true;

        return $data;
    }

    function justification($year = null, $month = null, $county = null, $partner = null, $to_year = null, $to_month = null) {
        if ($county == null || $county == 'null') {
            $county = $this->session->userdata('county_filter');
        }
        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_filter');
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
                $month = $this->session->userdata('filter_month');
            } else {
                $month = 0;
            }
        }

        if ($partner) {
            $sql = "CALL `proc_get_partner_justification`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            if ($county == null || $county == 'null') {
                $sql = "CALL `proc_get_national_justification`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            } else {
                $sql = "CALL `proc_get_regional_justification`('" . $county . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            }
        }
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        $data['justification']['name'] = lang('label.tests');
        $data['justification']['colorByPoint'] = true;

        $count = 0;

        $data['justification']['data'][0]['name'] = lang('label.no_data');

        foreach ($result as $key => $value) {
            if ($value['name'] == lang('label.routine_vl')) {
                $data['justification']['data'][$key]['color'] = '#5C970F';
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

    function justification_breakdown($year = null, $month = null, $county = null, $partner = null, $to_year = null, $to_month = null) {
        if ($county == null || $county == 'null') {
            $county = $this->session->userdata('county_filter');
        }
        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_filter');
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
            $month = $this->session->userdata('filter_month');
        }

        if ($partner) {
            // $sql = "CALL `proc_get_partner_justification_breakdown`('6','".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";
            // $sql2 = "CALL `proc_get_partner_justification_breakdown`('9','".$partner."','".$year."','".$month."','".$to_year."','".$to_month."')";

            $sql = "CALL `proc_get_vl_pmtct`('1','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','','','" . $partner . "','','')";
            $sql2 = "CALL `proc_get_vl_pmtct`('2','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','','','" . $partner . "','','')";
        } else {
            if ($county == null || $county == 'null') {
                // $sql = "CALL `proc_get_national_justification_breakdown`('6','".$year."','".$month."','".$to_year."','".$to_month."')";
                // $sql2 = "CALL `proc_get_national_justification_breakdown`('9','".$year."','".$month."','".$to_year."','".$to_month."')";

                $sql = "CALL `proc_get_vl_pmtct`('1','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','1','','','','')";
                $sql2 = "CALL `proc_get_vl_pmtct`('2','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','1','','','','')";
            } else {
                // $sql = "CALL `proc_get_regional_justification_breakdown`('6','".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";
                // $sql2 = "CALL `proc_get_regional_justification_breakdown`('9','".$county."','".$year."','".$month."','".$to_year."','".$to_month."')";

                $sql = "CALL `proc_get_vl_pmtct`('1','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','','" . $county . "','','','')";
                $sql2 = "CALL `proc_get_vl_pmtct`('2','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','','" . $county . "','','','')";
            }
        }
        // echo "<pre>";print_r($sql);
        // echo "<pre>";print_r($sql2);die();

        $preg_mo = $this->db->query($sql)->result_array();
        $this->db->close();
        $lac_mo = $this->db->query($sql2)->result_array();
        // echo "<pre>";print_r($preg_mo);echo "</pre>";
        // echo "<pre>";print_r($lac_mo);die();
        $data['just_breakdown'][0]['name'] = lang('label.not_suppressed_');
        $data['just_breakdown'][1]['name'] = lang('label.suppressed_');

        $count = 0;

        $data["just_breakdown"][0]["data"][0] = $count;
        $data["just_breakdown"][1]["data"][0] = $count;
        $data['categories'][0] = lang('label.no_data');

        foreach ($preg_mo as $key => $value) {
            $data['categories'][0] = lang('label.pregnant_mothers');
            $data["just_breakdown"][0]["data"][0] = (int) $value['less5000'] + (int) $value['above5000'];
            $data["just_breakdown"][1]["data"][0] = (int) $value['undetected'] + (int) $value['less1000'];
        }

        foreach ($lac_mo as $key => $value) {
            $data['categories'][1] = lang('label.lactating_mothers');
            $data["just_breakdown"][0]["data"][1] = (int) $value['less5000'] + (int) $value['above5000'];
            $data["just_breakdown"][1]["data"][1] = (int) $value['undetected'] + (int) $value['less1000'];
        }

        //$data['just_breakdown'][0]['drilldown']['color'] = '#913D88';
        //$data['just_breakdown'][1]['drilldown']['color'] = '#96281B';
        $data['just_breakdown'][0]['drilldown']['color'] = '#2f80d1';
        $data['just_breakdown'][1]['drilldown']['color'] = '#e8ee1d';

        return $data;
    }

    function age($year = null, $month = null, $county = null, $partner = null, $to_year = null, $to_month = null) {
        if ($county == null || $county == 'null') {
            $county = $this->session->userdata('county_filter');
        }
        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_filter');
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
                $month = $this->session->userdata('filter_month');
            } else {
                $month = 0;
            }
        }

        if ($partner) {
            $sql = "CALL `proc_get_partner_age`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            if ($county == null || $county == 'null') {
                $sql = "CALL `proc_get_national_age`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            } else {
                $sql = "CALL `proc_get_regional_age`('" . $county . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            }
        }
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

        $data["ageGnd"][0]["data"][0] = $count;
        $data["ageGnd"][1]["data"][0] = $count;
        $data['categories'][0] = lang('label.no_data');

        foreach ($result as $key => $value) {
            if ($value['name'] == lang('label.no_data')) {
                $loop = $key;
                $name = $value['name'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
            } else if ($value['name'] == lang('label.less2')) {
                $loop = $key;
                $name = $value['name'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
            } else if ($value['name'] == lang('label.less9')) {
                $loop = $key;
                $name = $value['name'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
            } else if ($value['name'] == lang('label.less14')) {
                $loop = $key;
                $name = $value['name'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
            } else if ($value['name'] == lang('label.less19')) {
                $loop = $key;
                $name = $value['name'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
            } else if ($value['name'] == lang('label.less24')) {
                $loop = $key;
                $name = $value['name'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
            } else if ($value['name'] == lang('label.over25')) {
                $loop = $key;
                $name = $value['name'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
            }

            $data['categories'][$loop] = $name;
            $data["ageGnd"][0]["data"][$loop] = (int) $nonsuppressed;
            $data["ageGnd"][1]["data"][$loop] = (int) $suppressed;
        }
        // die();
        //$data['ageGnd'][0]['drilldown']['color'] = '#913D88';
        //$data['ageGnd'][1]['drilldown']['color'] = '#96281B';
        $data['ageGnd'][0]['drilldown']['color'] = '#2f80d1';
        $data['ageGnd'][1]['drilldown']['color'] = '#e8ee1d';


        // echo "<pre>";print_r($data);die();
        $data['categories'] = array_values($data['categories']);
        $data["ageGnd"][0]["data"] = array_values($data["ageGnd"][0]["data"]);
        $data["ageGnd"][1]["data"] = array_values($data["ageGnd"][1]["data"]);
        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function age_breakdown($year = null, $month = null, $county = null, $partner = null, $to_year = null, $to_month = null) {
        if ($county == null || $county == 'null') {
            $county = $this->session->userdata('county_filter');
        }
        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_filter');
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
                $month = $this->session->userdata('filter_month');
            } else {
                $month = 0;
            }
        }

        if ($partner) {
            $sql = "CALL `proc_get_partner_age`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            if ($county == null || $county == 'null') {
                $sql = "CALL `proc_get_national_age`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            } else {
                $sql = "CALL `proc_get_regional_age`('" . $county . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            }
        }
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();

        $data['children']['name'] = lang('label.tests');
        $data['children']['colorByPoint'] = true;

        $data['adults']['name'] = lang('label.tests');
        $data['adults']['colorByPoint'] = true;
        $adults = 0;
        $sadult = 0;
        $children = 0;
        $schildren = 0;
        $count = 0;

        foreach ($result as $key => $value) {

            if ($value['name'] == lang('label.less2') || $value['name'] == lang('label.less9') || $value['name'] == lang('label.less14')) {
                $data['ul']['children'] = '';
                $children = (int) $children + (int) $value['agegroups'];
                $schildren = (int) $schildren + (int) $value['suppressed'];
                $data['children']['data'][$key]['y'] = $count;
                $data['children']['data'][$key]['name'] = $value['name'];
                $data['children']['data'][$key]['y'] = (int) $value['agegroups'];
            } else if ($value['name'] == lang('label.less19') || $value['name'] == lang('label.less24') || $value['name'] == lang('label.over25')) {
                $data['ul']['adults'] = '';
                $adults = (int) $adults + (int) $value['agegroups'];
                $sadult = (int) $sadult + (int) $value['suppressed'];
                $data['adults']['data'][$key]['y'] = $count;
                $data['adults']['data'][$key]['name'] = $value['name'];
                $data['adults']['data'][$key]['y'] = (int) $value['agegroups'];
            }
        }
        // echo "<pre>";print_r($schildren);echo "</pre>";
        // echo "<pre>";print_r($data);
        $data['ctotal'] = $children;
        $data['atotal'] = $adults;

        $data['ul']['children'] = '<li> ' . lang('label.children_suppression') . (int) (((int) $schildren / (int) $children) * 100) . '%</li>';
        $data['ul']['adults'] = '<li>' . lang('label.adults_suppression') . (int) (((int) $sadult / (int) $adults) * 100) . '%</li>';
        $data['children']['data'] = array_values($data['children']['data']);
        $data['adults']['data'] = array_values($data['adults']['data']);

        $data['children']['data'][0]['sliced'] = true;
        $data['children']['data'][0]['selected'] = true;

        $data['adults']['data'][0]['sliced'] = true;
        $data['adults']['data'][0]['selected'] = true;

        // echo "<pre>";print_r($data);die();

        return $data;
    }

    function gender($year = null, $month = null, $county = null, $partner = null, $to_year = null, $to_month = null) {
        if ($county == null || $county == 'null') {
            $county = $this->session->userdata('county_filter');
        }
        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_filter');
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
                $month = $this->session->userdata('filter_month');
            } else {
                $month = 0;
            }
        }

        if ($partner) {
            $sql = "CALL `proc_get_partner_gender`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            if ($county == null || $county == 'null') {
                $sql = "CALL `proc_get_national_gender`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            } else {
                $sql = "CALL `proc_get_regional_gender`('" . $county . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
            }
        }
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $data['gender'][0]['name'] = lang('label.not_suppressed_');
        $data['gender'][1]['name'] = lang('label.suppressed_');

        $count = 0;

        $data["gender"][0]["data"][0] = $count;
        $data["gender"][1]["data"][0] = $count;
        $data['categories'][0] = lang('label.no_data');

        foreach ($result as $key => $value) {
            $data['categories'][$key] = $value['name'];
            $data["gender"][0]["data"][$key] = (int) $value['nonsuppressed'];
            $data["gender"][1]["data"][$key] = (int) $value['suppressed'];
        }

        //$data['gender'][0]['drilldown']['color'] = '#913D88';
        //$data['gender'][1]['drilldown']['color'] = '#96281B';
        $data['gender'][0]['drilldown']['color'] = '#2f80d1';
        $data['gender'][1]['drilldown']['color'] = '#e8ee1d';

        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function get_sampletypesData($year = null, $county = null, $partner = null) {
        $array1 = array();
        $array2 = array();
        $sql2 = NULL;

        if ($county == null || $county == 'null') {
            $county = $this->session->userdata('county_filter');
        }
        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_filter');
        }


        if ($year == null || $year == 'null') {
            $to = $this->session->userdata('filter_year');
        } else {
            $to = $year;
        }
        $from = $to - 1;

        if ($partner) {
            $sql = "CALL `proc_get_partner_sample_types`('" . $partner . "','" . $from . "','" . $to . "')";
        } else {
            if ($county == null || $county == 'null') {
                $sql = "CALL `proc_get_national_sample_types`('" . $from . "','" . $to . "')";
            } else {
                $sql = "CALL `proc_get_regional_sample_types`('" . $county . "','" . $from . "','" . $to . "')";
            }
        }
        $array1 = $this->db->query($sql)->result_array();
        return $array1;
    }

    function sample_types($year = null, $county = null, $partner = null, $all = null) {
        $result = $this->get_sampletypesData($year, $county, $partner);

        // echo "<pre>";print_r($result);die();
        $data['sample_types'][0]['name'] = lang('label.sample_type_EDTA');
        $data['sample_types'][1]['name'] = lang('label.sample_type_DBS');
        $data['sample_types'][2]['name'] = lang('label.sample_type_plasma');
        // $data['sample_types'][3]['name'] = 'Suppression';

        $count = 0;

        $data['categories'][0] = lang('label.no_data');
        $data["sample_types"][0]["data"][0] = $count;
        $data["sample_types"][1]["data"][0] = $count;
        $data["sample_types"][2]["data"][0] = $count;
        // $data["sample_types"][3]["data"][0]	= $count;

        foreach ($result as $key => $value) {

            $data['categories'][$key] = $this->resolve_month($value['month']) . '-' . $value['year'];

            if ($all == 1) {
                $data["sample_types"][0]["data"][$key] = (int) $value['alledta'];
                $data["sample_types"][1]["data"][$key] = (int) $value['alldbs'];
                $data["sample_types"][2]["data"][$key] = (int) $value['allplasma'];
            } else {
                $data["sample_types"][0]["data"][$key] = (int) $value['edta'];
                $data["sample_types"][1]["data"][$key] = (int) $value['dbs'];
                $data["sample_types"][2]["data"][$key] = (int) $value['plasma'];
            }

            // $data["sample_types"][3]["data"][$key]	= round($value['suppression'],1);
        }

        return $data;
    }

    function download_sampletypes($year = null, $county = null, $partner = null) {
        $data = $this->get_sampletypesData($year, $county, $partner);
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
            /** default php csv handler * */
            fputcsv($f, $line, $delimiter);
        }
        /** rewrind the "file" with the csv lines * */
        fseek($f, 0);
        /** modify header to be downloadable csv file * */
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . Date('YmdH:i:s') . 'vl_sampleTypes.csv";');
        /** Send file to browser for download */
        fpassthru($f);
    }

    function get_patients($year = null, $month = null, $county = null, $partner = null, $to_year = null, $to_month = null) {
        $type = 0;
        $params;

        if ($county == null || $county == 'null') {
            $county = $this->session->userdata('county_filter');
        }
        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_filter');
        }

        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }
        if ($month == null || $month == 'null') {
            if ($this->session->userdata('filter_month') == null || $this->session->userdata('filter_month') == 'null') {
                $month = 0;
                $type = 1;
            } else {
                $month = $this->session->userdata('filter_month');
                $type = 3;
            }
        }

        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }
        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }

        if ($type == 0) {
            if ($to_year == 0) {
                $type = 3;
            } else {
                $type = 5;
            }
        }

        $sql;

        if ($partner) {
            $params = "patient/partner/{$partner}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
            $sql = "Select sum(totalartmar) as totalartmar from view_facilitys where partner='{$partner}'";
        } else {
            if ($county == null || $county == 'null') {
                $params = "patient/national/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
                $sql = "Select sum(totalartmar) as totalartmar from view_facilitys";
            } else {
                $query = $this->db->get_where('countys', array('id' => $county), 1)->row();
                $c = $query->CountyMFLCode;

                $params = "patient/county/{$c}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";
                $sql = "Select sum(totalartmar) as totalartmar from view_facilitys where county='{$county}'";
            }
        }
        $this->db->close();

        $result = $this->req($params);
        if ($result == null) {
            return null;
        }
        $res = $this->db->query($sql)->row();

        $this->db->close();

        // echo "<pre>";print_r($result);die();

        $data['outcomes'][0]['name'] = lang('label.patients_grouped_by_tests_received');

        // $data['outcomes'][0]['type'] = "column";
        // $data['outcomes'][0]['yAxis'] = 1;
        // $data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');

        $data['title'] = " ";

        $data['unique_patients'] = 0;
        $data['size'] = 0;
        $data['total_patients'] = $res->totalartmar;
        $data['total_tests'] = 0;

        foreach ($result as $key => $value) {

            $data['categories'][$key] = (int) $value->tests;

            $data['outcomes'][0]['data'][$key] = (int) $value->totals;
            $data['unique_patients'] += (int) $value->totals;
            $data['total_tests'] += ($data['categories'][$key] * $data['outcomes'][0]['data'][$key]);
            $data['size'] ++;
        }

        $data['coverage'] = round(($data['unique_patients'] / $data['total_patients'] * 100), 2);

        return $data;
    }

    function current_suppression($county = null, $partner = null, $annual = NULL) {
        if ($county == null || $county == 'null') {
            $county = $this->session->userdata('county_filter');
        }
        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_filter');
        }

        // echo "<pre>";print_r($result);die();
        $color = array('#6BB9F0', '#e8ee1d', '#2f80d1', '#5C97BF');

        $data['vl_outcomes']['name'] = lang('label.tests');
        $data['vl_outcomes']['colorByPoint'] = true;
        $data['ul'] = '';

        $data['vl_outcomes']['data'][0]['name'] = lang('label.suppressed_');
        $data['vl_outcomes']['data'][1]['name'] = lang('label.not_suppressed_');

        if ($partner) {
            $sql = "CALL `proc_get_vl_current_suppression`('3','" . $partner . "')";
            if ($annual == 1) {
                $sql = "CALL `proc_get_vl_current_suppression_year`('3','" . $partner . "')";
            }
        } else {
            if ($county == null || $county == 'null') {
                $sql = "CALL `proc_get_vl_current_suppression`('0','0')";
            } else {
                $sql = "CALL `proc_get_vl_current_suppression`('1','" . $county . "')";
            }
        }

        $result = $this->db->query($sql)->row();

        $this->db->close();


        $data['vl_outcomes']['data'][0]['y'] = (int) $result->suppressed;
        $data['vl_outcomes']['data'][1]['y'] = (int) $result->nonsuppressed;

        //$data['vl_outcomes']['data'][0]['color'] = '#1BA39C';
        //$data['vl_outcomes']['data'][1]['color'] = '#F2784B';
        $data['vl_outcomes']['data'][0]['color'] = '#2f80d1';
        $data['vl_outcomes']['data'][1]['color'] = '#e8ee1d';



        $data['vl_outcomes']['data'][0]['sliced'] = true;
        $data['vl_outcomes']['data'][0]['selected'] = true;

        $data['total'][0] = (int) $result->suppressed;
        $data['total'][1] = (int) $result->nonsuppressed;

        return $data;
    }

    function current_gender_chart($type, $param_type = 1, $param = NULL, $annual = NULL) {


        if ($param_type == 1) {

            if ($param == null || $param == 'null') {
                $param = $this->session->userdata('county_filter');
            }

            if ($param == null || $param == 'null') {
                $param = 0;
            }
            $sql = "CALL `proc_get_vl_current_gender_suppression_listing`({$type}, {$param})";
        } else {

            if ($param == null || $param == 'null') {
                $param = $this->session->userdata('partner_filter');
            }

            if ($param == null || $param == 'null') {
                $param = 1000;
            }
            $sql = "CALL `proc_get_vl_current_gender_suppression_listing_partner`({$type}, {$param})";

            if ($annual == 1) {
                $sql = "CALL `proc_get_vl_current_gender_suppression_listing_partner_year`({$type}, {$param})";
            }
        }

        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->row();
        // echo "<pre>";print_r($result);die();
        $data['gender'][0]['name'] = lang('label.not_suppressed_');
        $data['gender'][1]['name'] = lang('label.suppressed_');

        $data['categories'][0] = lang('label.no_data');
        $data["gender"][0]["data"][0] = (int) $result->nogender_nonsuppressed;
        $data["gender"][1]["data"][0] = (int) $result->nogender_suppressed;

        $data['categories'][1] = lang('label.male');
        $data["gender"][0]["data"][1] = (int) $result->male_nonsuppressed;
        $data["gender"][1]["data"][1] = (int) $result->male_suppressed;

        $data['categories'][2] = lang('label.female');
        $data["gender"][0]["data"][2] = (int) $result->female_nonsuppressed;
        $data["gender"][1]["data"][2] = (int) $result->female_suppressed;

        //$data['gender'][0]['drilldown']['color'] = '#913D88';
        //$data['gender'][1]['drilldown']['color'] = '#96281B';
        $data['gender'][0]['drilldown']['color'] = '#2f80d1';
        $data['gender'][1]['drilldown']['color'] = '#e8ee1d';
        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function current_age_chart($type, $param_type = 1, $param = NULL, $annual = NULL) {


        if ($param_type == 1) {

            if ($param == null || $param == 'null') {
                $param = $this->session->userdata('county_filter');
            }

            if ($param == null || $param == 'null') {
                $param = 0;
            }
            $sql = "CALL `proc_get_vl_current_age_suppression_listing`({$type}, {$param})";
        } else {

            if ($param == null || $param == 'null') {
                $param = $this->session->userdata('partner_filter');
            }

            if ($param == null || $param == 'null') {
                $param = 1000;
            }
            $sql = "CALL `proc_get_vl_current_age_suppression_listing_partner`({$type}, {$param})";

            if ($annual == 1) {
                $sql = "CALL `proc_get_vl_current_age_suppression_listing_partner_year`({$type}, {$param})";
            }
        }

        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->row();
        // echo "<pre>";print_r($result);die();
        $data['ageGnd'][0]['name'] = lang('label.not_suppressed_');
        $data['ageGnd'][1]['name'] = lang('label.suppressed_');

        $data['categories'][0] = lang('label.no_data');
        $data["ageGnd"][0]["data"][0] = (int) $result->noage_nonsuppressed;
        $data["ageGnd"][1]["data"][0] = (int) $result->noage_suppressed;

        $data['categories'][1] = lang('label.less2');
        $data["ageGnd"][0]["data"][1] = (int) $result->less2_nonsuppressed;
        $data["ageGnd"][1]["data"][1] = (int) $result->less2_suppressed;

        $data['categories'][2] = lang('label.less9');
        $data["ageGnd"][0]["data"][2] = (int) $result->less9_nonsuppressed;
        $data["ageGnd"][1]["data"][2] = (int) $result->less9_suppressed;

        $data['categories'][3] = lang('label.less14');
        $data["ageGnd"][0]["data"][3] = (int) $result->less14_nonsuppressed;
        $data["ageGnd"][1]["data"][3] = (int) $result->less14_suppressed;

        $data['categories'][4] = lang('label.less19');
        $data["ageGnd"][0]["data"][4] = (int) $result->less19_nonsuppressed;
        $data["ageGnd"][1]["data"][4] = (int) $result->less19_suppressed;

        $data['categories'][5] = lang('label.less24');
        $data["ageGnd"][0]["data"][5] = (int) $result->less24_nonsuppressed;
        $data["ageGnd"][1]["data"][5] = (int) $result->less24_suppressed;

        $data['categories'][6] = lang('label.over25');
        $data["ageGnd"][0]["data"][6] = (int) $result->over25_nonsuppressed;
        $data["ageGnd"][1]["data"][6] = (int) $result->over25_suppressed;

        //$data['ageGnd'][0]['drilldown']['color'] = '#913D88';
        //$data['ageGnd'][1]['drilldown']['color'] = '#96281B';
        $data['ageGnd'][0]['drilldown']['color'] = '#2f80d1';
        $data['ageGnd'][1]['drilldown']['color'] = '#e8ee1d';

        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function suppression_listings($type, $param_type = 1, $param = NULL, $annual = NULL) {
        $li = '';
        $table = '';
        $sql = '';

        if ($param_type == 1) {

            if ($param == null || $param == 'null') {
                $param = $this->session->userdata('county_filter');
            }

            if ($param == null || $param == 'null') {
                $param = 0;
            }
            $sql = "CALL `proc_get_vl_current_suppression_listing`({$type}, {$param})";
        } else {

            if ($param == null || $param == 'null') {
                $param = $this->session->userdata('partner_filter');
            }

            if ($param == null || $param == 'null') {
                $param = 1000;
            }
            $sql = "CALL `proc_get_vl_current_suppression_listing_partners`({$type}, {$param})";

            if ($annual == 1) {
                $sql = "CALL `proc_get_vl_current_suppression_listing_partners_year`({$type}, {$param})";
            }
        }


        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        // echo "<pre>";print_r($result);die();
        $count = 1;
        $listed = FALSE;

        if ($result) {
            foreach ($result as $key => $value) {
                $name;

                switch ($type) {
                    case 1:
                        $name = $value['countyname'];
                        break;
                    case 2:
                        $name = $value['subcounty'];
                        break;
                    case 3:
                        $name = $value['partnername'];
                        break;
                    case 4:
                        $name = $value['name'];
                        break;
                    default:
                        break;
                }
                $patients = ($value['suppressed'] + $value['nonsuppressed']);
                $suppression = round(($value['suppressed'] * 100 / $patients), 1);
                $coverage = round(($patients * 100 / $value['totallstrpt']), 1);

                if ($count < 16) {
                    $li .= '<a href="javascript:void(0);" class="list-group-item" ><strong>' . $count . '.</strong>&nbsp;' . $name . ':&nbsp;' . $suppression . '%</a>';
                }

                $table .= '<tr>';
                $table .= '<td>' . $count . '</td>';
                $table .= '<td>' . $name . '</td>';
                $table .= '<td>' . $suppression . '%</td>';
                $table .= '<td>' . $patients . '</td>';
                $table .= '<td>' . ($value['totallstrpt']) . '</td>';
                $table .= '<td>' . $coverage . '%</td>';
                $table .= '</tr>';
                $count++;
            }
        } else {
            $li = lang('label.no_data');
        }

        $data = array(
            'ul' => $li,
            'table' => $table);
        return $data;
    }

    function suppression_age_listings($suppressed, $type, $param_type = 1, $param = NULL, $annual = NULL) {
        $li = '';
        $table = '';
        $sql = '';

        if ($param_type == 1) {

            if ($param == null || $param == 'null') {
                $param = $this->session->userdata('county_filter');
            }

            if ($param == null || $param == 'null') {
                $param = 0;
            }
            $sql = "CALL `proc_get_vl_current_age_suppression_listing`({$type}, {$param})";
        } else {

            if ($param == null || $param == 'null') {
                $param = $this->session->userdata('partner_filter');
            }

            if ($param == null || $param == 'null') {
                $param = 1000;
            }
            $sql = "CALL `proc_get_vl_current_age_suppression_listing_partner`({$type}, {$param})";

            if ($annual == 1) {
                $sql = "CALL `proc_get_vl_current_age_suppression_listing_partner_year`({$type}, {$param})";
            }
        }


        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        // echo "<pre>";print_r($result);die();
        $count = 1;
        $listed = FALSE;

        if ($result) {
            foreach ($result as $key => $value) {
                $name;

                switch ($type) {
                    case 1:
                        $name = $value['countyname'];
                        break;
                    case 2:
                        $name = $value['subcounty'];
                        break;
                    case 3:
                        $name = $value['partnername'];
                        break;
                    case 4:
                        $name = $value['name'];
                        break;
                    default:
                        break;
                }

                if ($count < 16) {
                    $li .= '<a href="javascript:void(0);" class="list-group-item" ><strong>' . $count . '.</strong>&nbsp;' . $name . '</a>';
                }

                $table .= '<tr>';
                $table .= '<td>' . $count . '</td>';
                $table .= '<td>' . $name . '</td>';

                if ($suppressed == 1) {
                    $table .= '<td>' . $value['noage_suppressed'] . '</td>';
                    $table .= '<td>' . $value['less2_suppressed'] . '</td>';
                    $table .= '<td>' . $value['less9_suppressed'] . '</td>';
                    $table .= '<td>' . $value['less14_suppressed'] . '</td>';
                    $table .= '<td>' . $value['less19_suppressed'] . '</td>';
                    $table .= '<td>' . $value['less24_suppressed'] . '</td>';
                    $table .= '<td>' . $value['over25_suppressed'] . '</td>';
                } else {
                    $table .= '<td>' . $value['noage_nonsuppressed'] . '</td>';
                    $table .= '<td>' . $value['less2_nonsuppressed'] . '</td>';
                    $table .= '<td>' . $value['less9_nonsuppressed'] . '</td>';
                    $table .= '<td>' . $value['less14_nonsuppressed'] . '</td>';
                    $table .= '<td>' . $value['less19_nonsuppressed'] . '</td>';
                    $table .= '<td>' . $value['less24_nonsuppressed'] . '</td>';
                    $table .= '<td>' . $value['over25_nonsuppressed'] . '</td>';
                }

                $table .= '</tr>';
                $count++;
            }
        } else {
            $li = lang('label.no_data');
        }

        $data = array(
            'ul' => $li,
            'table' => $table);
        return $data;
    }

    function suppression_gender_listings($type, $param_type = 1, $param = NULL, $annual = NULL) {
        $li = '';
        $table = '';
        $sql = '';

        if ($param_type == 1) {

            if ($param == null || $param == 'null') {
                $param = $this->session->userdata('county_filter');
            }

            if ($param == null || $param == 'null') {
                $param = 0;
            }
            $sql = "CALL `proc_get_vl_current_gender_suppression_listing`({$type}, {$param})";
        } else {

            if ($param == null || $param == 'null') {
                $param = $this->session->userdata('partner_filter');
            }

            if ($param == null || $param == 'null') {
                $param = 1000;
            }
            $sql = "CALL `proc_get_vl_current_gender_suppression_listing_partner`({$type}, {$param})";

            if ($annual == 1) {
                $sql = "CALL `proc_get_vl_current_gender_suppression_listing_partner_year`({$type}, {$param})";
            }
        }


        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        // echo "<pre>";print_r($result);die();
        $count = 1;
        $listed = FALSE;

        if ($result) {
            foreach ($result as $key => $value) {
                $name;

                switch ($type) {
                    case 1:
                        $name = $value['countyname'];
                        break;
                    case 2:
                        $name = $value['subcounty'];
                        break;
                    case 3:
                        $name = $value['partnername'];
                        break;
                    case 4:
                        $name = $value['name'];
                        break;
                    default:
                        break;
                }

                if ($count < 16) {
                    $li .= '<a href="javascript:void(0);" class="list-group-item" ><strong>' . $count . '.</strong>&nbsp;' . $name . '</a>';
                }

                $table .= '<tr>';
                $table .= '<td>' . $count . '</td>';
                $table .= '<td>' . $name . '</td>';
                $table .= '<td>' . $value['male_suppressed'] . '</td>';
                $table .= '<td>' . $value['male_nonsuppressed'] . '</td>';
                $table .= '<td>' . $value['female_suppressed'] . '</td>';
                $table .= '<td>' . $value['female_nonsuppressed'] . '</td>';
                $table .= '<td>' . $value['nogender_suppressed'] . '</td>';
                $table .= '<td>' . $value['nogender_nonsuppressed'] . '</td>';
                $table .= '</tr>';
                $count++;
            }
        } else {
            $li = lang('label.no_data');
        }

        $data = array(
            'ul' => $li,
            'table' => $table);
        return $data;
    }

}
?>
 





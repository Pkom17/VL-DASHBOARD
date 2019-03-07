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
        //echo "<pre>";print_r($result);die();

        $data['outcomes'][0]['name'] = lang('label.gt1000');
        $data['outcomes'][1]['name'] = lang('label.lt1000');
        $data['outcomes'][2]['name'] = lang('label.undetectable');
        $data['outcomes'][3]['name'] = lang('label.invalids');
        $data['outcomes2'][0]['name'] = lang('label.not_suppressed');
        $data['outcomes2'][1]['name'] = lang('label.suppressed');
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
            $data['categories'][$key] = $value['name'];
            $data['outcomes'][0]['data'][$key] = (int) $value['all_nonsuppressed'];
            $data['outcomes'][1]['data'][$key] = (int) $value['all_less1000'];
            $data['outcomes'][2]['data'][$key] = (int) $value['all_undetected'];
            $data['outcomes'][3]['data'][$key] = (int) $value['all_invalids'];
            $data['categories2'][$key] = $value['name'];
            $data['outcomes2'][0]['data'][$key] = (int) $value['nonsuppressed'];
            $data['outcomes2'][1]['data'][$key] = (int) $value['undetected'] + (int) $value['less1000'];
            $data['outcomes2'][2]['data'][$key] = round(@((((int) $value['undetected'] + (int) $value['less1000']) * 100) / ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['nonsuppressed'] )), 1);
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

        $result = $this->db->query($sql)->result_array();
        $this->db->close();
        $sitessending = $this->db->query($sql2)->result_array();
        $this->db->close();
        $data['vl_outcomes']['name'] = lang('label.tests');
        $data['vl_outcomes2']['name'] = lang('label.tests');
        $data['vl_outcomes']['colorByPoint'] = true;
        $data['ul'] = '';
        $data['ul2'] = '';

        $data['vl_outcomes']['data'][0]['name'] = lang('label.invalids');
        $data['vl_outcomes']['data'][1]['name'] = lang('label.undetectable');
        $data['vl_outcomes']['data'][2]['name'] = lang('label.lt1000');
        $data['vl_outcomes']['data'][3]['name'] = lang('label.gt1000');
        $data['vl_outcomes2']['data'][0]['name'] = lang('label.suppressed');
        $data['vl_outcomes2']['data'][1]['name'] = lang('label.not_suppressed');

        $count = 0;

        $data['vl_outcomes']['data'][0]['y'] = $count;
        $data['vl_outcomes']['data'][1]['y'] = $count;
        $data['vl_outcomes']['data'][2]['y'] = $count;
        $data['vl_outcomes']['data'][3]['y'] = $count;
        $data['vl_outcomes2']['data'][0]['y'] = $count;
        $data['vl_outcomes2']['data'][1]['y'] = $count;

        foreach ($result as $key => $value) {
            $total = (int) ( $value['undetected'] + $value['less1000'] + $value['less5000'] + $value['above5000']);
            $all_total = (int) ( $value['all_undetected'] + $value['all_less1000'] + $value['all_less5000'] + $value['all_above5000']);
            $invalids = (int) ($value['invalids']);
            $undetected = (int) ($value['undetected']);
            $less1000 = (int) ($value['less1000']);
            $suppressed = (int) ($value['undetected'] + $value['less1000']);
            $greater = (int) ($value['less5000'] + $value['above5000']);
            $total_tests = (int) $value['invalids'] + (int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000'];
            $all_invalids = (int) ($value['all_invalids']);
            $all_undetected = (int) ($value['all_undetected']);
            $all_less1000 = (int) ($value['all_less1000']);
            $all_greater = (int) ($value['all_less5000'] + $value['all_above5000']);
            $all_total_tests = (int) ( $value['all_invalids'] + $value['all_undetected'] + $value['all_less1000'] + $value['all_less5000'] + $value['all_above5000']);
            if ($total == 0) {
                $val_sup = round((0), 1);
                $val_ls = round((0), 1);
                $val_gt = round((0), 1);
                $val_und = round((0), 1);
                $val_inv = round((0), 1);
                $all_val_ls = round((0), 1);
                $all_val_gt = round((0), 1);
                $all_val_und = round((0), 1);
                $all_val_inv = round((0), 1);
            } else {
                $val_sup = round(((($less1000 + $undetected) / $total_tests) * 100), 1);
                $val_ls = round((($less1000 / $total_tests) * 100), 1);
                $val_gt = round((($greater / $total_tests) * 100), 1);
                $val_und = round((($undetected / $total_tests) * 100), 1);
                $val_inv = round((($invalids / $total_tests) * 100), 1);
                $all_val_ls = round((($all_less1000 / $all_total_tests) * 100), 1);
                $all_val_gt = round((($all_greater / $all_total_tests) * 100), 1);
                $all_val_und = round((($all_undetected / $all_total_tests) * 100), 1);
                $all_val_inv = round((($all_invalids / $all_total_tests) * 100), 1);
            }
            $data['ul'] .= '
                <tr>
	    		<td>' . lang('total_done_test') . '</td>
	    		<td>' . number_format($all_total_tests) . '</td>
                        <td>' . lang('total_done_test_valid') . '</td>
                        <td>' . number_format($all_total) . '</td>
	    	</tr> 
	    	<tr>
	    		<td>' . lang('total_test_result_inv') . '</td>
	    		<td>' . $all_invalids . '<br/><b> ( ' . $all_val_inv . '% )</b></td>
	    		<td>' . lang('total_test_result_und') . '</td>
	    		<td>' . $all_undetected . '<br/><b> ( ' . $all_val_und . '% )</b></td>
	    	</tr>
 
	    	<tr>
	    		<td>' . lang('total_test_result_lt1000') . '</td>
	    		<td>' . $all_less1000 . '<br/><b> ( ' . $all_val_ls . '% )</b></td>
	    		<td>' . lang('total_test_result_gt1000') . '</td>
	    		<td>' . $all_greater . '<br/><b> ( ' . $all_val_gt . '% )</b></td>
	    	</tr>';
            $data['ul2'] .= '
			<tr>
	    		<td>&nbsp;' . lang('total_tested_patient') . '</td>
	    		<td>' . number_format($total_tests) . '</td>
                        <td>' . lang('total_suppressed_patient') . '</td>
                        <td>' . number_format($suppressed) . '<br/><b> ( ' . $val_sup . '% )</td>
	    	</tr>
	    	<tr>
	    		<td>&nbsp;' . lang('total_patient_result_inv') . '</td>
	    		<td>' . number_format($invalids) . '<br/><b> ( ' . $val_inv . '% )</b></td>
	    		<td>' . lang('total_patient_result_und') . '</td>
	    		<td>' . number_format($undetected) . '<br/><b> ( ' . $val_und . '% )</b></td>
	    	</tr>
	    	<tr>
	    		<td>&nbsp;' . lang('total_patient_result_lt1000') . '</td>
	    		<td>' . number_format($less1000) . '<br/><b> ( ' . $val_ls . '% )</b></td>
	    		<td>' . lang('total_patient_result_gt1000') . '</td>
	    		<td>' . number_format($greater) . '<br/><b> ( ' . $val_gt . '% )</b></td>
	    	</tr>';

            $data['vl_outcomes']['data'][0]['y'] = (int) $value['all_invalids'];
            $data['vl_outcomes']['data'][1]['y'] = (int) $value['all_undetected'];
            $data['vl_outcomes']['data'][2]['y'] = (int) $value['all_less1000'];
            $data['vl_outcomes']['data'][3]['y'] = (int) $value['all_less5000'] + (int) $value['all_above5000'];
            $data['vl_outcomes2']['data'][0]['y'] = (int) $value['undetected'] + (int) $value['less1000'];
            $data['vl_outcomes2']['data'][1]['y'] = (int) $value['less5000'] + (int) $value['above5000'];
//            $data['vl_outcomes']['data'][0]['color'] = '#000000';
//            $data['vl_outcomes']['data'][1]['color'] = '#00ff99';
//            $data['vl_outcomes']['data'][2]['color'] = '#2f80d1';
//            $data['vl_outcomes']['data'][3]['color'] = '#e8ee1d';
//            $data['vl_outcomes2']['data'][0]['color'] = '#40bf80';
//            $data['vl_outcomes2']['data'][1]['color'] = '#f72109';
        }

        $count = 0;
        $sites = 0;
        foreach ($sitessending as $key => $value) {
            if ((int) $value['sitessending'] != 0) {
                $sites = (int) $sites + (int) $value['sitessending'];
                $count++;
            }
        }
        $data['vl_outcomes']['data'][3]['sliced'] = true;
        $data['vl_outcomes']['data'][3]['selected'] = true;
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
        $query = $this->db->query($sql);
        $result = $query->result_array();
        // echo "<pre>";print_r($result);die();
        $count = 0;
        $loop = 0;
        $name = '';
        $invalids = 0;
        $undetectable = 0;
        $less1000 = 0;
        $over1000 = 0;
        $non_suppressed = 0;
        $suppressed = 0;

        // echo "<pre>";print_r($result);die();
        $data['ageGnd'][0]['name'] = lang('label.gt1000');
        $data['ageGnd'][1]['name'] = lang('label.lt1000');
        $data['ageGnd'][2]['name'] = lang('label.undetectable');
        $data['ageGnd'][3]['name'] = lang('label.invalids');
        $data['ageGnd2'][0]['name'] = lang('label.not_suppressed');
        $data['ageGnd2'][1]['name'] = lang('label.suppressed');

        $count = 0;

        $data["ageGnd"][0]["data"][0] = $count;
        $data["ageGnd"][1]["data"][0] = $count;
        $data["ageGnd"][2]["data"][0] = $count;
        $data["ageGnd"][3]["data"][0] = $count;
        $data["ageGnd2"][0]["data"][0] = $count;
        $data["ageGnd2"][1]["data"][0] = $count;
        $data['categories'][0] = lang('label.no_data');

        foreach ($result as $key => $value) {
            if ($value['name'] == lang('label.no_data')) {
                $loop = $key;
                $name = $value['name'];
                $invalids = $value['all_invalids'];
                $undetectable = $value['all_undetected'];
                $less1000 = $value['all_less1000'];
                $over1000 = $value['all_nonsuppressed'];
                $non_suppressed = $value['nonsuppressed'];
                $suppressed = (int) $value['undetected'] + (int) $value['less1000'];
            } else if ($value['name'] == lang('label.less2')) {
                $loop = $key;
                $name = $value['name'];
                $invalids = $value['all_invalids'];
                $undetectable = $value['all_undetected'];
                $less1000 = $value['all_less1000'];
                $over1000 = $value['all_nonsuppressed'];
                $non_suppressed = $value['nonsuppressed'];
                $suppressed = (int) $value['undetected'] + (int) $value['less1000'];
            } else if ($value['name'] == lang('label.less9')) {
                $loop = $key;
                $name = $value['name'];
                $invalids = $value['all_invalids'];
                $undetectable = $value['all_undetected'];
                $less1000 = $value['all_less1000'];
                $over1000 = $value['all_nonsuppressed'];
                $non_suppressed = $value['nonsuppressed'];
                $suppressed = (int) $value['undetected'] + (int) $value['less1000'];
            } else if ($value['name'] == lang('label.less14')) {
                $loop = $key;
                $name = $value['name'];
                $invalids = $value['all_invalids'];
                $undetectable = $value['all_undetected'];
                $less1000 = $value['all_less1000'];
                $over1000 = $value['all_nonsuppressed'];
                $non_suppressed = $value['nonsuppressed'];
                $suppressed = (int) $value['undetected'] + (int) $value['less1000'];
            } else if ($value['name'] == lang('label.less19')) {
                $loop = $key;
                $name = $value['name'];
                $invalids = $value['all_invalids'];
                $undetectable = $value['all_undetected'];
                $less1000 = $value['all_less1000'];
                $over1000 = $value['all_nonsuppressed'];
                $non_suppressed = $value['nonsuppressed'];
                $suppressed = (int) $value['undetected'] + (int) $value['less1000'];
            } else if ($value['name'] == lang('label.less24')) {
                $loop = $key;
                $name = $value['name'];
                $invalids = $value['all_invalids'];
                $undetectable = $value['all_undetected'];
                $less1000 = $value['all_less1000'];
                $over1000 = $value['all_nonsuppressed'];
                $non_suppressed = $value['nonsuppressed'];
                $suppressed = (int) $value['undetected'] + (int) $value['less1000'];
            } else if ($value['name'] == lang('label.over25')) {
                $loop = $key;
                $name = $value['name'];
                $invalids = $value['all_invalids'];
                $undetectable = $value['all_undetected'];
                $less1000 = $value['all_less1000'];
                $over1000 = $value['all_nonsuppressed'];
                $non_suppressed = $value['nonsuppressed'];
                $suppressed = (int) $value['undetected'] + (int) $value['less1000'];
            }

            $data['categories'][$loop] = $name;
            $data["ageGnd"][0]["data"][$loop] = (int) $over1000;
            $data["ageGnd"][1]["data"][$loop] = (int) $less1000;
            $data["ageGnd"][2]["data"][$loop] = (int) $undetectable;
            $data["ageGnd"][3]["data"][$loop] = (int) $invalids;
            $data["ageGnd2"][0]["data"][$loop] = (int) $non_suppressed;
            $data["ageGnd2"][1]["data"][$loop] = (int) $suppressed;
        }
        $data['categories'] = array_values($data['categories']);
        $data["ageGnd"][0]["data"] = array_values($data["ageGnd"][0]["data"]);
        $data["ageGnd"][1]["data"] = array_values($data["ageGnd"][1]["data"]);
        $data["ageGnd"][2]["data"] = array_values($data["ageGnd"][2]["data"]);
        $data["ageGnd"][3]["data"] = array_values($data["ageGnd"][3]["data"]);
        $data["ageGnd2"][0]["data"] = array_values($data["ageGnd2"][0]["data"]);
        $data["ageGnd2"][1]["data"] = array_values($data["ageGnd2"][1]["data"]);
//         echo "<pre>";print_r($data);die();
        return $data;
    }

    function p_age($year = null, $month = null, $county = null, $subcounty = null, $site = null, $partner = null, $to_year = null, $to_month = null) {
        if ($county == null) {
            $county_filter = $this->session->userdata('county_filter');
            $county = ($county_filter != null) ? $county_filter : 0;
        }
        if ($subcounty == null) {
            $sub_county_filter = $this->session->userdata('sub_county_filter');
            $subcounty = ($sub_county_filter != null) ? $sub_county_filter : 0;
        }
        if ($site == null) {
            $site_filter = $this->session->userdata('site_filter');
            $site = ($site_filter != null) ? $site_filter : 0;
        }
        if ($partner == null) {
            $partner_filter = $this->session->userdata('partner_filter');
            $partner = ($partner_filter != null) ? $partner_filter : 0;
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
            if ($this->session->userdata('filter_month') != null) {
                $month = $this->session->userdata('filter_month');
            } else {
                $month = 0;
            }
        }

        $sql = "CALL `proc_get_p_age`('" . $county . "','" . $subcounty . "','" . $site . "','" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";

        //  echo "<pre>";print_r($sql);die();
        $query = $this->db->query($sql);
        $result = $query->result_array();
        // echo "<pre>";print_r($result);die();
        // echo "<pre>";print_r($result);die();
        $data['ageGnd'][0]['name'] = lang('label.gt1000');
        $data['ageGnd'][1]['name'] = lang('label.lt1000');
        $data['ageGnd'][2]['name'] = lang('label.undetectable');
        $data['ageGnd'][3]['name'] = lang('label.invalids');
        $data['ageGnd2'][0]['name'] = lang('label.not_suppressed');
        $data['ageGnd2'][1]['name'] = lang('label.suppressed');
        $data['ageGnd2'][2]['name'] = lang('label.suppression');
        
        
        $data['ageGnd'][0]['yAxis'] = 0;
        $data['ageGnd'][1]['yAxis'] = 0;
        $data['ageGnd'][2]['yAxis'] = 0;
        $data['ageGnd'][3]['yAxis'] = 0;
        $data['ageGnd2'][0]['yAxis'] = 0;
        $data['ageGnd2'][1]['yAxis'] = 0;
        $data['ageGnd2'][2]['yAxis'] = 1;
        
        $data['ageGnd'][0]['type'] = "column";
        $data['ageGnd'][1]['type'] = "column";
        $data['ageGnd'][2]['type'] = "column";
        $data['ageGnd'][3]['type'] = "column";
        $data['ageGnd2'][0]['type'] = "column";
        $data['ageGnd2'][1]['type'] = "column";
        $data['ageGnd2'][2]['type'] = "spline";
        
        $data['ageGnd'][0]['tooltip'] = array("valueSuffix" => ' ');
        $data['ageGnd'][1]['tooltip'] = array("valueSuffix" => ' ');
        $data['ageGnd'][2]['tooltip'] = array("valueSuffix" => ' ');
        $data['ageGnd'][3]['tooltip'] = array("valueSuffix" => ' ');
        $data['ageGnd2'][0]['tooltip'] = array("valueSuffix" => ' ');
        $data['ageGnd2'][1]['tooltip'] = array("valueSuffix" => ' ');
        $data['ageGnd2'][2]['tooltip'] = array("valueSuffix" => ' %');

        $count = 0;

        $data["ageGnd"][0]["data"][0] = $count;
        $data["ageGnd"][1]["data"][0] = $count;
        $data["ageGnd"][2]["data"][0] = $count;
        $data["ageGnd"][3]["data"][0] = $count;
        $data["ageGnd2"][0]["data"][0] = $count;
        $data["ageGnd2"][1]["data"][0] = $count;
        $data['categories'][0] = lang('label.no_data');

        foreach ($result as $key => $value) {
            $data['categories'][$key] = $value['name'];
            $data["ageGnd"][0]["data"][$key] = (int) $value['all_nonsuppressed'];
            $data["ageGnd"][1]["data"][$key] = (int) $value['all_less1000'];
            $data["ageGnd"][2]["data"][$key] = (int) $value['all_undetected'];
            $data["ageGnd"][3]["data"][$key] = (int) $value['all_invalids'];
            $data["ageGnd2"][0]["data"][$key] = (int) $value['nonsuppressed'];
            $data["ageGnd2"][1]["data"][$key] = (int) $value['undetected'] + (int) $value['less1000'];
            $data['ageGnd2'][2]['data'][$key] = round(@((((int) $value['undetected'] + (int) $value['less1000']) * 100) / ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['nonsuppressed'] )), 1);
        }

        $data['categories'] = array_values($data['categories']);
        $data["ageGnd"][0]["data"] = array_values($data["ageGnd"][0]["data"]);
        $data["ageGnd"][1]["data"] = array_values($data["ageGnd"][1]["data"]);
        $data["ageGnd"][2]["data"] = array_values($data["ageGnd"][2]["data"]);
        $data["ageGnd"][3]["data"] = array_values($data["ageGnd"][3]["data"]);
        $data["ageGnd2"][0]["data"] = array_values($data["ageGnd2"][0]["data"]);
        $data["ageGnd2"][1]["data"] = array_values($data["ageGnd2"][1]["data"]);
        $data["ageGnd2"][2]["data"] = array_values($data["ageGnd2"][2]["data"]);
        //echo "<pre>";print_r($data);die();
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
                $children = (int) $children + (int) $value['tests'];
                $schildren = (int) $schildren + (int) $value['undetected'] + (int) $value['less1000'];
                $data['children']['data'][$key]['y'] = $count;
                $data['children']['data'][$key]['name'] = $value['name'];
                $data['children']['data'][$key]['y'] = (int) $value['tests'];
            } else if ($value['name'] == lang('label.less19') || $value['name'] == lang('label.less24') || $value['name'] == lang('label.over25')) {
                $data['ul']['adults'] = '';
                $adults = (int) $adults + (int) $value['tests'];
                $sadult = (int) $sadult + (int) $value['undetected'] + (int) $value['less1000'];
                $data['adults']['data'][$key]['y'] = $count;
                $data['adults']['data'][$key]['name'] = $value['name'];
                $data['adults']['data'][$key]['y'] = (int) $value['tests'];
            }
        }
        // echo "<pre>";print_r($schildren);echo "</pre>";
        // echo "<pre>";print_r($data);
        $data['ctotal'] = $children;
        $data['atotal'] = $adults;
        $data['ul']['children'] = '<li class="bold"> ' . lang('label.children_suppression') . ': ' . (int) (((int) $schildren / (int) $children) * 100) . '%</li>';
        $data['ul']['adults'] = '<li class="bold">' . lang('label.adults_suppression') . ': ' . (int) (((int) $sadult / (int) $adults) * 100) . '%</li>';
        $data['children']['data'] = array_values($data['children']['data']);
        $data['adults']['data'] = array_values($data['adults']['data']);

        $data['children']['data'][0]['sliced'] = true;
        $data['children']['data'][0]['selected'] = true;

        $data['adults']['data'][0]['sliced'] = true;
        $data['adults']['data'][0]['selected'] = true;

//         echo "<pre>";print_r($data);//die();

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
        $data['gender'][0]['name'] = lang('label.gt1000');
        $data['gender'][1]['name'] = lang('label.lt1000');
        $data['gender'][2]['name'] = lang('label.undetectable');
        $data['gender'][3]['name'] = lang('label.invalids');
        $data['gender2'][0]['name'] = lang('label.not_suppressed');
        $data['gender2'][1]['name'] = lang('label.suppressed');

        $count = 0;

        $data["gender"][0]["data"][0] = $count;
        $data["gender"][1]["data"][0] = $count;
        $data["gender"][2]["data"][0] = $count;
        $data["gender"][3]["data"][0] = $count;
        $data["gender2"][0]["data"][0] = $count;
        $data["gender2"][1]["data"][0] = $count;
        $data['categories'][0] = lang('label.no_data');

        foreach ($result as $key => $value) {
            $data['categories'][$key] = $value['name'];
            $data["gender"][0]["data"][$key] = (int) $value['all_nonsuppressed'];
            $data["gender"][1]["data"][$key] = (int) $value['all_less1000'];
            $data["gender"][2]["data"][$key] = (int) $value['all_undetected'];
            $data["gender"][3]["data"][$key] = (int) $value['all_invalids'];
            $data["gender2"][0]["data"][$key] = (int) $value['nonsuppressed'];
            $data["gender2"][1]["data"][$key] = (int) $value['less1000'] + (int) $value['undetected'];
        }

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
        //$data['sample_types'][0]['name'] = lang('label.sample_type_plasma');
        $data['sample_types'][0]['name'] = lang('label.sample_type_DBS');
        $data['sample_types'][1]['name'] = lang('label.sample_type_EDTA');
        // $data['sample_types'][3]['name'] = 'Suppression';

        $count = 0;

        $data['categories'][0] = lang('label.no_data');
        //$data["sample_types"][0]["data"][0] = $count;
        $data["sample_types"][0]["data"][0] = $count;
        $data["sample_types"][1]["data"][0] = $count;
        // $data["sample_types"][3]["data"][0]	= $count;

        foreach ($result as $key => $value) {

            $data['categories'][$key] = $this->resolve_month($value['month']) . '-' . $value['year'];

            if ($all == 1) {
                //$data["sample_types"][0]["data"][$key] = (int) $value['allplasma'];
                $data["sample_types"][0]["data"][$key] = (int) $value['alldbs'];
                $data["sample_types"][1]["data"][$key] = (int) $value['alledta'];
            } else {
                //$data["sample_types"][0]["data"][$key] = (int) $value['plasma'];
                $data["sample_types"][0]["data"][$key] = (int) $value['alldbs'];
                $data["sample_types"][1]["data"][$key] = (int) $value['alledta'];
            }

            // $data["sample_types"][3]["data"][$key]	= round($value['suppression'],1);
        }

        return $data;
    }

    function download_sampletypes($year = null, $county = null, $partner = null) {
        $data = $this->get_sampletypesData($year, $county, $partner);
        $to_remove = ['edta' => '', 'dbs' => '', 'plasma' => '', 'allplasma' => '', 'suppressed' => '', 'all_suppressed' => '', 'tests' => '', 'suppression' => ''];
        foreach ($data as $v) {
            $data1[] = array_diff_key($v, $to_remove);
        }
        // echo "<pre>";print_r($result);die();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

        /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
        $f = fopen('php://memory', 'w');
        /** loop through array  */
        $b = array(lang('date_months'), lang('date_year'),
            lang('label.sample_type_EDTA'), lang('label.sample_type_DBS'), lang('label.all_tests'));

        fputcsv($f, $b, $delimiter);

        foreach ($data1 as $line) {
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
        $data['vl_outcomes']['data'][0]['color'] = '#DAA520'; //#40bf80';
        $data['vl_outcomes']['data'][1]['color'] = '#f72109';



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
        $data['gender2'][0]['name'] = lang('label.not_suppressed_');
        $data['gender2'][1]['name'] = lang('label.suppressed_');
        $data['gender'] = '';

        $data['categories'][0] = lang('label.no_data');
        $data["gender2"][0]["data"][0] = (int) $result->nogender_nonsuppressed;
        $data["gender2"][1]["data"][0] = (int) $result->nogender_suppressed;

        $data['categories'][1] = lang('label.male');
        $data["gender2"][0]["data"][1] = (int) $result->male_nonsuppressed;
        $data["gender2"][1]["data"][1] = (int) $result->male_suppressed;

        $data['categories'][2] = lang('label.female');
        $data["gender2"][0]["data"][2] = (int) $result->female_nonsuppressed;
        $data["gender2"][1]["data"][2] = (int) $result->female_suppressed;

        //$data['gender'][0]['drilldown']['color'] = '#913D88';
        //$data['gender'][1]['drilldown']['color'] = '#96281B';
        $data['gender2'][0]['drilldown']['color'] = '#40bf80';
        $data['gender2'][1]['drilldown']['color'] = '#f72109';
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
        $data['ageGnd2'][0]['name'] = lang('label.not_suppressed_');
        $data['ageGnd2'][1]['name'] = lang('label.suppressed_');
        $data['ageGnd'] = '';

        $data['categories'][0] = lang('label.no_data');
        $data["ageGnd2"][0]["data"][0] = (int) $result->noage_nonsuppressed;
        $data["ageGnd2"][1]["data"][0] = (int) $result->noage_suppressed;

        $data['categories'][1] = lang('label.less2');
        $data["ageGnd2"][0]["data"][1] = (int) $result->less2_nonsuppressed;
        $data["ageGnd2"][1]["data"][1] = (int) $result->less2_suppressed;

        $data['categories'][2] = lang('label.less9');
        $data["ageGnd2"][0]["data"][2] = (int) $result->less9_nonsuppressed;
        $data["ageGnd2"][1]["data"][2] = (int) $result->less9_suppressed;

        $data['categories'][3] = lang('label.less14');
        $data["ageGnd2"][0]["data"][3] = (int) $result->less14_nonsuppressed;
        $data["ageGnd2"][1]["data"][3] = (int) $result->less14_suppressed;

        $data['categories'][4] = lang('label.less19');
        $data["ageGnd2"][0]["data"][4] = (int) $result->less19_nonsuppressed;
        $data["ageGnd2"][1]["data"][4] = (int) $result->less19_suppressed;

        $data['categories'][5] = lang('label.less24');
        $data["ageGnd2"][0]["data"][5] = (int) $result->less24_nonsuppressed;
        $data["ageGnd2"][1]["data"][5] = (int) $result->less24_suppressed;

        $data['categories'][6] = lang('label.over25');
        $data["ageGnd2"][0]["data"][6] = (int) $result->over25_nonsuppressed;
        $data["ageGnd2"][1]["data"][6] = (int) $result->over25_suppressed;

        //$data['ageGnd'][0]['drilldown']['color'] = '#913D88';
        //$data['ageGnd'][1]['drilldown']['color'] = '#96281B';
        $data['ageGnd2'][0]['drilldown']['color'] = '#2f80d1';
        $data['ageGnd2'][1]['drilldown']['color'] = '#e8ee1d';

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
                $suppression = @round(($value['suppressed'] * 100 / $patients), 1);
                $coverage = @round(($patients * 100 / $value['totallstrpt']), 1);

                if ($count < 16) {
                    $li .= '<a href="javascript:void(0);" class="list-group-item" ><strong>' . $count . '.</strong>&nbsp;' . $name . ':&nbsp;' . $suppression . '%</a>';
                }

                $table .= '<tr>';
                $table .= '<td>' . $count . '</td>';
                $table .= '<td>' . $name . '</td>';
                $table .= '<td>' . $suppression . '%</td>';
                $table .= '<td>' . $patients . '</td>';
                //$table .= '<td>' . ($value['totallstrpt']) . '</td>';
                //`$table .= '<td>' . $coverage . '%</td>';
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
 





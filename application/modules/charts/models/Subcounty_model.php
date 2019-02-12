<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 
 */
class Subcounty_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    function subcounty_outcomes($year = NULL, $month = NULL, $to_year = null, $to_month = null) {
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

        $sql = "CALL `proc_get_vl_subcounty_outcomes`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $res = $this->db->query($sql)->result_array();

        $result = array_splice($res, 0, 50);
        $data['outcomes'][0]['name'] = lang('label.gt1000');
        $data['outcomes'][1]['name'] = lang('label.lt1000');
        $data['outcomes'][2]['name'] = lang('label.undetectable');
        $data['outcomes'][3]['name'] = lang('label.invalids');
        $data['outcomes2'][0]['name'] = lang('label.not_suppressed_');
        $data['outcomes2'][1]['name'] = lang('label.suppressed_');
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
            $data['outcomes2'][0]['data'][$key] = (int) $value['nonsuppressed'];
            $data['outcomes2'][1]['data'][$key] = (int) $value['suppressed'];
            $data['outcomes2'][2]['data'][$key] = @round((((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
        }

        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function county_subcounties($year = NULL, $month = NULL, $to_year = null, $to_month = null) {
        $table = '';
        $count = 1;
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


        $sql = "CALL `proc_get_vl_subcounty_details`(0,'" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($sql);die();
        foreach ($result as $key => $value) {
            $vl = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
            $vl_und = ((int) $value['undetected']);
            $suppressed = ((int) $value['undetected'] + (int) $value['less1000'] );
            $non_suppressed = ((int) $value['less5000'] + (int) $value['above5000']);
            $all_non_suppressed = ((int) $value['all_less5000'] + (int) $value['all_above5000']);
            $table .= "<tr>
						<td>" . ($key + 1) . "</td>";
            $table .= "<td>" . $value['subcounty'] . "</td>";
            $table .= "<td>" . $value['county'] . "</td>";
            $table .= "<td>" . number_format((int) $value['all_tests']) . "</td>";
            $table .= "<td>" . number_format((int) $value['all_invalids']) . "</td>";
            $table .= "<td>" . number_format((int) $value['all_undetected']) . "</td>";
            $table .= "<td>" . number_format((int) $value['all_less1000']) . "</td>";
            $table .= "<td>" . number_format((int) $all_non_suppressed) . "</td>";
            $table .= "<td>" . number_format($vl) . "</td>";
            $table .= "<td>" . number_format($vl_und) . "</td>";
            $table .= "<td>" . number_format((int) $value['less1000']) . "</td>";
            //$table .="<td>".number_format($suppressed)."</td>";	
            $table .= "<td>" . number_format($non_suppressed) . "</td>";

            $table .= "</tr>";
            $count++;
        }

        return $table;
    }

    function subcounty_vl_outcomes($year = NULL, $month = NULL, $subcounty = NULL, $to_year = null, $to_month = null) {
        if ($subcounty == null || $subcounty == 'null') {
            $subcounty = $this->session->userdata('sub_county_filter');
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

        $sql = "CALL `proc_get_vl_subcounty_vl_outcomes`('" . $subcounty . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        $sql2 = "CALL `proc_get_vl_current_suppression`('2','" . $subcounty . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        $this->db->close();
        $current = $this->db->query($sql2)->row();
        $this->db->close();


        $color = array('#6BB9F0', '#e8ee1d', '#2f80d1', '#5C97BF');

        $data['vl_outcomes']['name'] = lang('label.tests');
        $data['vl_outcomes']['colorByPoint'] = true;
        $data['vl_outcomes2']['name'] = lang('label.tests');
        $data['vl_outcomes2']['colorByPoint'] = true;
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
        $data['vl_outcomes']['data'][3]['sliced'] = true;
        $data['vl_outcomes']['data'][3]['selected'] = true;
        $data['vl_outcomes2']['data'][0]['sliced'] = true;
        $data['vl_outcomes2']['data'][0]['selected'] = true;

        return $data;
    }

    function subcounty_gender($year = NULL, $month = NULL, $subcounty = NULL, $to_year = null, $to_month = null) {
        if ($subcounty == null || $subcounty == 'null') {
            $subcounty = $this->session->userdata('sub_county_filter');
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

        $sql = "CALL `proc_get_vl_subcounty_gender`('" . $subcounty . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
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
        $data["gender"][0]["data"][0] = $count;
        $data["gender"][1]["data"][0] = $count;
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

        return $data;
    }

    function subcounty_age($year = NULL, $month = NULL, $subcounty = NULL, $to_year = null, $to_month = null) {
        if ($subcounty == null || $subcounty == 'null') {
            $subcounty = $this->session->userdata('sub_county_filter');
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

        $sql = "CALL `proc_get_vl_subcounty_age`('" . $subcounty . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
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
            $data['categories'][$key] = $value['name'];
            $data["ageGnd"][0]["data"][$key] = (int) $value['all_nonsuppressed'];
            $data["ageGnd"][1]["data"][$key] = (int) $value['all_less1000'];
            $data["ageGnd"][2]["data"][$key] = (int) $value['all_undetected'];
            $data["ageGnd"][3]["data"][$key] = (int) $value['all_invalids'];
            $data["ageGnd2"][0]["data"][$key] = (int) $value['nonsuppressed'];
            $data["ageGnd2"][1]["data"][$key] = (int) (int) $value['undetected'] + (int) $value['less1000'];
        }
        $data['ageGnd'][0]['drilldown']['color'] = '#913D88';
        $data['ageGnd'][0]['drilldown']['color'] = '#913D88';
        return $data;
    }

    function get_sampletypesData($year = null, $subcounty = null) {
        $array1 = array();
        $array2 = array();
        $sql2 = NULL;

        if ($subcounty == null || $subcounty == 'null') {
            $subcounty = $this->session->userdata('sub_county_filter');
        }

        if ($year == null || $year == 'null') {
            $to = $this->session->userdata('filter_year');
        } else {
            $to = $year;
        }
        $from = $to - 1;

        $sql = "CALL `proc_get_vl_subcounty_sample_types`('" . $subcounty . "','" . $from . "','" . $to . "')";
        // echo "<pre>";print_r($sql);die();
        $array1 = $this->db->query($sql)->result_array();
        return $array1;

        // if ($sql2) {
        // 	$this->db->close();
        // 	$array2 = $this->db->query($sql2)->result_array();
        // }
        // return array_merge($array1,$array2);
    }

    function subcounty_samples($year = NULL, $subcounty = NULL, $all = null) {
        $result = $this->get_sampletypesData($year, $subcounty);

        //$data['sample_types'][0]['name'] = lang('label.sample_type_plasma');
        $data['sample_types'][0]['name'] = lang('label.sample_type_DBS');
        $data['sample_types'][1]['name'] = lang('label.sample_type_EDTA');
        $data['sample_types'][0]['tooltip'] = array("valueSuffix" => ' ');
        $data['sample_types'][1]['tooltip'] = array("valueSuffix" => ' ');
        $count = 0;

        $data['categories'][0] = lang('label.no_data');
        $data["sample_types"][0]["data"][0] = $count;
        $data["sample_types"][1]["data"][0] = $count;

        foreach ($result as $key => $value) {

            $data['categories'][$key] = $this->resolve_month($value['month']) . '-' . $value['year'];

            if ($all == 1) {
                //$data["sample_types"][0]["data"][$key]	= (int) $value['allplasma'];
                $data["sample_types"][0]["data"][$key] = (int) $value['all_dbs'];
                $data["sample_types"][1]["data"][$key] = (int) $value['all_edta'];
            } else {
                //$data["sample_types"][0]["data"][$key]	= (int) $value['plasma'];
                $data["sample_types"][0]["data"][$key] = (int) $value['all_dbs'];
                $data["sample_types"][1]["data"][$key] = (int) $value['all_edta'];
            }
            // $data["sample_types"][3]["data"][$key]	= round($value['suppression'],1);
        }

        return $data;
    }

    function download_sampletypes($year = null, $subcounty = null) {
        $data = $this->get_sampletypesData($year, $subcounty);
                $to_remove = ['edta' => '', 'dbs' => '', 'plasma' => '', 'all_plasma' => '', 'suppressed' => '', 'all_suppressed' => '', 'tests' => '', 'suppression' => ''];
        foreach ($data as $v) {
            $data1[] = array_diff_key($v, $to_remove);
        }
         //echo "<pre>";print_r($data1);die();
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
        header('Content-Disposition: attachement; filename="' . Date('YmdH:i:s') . 'vl_subcountysampleTypes.csv";');
        /** Send file to browser for download */
        fpassthru($f);
    }

    function subcounty_sites($year = NULL, $month = NULL, $subcounty = NULL, $to_year = null, $to_month = null) {
        if ($subcounty == null || $subcounty == 'null') {
            $subcounty = $this->session->userdata('sub_county_filter');
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

        $table = '';
        $count = 1;

        $sql = "CALL `proc_get_vl_subcounty_sites_details`('" . $subcounty . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        foreach ($result as $key => $value) {
            $vl = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
            $vl_und = ((int) $value['undetected']);
           //$suppressed = ((int) $value['undetected'] + (int) $value['less1000'] );
            $non_suppressed = ((int) $value['less5000'] + (int) $value['above5000']);
            $all_non_suppressed = ((int) $value['all_less5000'] + (int) $value['all_above5000']);
            $table .= "<tr>
				<td>" . $count . "</td>";
            $table .= "<td>" . $value['name'] . "</td>";
            $table .= "<td>" . $value['subcounty'] . "</td>";
            $table .= "<td>" . $value['county'] . "</td>";
            $table .= "<td>" . number_format((int) $value['alltests']) . "</td>";
            $table .= "<td>" . number_format((int) $value['all_invalids']) . "</td>";
            $table .= "<td>" . number_format((int) $value['all_undetected']) . "</td>";
            $table .= "<td>" . number_format((int) $value['all_less1000']) . "</td>";
            $table .= "<td>" . number_format((int) $all_non_suppressed) . "</td>";
            $table .= "<td>" . number_format($vl) . "</td>";
            $table .= "<td>" . number_format($vl_und) . "</td>";
            $table .= "<td>" . number_format((int) $value['less1000']) . "</td>";
            //$table .="<td>".number_format($suppressed)."</td>";	
            $table .= "<td>" . number_format($non_suppressed) . "</td>";
            $table .= "</tr>";
            $count++;
        }

        return $table;
    }

    function download_subcounty_sites($year = NULL, $month = NULL, $subcounty = NULL, $to_year = null, $to_month = null) {
        if ($subcounty == null || $subcounty == 'null') {
            $subcounty = $this->session->userdata('sub_county_filter');
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

        $table = '';
        $count = 1;

        $sql = "CALL `proc_get_vl_subcounty_sites_details`('" . $subcounty . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";

        /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
        $f = fopen('php://memory', 'w');
        /** loop through array  */
        $b = array(lang('label.code.MFL'), lang('label.facility'), lang('label.table_county'),
            lang('label.sub_county'), lang('label.received'), lang('label.rejected'),
            lang('label.all_tests'), lang('label.redraws'), lang('label.undetected'),
            lang('label.less1000'), lang('label.above1000_less5000'), lang('label.above5000'),
            lang('label.baseline_tests'), lang('label.baseline_gt1000'), lang('label.confirmatory_tests'),
            lang('label.confirmatory_gt1000'));

        fputcsv($f, $b, $delimiter);

        foreach ($result as $line) {
            /** default php csv handler * */
            fputcsv($f, $line, $delimiter);
        }
        /** rewrind the "file" with the csv lines * */
        fseek($f, 0);
        /** modify header to be downloadable csv file * */
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . Date('Ymd H:i:s') . 'vl_subcounty_sites.csv";');
        /** Send file to browser for download */
        fpassthru($f);
    }

    function justification_breakdown($year = null, $month = null, $subcounty = null, $to_year = null, $to_month = null) {

        if ($subcounty == null || $subcounty == 'null') {
            $subcounty = $this->session->userdata('sub_county_filter');
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


        $sql = "CALL `proc_get_vl_pmtct`('1','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','','','','" . $subcounty . "','')";
        $sql2 = "CALL `proc_get_vl_pmtct`('2','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','','','','" . $subcounty . "','')";



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

        $data['just_breakdown'][0]['drilldown']['color'] = '#913D88';
        $data['just_breakdown'][1]['drilldown']['color'] = '#96281B';

        return $data;
    }

}

?>
<?php

defined('BASEPATH') or exit();

/**
 * 
 */
class Sites_model extends MY_Model {

    function __construct() {
        parent:: __construct();
    }

    function sites_outcomes($year = null, $month = null, $partner = null, $to_year = null, $to_month = null) {

        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }
        if ($partner == null || $partner == 'null') {
            $partner = $this->session->userdata('partner_year');
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

        if ($partner) {
            $sql = "CALL `proc_get_partner_sites_outcomes`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        } else {
            $sql = "CALL `proc_get_all_sites_outcomes`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        }
        // $sql = "CALL `proc_get_all_sites_outcomes`('".$year."','".$month."')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();

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

    function partner_sites_outcomes($year = NULL, $month = NULL, $partner = NULL, $to_year = null, $to_month = null, $all = 0) {
        $table = '';
        $count = 1;
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

        $sql = "CALL `proc_get_partner_sites_details`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','" . $all . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($sql);die();
        foreach ($result as $key => $value) {
            $table .= "<tr>
				<td>" . $count . "</td>";
            $table .= "<td>" . $value['name'] . "</td>";
            $table .= "<td>" . $value['partnername'] . "</td>";
            $table .= "<td>" . $value['subcounty'] . "</td>";
            $table .= "<td>" . $value['county'] . "</td>";
            $table .= "<td>" . number_format((int) $value['alltests']) . "</td>";
            $table .= "<td>" . number_format((int) $value['all_invalids']) . "</td>";
            $table .= "<td>" . number_format((int) $value['all_undetected']) . "</td>";
            $table .= "<td>" . number_format((int) $value['all_less1000']) . "</td>";
            $table .= "<td>" . number_format((int) $value['all_less5000'] + (int) $value['all_above5000']) . "</td>";
            $table .= "<td>" . number_format((int) $value['tests']) . "</td>";
            $table .= "<td>" . number_format((int) $value['undetected']) . "</td>";
            $table .= "<td>" . number_format((int) $value['less1000']) . "</td>";
            $table .= "<td>" . number_format((int) $value['less5000'] + (int) $value['above5000']) . "</td>";
            $count++;
        }

        return $table;
    }

    function sites_trends($year = null, $month = null, $site = null, $to_year = null, $to_month = null) {
        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }
        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }
        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
        }
        $data['year'] = $year;

        $sql = "CALL `proc_get_sites_trends`('" . $site . "','" . $year . "')";

        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();

        $months = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $count = 0;


        $data['test_trends'][0]['name'] = lang('label.tests');
        $data['test_trends'][1]['name'] = lang('label.suppressed_');
        $data['test_trends'][2]['name'] = lang('label.non_suppressed_');
        //$data['test_trends'][3]['name'] = lang('label.rejected');

        $data['test_trends'][0]['data'][0] = $count;
        $data['test_trends'][1]['data'][0] = $count;
        $data['test_trends'][2]['data'][0] = $count;
        //$data['test_trends'][3]['data'][0] = $count;

        foreach ($months as $key1 => $value1) {
            $data['test_trends'][0]['data'][$count] = null;
            $data['test_trends'][1]['data'][$count] = null;
            $data['test_trends'][2]['data'][$count] = null;
            //$data['test_trends'][3]['data'][$count] = null;
            foreach ($result as $key2 => $value2) {
                if ((int) $value1 == (int) $value2['month']) {
                    $data['test_trends'][0]['data'][$count] = (int) $value2['alltests'];
                    $data['test_trends'][1]['data'][$count] = (int) $value2['undetected'] + (int) $value2['less1000'];
                    $data['test_trends'][2]['data'][$count] = (int) $value2['less5000'] + (int) $value2['above5000'];
                    //$data['test_trends'][3]['data'][$count] = (int) $value2['rejected'];
                }
            }
            $count++;
        }

//        echo "<pre>";
//        print_r($data);
//        die();
        return $data;
    }

    function site_outcomes_chart($year = null, $month = null, $site = null, $to_year = null, $to_month = null) {
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
        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
        }

        $sql = "CALL `proc_get_sites_sample_types`('" . $site . "','" . $year . "')";

        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
//        echo "<pre>";print_r($result);die();
        $months = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

        //$data['sample_types'][0]['name'] = lang('label.sample_type_plasma');
        $data['sample_types'][0]['name'] = lang('label.sample_type_DBS');
        $data['sample_types'][1]['name'] = lang('label.sample_type_EDTA');

        $data['categories'] = array(lang('cal_jan'), lang('cal_feb'), lang('cal_mar'), lang('cal_apr'), lang('cal_may'), lang('cal_jun'), lang('cal_jul'), lang('cal_aug'), lang('cal_sep'), lang('cal_oct'), lang('cal_nov'), lang('cal_dec'));
        /* $data["sample_types"][0]["data"][0] = $count;
          $data["sample_types"][1]["data"][0] = $count;
          $data["sample_types"][2]["data"][0] = $count; */
        foreach ($months as $value) {
            foreach ($result as $value1) {
                if ((int) $value == (int) $value1['month']) {
                    //$data["sample_types"][0]["data"][] = (int) $value1['plasma'];
                    $data["sample_types"][0]["data"][] = (int) $value1['all_dbs'];
                    $data["sample_types"][1]["data"][] = (int) $value1['all_edta'];
                    continue(2);
                }
            }
            //$data["sample_types"][0]["data"][] = 0;
            $data["sample_types"][0]["data"][] = 0;
            $data["sample_types"][1]["data"][] = 0;
        }
//         echo "<pre>";print_r($data);die();

        return $data;
    }

    function sites_vloutcomes($year = null, $month = null, $site = null, $to_year = null, $to_month = null) {
        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
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

        $sql = "CALL `proc_get_sites_vl_outcomes`('" . $site . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        $sql2 = "CALL `proc_get_vl_current_suppression`('4','" . $site . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        $this->db->close();

        // echo "<pre>";print_r($result);die();
        $color = array('#6BB9F0', '#e8ee1d', '#2f80d1', '#5C97BF');

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
        $data['vl_outcomes'][0]['type'] = "column";
        $data['vl_outcomes'][1]['type'] = "column";
        $data['vl_outcomes'][2]['type'] = "column";
        $data['vl_outcomes'][3]['type'] = "column";
        $data['vl_outcomes2'][0]['type'] = "column";
        $data['vl_outcomes2'][1]['type'] = "column";

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

        return $data;
    }

    function sites_age($year = null, $month = null, $site = null, $to_year = null, $to_month = null) {
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
        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }
        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
        }

        $sql = "CALL `proc_get_sites_age`('" . $site . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $count = 0;
        $loop = 0;
        $name = '';
        $nonsuppressed = 0;
        $suppressed = 0;

        // echo "<pre>";print_r($result);die();
        $data['ageGnd'][0]['name'] = lang('label.gt1000');
        $data['ageGnd'][1]['name'] = lang('label.lt1000');
        $data['ageGnd'][2]['name'] = lang('label.undetectable');
        $data['ageGnd'][3]['name'] = lang('label.invalids');
        $data['ageGnd2'][0]['name'] = lang('label.not_suppressed_');
        $data['ageGnd2'][1]['name'] = lang('label.suppressed_');
        $data['ageGnd2'][2]['name'] = lang('label.suppression');

        $count = 0;

        $data["ageGnd"][0]["data"][0] = $count;
        $data["ageGnd"][1]["data"][0] = $count;
        $data["ageGnd"][2]["data"][0] = $count;
        $data["ageGnd"][3]["data"][0] = $count;
        $data["ageGnd2"][0]["data"][0] = $count;
        $data["ageGnd2"][1]["data"][0] = $count;
        $data["ageGnd2"][2]["data"][0] = $count;
        $data['categories'][0] = lang('label.no_data');
        $data['ageGnd2'][2]['tooltip'] = array("valueSuffix" => ' %');

        $data['ageGnd'][0]['type'] = "column";
        $data['ageGnd'][1]['type'] = "column";
        $data['ageGnd'][2]['type'] = "column";
        $data['ageGnd'][3]['type'] = "column";
        $data['ageGnd2'][0]['type'] = "column";
        $data['ageGnd2'][1]['type'] = "column";
        $data['ageGnd2'][2]['type'] = "spline";

        foreach ($result as $key => $value) {
            if ($value['name'] == lang('label.no_data')) {
                $loop = $key;
                $name = $value['name'];
                $all_nonsuppressed = $value['all_nonsuppressed'];
                $all_less1000 = $value['all_less1000'];
                $all_undetected = $value['all_undetected'];
                $all_invalids = $value['all_invalids'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
                $suppression = @round((((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            } else if ($value['name'] == lang('label.less2')) {
                $loop = $key;
                $name = $value['name'];
                $all_nonsuppressed = $value['all_nonsuppressed'];
                $all_less1000 = $value['all_less1000'];
                $all_undetected = $value['all_undetected'];
                $all_invalids = $value['all_invalids'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
                $suppression = @round((((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            } else if ($value['name'] == lang('label.less9')) {
                $loop = $key;
                $name = $value['name'];
                $all_nonsuppressed = $value['all_nonsuppressed'];
                $all_less1000 = $value['all_less1000'];
                $all_undetected = $value['all_undetected'];
                $all_invalids = $value['all_invalids'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
                $suppression = @round((((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            } else if ($value['name'] == lang('label.less14')) {
                $loop = $key;
                $name = $value['name'];
                $all_nonsuppressed = $value['all_nonsuppressed'];
                $all_less1000 = $value['all_less1000'];
                $all_undetected = $value['all_undetected'];
                $all_invalids = $value['all_invalids'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
                $suppression = @round((((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            } else if ($value['name'] == lang('label.less19')) {
                $loop = $key;
                $name = $value['name'];
                $all_nonsuppressed = $value['all_nonsuppressed'];
                $all_less1000 = $value['all_less1000'];
                $all_undetected = $value['all_undetected'];
                $all_invalids = $value['all_invalids'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
                $suppression = @round((((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            } else if ($value['name'] == lang('label.less24')) {
                $loop = $key;
                $name = $value['name'];
                $all_nonsuppressed = $value['all_nonsuppressed'];
                $all_less1000 = $value['all_less1000'];
                $all_undetected = $value['all_undetected'];
                $all_invalids = $value['all_invalids'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
                $suppression = @round((((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            } else if ($value['name'] == lang('label.over25')) {
                $loop = $key;
                $name = $value['name'];
                $all_nonsuppressed = $value['all_nonsuppressed'];
                $all_less1000 = $value['all_less1000'];
                $all_undetected = $value['all_undetected'];
                $all_invalids = $value['all_invalids'];
                $nonsuppressed = $value['nonsuppressed'];
                $suppressed = $value['suppressed'];
                $suppression = @round((((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            }

            $data['categories'][$loop] = $name;
            $data["ageGnd"][0]["data"][$loop] = (int) $all_nonsuppressed;
            $data["ageGnd"][1]["data"][$loop] = (int) $all_less1000;
            $data["ageGnd"][2]["data"][$loop] = (int) $all_undetected;
            $data["ageGnd"][3]["data"][$loop] = (int) $all_invalids;
            $data["ageGnd2"][0]["data"][$loop] = (int) $nonsuppressed;
            $data["ageGnd2"][1]["data"][$loop] = (int) $suppressed;
            $data["ageGnd2"][2]["data"][$loop] = $suppression;
        }
        // die();
        $data['ageGnd'][0]['drilldown']['color'] = '#913D88';
        $data['ageGnd'][1]['drilldown']['color'] = '#96281B';

        // echo "<pre>";print_r($data);die();
        $data['categories'] = array_values($data['categories']);
        $data["ageGnd"][0]["data"] = array_values($data["ageGnd"][0]["data"]);
        $data["ageGnd"][1]["data"] = array_values($data["ageGnd"][1]["data"]);
        $data["ageGnd"][2]["data"] = array_values($data["ageGnd"][2]["data"]);
        $data["ageGnd"][3]["data"] = array_values($data["ageGnd"][3]["data"]);
        $data["ageGnd2"][0]["data"] = array_values($data["ageGnd2"][0]["data"]);
        $data["ageGnd2"][1]["data"] = array_values($data["ageGnd2"][1]["data"]);
        $data["ageGnd2"][2]["data"] = array_values($data["ageGnd2"][2]["data"]);
        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function sites_gender($year = null, $month = null, $site = null, $to_year = null, $to_month = null) {
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
        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }
        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
        }

        $sql = "CALL `proc_get_sites_gender`('" . $site . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $data['gender'][0]['name'] = lang('label.gt1000');
        $data['gender'][1]['name'] = lang('label.lt1000');
        $data['gender'][2]['name'] = lang('label.undetectable');
        $data['gender'][3]['name'] = lang('label.invalids');
        $data['gender2'][0]['name'] = lang('label.not_suppressed_');
        $data['gender2'][1]['name'] = lang('label.suppressed_');
        $data['gender2'][2]['name'] = lang('label.suppression');

        $count = 0;

        $data["gender"][0]["data"][0] = $count;
        $data["gender"][1]["data"][0] = $count;
        $data["gender"][2]["data"][0] = $count;
        $data["gender"][3]["data"][0] = $count;
        $data["gender2"][0]["data"][0] = $count;
        $data["gender2"][1]["data"][0] = $count;
        $data['categories'][0] = lang('label.no_data');

        $data['gender'][0]['type'] = "column";
        $data['gender'][1]['type'] = "column";
        $data['gender'][2]['type'] = "column";
        $data['gender'][3]['type'] = "column";
        $data['gender2'][0]['type'] = "column";
        $data['gender2'][1]['type'] = "column";
        $data['gender2'][2]['type'] = "spline";

        foreach ($result as $key => $value) {
            $data['categories'][$key] = $value['name'];
            $data["gender"][0]["data"][$key] = (int) $value['all_nonsuppressed'];
            $data["gender"][1]["data"][$key] = (int) $value['all_less1000'];
            $data["gender"][2]["data"][$key] = (int) $value['all_undetected'];
            $data["gender"][3]["data"][$key] = (int) $value['all_invalids'];
            $data["gender2"][0]["data"][$key] = (int) $value['nonsuppressed'];
            $data["gender2"][1]["data"][$key] = (int) $value['suppressed'];
            $data["gender2"][2]["data"][$key] = @round((((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
        }

        $data['gender'][0]['drilldown']['color'] = '#913D88';
        $data['gender'][1]['drilldown']['color'] = '#96281B';
        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function partner_sites_outcomes_download($year = NULL, $month = NULL, $partner = NULL, $to_year = null, $to_month = null, $all = "0") {
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
        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        }

        $sql = "CALL `proc_get_partner_sites_details`('" . $partner . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $data = $this->db->query($sql)->result_array();

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
            /** default php csv handler * */
            fputcsv($f, $line, $delimiter);
        }
        /** rewrind the "file" with the csv lines * */
        fseek($f, 0);
        /** modify header to be downloadable csv file * */
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="vl_partner_sites.csv";');
        /** Send file to browser for download */
        fpassthru($f);
    }

    function justification($year = null, $month = null, $site = null, $to_year = null, $to_month = null) {

        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
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

        $sql = "CALL `proc_get_vl_site_justification`('" . $site . "','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        $data['justification']['name'] = lang('label.tests');
        $data['justification']['colorByPoint'] = true;

        $count = 0;

        $data['justification']['data'][0]['name'] = lang('label.no_data');

        foreach ($result as $key => $value) {
            if ($value['name'] == 'Routine VL') {
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

    function justification_breakdown($year = null, $month = null, $site = null, $to_year = null, $to_month = null) {

        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
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


        $sql = "CALL `proc_get_vl_pmtct`('1','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','','','','','" . $site . "')";
        $sql2 = "CALL `proc_get_vl_pmtct`('2','" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "','','','','','" . $site . "')";



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

    function get_patients($site = null, $year = null, $month = null, $to_year = NULL, $to_month = NULL) {
        $type = 0;

        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
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

        $query = $this->db->get_where('facilitys', array('id' => $site), 1)->row();

        $facility = $query->facilitycode;

        $this->db->close();

        $sql = "CALL `proc_get_vl_site_patients`('" . $site . "','" . $year . "')";
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
        if (is_array(@result)) {
            foreach ($result as $key => $value) {

                $data['categories'][$key] = $value->tests;

                $data['outcomes'][0]['data'][$key] = (int) $value->totals;

                $data['outcomes'][0]['data'][$key] = (int) $value->totals;
                $data['unique_patients'] += (int) $value->totals;
                $data['total_tests'] += ($data['categories'][$key] * $data['outcomes'][0]['data'][$key]);
                $data['size'] ++;
            }
        }

        $data['coverage'] = @round(($data['unique_patients'] / $data['total_patients'] * 100), 2);

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

    function current_suppression($site = null) {
        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
        }

        $sql = "CALL `proc_get_vl_current_suppression`('4','" . $site . "')";
        $result = $this->db->query($sql)->row();

        $this->db->close();

        //echo "<pre>";print_r($result);
        //$color = array('#6BB9F0', '#e8ee1d', '#2f80d1', '#5C97BF');

        $data['vl_outcomes']['name'] = lang('label.tests');
        $data['vl_outcomes']['colorByPoint'] = true;
        $data['ul'] = '';

        $data['vl_outcomes']['data'][0]['name'] = lang('label.suppressed_');
        $data['vl_outcomes']['data'][1]['name'] = lang('label.not_suppressed_');

        $data['vl_outcomes']['data'][0]['y'] = (int) $result->suppressed;
        $data['vl_outcomes']['data'][1]['y'] = (int) $result->nonsuppressed;

        $data['vl_outcomes']['data'][0]['color'] = '#2f80d1';
        $data['vl_outcomes']['data'][1]['color'] = '#e8ee1d';


        $data['vl_outcomes']['data'][0]['sliced'] = true;
        $data['vl_outcomes']['data'][0]['selected'] = true;

        $data['total'][0] = (int) $result->suppressed;
        $data['total'][1] = (int) $result->nonsuppressed;

        return $data;
    }

    function get_patients_outcomes($site = null, $year = null, $month = null, $to_year = NULL, $to_month = NULL) {
        $type = 0;

        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
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

        $query = $this->db->get_where('facilitys', array('id' => $site), 1)->row();

        $facility = $query->facilitycode;

        $params = "patient/facility/{$facility}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";

        $result = $this->req($params);

        $data['categories'] = array(lang('label.total_patients'), lang('label.vl_done'));
        $data['outcomes']['name'] = lang('label.tests');
        $data['outcomes']['data'][0] = (int) $result->total_patients;
        $data['outcomes']['data'][1] = (int) $result->total_tests;
        $data["outcomes"]["color"] = '#2f80d1';

        return $data;
    }

    function get_patients_graph($site = null, $year = null, $month = null, $to_year = NULL, $to_month = NULL) {
        $type = 0;

        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
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
        $data["outcomes"]["color"] = '#2f80d1';

        return $data;
    }

    function site_patients($site = null, $year = null, $month = null, $to_year = NULL, $to_month = NULL) {
        $type = 0;

        if ($site == null || $site == 'null') {
            $site = $this->session->userdata('site_filter');
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

        $query = $this->db->get_where('facilitys', array('id' => $site), 1)->row();

        $facility = $query->facilitycode;

        $params = "patient/facility/{$facility}/{$type}/{$year}/{$month}/{$to_year}/{$to_month}";

        $data = $this->req($params);

        return $data;
    }

}

?>

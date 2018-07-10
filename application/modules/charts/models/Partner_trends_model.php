<?php

defined("BASEPATH") or exit("No direct script access allowed");

/**
 * 
 */
class Partner_trends_model extends MY_Model {

    function __construct() {
        parent:: __construct();
        ;
    }

    function yearly_trends($partner = NULL) {

        if ($partner == NULL || $partner == 'NA') {
            $partner = 0;
        }

        if ($partner == 0) {
            $sql = "CALL `proc_get_vl_national_yearly_trends`();";
        } else {
            $sql = "CALL `proc_get_vl_partner_yearly_trends`(" . $partner . ");";
        }

        $result = $this->db->query($sql)->result_array();

        $year;
        $i = 0;
        $b = true;

        $data = [
            'suppression_trends' => [],
            'test_trends' => [],
            'rejected_trends' => [],
            'tat_trends' => [],
        ];

                $months = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        foreach ($months as $m) {
            foreach ($result as $value) {

                if ($b) {
                    $b = false;
                    $year = (int) $value['year'];
                }

                $y = (int) $value['year'];
                if ($value['year'] != $year) {
                    $i++;
                    $year--;
                }

                $month = (int) $value['month'];
                //$month--;
                if ((int) $m == (int) $month) {
                    $tests = (int) $value['suppressed'] + (int) $value['nonsuppressed'];

                    $data['suppression_trends'][$i]['name'] = $value['year'];
                    $data['suppression_trends'][$i]['data'][] = round((($value['suppressed'] * 100) / $tests), 4, PHP_ROUND_HALF_UP);


                    $data['test_trends'][$i]['name'] = $value['year'];
                    $data['test_trends'][$i]['data'][] = $tests;

                    $data['rejected_trends'][$i]['name'] = $value['year'];
                    $data['rejected_trends'][$i]['data'][] = round((($value['rejected'] * 100) / $value['received']), 4, PHP_ROUND_HALF_UP);

                    $data['tat_trends'][$i]['name'] = $value['year'];
                    $data['tat_trends'][$i]['data'][] = (int) $value['tat4'];

                    continue(2);
                }
            }
            $data['suppression_trends'][$i]['name'] = $value['year'];
            $data['suppression_trends'][$i]['data'][] = 0;
            $data['test_trends'][$i]['name'] = $value['year'];
            $data['test_trends'][$i]['data'][] = 0;
            $data['rejected_trends'][$i]['name'] = $value['year'];
            $data['rejected_trends'][$i]['data'][] = 0;
            $data['tat_trends'][$i]['name'] = $value['year'];
            $data['tat_trends'][$i]['data'][] = 0;
        }

        return $data;
    }

    function yearly_summary($partner = NULL) {

        if ($partner == NULL || $partner == 'NA') {
            $partner = 0;
        }

        if ($partner == 0) {
            $sql = "CALL `proc_get_vl_national_yearly_summary`();";
        } else {
            $sql = "CALL `proc_get_vl_partner_yearly_summary`(" . $partner . ");";
        }
        // echo "<pre>";print_r($sql);die();

        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $year = date("Y");
        $i = 0;

        $data['outcomes'][0]['name'] = lang('label.non_suppressed_');
        $data['outcomes'][1]['name'] = lang('label.suppressed_');
        $data['outcomes'][2]['name'] = lang('label.suppression');

        $data['outcomes'][0]['type'] = "column";
        $data['outcomes'][1]['type'] = "column";
        $data['outcomes'][2]['type'] = "spline";

        $data['outcomes'][0]['yAxis'] = 1;
        $data['outcomes'][1]['yAxis'] = 1;

        foreach ($result as $key => $value) {
            $data['categories'][$i] = $value['year'];

            $data['outcomes'][0]['data'][$i] = (int) $value['nonsuppressed'];
            $data['outcomes'][1]['data'][$i] = (int) $value['suppressed'];
            $data['outcomes'][2]['data'][$i] = round(@(((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            $i++;
        }
        $data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

        $data['title'] = lang('label.outcomes');

        return $data;
    }

}

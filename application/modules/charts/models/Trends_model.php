<?php

defined("BASEPATH") or exit("No direct script access allowed");

/**
 * 
 */
class Trends_model extends MY_Model {

    function __construct() {
        parent:: __construct();
        ;
    }

    function yearly_trends($county = NULL) {

        if ($county == NULL || $county == 48) {
            $county = 0;
        }

        if ($county == 0) {
            $sql = "CALL `proc_get_vl_national_yearly_trends`();";
        } else {
            $sql = "CALL `proc_get_vl_yearly_trends`(" . $county . ");";
        }

        $result = $this->db->query($sql)->result_array();

        $i = 0;
        $y = 0;

        $data = [
            'suppression_trends' => [],
            'test_trends' => [],
            //'rejected_trends' => [],
            'tat_trends' => [],
        ];
        $months = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

        //extract years
        $years = [];
        foreach ($result as $row) {
            $years[] = $row['year'];
        }
        $unique_years = array_unique($years);

        foreach ($unique_years as $unique_year) {
            $y = 0;
            foreach ($months as $m) {
                $data['suppression_trends'][$i]['data'][$y] = 0;
                $data['test_trends'][$i]['data'][$y] = 0;
                //$data['rejected_trends'][$i]['data'][] = 0;
                $data['tat_trends'][$i]['data'][$y] = 0;
                foreach ($result as $value) {
                    if ((int) $value['year'] == (int) $unique_year) {
                        $data['suppression_trends'][$i]['name'] = $unique_year;
                        $data['test_trends'][$i]['name'] = $unique_year;
                        //$data['rejected_trends'][$i]['name'] = $unique_year;
                        $data['tat_trends'][$i]['name'] = $unique_year;
                        if ((int) $m == (int) $value['month']) {
                            $tests = (int) $value['suppressed'] + (int) $value['nonsuppressed'];
                            $all_tests = (int) $value['all_suppressed'] + (int) $value['all_nonsuppressed'];
                            $data['suppression_trends'][$i]['data'][$y] = @round((($value['suppressed'] * 100) / $tests), 4, PHP_ROUND_HALF_UP);
                            $data['test_trends'][$i]['data'][$y] = $all_tests;
                            //$data['rejected_trends'][$i]['data'][] = @round((($value['rejected'] * 100) / $value['received']), 4, PHP_ROUND_HALF_UP);
                            $data['tat_trends'][$i]['data'][$y] = (int) $value['tat4'];
                        }
                    }
                }
                $y++;
            }
            $i++;
        }
        return $data;
    }

    function yearly_summary($county = NULL) {
        $months = [
            1 => lang('cal_jan'), 2 => lang('cal_feb'), 3 => lang('cal_mar'), 4 => lang('cal_apr'), 5 => lang('cal_may'), 6 => lang('cal_jun'),
            7 => lang('cal_jul'), 8 => lang('cal_aug'), 9 => lang('cal_sep'), 10 => lang('cal_oct'), 11 => lang('cal_nov'), 12 => lang('cal_dec')
        ];

        if ($county == NULL || $county == 48) {
            $county = 0;
        }

        if ($county == 0) {
            $sql = "CALL `proc_get_vl_national_yearly_summary`();";
        } else {
            $sql = "CALL `proc_get_vl_yearly_summary`(" . $county . ");";
        }
        // echo "<pre>";print_r($sql);die();

        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $year = date("Y");
        $i = 0;

        $data['outcomes'][0]['name'] = lang('label.gt1000');
        $data['outcomes'][1]['name'] = lang('label.lt1000');
        $data['outcomes'][2]['name'] = lang('label.undetectable');
        $data['outcomes'][3]['name'] = lang('label.invalids');
        //$data['outcomes'][2]['name'] = lang('label.suppression');
        $data['outcomes2'][0]['name'] = lang('label.gt1000');
        $data['outcomes2'][1]['name'] = lang('label.lt1000');


        //$data['outcomes'][0]['drilldown']['color'] = '#913D88';
        //$data['outcomes'][1]['drilldown']['color'] = '#96281B';
        //$data['outcomes'][2]['color'] = '#257766';

        $data['outcomes'][0]['type'] = "column";
        $data['outcomes'][1]['type'] = "column";
        $data['outcomes'][2]['type'] = "column";
        $data['outcomes'][3]['type'] = "column";
        $data['outcomes2'][0]['type'] = "column";
        $data['outcomes2'][1]['type'] = "column";

        $data['outcomes'][0]['yAxis'] = 1;
        $data['outcomes'][1]['yAxis'] = 1;
        $data['outcomes'][2]['yAxis'] = 1;
        $data['outcomes'][3]['yAxis'] = 1;
        $data['outcomes2'][0]['yAxis'] = 1;
        $data['outcomes2'][1]['yAxis'] = 1;

        foreach ($result as $key => $value) {
            $data['categories'][$i] = $months[$value['month']] . '-' . $value['year'];
            $data['outcomes'][0]['data'][$i] = (int) $value['all_nonsuppressed'];
            $data['outcomes'][1]['data'][$i] = (int) $value['all_less1000'];
            $data['outcomes'][2]['data'][$i] = (int) $value['all_undetected'];
            $data['outcomes'][3]['data'][$i] = (int) $value['all_invalids'];
            $i++;
        }
        $data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][3]['tooltip'] = array("valueSuffix" => ' ');
        // $data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');
        $data['outcomes2'][0]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes2'][1]['tooltip'] = array("valueSuffix" => ' ');

        $data['title'] = " ";

        return $data;
    }

    function quarterly_trends($county = NULL) {

        if ($county == NULL || $county == 48) {
            $county = 0;
        }

        if ($county == 0) {
            $sql = "CALL `proc_get_vl_national_yearly_trends`();";
        } else {
            $sql = "CALL `proc_get_vl_yearly_trends`(" . $county . ");";
        }

        $result = $this->db->query($sql)->result_array();
        $count_result = count($result);
        for ($k = 0; $k < $count_result; $k++) {
            $quarter = ceil($result[$k]['month'] / 3);
            $result[$k]['quarter'] = $quarter;
        }
        $data = [
            'suppression_trends' => [],
            'test_trends' => [],
            //'rejected_trends' => [],
            'tat_trends' => [],
        ];
        $i = 0;
        $m = 0;

        //extract distincts quarters
        $quarters = [];
        foreach ($result as $value) {
            $index = (int) ceil($value['month'] / 3);
            $quarters[] = $value['year'] . '-Q' . $index;
        }
        $distinct_quarters = array_unique($quarters);

        foreach ($distinct_quarters as $distinct_quarter) {
            $y = 0;
            for ($m = 1; $m <= 3; $m++) {
                $data['suppression_trends'][$i]['data'][$y] = 0;
                $data['test_trends'][$i]['data'][$y] = 0;
                //$data['rejected_trends'][$i]['data'][] = 0;
                $data['tat_trends'][$i]['data'][$y] = 0;
                foreach ($result as $value) {
                    if (((int) $value['year'] == (int) substr($distinct_quarter, 0, 4)) && in_array((int) $value['month'], $this->getQuarterMonths($distinct_quarter))) {
                        $data['suppression_trends'][$i]['name'] = $distinct_quarter;
                        $data['test_trends'][$i]['name'] = $distinct_quarter;
                        $data['tat_trends'][$i]['name'] = $distinct_quarter;
                        if ($m == $this->getRangeFromQuarterMonth($value['month'])) {
                            $tests = (int) $value['suppressed'] + (int) $value['nonsuppressed'];
                            $all_tests = (int) $value['all_suppressed'] + (int) $value['all_nonsuppressed'];
                            $data['suppression_trends'][$i]['data'][$y] = @round((($value['suppressed'] * 100) / $tests), 4, PHP_ROUND_HALF_UP);
                            $data['test_trends'][$i]['data'][$y] = $all_tests;
                            $data['tat_trends'][$i]['data'][$y] = (int) $value['tat4'];
                        }
                    }
                }
                $y++;
            }
            $i++;
        }
        return $data;
    }

    function quarterly_outcomes($county = NULL) {

        if ($county == NULL || $county == 48) {
            $county = 0;
        }

        if ($county == 0) {
            $sql = "CALL `proc_get_vl_national_yearly_trends`();";
        } else {
            $sql = "CALL `proc_get_vl_yearly_trends`(" . $county . ");";
        }

        $result = $this->db->query($sql)->result_array();
        $data['outcomes'][0]['name'] = lang('label.gt1000');
        $data['outcomes'][1]['name'] = lang('label.lt1000');
        $data['outcomes'][2]['name'] = lang('label.undetectable');
        $data['outcomes'][3]['name'] = lang('label.invalids');
        $data['outcomes2'][0]['name'] = lang('label.gt1000');
        $data['outcomes2'][1]['name'] = lang('label.lt1000');

        $data['outcomes'][0]['type'] = "column";
        $data['outcomes'][1]['type'] = "column";
        $data['outcomes'][2]['type'] = "column";
        $data['outcomes'][3]['type'] = "column";
        $data['outcomes2'][0]['type'] = "column";
        $data['outcomes2'][1]['type'] = "column";

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

        $data['title'] = '';



        $i = 0;
        //extract distincts quarters
        $quarters = [];
        foreach ($result as $value) {
            $index = (int) ceil($value['month'] / 3);
            $quarters[] = $value['year'] . '-Q' . $index;
        }
        $distinct_quarters = array_unique($quarters);
        asort($distinct_quarters);
        $nb = count($distinct_quarters);
        $data['categories'] = array_fill(0, $nb, "-");
        $data['outcomes'][0]['data'] = array_fill(0, $nb, 0);
        $data['outcomes'][1]['data'] = array_fill(0, $nb, 0);
        $data['outcomes'][2]['data'] = array_fill(0, $nb, 0);
        $data['outcomes'][3]['data'] = array_fill(0, $nb, 0);
        $data['outcomes2'][0]['data'] = array_fill(0, $nb, 0);
        $data['outcomes2'][1]['data'] = array_fill(0, $nb, 0);

        foreach ($distinct_quarters as $distinct_quarter) {
//            $val_sup = 0;
//            $val_sup_unsup = 0;
            foreach ($result as $value) {
                if (((int) $value['year'] == (int) substr($distinct_quarter, 0, 4)) && in_array((int) $value['month'], $this->getQuarterMonths($distinct_quarter))) {

                    $data['categories'][$i] = $distinct_quarter;
                    $data['outcomes'][0]['data'][$i] += (int) $value['all_nonsuppressed'];
                    $data['outcomes'][1]['data'][$i] += (int) $value['all_less1000'];
                    $data['outcomes'][2]['data'][$i] += (int) $value['all_undetected'];
                    $data['outcomes'][3]['data'][$i] += (int) $value['all_invalids'];
                    $data['outcomes2'][0]['data'][$i] += (int) $value['all_nonsuppressed'];
                    $data['outcomes2'][1]['data'][$i] += (int) $value['all_suppressed'];
//                    $val_sup += (int) $value['suppressed'];
//                    $val_sup_unsup += (int) $value['nonsuppressed'];
                }
            }
//            $data['outcomes'][2]['data'][$i] += @round((( $val_sup * 100) / ($val_sup + $val_sup_unsup)), 1);
            $i++;
        }
        return $data;
    }

    public function getQuarterMonths($quarter) {
        $q = substr($quarter, -2);
        $qs = [
            'Q1' => [1, 2, 3],
            'Q2' => [4, 5, 6],
            'Q3' => [7, 8, 9],
            'Q4' => [10, 11, 12],
        ];

        return $qs[$q];
    }

    public function getRangeFromQuarterMonth($month) {
        $r = $month % 3;
        if ($r == 0) {
            $r = 3;
        }
        return $r;
    }

}

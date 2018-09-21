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

        $year=0;
        $i = 0;
        $b = true;

        $data = [
            'suppression_trends' => [],
            'test_trends' => [],
            //'rejected_trends' => [],
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
                    $data['suppression_trends'][$i]['data'][] = @round((($value['suppressed'] * 100) / $tests), 4, PHP_ROUND_HALF_UP);


                    $data['test_trends'][$i]['name'] = $value['year'];
                    $data['test_trends'][$i]['data'][] = $tests;

                    //$data['rejected_trends'][$i]['name'] = $value['year'];
                    //$data['rejected_trends'][$i]['data'][] = @round((($value['rejected'] * 100) / $value['received']), 4, PHP_ROUND_HALF_UP);

                    $data['tat_trends'][$i]['name'] = $value['year'];
                    $data['tat_trends'][$i]['data'][] = (int) $value['tat4'];

                    continue(2);
                }
            }
            if(isset($value)){
            $data['suppression_trends'][$i]['name'] = $value['year'];
            $data['suppression_trends'][$i]['data'][] = 0;
            $data['test_trends'][$i]['name'] = $value['year'];
            $data['test_trends'][$i]['data'][] = 0;
            //$data['rejected_trends'][$i]['name'] = $value['year'];
            //$data['rejected_trends'][$i]['data'][] = 0;
            $data['tat_trends'][$i]['name'] = $value['year'];
            $data['tat_trends'][$i]['data'][] = 0;
            }
        }


        return $data;
    }

    function yearly_summary($county = NULL) {
        $months=[
            1=>lang('cal_jan'),2=>lang('cal_feb'),3=>lang('cal_mar'),4=>lang('cal_apr'),5=>lang('cal_may'),6=>lang('cal_jun'),
            7=>lang('cal_jul'),8=>lang('cal_aug'),9=>lang('cal_sep'),10=>lang('cal_oct'),11=>lang('cal_nov'),12=>lang('cal_dec')
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

        foreach ($result as $key => $value) {
            $data['categories'][$i] = $months[$value['month']].'-'.$value['year'];


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
        $x = 0;
        $m = 0;
        //foreach ($months as $m) {
        foreach ($result as $value) {
            $x++;
            $year = (int) $value['year'];
            $month = (int) $value['month'];
            $quarter = (int) ceil(($month / 3));
            //$quarter = ((int) ceil(($month / 3))+1)%4;
            $name = $year . ' Q' . $quarter;
            $m = $quarter - 1;
            $i = ($month % 3 == 0) ? 3 : $month % 3;
            while ($x < $i) {
                $data['suppression_trends'][$m]['name'] = $name;
                $data['suppression_trends'][$m]['data'][] = 0;
                $data['test_trends'][$m]['name'] = $name;
                $data['test_trends'][$m]['data'][] = 0;
                //$data['rejected_trends'][$m]['name'] = $name;
                //$data['rejected_trends'][$m]['data'][] = 0;
                $data['tat_trends'][$m]['name'] = $name;
                $data['tat_trends'][$m]['data'][] = 0;
                $x++;
            }
            $tests = (int) $value['suppressed'] + (int) $value['nonsuppressed'];
            $data['suppression_trends'][$m]['name'] = $name;
            $data['suppression_trends'][$m]['data'][] = @round((($value['suppressed'] * 100) / $tests), 4, PHP_ROUND_HALF_UP);
            $data['test_trends'][$m]['name'] = $name;
            $data['test_trends'][$m]['data'][] = $tests;
            //$data['rejected_trends'][$m]['name'] = $name;
            //$data['rejected_trends'][$m]['data'][] = @round((($value['rejected'] * 100) / $value['received']), 4, PHP_ROUND_HALF_UP);
            $data['tat_trends'][$m]['name'] = $name;
            $data['tat_trends'][$m]['data'][] = (int) $value['tat4'];
            if ($x == 3) {
                $x = 0;
            }
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

        $year;
        $i = 4;
        $b = true;
        $limit = 0;
        $quarter = 1;

        $data;

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

        $data['categories'] = array_fill(0, 8, "Null");
        $data['outcomes'][0]['data'] = array_fill(0, 8, 0);
        $data['outcomes'][1]['data'] = array_fill(0, 8, 0);
        $data['outcomes'][2]['data'] = array_fill(0, 8, 0);


        foreach ($result as $key => $value) {

            if ($b) {
                $b = false;
                $year = (int) $value['year'];
            }

            $y = (int) $value['year'];
            $name = $y . ' Q' . $quarter;
            if ($value['year'] != $year) {
                $year--;
            }

            $month = (int) $value['month'];
            $modulo = ($month % 3);

            $data['categories'][$i] = $name;

            $data['outcomes'][0]['data'][$i] += (int) $value['nonsuppressed'];
            $data['outcomes'][1]['data'][$i] += (int) $value['suppressed'];
            $val_sup_unsup = ((int) $value['suppressed'] + (int) $value['nonsuppressed']);
            $data['outcomes'][2]['data'][$i] += @round((((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);


            if ($modulo == 0) {
                $data['outcomes'][2]['data'][$i] /= 3;
                $i++;
                $quarter++;
                $limit++;

                if ($limit == 8) {
                    break;
                }
            }
            if ($quarter == 5) {
                $quarter = 1;
                $i = 0;
            }
        }

        return $data;
    }

}

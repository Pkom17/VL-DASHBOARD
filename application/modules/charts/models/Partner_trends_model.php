<?php

defined("BASEPATH") or exit("No direct script access allowed");

/**
 * 
 */
class Partner_trends_model extends MY_Model {

    function __construct() {
        parent:: __construct();
       
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
                $data['suppression_trends'][$i]['data'][$y] = null;
                $data['test_trends'][$i]['data'][$y] = null;
                //$data['rejected_trends'][$i]['data'][] = 0;
                $data['tat_trends'][$i]['data'][$y] = null;
                foreach ($result as $value) {
                    if ((int) $value['year'] == (int) $unique_year) {
                        $data['suppression_trends'][$i]['name'] = $unique_year;
                        $data['test_trends'][$i]['name'] = $unique_year;
                        //$data['rejected_trends'][$i]['name'] = $unique_year;
                        $data['tat_trends'][$i]['name'] = $unique_year;
                        if ((int) $m == (int) $value['month']) {
                            $tests = (int) $value['all_invalids'] + (int) $value['all_undetected'] + (int) $value['all_less1000'] + (int) $value['all_nonsuppressed'];
                            $data['suppression_trends'][$i]['data'][$y] = @round((($value['suppressed'] * 100) / $tests), 4, PHP_ROUND_HALF_UP);
                            $data['test_trends'][$i]['data'][$y] = $tests;
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

    function yearly_summary($partner = NULL) {
        $months = [
            1 => lang('cal_jan'), 2 => lang('cal_feb'), 3 => lang('cal_mar'), 4 => lang('cal_apr'), 5 => lang('cal_may'), 6 => lang('cal_jun'),
            7 => lang('cal_jul'), 8 => lang('cal_aug'), 9 => lang('cal_sep'), 10 => lang('cal_oct'), 11 => lang('cal_nov'), 12 => lang('cal_dec')
        ];
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

        $data['outcomes'][0]['name'] = lang('label.gt1000');
        $data['outcomes'][1]['name'] = lang('label.lt1000');
        $data['outcomes'][2]['name'] = lang('label.undetectable');
        $data['outcomes'][3]['name'] = lang('label.invalids');
        //$data['outcomes'][2]['name'] = lang('label.suppression');
        $data['outcomes2'][2]['name'] = '';

        $data['outcomes'][0]['type'] = "column";
        $data['outcomes'][1]['type'] = "column";
        $data['outcomes'][2]['type'] = "column";
        $data['outcomes'][3]['type'] = "column";
        //$data['outcomes'][2]['type'] = "spline";

        $data['outcomes'][0]['yAxis'] = 1;
        $data['outcomes'][1]['yAxis'] = 1;
        $data['outcomes'][2]['yAxis'] = 1;
        $data['outcomes'][3]['yAxis'] = 1;

        foreach ($result as $key => $value) {
            $data['categories'][$i] = $months[$value['month']] . '-' . $value['year'];
            
            $data['outcomes'][0]['data'][$i] = (int) $value['all_nonsuppressed'];
            $data['outcomes'][1]['data'][$i] = (int) $value['all_less1000'];
            $data['outcomes'][2]['data'][$i] = (int) $value['all_undetected'];
            $data['outcomes'][3]['data'][$i] = (int) $value['all_invalids'];
            //$data['outcomes'][2]['data'][$i] = @round(@(((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
            $i++;
        }
        $data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][3]['tooltip'] = array("valueSuffix" => ' ');
        //$data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

        $data['title'] = lang('label.outcomes');

        return $data;
    }

}

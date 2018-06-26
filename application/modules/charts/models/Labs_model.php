<?php

defined("BASEPATH") or exit("No direct script access allowed!");

/**
 * 
 */
class Labs_model extends MY_Model {

    function lab_performance_stat($year = NULL, $month = NULL, $to_year = null, $to_month = null) {
        // echo round(3.6451895227869, 2, PHP_ROUND_HALF_UP);die();
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

        $sql = "CALL `proc_get_vl_lab_performance_stats`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "');";

        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);echo "</pre>";die();
        $ul = '';
        foreach ($result as $key => $value) {
            $routine = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
            $routinesus = ((int) $value['less5000'] + (int) $value['above5000']);
            $ul .= "<tr>
						<td>" . ($key + 1) . "</td>
						<td>" . $value['name'] . "</td>
						<td>" . number_format((int) $value['sitesending']) . "</td>
						<td>" . number_format((int) $value['received']) . "</td>
						<td>" . number_format((int) $value['rejected']) . " (" .
                    round((($value['rejected'] * 100) / $value['received']), 1, PHP_ROUND_HALF_UP) . "%)</td>
						<td>" . number_format((int) $value['alltests']) . "</td>
						<td>" . number_format((int) $value['invalids']) . "</td>
						<td>" . number_format((int) $value['eqa']) . "</td>

						<td>" . number_format($routine) . "</td>
						<td>" . number_format($routinesus) . "</td>
						<td>" . number_format((int) $value['baseline']) . "</td>
						<td>" . number_format((int) $value['baselinesustxfail']) . "</td>
						<td>" . number_format((int) $value['confirmtx']) . "</td>
						<td>" . number_format((int) $value['confirm2vl']) . "</td>
						<td>" . number_format((int) $routine + (int) $value['baseline'] + (int) $value['confirm2vl']) . "</td>
						<td>" . number_format((int) $routinesus + (int) $value['baselinesustxfail'] + (int) $value['confirmtx']) . "</td>
						
					</tr>";
        }

        return $ul;
    }

    function download_lab_performance_stats($year = null, $month = null, $to_year = null, $to_month = null) {

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

        $sql = "CALL `proc_get_vl_lab_performance_stats`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "');";

        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $delimiter = ",";
        $newline = "\r\n";
        $filename = "labs.csv";
        $result = $this->db->query($sql)->result_array();

        $sheet;

        foreach ($result as $key => $value) {
            $sheet[$key]['name'] = $value['name'];
            $sheet[$key]['sites_sending'] = (int) $value['sitesending'];
            $sheet[$key]['received'] = (int) $value['received'];
            $sheet[$key]['rejected'] = (int) $value['rejected'];
            $sheet[$key]['rejection_rate'] = round((($value['rejected'] * 100) / $value['received']), 1, PHP_ROUND_HALF_UP) . "%";
            $sheet[$key]['invalid_tests'] = (int) $value['invalids'];
            $sheet[$key]['all_tests'] = (int) $value['alltests'];
            $sheet[$key]['valid_tests'] = ((int) $value['undetected'] + (int) $value['less1000'] + (int) $value['less5000'] + (int) $value['above5000']);
            $sheet[$key]['eqa'] = (int) $value['eqa'];
            $sheet[$key]['confirmatory_repeat_tests'] = (int) $value['confirmtx'];
            $sheet[$key]['total_tests'] = ((int) $value['alltests'] + (int) $value['eqa'] + (int) $value['confirmtx']);
            $sheet[$key]['>1000_copies'] = ( (int) $value['less5000'] + (int) $value['above5000']);
            $sheet[$key]['>1000_copies5'] = round(((($value['less5000'] + $value['above5000']) * 100) / ($value['undetected'] + $value['less1000'] + $value['less5000'] + $value['above5000'])), 1, PHP_ROUND_HALF_UP) . "%";
            $sheet[$key]['<1000_copies'] = ((int) $value['undetected'] + (int) $value['less1000']);
            $sheet[$key]['<1000_copies%'] = round(((($value['undetected'] + $value['less1000']) * 100) / ($value['undetected'] + $value['less1000'] + $value['less5000'] + $value['above5000'])), 1, PHP_ROUND_HALF_UP) . "%";
        }

        // echo "<pre>";print_r($sheet);die();
        //       $data = $this->dbutil->csv_from_result($sheet, $delimiter, $newline);
        //       force_download($filename, $data);

        /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
        $f = fopen('php://memory', 'w');
        /** loop through array  */
        $b = array(lang('label.testing_lab'), lang('label.facilities_served'),
            lang('label.total_samples_received'), lang('label.rejected_samples_orl'),
            lang('label.rejection_rate'), lang('label.redraws_after_testing'),
            lang('label.all_samples_run'), lang('label.valid_test_results'), lang('label.eqa_qa_iqc_tests'),
            lang('label.confirmatory_repeat_tests'), lang('label.total_tests_performed'),
            lang('label.gt1000_copies_ml'), lang('label.percent_gt1000_copies_ml'), lang('label.lt1000_copies_ml'), lang('label.percent_lt1000_copies_ml'));

        fputcsv($f, $b, $delimiter);

        foreach ($sheet as $line) {
            /** default php csv handler * */
            fputcsv($f, $line, $delimiter);
        }
        /** rewrind the "file" with the csv lines * */
        fseek($f, 0);
        /** modify header to be downloadable csv file * */
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="labs.csv";');
        /** Send file to browser for download */
        fpassthru($f);
    }

    function lab_testing_trends($year = NULL) {
        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }


        $data['year'] = $year;

        $sql = "CALL `proc_get_labs_testing_trends`('" . $year . "')";

        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        //echo "<pre>";print_r($result);die();
        if ($result) {
            $categories = array();
            foreach ($result as $key => $value) {
                if (!in_array($value['labname'], $categories)) {
                    $categories[] = $value['labname'];
                }
            }

            $months = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
            $count = 0;
            foreach ($categories as $key => $value) {
                foreach ($months as $key1 => $value1) {
                    foreach ($result as $key2 => $value2) {
                        if ((int) $value1 == (int) $value2['month'] && $value == $value2['labname']) {
                            $data['test_trends'][$key]['name'] = $value;
                            $data['test_trends'][$key]['data'][$count] = (int) $value2['alltests'] + (int) $value['eqa'] + (int) $value['confirmtx'];
                        }
                    }
                    $count++;
                }
                $count = 0;
            }
        } else {
            echo "<pre>";
            print_r(lang('label.no_testing_data_found'));
            echo "</pre>";
            die();
        }


        $this->db->close();

        $sql2 = "CALL `proc_get_avg_labs_testing_trends`('" . $year . "')";
        $result2 = $this->db->query($sql2)->result_array();

        $i = count($data['test_trends']);
        $count = 0;
        foreach ($result2 as $key => $value) {

            $data['test_trends'][$i]['name'] = lang('label.average_lab_testing_vol');
            $data['test_trends'][$i]['data'][$count] = (int) $value['alltests'];
            $count++;
        }


        //echo "<pre>";print_r($result2);die();
        return $data;
    }

    function lab_rejection_trends($year = NULL) {
        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }

        $data['year'] = $year;

        $sql = "CALL `proc_get_labs_testing_trends`('" . $year . "')";

        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        if ($result) {
            $categories = array();
            foreach ($result as $key => $value) {
                if (!in_array($value['labname'], $categories)) {
                    $categories[] = $value['labname'];
                }
            }

            $months = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
            $count = 0;
            foreach ($categories as $key => $value) {
                foreach ($months as $key1 => $value1) {
                    foreach ($result as $key2 => $value2) {
                        if ((int) $value1 == (int) $value2['month'] && $value == $value2['labname']) {
                            $data['reject_trend'][$key]['name'] = $value;
                            $data['reject_trend'][$key]['data'][$count] = round(@((int) $value2['rejected'] * 100 / (int) $value2['received']), 1);
                        }
                    }
                    $count++;
                }
                $count = 0;
            }
        } else {
            echo "<pre>";
            print_r(lang('label.no_rejection_data_found'));
            echo "</pre>";
            die();
        }

        $this->db->close();

        $sql2 = "CALL `proc_get_avg_labs_testing_trends`('" . $year . "')";
        $result2 = $this->db->query($sql2)->result_array();

        $i = count($data['reject_trend']);
        $count = 0;
        foreach ($result2 as $key => $value) {

            $data['reject_trend'][$i]['name'] = lang('label.national_rejection_rate');
            $data['reject_trend'][$i]['data'][$count] = round(@((int) $value['rejected'] * 100 / (int) $value['received']), 1);
            $count++;
        }

        return $data;
    }

    function sample_types($year = NULL, $month = NULL, $to_year = null, $to_month = null) {
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
                $month = $this->session->userdata('filter_month');
            } else {
                $month = 0;
            }
        }

        $sql = "CALL `proc_get_labs_sampletypes`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";

        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        $data['sample_types'][0]['name'] = lang('label.sample_type_EDTA');
        $data['sample_types'][1]['name'] = lang('label.sample_type_DBS');
        $data['sample_types'][2]['name'] = lang('label.sample_type_plasma');

        $count = 0;

        $data['categories'][0] = lang('label.no_data');
        $data["sample_types"][0]["data"][0] = $count;
        $data["sample_types"][1]["data"][0] = $count;
        $data["sample_types"][2]["data"][0] = $count;
        if ($result) {
            foreach ($result as $key => $value) {

                $data['categories'][$key] = $value['labname'];

                $data["sample_types"][0]["data"][$key] = (int) $value['edta'];
                $data["sample_types"][1]["data"][$key] = (int) $value['dbs'];
                $data["sample_types"][2]["data"][$key] = (int) $value['plasma'];
            }
        }

        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function labs_turnaround($year = NULL, $month = NULL, $to_year = null, $to_month = null) {
        $title = null;
        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }

        if ($month == null || $month == 'null') {
            if ($this->session->userdata('filter_month') == null || $this->session->userdata('filter_month') == 'null') {
                $month = $this->session->userdata('filter_month');
                $title = " (" . $year . ")";
            } else {
                $month = 0;
            }
        }

        if (!$title) {
            $title = " (" . $year . ", " . $this->resolve_month($month) . ")";
        }

        if ($to_month == null || $to_month == 'null') {
            $to_month = 0;
        }
        if ($to_year == null || $to_year == 'null') {
            $to_year = 0;
        } else {
            $title = " (" . $year . ", " . $this->resolve_month($month) . " - " . $to_year . ", " . $this->resolve_month($to_month) . ")";
        }


        $sql = "CALL `proc_get_labs_tat`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";
        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();
        $lab = NULL;
        $count = 1;
        $tat1 = 0;
        $tat2 = 0;
        $tat3 = 0;
        $tat4 = 0;
        $tat = array();

        if ($result) {
            foreach ($result as $key => $value) {

                $labname = strtolower(str_replace(" ", "_", $value['labname']));
                // $labname = $value['labname'];
                if ($lab) {
                    if ($lab == $value['labname']) {
                        $tat1 = $tat1 + $value['tat1'];
                        $tat2 = $tat2 + $value['tat2'];
                        $tat3 = $tat3 + $value['tat3'];
                        $tat4 = $tat4 + $value['tat4'];
                        $tat[$labname] = array(
                            'lab' => $labname,
                            'tat1' => $tat1,
                            'tat2' => $tat2,
                            'tat3' => $tat3,
                            'tat4' => $tat4,
                            'count' => $count
                        );
                        $count++;
                    } else {
                        $count = 1;
                        $tat1 = $value['tat1'];
                        $tat2 = $value['tat2'];
                        $tat3 = $value['tat3'];
                        $tat4 = $value['tat4'];
                        $lab = $value['labname'];
                        $tat[$labname] = array(
                            'lab' => $labname,
                            'tat1' => $tat1,
                            'tat2' => $tat2,
                            'tat3' => $tat3,
                            'tat4' => $tat4,
                            'count' => $count
                        );
                        $count++;
                    }
                } else {
                    $lab = $value['labname'];
                    $tat1 = $tat1 + $value['tat1'];
                    $tat2 = $tat2 + $value['tat2'];
                    $tat3 = $tat3 + $value['tat3'];
                    $tat4 = $tat4 + $value['tat4'];
                    $tat[$labname] = array(
                        'lab' => $labname,
                        'tat1' => $tat1,
                        'tat2' => $tat2,
                        'tat3' => $tat3,
                        'tat4' => $tat4,
                        'count' => $count
                    );

                    $count++;
                }
            }
            // echo "<pre>";print_r($tat);die();
            foreach ($tat as $key => $value) {
                $data[$key]['name'] = $value['lab'];
                $data[$key]['div_name'] = "container" . $key;
                $data[$key]['tat1'] = round($value['tat1'] / $value['count']);
                $data[$key]['tat2'] = round(($value['tat2'] / $value['count']) + $data[$key]['tat1']);
                $data[$key]['tat3'] = round(($value['tat3'] / $value['count']) + $data[$key]['tat2']);
                $data[$key]['tat4'] = round($value['tat4'] / $value['count']);
            }
        } else {
            echo "<pre>";
            print_r(lang('label.no_tat_data_found'));
            echo "</pre>";
            die();
        }

        // echo "<pre>";print_r($data);
        return $data;
    }

    function labs_outcomes($year = NULL, $month = NULL, $to_year = null, $to_month = null) {
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
                $month = $this->session->userdata('filter_month');
            } else {
                $month = 0;
            }
        }

        $sql = "CALL `proc_get_lab_outcomes`('" . $year . "','" . $month . "','" . $to_year . "','" . $to_month . "')";

        // echo "<pre>";print_r($sql);die();
        $result = $this->db->query($sql)->result_array();

        $data['lab_outcomes'][0]['name'] = lang('label.not_suppressed_');
        $data['lab_outcomes'][1]['name'] = lang('label.suppressed_');

        $count = 0;

        $data["lab_outcomes"][0]["data"][0] = $count;
        $data["lab_outcomes"][1]["data"][0] = $count;
        $data['categories'][0] = lang('label.no_data');

        foreach ($result as $key => $value) {
            $data['categories'][$key] = $value['labname'];
            $data["lab_outcomes"][0]["data"][$key] = (int) $value['sustxfl'];
            $data["lab_outcomes"][1]["data"][$key] = (int) $value['detectableNless1000'];
        }

        // echo "<pre>";print_r($data);die();
        return $data;
    }

    function yearly_trends($lab = NULL) {


        $sql = "CALL `proc_get_vl_yearly_lab_trends`(" . $lab . ");";

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

        foreach ($result as $key => $value) {

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
            $month--;

            $tests = (int) $value['suppressed'] + (int) $value['nonsuppressed'];

            $data['suppression_trends'][$i]['name'] = $value['year'];
            $data['suppression_trends'][$i]['data'][$month] = round(@(($value['suppressed'] * 100) / $tests), 1, PHP_ROUND_HALF_UP);


            $data['test_trends'][$i]['name'] = $value['year'];
            $data['test_trends'][$i]['data'][$month] = $tests;

            $data['rejected_trends'][$i]['name'] = $value['year'];
            $data['rejected_trends'][$i]['data'][$month] = round(@(($value['rejected'] * 100) / $value['received']), 1, PHP_ROUND_HALF_UP);

            $data['tat_trends'][$i]['name'] = $value['year'];
            $data['tat_trends'][$i]['data'][$month] = (int) $value['tat4'];
        }


        return $data;
    }

    function yearly_summary($lab = NULL, $year = NULL) {

        if ($lab == NULL || $lab == 'null') {
            $lab = 0;
        }

        if ($year == null || $year == 'null') {
            $year = $this->session->userdata('filter_year');
        }

        $from = $year - 1;

        $sql = "CALL `proc_get_vl_yearly_lab_summary`(" . $lab . ",'" . $from . "','" . $year . "');";

        // echo "<pre>";print_r($sql);die();

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

        foreach ($result as $key => $value) {
            $data['categories'][$key] = $this->resolve_month($value['month']) . '-' . $value['year'];

            $data['outcomes'][0]['data'][$key] = (int) $value['nonsuppressed'];
            $data['outcomes'][1]['data'][$key] = (int) $value['suppressed'];
            $data['outcomes'][2]['data'][$key] = round(@(((int) $value['suppressed'] * 100) / ((int) $value['suppressed'] + (int) $value['nonsuppressed'])), 1);
        }
        $data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][1]['tooltip'] = array("valueSuffix" => ' ');
        $data['outcomes'][2]['tooltip'] = array("valueSuffix" => ' %');

        $data['title'] = lang('label.outcomes');

        return $data;
    }

    function rejections($lab = NULL, $year = NULL, $month = NULL, $to_year = NULL, $to_month = NULL) {

        if ($lab == NULL || $lab == 'null') {
            $lab = 0;
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
                $month = $this->session->userdata('filter_month');
            } else {
                $month = 0;
            }
        }

        $sql = "CALL `proc_get_vl_lab_rejections`({$lab}, '{$year}', '{$month}', '{$to_year}', '{$to_month}' );";

        // echo "<pre>";print_r($sql);die();

        $result = $this->db->query($sql)->result_array();
        // echo "<pre>";print_r($result);die();

        $data['outcomes'][0]['name'] = lang('label.rejected_samples');

        $data['outcomes'][0]['type'] = "column";

        $data['outcomes'][0]['yAxis'] = 1;

        foreach ($result as $key => $value) {
            $data['categories'][$key] = $value['alias'];

            $data['outcomes'][0]['data'][$key] = (int) $value['total'];
        }
        $data['outcomes'][0]['tooltip'] = array("valueSuffix" => ' ');

        if ($lab == 0) {
            $data['title'] = lang('label.national_rejections');
        } else {
            $data['title'] = lang('label.lab_rejections');
        }


        return $data;
    }

}

?>
<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author IT
 */
class Sample_model extends CI_Model {

    function __construct() {
        parent:: __construct();
    }

    /**
     * 
     * @param array $samples
     * @return array to remove from chart data
     */
    public function saveSample(array $samples) {

        foreach ($samples as $v) {
            // insert into vl_sample_import
            $q = $this->db->get_where('vl_sample_import', array('labno' => $v['labno']));
            $row = $q->result_array();
            if (is_array($row) && count($row) > 0) {
                $v['computed'] = 'N';
                $this->db->where('labno', $v['labno']);
                $this->db->update('vl_sample_import', $v);
                $row_id = $row[0]['id'];
            } else {
                $this->db->insert('vl_sample_import', $v);
                $row_id = $this->db->insert_id();
            }
        }
        return;
    }

    public function monthlyPatientData($year = null, $month = null, $to_year = null, $to_month = null) {
        $sql = 'select * from vl_sample_import where 1 ';
        if ($month != null && $month != 0 && $month != '') {
            if ($to_month != null && $to_month != 0 && $to_month != '' && ($year = $to_year)) {
                $sql .= ' and year = ' . $year . ' and month between ' . $month . ' and ' . $to_month;
            } elseif ($to_month != null && $to_month != 0 && $to_month != '' && ($year != $to_year)) {
                $sql .= ' and ((year > ' . $year . ' and year < ' . $to_year . ') or (year = ' . $year . ' and month >=' . $month . ')  or  (year = ' . $to_year . ' and month <=' . $to_month . ' ))';
            } else {
                $sql .= ' and year = ' . $year . ' and  month =' . $month;
            }
        } else {
            $sql .= ' and year = ' . $year;
        }
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        $patientnos = [];
        $monthly_data = [];
        foreach ($rows as $v) {
            if (in_array($v['patientno'], $patientnos)) {
                
            } else {
                $patientnos[] = $v['patientno'];
                $monthly_data[$v['patientno']] = $v;
            }
        }

        return count($monthly_data);
    }

}

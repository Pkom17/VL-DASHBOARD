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

    public function findAgeDistinctRow() {
        $sql = 'select distinct year,month,facility as sitecode,agecat2 age from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function findAgeDistinctRow2() {
        $sql = 'select distinct year,month,agecat2 age from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function findGenderDistinctRow() {
        $sql = 'select distinct year,month,facility as sitecode,gender from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function findGenderDistinctRow2() {
        $sql = 'select distinct year,month,gender from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function findJustificationDistinctRow() {
        $sql = 'select distinct year,month,facility as sitecode,justification from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function findJustificationDistinctRow2() {
        $sql = 'select distinct year,month,justification from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function findRegimenDistinctRow() {
        $sql = 'select distinct year,month,facility as sitecode,regimen from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function findRegimenDistinctRow2() {
        $sql = 'select distinct year,month,regimen from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function findSampleTypeDistinctRow() {
        $sql = 'select distinct year,month,facility as sitecode,sampletype from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function findSampleTypeDistinctRow2() {
        $sql = 'select distinct year,month,sampletype from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function finSummaryDistinctRow() {
        $sql = 'select distinct year,month,facility as sitecode from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    public function findSummaryDistinctRow2() {
        $sql = 'select distinct year,month from  temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * 
     * @param array $samples
     * @return array to remove from chart data
     */
    public function saveSample(array $samples) {
        $toRemove = [];
        foreach ($samples as $v) {
            $q = $this->db->get_where('sample_import', array('labno' => $v['labno']));
            $row = $q->result_array();
            if (is_array($row) && count($row) > 0) {
                $toRemove[] = $row;
            } elseif (!$this->db->insert('sample_import', $v)) {
                echo $this->db->_error_message();
            }
        }
        return $toRemove;
    }

    public function saveTempSample(array $samples) {
        if (!$this->db->insert_batch('temp_sample_import', $samples)) {
            echo $this->db->_error_message();
        }
    }

}

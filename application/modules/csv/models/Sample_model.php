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

    public function findSiteAgeRow() {
        $sql = 'select distinct year,month,facility,agecat2 age from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findSubcountyAgeRow() {
        $sql = 'select distinct year,month,subcounty,agecat2 age from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findCountyAgeRow() {
        $sql = 'select distinct year,month,county,agecat2 age from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findPartnerAgeRow() {
        $sql = 'select distinct year,month,partner,agecat2 age from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findNAtionalAgeRow() {
        $sql = 'select distinct year,month,agecat2 age from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findGenderDistinctRow() {
        $sql = 'select distinct year,month,facility,gender from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findGenderDistinctRow2() {
        $sql = 'select distinct year,month,gender from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findJustificationDistinctRow() {
        $sql = 'select distinct year,month,facility,justification from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findJustificationDistinctRow2() {
        $sql = 'select distinct year,month,justification from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findRegimenDistinctRow() {
        $sql = 'select distinct year,month,facility,regimen from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findRegimenDistinctRow2() {
        $sql = 'select distinct year,month,regimen from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findSampleTypeDistinctRow() {
        $sql = 'select distinct year,month,facility,sampletype from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findSampleTypeDistinctRow2() {
        $sql = 'select distinct year,month,sampletype from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function finSummaryDistinctRow() {
        $sql = 'select distinct year,month,facility  from  vl_temp_sample_import';
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function findSummaryDistinctRow2() {
        $sql = 'select distinct year,month from  vl_temp_sample_import';
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
            $q = $this->db->get_where('vl_sample_import', array('labno' => $v['labno']));
            $row = $q->result_array();
            if (is_array($row) && count($row) > 0) {
                $this->db->where('labno', $v['labno']); 
                $v['computed']= 'N';
                $this->db->update('vl_sample_import', $v); 
            } elseif (!$this->db->insert('vl_sample_import', $v)) {
                echo $this->db->_error_message();
            }
        }
        return $toRemove;
    }

    public function saveTempSample(array $samples) {
        foreach ($samples as $v) {
            $q = $this->db->get_where('vl_temp_sample_import', array('labno' => $v['labno']));
            $row = $q->result_array();
            if (is_array($row) && count($row) > 0) {
               $this->db->replace('vl_temp_sample_import', $v); 
            } elseif (!$this->db->insert('vl_temp_sample_import', $v)) {
                echo $this->db->_error_message();
            }
        }
    }

    public function emptyTempSample() {
        if (!$this->db->empty_table('vl_temp_sample_import')) {
            echo $this->db->_error_message();
        }
    }

}

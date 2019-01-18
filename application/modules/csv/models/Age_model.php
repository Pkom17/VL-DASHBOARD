<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author IT
 */
class Age_model extends CI_Model {

    function __construct() {
        parent:: __construct();
    }

    public function getAge1Id($age) {
        $agecat1 = $this->Csv_import_model->get_ageCategory1();
        $keys = array_keys($agecat1, $age);
        if (count($keys) > 0) {
            return $keys[0];
        } else {
            return 0;
        }
    }

    public function getAge2Id($age) {
        $agecat2 = $this->Csv_import_model->get_ageCategory2();
        $keys = array_keys($agecat2, $age);
        if (count($keys) > 0) {
            return $keys[0];
        } else {
            return 0;
        }
    }

    public function getAgeCat1Id($agecat) {
        $sql = 'select ID from agecategory where name= ? and  subID = 1';
        $query = $this->db->query($sql, [$agecat]);
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function getAgeCat2Id($agecat) {
        $query = $this->db->query('select ID from agecategory where name= ? and  subID = 2', [$agecat]);
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

}

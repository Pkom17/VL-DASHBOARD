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
        $keys = array_keys($agecat1,$age);
        if (count($keys) > 0) {
            return $keys[0];
        } else {
            return 0;
        }
    }

    public function getAge2Id($age) {
        $agecat2 = $this->Csv_import_model->get_ageCategory2();
        $keys = array_keys($agecat2,$age);
        if (count($keys) > 0) {
            return $keys[0];
        } else {
            return 0;
        }
    }
    
    public function toNationalAge(){
        
    }
    
    


}

<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of CsvModel
 *
 * @author IT
 */
class Csv_import_model extends CI_Model {

    function __construct() {
        parent:: __construct();
    }

    function get_user_languages_dropdown() {
        $dropdown = '';
        if (null !== $this->session->userdata('language')) {
            $language = $this->session->get_userdata('language')['language'];
        } else {
//get browser language
            $tLang_l = preg_split('/[;,]/', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
            switch ($tLang_l[0]) {
                case 'fr':$language = "french";
                    break;
                case 'en':$language = "english";
                    break;
                default:
                    $language = "french";
            }
        }
        $lang = array('french' => "Français", 'english' => "English");
        foreach ($lang as $key => $val) {
            if ($key == $language) {
                $dropdown .= "<option value = '" . $key . "' selected>" . $val . "</option>";
                $this->lang->load($language, $language); //charger la langue dans le système
            } else {
                $dropdown .= "<option value = '" . $key . "'>" . $val . "</option>";
            }
        }
        return $dropdown;
    }

    function get_sites() {
        $dropdown = '';
        $query = $this->db->query('select ID,name from facilitys');
        $sites = $query->result_array();
        foreach ($sites as $val) {
            $dropdown .= "<option value = '" . $val['ID'] . "'>" . $val['name'] . "</option>";
        }
        return $dropdown;
    }

    function get_ageCategory1() {
        $ages = [];
        $query = $this->db->query('select ID,name from agecategory where subID = 1');
        $rows = $query->result_array();
        foreach ($rows as $v) {
            $ages[$v['ID']] = $v['name'];
        }
        return $ages;
    }

    function get_ageCategory2() {
        $ages = [];
        $query = $this->db->query('select ID,name from agecategory where subID = 2');
        $rows = $query->result_array();
        foreach ($rows as $v) {
            $ages[$v['ID']] = $v['name'];
        }
        return $ages;
    }

    public function saveVLSiteAge($array_data) {
        if (!$this->db->insert_batch('vl_site_age', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLNationalAge($array_data) {
        if (!$this->db->insert_batch('vl_national_age', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLCountyAge($array_data) {
        if (!$this->db->insert_batch('vl_county_age', $array_data)) {
            echo $this->db->_error_message();
        }
    }
    public function saveVLSubcountyAge($array_data) {
        if (!$this->db->insert_batch('vl_subcounty_age', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLPartnerAge($array_data) {
        if (!$this->db->insert_batch('vl_partner_age', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLSiteGender($array_data) {
        if (!$this->db->insert_batch('vl_site_gender', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLNationalGender($array_data) {
        if (!$this->db->insert_batch('vl_national_gender', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLCountyGender($array_data) {
        if (!$this->db->insert_batch('vl_county_gender', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLSubcountyGender($array_data) {
        if (!$this->db->insert_batch('vl_subcounty_gender', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLPartnerGender($array_data) {
        if (!$this->db->insert_batch('vl_partner_gender', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLSiteJustification($array_data) {
        if (!$this->db->insert_batch('vl_site_justification', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLNationalJustification($array_data) {
        if (!$this->db->insert_batch('vl_national_justification', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLCountyJustification($array_data) {
        if (!$this->db->insert_batch('vl_county_justification', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLSubcountyJustification($array_data) {
        if (!$this->db->insert_batch('vl_subcounty_justification', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLPartnerJustification($array_data) {
        if (!$this->db->insert_batch('vl_partner_justification', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLSiteRegimen($array_data) {
        if (!$this->db->insert_batch('vl_site_regimen', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLNationalRegimen($array_data) {
        if (!$this->db->insert_batch('vl_national_regimen', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLCountyRegimen($array_data) {
        if (!$this->db->insert_batch('vl_county_regimen', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLSubcountyRegimen($array_data) {
        if (!$this->db->insert_batch('vl_subcounty_regimen', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLPartnerRegimen($array_data) {
        if (!$this->db->insert_batch('vl_partner_regimen', $array_data)) {
            echo $this->db->_error_message();
        }
    }
    public function saveVLSiteSummary($array_data) {
        if (!$this->db->insert_batch('vl_site_summary', $array_data)) {
            echo $this->db->_error_message();
        }
    }
    public function saveVLNationalSummary($array_data) {
        if (!$this->db->insert_batch('vl_national_summary', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLCountySummary($array_data) {
        if (!$this->db->insert_batch('vl_county_summary', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLSubcountySummary($array_data) {
        if (!$this->db->insert_batch('vl_subcounty_summary', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function saveVLPartnerSummary($array_data) {
        if (!$this->db->insert_batch('vl_partner_summary', $array_data)) {
            echo $this->db->_error_message();
        }
    }
    public function saveVLLabSummary($array_data) {
        if (!$this->db->insert_batch('vl_lab_summary', $array_data)) {
            echo $this->db->_error_message();
        }
    }

    public function findSiteIdByDatimCode($datimcode) {
        $query = $this->db->query('select ID from facilitys where DATIMcode = ?', array($datimcode));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findDistrictIdByDatimCode($datimcode) {
        $sql = 'select d.ID from districts d join  facilitys f on f.district = d.ID  where f.DATIMcode = ? ';
        $query = $this->db->query($sql, array($datimcode));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findCountyIdByDatimCode($datimcode) {
        $sql = 'select c.id from countys c join  districts d on d.county = c.id join  facilitys f on f.district = d.id  where f.DATIMcode = ? ';
        $query = $this->db->query($sql, array($datimcode));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findPartnerIdByDatimCode($datimcode) {
        $sql = 'select p.ID from  partners p join  facilitys f on f.partner = p.ID  where f.DATIMcode = ? ';
        $query = $this->db->query($sql, array($datimcode));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findLabIdByDatimCode($datimcode) {
        $sql = 'select p.ID from  labs l join  facilitys f on f.lab = l.ID  where f.DATIMcode = ? ';
        $query = $this->db->query($sql, array($datimcode));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findJustificationIdByName($name) {
        $sql = 'select v.ID from  viraljustifications v where v.name = ?';
        $query = $this->db->query($sql, array($name));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findRegimenIdByName($name) {
        $sql = 'select v.ID from  viralprophylaxis v where v.name = ?';
        $query = $this->db->query($sql, array($name));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }
    
    public function findSampleTypeIdByname($name){
        $sql = 'select v.ID from  viralsampletypedetails v where v.name = ?';
        $query = $this->db->query($sql, array($name));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

}

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

    public function saveData($table, array $data, array $where) {
        foreach ($data as $v) {
            $set = array_diff(array_keys($v), $where, array('dateupdated'));
            $whereClause = [];
            foreach ($where as $w) {
                $whereClause[$w] = $v[$w];
            }
            $this->db->where($whereClause);
            $q = $this->db->get($table);
            $this->db->reset_query();
            if ($q->num_rows() > 0) {
                $setClause = [];
                foreach ($set as $s) {
                    $setClause[$s] = $s . ' + ' . $v[$s];
                    //$setClause[$s] =  $v[$s];
                }
                $setClause['dateupdated'] = '"' . date('d/m/Y H:i:s') . '"';
                $this->db->set($setClause, '', false);
                $this->db->where($whereClause);
                $this->db->update($table);
            } else {
                $v['dateupdated'] = date('d/m/Y H:i:s');
                $this->db->insert($table, $v);
            }
        }
    }

    public function saveSummaryData($table, array $data) {
        foreach ($data as $v) {
            $v['dateupdated'] = date('d/m/Y H:i:s');
            $this->db->insert($table, $v);
        }
    }

    public function removeData($table, array $data, array $where) {
        foreach ($data as $v) {
            $set = array_diff(array_keys($v), $where, array('dateupdated'));
            $whereClause = [];
            foreach ($where as $w) {
                $whereClause[$w] = $v[$w];
            }
            $setClause = [];
            foreach ($set as $s) {
                $setClause[$s] = $s . ' -' . $v[$s];
            }
            $this->db->update($table, $setClause, $whereClause);
        }
    }

    public function findDistrictIdByDatimCode($datimcode) {
        $sql = 'select d.ID ID from districts d join  facilitys f on f.district = d.ID  where f.DATIMcode = ? ';
        $query = $this->db->query($sql, array($datimcode));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
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

    public function findCountyIdByDatimCode($datimcode) {
        $sql = 'select c.id ID from countys c join  districts d on d.county = c.id join  facilitys f on f.district = d.id  where f.DATIMcode = ? ';
        $query = $this->db->query($sql, array($datimcode));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findPartnerIdByDatimCode($datimcode) {
        $sql = 'select p.ID ID from  partners p join  facilitys f on f.partner = p.ID  where f.DATIMcode = ? ';
        $query = $this->db->query($sql, array($datimcode));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findLabIdByDatimCode($datimcode) {
        $sql = 'select l.ID ID from  labs l join  facilitys f on f.lab = l.ID  where f.DATIMcode = ? ';
        $query = $this->db->query($sql, array($datimcode));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findLabIdByLabNo($labno) {
        $prefix = substr($labno, 0, 4);
        $sql = 'select l.ID ID from  labs l join  lab_prefix p on p.lab_id = l.ID  where p.vl_prefix = ? ';
        $query = $this->db->query($sql, array($prefix));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findJustificationIdByName($name) {
        $sql = 'select v.ID ID from  viraljustifications v where v.name = ?';
        $query = $this->db->query($sql, array($name));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findRegimenIdByName($name) {
        $sql = 'select v.ID ID from  viralprophylaxis v where v.name = ?';
        $query = $this->db->query($sql, array($name));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function findSampleTypeIdByname($name) {
        $sql = 'select v.ID ID from  viralsampletypedetails v where v.name = ?';
        $query = $this->db->query($sql, array($name));
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function setComputedData() {
        $this->db->update('vl_sample_import', array('computed' => 'O'));
        if ($this->db->affected_rows() > 0){
            $this->refreshChartData();
        }
    }

    public function refreshChartData() {
        $procedures = [
            'maj_vl_national_gender()', 'maj_vl_site_gender()', 'maj_vl_subcounty_gender()', 'maj_vl_county_gender()', 'maj_vl_partner_gender()', 'maj_vl_national_age()',
            'maj_vl_site_age()', 'maj_vl_subcounty_age()', 'maj_vl_county_age()', 'maj_vl_partner_age()', 'maj_vl_national_regimen()', 'maj_vl_site_regimen()',
            'maj_vl_subcounty_regimen()', 'maj_vl_county_regimen()', 'maj_vl_partner_regimen()', 'maj_vl_national_justification()', 'maj_vl_site_justification()',
            'maj_vl_subcounty_justification()', 'maj_vl_county_justification()', 'maj_vl_partner_justification()', 'maj_vl_national_sampletype()', 'maj_vl_site_sampletype()',
            'maj_vl_subcounty_sampletype()', 'maj_vl_county_sampletype()', 'maj_vl_partner_sampletype()', 'maj_vl_lab_sampletype()', 'maj_vl_national_summary()',
            'maj_vl_site_summary()', 'maj_vl_subcounty_summary()', 'maj_vl_county_summary()', 'maj_vl_partner_summary()', 'maj_vl_lab_summary()'];
        foreach ($procedures as $proc) {
            $this->db->query('CALL '.$proc);
        }
    }

}

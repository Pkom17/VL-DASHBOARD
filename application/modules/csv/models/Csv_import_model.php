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

    public function computeData() {
        $interval = $this->getIntervalDateToUpdate();
        $min_date = $interval['min_date'];
        $max_date = $interval['max_date'];
        $min_year = substr($min_date, 0, 4);
        $min_month = substr($min_date, 4, 2);
        $max_year = substr($max_date, 0, 4);
        $max_month = substr($max_date, 4, 2);
//        echo $min_month.'<br>';
//        echo $min_year.'<br>';
//        echo $max_month.'<br>';
//        echo $max_year.'<br>';        //die();
        $this->db->update('vl_sample_import', array('computed' => 'O'));
        $affected_rows = $this->db->affected_rows();
        if (($min_date != '' || $min_date != null) && ($max_date != '' || $max_date != null)) {
            $this->defineLastTestByPatient($min_date, $max_date);
            if ($affected_rows > 0) {
                $this->refreshChartData($min_date, $max_date, $min_month, $min_year, $max_month, $max_year);
            }
        }
    }

    private function getIntervalDateToUpdate() {
        $sql = 'select min(yearmonth) min_date, max(yearmonth) max_date from vl_sample_import  where computed = ?';
        $query = $this->db->query($sql, array('N'));
        $rows = $query->result_array();
        // print_r($rows); die();
        return ($rows[0]);
    }

    public function refreshChartData($min_date, $max_date, $min_month, $min_year, $max_month, $max_year) {
        $procedures = ['maj_vl_gender', 'maj_vl_age', 'maj_vl_regimen', 'maj_vl_justification', 'maj_vl_sampletype', 'maj_vl_summary'];
        foreach ($procedures as $proc) {
            //echo $proc.'('. $min_date . ',' . $max_date .','.$min_month.','.$min_year.','.$max_month.','.$max_year. ')<br>';      
            $this->db->query('CALL ' . $proc . '(' . $min_date . ',' . $max_date . ',' . $min_month . ',' . $min_year . ',' . $max_month . ',' . $max_year . ')');
        }
        $this->db->query('CALL maj_vl_site_suppression');
        // die();
    }

    public function defineLastTestByPatient($min_date, $max_date) {
        $min_year = substr($min_date, 0, 4);
        $max_year = substr($max_date, 0, 4);
        $sql0 = 'update vl_sample_import set lastTestInYear=0,lastTestInCop = 0 where year between ? and ?';
        $this->db->query($sql0, array($min_year, $max_year));
        $sql1 = 'select distinct year,cop from vl_sample_import where year between ? and ?';
        $query1 = $this->db->query($sql1, array($min_year, $max_year));
        $rows = $query1->result_array();
        $num = count($rows);
        $years = [];
        $unique_years = [];
        $cops = [];
        $unique_cops = [];
        $a = 0;
        foreach ($rows as $v) {
            $years[$a] = $v['year'];
            $cops[$a] = $v['cop'];
            $a++;
        }
        for ($j = 0; $j < $num; $j++) {
            if (in_array($years[$j], $unique_years)) {
                continue;
            }
            $unique_years[] = $years[$j];
        }
        for ($j = 0; $j < $num; $j++) {
            if (in_array($cops[$j], $unique_cops)) {
                continue;
            }
            $unique_cops[] = $cops[$j];
        }
        //lastInYear
        $years_id = [];
        $d_years = implode(',', $unique_years);
        $sql2 = 'select max(id) id from vl_sample_import where year in (' . $d_years . ') group by patientno';
        $query2 = $this->db->query($sql2);
        $rows2 = $query2->result_array();
        foreach ($rows2 as $r) {
            $years_id[] = $r['id'];
        }
        //lastInCop
        $cops_id = [];
        $d_cops = implode('\',\'', $unique_cops);
        $sql21 = 'select max(id) id from vl_sample_import where cop in (\'' . $d_cops . '\') group by patientno';
        $query21 = $this->db->query($sql21);
        $rows21 = $query21->result_array();
        foreach ($rows21 as $r) {
            $cops_id[] = $r['id'];
        }

        $ids1 = implode(',', $years_id);
        $sql5 = 'update vl_sample_import set lastTestInYear = 1 where id in (' . $ids1 . ')';
        $ids11 = implode(',', $cops_id);
        $sql51 = 'update vl_sample_import set lastTestInCop = 1 where id in (' . $ids11 . ')';
        $this->db->query($sql5);
        $this->db->query($sql51);
        // echo '</pre>';
    }

}

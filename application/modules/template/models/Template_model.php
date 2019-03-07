<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 
 */
class Template_model extends MY_Model {

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

    function get_counties_dropdown() {
        $dropdown = '';
        $this->db->order_by("name", "asc");
        $county_data = $this->db->get('countys')->result_array();

        foreach ($county_data as $key => $value) {
            $dropdown .= '<option value="' . $value['ID'] . '">' . $value['name'] . '</option>';
        }

        return $dropdown;
    }

    function get_sub_county_dropdown() {
        $dropdown = '';
        $this->db->from('districts');
        $this->db->order_by("name", "asc");
        $county_data = $this->db->get()->result_array();

        foreach ($county_data as $key => $value) {
            $dropdown .= '<option value="' . $value['ID'] . '">' . $value['name'] . '</option>';
        }

        return $dropdown;
    }

    function get_partners_dropdown() {
        $dropdown = '';
        $this->db->order_by("name", "asc");
        $partner_data = $this->db->get('partners')->result_array();
        // $partner_data = $this->db->query('SELECT `ID`, `name` FROM `partners` ORDER BY `name` ASC')->result_array();

        foreach ($partner_data as $key => $value) {
            $dropdown .= '<option value="' . $value['ID'] . '">' . $value['name'] . '</option>';
        }

        return $dropdown;
    }

    function get_lab_dropdown() {
        $dropdown = '';
        $this->db->order_by("name", "asc");
        $lab_data = $this->db->get('labs')->result_array();

        foreach ($lab_data as $key => $value) {
            $dropdown .= '<option value="' . $value['ID'] . '">' . $value['labname'] . '</option>';
        }

        return $dropdown;
    }

    function get_sample_dropdown() {
        $dropdown = '';
        $this->db->order_by("name", "asc");
        $lab_data = $this->db->get('viralsampletypedetails')->result_array();

        foreach ($lab_data as $key => $value) {
            $dropdown .= '<option value="' . $value['ID'] . '">' . $value['name'] . '</option>';
        }

        return $dropdown;
    }

    function get_site_dropdown() {
        $dropdown = '';
        $site_data = $this->db->query('SELECT DISTINCT `view_facilitys`.`ID`, `view_facilitys`.`name` FROM `vl_summary` JOIN `view_facilitys` ON `vl_summary`.`facility` = `view_facilitys`.`ID` order by `view_facilitys`.`name`')->result_array();

        foreach ($site_data as $key => $value) {
            $dropdown .= '<option value="' . $value['ID'] . '">' . $value['name'] . '</option>';
        }

        return $dropdown;
    }

    function get_regimen_dropdown() {
        $dropdown = '';
        $this->db->order_by("name", "asc");
        $county_data = $this->db->get('viralprophylaxis')->result_array();

        foreach ($county_data as $key => $value) {
            $dropdown .= '<option value="' . $value['ID'] . '">' . $value['name'] . '</option>';
        }

        return $dropdown;
    }

    function get_age_dropdown() {
        $dropdown = '';
        $regimen_data1 = $this->db->query("SELECT * FROM `agecategory` WHERE `subID` ='2' ")->result_array();
        $regimen_data2 = $this->db->query("SELECT * FROM `agecategory` WHERE `subID` ='3' ")->result_array();

        $dropdown .= '<optgroup label="'. lang("classification.first").'">';
        foreach ($regimen_data1 as $key => $value) {
            $dropdown .= '<option value="' . $value['ID'] . '">' . $value['name'] . '</option>';
        }
        $dropdown .= '</optgroup>';
        $dropdown .= '<optgroup label="'. lang("classification.second").'">';
        foreach ($regimen_data2 as $key => $value) {
            $dropdown .= '<option value="' . $value['ID'] . '">' . $value['name'] . '</option>';
        }
        $dropdown .= '</optgroup>';

        return $dropdown;
    }

    function pmtct_dropdown() {
        $dropdown = '';
        // $this->db->order_by("name","asc");
        $this->db->where('subID', 1);
        $lab_data = $this->db->get('viralpmtcttype')->result_array();

        foreach ($lab_data as $key => $value) {
            $dropdown .= '<option value="' . $value['ID'] . '">' . $value['name'] . '</option>';
        }

        return $dropdown;
    }

    function get_county_name($county_id) {
        $this->db->where('ID', $county_id);
        $data = $this->db->get('countys')->result_array();
        $name = $data[0]["name"];

        return $name;
    }

    function get_sub_county_name($sub_county_id) {
        $this->db->where('ID', $sub_county_id);
        $data = $this->db->get('districts')->result_array();
        $name = $data[0]["name"];

        return $name;
    }

    function get_partner_name($partner_id) {
        $this->db->where('ID', $partner_id);
        $data = $this->db->get('partners')->result_array();
        $name = $data[0]["name"];

        return $name;
    }

}

?>
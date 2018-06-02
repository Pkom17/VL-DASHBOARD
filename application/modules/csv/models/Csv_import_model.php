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
        $query = $this->db->query('select id,name from facilitys');
        $sites = $query->result_array();
        foreach ($sites as $val) {
            $dropdown .= "<option value = '" . $val['id'] . "'>" . $val['name'] . "</option>";
        }
        return $dropdown;
    }
    function get_ageCategory1() {
        $ages = [];
        $query = $this->db->query('select id,name from agecategory where subID = 1');
        $rows = $query->result_array();
        foreach ($rows as $v) {
            $ages[$v['id']]=$v['name'];
        }
        return $ages;
    }
    function get_ageCategory2() {
        $ages = [];
        $query = $this->db->query('select id,name from agecategory where subID = 2');
        $rows = $query->result_array();
        foreach ($rows as $v) {
            $ages[$v['id']]=$v['name'];
        }
        return $ages;
    }

}

<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

    function __construct() {
        parent:: __construct();
        $l = $this->get_language();
         $this->lang->load($l,$l); //charger la langue dans le système
    }

    function get_language() {
        $language = $this->session->userdata('language');
        if (is_null($language)) {
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
        return $language;
    }

    function resolve_month($month) {
        switch ($month) {
            case 1:
                $value = lang('cal_jan');
                break;
            case 2:
                $value = lang('cal_feb');
                break;
            case 3:
                $value = lang('cal_mar');
                break;
            case 4:
                $value = lang('cal_apr');
                break;
            case 5:
                $value = lang('cal_may');
                break;
            case 6:
                $value = lang('cal_jun');
                break;
            case 7:
                $value = lang('cal_jul');
                break;
            case 8:
                $value = lang('cal_aug');
                break;
            case 9:
                $value = lang('cal_sep');
                break;
            case 10:
                $value = lang('cal_oct');
                break;
            case 11:
                $value = lang('cal_nov');
                break;
            case 12:
                $value = lang('cal_dec');
                break;
            default:
                $value = NULL;
                break;
        }

        return $value;
    }

    function dow_to_local($dow) {//day of week to local
        switch ($dow){
            case 'Mon':$dow_tl =  'cal_mon';
                break;
            case 'Tue':$dow_tl =  'cal_tue';
                break;
            case 'Wed':$dow_tl =  'cal_wed';
                break;
            case 'Thu':$dow_tl =  'cal_thu';
                break;
            case 'Fri':$dow_tl =  'cal_fri';
                break;
            case 'Sat':$dow_tl =  'cal_sat';
                break;
            case 'Sun':$dow_tl =  'cal_sun';
                break;
        }
        return $dow_tl;
    }

    function req($url) {
        return null;
        /**  Update link 
          $this->load->library('requests/library/requests');
          $this->requests->register_autoloader();
          // $headers = array('X-Auth-Token' => 'jhWXc65gZUI=yG5ndWkpAGNsaW50b85oZWFsdGhhY2Nlc3Mub3Jn');
          $headers = array();
          $options = array('timeout' => 40);
          $my_url = "http://eidapi.nascop.org/vl/ver2.0/" . $url;
          $request = $this->requests->get($my_url, $headers, $options);
          // $request = $this->requests->get($my_url);
          // return json_decode(json_encode(json_decode($request->body)), true);
          return json_decode($request->body);
         * 
         */
    }

}

?>
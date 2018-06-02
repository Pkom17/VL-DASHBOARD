<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Import openelis exported data  into vl_dashboard db
 *
 * @author IT
 */
class Csv extends MY_Controller {

    function __construct() {
        parent:: __construct();
        $this->load->library('session');
        $this->load->library('csvimport');
        $this->load->library('csvdatadispatcher');
        $this->load->model('csv_import_model');
    }

    public function index() {
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
        $this->lang->load($language, $language); //charger la langue dans le système
        $this->load->model('Csv_import_model');
        $data['user_lang'] = $this->Csv_import_model->get_user_languages_dropdown();
        $data['sites'] = $this->Csv_import_model->get_sites();
        $this->load->view('csv_import', $data);
    }

    public function upload() {
        $data = [];
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'csv';
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('csv_file')) {
            //on récupère l'erreur dans une variable 
            $data['error'] = $this->upload->display_errors();
            echo json_encode($data);
            die();
        }
        $agecategories1 = $this->Csv_import_model->get_ageCategory1();
        $agecategories2 = $this->Csv_import_model->get_ageCategory2();
        $site = $this->input->post("site");
        $file_data = $this->upload->data();
        $csv_array = $this->csvimport->get_array($file_data['full_path']);
        if ($csv_array) {
            $this->csvdatadispatcher->load($csv_array);
            $this->csvdatadispatcher->setAgeCategories($agecategories1,$agecategories2);
            $data['data'] = $this->csvdatadispatcher->dispatch();
            $nbread = count($csv_array);
              foreach ($csv_array as $row) {
                  
              } 
            $data['success'] = '1';
            $data['nbread'] = $nbread;
            echo json_encode($data);
        } else {
            $data['success'] = '-1';
            echo json_encode($data);
        }
        
    }

}

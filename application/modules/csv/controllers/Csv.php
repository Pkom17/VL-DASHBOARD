<?php

defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 3000);
ini_set('upload_max_filesize', '8M');
ini_set('post_max_size', '8M');
ini_set('max_input_time', 3000);

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
        $this->load->library('regimen_extractor');
        $this->load->model('Csv_import_model');
        $this->load->model('Sample_model');
        $this->load->model('Age_model');
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
        $data['user_lang'] = $this->Csv_import_model->get_user_languages_dropdown();
        $data['sites'] = $this->Csv_import_model->get_sites();

        $this->load->view('csv_import', $data);
    }

    public function upload() {
        $t1 = time();
        $data = [];
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = 8000;
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('csv_file')) {
            //on récupère l'erreur dans une variable 
            $data['message'] = $this->upload->display_errors();
            $data['success'] = FALSE;
            $data['uploaded'] = TRUE;
            $this->session->set_flashdata($data);
            redirect("csv");
        }
        $file_data = $this->upload->data();
        $csv_array = $this->csvimport->get_array($file_data['full_path']);
        $file_name = $file_data['client_name'];
        $file_size = $file_data['file_size'];
        unlink($file_data['full_path']);
        $valid_array = \Utils::validData($csv_array);

        if (is_array($valid_array)) {
            $samples = $this->getDataFromImport($valid_array);
            $this->Sample_model->saveSample($samples);
            $nbread = count($valid_array);
            $data['message'] = lang("data.import.success");
            $data['success'] = TRUE;
            $data['nbread'] = $nbread;
            $data['filename'] = $file_name;
            $data['filesize'] = $file_size;
        } else {
            $data['message'] = lang("errors.occurred") . '<br/>' . lang("no.valid.data");
            $data['success'] = FALSE;
        }
        $data['time'] = time() - $t1;
        $data['uploaded'] = TRUE;
        $this->session->set_flashdata($data);
        redirect("csv");
    }

    public function refresh() {
        $this->Csv_import_model->computeData();
       redirect("csv");
    }

    // y mettre les id de labo, county, subcounty et partner
    private function getDataFromImport(array $data) {
        $n = count($data);
        $regimenExtractor = $this->regimen_extractor;
        for ($i = 0; $i < $n; $i++) {
            $data[$i]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$i]['labno'] = \Utils::extractLabNo($data[$i]);
            $data[$i]['patientno'] = \Utils::extractPatientNo($data[$i]);
            $data[$i]['year'] = \Utils::extractYear($data[$i]);
            $data[$i]['month'] = \Utils::extractMonth($data[$i]);
            $data[$i]['day'] = \Utils::extractDay($data[$i]);
            $data[$i]['quarter'] = \Utils::getQuarter($data[$i]['month']);
            $data[$i]['cop'] = \Utils::getCop($data[$i]['year'], $data[$i]['month']);
            $month = ($data[$i]['month'] >=10)?$data[$i]['month']:'0'.$data[$i]['month'];
            $day = ($data[$i]['day'] >=10)?$data[$i]['day']:'0'.$data[$i]['day'];
            $data[$i]['yearmonthday'] = $data[$i]['year'].''.$month.''.$day;
            $data[$i]['yearmonth'] = $data[$i]['year'].''.$month;
            $datim_code = \Utils::extractDatimCode($data[$i]);
            $data[$i]['datim_code'] = $datim_code;
            $data[$i]['facility'] = $this->Csv_import_model->findSiteIdByDatimCode($datim_code);
            $data[$i]['subcounty'] = $this->Csv_import_model->findDistrictIdByDatimCode($datim_code);
            $data[$i]['county'] = $this->Csv_import_model->findCountyIdByDatimCode($datim_code);
            $data[$i]['partner'] = $this->Csv_import_model->findPartnerIdByDatimCode($datim_code);
            $data[$i]['lab'] = $this->Csv_import_model->findLabIdByLabNo($data[$i]['labno']);
            $age = \Utils::extractAge($data[$i]);
            $data[$i]['age'] = $age;
            $data[$i]['agecat1'] = $this->Age_model->getAgeCat1Id(\Utils::getAgeCategorysub1($age));
            $data[$i]['agecat2'] = $this->Age_model->getAgeCat2Id(\Utils::getAgeCategorysub2($age));
            $data[$i]['gender'] =  \Utils::extractGender($data[$i]);
            $m1 = \Utils::extractVLCurrent1($data[$i]);
            $m2 = \Utils::extractVLCurrent2($data[$i]);
            $m3 = \Utils::extractVLCurrent3($data[$i]);
            $data[$i]['regimen_name'] = $regimenExtractor->getRegimen($m1, $m2, $m3);
            $data[$i]['sampletype_name'] = \Utils::extractTypeOfSample($data[$i]);
            $data[$i]['justification_name'] = \Utils::extractVLReason($data[$i]);
            $data[$i]['regimen'] = $this->Csv_import_model->findRegimenIdByName($data[$i]['regimen_name']);
            $data[$i]['sampletype'] = $this->Csv_import_model->findSampleTypeIdByname($data[$i]['sampletype_name']);
            $data[$i]['justification'] = $this->Csv_import_model->findJustificationIdByName($data[$i]['justification_name']);
            $tat1 = intval(\Utils::dateDiff(\Utils::extractInterviewDate($data[$i]), \Utils::extractReceivedDate($data[$i])));
            $tat2 = intval(\Utils::dateDiff(\Utils::extractCompletedDate($data[$i]), \Utils::extractInterviewDate($data[$i])));
            $tat3 = intval(\Utils::dateDiff(\Utils::extractReleasedDate($data[$i]), \Utils::extractCompletedDate($data[$i])));
            $tat4 = intval(\Utils::dateDiff(\Utils::extractReleasedDate($data[$i]), \Utils::extractReceivedDate($data[$i])));
            $data[$i]['viralload'] = \Utils::extractViralLoad($data[$i]);
            $data[$i]['tat1'] = $tat1;
            $data[$i]['tat2'] = $tat2;
            $data[$i]['tat3'] = $tat3;
            $data[$i]['tat4'] = $tat4;
            unset($data[$i]['LABNO']);
            unset($data[$i]['SUJETSIT']);
            unset($data[$i]['SUJETNO']);
            unset($data[$i]['DRCPT']);
            unset($data[$i]['DINTV']);
            unset($data[$i]['CODE_SITE_DATIM']);
            unset($data[$i]['SEXE']);
            unset($data[$i]['AGEANS']);
            unset($data[$i]['Viral Load']);
            unset($data[$i]['Type_of_sample']);
            unset($data[$i]['COMPLETED_DATE']);
            unset($data[$i]['RELEASED_DATE']);
            unset($data[$i]['CURRENT1']);
            unset($data[$i]['CURRENT2']);
            unset($data[$i]['CURRENT3']);
            unset($data[$i]['VL_REASON']);
        }
        return $data;
    }

    public function import_history() {
        redirect("csv");
    }

}

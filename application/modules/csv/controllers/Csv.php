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
        $this->load->model('Csv_import_model');
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
        $file_data = $this->upload->data();
        $csv_array = $this->csvimport->get_array($file_data['full_path']);
        $valid_array = \CsvUtils::validData($csv_array);

        if (is_array($valid_array)) {
            $this->csvdatadispatcher->load($valid_array);
            $this->csvdatadispatcher->setAgeCategories($agecategories1, $agecategories2);
           /* $t1 = microtime(true);
            $this->csvdatadispatcher->getData();
            echo '   Gl'.(microtime(true)-$t1);
            die();*/
            $nationalAges = $this->csvdatadispatcher->toNationalAge();
            $siteAges = $this->csvdatadispatcher->toSiteAge();
            $nationalGenders = $this->csvdatadispatcher->toNationalGender();
            $siteGenders = $this->csvdatadispatcher->toSiteGender();
            $nationalJustifications = $this->retrieveJustification($this->csvdatadispatcher->toNationalJustification());
            $siteJustifications = $this->retrieveJustification($this->csvdatadispatcher->toSiteJustification());
            $nationalRegimens = $this->retrieveRegimen($this->csvdatadispatcher->toNationalRegimen());
            $siteRegimens = $this->retrieveRegimen($this->csvdatadispatcher->toSiteRegimen());
            $nationalSummary = $this->csvdatadispatcher->toNationalSummary();
            $siteSummary = $this->csvdatadispatcher->toSiteSummary();
            $nationalSampleType = $this->retrieveSampleType($this->csvdatadispatcher->toNationalSampleType());
            $siteSampleType = $this->retrieveSampleType($this->csvdatadispatcher->toSiteSampleType());
            $labSampleType = $this->retrieveSampleType($this->csvdatadispatcher->toLabSampleType());
            //insertion
            $this->Csv_import_model->saveVLNationalAge($nationalAges);
            $this->Csv_import_model->saveVLSiteAge($this->retrieveSite($siteAges));
            $this->Csv_import_model->saveVLCountyAge($this->retrieveCounty($siteAges));
            $this->Csv_import_model->saveVLSubcountyAge($this->retrieveSubcounty($siteAges));
            $this->Csv_import_model->saveVLPartnerAge($this->retrievePartner($siteAges));
            $this->Csv_import_model->saveVLNationalJustification($nationalJustifications);
            $this->Csv_import_model->saveVLSiteJustification($this->retrieveSite($siteJustifications));
            $this->Csv_import_model->saveVLCountyJustification($this->retrieveCounty($siteJustifications));
            $this->Csv_import_model->saveVLSubcountyJustification($this->retrieveSubcounty($siteJustifications));
            $this->Csv_import_model->saveVLPartnerJustification($this->retrievePartner($siteJustifications));
            $this->Csv_import_model->saveVLNationalRegimen($nationalRegimens);
            $this->Csv_import_model->saveVLSiteRegimen($this->retrieveSite($siteRegimens));
            $this->Csv_import_model->saveVLCountyRegimen($this->retrieveCounty($siteRegimens));
            $this->Csv_import_model->saveVLSubcountyRegimen($this->retrieveSubcounty($siteRegimens));
            $this->Csv_import_model->saveVLPartnerRegimen($this->retrievePartner($siteRegimens));
            $this->Csv_import_model->saveVLNationalGender($nationalGenders);
            $this->Csv_import_model->saveVLSiteGender($this->retrieveSite($siteGenders));
            $this->Csv_import_model->saveVLCountyGender($this->retrieveCounty($siteGenders));
            $this->Csv_import_model->saveVLSubcountyGender($this->retrieveSubcounty($siteGenders));
            $this->Csv_import_model->saveVLPartnerGender($this->retrievePartner($siteGenders));
            $this->Csv_import_model->saveVLNationalSummary($nationalSummary);
            $this->Csv_import_model->saveVLSiteSummary($this->retrieveSite($siteSummary));
            $this->Csv_import_model->saveVLCountySummary($this->retrieveCounty($siteSummary));
            $this->Csv_import_model->saveVLSubcountySummary($this->retrieveSubcounty($siteSummary));
            $this->Csv_import_model->saveVLPartnerSummary($this->retrievePartner($siteSummary));
            $this->Csv_import_model->saveVLLabSummary($this->retrieveLab($siteSummary));
            $this->Csv_import_model->saveVLNationalSampleType($nationalSampleType);
            $this->Csv_import_model->saveVLSiteSampleType($this->retrieveSite($siteSampleType));
            $this->Csv_import_model->saveVLCountySampleType($this->retrieveCounty($siteSampleType));
            $this->Csv_import_model->saveVLSubcountySampleType($this->retrieveSubcounty($siteSampleType));
            $this->Csv_import_model->saveVLPartnerSampleType($this->retrievePartner($siteSampleType));
            $this->Csv_import_model->saveVLLabSampleType($this->retrieveLab($labSampleType));
            $nbread = count($csv_array);
            $ret['success'] = '1';
            $ret['nbread'] = $nbread;
            echo json_encode($ret);
        } else {
            $ret['success'] = '-1';
            echo json_encode($ret);
        }
    }

    /**
     * Replace regimen label by its id
     * @param array $data
     * @return array
     */
    private function retrieveRegimen(array $data) {
        $count = count($data);
        for ($k = 0; $k < $count; $k++) {
            $data[$k]['regimen'] = $this->Csv_import_model->findRegimenIdByName($data[$k]['regimen']);
        }
        return $data;
    }

    private function retrieveJustification(array $data) {
        $count = count($data);
        for ($k = 0; $k < $count; $k++) {
            $data[$k]['justification'] = $this->Csv_import_model->findJustificationIdByName($data[$k]['justification']);
        }
        return $data;
    }

    private function retrieveSampleType(array $data) {
        $count = count($data);
        for ($k = 0; $k < $count; $k++) {
            $data[$k]['sampletype'] = $this->Csv_import_model->findSampleTypeIdByName($data[$k]['sampletype']);
        }
        return $data;
    }

    private function retrieveSite(array $data) {
        $count = count($data);
        for ($k = 0; $k < $count; $k++) {
            $data[$k]['facility'] = $this->Csv_import_model->findSiteIdByDatimCode($data[$k]['sitecode']);
            unset($data[$k]['sitecode']);
        }
        return $data;
    }

    private function retrieveSubcounty(array $data) {
        $count = count($data);
        for ($k = 0; $k < $count; $k++) {
            $data[$k]['subcounty'] = $this->Csv_import_model->findDistrictIdByDatimCode($data[$k]['sitecode']);
            unset($data[$k]['sitecode']);
        }
        return $data;
    }

    private function retrieveCounty(array $data) {
        $count = count($data);
        for ($k = 0; $k < $count; $k++) {
            $data[$k]['county'] = $this->Csv_import_model->findCountyIdByDatimCode($data[$k]['sitecode']);
            unset($data[$k]['sitecode']);
        }
        return $data;
    }

    private function retrievePartner(array $data) {
        $count = count($data);
        for ($k = 0; $k < $count; $k++) {
            $data[$k]['partner'] = $this->Csv_import_model->findPartnerIdByDatimCode($data[$k]['sitecode']);
            unset($data[$k]['sitecode']);
        }
        return $data;
    }

    private function retrieveLab(array $data) {
        $count = count($data);
        for ($k = 0; $k < $count; $k++) {
            $data[$k]['lab'] = $this->Csv_import_model->findLabIdByDatimCode($data[$k]['sitecode']);
            unset($data[$k]['sitecode']);
        }
        return $data;
    }

}

<?php

defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 1500);

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
        $this->load->library('csv_datadispatcher');
        $this->load->library('age_dispatcher');
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
        $data = [];
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = 1500;
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
            $this->csv_datadispatcher->load($valid_array);
            $this->csv_datadispatcher->setAgeCategories($agecategories1, $agecategories2);
            $t1 = microtime(true);
            $samples = $this->getDataFromImport($valid_array);
            echo '   TTL1: ' . (microtime(true) - $t1);
            $toRemove = $this->Sample_model->saveSample($samples);
            $this->Sample_model->saveTempSample($samples);
            $this->dispatch($samples, $toRemove);
            //print_r($samples);

            $this->Sample_model->emptyTempSample();
            die();

            $this->csv_datadispatcher->getData();
            die();

            $nationalAges = $this->csv_datadispatcher->toNationalAge();
            $siteAges = $this->csv_datadispatcher->toSiteAge();
            $nationalGenders = $this->csv_datadispatcher->toNationalGender();
            $siteGenders = $this->csv_datadispatcher->toSiteGender();
            $nationalJustifications = $this->retrieveJustification($this->csv_datadispatcher->toNationalJustification());
            $siteJustifications = $this->retrieveJustification($this->csv_datadispatcher->toSiteJustification());
            $nationalRegimens = $this->retrieveRegimen($this->csv_datadispatcher->toNationalRegimen());
            $siteRegimens = $this->retrieveRegimen($this->csv_datadispatcher->toSiteRegimen());
            $nationalSummary = $this->csv_datadispatcher->toNationalSummary();
            $siteSummary = $this->csv_datadispatcher->toSiteSummary();
            $nationalSampleType = $this->retrieveSampleType($this->csv_datadispatcher->toNationalSampleType());
            $siteSampleType = $this->retrieveSampleType($this->csv_datadispatcher->toSiteSampleType());
            $labSampleType = $this->retrieveSampleType($this->csv_datadispatcher->toLabSampleType());
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

    public function dispatch($data, $toRemove) {
        $t1 = microtime(true);
        /*
        $nationalAge = $this->Sample_model->findNationalAgeRow();
        $siteAge = $this->Sample_model->findSiteAgeRow();
        $subcountyAge = $this->Sample_model->findSubcountyAgeRow();
        $countyAge = $this->Sample_model->findCountyAgeRow();
        $partnerAge = $this->Sample_model->findPartnerAgeRow();
        //print_r($ageDisp2);
        $this->age_dispatcher->load($data, $toRemove, $nationalAge, $siteAge,$subcountyAge,$countyAge,$partnerAge);
        $nationalAges = $this->age_dispatcher->toNationalAge();
        $siteAges = $this->age_dispatcher->toSiteAge();
        $countyAges = $this->age_dispatcher->toCountyAge();
        $subcountyAges = $this->age_dispatcher->toSubcountyAge();
        $partnerAges = $this->age_dispatcher->toPartnerAge();
        //$siteAges = $this->age_dispatcher->toSiteAge();
        //$countyAges = $this->age_dispatcher->toCountyAge(); 
         * 
         */
        $nationalAges = $this->Age_model->toNationalAge();
        $siteAges = $this->Age_model->toSiteAge();
        $countyAges = $this->Age_model->toCountyAge();
        $subcountyAges = $this->Age_model->toSubcountyAge();
        $partnerAges = $this->Age_model->toPartnerAge();
        echo '   TTL2: ' . (microtime(true) - $t1);
//        print_r($nationalAges);
//        print_r($siteAges);
//        print_r($countyAges);
//        print_r($subcountyAges);
        print_r($partnerAges);
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

    // y mettre les id de labo, county, subcounty et partner
    private function getDataFromImport(array $data) {
        $n = count($data);
        $regimenExtractor = $this->regimen_extractor;

        for ($i = 0; $i < $n; $i++) {
            $data[$i]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$i]['labno'] = $data[$i]['LABNO'];
            $data[$i]['year'] = \CsvUtils::extractYear($data[$i]);
            $data[$i]['month'] = \CsvUtils::extractMonth($data[$i]);
            $datim_code = \CsvUtils::extractDatimCode($data[$i]);
            $data[$i]['datim_code'] = $datim_code;
            $data[$i]['facility'] = $this->Csv_import_model->findSiteIdByDatimCode($datim_code);
            $data[$i]['subcounty'] = $this->Csv_import_model->findDistrictIdByDatimCode($datim_code);
            $data[$i]['county'] = $this->Csv_import_model->findCountyIdByDatimCode($datim_code);
            $data[$i]['partner'] = $this->Csv_import_model->findPartnerIdByDatimCode($datim_code);
            $data[$i]['partner'] = $this->Csv_import_model->findPartnerIdByDatimCode($datim_code);
            $data[$i]['lab'] = $this->Csv_import_model->findLabIdByDatimCode($datim_code);
            $age = \CsvUtils::extractAge($data[$i]);
            $data[$i]['age'] = $age;
            $data[$i]['agecat1'] = $this->Age_model->getAgeCat1Id(\CsvUtils::getAgeCategorysub1($age));
            $data[$i]['agecat2'] = $this->Age_model->getAgeCat2Id(\CsvUtils::getAgeCategorysub2($age));
            $data[$i]['gender'] = $data[$i]['SEXE'];
            $m1 = \CsvUtils::extractVLCurrent1($data[$i]);
            $m2 = \CsvUtils::extractVLCurrent2($data[$i]);
            $m3 = \CsvUtils::extractVLCurrent3($data[$i]);
            $data[$i]['regimen_name'] = $regimenExtractor->getRegimen($m1, $m2, $m3);
            $data[$i]['sampletype_name'] = \CsvUtils::extractTypeOfSample($data[$i]);
            $data[$i]['justification_name'] = \CsvUtils::extractVLReason($data[$i]);
            $data[$i]['regimen'] = $this->Csv_import_model->findRegimenIdByName($data[$i]['regimen_name']);
            $data[$i]['sampletype'] = $this->Csv_import_model->findSampleTypeIdByname($data[$i]['sampletype_name']);
            $data[$i]['justification'] = $this->Csv_import_model->findJustificationIdByName($data[$i]['justification_name']);
            $tat1 = intval(\CsvUtils::dateDiff(\CsvUtils::extractInterviewDate($data[$i]), \CsvUtils::extractReceivedDate($data[$i])));
            $tat2 = intval(\CsvUtils::dateDiff(\CsvUtils::extractCompletedDate($data[$i]), \CsvUtils::extractInterviewDate($data[$i])));
            $tat3 = intval(\CsvUtils::dateDiff(\CsvUtils::extractReleasedDate($data[$i]), \CsvUtils::extractCompletedDate($data[$i])));
            $tat4 = intval(\CsvUtils::dateDiff(\CsvUtils::extractReleasedDate($data[$i]), \CsvUtils::extractInterviewDate($data[$i])));
            $data[$i]['viralload'] = $data[$i]['Viral Load'];
            $data[$i]['tat1'] = $tat1;
            $data[$i]['tat2'] = $tat2;
            $data[$i]['tat3'] = $tat3;
            $data[$i]['tat4'] = $tat4;
            unset($data[$i]['LABNO']);
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

}
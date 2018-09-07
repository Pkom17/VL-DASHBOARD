<?php

defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 1500);

/**
 * Import openelis exported data  into vl_dashboard db
 *
 * @author IT
 */
class Csv extends MY_Controller {

    const ADD = 0;
    const REM = 1;

    function __construct() {
        parent:: __construct();
        $this->load->library('session');
        $this->load->library('csvimport');
        $this->load->library('regimen_extractor');
        $this->load->model('Csv_import_model');
        $this->load->model('Sample_model');
        $this->load->model('Age_model');
        $this->load->model('Gender_model');
        $this->load->model('Justification_model');
        $this->load->model('Regimen_model');
        $this->load->model('Sampletype_model');
        $this->load->model('Summary_model');
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
        $config['max_size'] = 1536;
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('csv_file')) {
            //on récupère l'erreur dans une variable 
            $data['error'] = $this->upload->display_errors();
            echo json_encode($data);
            die();
        }
        $file_data = $this->upload->data();
        $csv_array = $this->csvimport->get_array($file_data['full_path']);
        unlink($file_data['full_path']);
        $valid_array = \CsvUtils::validData($csv_array);

        if (is_array($valid_array)) {
            $samples = $this->getDataFromImport($valid_array);
            $this->Sample_model->saveSample($samples);
            //$this->Sample_model->emptyTempSample();
            $nbread = count($csv_array);
            $ret['success'] = '1';
            $ret['nbread'] = $nbread;
            echo json_encode($ret);
        } else {
            $ret['success'] = '-1';
            echo json_encode($ret);
        }
    }

    public function refresh() {
        $this->dispatchToAge();
        $this->dispatchToGender();
        $this->dispatchToJustification();
        $this->dispatchToRegimen();
        $this->dispatchToSampletype();
        $this->dispatchToSummary();
        $this->Csv_import_model->setComputedData();
        redirect("Csv");  
    }

    public function dispatchToAge() {
        $nationalAges = $this->Age_model->toNationalAge();
        $siteAges = $this->Age_model->toSiteAge();
        $countyAges = $this->Age_model->toCountyAge();
        $subcountyAges = $this->Age_model->toSubcountyAge();
        $partnerAges = $this->Age_model->toPartnerAge();
        $this->Csv_import_model->saveData('vl_national_age', $nationalAges, array('year', 'month', 'age'));
        $this->Csv_import_model->saveData('vl_site_age', $siteAges, array('year', 'month', 'age', 'facility'));
        $this->Csv_import_model->saveData('vl_county_age', $countyAges, array('year', 'month', 'age', 'county'));
        $this->Csv_import_model->saveData('vl_subcounty_age', $subcountyAges, array('year', 'month', 'age', 'subcounty'));
        $this->Csv_import_model->saveData('vl_partner_age', $partnerAges, array('year', 'month', 'age', 'partner'));
    }

    public function dispatchToGender() {
        $nationalGenders = $this->Gender_model->toNationalGender();
        $siteGenders = $this->Gender_model->toSiteGender();
        $countyGenders = $this->Gender_model->toCountyGender();
        $subcountyGenders = $this->Gender_model->toSubcountyGender();
        $partnerGenders = $this->Gender_model->toPartnerGender();
        $this->Csv_import_model->saveData('vl_national_gender', $nationalGenders, array('year', 'month', 'gender'));
        $this->Csv_import_model->saveData('vl_site_gender', $siteGenders, array('year', 'month', 'gender', 'facility'));
        $this->Csv_import_model->saveData('vl_county_gender', $countyGenders, array('year', 'month', 'gender', 'county'));
        $this->Csv_import_model->saveData('vl_subcounty_gender', $subcountyGenders, array('year', 'month', 'gender', 'subcounty'));
        $this->Csv_import_model->saveData('vl_partner_gender', $partnerGenders, array('year', 'month', 'gender', 'partner'));
    }

    public function dispatchToJustification() {
        $nationalJustifications = $this->Justification_model->toNationalJustification();
        $siteJustifications = $this->Justification_model->toSiteJustification();
        $countyJustifications = $this->Justification_model->toCountyJustification();
        $subcountyJustifications = $this->Justification_model->toSubcountyJustification();
        $partnerJustifications = $this->Justification_model->toPartnerJustification();
        $this->Csv_import_model->saveData('vl_national_justification', $nationalJustifications, array('year', 'month', 'justification'));
        $this->Csv_import_model->saveData('vl_site_justification', $siteJustifications, array('year', 'month', 'justification', 'facility'));
        $this->Csv_import_model->saveData('vl_county_justification', $countyJustifications, array('year', 'month', 'justification', 'county'));
        $this->Csv_import_model->saveData('vl_subcounty_justification', $subcountyJustifications, array('year', 'month', 'justification', 'subcounty'));
        $this->Csv_import_model->saveData('vl_partner_justification', $partnerJustifications, array('year', 'month', 'justification', 'partner'));
    }

    public function dispatchToRegimen() {
        $nationalRegimens = $this->Regimen_model->toNationalRegimen();
        $siteRegimens = $this->Regimen_model->toSiteRegimen();
        $countyRegimens = $this->Regimen_model->toCountyRegimen();
        $subcountyRegimens = $this->Regimen_model->toSubcountyRegimen();
        $partnerRegimens = $this->Regimen_model->toPartnerRegimen();
        $this->Csv_import_model->saveData('vl_national_regimen', $nationalRegimens, array('year', 'month', 'regimen'));
        $this->Csv_import_model->saveData('vl_site_regimen', $siteRegimens, array('year', 'month', 'regimen', 'facility'));
        $this->Csv_import_model->saveData('vl_county_regimen', $countyRegimens, array('year', 'month', 'regimen', 'county'));
        $this->Csv_import_model->saveData('vl_subcounty_regimen', $subcountyRegimens, array('year', 'month', 'regimen', 'subcounty'));
        $this->Csv_import_model->saveData('vl_partner_regimen', $partnerRegimens, array('year', 'month', 'regimen', 'partner'));
    }

    public function dispatchToSampletype() {
        $nationalSampletypes = $this->Sampletype_model->toNationalSampletype();
        $siteSampletypes = $this->Sampletype_model->toSiteSampletype();
        $countySampletypes = $this->Sampletype_model->toCountySampletype();
        $subcountySampletypes = $this->Sampletype_model->toSubcountySampletype();
        $partnerSampletypes = $this->Sampletype_model->toPartnerSampletype();
        $labSampletypes = $this->Sampletype_model->toLabSampletype();
        $this->Csv_import_model->saveData('vl_national_sampletype', $nationalSampletypes, array('year', 'month', 'sampletype'));
        $this->Csv_import_model->saveData('vl_site_sampletype', $siteSampletypes, array('year', 'month', 'sampletype', 'facility'));
        $this->Csv_import_model->saveData('vl_county_sampletype', $countySampletypes, array('year', 'month', 'sampletype', 'county'));
        $this->Csv_import_model->saveData('vl_subcounty_sampletype', $subcountySampletypes, array('year', 'month', 'sampletype', 'subcounty'));
        $this->Csv_import_model->saveData('vl_partner_sampletype', $partnerSampletypes, array('year', 'month', 'sampletype', 'partner'));
        $this->Csv_import_model->saveData('vl_lab_sampletype', $labSampletypes, array('year', 'month', 'sampletype', 'lab'));
    }

    public function dispatchToSummary() {

        $nationalSummaries = $this->Summary_model->toNationalSummary();
        $siteSummaries = $this->Summary_model->toSiteSummary();
        $countySummaries = $this->Summary_model->toCountySummary();
        $subcountySummaries = $this->Summary_model->toSubcountySummary();
        $partnerSummaries = $this->Summary_model->toPartnerSummary();
        $labSummaries = $this->Summary_model->toLabSummary();
        $this->Csv_import_model->saveSummaryData('vl_national_summary', $nationalSummaries, array('year', 'month'));
        $this->Csv_import_model->saveSummaryData('vl_site_summary', $siteSummaries, array('year', 'month', 'facility'));
        $this->Csv_import_model->saveSummaryData('vl_county_summary', $countySummaries, array('year', 'month', 'county'));
        $this->Csv_import_model->saveSummaryData('vl_subcounty_summary', $subcountySummaries, array('year', 'month', 'subcounty'));
        $this->Csv_import_model->saveSummaryData('vl_partner_summary', $partnerSummaries, array('year', 'month', 'partner'));
        $this->Csv_import_model->saveSummaryData('vl_lab_summary', $labSummaries, array('year', 'month', 'lab'));
    }

    // y mettre les id de labo, county, subcounty et partner
    private function getDataFromImport(array $data) {
        $n = count($data);
        $regimenExtractor = $this->regimen_extractor;

        for ($i = 0; $i < $n; $i++) {
            $data[$i]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$i]['labno'] = \CsvUtils::extractLabNo($data[$i]);
            $data[$i]['year'] = \CsvUtils::extractYear($data[$i]);
            $data[$i]['month'] = \CsvUtils::extractMonth($data[$i]);
            $datim_code = \CsvUtils::extractDatimCode($data[$i]);
            $data[$i]['datim_code'] = $datim_code;
            $data[$i]['facility'] = $this->Csv_import_model->findSiteIdByDatimCode($datim_code);
            $data[$i]['subcounty'] = $this->Csv_import_model->findDistrictIdByDatimCode($datim_code);
            $data[$i]['county'] = $this->Csv_import_model->findCountyIdByDatimCode($datim_code);
            $data[$i]['partner'] = $this->Csv_import_model->findPartnerIdByDatimCode($datim_code);
            //$data[$i]['lab'] = $this->Csv_import_model->findLabIdByDatimCode($datim_code);
            $data[$i]['lab'] = $this->Csv_import_model->findLabIdByLabNo($data[$i]['labno']);
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
            $tat4 = intval(\CsvUtils::dateDiff(\CsvUtils::extractReleasedDate($data[$i]), \CsvUtils::extractReceivedDate($data[$i])));
            $data[$i]['viralload'] = \CsvUtils::extractViralLoad($data[$i]);
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
    
    public function import_history()
    {
        
        redirect("Csv"); 
    }

}

<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of AgeDispatcher
 *
 * @author IT
 */
class Age_dispatcher {

    private $data;
    private $toRemove;
    private $nationalAge;
    private $siteAge;
    private $subcountyAge;
    private $countyAge;
    private $partnerAge;

    function __construct() {
        
    }

    public function load($data, $toRemove, $nationalAge, $siteAge, $subcountyAge, $countyAge, $partnerAge) {
        $this->data = $data;
        $this->toRemove = $toRemove;
        $this->nationalAge = $nationalAge;
        $this->siteAge = $siteAge;
        $this->subcountyAge = $subcountyAge;
        $this->countyAge = $countyAge;
        $this->partnerAge = $partnerAge;
    }

    public function toNationalAge() {
        $arrayAdd = $this->data;
        $periode = $this->nationalAge;
        $data = [];
        foreach ($periode as $key => $v) {
            $alltests = 0;
            $femaletest = 0;
            $maletest = 0;
            $nogendertest = 0;
            $malenonsuppressed = 0;
            $femalenonsuppressed = 0;
            $nogendernonsuppressed = 0;
            $invalids = 0;
            $undetected = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
            $less1000 = 0;
            $less5000 = 0;
            $above5000 = 0;
            foreach ($arrayAdd as $row) {
                $year = $row['year'];
                $month = $row['month'];
                $age = $row['agecat2'];
                $sample = $row['sampletype_name'];
                $sexe = $row['gender'];
                $vl = $row['viralload'];
                if (($v['year'] == $year) && ($v['month'] == $month ) && ($v['age'] == $age )) {
                    $alltests += 1;
                    $edta += \CsvUtils::addEdta($sample);
                    $dbs += \CsvUtils::addDbs($sample);
                    $plasma += \CsvUtils::addPlasma($sample);
                    $maletest += \CsvUtils::addMaleTest($sexe);
                    $femaletest += \CsvUtils::addFemaleTest($sexe);
                    $nogendertest += \CsvUtils::addNoGenderTest($sexe);
                    $malenonsuppressed += \CsvUtils::addMaleNonSuppressed($sexe, $vl);
                    $femalenonsuppressed += \CsvUtils::addFemaleNonSuppressed($sexe, $vl);
                    $nogendernonsuppressed += \CsvUtils::addNoGenderNonSuppressed($sexe, $vl);
                    $undetected += \CsvUtils::addUndetected($vl);
                    $less1000 += \CsvUtils::addLess1000($vl);
                    $less5000 += \CsvUtils::addLess5000($vl);
                    $above5000 += \CsvUtils::addAbove5000($vl);
                    $invalids += \CsvUtils::addInvalids($vl);
                }
            }
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['age'] = $v['age'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['sustxfail'] = $less5000 + $above5000;
            $data[$key]['confirmtx'] = 0;
            $data[$key]['confirm2vl'] = 0;
            $data[$key]['baseline'] = 0;
            $data[$key]['baselinesustxfail'] = 0;
            $data[$key]['repeattests'] = 0;
            $data[$key]['rejected'] = 0;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
            $data[$key]['edta'] = $edta;
            $data[$key]['maletest'] = $maletest;
            $data[$key]['femaletest'] = $femaletest;
            $data[$key]['nogendertest'] = $nogendertest;
            $data[$key]['malenonsuppressed'] = $malenonsuppressed;
            $data[$key]['femalenonsuppressed'] = $femalenonsuppressed;
            $data[$key]['nogendernonsuppressed'] = $nogendernonsuppressed;
            $data[$key]['undetected'] = $undetected;
            $data[$key]['less1000'] = $less1000;
            $data[$key]['less5000'] = $less5000;
            $data[$key]['above5000'] = $above5000;
            $data[$key]['invalids'] = $invalids;
        }

        return $data;
    }

    public function toSiteAge() {
        $arrayAdd = $this->data;
        $periode = $this->siteAge;
        $data = [];
        foreach ($periode as $key => $v) {
            $received = 0;
            $alltests = 0;
            $femaletest = 0;
            $maletest = 0;
            $nogendertest = 0;
            $malenonsuppressed = 0;
            $femalenonsuppressed = 0;
            $nogendernonsuppressed = 0;
            $undetected = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
            $less1000 = 0;
            $less5000 = 0;
            $above5000 = 0;
            $invalids = 0;

            foreach ($arrayAdd as $row) {
                $year = $row['year'];
                $month = $row['month'];
                $age = $row['agecat2'];
                $sample = $row['sampletype'];
                $sexe = $row['gender'];
                $vl = $row['viralload'];
                $sitecode = $row['facility'];
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['facility'] == $sitecode ) && ($v['age'] == $age))) {
                    continue;
                }
                $received += 1;
                $alltests += 1;
                $edta += \CsvUtils::addEdta($sample);
                $dbs += \CsvUtils::addDbs($sample);
                $plasma += \CsvUtils::addPlasma($sample);
                $maletest += \CsvUtils::addMaleTest($sexe);
                $femaletest += \CsvUtils::addFemaleTest($sexe);
                $nogendertest += \CsvUtils::addNoGenderTest($sexe);
                $malenonsuppressed += \CsvUtils::addMaleNonSuppressed($sexe, $vl);
                $femalenonsuppressed += \CsvUtils::addFemaleNonSuppressed($sexe, $vl);
                $nogendernonsuppressed += \CsvUtils::addNoGenderNonSuppressed($sexe, $vl);
                $undetected += \CsvUtils::addUndetected($vl);
                $less1000 += \CsvUtils::addLess1000($vl);
                $less5000 += \CsvUtils::addLess5000($vl);
                $above5000 += \CsvUtils::addAbove5000($vl);
                $invalids += \CsvUtils::addInvalids($vl);
            }
            $data[$key]['month'] = $v['month'];
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['year'] = $v['year'];
            $data[$key]['facility'] = $v['facility'];
            $data[$key]['age'] = $v['age'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['sustxfail'] = $less5000 + $above5000;
            $data[$key]['confirmtx'] = 0;
            $data[$key]['confirm2vl'] = 0;
            $data[$key]['baseline'] = 0;
            $data[$key]['baselinesustxfail'] = 0;
            $data[$key]['repeattests'] = 0;
            $data[$key]['rejected'] = 0;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
            $data[$key]['edta'] = $edta;
            $data[$key]['maletest'] = $maletest;
            $data[$key]['femaletest'] = $femaletest;
            $data[$key]['nogendertest'] = $nogendertest;
            $data[$key]['undetected'] = $undetected;
            $data[$key]['less1000'] = $less1000;
            $data[$key]['less5000'] = $less5000;
            $data[$key]['above5000'] = $above5000;
            $data[$key]['invalids'] = $invalids;
        }
        return $data;
    }

    public function toCountyAge() {
        $arrayAdd = $this->data;
        $periode = $this->countyAge;
        $data = [];
        foreach ($periode as $key => $v) {
            $received = 0;
            $alltests = 0;
            $femaletest = 0;
            $maletest = 0;
            $nogendertest = 0;
            $malenonsuppressed = 0;
            $femalenonsuppressed = 0;
            $nogendernonsuppressed = 0;
            $undetected = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
            $less1000 = 0;
            $less5000 = 0;
            $above5000 = 0;
            $invalids = 0;

            foreach ($arrayAdd as $row) {
                $year = $row['year'];
                $month = $row['month'];
                $age = $row['agecat2'];
                $sample = $row['sampletype'];
                $sexe = $row['gender'];
                $vl = $row['viralload'];
                $county = $row['county'];
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['county'] == $county ) && ($v['age'] == $age))) {
                    continue;
                }
                $received += 1;
                $alltests += 1;
                $edta += \CsvUtils::addEdta($sample);
                $dbs += \CsvUtils::addDbs($sample);
                $plasma += \CsvUtils::addPlasma($sample);
                $maletest += \CsvUtils::addMaleTest($sexe);
                $femaletest += \CsvUtils::addFemaleTest($sexe);
                $nogendertest += \CsvUtils::addNoGenderTest($sexe);
                $malenonsuppressed += \CsvUtils::addMaleNonSuppressed($sexe, $vl);
                $femalenonsuppressed += \CsvUtils::addFemaleNonSuppressed($sexe, $vl);
                $nogendernonsuppressed += \CsvUtils::addNoGenderNonSuppressed($sexe, $vl);
                $undetected += \CsvUtils::addUndetected($vl);
                $less1000 += \CsvUtils::addLess1000($vl);
                $less5000 += \CsvUtils::addLess5000($vl);
                $above5000 += \CsvUtils::addAbove5000($vl);
                $invalids += \CsvUtils::addInvalids($vl);
            }
            $data[$key]['month'] = $v['month'];
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['year'] = $v['year'];
            $data[$key]['county'] = $v['county'];
            $data[$key]['age'] = $v['age'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['sustxfail'] = $less5000 + $above5000;
            $data[$key]['confirmtx'] = 0;
            $data[$key]['confirm2vl'] = 0;
            $data[$key]['baseline'] = 0;
            $data[$key]['baselinesustxfail'] = 0;
            $data[$key]['repeattests'] = 0;
            $data[$key]['rejected'] = 0;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
            $data[$key]['edta'] = $edta;
            $data[$key]['maletest'] = $maletest;
            $data[$key]['femaletest'] = $femaletest;
            $data[$key]['nogendertest'] = $nogendertest;
            $data[$key]['undetected'] = $undetected;
            $data[$key]['less1000'] = $less1000;
            $data[$key]['less5000'] = $less5000;
            $data[$key]['above5000'] = $above5000;
            $data[$key]['invalids'] = $invalids;
        }
        return $data;
    }

    public function toSubcountyAge() {
        $arrayAdd = $this->data;
        $periode = $this->subcountyAge;
        $data = [];
        foreach ($periode as $key => $v) {
            $received = 0;
            $alltests = 0;
            $femaletest = 0;
            $maletest = 0;
            $nogendertest = 0;
            $malenonsuppressed = 0;
            $femalenonsuppressed = 0;
            $nogendernonsuppressed = 0;
            $undetected = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
            $less1000 = 0;
            $less5000 = 0;
            $above5000 = 0;
            $invalids = 0;

            foreach ($arrayAdd as $row) {
                $year = $row['year'];
                $month = $row['month'];
                $age = $row['agecat2'];
                $sample = $row['sampletype'];
                $sexe = $row['gender'];
                $vl = $row['viralload'];
                $subcounty = $row['subcounty'];
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['subcounty'] == $subcounty ) && ($v['age'] == $age))) {
                    continue;
                }
                $received += 1;
                $alltests += 1;
                $edta += \CsvUtils::addEdta($sample);
                $dbs += \CsvUtils::addDbs($sample);
                $plasma += \CsvUtils::addPlasma($sample);
                $maletest += \CsvUtils::addMaleTest($sexe);
                $femaletest += \CsvUtils::addFemaleTest($sexe);
                $nogendertest += \CsvUtils::addNoGenderTest($sexe);
                $malenonsuppressed += \CsvUtils::addMaleNonSuppressed($sexe, $vl);
                $femalenonsuppressed += \CsvUtils::addFemaleNonSuppressed($sexe, $vl);
                $nogendernonsuppressed += \CsvUtils::addNoGenderNonSuppressed($sexe, $vl);
                $undetected += \CsvUtils::addUndetected($vl);
                $less1000 += \CsvUtils::addLess1000($vl);
                $less5000 += \CsvUtils::addLess5000($vl);
                $above5000 += \CsvUtils::addAbove5000($vl);
                $invalids += \CsvUtils::addInvalids($vl);
            }
            $data[$key]['month'] = $v['month'];
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['year'] = $v['year'];
            $data[$key]['subcounty'] = $v['subcounty'];
            $data[$key]['age'] = $v['age'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['sustxfail'] = $less5000 + $above5000;
            $data[$key]['confirmtx'] = 0;
            $data[$key]['confirm2vl'] = 0;
            $data[$key]['baseline'] = 0;
            $data[$key]['baselinesustxfail'] = 0;
            $data[$key]['repeattests'] = 0;
            $data[$key]['rejected'] = 0;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
            $data[$key]['edta'] = $edta;
            $data[$key]['maletest'] = $maletest;
            $data[$key]['femaletest'] = $femaletest;
            $data[$key]['nogendertest'] = $nogendertest;
            $data[$key]['undetected'] = $undetected;
            $data[$key]['less1000'] = $less1000;
            $data[$key]['less5000'] = $less5000;
            $data[$key]['above5000'] = $above5000;
            $data[$key]['invalids'] = $invalids;
        }
        return $data;
    }

    public function toPartnerAge() {
        $arrayAdd = $this->data;
        $periode = $this->partnerAge;
        $data = [];
        foreach ($periode as $key => $v) {
            $received = 0;
            $alltests = 0;
            $femaletest = 0;
            $maletest = 0;
            $nogendertest = 0;
            $malenonsuppressed = 0;
            $femalenonsuppressed = 0;
            $nogendernonsuppressed = 0;
            $undetected = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
            $less1000 = 0;
            $less5000 = 0;
            $above5000 = 0;
            $invalids = 0;

            foreach ($arrayAdd as $row) {
                $year = $row['year'];
                $month = $row['month'];
                $age = $row['agecat2'];
                $sample = $row['sampletype'];
                $sexe = $row['gender'];
                $vl = $row['viralload'];
                $partner = $row['partner'];
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['partner'] == $partner ) && ($v['age'] == $age))) {
                    continue;
                }
                $received += 1;
                $alltests += 1;
                $edta += \CsvUtils::addEdta($sample);
                $dbs += \CsvUtils::addDbs($sample);
                $plasma += \CsvUtils::addPlasma($sample);
                $maletest += \CsvUtils::addMaleTest($sexe);
                $femaletest += \CsvUtils::addFemaleTest($sexe);
                $nogendertest += \CsvUtils::addNoGenderTest($sexe);
                $malenonsuppressed += \CsvUtils::addMaleNonSuppressed($sexe, $vl);
                $femalenonsuppressed += \CsvUtils::addFemaleNonSuppressed($sexe, $vl);
                $nogendernonsuppressed += \CsvUtils::addNoGenderNonSuppressed($sexe, $vl);
                $undetected += \CsvUtils::addUndetected($vl);
                $less1000 += \CsvUtils::addLess1000($vl);
                $less5000 += \CsvUtils::addLess5000($vl);
                $above5000 += \CsvUtils::addAbove5000($vl);
                $invalids += \CsvUtils::addInvalids($vl);
            }
            $data[$key]['month'] = $v['month'];
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['year'] = $v['year'];
            $data[$key]['partner'] = $v['partner'];
            $data[$key]['age'] = $v['age'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['sustxfail'] = $less5000 + $above5000;
            $data[$key]['confirmtx'] = 0;
            $data[$key]['confirm2vl'] = 0;
            $data[$key]['baseline'] = 0;
            $data[$key]['baselinesustxfail'] = 0;
            $data[$key]['repeattests'] = 0;
            $data[$key]['rejected'] = 0;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
            $data[$key]['edta'] = $edta;
            $data[$key]['maletest'] = $maletest;
            $data[$key]['femaletest'] = $femaletest;
            $data[$key]['nogendertest'] = $nogendertest;
            $data[$key]['undetected'] = $undetected;
            $data[$key]['less1000'] = $less1000;
            $data[$key]['less5000'] = $less5000;
            $data[$key]['above5000'] = $above5000;
            $data[$key]['invalids'] = $invalids;
        }
        return $data;
    }

}

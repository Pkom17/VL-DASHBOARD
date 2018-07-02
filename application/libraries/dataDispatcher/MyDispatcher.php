<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dataDispatcher;

/**
 * Description of AgeDispatcher
 *
 * @author IT
 */
class MyDispatcher {

    const SITE_AGE = 1;
    const COUNTY_AGE = 2;
    const PARTNER_AGE = 3;
    const NATIONAL_AGE = 4;
    const SUBCOUNTY_AGE = 5;

    private $data;
    private $ageCat1;
    private $ageCat2;

    function __construct($data, $ageCat1, $ageCat2) {
        $this->data = $data;
        $this->ageCat1 = $ageCat1;
        $this->ageCat2 = $ageCat2;
    }

    public function getCategorysub1($age) {
        $cat = '';
        if ($age < 5) {
            $cat = '<5';
        } elseif ($age >= 5 && $age < 10) {
            $cat = '5-9';
        } elseif ($age >= 10 && $age < 15) {
            $cat = '10-14';
        } elseif ($age >= 15 && $age < 18) {
            $cat = '15-17';
        } elseif ($age >= 18) {
            $cat = '>18';
        }
        return $cat;
    }

    public function getCategorysub2($age) {
        $cat = '';
        if ($age < 2) {
            $cat = '<2';
        } elseif ($age >= 2 && $age < 10) {
            $cat = '2-9';
        } elseif ($age >= 10 && $age < 15) {
            $cat = '10-14';
        } elseif ($age >= 15 && $age < 20) {
            $cat = '15-19';
        } elseif ($age >= 20 && $age < 25) {
            $cat = '20-24';
        } elseif ($age >= 25) {
            $cat = '>25';
        }
        return $cat;
    }

    public function getAge1Id($age) {
        $keys = array_keys($this->ageCat1, $this->getCategorysub1($age));
        if (count($keys) > 0) {
            return $keys[0];
        } else {
            return 0;
        }
    }

    public function getAge2Id($age) {
        $keys = array_keys($this->ageCat2, $this->getCategorysub2($age));
        if (count($keys) > 0) {
            return $keys[0];
        } else {
            return 0;
        }
    }

    public function dispatch() {
        $periode = [];
        $array = $this->data;
        $x = 0;
        foreach ($array as $row) {
            $year = \CsvUtils::extractYear($row);
            $month = \CsvUtils::extractMonth($row);
            $sitecode = \CsvUtils::extractDatimCode($row);
            $age = \CsvUtils::extractAge($row);
            $agecat = $this->getAge2Id($age);
            $vlreason = \CsvUtils::extractVLReason($row);
            $sexe = \CsvUtils::extractSexe($row);
            $c1 = \CsvUtils::extractVLCurrent1($row);
            $c2 = \CsvUtils::extractVLCurrent2($row);
            $c3 = \CsvUtils::extractVLCurrent3($row);
            $regimenExtractor = new RegimenExtractor();
            $sampletype = \CsvUtils::extractTypeOfSample($row);
            $regimen = $regimenExtractor->getRegimen($c1, $c2, $c3);
            if (!in_array(['year' => $year, 'month' => $month, 'sitecode' => $sitecode, 'justification' => $vlreason, 'gender' => $sexe, 'age' => $agecat, 'regimen' => $regimen, 'sampletype' => $sampletype], $periode)) {
                $periode[$x]['year'] = $year;
                $periode[$x]['month'] = $month;
                $periode[$x]['sitecode'] = $sitecode;
                $periode[$x]['gender'] = $sexe;
                $periode[$x]['justification'] = $vlreason;
                $periode[$x]['age'] = $agecat;
                $periode[$x]['regimen'] = $regimen;
                $periode[$x]['sampletype'] = $sampletype;
                $x++;
            }
        }
        $data = $this->dispatchData($periode);
        return $data;
    }

    public function dispatchData($periode) {
        $t1 = microtime();
        $array = $this->data;
        $data = [];
        foreach ($periode as $key => $v) {
            $alltests = 0;
            $received = 0;
            $adults = 0;
            $paeds = 0;
            $noage = 0;
            $less2 = 0;
            $less9 = 0;
            $less14 = 0;
            $less19 = 0;
            $less24 = 0;
            $over25 = 0;
            $less5 = 0;
            $less10 = 0;
            $less15 = 0;
            $less18 = 0;
            $over18 = 0;
            $tat1 = 0;
            $tat2 = 0;
            $tat3 = 0;
            $tat4 = 0;
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
            $tat_count = 0; // for average

            foreach ($array as $row) {
                $year = \CsvUtils::extractYear($row);
                $month = \CsvUtils::extractMonth($row);
                $sitecode = \CsvUtils::extractDatimCode($row);
                $age = \CsvUtils::extractAge($row);
                $agecat = $this->getAge2Id($age);
                $vlreason = \CsvUtils::extractVLReason($row);
                $sexe = \CsvUtils::extractSexe($row);
                $c1 = \CsvUtils::extractVLCurrent1($row);
                $c2 = \CsvUtils::extractVLCurrent2($row);
                $c3 = \CsvUtils::extractVLCurrent3($row);
                $received_date = \CsvUtils::extractReceivedDate($row);
                $interv_date = \CsvUtils::extractInterviewDate($row);
                $completed_date = \CsvUtils::extractCompletedDate($row);
                $released_date = \CsvUtils::extractReleasedDate($row);
                $regimenExtractor = new RegimenExtractor();
                $sampletype = \CsvUtils::extractTypeOfSample($row);
                $regimen = $regimenExtractor->getRegimen($c1, $c2, $c3);
                $vl = \CsvUtils::extractViralLoad($row);
                if (($v['year'] == $year) && ($v['month'] == $month ) && ($v['sitecode'] == $sitecode ) && ($v['age'] == $agecat ) && ($v['gender'] == $sexe ) && ($v['regimen'] == $regimen ) && ($v['sampletype'] == $sampletype) && ($v['justification'] == $vlreason)) {
                    $tat1 += intval(\CsvUtils::dateDiff($interv_date, $received_date));
                    $tat2 += intval(\CsvUtils::dateDiff($completed_date, $interv_date));
                    $tat3 += intval(\CsvUtils::dateDiff($released_date, $completed_date));
                    $tat4 += intval(\CsvUtils::dateDiff($released_date, $received_date));
                    $tat_count += 1;
                    $alltests += 1;
                    $received += 1;
                    $edta += \CsvUtils::addEdta($sampletype);
                    $dbs += \CsvUtils::addDbs($sampletype);
                    $plasma += \CsvUtils::addPlasma($sampletype);
                    $maletest = \CsvUtils::addMaleTest($sexe);
                    $femaletest = \CsvUtils::addFemaleTest($sexe);
                    $nogendertest = \CsvUtils::addNoGenderTest($sexe);
                    $malenonsuppressed = \CsvUtils::addMaleNonSuppressed($sexe, $vl);
                    $femalenonsuppressed = \CsvUtils::addFemaleNonSuppressed($sexe, $vl);
                    $nogendernonsuppressed = \CsvUtils::addNoGenderNonSuppressed($sexe, $vl);
                    $adults = \CsvUtils::addAdults($age);
                    $paeds = \CsvUtils::addPaeds($age);
                    $noage = \CsvUtils::addNoage($age);
                    $undetected = \CsvUtils::addUndetected($vl);
                    $invalids = \CsvUtils::addInvalids($vl);
                    $less1000 = \CsvUtils::addLess1000($vl);
                    $less5000 = \CsvUtils::addLess5000($vl);
                    $above5000 = \CsvUtils::addAbove5000($vl);
                    $less2 = \CsvUtils::addLess2($age);
                    $less9 = \CsvUtils::addLess9($age);
                    $less14 = \CsvUtils::addLess14($age);
                    $less19 = \CsvUtils::addLess19($age);
                    $less24 = \CsvUtils::addLess24($age);
                    $over25 = \CsvUtils::addOver25($age);
                    $less5 = \CsvUtils::addLess5($age);
                    $less10 = \CsvUtils::addLess10($age);
                    $less15 = \CsvUtils::addLess15($age);
                    $less18 = \CsvUtils::addLess18($age);
                    $over18 = \CsvUtils::addOver18($age);
                }
            }
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['age'] = $v['age'];
            $data[$key]['gender'] = $v['gender'];
            $data[$key]['sitecode'] = $v['sitecode'];
            $data[$key]['regimen'] = $v['regimen'];
            $data[$key]['sampletype'] = $v['sampletype'];
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
            $data[$key]['alledta'] = $edta;
            $data[$key]['alldbs'] = $dbs;
            $data[$key]['allplasma'] = $plasma;
            $data[$key]['maletest'] = $maletest;
            $data[$key]['femaletest'] = $femaletest;
            $data[$key]['nogendertest'] = $nogendertest;
            $data[$key]['adults'] = $adults;
            $data[$key]['paeds'] = $paeds;
            $data[$key]['noage'] = $noage;
            $data[$key]['malenonsuppressed'] = $malenonsuppressed;
            $data[$key]['femalenonsuppressed'] = $femalenonsuppressed;
            $data[$key]['nogendernonsuppressed'] = $nogendernonsuppressed;
            $data[$key]['undetected'] = $undetected;
            $data[$key]['less1000'] = $less1000;
            $data[$key]['less5000'] = $less5000;
            $data[$key]['above5000'] = $above5000;
            $data[$key]['invalids'] = $invalids;
            $data[$key]['less5'] = $less5;
            $data[$key]['less10'] = $less10;
            $data[$key]['less15'] = $less15;
            $data[$key]['less18'] = $less18;
            $data[$key]['over18'] = $over18;
            $data[$key]['tat1'] = round(($tat_count != 0) ? ($tat1 / $tat_count) : 0, 0);
            $data[$key]['tat2'] = round(($tat_count != 0) ? ($tat2 / $tat_count) : 0, 0);
            $data[$key]['tat3'] = round(($tat_count != 0) ? ($tat3 / $tat_count) : 0, 0);
            $data[$key]['tat4'] = round(($tat_count != 0) ? ($tat4 / $tat_count) : 0, 0);
            $data[$key]['less2'] = $less2;
            $data[$key]['less9'] = $less9;
            $data[$key]['less14'] = $less14;
            $data[$key]['less19'] = $less19;
            $data[$key]['less24'] = $less24;
            $data[$key]['over25'] = $over25;
        }
        $t2 = microtime();
        echo '--- TT:' . ($t2 - $t1);
        return $data;
    }

}

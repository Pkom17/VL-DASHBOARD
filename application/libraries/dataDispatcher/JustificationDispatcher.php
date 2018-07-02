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
class JustificationDispatcher {

    const AGE_ADULT = 21;
    const AGE_PAEDS = 15;
    const NATIONAL_JUSTIFICATION = 1;
    const SITE_JUSTIFICATION = 2;
    const COUNTY_JUSTIFICATION = 3;
    const SUBCOUNTY_JUSTIFICATION = 4;
    const PARTNER_JUSTIFICATION = 5;

    private $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function dispatchByJustification($type) {
        $periode1 = [];
        $periode2 = [];
        $array = $this->data;
        $x = 0;
        $y = 0;
        foreach ($array as $row) {
            $year = \CsvUtils::extractYear($row);
            $month = \CsvUtils::extractMonth($row);
            $vlreason = \CsvUtils::extractVLReason($row);
            $sitecode = \CsvUtils::extractDatimCode($row);

            if (!in_array(['year' => $year, 'month' => $month, 'sitecode' => $sitecode, 'justification' => $vlreason], $periode1)) {
                $periode1[$x]['year'] = $year;
                $periode1[$x]['month'] = $month;
                $periode1[$x]['sitecode'] = $sitecode;
                $periode1[$x]['justification'] = $vlreason;
                $x++;
            }
            if (!in_array(['year' => $year, 'month' => $month, 'justification' => $vlreason], $periode2)) {
                $periode2[$y]['year'] = $year;
                $periode2[$y]['month'] = $month;
                $periode2[$y]['justification'] = $vlreason;
                $y++;
            }
        }
        $data = [];
        switch ($type) {
            case self::NATIONAL_JUSTIFICATION:
                $data = $this->dispatchDataToNationalJustification($periode2);
                break;
            case self::SITE_JUSTIFICATION:
                $data = $this->dispatchDataToSiteJustification($periode1);
                break;
            case self::COUNTY_JUSTIFICATION:
                $data = $this->dispatchDataToCountyJustification($periode1);
                break;
            case self::SUBCOUNTY_JUSTIFICATION:
                $data = $this->dispatchDataToSubcountyJustification($periode1);
                break;
            case self::PARTNER_JUSTIFICATION:
                $data = $this->dispatchDataToPartnerJustification($periode1);
                break;
        }
        return $data;
    }

    public function dispatchDataToNationalJustification($periode) {
        $array = $this->data;
        $data = [];
        foreach ($periode as $key => $v) {
            $alltests = 0;
            $adults = 0;
            $paeds = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
            $maletest = 0;
            $femaletest = 0;
            $nogendertest = 0;
            $noage = 0;
            $undetected = 0;
            $less1000 = 0;
            $less5000 = 0;
            $above5000 = 0;
            $invalids = 0;
            $less5 = 0;
            $less10 = 0;
            $less15 = 0;
            $less18 = 0;
            $over18 = 0;
            $less2 = 0;
            $less9 = 0;
            $less14 = 0;
            $less19 = 0;
            $less24 = 0;
            $over25 = 0;
            foreach ($array as $row) {
                $year = \CsvUtils::extractYear($row);
                $month = \CsvUtils::extractMonth($row);
                $age = \CsvUtils::extractAge($row);
                $sample = \CsvUtils::extractTypeOfSample($row);
                $vl = \CsvUtils::extractViralLoad($row);
                $sexe = \CsvUtils::extractSexe($row);
                $vlreason = \CsvUtils::extractVLReason($row);
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['justification'] == $vlreason))) {
                    continue;
                }
                $alltests += 1;
                $edta += \CsvUtils::addEdta($sample);
                $dbs += \CsvUtils::addDbs($sample);
                $plasma += \CsvUtils::addPlasma($sample);
                $adults = \CsvUtils::addAdults($age);
                $paeds = \CsvUtils::addPaeds($age);
                $noage = \CsvUtils::addNoage($age);
                $maletest = \CsvUtils::addMaleTest($sexe);
                $femaletest = \CsvUtils::addFemaleTest($sexe);
                $nogendertest = \CsvUtils::addNoGenderTest($sexe);
                $undetected = \CsvUtils::addUndetected($vl);
                $less1000 = \CsvUtils::addLess1000($vl);
                $less5000 = \CsvUtils::addLess5000($vl);
                $above5000 = \CsvUtils::addAbove5000($vl);
                $invalids = \CsvUtils::addInvalids($vl);
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
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['justification'] = $v['justification'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['sustxfail'] = $less5000 + $above5000;
            $data[$key]['edta'] = $edta;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
            $data[$key]['adults'] = $adults;
            $data[$key]['paeds'] = $paeds;
            $data[$key]['noage'] = $noage;
            $data[$key]['maletest'] = $maletest;
            $data[$key]['femaletest'] = $femaletest;
            $data[$key]['nogendertest'] = $nogendertest;
            $data[$key]['undetected'] = $undetected;
            $data[$key]['less1000'] = $less1000;
            $data[$key]['less5000'] = $less5000;
            $data[$key]['above5000'] = $above5000;
            $data[$key]['invalids'] = $invalids;
            $data[$key]['less10'] = $less10;
            $data[$key]['less15'] = $less15;
            $data[$key]['less18'] = $less18;
            $data[$key]['over18'] = $over18;
            $data[$key]['less2'] = $less2;
            $data[$key]['less9'] = $less9;
            $data[$key]['less14'] = $less14;
            $data[$key]['less19'] = $less19;
            $data[$key]['less24'] = $less24;
            $data[$key]['over25'] = $over25;
        }
        return $data;
    }

    public function dispatchDataToSiteJustification($periode) {
        $array = $this->data;
        $data = [];
        foreach ($periode as $key => $v) {
            $alltests = 0;
            $adults = 0;
            $paeds = 0;
            $noage = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
            $maletest = 0;
            $femaletest = 0;
            $nogendertest = 0;
            $undetected = 0;
            $less1000 = 0;
            $less5000 = 0;
            $above5000 = 0;
            $invalids = 0;
            $less5 = 0;
            $less10 = 0;
            $less15 = 0;
            $less18 = 0;
            $over18 = 0;
            $less2 = 0;
            $less9 = 0;
            $less14 = 0;
            $less19 = 0;
            $less24 = 0;
            $over25 = 0;
            foreach ($array as $row) {
                $year = \CsvUtils::extractYear($row);
                $month = \CsvUtils::extractMonth($row);
                $vlreason = \CsvUtils::extractVLReason($row);
                $sitecode = \CsvUtils::extractDatimCode($row);
                $sample = \CsvUtils::extractTypeOfSample($row);
                $age = \CsvUtils::extractAge($row);
                $vl = \CsvUtils::extractViralLoad($row);
                $sexe = \CsvUtils::extractSexe($row);
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['sitecode'] == $sitecode ) && ($v['justification'] == $vlreason ))) {
                    continue;
                }
                $alltests += 1;
                $edta += \CsvUtils::addEdta($sample);
                $dbs += \CsvUtils::addDbs($sample);
                $plasma += \CsvUtils::addPlasma($sample);
                $adults = \CsvUtils::addAdults($age);
                $paeds = \CsvUtils::addPaeds($age);
                $noage = \CsvUtils::addNoage($age);
                $maletest = \CsvUtils::addMaleTest($sexe);
                $femaletest = \CsvUtils::addFemaleTest($sexe);
                $nogendertest = \CsvUtils::addNoGenderTest($sexe);
                $undetected = \CsvUtils::addUndetected($vl);
                $less1000 = \CsvUtils::addLess1000($vl);
                $less5000 = \CsvUtils::addLess5000($vl);
                $above5000 = \CsvUtils::addAbove5000($vl);
                $invalids = \CsvUtils::addInvalids($vl);
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
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['sitecode'] = $v['sitecode'];
            $data[$key]['justification'] = $v['justification'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['sustxfail'] = $less5000 + $above5000;
            $data[$key]['edta'] = $edta;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
            $data[$key]['maletest'] = $maletest;
            $data[$key]['femaletest'] = $femaletest;
            $data[$key]['nogendertest'] = $nogendertest;
            $data[$key]['adults'] = $adults;
            $data[$key]['paeds'] = $paeds;
            $data[$key]['noage'] = $noage;
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
            $data[$key]['less2'] = $less2;
            $data[$key]['less9'] = $less9;
            $data[$key]['less14'] = $less14;
            $data[$key]['less19'] = $less19;
            $data[$key]['less24'] = $less24;
            $data[$key]['over25'] = $over25;
        }
        return $data;
    }

    public function dispatchDataToCountyJustification($periode) {
        return $this->dispatchDataToSiteJustification($periode);
    }

    public function dispatchDataToPartnerJustification($periode) {
        return $this->dispatchDataToSiteJustification($periode);
    }

    public function dispatchDataToSubcountyJustification($periode) {
        return $this->dispatchDataToSiteJustification($periode);
    }

}

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
class GenderDispatcher {

    const AGE_ADULT = 21;
    const AGE_PAEDS = 15;
    const NATIONAL_GENDER = 1;
    const SITE_GENDER = 2;
    const COUNTY_GENDER = 3;
    const SUBCOUNTY_GENDER = 4;
    const PARTNER_GENDER = 5;

    private $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function dispatchByGender($type) {
        $periode1 = [];
        $periode2 = [];
        $array = $this->data;
        $x = 0;
        $y = 0;
        foreach ($array as $row) {
            $year = \CsvUtils::extractYear($row);
            $month = \CsvUtils::extractMonth($row);
            $sitecode = \CsvUtils::extractDatimCode($row);
            $sexe = \CsvUtils::extractSexe($row);

            if (!in_array(['year' => $year, 'month' => $month, 'sitecode' => $sitecode, 'gender' => $sexe], $periode1)) {
                $periode1[$x]['year'] = $year;
                $periode1[$x]['month'] = $month;
                $periode1[$x]['sitecode'] = $sitecode;
                $periode1[$x]['gender'] = $sexe;
                $x++;
            }
            if (!in_array(['year' => $year, 'month' => $month, 'gender' => $sexe], $periode2)) {
                $periode2[$y]['year'] = $year;
                $periode2[$y]['month'] = $month;
                $periode2[$y]['gender'] = $sexe;
                $y++;
            }
        }

        $data = [];
        switch ($type) {
            case self::NATIONAL_GENDER:
                $data = $this->dispatchDataToNationalGender($periode2);
                break;
            case self::SITE_GENDER:
                $data = $this->dispatchDataToSiteGender($periode1);
                break;
            case self::COUNTY_GENDER:
                $data = $this->dispatchDataToCountyGender($periode1);
                break;
            case self::SUBCOUNTY_GENDER:
                $data = $this->dispatchDataToSubcountyGender($periode1);
                break;
            case self::PARTNER_GENDER:
                $data = $this->dispatchDataToPartnerGender($periode1);
                break;
        }
        return $data;
    }

    public function dispatchDataToNationalGender($periode) {
        $array = $this->data;
        $data = [];
        foreach ($periode as $key => $v) {
            $received = 0;
            $alltests = 0;
            $adults = 0;
            $paeds = 0;
            $noage = 0;
            $undetected = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
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
                $sexe = \CsvUtils::extractSexe($row);
                $sample = \CsvUtils::extractTypeOfSample($row);
                $age = \CsvUtils::extractAge($row);
                $vl = \CsvUtils::extractViralLoad($row);
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['gender'] == $sexe ))) {
                    continue;
                }
                $received += 1;
                $alltests += 1;
                $edta += \CsvUtils::addEdta($sample);
                $dbs += \CsvUtils::addDbs($sample);
                $plasma += \CsvUtils::addPlasma($sample);
                $adults = \CsvUtils::addAdults($age);
                $paeds = \CsvUtils::addPaeds($age);
                $noage = \CsvUtils::addNoage($age);
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
            $data[$key]['month'] = $v['month'];
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['year'] = $v['year'];
            $data[$key]['gender'] = $v['gender'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['sustxfail'] = $less5000 + $above5000;
            $data[$key]['edta'] = $edta;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
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

    public function dispatchDataToSiteGender($periode) {
        $array = $this->data;
        $data = [];
        foreach ($periode as $key => $v) {
            $received = 0;
            $alltests = 0;
            $adults = 0;
            $paeds = 0;
            $noage = 0;
            $undetected = 0;
            $invalids = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
            $less1000 = 0;
            $less5000 = 0;
            $above5000 = 0;
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
                $sitecode = \CsvUtils::extractDatimCode($row);
                $sexe = \CsvUtils::extractSexe($row);
                $sample = \CsvUtils::extractTypeOfSample($row);
                $age = \CsvUtils::extractAge($row);
                $vl = \CsvUtils::extractViralLoad($row);
                if ((($v['year'] == $year) && ($v['month'] == $month ) && ($v['sitecode'] == $sitecode ) && ($v['gender'] == $sexe ))) {
                    $received += 1;
                    $alltests += 1;
                    $edta += \CsvUtils::addEdta($sample);
                    $dbs += \CsvUtils::addDbs($sample);
                    $plasma += \CsvUtils::addPlasma($sample);
                    $adults = \CsvUtils::addAdults($age);
                    $paeds = \CsvUtils::addPaeds($age);
                    $noage = \CsvUtils::addNoage($age);
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
            }
            $data[$key]['dateupdated'] = date('d/m/Y');
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['sitecode'] = $v['sitecode'];
            $data[$key]['gender'] = $v['gender'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['sustxfail'] = $less5000 + $above5000;
            $data[$key]['edta'] = $edta;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
            $data[$key]['adults'] = $adults;
            $data[$key]['paeds'] = $paeds;
            $data[$key]['noage'] = $noage;
            $data[$key]['undetected'] = $undetected;
            $data[$key]['less1000'] = $less1000;
            $data[$key]['less5000'] = $less5000;
            $data[$key]['above5000'] = $above5000;
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

    public function dispatchDataToCountyGender($periode) {
        return $this->dispatchDataToSiteGender($periode);
    }

    public function dispatchDataToPartnerGender($periode) {
        return $this->dispatchDataToSiteGender($periode);
    }

    public function dispatchDataToSubcountyGender($periode) {
        return $this->dispatchDataToSiteGender($periode);
    }

}

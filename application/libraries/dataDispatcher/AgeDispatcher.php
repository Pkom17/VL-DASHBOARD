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
class AgeDispatcher {

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

    public function dispatchByAge($typeAge) {
        $periode1 = [];
        $periode2 = [];
        $array = $this->data;
        $x = 0;
        $y = 0;
        foreach ($array as $row) {
            $year = \CsvUtils::extractYear($row);
            $month = \CsvUtils::extractMonth($row);
            $sitecode = \CsvUtils::extractDatimCode($row);
            $age = \CsvUtils::extractAge($row);
            $agecat = $this->getAge2Id($age);
            if (!in_array(['year' => $year, 'month' => $month, 'sitecode' => $sitecode, 'age' => $agecat], $periode1)) {
                $periode1[$x]['year'] = $year;
                $periode1[$x]['month'] = $month;
                $periode1[$x]['sitecode'] = $sitecode;
                $periode1[$x]['age'] = $agecat;
                $x++;
            }
            if (!in_array(['year' => $year, 'month' => $month, 'age' => $agecat], $periode2)) {
                $periode2[$y]['year'] = $year;
                $periode2[$y]['month'] = $month;
                $periode2[$y]['age'] = $agecat;
                $y++;
            }
        }

        $data = [];
        switch ($typeAge) {
            case self::SITE_AGE:
                $data = $this->dispatchDataToSiteAge($periode1);
                break;
            case self::COUNTY_AGE:
                $data = $this->dispatchDataToCountyAge($periode1);
                break;
            case self::SUBCOUNTY_AGE:
                $data = $this->dispatchDataToSubcountyAge($periode1);
                break;
            case self::NATIONAL_AGE:
                $data = $this->dispatchDataToNationalAge($periode2);
                break;
            case self::PARTNER_AGE:
                $data = $this->dispatchDataToPartnerAge($periode1);
                break;
        }

        return $data;
    }

    public function dispatchDataToNationalAge($periode) {
        $array = $this->data;
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

            foreach ($array as $row) {
                $year = \CsvUtils::extractYear($row);
                $month = \CsvUtils::extractMonth($row);
                $age = \CsvUtils::extractAge($row);
                $sample = \CsvUtils::extractTypeOfSample($row);
                $agecat = $this->getAge2Id($age);
                $sexe = \CsvUtils::extractSexe($row);
                $vl = \CsvUtils::extractViralLoad($row);
                if (($v['year'] == $year) && ($v['month'] == $month ) && ($v['age'] == $agecat )) {
                    $alltests += 1;
                    $edta += \CsvUtils::addEdta($sample);
                    $dbs += \CsvUtils::addDbs($sample);
                    $plasma += \CsvUtils::addPlasma($sample);
                    $maletest = \CsvUtils::addMaleTest($sexe);
                    $femaletest = \CsvUtils::addFemaleTest($sexe);
                    $nogendertest = \CsvUtils::addNoGenderTest($sexe);
                    $malenonsuppressed = \CsvUtils::addMaleNonSuppressed($sexe, $vl);
                    $femalenonsuppressed = \CsvUtils::addFemaleNonSuppressed($sexe, $vl);
                    $nogendernonsuppressed = \CsvUtils::addNoGenderNonSuppressed($sexe, $vl);
                    $undetected = \CsvUtils::addUndetected($vl);
                    $less1000 = \CsvUtils::addLess1000($vl);
                    $less5000 = \CsvUtils::addLess5000($vl);
                    $above5000 = \CsvUtils::addAbove5000($vl);
                    $invalids = \CsvUtils::addInvalids($vl);
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

    public function dispatchDataToSiteAge($periode) {
        $array = $this->data;
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

            foreach ($array as $row) {
                $year = \CsvUtils::extractYear($row);
                $month = \CsvUtils::extractMonth($row);
                $sitecode = \CsvUtils::extractDatimCode($row);
                $sample = \CsvUtils::extractTypeOfSample($row);
                $age = \CsvUtils::extractAge($row);
                $agecat = $this->getAge2Id($age);
                $sexe = \CsvUtils::extractSexe($row);
                $vl = \CsvUtils::extractViralLoad($row);
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['sitecode'] == $sitecode ) && ($v['age'] == $agecat ))) {
                    continue;
                }
                $received += 1;
                $alltests += 1;
                $edta += \CsvUtils::addEdta($sample);
                $dbs += \CsvUtils::addDbs($sample);
                $plasma += \CsvUtils::addPlasma($sample);
                $maletest = \CsvUtils::addMaleTest($sexe);
                $femaletest = \CsvUtils::addFemaleTest($sexe);
                $nogendertest = \CsvUtils::addNoGenderTest($sexe);
                $malenonsuppressed = \CsvUtils::addMaleNonSuppressed($sexe, $vl);
                $femalenonsuppressed = \CsvUtils::addFemaleNonSuppressed($sexe, $vl);
                $nogendernonsuppressed = \CsvUtils::addNoGenderNonSuppressed($sexe, $vl);
                $undetected = \CsvUtils::addUndetected($vl);
                $less1000 = \CsvUtils::addLess1000($vl);
                $less5000 = \CsvUtils::addLess5000($vl);
                $above5000 = \CsvUtils::addAbove5000($vl);
                $invalids = \CsvUtils::addInvalids($vl);
            }
            $data[$key]['month'] = $v['month'];
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['year'] = $v['year'];
            $data[$key]['sitecode'] = $v['sitecode'];
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

    public function dispatchDataToCountyAge($periode) {
        return $this->dispatchDataToSiteAge($periode);
    }

    public function dispatchDataToSubcountyAge($periode) {
        return $this->dispatchDataToSiteAge($periode);
    }

    public function dispatchDataToPartnerAge($periode) {
        return $this->dispatchDataToSiteAge($periode);
    }

}

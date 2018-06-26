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
        $periode = [];
        $array = $this->data;
        $x = 0;
        foreach ($array as $row) {
            $year = \CsvUtils::extractYear($row);
            $month = \CsvUtils::extractMonth($row);
            $sitecode = \CsvUtils::extractDatimCode($row);
            $sexe = \CsvUtils::extractSexe($row);
            foreach ($periode as $val) {
                if (($val['year'] == $year) && ($val['month'] == $month ) && ($val['sitecode'] == $sitecode ) && ($val['gender'] == $sexe )) {
                    continue(2);
                }
            }
            $periode[$x]['year'] = $year;
            $periode[$x]['month'] = $month;
            $periode[$x]['sitecode'] = $sitecode;
            $periode[$x]['gender'] = $sexe;
            $x++;
        }

        $data = [];
        switch ($type) {
            case self::NATIONAL_GENDER:
                $data = $this->dispatchDataToNationalGender($periode);
                break;
            case self::SITE_GENDER:
                $data = $this->dispatchDataToSiteGender($periode);
                break;
            case self::COUNTY_GENDER:
                $data = $this->dispatchDataToCountyGender($periode);
                break;
            case self::SUBCOUNTY_GENDER:
                $data = $this->dispatchDataToSubcountyGender($periode);
                break;
            case self::PARTNER_GENDER:
                $data = $this->dispatchDataToPartnerGender($periode);
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
                                if($sample== \CsvUtils::SAMPLE_EDTA){
                    $edta+=1;
                }
                elseif($sample== \CsvUtils::SAMPLE_DBS){
                    $dbs+=1;
                }elseif($sample== \CsvUtils::SAMPLE_PLASMA){
                    $plasma+=1;
                }
                if (intval($age) >= self::AGE_ADULT) {
                    $adults += 1;
                }
                if (intval($age) >= self::AGE_PAEDS) {
                    $paeds += 1;
                }
                if ($age === null || trim($age) === '') {
                    $noage += 1;
                }
                if ($vl === '< LL') {
                    $undetected += 1;
                } elseif (intval($vl) < 1000) {
                    $less1000 += 1;
                } elseif (intval($vl) < 5000) {
                    $less5000 += 1;
                } elseif (intval($vl) >= 5000) {
                    $above5000 += 1;
                } else {
                    $invalids += 1;
                }
                if (intval($age) < 2) {
                    $less2 += 1;
                } elseif (intval($age) <= 9) {
                    $less9 += 1;
                } elseif (intval($age) <= 14) {
                    $less14 += 1;
                } elseif (intval($age) <= 19) {
                    $less19 += 1;
                } elseif (intval($age) <= 24) {
                    $less24 += 1;
                } elseif (intval($age) >= 25) {
                    $over25 += 1;
                }
                if (intval($age) < 5) {
                    $less5 += 1;
                } elseif (intval($age) < 10) {
                    $less10 += 1;
                } elseif (intval($age) < 15) {
                    $less15 += 1;
                } elseif (intval($age) < 18) {
                    $less18 += 1;
                } elseif (intval($age) >= 18) {
                    $over18 += 1;
                }
            }
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['gender'] = $v['gender'];
            $data[$key]['tests'] = $alltests;
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
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['sitecode'] == $sitecode ) && ($v['gender'] == $sexe ))) {
                    continue;
                }
                $received += 1;
                $alltests += 1;
                if ($sample == \CsvUtils::SAMPLE_EDTA) {
                    $edta += 1;
                } elseif ($sample == \CsvUtils::SAMPLE_DBS) {
                    $dbs += 1;
                } elseif ($sample == \CsvUtils::SAMPLE_PLASMA) {
                    $plasma += 1;
                }
                if (intval($age) >= self::AGE_ADULT) {
                    $adults += 1;
                }
                if (intval($age) >= self::AGE_PAEDS) {
                    $paeds += 1;
                }
                if ($age === null || trim($age) === '') {
                    $noage += 1;
                }
                if ($vl === '< LL') {
                    $undetected += 1;
                } elseif (intval($vl) < 1000) {
                    $less1000 += 1;
                } elseif (intval($vl) < 5000) {
                    $less5000 += 1;
                } elseif (intval($vl) >= 5000) {
                    $above5000 += 1;
                } else {
                    $invalids += 1;
                }
                if (intval($age) < 2) {
                    $less2 += 1;
                } elseif (intval($age) <= 9) {
                    $less9 += 1;
                } elseif (intval($age) <= 14) {
                    $less14 += 1;
                } elseif (intval($age) <= 19) {
                    $less19 += 1;
                } elseif (intval($age) <= 24) {
                    $less24 += 1;
                } elseif (intval($age) >= 25) {
                    $over25 += 1;
                }
                if (intval($age) < 5) {
                    $less5 += 1;
                } elseif (intval($age) < 10) {
                    $less10 += 1;
                } elseif (intval($age) < 15) {
                    $less15 += 1;
                } elseif (intval($age) < 18) {
                    $less18 += 1;
                } elseif (intval($age) >= 18) {
                    $over18 += 1;
                }
            }
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['sitecode'] = $v['sitecode'];
            $data[$key]['gender'] = $v['gender'];
            $data[$key]['tests'] = $alltests;
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

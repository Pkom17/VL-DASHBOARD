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
class SampleTypeDispatcher {

    const AGE_ADULT = 21;
    const AGE_PAEDS = 15;
    const NATIONAL_SAMPLETYPE = 1;
    const SITE_SAMPLETYPE = 2;
    const COUNTY_SAMPLETYPE = 3;
    const SUBCOUNTY_SAMPLETYPE = 4;
    const PARTNER_SAMPLETYPE = 5;
    const LAB_SAMPLETYPE = 6;

    private $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function dispatchBySampleType($type) {
        $periode1 = [];
        $periode2 = [];
        $array = $this->data;
        $x = 0;
        $y = 0;
        foreach ($array as $row) {
            $year = \CsvUtils::extractYear($row);
            $month = \CsvUtils::extractMonth($row);
            $sitecode = \CsvUtils::extractDatimCode($row);
            $sampletype = \CsvUtils::extractTypeOfSample($row);

                        if (!in_array(['year' => $year, 'month' => $month, 'sitecode' => $sitecode, 'sampletype' => $sampletype], $periode1)) {
                $periode1[$x]['year'] = $year;
                $periode1[$x]['month'] = $month;
                $periode1[$x]['sitecode'] = $sitecode;
                $periode1[$x]['sampletype'] = $sampletype;
                $x++;
            }
            if (!in_array(['year' => $year, 'month' => $month, 'sampletype' => $sampletype], $periode2)) {
                $periode2[$y]['year'] = $year;
                $periode2[$y]['month'] = $month;
                $periode2[$y]['sampletype'] = $sampletype;
                $y++;
            }
        }
        $data = [];
        switch ($type) {
            case self::NATIONAL_SAMPLETYPE:
                $data = $this->dispatchDataToNationalSampleType($periode2);
                break;
            case self::SITE_SAMPLETYPE:
                $data = $this->dispatchDataToSiteSampleType($periode1);
                break;
            case self::COUNTY_SAMPLETYPE:
                $data = $this->dispatchDataToCountySampleType($periode1);
                break;
            case self::SUBCOUNTY_SAMPLETYPE:
                $data = $this->dispatchDataToSubcountySampleType($periode1);
                break;
            case self::PARTNER_SAMPLETYPE:
                $data = $this->dispatchDataToPartnerSampleType($periode1);
                break;
            case self::LAB_SAMPLETYPE:
                $data = $this->dispatchDataToLabSampleType($periode1);
                break;
        }
        return $data;
    }

    public function dispatchDataToNationalSampleType($periode) {
        $array = $this->data;
        $data = [];
        foreach ($periode as $key => $v) {
            $alltests = 0;
            $maletests = 0;
            $femaletests = 0;
            $nogendertests = 0;
            $adults = 0;
            $paeds = 0;
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
                $sampletype = \CsvUtils::extractTypeOfSample($row);
                $age = \CsvUtils::extractAge($row);
                $sexe = \CsvUtils::extractSexe($row);
                $vl = \CsvUtils::extractViralLoad($row);
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['sampletype'] == $sampletype))) {
                    continue;
                }
                $alltests += 1;
                $adults+= \CsvUtils::addAdults($age);
                $paeds+= \CsvUtils::addPaeds($age);
                $noage+= \CsvUtils::addNoage($age);
                if (intval($sexe) === 1) {
                    $maletests += 1;
                } elseif (intval($sexe) === 2) {
                    $femaletests += 1;
                } else {
                    $nogendertests += 1;
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
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['sampletype'] = $v['sampletype'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['maletest'] = $maletests;
            $data[$key]['femaletest'] = $femaletests;
            $data[$key]['nogendertest'] = $nogendertests;
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

    public function dispatchDataToSiteSampleType($periode) {
        $array = $this->data;
        $data = [];
        foreach ($periode as $key => $v) {
            $alltests = 0;
            $maletests = 0;
            $femaletests = 0;
            $nogendertests = 0;
            $adults = 0;
            $paeds = 0;
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
                $sitecode = \CsvUtils::extractDatimCode($row);
                $age = \CsvUtils::extractAge($row);
                $sexe = \CsvUtils::extractSexe($row);
                $vl = \CsvUtils::extractViralLoad($row);
                $sampletype = \CsvUtils::extractTypeOfSample($row);
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['sitecode'] == $sitecode ) && ($v['sampletype'] == $sampletype ))) {
                    continue;
                }
                $alltests += 1;
                if (intval($age) >= self::AGE_ADULT) {
                    $adults += 1;
                }
                if (intval($age) >= self::AGE_PAEDS) {
                    $paeds += 1;
                }
                if ($age === null || trim($age) === '') {
                    $noage += 1;
                }
                if (intval($sexe) === 1) {
                    $maletests += 1;
                } elseif (intval($sexe) === 2) {
                    $femaletests += 1;
                } else {
                    $nogendertests += 1;
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
            $data[$key]['dateupdated'] = date('d/m/Y H:i:s');
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['sitecode'] = $v['sitecode'];
            $data[$key]['sampletype'] = $v['sampletype'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['maletest'] = $maletests;
            $data[$key]['femaletest'] = $femaletests;
            $data[$key]['nogendertest'] = $nogendertests;
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

    public function dispatchDataToCountySampleType($periode) {
        return $this->dispatchDataToSiteSampleType($periode);
    }

    public function dispatchDataToLabSampleType($periode) {
        $lab_data =  $this->dispatchDataToSiteSampleType($periode);
        for($j=0;$j< count($lab_data);$j++){
            unset($lab_data[$j]['nogendertest']);
            unset($lab_data[$j]['dateupdated']);
        }
        return $lab_data;
    }

    public function dispatchDataToPartnerSampleType($periode) {
        return $this->dispatchDataToSiteSampleType($periode);
    }

    public function dispatchDataToSubcountySampleType($periode) {
        return $this->dispatchDataToSiteSampleType($periode);
    }

}

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
        $periode = [];
        $array = $this->data;
        $x = 0;
        foreach ($array as $row) {
            $year = \CsvUtils::extractYear($row);
            $month = \CsvUtils::extractMonth($row);
            $vlreason = \CsvUtils::extractVLReason($row);
            $sitecode = \CsvUtils::extractDatimCode($row);
            foreach ($periode as $val) {
                if (($val['year'] == $year) && ($val['month'] == $month ) && ($val['sitecode'] == $sitecode ) && ($val['justification'] == $vlreason )) {
                    continue(2);
                }
            }
            $periode[$x]['year'] = $year;
            $periode[$x]['month'] = $month;
            $periode[$x]['sitecode'] = $sitecode;
            $periode[$x]['justification'] = $vlreason;
            $x++;
        }
        $data = [];
        switch ($type) {
            case self::NATIONAL_JUSTIFICATION:
                $data = $this->dispatchDataToNationalJustification($periode);
                break;
            case self::SITE_JUSTIFICATION:
                $data = $this->dispatchDataToSiteJustification($periode);
                break;
            case self::COUNTY_JUSTIFICATION:
                $data = $this->dispatchDataToCountyJustification($periode);
                break;
            case self::SUBCOUNTY_JUSTIFICATION:
                $data = $this->dispatchDataToSubcountyJustification($periode);
                break;
            case self::PARTNER_JUSTIFICATION:
                $data = $this->dispatchDataToPartnerJustification($periode);
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
            $maletests = 0;
            $femaletests = 0;
            $nogendertests = 0;
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
            $data[$key]['justification'] = $v['justification'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['edta'] = $edta;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
            $data[$key]['adults'] = $adults;
            $data[$key]['paeds'] = $paeds;
            $data[$key]['noage'] = $noage;
            $data[$key]['maletest'] = $maletests;
            $data[$key]['femaletest'] = $femaletests;
            $data[$key]['nogendertest'] = $nogendertests;
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
            $maletests = 0;
            $femaletests = 0;
            $nogendertests = 0;
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
            $data[$key]['justification'] = $v['justification'];
            $data[$key]['tests'] = $alltests;
            $data[$key]['edta'] = $edta;
            $data[$key]['dbs'] = $dbs;
            $data[$key]['plasma'] = $plasma;
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

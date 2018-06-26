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
class SummaryDispatcher {

    const AGE_ADULT = 21;
    const AGE_PAEDS = 15;
    const NATIONAL_SUMMARY = 1;
    const SITE_SUMMARY = 2;
    const COUNTY_SUMMARY = 3;
    const SUBCOUNTY_SUMMARY = 4;
    const PARTNER_SUMMARY = 5;
    const LAB_SUMMARY = 6;

    private $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function dispatchBySummary($type) {
        $periode = [];
        $array = $this->data;
        $x = 0;
        foreach ($array as $row) {
            $year = \CsvUtils::extractYear($row);
            $month = \CsvUtils::extractMonth($row);
            $sitecode = \CsvUtils::extractDatimCode($row);
            foreach ($periode as $val) {
                if (($val['year'] == $year) && ($val['month'] == $month ) && ($val['sitecode'] == $sitecode )) {
                    continue(2);
                }
            }
            $periode[$x]['year'] = $year;
            $periode[$x]['month'] = $month;
            $periode[$x]['sitecode'] = $sitecode;
            $x++;
        }
        $data = [];
        switch ($type) {
            case self::NATIONAL_SUMMARY:
                $data = $this->dispatchDataToNationalSummary($periode);
                break;
            case self::SITE_SUMMARY:
                $data = $this->dispatchDataToSiteSummary($periode);
                break;
            case self::COUNTY_SUMMARY:
                $data = $this->dispatchDataToCountySummary($periode);
                break;
            case self::SUBCOUNTY_SUMMARY:
                $data = $this->dispatchDataToSubcountySummary($periode);
                break;
            case self::PARTNER_SUMMARY:
                $data = $this->dispatchDataToPartnerSummary($periode);
                break;
            case self::LAB_SUMMARY:
                $data = $this->dispatchDataToLabSummary($periode);
                break;
        }
        return $data;
    }

    public function dispatchDataToNationalSummary($periode) {
        $array = $this->data;
        $data = [];
        foreach ($periode as $key => $v) {
            $received = 0;
            $alltests = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
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
            $tat1 = 0;
            $tat2 = 0;
            $tat3 = 0;
            $tat4 = 0;
            $less2 = 0;
            $less9 = 0;
            $less14 = 0;
            $less19 = 0;
            $less24 = 0;
            $over25 = 0;
            foreach ($array as $row) {
                $year = \CsvUtils::extractYear($row);
                $month = \CsvUtils::extractMonth($row);
                $sample = \CsvUtils::extractTypeOfSample($row);
                $received_date = \CsvUtils::extractReceivedDate($row);
                $interv_date = \CsvUtils::extractInterviewDate($row);
                $completed_date = \CsvUtils::extractCompletedDate($row);
                $released_date = \CsvUtils::extractReleasedDate($row);
                $age = \CsvUtils::extractAge($row);
                $sexe = \CsvUtils::extractSexe($row);
                $vl = \CsvUtils::extractViralLoad($row);
                $tat1 = \CsvUtils::dateDiff($interv_date, $received_date);
                $tat2 = \CsvUtils::dateDiff($completed_date, $interv_date);
                $tat3 = \CsvUtils::dateDiff($released_date, $completed_date);
                $tat4 = \CsvUtils::dateDiff($released_date, $received_date);
                print_r($received_date);
                print_r($interv_date.'||');
                print_r($received_date.'||');
                print_r($completed_date.'||');
                print_r($released_date.'||');
                //print_r($released_date);
                print_r('------');
                //print_r($tat1);
                print_r($tat2);
                print_r("   ");
                //print_r($tat3);
                //print_r($tat4);
                if (!(($v['year'] == $year) && ($v['month'] == $month ))) {
                    continue;
                }
                $alltests += 1;
                $received += 1;
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
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['received'] = $received;
            $data[$key]['alltests'] = $alltests;
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
            $data[$key]['tat1'] = $tat1;
            $data[$key]['tat2'] = $tat2;
            $data[$key]['tat3'] = $tat3;
            $data[$key]['tat4'] = $tat4;
            $data[$key]['less2'] = $less2;
            $data[$key]['less9'] = $less9;
            $data[$key]['less14'] = $less14;
            $data[$key]['less19'] = $less19;
            $data[$key]['less24'] = $less24;
            $data[$key]['over25'] = $over25;
        }
        return $data;
    }

    public function dispatchDataToSiteSummary($periode) {
        $array = $this->data;
        $data = [];
        foreach ($periode as $key => $v) {
            $received = 0;
            $alltests = 0;
            $edta = 0;
            $dbs = 0;
            $plasma = 0;
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
            $tat1 = 0;
            $tat2 = 0;
            $tat3 = 0;
            $tat4 = 0;
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
                $sample = \CsvUtils::extractTypeOfSample($row);
                $received_date = \CsvUtils::extractReceivedDate($row);
                $interv_date = \CsvUtils::extractInterviewDate($row);
                $completed_date = \CsvUtils::extractCompletedDate($row);
                $released_date = \CsvUtils::extractReleasedDate($row);
                $tat1 = \CsvUtils::dateDiff($interv_date, $received_date);
                $tat2 = \CsvUtils::dateDiff($completed_date, $interv_date);
                $tat3 = \CsvUtils::dateDiff($released_date, $completed_date);
                $tat4 = \CsvUtils::dateDiff($released_date, $received_date);
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['sitecode'] == $sitecode ))) {
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
            $data[$key]['month'] = $v['month'];
            $data[$key]['year'] = $v['year'];
            $data[$key]['sitecode'] = $v['sitecode'];
            $data[$key]['received'] = $received;
            $data[$key]['alltests'] = $alltests;
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
            $data[$key]['tat1'] = $tat1;
            $data[$key]['tat2'] = $tat2;
            $data[$key]['tat3'] = $tat3;
            $data[$key]['tat4'] = $tat4;
            $data[$key]['less2'] = $less2;
            $data[$key]['less9'] = $less9;
            $data[$key]['less14'] = $less14;
            $data[$key]['less19'] = $less19;
            $data[$key]['less24'] = $less24;
            $data[$key]['over25'] = $over25;
        }
        return $data;
    }

    public function dispatchDataToCountySummary($periode) {
        return $this->dispatchDataToSiteSummary($periode);
    }

    public function dispatchDataToLabSummary($periode) {
        return $this->dispatchDataToLabSummary($periode);
    }

    public function dispatchDataToPartnerSummary($periode) {
        return $this->dispatchDataToSiteSummary($periode);
    }

    public function dispatchDataToSubcountySummary($periode) {
        return $this->dispatchDataToSiteSummary($periode);
    }

}

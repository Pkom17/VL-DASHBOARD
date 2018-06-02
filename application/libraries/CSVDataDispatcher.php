<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of CSVDataDispatcher
 *
 * @author IT
 */
class CSVDataDispatcher {

    const AGE_ADULT = 21;
    const AGE_PAEDS = 15;

    private $data;
    private $ageCat1;
    private $ageCat2;

//    private $month=0;
//    private $year=0;
//    private $site=0;
//    private $received=0;
//    private $alltests=0;
//    private $edta=0;
//    private $maletest=0;
//    private $femaletest=0;
//    private $nogendertest=0;
//    private $adults=0;
//    private $paeds=0; //>15 years old
//    private $noage=0;
//    private $undetected=0;
//    private $less1000=0;
//    private $less5000=0;
//    private $above5000=0;
//    private $less5=0;
//    private $less10=0;
//    private $less15=0;
//    private $less18=0;
//    private $over18=0;
//    private $sitessending=0;
//    private $less2=0;
//    private $less9=0;
//    private $less14=0;
//    private $less19=0;
//    private $less24=0;
//    private $over35=0;
    function __construct() {
        
    }

    public function load($csvData) {
        $this->data = $csvData;
    }
    public function setAgeCategories($cat1,$cat2){
        $this->ageCat1=$cat1;
        $this->ageCat2=$cat2;
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
        return array_keys($this->ageCat1, $this->getCategorysub1($age));
    }
    public function getAge2Id($age) {
        return array_keys($this->ageCat2, $this->getCategorysub2($age));
    }

    public function dispatch() {
        $periode = [];
        $array = $this->data;
        $x = 0;
        foreach ($array as $row) {
            $year = (array_key_exists('RELEASED_DATE', $row)) ? ((count($years = explode('-', $row['RELEASED_DATE'])) == 3) ? $years[0] : 0) : -1;
            $month = (array_key_exists('RELEASED_DATE', $row)) ? ((count($months = explode('-', $row['RELEASED_DATE'])) == 3) ? $months[1] : 0) : -1;
            $sitecode = (array_key_exists('SITECODE', $row)) ? $row['SITECODE'] : -1;
            $sitename = (array_key_exists('SITENOM', $row)) ? $row['SITENOM'] : -1;
            $vlreason = (array_key_exists('VL_REASON', $row)) ? $row['VL_REASON'] : -1;
            foreach ($periode as $val) {
                if (($val['year'] == $year) && ($val['month'] == $month ) && ($val['sitecode'] == $sitecode ) && ($val['vlreason'] == $vlreason )) {
                    continue(2);
                }
            }
            $periode[$x]['year'] = $year;
            $periode[$x]['month'] = $month;
            $periode[$x]['sitecode'] = $sitecode;
            $periode[$x]['sitename'] = $sitename;
            $periode[$x]['vlreason'] = $vlreason;
            $x++;
        }
        $data = $this->dispatchData($periode);
        return $data;
    }

    public function dispatchData($periode) {
        $array = $this->data;
        $data = [];
        foreach ($periode as $key => $v) {
            $received = 0;
            $alltests = 0;
            $femaletest = 0;
            $maletest = 0;
            $malnonsuppressed = 0;
            $femalenonsuppressed = 0;
            $nogendernonsuppressed = 0;
            $nogendertest = 0;
            $adults = 0;
            $paeds = 0;
            $noage = 0;
            $undetected = 0;
            $edta = 0;
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
                $year = (count($years = explode('-', $row['RELEASED_DATE'])) == 3) ? $years[0] : 0;
                $month = (count($months = explode('-', $row['RELEASED_DATE'])) == 3) ? $months[1] : 0;
                $sitecode = (array_key_exists('SITECODE', $row)) ? $row['SITECODE'] : -1;
                $vlreason = (array_key_exists('VL_REASON', $row)) ? $row['VL_REASON'] : -1;
                if (!(($v['year'] == $year) && ($v['month'] == $month ) && ($v['sitecode'] == $sitecode ) && ($v['vlreason'] == $vlreason ))) {
                    continue;
                }
                if ($row['ETUDE'] == 'VLS') {
                    $received += 1;
                    $alltests += 1;
                    $edta += 1;
                }
                if (intval($row['SEXE']) === 1) {
                    $maletest += 1;
                } elseif (intval($row['SEXE']) === 2) {
                    $femaletest += 1;
                } else {
                    $nogendertest += 1;
                }
                if (intval($row['AGEANS']) >= self::AGE_ADULT) {
                    $adults += 1;
                }
                if (intval($row['AGEANS']) >= self::AGE_PAEDS) {
                    $paeds += 1;
                }
                if ($row['AGEANS'] === null || trim($row['AGEANS']) === '') {
                    $noage += 1;
                }
                if ($row['Viral Load'] === '< LL') {
                    $undetected += 1;
                } elseif (intval($row['Viral Load']) < 1000) {
                    $less1000 += 1;
                } elseif (intval($row['Viral Load']) < 5000) {
                    $less5000 += 1;
                } elseif (intval($row['Viral Load']) >= 5000) {
                    $above5000 += 1;
                }
                if (intval($row['AGEANS']) < 2) {
                    $less2 += 1;
                } elseif (intval($row['AGEANS']) <= 9) {
                    $less9 += 1;
                } elseif (intval($row['AGEANS']) <= 14) {
                    $less14 += 1;
                } elseif (intval($row['AGEANS']) <= 19) {
                    $less19 += 1;
                } elseif (intval($row['AGEANS']) <= 24) {
                    $less24 += 1;
                } elseif (intval($row['AGEANS']) >= 25) {
                    $over25 += 1;
                }
                if (intval($row['AGEANS']) < 5) {
                    $less5 += 1;
                } elseif (intval($row['AGEANS']) < 10) {
                    $less10 += 1;
                } elseif (intval($row['AGEANS']) < 15) {
                    $less15 += 1;
                } elseif (intval($row['AGEANS']) < 18) {
                    $less18 += 1;
                } elseif (intval($row['AGEANS']) >= 18) {
                    $over18 += 1;
                }
            }
            $data[$key]['year'] = $v['year'];
            $data[$key]['month'] = $v['month'];
            $data[$key]['sitecode'] = $v['sitecode'];
            $data[$key]['sitename'] = $v['sitename'];
            $data[$key]['vlreason'] = $v['vlreason'];
            $data[$key]['femaletest'] = $femaletest;
            $data[$key]['maletest'] = $maletest;
            $data[$key]['nogendertest'] = $nogendertest;
            $data[$key]['received'] = $received;
            $data[$key]['alltests'] = $alltests;
            $data[$key]['adults'] = $adults;
            $data[$key]['paeds'] = $paeds;
            $data[$key]['noage'] = $noage;
            $data[$key]['undetected'] = $undetected;
            $data[$key]['less1000'] = $less1000;
            $data[$key]['less5000'] = $less5000;
            $data[$key]['above5000'] = $above5000;
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
            //$i = array_keys($periode, ['year' => $year, 'month' => $month]);
        }
        //print_r($data);
        return $data;
    }

}

<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of CSVDataDispatcher
 *
 * @author IT
 */
class Utils {

    const AGE_ADULT = 21;
    const AGE_PAEDS = 15;
    const SAMPLE_EDTA = 'Tube EDTA - Violet';
    const SAMPLE_EDTA_IN_BASE = 'EDTA/Plasma';
    const SAMPLE_DBS = 'DBS';

    //const SAMPLE_PLASMA = 'Plasma';

    public static function validData($csvData) {
        if (!is_array($csvData)) {
            return false;
        }
        $toRemove = ['ECHSTAT' => '','ETUDE' => '', 'NOM_SITE' => '', 'CODE_SITE' => '', 'NOM_SITE_DATIM' => '', 'DATENAIS' => '', 'AGEMOIS' => '', 'AGESEMS' => '', 'Viral Load log' => '',
            'STARTED_DATE' => '', 'STATVIH' => '', 'NOMMED' => '', 'NOMPRELEV' => '', 'ARV_INIT_DATE' => '', 'ARVREG' => '', 'CURRENT4' => '', 'CURRENT_ART' => '',
            'INITCD4_COUNT' => '', 'INITCD4_PERCENT' => '', 'INITCD4_DATE' => '', 'DEMANDCD4_COUNT' => '', 'DEMANDCD4_PERCENT' => '', 'REASON_OTHER' => '',
            'DEMANDCD4_DATE' => '', 'PRIOR_VL_BENEFIT' => '', 'VL_PREGNANCY' => '', 'VL_SUCKLE' => '', 'PRIOR_VL_Lab' => '', 'PRIOR_VL_Value' => '', 'PRIOR_VL_Date' => ''];
        $toLookFor = [
            'LABNO' => '', 'SUJETSIT' => '','SUJETNO' => '', 'DRCPT' => '', 'DINTV' => '', 'SEXE' => '', 'AGEANS' => '', 'Viral Load' => '', 'Type_of_sample' => '', 'COMPLETED_DATE' => '', 'RELEASED_DATE' => '', 'CURRENT1' => '',
            'CURRENT2' => '', 'CURRENT3' => '', 'VL_REASON' => ''
        ];
        foreach ($csvData as $v) {
            $d[] = array_diff_key($v, $toRemove);
        }
        if (!isset($d[0]) || count(array_diff_key($toLookFor, $d[0])) != 0) {
            return false;
        }
        return $d;
    }

    public static function extractYear(array $row) {
        $d = (array_key_exists('RELEASED_DATE', $row)) ? ($row['RELEASED_DATE']) : ((array_key_exists('COMPLETED_DATE', $row)) ? ($row['COMPLETED_DATE']) : -1);
        $date = DateTime::createFromFormat("d/m/Y H:i", $d);
        if ($date == FALSE) {
            return date("Y");
        }
        return intval($date->format("Y"));
    }

    public static function extractLabNo(array $row) {
        $d = (array_key_exists('LABNO', $row)) ? ($row['LABNO']) : -1;
        return trim($d);
    }

    public static function extractPatientNo(array $row) {
        $d1 = (array_key_exists('SUJETSIT', $row)) ? ($row['SUJETSIT']) : -1;
        if($d1 == null || $d1 == ''){
            $d1 = (array_key_exists('SUJETNO', $row)) ? ($row['SUJETNO']) : -1;
        }
        return trim($d1);
    }

    public static function extractMonth(array $row) {
        $d = (array_key_exists('RELEASED_DATE', $row)) ? ($row['RELEASED_DATE']) : ((array_key_exists('COMPLETED_DATE', $row)) ? ($row['COMPLETED_DATE']) : -1);
        $date = DateTime::createFromFormat("d/m/Y H:i", $d);
        if ($date == FALSE) {
            return intval(date("m")) - 1;
        }
        return intval($date->format("m"));
    }

    public static function extractDay(array $row) {
        $d = (array_key_exists('RELEASED_DATE', $row)) ? ($row['RELEASED_DATE']) : ((array_key_exists('COMPLETED_DATE', $row)) ? ($row['COMPLETED_DATE']) : -1);
        $date = DateTime::createFromFormat("d/m/Y H:i", $d);
        if ($date == FALSE) {
            return intval(date("d")) - 1;
        }
        return intval($date->format("d"));
    }

    public static function extractDatimCode(array $row) {
        $code = (array_key_exists('CODE_SITE_DATIM', $row)) ? ($row['CODE_SITE_DATIM']) : -1;
        return trim($code);
    }

    public static function extractAge(array $row) {
        $age = (array_key_exists('AGEANS', $row)) ? ($row['AGEANS']) : -1;
        return intval($age);
    }

    public static function extractGender(array $row) {
        $sexe_1 = (array_key_exists('SEXE', $row)) ? ($row['SEXE']) : -1;
        $sexe = ($sexe_1!= 1 || $sexe_1 !=2) ? ($sexe_1) : 2;
        return intval($sexe);
    }

    public static function extractViralLoad(array $row) {
        $vl = (array_key_exists('Viral Load', $row)) ? ($row['Viral Load']) : 0;
        $nvl = trim($vl);
        if (stristr($nvl, '<') != FALSE || stristr($nvl, 'LL') != FALSE) {
            return -1;
        } elseif (stristr($nvl, '>') != FALSE) {
            return '10000000';
        } elseif (stristr($nvl, 'X') != FALSE) {
            return -2;
        } elseif (is_numeric($nvl)) {
            return intval($nvl);
        } else {
            return -2;
        }
    }

    public static function extractVLReason(array $row) {
        $vlReason = (array_key_exists('VL_REASON', $row)) ? ($row['VL_REASON']) : -1;
        $res = trim($row['VL_REASON']);
        if (stristr($vlReason, "CV contr") != FALSE || $vlReason == 'CV contrÃ´le sous ARV' || $vlReason == 'CV contrôle sous ARV') {
            $res = "CV contrôle sous ARV";
        }
        if (trim($vlReason) == "" || stristr($vlReason, "Autres") != FALSE || trim($vlReason) == "Autres (à  préciser)" || trim($vlReason) == "Autres (Ã Â  prÃ©ciser)") {
            $res = "Autres";
        }
        return $res;
    }

    public static function extractVLCurrent1(array $row) {
        $c1 = (array_key_exists('CURRENT1', $row)) ? ($row['CURRENT1']) : -1;
        return trim(utf8_decode($c1));
    }

    public static function extractVLCurrent2(array $row) {
        $c2 = (array_key_exists('CURRENT2', $row)) ? ($row['CURRENT2']) : -1;
        return trim(utf8_decode($c2));
    }

    public static function extractVLCurrent3(array $row) {
        $c3 = (array_key_exists('CURRENT3', $row)) ? ($row['CURRENT3']) : -1;
        return trim(utf8_decode($c3));
    }

    public static function extractReceivedDate(array $row) {
        $d = (array_key_exists('DRCPT', $row)) ? ($row['DRCPT']) : -1;
        $date = DateTime::createFromFormat("d/m/Y H:i", $d);
        if ($date == FALSE) {
            return self::extractInterviewDate($row);
        }
        return $date->format("d/m/Y");
    }

    public static function extractInterviewDate(array $row) {
        $d = (array_key_exists('DINTV', $row)) ? ($row['DINTV']) : -1;
        $date = DateTime::createFromFormat("d/m/Y H:i", $d);
        if ($date == FALSE) {
            return self::extractCompletedDate($row);
        }
        return $date->format("d/m/Y");
    }

    public static function extractCompletedDate(array $row) {
        $d = (array_key_exists('COMPLETED_DATE', $row)) ? ($row['COMPLETED_DATE']) : -1;
        $date = DateTime::createFromFormat("d/m/Y H:i", $d);
        if ($date == FALSE) {
            return self::extractReleasedDate($row);
        }
        return $date->format("d/m/Y");
    }

    public static function extractReleasedDate(array $row) {
        $d = (array_key_exists('RELEASED_DATE', $row)) ? ($row['RELEASED_DATE']) : -1;
        $date = DateTime::createFromFormat("d/m/Y H:i", $d);
        if ($date == FALSE) {
            return date("d/m/Y");
        }
        return $date->format("d/m/Y");
    }

    public static function extractTypeOfSample(array $row) {
        $d = (array_key_exists('Type_of_sample', $row)) ? (trim($row['Type_of_sample'])) : -1;
        if ($d == self::SAMPLE_EDTA) {
            return self::SAMPLE_EDTA_IN_BASE;
        }
        return $d;
    }

    public static function dateDiff($date_1, $date_2, $differenceFormat = '%a') {
        $date1 = date_create_from_format("d/m/Y", $date_1);
        $date2 = date_create_from_format("d/m/Y", $date_2);
        $interval = date_diff($date1, $date2);
        return $interval->format($differenceFormat);
    }

    public static function addAdults($age) {
        $inc = 0;
        if (intval($age) >= self::AGE_ADULT) {
            $inc = 1;
        }
        return $inc;
    }

    public static function addPaeds($age) {
        $inc = 0;
        if (intval($age) >= self::AGE_PAEDS) {
            $inc = 1;
        }
        return $inc;
    }

    public static function addNoage($age) {
        $inc = 0;
        if ($age === null || trim($age) === '') {
            $inc = 1;
        }
        return $inc;
    }

    public static function addEdta($sample) {
        $inc = 0;
        if ($sample == \Utils::SAMPLE_EDTA_IN_BASE) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addDbs($sample) {
        $inc = 0;
        if ($sample == \Utils::SAMPLE_DBS) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addPlasma($sample) {
        $inc = 0;
        if ($sample == \Utils::SAMPLE_PLASMA) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addMaleTest($sexe) {
        $inc = 0;
        if ($sexe == 1) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addFemaleTest($sexe) {
        $inc = 0;
        if ($sexe == 2) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addNoGenderTest($sexe) {
        $inc = 0;
        if ($sexe != 2 && $sexe != 1) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addMaleNonSuppressed($sexe, $vl) {
        $inc = 0;
        if ($sexe == 1 && $vl >= 1000) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addFemaleNonSuppressed($sexe, $vl) {
        $inc = 0;
        if ($sexe == 2 && $vl >= 1000) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addNoGenderNonSuppressed($sexe, $vl) {
        $inc = 0;
        if (($sexe != 2 && $sexe != 1) && $vl >= 1000) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addUndetected($vl) {
        $inc = 0;
        if ($vl == -1) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess1000($vl) {
        $inc = 0;
        if ($vl != -1 && $vl < 1000) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess5000($vl) {
        $inc = 0;
        if ($vl >= 1000 && $vl < 5000) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addAbove5000($vl) {
        $inc = 0;
        if ($vl >= 5000) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addInvalids($vl) {
        $inc = 0;
        if ($vl != -2 && !is_numeric($inc)) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess2($age) {
        $inc = 0;
        if (intval($age) < 2) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess9($age) {
        $inc = 0;
        if (intval($age) >= 2 && intval($age) < 9) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess14($age) {
        $inc = 0;
        if (intval($age) >= 9 && intval($age) < 14) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess19($age) {
        $inc = 0;
        if (intval($age) >= 14 && intval($age) < 19) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess24($age) {
        $inc = 0;
        if (intval($age) >= 19 && intval($age) <= 24) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addOver25($age) {
        $inc = 0;
        if (intval($age) >= 25) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess5($age) {
        $inc = 0;
        if (intval($age) < 5) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess10($age) {
        $inc = 0;
        if (intval($age) >= 5 && intval($age) < 10) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess15($age) {
        $inc = 0;
        if (intval($age) >= 10 && intval($age) < 15) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addLess18($age) {
        $inc = 0;
        if (intval($age) >= 15 && intval($age) < 18) {
            $inc += 1;
        }
        return $inc;
    }

    public static function addOver18($age) {
        $inc = 0;
        if (intval($age) >= 18) {
            $inc += 1;
        }
        return $inc;
    }

    public static function getCop($year, $month) {
        $cop = 'COP ';
        if (in_array($month, [10, 11, 12])) {
            $cop .= intval(substr($year, -2));
        } elseif (in_array($month, [1, 2, 3, 4, 5, 6, 7, 8, 9])) {
            $cop .= (intval(substr($year, -2)) - 1);
        }
        return $cop;
    }

    public static function getAgeCategorysub1($age) {
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

    public static function getAgeCategorysub2($age) {
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
    public static function getAgeCategorysub3($age) {
        $cat = '';
        if ($age < 1) {
            $cat = '<1';
        } elseif ($age >= 1 && $age <=4) {
            $cat = '1-4';
        } elseif ($age >= 5 && $age <= 9) {
            $cat = '5-9';
        } elseif ($age >= 10 && $age <= 14) {
            $cat = '10-14';
        } elseif ($age >= 15 && $age <= 19) {
            $cat = '15-19';
        }elseif ($age >= 20 && $age <= 24) {
            $cat = '20-24';
        }elseif ($age >= 25 && $age <= 29) {
            $cat = '25-29';
        }elseif ($age >= 30 && $age <= 34) {
            $cat = '30-34';
        }elseif ($age >= 35 && $age <= 39) {
            $cat = '35-39';
        }elseif ($age >= 40 && $age <= 44) {
            $cat = '40-44';
        }elseif ($age >= 45 && $age <= 49) {
            $cat = '45-49';
        } elseif ($age >= 50) {
            $cat = '50+';
        }
        return $cat;
    }

    public static function getQuarter($mois) {
        return ceil($mois/3);
    }

}

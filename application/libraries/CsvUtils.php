<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of CSVDataDispatcher
 *
 * @author IT
 */
class CsvUtils {

    const AGE_ADULT = 21;
    const AGE_PAEDS = 15;
    const SAMPLE_EDTA = 'Tube EDTA - Violet';
    const SAMPLE_EDTA_IN_BASE = 'EDTA';
    const SAMPLE_DBS = 'DBS';
    const SAMPLE_PLASMA = 'Plasma';

    public function validData() {
        
    }

    public static function extractYear(array $row) {
        $d = (array_key_exists('RELEASED_DATE', $row)) ? ($row['RELEASED_DATE']) : -1;
        $dAsArray = explode(" ", $d);
        if (is_array($dAsArray)) {
            $date = date_create_from_format("d/m/Y", $dAsArray[0]);
            return intval($date->format("Y"));
        }
        return -1;
    }

    public static function extractMonth(array $row) {
        $d = (array_key_exists('RELEASED_DATE', $row)) ? ($row['RELEASED_DATE']) : -1;
        $dAsArray = explode(" ", $d);
        if (is_array($dAsArray)) {
            $date = date_create_from_format("d/m/Y", $dAsArray[0]);
            return intval($date->format("m"));
        }
        return -1;
    }

    public static function extractDatimCode(array $row) {
        $code = (array_key_exists('CODE_SITE_DATIM', $row)) ? ($row['CODE_SITE_DATIM']) : -1;
        return trim($code);
    }

    public static function extractAge(array $row) {
        $age = (array_key_exists('AGEANS', $row)) ? ($row['AGEANS']) : -1;
        return intval($age);
    }

    public static function extractSexe(array $row) {
        $sexe = (array_key_exists('SEXE', $row)) ? ($row['SEXE']) : -1;
        return intval($sexe);
    }

    public static function extractViralLoad(array $row) {
        $vl = (array_key_exists('Viral Load', $row)) ? ($row['Viral Load']) : -1;
        return trim($vl);
    }

    public static function extractVLReason(array $row) {
        $vlReason = (array_key_exists('VL_REASON', $row)) ? ($row['VL_REASON']) : -1;
        return trim(utf8_decode($vlReason));
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
        $dAsArray = explode(" ", $d);
        if (is_array($dAsArray)) {
            return $dAsArray[0];
        }
        return $dAsArray;
    }

    public static function extractInterviewDate(array $row) {
        $d = (array_key_exists('DINTV', $row)) ? ($row['DINTV']) : -1;
        $dAsArray = explode(" ", $d);
        if (is_array($dAsArray)) {
            return $dAsArray[0];
        }
        return $dAsArray;
    }

    public static function extractCompletedDate(array $row) {
        $d = (array_key_exists('COMPLETED_DATE', $row)) ? ($row['COMPLETED_DATE']) : -1;
        $dAsArray = explode(" ", $d);
        if (is_array($dAsArray)) {
            return $dAsArray[0];
        }
        return $dAsArray;
    }

    public static function extractReleasedDate(array $row) {
        $d = (array_key_exists('RELEASED_DATE', $row)) ? ($row['RELEASED_DATE']) : -1;
        $dAsArray = explode(" ", $d);
        if (is_array($dAsArray)) {
            return $dAsArray[0];
        }
        return $dAsArray;
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
        if ($age === null || trim($age) === ''){
            $inc = 1;
        }
        return $inc;
    }

}

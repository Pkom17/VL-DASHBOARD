<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Description of RegimenExtractor
 *
 * @author IT
 */
class Regimen_extractor {

    const TDF = 'TDF';
    const TC = '3TC';
    const EFV = 'EFV';
    const ABC = 'ABC';
    const AZT = 'AZT';
    const LPV = 'LPV/r';
    const NVP = 'NVP';
    const ATV = 'ATV/r';
    const SQV = 'SQV/r';
    const DRV = 'DRV/r';
    const TDF_3TC_EFV = 'TDF+3TC+EFV';
    const AZT_3TC_EFV = 'AZT+3TC+EFV';
    const ABC_3TC_EFV = 'ABC+3TC+EFV';
    const TDF_3TC_LPVR = 'TDF+3TC+LPV/r';
    const TDF_3TC_NVP = 'TDF+3TC+NVP';
    const TDF_3TC_AZT = 'TDF+3TC+AZT';
    const AZT_3TC_ATVR = 'AZT+3TC+ATV/r';
    const TDF_3TC_ATVR = 'TDF+3TC+ATV/r';
    const TDF_3TC_SQVR = 'TDF+3TC+SQV/r';
    const TDF_3TC_DRVR = 'TDF+3TC+DRV/r';
    const ABC_3TC_LPVR = 'ABC+3TC+LPV/r';
    const AZT_3TC_ABC = 'AZT+3TC+ABC';
    const AZT_3TC_LPVR = 'AZT+3TC+LPV/r';
    const AZT_3TC_DRVR = 'AZT+3TC+LPV/r';
    const ABC_3TC_ATVR = 'AZT+3TC+LPV/r';
    const ABC_3TC_NVP = 'AZT+3TC+LPV/r';
    const AZT_3TC_NVP = 'AZT+3TC+LPV/r';
    const AUCUNE = 'Aucune donnée';
    const AUTRE = 'Autre';

    public function getRegimen($m1, $m2, $m3) {
        if(is_null($m1) && is_null($m2) && is_null($m3)){
            return self::AUCUNE;
        }
        $array_mol = [$this->normalize($m1), $this->normalize($m2), $this->normalize($m3)];
        $array_reg_ref = [
            self::TDF_3TC_EFV => [self::TDF, self::TC, self::EFV],
            self::AZT_3TC_EFV => [self::AZT, self::TC, self::EFV],
            self::ABC_3TC_EFV => [self::ABC, self::TC, self::EFV],
            self::TDF_3TC_LPVR => [self::TDF, self::TC, self::LPV],
            self::TDF_3TC_NVP => [self::TDF, self::TC, self::NVP],
            self::TDF_3TC_AZT => [self::TDF, self::TC, self::AZT],
            self::AZT_3TC_ATVR => [self::AZT, self::TC, self::ATV],
            self::TDF_3TC_ATVR => [self::TDF, self::TC, self::ATV],
            self::TDF_3TC_SQVR => [self::TDF, self::TC, self::SQV],
            self::TDF_3TC_DRVR => [self::TDF, self::TC, self::DRV],
            self::ABC_3TC_LPVR => [self::ABC, self::TC, self::LPV],
            self::AZT_3TC_ABC => [self::AZT, self::TC, self::ABC],
            self::AZT_3TC_LPVR => [self::AZT, self::TC, self::LPV],
            self::AZT_3TC_DRVR => [self::AZT, self::TC, self::DRV],
            self::ABC_3TC_ATVR => [self::ABC, self::TC, self::ATV],
            self::ABC_3TC_NVP => [self::ABC, self::TC, self::NVP],
            self::AZT_3TC_NVP => [self::AZT, self::TC, self::NVP],
        ];
        foreach ($array_reg_ref as $key => $value) {
            if(count(array_diff($value, $array_mol)) ==0){
                return $key;
            }
        }
        return self::AUTRE;
    }

    public function normalize($mol) {
        $val = '';
        $m = strtoupper($mol);
        switch ($m) {
            case 'T':
            case 'TDF': $val = self::TDF;
                break;
            case '3':
            case '3T':
            case '3TC': $val = self::TC;
                break;
            case 'EFZ':
            case 'EF':
            case 'EV':
            case 'EFV':
            case 'EFV600':
            case 'EFVO': $val = self::EFV;
                break;
            case 'ABC': $val = self::ABC;
                break;
            case 'AZT': $val = self::AZT;
                break;
            case 'L/R':
            case 'LPRT':
            case 'LPV/R':
            case 'LOP':
            case 'LOPV':
            case 'LOPV/R':
            case 'LOPI':
            case 'LPVIRT':
            case 'LOPI/RITO':
            case 'LOPIRITO':
            case 'LP':
            case 'LP/R':
            case 'LPV/RT':
            case 'LOP/R':
            case 'LOP/RT':
            case 'LP/RT':
            case 'LPR':
            case 'LPV/RTV':
            case 'LPV/RITO':
            case 'LPRT/RT':
            case 'LPR/RT':
            case 'LP/V':
            case 'LOP/R':
            case 'LPVIR':
            case 'LPVR':
            case 'ALLUVIA':
            case 'ALUVIA':
            case 'LPV': $val = self::LPV;
                break;
            case 'N':
            case 'NVP SIROP':
            case 'NVP': $val = self::NVP;
                break;
            case 'ATV': $val = self::ATV;
                break;
            case 'SQV': $val = self::SQV;
                break;
            case 'DRV': $val = self::DRV;
                break;
            default:
                break;
        }
        return $val;
    }

}

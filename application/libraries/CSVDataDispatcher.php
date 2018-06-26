<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Description of CSVDataDispatcher
 *
 * @author IT
 */
use dataDispatcher\GenderDispatcher;
use dataDispatcher\AgeDispatcher;
use dataDispatcher\JustificationDispatcher;
use dataDispatcher\RegimenDispatcher;
use dataDispatcher\SummaryDispatcher;

class CSVDataDispatcher {

    const AGE_ADULT = 21;
    const AGE_PAEDS = 15;

    private $data;
    private $ageCat1;
    private $ageCat2;

    function __construct() {
        
    }

    public function load($csvData) {
        $this->data = $csvData;
    }

    public function setAgeCategories($cat1, $cat2) {
        $this->ageCat1 = $cat1;
        $this->ageCat2 = $cat2;
    }

    public function toSiteAge() {
        $ageDispatcher = new AgeDispatcher($this->data, $this->ageCat1, $this->ageCat2);
        return $ageDispatcher->dispatchByAge(AgeDispatcher::SITE_AGE);
    }

    public function toCountyAge() {
        $ageDispatcher = new AgeDispatcher($this->data, $this->ageCat1, $this->ageCat2);
        return $ageDispatcher->dispatchByAge(AgeDispatcher::COUNTY_AGE);
    }

    public function toSubcountyAge() {
        $ageDispatcher = new AgeDispatcher($this->data, $this->ageCat1, $this->ageCat2);
        return $ageDispatcher->dispatchByAge(AgeDispatcher::SUBCOUNTY_AGE);
    }

    public function toPartnerAge() {
        $ageDispatcher = new AgeDispatcher($this->data, $this->ageCat1, $this->ageCat2);
        return $ageDispatcher->dispatchByAge(AgeDispatcher::PARTNER_AGE);
    }

    public function toNationalAge() {
        $ageDispatcher = new AgeDispatcher($this->data, $this->ageCat1, $this->ageCat2);
        return $ageDispatcher->dispatchByAge(AgeDispatcher::NATIONAL_AGE);
    }

    public function toSiteGender() {
        $genderDispatcher = new GenderDispatcher($this->data);
        return $genderDispatcher->dispatchByGender(GenderDispatcher::SITE_GENDER);
    }

    public function toCountyGender() {
        $genderDispatcher = new GenderDispatcher($this->data);
        return $genderDispatcher->dispatchByGender(GenderDispatcher::COUNTY_GENDER);
    }

    public function toSubcountyGender() {
        $genderDispatcher = new GenderDispatcher($this->data);
        return $genderDispatcher->dispatchByGender(GenderDispatcher::SUBCOUNTY_GENDER);
    }

    public function toPartnerGender() {
        $genderDispatcher = new GenderDispatcher($this->data);
        return $genderDispatcher->dispatchByGender(GenderDispatcher::PARTNER_GENDER);
    }

    public function toNationalGender() {
        $genderDispatcher = new GenderDispatcher($this->data);
        return $genderDispatcher->dispatchByGender(GenderDispatcher::NATIONAL_GENDER);
    }

    public function toSiteJustification() {
        $justificationDispatcher = new JustificationDispatcher($this->data);
        return $justificationDispatcher->dispatchByJustification(JustificationDispatcher::SITE_JUSTIFICATION);
    }

    public function toCountyJustification() {
        $justificationDispatcher = new JustificationDispatcher($this->data);
        return $justificationDispatcher->dispatchByJustification(JustificationDispatcher::COUNTY_JUSTIFICATION);
    }

    public function toSubcountyJustification() {
        $justificationDispatcher = new JustificationDispatcher($this->data);
        return $justificationDispatcher->dispatchByJustification(JustificationDispatcher::SUBCOUNTY_JUSTIFICATION);
    }

    public function toPartnerJustification() {
        $justificationDispatcher = new JustificationDispatcher($this->data);
        return $justificationDispatcher->dispatchByJustification(JustificationDispatcher::PARTNER_JUSTIFICATION);
    }

    public function toNationalJustification() {
        $justificationDispatcher = new JustificationDispatcher($this->data);
        return $justificationDispatcher->dispatchByJustification(JustificationDispatcher::NATIONAL_JUSTIFICATION);
    }

    public function toSiteRegimen() {
        $regimenDispatcher = new RegimenDispatcher($this->data);
        return $regimenDispatcher->dispatchByRegimen(RegimenDispatcher::SITE_REGIMEN);
    }

    public function toCountyRegimen() {
        $regimenDispatcher = new RegimenDispatcher($this->data);
        return $regimenDispatcher->dispatchByRegimen(RegimenDispatcher::COUNTY_REGIMEN);
    }

    public function toSubcountyRegimen() {
        $regimenDispatcher = new RegimenDispatcher($this->data);
        return $regimenDispatcher->dispatchByRegimen(RegimenDispatcher::SUBCOUNTY_REGIMEN);
    }

    public function toPartnerRegimen() {
        $regimenDispatcher = new RegimenDispatcher($this->data);
        return $regimenDispatcher->dispatchByRegimen(RegimenDispatcher::PARTNER_REGIMEN);
    }

    public function toNationalRegimen() {
        $regimenDispatcher = new RegimenDispatcher($this->data);
        return $regimenDispatcher->dispatchByRegimen(RegimenDispatcher::NATIONAL_REGIMEN);
    }
    public function toSiteSummary() {
        $summaryDispatcher = new SummaryDispatcher($this->data);
        return $summaryDispatcher->dispatchBySummary(SummaryDispatcher::SITE_SUMMARY);
    }

    public function toCountySummary() {
        $summaryDispatcher = new SummaryDispatcher($this->data);
        return $summaryDispatcher->dispatchBySummary(SummaryDispatcher::COUNTY_SUMMARY);
    }

    public function toSubcountySummary() {
        $summaryDispatcher = new SummaryDispatcher($this->data);
        return $summaryDispatcher->dispatchBySummary(SummaryDispatcher::SUBCOUNTY_SUMMARY);
    }

    public function toPartnerSummary() {
        $summaryDispatcher = new SummaryDispatcher($this->data);
        return $summaryDispatcher->dispatchBySummary(SummaryDispatcher::PARTNER_SUMMARY);
    }
    public function toLabSummary() {
        $summaryDispatcher = new SummaryDispatcher($this->data);
        return $summaryDispatcher->dispatchBySummary(SummaryDispatcher::LAB_SUMMARY);
    }

    public function toNationalSummary() {
        $summaryDispatcher = new SummaryDispatcher($this->data);
        return $summaryDispatcher->dispatchBySummary(SummaryDispatcher::NATIONAL_SUMMARY);
    }

}

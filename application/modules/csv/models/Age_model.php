<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author IT
 */
class Age_model extends CI_Model {

    function __construct() {
        parent:: __construct();
    }

    public function getAge1Id($age) {
        $agecat1 = $this->Csv_import_model->get_ageCategory1();
        $keys = array_keys($agecat1, $age);
        if (count($keys) > 0) {
            return $keys[0];
        } else {
            return 0;
        }
    }

    public function getAge2Id($age) {
        $agecat2 = $this->Csv_import_model->get_ageCategory2();
        $keys = array_keys($agecat2, $age);
        if (count($keys) > 0) {
            return $keys[0];
        } else {
            return 0;
        }
    }

    public function getAgeCat1Id($agecat) {
        $sql = 'select ID from agecategory where name= ? and  subID = 1';
        $query = $this->db->query($sql, [$agecat]);
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function getAgeCat2Id($agecat) {
        $query = $this->db->query('select ID from agecategory where name= ? and  subID = 2', [$agecat]);
        $rows = $query->result_array();
        if (is_array($rows) && isset($rows[0]['ID'])) {
            return $rows[0]['ID'];
        } else {
            return 0;
        }
    }

    public function toNationalAge() {
        $sql = 'SELECT DISTINCT 
	year,month,agecat2 age, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(gender=1 and viralload>=1000,1,0)) malenonsuppressed,SUM(IF(gender=2 and viralload>=1000,1,0)) femalenonsuppressed,
	SUM(IF((gender!=1 and gender !=2) and viralload>=1000,1,0)) nogendernonsuppressed,SUM(IF(viralload="< LL",1,0)) undetected,
	SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,
	SUM(IF(viralload>=5000,1,0)) above5000, 0 invalids
        FROM `vl_sample_import` 
        WHERE computed = "N" 
        GROUP BY year,month,agecat2
        ORDER BY year DESC, month asc, agecat2 asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toSiteAge() {
        $sql = 'SELECT DISTINCT 
	year,month,facility,agecat2 age, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,
	SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,
	SUM(IF(viralload>=5000,1,0)) above5000, 0 invalids
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,facility, agecat2
ORDER BY year DESC, month asc, agecat2 asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toSubcountyAge() {
        $sql = 'SELECT DISTINCT 
	year,month,subcounty,agecat2 age, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,
	SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,
	SUM(IF(viralload>=5000,1,0)) above5000, 0 invalids
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,subcounty, agecat2
ORDER BY year DESC, month asc, agecat2 asc;';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toCountyAge() {
        $sql = 'SELECT DISTINCT 
	year,month,county,agecat2 age, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,
	SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,
	SUM(IF(viralload>=5000,1,0)) above5000, 0 invalids
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,county, agecat2
ORDER BY year DESC, month asc, agecat2 asc;';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toPartnerAge() {
        $sql = 'SELECT DISTINCT 
	year,month,partner,agecat2 age, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,
	SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,
	SUM(IF(viralload>=5000,1,0)) above5000, 0 invalids
        FROM `vl_sample_import` 
 WHERE computed = "N" 
        GROUP BY year,month,partner, agecat2
        ORDER BY year DESC, month asc, agecat2 asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

}

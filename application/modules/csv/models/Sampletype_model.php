<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author IT
 */
class Sampletype_model extends CI_Model {

    function __construct() {
        parent:: __construct();
    }


    public function toNationalSampletype() {
        $sql = 'SELECT DISTINCT 
	year,month,sampletype, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,sampletype
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toSiteSampletype() {
        $sql = 'SELECT DISTINCT 
	year,month,facility,sampletype, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,facility,sampletype
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toSubcountySampletype() {
        $sql = 'SELECT DISTINCT 
	year,month,subcounty,sampletype, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,subcounty,sampletype
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toCountySampletype() {
        $sql = 'SELECT DISTINCT 
	year,month,county,sampletype, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,county,sampletype
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toPartnerSampletype() {
        $sql = 'SELECT DISTINCT 
	year,month,partner,sampletype, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,partner,sampletype
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toLabSampletype() {
        $sql = 'SELECT DISTINCT 
	year,month,lab,sampletype, COUNT(*) tests, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,lab,sampletype
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }
}

<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author IT
 */
class Summary_model extends CI_Model {

    function __construct() {
        parent:: __construct();
    }


    public function toNationalSummary() {
        $sql = 'SELECT DISTINCT 
	year,month, COUNT(*) alltests, COUNT(*) received,SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(sampletype=1,1,0)) alledta,SUM(IF(sampletype=2,1,0)) alldbs,SUM(IF(sampletype=3,1,0)) allplasma,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25,
	avg(tat1) tat1,avg(tat2) tat2,avg(tat3) tat3,avg(tat4) tat4
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toSiteSummary() {
        $sql = 'SELECT DISTINCT 
	year,month,facility, COUNT(*) alltests, COUNT(*) received, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(sampletype=1,1,0)) alledta,SUM(IF(sampletype=2,1,0)) alldbs,SUM(IF(sampletype=3,1,0)) allplasma,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25,
	avg(tat1) tat1,avg(tat2) tat2,avg(tat3) tat3,avg(tat4) tat4
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,facility
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toSubcountySummary() {
        $sql = 'SELECT DISTINCT 
	year,month,subcounty, COUNT(*) alltests, COUNT(*) received, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(sampletype=1,1,0)) alledta,SUM(IF(sampletype=2,1,0)) alldbs,SUM(IF(sampletype=3,1,0)) allplasma,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25,
	avg(tat1) tat1,avg(tat2) tat2,avg(tat3) tat3,avg(tat4) tat4
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,subcounty
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toCountySummary() {
        $sql = 'SELECT DISTINCT 
	year,month,county, COUNT(*) alltests, COUNT(*) received,SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(sampletype=1,1,0)) alledta,SUM(IF(sampletype=2,1,0)) alldbs,SUM(IF(sampletype=3,1,0)) allplasma,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25,
	avg(tat1) tat1,avg(tat2) tat2,avg(tat3) tat3,avg(tat4) tat4
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,county
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toPartnerSummary() {
        $sql = 'SELECT DISTINCT 
	year,month,partner, COUNT(*) alltests,COUNT(*) received, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(sampletype=1,1,0)) alledta,SUM(IF(sampletype=2,1,0)) alldbs,SUM(IF(sampletype=3,1,0)) allplasma,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25,
	avg(tat1) tat1,avg(tat2) tat2,avg(tat3) tat3,avg(tat4) tat4
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,partner
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }

    public function toLabSummary() {
        $sql = 'SELECT DISTINCT 
	year,month,lab, COUNT(*) alltests,COUNT(*) received, SUM(IF(viralload>=1000,1,0)) sustxfail,
	SUM(IF(age>21,1,0)) adults,SUM(IF(age>15,1,0)) paeds, SUM(IF(age=null or age="",1,0)) noage,
	SUM(IF(sampletype=1,1,0)) edta,SUM(IF(sampletype=2,1,0)) dbs,SUM(IF(sampletype=3,1,0)) plasma,
	SUM(IF(sampletype=1,1,0)) alledta,SUM(IF(sampletype=2,1,0)) alldbs,SUM(IF(sampletype=3,1,0)) allplasma,
	SUM(IF(gender=1,1,0)) maletest,SUM(IF(gender=2,1,0)) femaletest,SUM(IF(gender!=1 and gender!=2,1,0)) nogendertest,
	SUM(IF(viralload="< LL",1,0)) undetected,SUM(IF(viralload!="< LL" and viralload<1000,1,0)) less1000,
	SUM(IF(viralload>=1000 and viralload<5000,1,0)) less5000,SUM(IF(viralload>=5000,1,0)) above5000,
	0 invalids, SUM(IF(age>=5 and age < 10,1,0)) less10,SUM(IF(age>=10 and age < 15,1,0)) less15,
	SUM(IF(age>=15 and age < 18,1,0)) less18,SUM(IF(age>=18,1,0)) over18,SUM(IF(age<2,1,0)) less2,
	SUM(IF(age>=2 and age <9,1,0)) less9, SUM(IF(age>=9 and age <14,1,0)) less14,SUM(IF(age>=14 and age <19,1,0)) less19,
	SUM(IF(age>=19 and age <=24,1,0)) less24, SUM(IF(age>=25,1,0)) over25,
	avg(tat1) tat1,avg(tat2) tat2,avg(tat3) tat3,avg(tat4) tat4
FROM `vl_sample_import` 
WHERE computed = "N" 
GROUP BY year,month,lab
ORDER BY year DESC, month asc';
        $query = $this->db->query($sql);
        $rows = $query->result_array();
        return $rows;
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
* 
*/
class Samples extends MY_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('samples_model');
	}

	function samples_outcomes($year=NULL,$month=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes']= $this->samples_model->samples_outcomes($year,$month,$to_year,$to_month);
		$data['div'] = "random_div";
		$data['type'] = "normal";
		//echo "this";

		$this->load->view('county_outcomes_view', $data);
	}

	function samples_county_outcomes($year=NULL,$month=NULL,$sample=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['trends'] = $this->samples_model->county_outcomes($year,$month,$sample,$to_year,$to_month);
		$data['div_name'] = "randomer_div";

    	$this->load->view('trends_outcomes_view',$data);

    }

	function samples_vl_outcome($year=NULL,$month=NULL,$sample=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes']= $this->samples_model->samples_vl_outcomes($year,$month,$sample,$to_year,$to_month);

		// $this->load->view('vl_outcomes_view',$data);
		$this->load->view('vl_outcomes_view',$data);
	}

	function samples_gender($year=NULL,$month=NULL,$sample=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes'] = $this->samples_model->samples_gender($year,$month,$sample,$to_year,$to_month);
		
    	// $this->load->view('gender_view',$data);
    	$this->load->view('age_regimen_gender_view',$data);
	}

	function samples_age($year=NULL,$month=NULL,$sample=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes'] = $this->samples_model->samples_age($year,$month,$sample,$to_year,$to_month);
		
    	// $this->load->view('agegroup_view',$data);
    	$this->load->view('regimen_agegroup_view',$data);
	}

	function suppression($year=NULL,$sample=NULL)
	{
		$data['trends'] = $this->samples_model->samples_suppression($year,$sample);

		$data['div_name'] = "sample_outcome_suppression";

		$this->load->view('trends_outcomes_view', $data);
	}
}
?>
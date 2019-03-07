<?php
defined("BASEPATH") or exit();
/**
* 
*/
class Ages extends MY_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('ages_model');
	}

	function age_outcomes($year=NULL,$month=NULL,$to_year=NULL,$to_month=NULL,$partner=NULL)
	{
		$data['trends']= $this->ages_model->ages_outcomes($year,$month,$to_year,$to_month,$partner);
		$data['div_name'] = "age_summary_outcomes";		

		$this->load->view('trends_outcomes_view', $data);
	}
	function age_outcomes_sup($year=NULL,$month=NULL,$to_year=NULL,$to_month=NULL,$partner=NULL)
	{
		$data['trends']= $this->ages_model->ages_outcomes($year,$month,$to_year,$to_month,$partner);
		$data['div_name'] = "age_summary_outcomes_sup";		

		$this->load->view('trends_outcomes_view', $data);
	}

	function age_county_outcomes($year=NULL,$month=NULL,$age=NULL,$to_year=NULL,$to_month=NULL,$partner=NULL)
	{
		$data['trends'] = $this->ages_model->county_outcomes($year,$month,$age,$to_year,$to_month,$partner);
		$data['div_name'] = "age_county_outcomes";		

		$this->load->view('trends_outcomes_view', $data);
	}

	function age_vl_outcome($year=NULL,$month=NULL,$age=NULL,$to_year=NULL,$to_month=NULL,$partner=NULL)
	{
		$data['outcomes']= $this->ages_model->ages_vl_outcomes($year,$month,$age,$to_year,$to_month,$partner);

		$this->load->view('vl_outcomes_view',$data);
	}

	function age_gender($year=NULL,$month=NULL,$age=NULL,$to_year=NULL,$to_month=NULL,$partner=NULL)
	{
		$data['outcomes'] = $this->ages_model->ages_gender($year,$month,$age,$to_year,$to_month,$partner);

                $this->load->view('age_regimen_gender_view',$data);
	}
	function p_age_gender2($year=NULL,$month=NULL,$age=NULL,$partner=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes'] = $this->ages_model->p_ages_gender2($year,$month,$age,$partner,$to_year,$to_month);

                $this->load->view('p_age_regimen_gender_view',$data);
	}
	function p_age_gender_regimen($year=NULL,$month=NULL,$age=NULL,$partner=NULL,$regimen=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes'] = $this->ages_model->p_ages_gender_regimen($year,$month,$age,$partner,$regimen,$to_year,$to_month);

                $this->load->view('p_age_regimen_gender_view',$data);
	}

	function age_breakdowns($year=NULL,$month=NULL,$age=NULL,$to_year=NULL,$to_month=NULL,$county=null,$partner=null,$subcounty=null,$site=null)
	{
		$data['outcomes'] = $this->ages_model->ages_breakdowns($year,$month,$age,$to_year,$to_month,$county,$partner,$subcounty,$site);
		
		$this->load->view('age_breakdown_listing',$data);
	}

	function sample_types($year=NULL,$age=NULL,$partner=NULL)
	{
		$data['outcomes'] = $this->ages_model->ages_samples($year,$age,$partner);
		$link = $year . '/' . $age . '/' . $partner;

		$data['link'] =  base_url('charts/ages/download_sampletypes/' . $link);

    	$this->load->view('sample_types_view',$data);
	}


	function download_sampletypes($year=NULL,$age=NULL,$partner=NULL)
	{
		$this->ages_model->download_sampletypes($year,$age,$partner);
	}


}
?>
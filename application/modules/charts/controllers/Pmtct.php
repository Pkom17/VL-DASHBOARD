<?php
(defined('BASEPATH') or exit('No direct script access allowed!'));

/**
* 
*/
class Pmtct extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('pmtct_model');
	}

	public function pmtct_outcomes ($year=null,$month=null,$type=1,$to_year=null,$to_month=null,$partner=null,$national=null,$county=null,$subcounty=null,$site=null)
	{
		$data['outcomes'] = $this->pmtct_model->pmtct_outcomes($year,$month,$to_year,$to_month,$partner,$national,$county,$subcounty,$site);
		if ($type == 1) {
			$data['type'] = 'normal';
			$data['div_name'] = 'pmtct_outcomes_normal';
		} else if ($type == 2) {
			$data['type'] = 'percent';
			$data['div_name'] = 'pmtct_outcomes_percent';
		}
		//$this->load->view('pmtct_view',$data);
	}

	public function pmtct_suppression($year=null,$month=null,$pmtcttype=null,$to_year=null,$to_month=null,$partner=null,$national=null,$county=null,$subcounty=null,$site=null)
	{
		$data['trends'] = $this->pmtct_model->suppression($year,$month,$pmtcttype,$to_year,$to_month,$partner,$national,$county,$subcounty,$site);
		$data['div_name'] = "pmtct_suppression";		

		//$this->load->view('trends_outcomes_view', $data);
	}
	public function pmtct_vl_outcomes($year=null,$month=null,$pmtcttype=null,$to_year=null,$to_month=null,$partner=null,$national=null,$county=null,$subcounty=null,$site=null)
	{
		$data['outcomes'] = $this->pmtct_model->vl_outcomes($year,$month,$pmtcttype,$to_year,$to_month,$partner,$national,$county,$subcounty,$site);

		//$this->load->view('vl_outcomes_view', $data);
	}
	public function pmtct_breakdown($year=null,$month=null,$pmtcttype=null,$to_year=null,$to_month=null,$county=null,$sub_county=null,$partner=null,$site=null)
	{
		$data['outcomes'] = $this->pmtct_model->breakdown($year,$month,$pmtcttype,$to_year,$to_month,$county,$sub_county,$partner,$site);
		
		//$this->load->view('age_breakdown_listing',$data);
	}
	public function pmtct($year=null,$month=null,$pmtcttype=null,$to_year=null,$to_month=null,$county=null,$sub_county=null,$partner=null)
	{
		$data['trends'] = $this->pmtct_model->pmtct($year,$month,$pmtcttype,$to_year,$to_month,$county,$sub_county,$partner);
		$data['div_name'] = "pmtct_outcomes_pmtct";		

		//$this->load->view('trends_outcomes_view', $data);
	}
}
?>
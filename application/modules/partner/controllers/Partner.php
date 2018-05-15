<?php
defined("BASEPATH") or exit();

/**
* 
*/
class Partner extends MY_Controller
{
	public $data = array();

	function __construct()
	{
		parent:: __construct();
		$this->data	=	array_merge($this->data,$this->load_libraries(array('material','highstock','highmaps','highcharts','custom','tablecloth','select2')));
		$this->session->unset_userdata('county_filter');
		$this->data['part'] = TRUE;
	}

	public function index()
	{
		$this->load->module('charts/summaries');
		$this->clear_all_session_data();
		$this->data['content_view'] = 'partner/partner_summary_view';
		$this -> template($this->data);
	}

	public function trends()
	{
		$this->load->module('charts/partner_trends');
		$this->clear_all_session_data();
		$this->data['content_view'] = 'partner/partner_trends_view';
		$this->template($this->data);
	}

	public function age()
	{
		$this->load->module('charts/ages');
		$this->clear_all_session_data();
		$this->data['content_view'] = 'partner/partner_age_view';
		$this->template($this->data);
	}

	public function regimen()
	{
		$this->load->module('charts/regimen');
		$this->clear_all_session_data();
		$this->data['content_view'] = 'partner/partner_regimen_view';
		$this->template($this->data);
	}

	public function nosuppression()
	{
		// echo "<pre>";print_r($this->session->all_userdata());die();
		$this->load->module('charts/nonsuppression');
		$this->clear_all_session_data();
		$this->data['content_view'] = 'partner/partner_no_suppression_view';
		$this -> template($this->data);
	}

	public function counties()
	{
		$this->clear_all_session_data();
		$this->session->unset_userdata('partner_filter');
		$this->load->module('charts/partner_summaries');

		$this->data['content_view'] = 'partner/partner_counties_view';
		// echo "<pre>";print_r($this->data);die();
		$this -> template($this->data);
	}

	public function sites()
	{
		$this->load->module('charts/sites');
		$this->clear_all_session_data();
		$this->data['content_view'] = 'partner/partner_sites_view';
		// echo "<pre>";print_r($this->data);die();
		$this -> template($this->data);
	}

	public function current()
	{
		$this->load->module('charts/summaries');
		$this->clear_all_session_data();
		$this->data['content_view'] = 'partner/partner_current_view';
		$this -> template($this->data);
	}

	public function annual()
	{
		$this->load->module('charts/summaries');
		$this->clear_all_session_data();
		$this->data['content_view'] = 'partner/partner_annual_view';
		$this -> template($this->data);
	}

	public function pmtct()
	{
		$this->load->module('charts/summaries');
		$this->load->module('charts/pmtct');
		$this->clear_all_session_data();
		$this->data['content_view'] = 'partner/partner_pmtct_view';
		// echo "<pre>";print_r($this->data);die();
		$this -> template($this->data);
	}

	public function get_selected_partner()
	{
		if ($this->session->userdata('partner_filter')) {
			$partner = $this->session->userdata('partner_filter');
		} else {
			$partner = 1;
		}
		 echo $partner;
	}

	public function check_partner_select()
	{
		if ($this->session->userdata('partner_filter')) {
			$partner = $this->session->userdata('partner_filter');
		} else {
			$partner = 0;
		}
		echo json_encode($partner);
	}

	public function check_partner_age_select()
	{
		if ($this->session->userdata('patner_age_category_filter')) {
			$partner_age = $this->session->userdata('patner_age_category_filter');
		} else {
			$partner_age = 0;
		}
		echo json_encode($partner_age);
	}

	public function check_partner_regimen_select()
	{
		if ($this->session->userdata('patner_regimen_filter')) {
			$partner_regimen = $this->session->userdata('patner_regimen_filter');
		} else {
			$partner_regimen = 0;
		}
		echo json_encode($partner_regimen);
	}
}
?>
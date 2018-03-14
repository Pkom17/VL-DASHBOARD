<?php
defined('BASEPATH') or exit('No direct script access allowed!');

/**
* 
*/
class Sites extends MY_Controller
{
	public $data = array();

	function __construct()
	{
		parent:: __construct();
		$this->data	=	array_merge($this->data,$this->load_libraries(array('material','highstock','highmaps','highcharts','custom','select2','tablecloth')));
		$this->data['sit'] = TRUE;
		$this->load->module('charts/sites');
		$this->load->module('charts/pmtct');
	}

	function index()
	{
		$this->clear_all_session_data();
		$this->data['content_view'] = 'sites/sites_view';
		$this -> template($this->data);
	}
	
	public function pmtct()
	{
		$this->load->module('charts/sites');
		$this->load->module('charts/pmtct');
		$this->clear_all_session_data();
		$this->data['content_view'] = 'sites/sites_pmtct_view';
		// echo "<pre>";print_r($this->data);die();
		$this -> template($this->data);
	}

	public function check_site_select()
	{
		if ($this->session->userdata('site_filter')) {
			$site = $this->session->userdata('site_filter');
		} else {
			$site = 0;
		}
		echo json_decode($site);
		// echo json_encode($this->session->userdata('site_filter'));
	}
}
?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Counties extends MY_Controller {

	public $data = array();

	function __construct()
	{
		parent:: __construct();
		$this->data	=	array_merge($this->data,$this->load_libraries(array('material','highstock','highmaps','highcharts','custom','tablecloth','select2')));
		$this->session->set_userdata('partner_filter', NULL);
		$this->load->module('charts/counties');
		$this->data['cout'] = TRUE;
	}

	public function index()
	{
		// echo $_SERVER['SERVER_PORT'],"<___>".base_url();die();
            $this->clear_all_session_data();
		$this->data['content_view'] = 'counties/county';
		// echo "<pre>";print_r($this->data);die();
		$this -> template($this->data);
	}
}
<?php
defined("BASEPATH") or exit("No direct script access allowed!");

/**
* 
*/
class Baseline extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->data	=	array_merge($this->data,$this->load_libraries(array('material','highstock','highmaps','highcharts','custom','tablecloth','select2')));
		$this->session->unset_userdata('county_filter');
		$this->data['no_filter'] = TRUE;
		$this->load->module('charts/baseline');
	}

	function index()
	{
		$this->clear_all_session_data();
		$this->data['content_view'] = 'baseline/baseline_view';
		// echo "<pre>";print_r($t`his->data);die();
		$this -> template($this->data);
	}

}
?>
<?php
defined('BASEPATH') or exit('No direct script access');

/**
* 
*/
class Regimen extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->data	=	array_merge($this->data,$this->load_libraries(array('material','highstock','highmaps','highcharts','custom','tablecloth','select2')));
		$this->data['reg'] = TRUE;
		$this->load->module('charts/regimen');
	}

	function index()
	{
		$this->clear_all_session_data();
		$this->data['content_view'] = 'regimen/regimen_view';
		// echo "<pre>";print_r($this->data);die();
		$this -> template($this->data);
	}

	function check_regimen_select()
	{
		if ($this->session->userdata('regimen_filter')) {
			$regimen = $this->session->userdata('regimen_filter');
		} else {
			$regimen = 0;
		}
		echo json_encode($regimen);
	}
}
?>
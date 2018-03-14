<?php
defined('BASEPATH') or exit('No direct script access');

/**
* 
*/
class Sample extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->data	=	array_merge($this->data,$this->load_libraries(array('material','highstock','highmaps','highcharts','custom','tablecloth','select2')));
		$this->data['sample'] = TRUE;
		$this->load->module('charts/samples');
	}

	function index()
	{
		$this->clear_all_session_data();
		$this->data['content_view'] = 'sample/sample_view';
		// echo "<pre>";print_r($this->data);die();
		$this -> template($this->data);
	}

	function check_sample_select()
	{
		if ($this->session->userdata('sample_filter')) {
			$sample = $this->session->userdata('sample_filter');
		} else {
			$sample = 0;
		}
		echo json_encode($sample);
	}
}
?>
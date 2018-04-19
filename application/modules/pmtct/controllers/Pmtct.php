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
		$this->data	=	array_merge($this->data,$this->load_libraries(array('material','highstock','highmaps','highcharts','custom','tablecloth','select2')));
		$this->data['pmtct'] = TRUE;
		$this->load->module('charts/pmtct');
	}

	public function index()
	{
		$this->clear_all_session_data();
		$this->data['content_view'] = 'pmtct/pmtct_view';
		// echo "<pre>";print_r($this->data);die();
		$this -> template($this->data);
	}

	public function check_pmtct_select()
	{
		if ($this->session->userdata('pmtct_filter')) {
			$pmtct = $this->session->userdata('pmtct_filter');
		} else {
			$pmtct = 0;
		}
		echo json_encode($pmtct);
	}
}
?>
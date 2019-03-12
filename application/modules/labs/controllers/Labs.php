<?php
defined("BASEPATH") or exit("No direct script access allowed!");
/**
* 
*/
class Labs extends MY_Controller
{
	public $data = array();

	function __construct()
	{
		parent:: __construct();
		$this->data	=	array_merge($this->data,$this->load_libraries(array('material','highstock','highmaps','highcharts','custom', 'tablecloth','select2')));
		$this->load->module('charts/labs');
	}

	public function index()
	{
		$this->data['labs'] = TRUE;
                $this->clear_all_session_data();
		$this->data['content_view'] = 'labs/labs_summary_view';
		$this -> template($this->data);
	}
}
?>
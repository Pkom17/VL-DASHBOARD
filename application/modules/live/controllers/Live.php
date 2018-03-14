<?php
defined('BASEPATH') or die('No direct script access allowed');
/**
* 
*/
class Live extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->data	=	array_merge($this->data,$this->load_libraries(array('material','highstock','highmaps','highcharts','custom','tablecloth','select2')));
		$this->data['live'] = TRUE;
		$this->load->module('charts/live');
	}

	function index()
	{
		$this->data['content_view'] = 'live/live_view';
		// echo "<pre>";print_r($this->data);die();
		$this -> template($this->data);
	}
}
?>
<?php
defined('BASEPATH') or exit('No direct script access allowed!');

/**
* 
*/
class Shortcodes extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->data	=	array_merge($this->data,$this->load_libraries(array('material','highstock','highmaps','highcharts','custom','tablecloth','select2')));
		$this->data['codes'] = TRUE;
		$this->load->module('charts/shortcodes');
	}

	function index(){
		$this->data['content_view'] = 'shortcodes/shortcodes_view';
		// echo "<pre>";print_r($this->data);die();
		$this -> template($this->data);
	}
}
?>
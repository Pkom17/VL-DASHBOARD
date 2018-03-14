<?php
defined("BASEPATH") or exit();
/**
* 
*/
class Live extends MY_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('live_model');
	}

	function get_data($type=2, $lab=1){
		echo $this->live_model->get_data($type, $lab);
	}

	function get_dropdown(){
		echo $this->live_model->get_dropdown();
	}

	function get_vl_data(){
		echo $this->live_model->get_vl_data();
	}

	function get_eid_data(){
		echo $this->live_model->get_eid_data();
	}

	
}
?>
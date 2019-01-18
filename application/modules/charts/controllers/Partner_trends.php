<?php
defined("BASEPATH") or exit("No direct script access allowed!");

/**
* 
*/
class Partner_trends extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('partner_trends_model');
	}

	function positive_trends($partner=NULL){
		$obj = $this->partner_trends_model->yearly_trends($partner);
		// echo "<pre>";print_r($obj);echo "</pre>";die();

		$data['trends'] = $obj['suppression_trends'];
		$data['title'] = lang('label.suppression_trends');
		$data['div_name'] = "suppression";
		$data['suffix'] = "%";
		$data['yAxis'] = lang('label.suppression_rate_percent');
		$this->load->view('yearly_trends_view', $data);

		$data['trends'] = $obj['test_trends'];
		$data['title'] = lang('label.testing_trends');
		$data['div_name'] = "tests";
		$data['suffix'] = "";
		$data['yAxis'] =  lang('label.number_valid_tests');
		$this->load->view('yearly_trends_view', $data);

		


		$data['trends'] = $obj['tat_trends'];
		$data['title'] = lang('label.collection_dispatch');
		$data['div_name'] = "tat";
		$data['suffix'] = "";
		$data['yAxis'] = lang('label.TAT_days');
		$this->load->view('yearly_trends_view', $data);

		

		//echo json_encode($obj);
		//echo "<pr>";print_r($obj);die;

	}

	function summary($partner=NULL){
		$data['trends'] = $this->partner_trends_model->yearly_summary($partner);
		$data['div_name'] = "partner_trends";
		//$data['trends'] = $this->positivity_model->yearly_summary();
		//echo json_encode($data);
		// echo "<pre>";print_r($data);die();
		$this->load->view('trends_outcomes_view', $data);
	}


}
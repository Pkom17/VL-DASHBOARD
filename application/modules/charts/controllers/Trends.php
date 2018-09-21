<?php
defined("BASEPATH") or exit("No direct script access allowed!");

/**
* 
*/
class Trends extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('trends_model');
	}

	function positive_trends($county=NULL){
		$obj = $this->trends_model->yearly_trends($county);
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
		$data['yAxis'] = lang('label.number_valid_tests');
		$this->load->view('yearly_trends_view', $data);

		
//
//		$data['trends'] = $obj['rejected_trends'];
//		$data['title'] = lang('label.rejected_rate_trends');
//		$data['div_name'] = "rejects";
//		$data['suffix'] = "%";
//		$data['yAxis'] = lang('label.rejection_percent');
//		$this->load->view('yearly_trends_view', $data);


		$data['trends'] = $obj['tat_trends'];
		$data['title'] = lang('label.turn_around_time_cd');
		$data['div_name'] = "tat";
		$data['suffix'] = "";
		$data['yAxis'] =  lang('label.TAT_days');
		$this->load->view('yearly_trends_view', $data);

		

		//echo json_encode($obj);
		//echo "<pr>";print_r($obj);die;

	}

	function summary($county=NULL){
		$data['trends'] = $this->trends_model->yearly_summary($county);
		$data['div_name'] = "national_trends";
		//$data['trends'] = $this->positivity_model->yearly_summary();
		//echo json_encode($data);
		// echo "<pre>";print_r($data);die();
		$this->load->view('trends_outcomes_view', $data);
	}

	function quarterly($county=NULL){
		$obj = $this->trends_model->quarterly_trends($county);
		// echo "<pre>";print_r($obj);echo "</pre>";die();

		$data['trends'] = $obj['suppression_trends'];
		$data['title'] = lang('label.suppression_trends');
		$data['div_name'] = "suppression_q";
		$data['suffix'] = "%";
		$data['yAxis'] = lang('label.suppression_rate_percent');
		$this->load->view('quarterly_trends_view', $data);

		$data['trends'] = $obj['test_trends'];
		$data['title'] =  lang('label.testing_trends');
		$data['div_name'] = "tests_q";
		$data['suffix'] = "";
		$data['yAxis'] = lang('label.number_valid_tests');
		$this->load->view('quarterly_trends_view', $data);

//		$data['trends'] = $obj['rejected_trends'];
//		$data['title'] =  lang('label.rejected_rate_trends');
//		$data['div_name'] = "rejects_q";
//		$data['suffix'] = "%";
//		$data['yAxis'] =lang('label.rejection_percent');
//		$this->load->view('quarterly_trends_view', $data);


		$data['trends'] = $obj['tat_trends'];
		$data['title'] =  lang('label.turn_around_time_cd');
		$data['div_name'] = "tat_q";
		$data['suffix'] = "";
		$data['yAxis'] =  lang('label.TAT_days');
		$this->load->view('quarterly_trends_view', $data);
	}

	function quarterly_outcomes($county=NULL){
		$data['trends'] = $this->trends_model->quarterly_outcomes($county);
		// echo "<pre>";print_r($data);echo "</pre>";die();
		$data['div_name'] = "quarterly_trends";
		$this->load->view('trends_outcomes_view', $data);
	}


}
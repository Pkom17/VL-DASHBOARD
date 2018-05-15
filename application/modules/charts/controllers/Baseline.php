<?php
defined("BASEPATH") or exit();
/**
* 
*/
class Baseline extends MY_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('baseline_model');
	}

	function notification($param_type=NULL,$param=NULL,$year=NULL,$month=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['suppressions']= $this->baseline_model->notification($param_type,$param,$year,$month,$to_year,$to_month);
		$this->load->view('sup_notification_view', $data);
	}

	function age($param_type=NULL,$param=NULL,$year=NULL,$month=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes']= $this->baseline_model->age($param_type,$param,$year,$month,$to_year,$to_month);
		$this->load->view('agegroup_view', $data);
	}

	function gender($param_type=NULL,$param=NULL,$year=NULL,$month=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes']= $this->baseline_model->gender($param_type,$param,$year,$month,$to_year,$to_month);
		$this->load->view('gender_view', $data);
	}

	function samples($param_type=NULL,$param=NULL,$year=NULL,$month=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes']= $this->baseline_model->samples($param_type,$param,$year,$month,$to_year,$to_month);
		$data['div'] = "random_div";
		$data['type'] = "normal";
		$this->load->view('county_outcomes_view', $data);
	}



	function baseline_list($param_type=NULL,$year=NULL,$month=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['trends'] = $this->baseline_model->baseline_list($param_type,$year,$month,$to_year,$to_month);

		$data['div_name'] = $data['trends']['div_name'];

		$this->load->view('trends_outcomes_view', $data);
	}



	


}
?>
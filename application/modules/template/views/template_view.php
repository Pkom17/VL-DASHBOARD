<?php
	ob_start();
	$this->load->view('header_view');
	// echo "<pre>";print_r($labs);print_r($part);print_r($sit);print_r($county);print_r($regimen);print_r($age);die();
	if ($labs) {
		$this->load->view('utils/date_filter_view');
	}else if ($part) {
		$this->load->view('utils/partner_filter_view');
	}else if ($sit) {
		$this->load->view('utils/site_filter_view');
	}else if ($cout) {
		$this->load->view('utils/filter_view');
	}else if ($reg) {
		$this->load->view('utils/regimen_filter_view');
	}else if ($age) {
		$this->load->view('utils/age_filter_view');
	} else if ($sub_county){
		$this->load->view('utils/sub_county_filter_view');
	} else if ($contacts) {

	}else if ($live){

	}else if (isset($no_filter)){
		$this->load->view('utils/no_filter_view');
	}else if ($sample) {
		$this->load->view('utils/sample_filter_view');
	}else if ($codes){
		$this->load->view('utils/filter_view');
	}else if ($pmtct){
		$this->load->view('utils/regimen_filter_view');
	}else {
		$this->load->view('utils/filter_view');
	}
	
	$this->load->view($content_view);
	$this->load->view('footer_view');
?>
<?php
defined('BASEPATH') or exit('No direct path access allowed!');

/**
* 
*/
class Shortcodes extends MY_Controller
{
	
	function __construct()
	{
		parent:: __construct();
		$this->load->model('shortcodes_model');
	}

	function request_trends($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes'] = $this->shortcodes_model->request_trends($year,$month,$county,$to_year,$to_month);

		$this->load->view('shortcodes_request_trends_view',$data);
	}
	
	function counties($year=NULL,$month=NULL,$pfil=NULL,$partner=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['countys'] = $this->shortcodes_model->county_listings($year,$month,$county,$to_year,$to_month);

    	$this->load->view('county_listings',$data);
	}

	function subcounties($year=NULL,$month=NULL,$county=NULL,$partner=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['subCounty'] = $this->shortcodes_model->subcounty_listings($year,$month,$county,$to_year,$to_month);

    	$this->load->view('sup_subcounty_listing',$data);
	}
	

	function facilities($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['facilities'] = $this->shortcodes_model->facility_listing($year,$month,$county,$to_year,$to_month);

    	$this->load->view('site_listings',$data);
	}

	function partner($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['partners'] = $this->shortcodes_model->partners($year,$month,$county,$to_year,$to_month);
		
		$this->load->view('sup_partner_listing',$data);
	}

	function facilities_requesting($year=NULL,$month=NULL,$county=NULL,$to_year=NULL,$to_month=NULL)
	{
		$data['outcomes'] = $this->shortcodes_model->facilities_requesting($year,$month,$county,$to_year,$to_month);
		
    	$this->load->view('shortcodes_facilities_view',$data);
	}
}

?>
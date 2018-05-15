<style type="text/css">
	@import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700");
	@import url("https://fonts.googleapis.com/css?family=Roboto:400,300,500,700");
	/*
	 *
	 *   INSPINIA - Responsive Admin Theme
	 *   version 2.6.2
	 *
	*/
	h1,
	h2,
	h3,
	h4,
	h5,
	h6 {
	  font-weight: 100;
	}
	h1 {
	  font-size: 30px;
	}
	h2 {
	  font-size: 24px;
	}
	h3 {
	  font-size: 16px;
	}
	h4 {
	  font-size: 14px;
	}
	h5 {
	  font-size: 12px;
	}
	h6 {
	  font-size: 10px;
	}
	h3,
	h4,
	h5 {
	  margin-top: 5px;
	  font-weight: 600;
	}
</style>

<!-- <div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="ibox-title">
		            <h5>Date: <span id="time_now"></span></h5>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-md-4">
		<div id="breadcrum" class="alert" style="background-color: #1BA39C;text-align: center;vertical-align: middle;">
      		<span id="current_source">Viralload</span> (Click to switch)	
    	</div>
		     
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="ibox-title">
		            <h5>Last Update: <span id="last_updated"></span></h5>
		        </div>
		    </div>
		</div>
	</div>
</div> -->

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"> <?=lang('label.requests_trends')?> </h3>
			</div>
			<div class="panel-body">
				<div id="requests"></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.pmtct_counties')?> </h3>
			</div>
			<div class="panel-body">
				<div id="counties"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"> <?=lang('label.pmtct_sub.counties')?></h3>
			</div>
			<div class="panel-body">
				<div id="subcounties"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"> <?=lang('label.current_facilities')?></h3>
			</div>
			<div class="panel-body">
				<div id="facilities"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.current_partners')?></h3>
			</div>
			<div class="panel-body">
				<div id="partners"></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.shortcodes_facilities.requesting')?></h3>
			</div>
			<div class="panel-body">
				<div id="facilities_requesting"></div>
			</div>
		</div>
	</div>
</div>
<?= $this->load->view('shortcodes_view_footer'); ?>
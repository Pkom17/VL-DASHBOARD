<div class="row">
		<!-- Map of the country -->
	
</div>

<div id="first">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  <?=lang('label.counties_outcomes')?> <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="county">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				<?=lang('label.counties_cap')?> <div class="display_date"></div>
				</div>
			  	<div class="panel-body" id="county_sites">
			  		<center><div class="loader"></div></center>
			  	</div>
			</div>
		</div>
	</div>
</div>

<div id="second">
	<div class="row">
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading" id="heading">
			  <?=lang('label.sub-counties_outcomes')?> <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="subcounty">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading" id="heading">
			  <?=lang('label.sub-counties_suppression')?> <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="subcountypos">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				  <?=lang('label.sub-counties')?><div class="display_date"></div>
				</div>
			  	<div class="panel-body" id="sub_counties">
			  		<center><div class="loader"></div></center>
			  	</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				  <?=lang('label.partners')?><div class="display_date"></div>
				</div>
			  	<div class="panel-body" id="partners">
			  		<center><div class="loader"></div></center>
			  	</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				  <?=lang('label.facilities_PMTCT')?> <div class="display_date"></div>
				</div>
			  	<div class="panel-body" id="facilities_pmtct">
			  		<center><div class="loader"></div></center>
			  	</div>
			</div>
		</div>
	</div>
</div>



<?php $this->load->view('county_view_footer'); ?>
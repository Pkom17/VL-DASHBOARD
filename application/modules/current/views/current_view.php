
<style type="text/css">
	.navbar-inverse {
		border-radius: 0px;
	}
	.navbar .container-fluid .navbar-header .navbar-collapse .collapse .navbar-responsive-collapse .nav .navbar-nav {
		border-radius: 0px;
	}
	.panel {
		border-radius: 0px;
	}
	.panel-primary {
		border-radius: 0px;
	}
	.panel-heading {
		border-radius: 0px;
	}
	.list-group-item{
		margin-bottom: 0.1em;
	}
	.btn {
		margin: 0px;
	}
	.alert {
		margin-bottom: 0px;
		padding: 8px;
	}
	.filter {
		margin: 2px 20px;
	}
	.display_date {
		width: 130px;
		display: inline;
	}
	.display_range {
		width: 130px;
		display: inline;
	}
	#filter {
		background-color: white;
		margin-bottom: 1.2em;
		margin-right: 0.1em;
		margin-left: 0.1em;
		padding-top: 0.5em;
		padding-bottom: 0.5em;
	}
	#year-month-filter {
		font-size: 12px;
	}

</style>

<div class="row">
	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		   <?=lang('label.tests_done_by_unique_patients')?><div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="long_tracking">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>

	<div class="col-md-6 col-sm-6 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		   <?=lang('label.suppression_rate')?><div class="display_current_range"></div>
		  </div>
		  <div class="panel-body" id="current_sup">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>

<div class="row">
	<center><h3><?=lang('label.current_suppression_rates')?> <div class="display_current_range"></div></h3></center>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_counties')?>
			</div>
		  	<div class="panel-body">
		  	<div id="countys">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_sub.counties')?> 
			</div>
		  	<div class="panel-body">
		  	<div id="subcounty">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_facilities')?> 
			</div>
		  	<div class="panel-body">
		  	<div id="facilities">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_partners')?>
			</div>
		  	<div class="panel-body">
		  	<div id="partners">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
</div>

<div class="row">
	<center><h3><?=lang('label.current_sup_supped_age.data')?><div class="display_current_range"></div></h3></center>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_counties')?>
			</div>
		  	<div class="panel-body">
		  	<div id="countys_a">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_sub.counties')?>
			</div>
		  	<div class="panel-body">
		  	<div id="subcounty_a">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_sub.facilities')?>
			</div>
		  	<div class="panel-body">
		  	<div id="facilities_a">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_partners')?>
			</div>
		  	<div class="panel-body">
		  	<div id="partners_a">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
</div>

<div class="row">
	<center><h3><?=lang('label.current_sup_supped_age.data')?><div class="display_current_range"></div></h3></center>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_counties')?>
			</div>
		  	<div class="panel-body">
		  	<div id="countys_na">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			 <?=lang('label.current_subcounties')?> 
			</div>
		  	<div class="panel-body">
		  	<div id="subcounty_na">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_facilities')?>
			</div>
		  	<div class="panel-body">
		  	<div id="facilities_na">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_partners')?>
			</div>
		  	<div class="panel-body">
		  	<div id="partners_na">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
</div>

<div class="row">
	<center><h3><?=lang('label.current_sup_gender.data')?><div class="display_current_range"></div></h3></center>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_counties')?>
			</div>
		  	<div class="panel-body">
		  	<div id="countys_g">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_subcounties')?>
			</div>
		  	<div class="panel-body">
		  	<div id="subcounty_g">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_facilities')?>
			</div>
		  	<div class="panel-body">
		  	<div id="facilities_g">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			  <?=lang('label.current_partners')?>
			</div>
		  	<div class="panel-body">
		  	<div id="partners_g">
		  		<div><?=lang('label.loading')?></div>
		  	</div>
		  	<!-- -->
		  </div>
		</div>
	</div>
</div>

<?php $this->load->view('current_view_footer'); ?>
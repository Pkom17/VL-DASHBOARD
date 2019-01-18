
<style type="text/css">
	.navbar-inverse {
		border-radius: 0px;
	}
	.navbar .container-fluid .navbar-header .navbar-collapse .collapse .navbar-responsive-collapse .nav .navbar-nav {
		border-radius: 0px;
	}
	.panel {
		border-radius: 0px;
        }.list-group{
            overflow: auto;
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
	<div class="col-md-4 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading chart_title">
		    <?=lang('label.suppression_rate')?> <div class="display_current_range"></div>
		  </div>
		  <div class="panel-body" id="current_sup">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>

	<div class="col-md-4 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading chart_title">
		    <?=lang('label.current_by_gender')?> <div class="display_current_range"></div>
		  </div>
		  <div class="panel-body" id="current_sup_gender">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>

	<div class="col-md-4 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading chart_title">
		    <?=lang('label.current_by_age')?> <div class="display_current_range"></div>
		  </div>
		  <div class="panel-body" id="current_sup_age">
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
			<div class="panel-heading chart_title">
			  <?=lang('label.counties')?>
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
			<div class="panel-heading chart_title">
			  <?=lang('label.sub_counties')?>
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
			<div class="panel-heading chart_title">
			  <?=lang('label.facilities')?>
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
			<div class="panel-heading chart_title">
			  <?=lang('label.partners')?>
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
	<center><h3><?=lang('label.current_suppression_data')?> <div class="display_current_range"></div></h3></center>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading chart_title">
			  <?=lang('label.counties')?>
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
			<div class="panel-heading chart_title">
			  <?=lang('label.sub_counties')?>
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
			<div class="panel-heading chart_title">
			  <?=lang('label.facilities')?>
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
			<div class="panel-heading chart_title">
			  <?=lang('label.partners')?>
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
	<center><h3><?=lang('label.current_non_suppression_data')?> <div class="display_current_range"></div></h3></center>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading chart_title">
			  <?=lang('label.counties')?>
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
			<div class="panel-heading chart_title">
			  <?=lang('label.sub_counties')?> 
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
			<div class="panel-heading chart_title">
			  <?=lang('label.facilities')?> 
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
			<div class="panel-heading chart_title">
			  <?=lang('label.partners')?>
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
	<center><h3><?=lang('label.current_gender_data')?> <div class="display_current_range"></div></h3></center>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading chart_title">
			  <?=lang('label.counties')?>
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
			<div class="panel-heading chart_title">
			  <?=lang('label.sub_counties')?>
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
			<div class="panel-heading chart_title">
			  <?=lang('label.facilities')?>
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
			<div class="panel-heading chart_title">
			  <?=lang('label.partners')?>
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

<?php $this->load->view('partner_current_view_footer'); ?>
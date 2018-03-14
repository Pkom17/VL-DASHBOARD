<style type="text/css">
	.display_date {
		width: 130px;
		display: inline;
	}
	.display_range {
		width: 130px;
		display: inline;
	}
	#age_dropdown{
		margin-bottom: 1em;
	}
</style>
<center>
	<div id="age_dropdown">
		<select class="btn btn-primary js-example-basic-single" style="background-color: #C5EFF7;" id="site_pmtct" name="site_pmtct">
	        <option value="0" disabled="true" selected="true"><?=lang('label.site_select.pmtct')?></option>
            <option value="NA"><?=lang('label.site_all.pmtct')?></option>
            <!-- <optgroup value="Counties"> -->
            <?php echo $pmtcts; ?>
	        <!-- </optgroup> -->
	      </select>
	</div>
	<!-- <div> All Age Categories </div> -->
</center>
<div class="row" id="first">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.site_outcomes_routine.vl')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="partner_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>
<div class="row" id="second">
	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.site_pmtctoutcomes_routine.vl')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="pmtct_outcomes_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.site_pmtct_sup.outcomes_routine.vl')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="pmtct_sup_outcomes_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>
<div class="row" id="third">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.site_pmtctsup_routine.vl')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="pmtct_suppression_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.pmtct_vl.outcomes')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="pmtct_vl_outcomes_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	<!-- <div class="col-md-4 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	Counties <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="pmtct_counties_listing_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	<div class="col-md-4 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	Partners <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="pmtct_partners_listing_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	<div class="col-md-4 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	Sites <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="pmtct_sites_listing_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div> -->
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.site_pmtctoutcomes_routine.vl')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="pmtct_counties_outcomes_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>
<div class="row" id="fourth">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.site_pmtctsup_routine.vl')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="pmtct_suppression_all_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.site_vl.outcomes')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="pmtct_vl_outcomes_all_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>
<?= @$this->load->view('sites/sites_pmtct_footer_view');?>
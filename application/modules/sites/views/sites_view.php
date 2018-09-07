<style type="text/css">
	.display_date {
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

<div id="first">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				  <?=lang('label.facilities_outcomes')?> <div class="display_date"></div>
				</div>
				<div class="panel-body" id="siteOutcomes">
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
			  <div class="panel-heading">
			    <?=lang('label.site_trends')?> <div class="display_range"></div>
			  </div>
			  <div class="panel-body" id="tsttrends">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <?=lang('label.site_outcomes')?> <div class="display_range"></div>
			  </div>
			  <div class="panel-body" id="stoutcomes">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>
	<div class="row">
		<!-- Map of the country -->
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?=lang('label.vl_outcomes')?> <div class="display_date" ></div>
			  </div>
			  <div id="vlOutcomes">
			  	<center><div class="loader"></div></center>
			  </div>
			  
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <?=lang('label.site_routinevls_outcomes.byage')?> <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="ageGroups">
			    <center><div class="loader"></div></center>
			  </div>
			  <!-- <div>
			  	<button class="btn btn-default" onclick="ageModal();"><?=lang('label.modal.click_breakdown');?></button>
			  </div> -->
			</div>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <?=lang('label.site_routinevls_outcomes.bygender')?> <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="gender" style="padding-bottom:0px;">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>
		
		<!-- Map of the country -->
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default">
			  <div class="panel-heading">
				  <?=lang('label.site_justification_for_tests')?> <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="justification" >
			    <center><div class="loader"></div></center>
			  </div>
			  <div>
                              <center><button class="btn btn-default" onclick="justificationModal();" style="background-color: #2f80d1;color: white;margin-bottom: 1em;"><?=lang('label.modal.click_breakdown');?></button></center>
			  </div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel panel-default">
			  <div class="panel-heading">
				  <?=lang('label.site_suppression.rate')?> <div class="display_current_range"></div>
			  </div>
			  <div class="panel-body" id="current_sup" >
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
				  <?=lang('label.site_testsdone_unique.patients')?> <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="long_tracking" >
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>

	</div>
	<div class="row" style="display: none;">
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <?=lang('label.site_longitudinalpatient_trackg.statistics')?> <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="pat_stats">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="panel panel-default">
					  <div class="panel-heading">
					    <?=lang('label.site_patients.outcomes')?> <div class="display_date"></div>
					  </div>
					  <div class="panel-body" id="pat_out">
					    <center><div class="loader"></div></center>
					  </div>
					</div>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="panel panel-default">
					  <div class="panel-heading">
					    <?=lang('label.site_patients.graphs')?> <div class="display_date"></div>
					  </div>
					  <div class="panel-body" id="pat_graph">
					    <center><div class="loader"></div></center>
					  </div>
					</div>
				</div>
			</div>
		</div>	
	</div>

</div>

<div class="modal fade" tabindex="-1" role="dialog" id="agemodal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?=lang('label.modal_close')?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=lang('label.site_age_category.breakdown')?> </h4>
      </div>
      <div class="modal-body" id="CatAge">
        <center><div class="loader"></div></center>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="justificationmodal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=lang('label.site_pregnant_lactating.mothers')?></h4>
      </div>
      <div class="modal-body" id="CatJust">
        <center><div class="loader"></div></center>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('sites_view_footer')?>
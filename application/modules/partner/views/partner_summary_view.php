<style type="text/css">
	.display_date {
		width: 130px;
		display: inline;
	}
	.display_range {
		width: 130px;
		display: inline;
	}
</style>

<div class="row" id="third">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading" style="min-height: 4em;">
		  	<div class="col-sm-3">
		  		<div id="samples_heading"><?=lang('label.testing_trends_for_routine_VL')?></div> <div class="display_range"></div>
		  	</div>
		    <div class="col-sm-3">
		    	<input type="submit" class="btn btn-primary" id="switchButton" onclick="switch_source()" value="Click to Switch to All Tests">
		    </div>
		  </div>
		  <div class="panel-body" id="samples">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>
<div class="row" id="second">
	<!-- Map of the country -->
		<div class="col-md-7 col-sm-7 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?=lang('label.vl_outcomes')?> <div class="display_date" ></div>
			  </div>
			  <div id="vlOutcomes">
			  	<center><div class="loader"></div></center>
			  </div>
			  
			</div>
		</div>
		<div class="col-md-5">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <?=lang('label.routine_VL_outcomes_gender')?><div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="gender">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>

	<!-- Map of the country -->
		<div class="col-md-6">
			<div class="panel panel-default">
					  <div class="panel-heading">
					    <?=lang('label.routine_by_age')?> <div class="display_date"></div>
					  </div>
					  <div class="panel-body" id="ageGroups">
					    <center><div class="loader"></div></center>
					  </div>
					  <div>
					  	<center><button class="btn btn-default" onclick="ageModal();" style="background-color: #2f80d1;color: white; margin-top: 1em;margin-bottom: 1em;"><?=lang('label.age_category_breakdown')?></button></center>
					  </div>
					</div>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="panel panel-default">
			  	<div class="panel-heading">
				  	<?=lang('label.justification_for_tests')?> <div class="display_date"></div>
			  	</div>
				<div class="panel-body" id="justification">
				    <center><div class="loader"></div></center>
				</div>
			  	<div>
			  		<center><button class="btn btn-default" onclick="justificationModal();" style="background-color: #2f80d1;color: white; margin-top: 1em;margin-bottom: 1em;"><?=lang('label.age_category_breakdown')?></button></center>
			  	</div>
			</div>
		</div>

	<div class="col-md-12">
		<!--<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    &nbsp;&nbsp;&nbsp;&nbsp; <?=lang('label.tests_done_by_unique_patients')?><div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="long_tracking">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div> -->

		<div class="col-md-6 col-sm-6 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <?=lang('label.suppression_rate')?> <div class="display_current_range"></div>
			  </div>
			  <div class="panel-body" id="current_sup">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>	
</div>

<div class="row" id="first">
	<!-- Map of the country -->
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.partner_outcomes')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="partner_div">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="agemodal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?=lang('label.modal_close')?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=lang('label.age_category_breakdown')?></h4>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="<?=lang('label.modal_close')?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=lang('label.pregnant_and_lactating_mothers')?></h4>
      </div>
      <div class="modal-body" id="CatJust">
        <center><div class="loader"></div></center>
      </div>
    </div>
  </div>
</div>
		
<?php $this->load->view('partner_summary_view_footer'); ?>
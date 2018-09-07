<style type="text/css">
	.display_date {
		width: 130px;
		display: inline;
	}
	.display_range {
		width: 130px;
		display: inline;
	}
	#nattatdiv {
		background-color: white;
		margin-right: 1em;
		margin-left: 1em;
		margin-bottom: 1em;
	}
	.title-name {
		color: blue;
	}
	#title {
		padding-top: 1.5em;
	}
	.key {
		font-size: 11px;
		margin-top: 0.5em;
	}
	.cr {
		background-color: rgba(255, 0, 0, 0.498039);
	}
	.rp {
		background-color: rgba(255, 255, 0, 0.498039);
	}
	.pd {
		background-color: rgba(0, 255, 0, 0.498039);
	}
	.cd {
		width: 0px;
		height: 0px;
		border-left: 8px solid transparent;
		border-right: 8px solid transparent;
		border-top: 8px solid black;
	}
</style>
<div class="row">
	<div class="col-md-12" id="nattatdiv">
		<div class="col-md-4">
			<div class="col-md-4 title-name" id="title">
                            <center><?=lang('label.VL_coverage_percent');?> </center>
			</div>
			<div class="col-md-8">
				<div id="coverage"></div>
			</div>
		</div>
		<div class="col-md-5">
			<div class="col-md-4 title-name" id="title">
				<center><?=lang('label.national_tat');?> <l style="color:red;"><?=lang('label.days');?></l></center>
			</div>
			<div class="col-md-8">
				<div id="nattat"></div>
			</div>
		</div>
		<div class="col-md-3">
                    <div class="title-name"><?=lang('label.key');?></div>
			<div class="row">
				<div class="col-md-6">
					<div class="key cr"><center>  <?=lang('label.collection_receipt_cr');?></center></div>
					<div class="key rp"><center> <?=lang('label.receipt_process');?></center></div>
				</div>
				<div class="col-md-6">
					<div class="key pd"><center><?=lang('label.process_dispatch');?></center></div>
					<div class="key"><center><div class="cd"></div><?=lang('label.collection_dispatch_cd');?></center></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading" style="min-height: 4em;">
		  	<div class="col-sm-3">
		  		<div id="samples_heading"><?=lang('label.testing_trends_for_routine_VL');?></div> <div class="display_range"></div>
		  	</div>
		    <div class="col-sm-3">
		    	<input type="submit" class="btn btn-primary" id="switchButton" onclick="switch_source()" value="<?=lang('label.switch_all_tests');?>">
		    </div>
		  </div>
		  <div class="panel-body" id="samples">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>
<div class="row">
	<!-- Map of the country -->
	<div class="col-md-7 col-sm-3 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.vl_outcomes');?> <div class="display_date" ></div>
		  </div>
		  <div class="panel-body" id="vlOutcomes">
		  	<center><div class="loader"></div></center>
		  </div>
		  
		</div>
	</div>
	<div class="col-md-5">
		<div class="panel panel-default">
		  <div class="panel-heading">
		    <?=lang('label.routine_VL_outcomes_gender');?>  <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="gender" style="height:650px;padding-bottom:0px;">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	
	<div class="col-md-6">
		<div class="panel panel-default">
		  <div class="panel-heading">
		    <?=lang('label.routine_VL_outcomes_age');?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="ageGroups">
		    <center><div class="loader"></div></center>
		  </div>
		  <div>
		  	<center><button class="btn btn-default" onclick="ageModal();" style="background-color: #2f80d1;color: white; margin-top: 1em;margin-bottom: 1em;"> <?=lang('label.modal.click_breakdown');?></button></center>
		  </div>
		</div>
	</div>
	<!-- Map of the country -->
	<div class="col-md-6 col-sm-4 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
			  <?=  lang('label.justification_tests')?>  <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="justification" style="height:500px;">
		    <center><div class="loader"></div></center>
		  </div>
		  <div>
		  	<center><button class="btn btn-default" onclick="justificationModal();" style="background-color: #2f80d1;color: white;margin-bottom: 1em;"><?=lang('label.modal.click_breakdown');?></button></center>
		  </div>
		</div>
	</div>
	
	
</div>
<div class="row">
	<!-- Map of the country -->
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading" id="heading">
		  	<?=  lang('label.county_outcomes')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="county">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="agemodal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="<?=  lang('label.modal_close')?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=  lang('label.age_category_breakdown')?></h4>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="<?=  lang('label.modal_close')?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"> <?=  lang('label.pregnant_lactating_mothers')?> </h4>
      </div>
      <div class="modal-body" id="CatJust">
        <center><div class="loader"></div></center>
      </div>
    </div>
  </div>
</div>

<?php $this->load->view('summary_view_footer'); ?>
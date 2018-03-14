<div id="first">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <?=lang('label.subcounties_outcomes')?> <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="regimen_outcomes">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				<?=lang('label.subcounties')?> <div class="display_date"></div>
				</div>
			  	<div class="panel-body" id="subcounty_summary">
			  		<center><div class="loader"></div></center>
			  	</div>
			</div>
		</div>
	</div>
</div>

<div id="second">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading" onclick="switch_source()">
			    <div id="samples_heading"><?=lang('label.testing_trends_for_routine')?></div> <?=lang('label.Click_to_switch')?><div class="display_range"></div>
			  </div>
			  <div class="panel-body" id="samples">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
		<div class="col-md-6 col-sm-3 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?=lang('label.vl_outcomes')?> <div class="display_date" ></div>
			  </div>
			  <div class="panel-body" id="vlOutcomes">
			  	<center><div class="loader"></div></center>
			  </div>
			  
			</div>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?=lang('label.routine_VLs_outcomes_by_gender')?> <div class="display_date" ></div>
			  </div>
			  <div class="panel-body" id="gender">
			  	<center><div class="loader"></div></center>
			  </div>
			  
			</div>
		</div>
		<div class="col-md-3 col-sm-3 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?=lang('label.routine_by_age')?> <div class="display_date" ></div>
			  </div>
			  <div class="panel-body" id="age">
			  	<center><div class="loader"></div></center>
			  </div>
			  
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
				  <?=lang('label.sub-county_sites')?> <div class="display_date"></div>
				</div>
			  	<div class="panel-body" id="sub_counties">
			  		<center><div class="loader"></div></center>
			  	</div>
			</div>
		</div>
	</div>
	

</div>

<?php $this->load->view('subcounty_view_footer');?>
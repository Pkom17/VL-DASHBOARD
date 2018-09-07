
<div id="notification" style="margin-bottom: 1em;background-color:#E4F1FE;">
  	<?= lang('label.not_suppressed'); ?>
</div>
<div class="row">
	<div class="col-md-4 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?= lang('label.tests_by_sample'); ?> <div class="display_date" ></div>
		  </div>
		  <div class="panel-body" id="samples">
		  	<center><div class="loader"></div></center>
		  </div>
		  
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
		  <div class="panel-heading">
		    <?= lang('label.tests_by_gender'); ?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="gender" style="height:650px;padding-bottom:0px;">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="panel panel-default">
		  <div class="panel-heading">
		    <?= lang('label.tests_by_age'); ?><div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="age">
		    <center><div class="loader"></div></center>
		  </div>
		  <div>
		  	<center><button class="btn btn-default" onclick="ageModal();" style="background-color: #2f80d1;color: white; margin-top: 1em;margin-bottom: 1em;"><?= lang('label.modal.click_breakdown'); ?></button></center>
		  </div>
		</div>
	</div>
</div>

<div class="row">
	<!-- Map of the country -->
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading" id="heading">
		  	<?= lang('label.partner_outcomes'); ?><div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="partner">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>

<div class="row">
	<!-- Map of the country -->
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading" id="heading">
		  	<?= lang('label.county_outcomes'); ?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="county">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>

<div class="row">
	<!-- Map of the country -->
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading" id="heading">
		  	<?= lang('label.sub_county_outcomes'); ?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="subcounty">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>

<div class="row">
	<!-- Map of the country -->
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading" id="heading">
		  	<?= lang('label.facilities_outcomes'); ?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="facility">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>


<?php $this->load->view('baseline_view_footer');?>
<div id="first">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  <?= lang('label.age_outcomes'); ?> <div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="age_outcomes">
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
			  <div class="panel-heading">
			    <?= lang('label.testing_trends'); ?> <div class="display_range"></div>
			  </div>
			  <div class="panel-body" id="samples">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?= lang('label.vl_outcomes'); ?><div class="display_date" ></div>
			  </div>
			  <div class="panel-body" id="vlOutcomes">
			  	<center><div class="loader"></div></center>
			  </div>
			  
			</div>
		</div>
		<div class="col-md-6 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?= lang('label.gender'); ?> <div class="display_date" ></div>
			  </div>
			  <div class="panel-body" id="gender">
			  	<center><div class="loader"></div></center>
			  </div>
			  
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?= lang('label.counties'); ?> <div class="display_date" ></div>
			  </div>
			  <div class="panel-body" id="countiesAge">
			  	<center><div class="loader"></div></center>
			  </div>
			  
			</div>
		</div>
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?= lang('label.partners'); ?> <div class="display_date" ></div>
			  </div>
			  <div class="panel-body" id="partnersAge">
			  	<center><div class="loader"></div></center>
			  </div>
			  
			</div>
		</div>
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?= lang('label.sub_counties'); ?><div class="display_date" ></div>
			  </div>
			  <div class="panel-body" id="subcountiesAge">
			  	<center><div class="loader"></div></center>
			  </div>
			  
			</div>
		</div>
		<div class="col-md-3 col-sm-12 col-xs-12">
			<div class="panel panel-default">
			  <div class="panel-heading">
			  	<?= lang('label.facilities'); ?> <div class="display_date" ></div>
			  </div>
			  <div class="panel-body" id="facilitiesAge">
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
			  	<?= lang('label.county_outcomes'); ?><div class="display_date"></div>
			  </div>
			  <div class="panel-body" id="county">
			    <center><div class="loader"></div></center>
			  </div>
			</div>
		</div>
	</div>

</div>


<?php $this->load->view('age_view_footer');?>
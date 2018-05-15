<div class="row">
	<!-- Map of the country -->
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
		  <div class="panel-heading">
		  	<?=lang('label.county_outcomes')?> <div class="display_date"></div>
		  </div>
		  <div class="panel-body" id="county">
		    <center><div class="loader"></div></center>
		  </div>
		</div>
	</div>
</div>

<div class="row" >
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading" id="heading">
			  <?=lang('label.counties')?> <div class="display_date"></div>
			</div>
		  	<div class="panel-body" id="county_sites">
		  		<center><div class="loader"></div></center>
		  	</div>
		</div>
	</div>
</div>

<?php $this->load->view('county_footer'); ?>
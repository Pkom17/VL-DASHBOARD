<script type="text/javascript">
	$().ready(function(){
		$.get("<?php echo base_url();?>template/dates", function(data){
    		obj = $.parseJSON(data);
	
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
    	});

    	$("#first").show();
		$("#second").hide();

		$("#county").load("<?php echo base_url('charts/summaries/county_outcomes');?>");
		$("#county_sites").load("<?php echo base_url('charts/county/county_table');?>");

		$("select").change(function(){
			em = $(this).val();

			// Send the data using post
	        var posting = $.post( "<?php echo base_url();?>template/filter_county_data", { county: em } );
	     
	        // Put the results in a div
	        posting.done(function( county ) {
	        	if(county!=""){
	        		county = JSON.parse(county);
	        	}
	        	
	        	// alert(county);
	        	/*$.get("<?php echo base_url();?>template/breadcrum/"+county, function(data){
	        		$("#breadcrum").html(data);
	        	});*/
	        	$.get("<?php echo base_url();?>template/dates", function(data){
	        		obj = $.parseJSON(data);
			
					if(obj['month'] == "null" || obj['month'] == null){
						obj['month'] = "";
					}
					$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
					$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
	        	});

	        	// alert(data);
	        	//

	        	if(county == ""){

	        		
	        		$("#first").show();
					$("#second").hide();

					$('#heading').html('<?=lang('label.counties_outcomes')?> <div class="display_date"></div>');

	        		$("#county").html("<center><div class='loader'></div></center>");
					$("#county").load("<?php echo base_url('charts/summaries/county_outcomes');?>");

					$("#county_sites").html("<center><div class='loader'></div></center>");
					$("#county_sites").load("<?php echo base_url('charts/county/county_table'); ?>"); 
	        	}
	        	else{
	        		
	        		$("#second").show();
					$("#first").hide();

					// $("#county_sites").empty();

					$('#heading').html('<?=lang('label.sub-counties_outcomes')?> <div class="display_date"></div>');

					$("#subcounty").html("<center><div class='loader'></div></center>");
					$("#subcounty").load("<?php echo base_url('charts/county/subcounty_outcomes');?>/"+null+"/"+null+"/"+county);

					$("#subcountypos").html("<center><div class='loader'></div></center>");
					$("#subcountypos").load("<?php echo base_url('charts/county/subcounty_outcomes_positivity');?>/"+null+"/"+null+"/"+county);

					$("#sub_counties").html("<center><div class='loader'></div></center>");
					$("#sub_counties").load("<?php echo base_url('charts/county/county_subcounties'); ?>/"+null+"/"+null+"/"+county);

					$("#partners").html("<center><div class='loader'></div></center>");
					$("#partners").load("<?php echo base_url('charts/county/county_partners'); ?>/"+null+"/"+null+"/"+county);

					$("#facilities_pmtct").html("<center><div class='loader'></div></center>");
					$("#facilities_pmtct").load("<?php echo base_url('charts/pmtct/pmtct'); ?>/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
	        	}

		         
	        });
		});

		$("button").click(function () {
		    var first, second;
		    first = $(".date-picker[name=startDate]").val();
		    second = $(".date-picker[name=endDate]").val();
		    
		    var new_title = set_multiple_date(first, second);

		    $(".display_date").html(new_title);
		    
		    from = format_date(first);
		    /* from is an array
		     	[0] => month
		     	[1] => year*/
		    to 	= format_date(second);
		    var error_check = check_error_date_range(from, to);
		    
		    if (!error_check) {
			    $.get("<?php echo base_url('county/check_county_select');?>", function(county) {
					//Checking if county was previously selected and calling the relevant views
					if (county==0) {
						$("#first").show();
						$("#second").hide();

						$('#heading').html('<?=lang('label.counties_outcomes')?> <div class="display_date"></div>');

						$("#county").html("<center><div class='loader'></div></center>"); 
		 				$("#county").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);
				
						$("#county_sites").html("<center><div class='loader'></div></center>");
						$("#county_sites").load("<?php echo base_url('charts/county/county_table'); ?>/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);

					} else {
						county = JSON.parse(county);
						$("#second").show();
						$("#first").hide();

						$('#heading').html('<?=lang('label.sub-counties_outcomes')?> <div class="display_date"></div>');

						$("#subcounty").html("<center><div class='loader'></div></center>"); 
		 				$("#subcounty").load("<?php echo base_url('charts/county/subcounty_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+county+"/"+to[1]+"/"+to[0]);

		 				$("#subcountypos").html("<center><div class='loader'></div></center>"); 
		 				$("#subcountypos").load("<?php echo base_url('charts/county/subcounty_outcomes_positivity'); ?>/"+from[1]+"/"+from[0]+"/"+county+"/"+to[1]+"/"+to[0]);
				
						$("#sub_counties").html("<center><div class='loader'></div></center>");
						$("#sub_counties").load("<?php echo base_url('charts/county/county_subcounties'); ?>/"+from[1]+"/"+from[0]+"/"+county+"/"+to[1]+"/"+to[0]);

						$("#partners").html("<center><div class='loader'></div></center>");
						$("#partners").load("<?php echo base_url('charts/county/county_partners'); ?>/"+from[1]+"/"+from[0]+"/"+county+"/"+to[1]+"/"+to[0]);

						$("#facilities_pmtct").html("<center><div class='loader'></div></center>");
						$("#facilities_pmtct").load("<?php echo base_url('charts/pmtct/pmtct'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]+"/"+county);
						$(".display_date").html(new_title);
					}
				});
			}
		    
		});
	});

	function date_filter(criteria, id)
 	{
 		if (criteria === "monthly") {
 			year = null;
 			month = id;
 		}else {
 			year = id;
 			month = null;
 		}

 		var posting = $.post( '<?php echo base_url();?>template/filter_date_data', { 'year': year, 'month': month } );

 		// Put the results in a div
		posting.done(function( data ) {
			obj = $.parseJSON(data);
			
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
			
		});

		

		$.get("<?php echo base_url('county/check_county_select');?>", function(county) {
			//Checking if county was previously selected and calling the relevant views
			if (county==0) {
				$("#first").show();
				$("#second").hide();

				$('#heading').html('<?=lang('label.counties_outcomes')?> <div class="display_date"></div>');

				$("#county").html("<center><div class='loader'></div></center>"); 
 				$("#county").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+year+"/"+month);
		
				$("#county_sites").html("<center><div class='loader'></div></center>");
				$("#county_sites").load("<?php echo base_url('charts/county/county_table'); ?>/"+year+"/"+month);

			} else {
				county = JSON.parse(county);
				$("#second").show();
				$("#first").hide();

				$('#heading').html('<?=lang('label.sub-counties_outcomes')?> <div class="display_date"></div>');

				$("#subcounty").html("<center><div class='loader'></div></center>"); 
 				$("#subcounty").load("<?php echo base_url('charts/county/subcounty_outcomes'); ?>/"+year+"/"+month);

 				$("#subcountypos").html("<center><div class='loader'></div></center>"); 
 				$("#subcountypos").load("<?php echo base_url('charts/county/subcounty_outcomes_positivity'); ?>/"+year+"/"+month);
		
				$("#sub_counties").html("<center><div class='loader'></div></center>");
				$("#sub_counties").load("<?php echo base_url('charts/county/county_subcounties'); ?>/"+year+"/"+month);

				$("#partners").html("<center><div class='loader'></div></center>");
				$("#partners").load("<?php echo base_url('charts/county/county_partners'); ?>/"+year+"/"+month);

				$("#facilities_pmtct").html("<center><div class='loader'></div></center>");
				$("#facilities_pmtct").load("<?php echo base_url('charts/pmtct/pmtct'); ?>/"+year+"/"+month+"/"+null+"/"+null+"/"+null+"/"+county);
			}
		});
 		
		
 		 
	}
</script>
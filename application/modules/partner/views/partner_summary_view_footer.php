<script type="text/javascript">
	$().ready(function () {
		localStorage.setItem("my_var", 0);
		$.get("<?php echo base_url();?>template/dates", function(data){
    		obj = $.parseJSON(data);
	
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
    	});
    	$.get("<?php echo base_url();?>template/get_current_header", function(data){
			$(".display_current_range").html(data);
    	});
		$("#second").hide();
		$("#third").hide();
		// fetching the partner outcomes
		$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+null+"/"+null+"/"+1);
		
		// fetching the data for a specific partner
		$("select").change(function(){
			var all = localStorage.getItem("my_var");
			part = $(this).val();
			// Send the data using post
	        var posting = $.post( "<?php echo base_url();?>template/filter_partner_data", { partner: part } );
	        
	        // Put the results in a div
	        posting.done(function( data ) {
	        	$.get("<?php echo base_url();?>template/breadcrum/"+data+"/"+1, function(data){
	        		$("#breadcrum").html(data);
	        	});
	        	$.get("<?php echo base_url();?>template/dates", function(data){
		    		obj = $.parseJSON(data);
			
					if(obj['month'] == "null" || obj['month'] == null){
						obj['month'] = "";
					}
					$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
					$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
		    	});
	        	// Condition to dispay the proper divs based on whether a partner is selected or not
	        	data = $.parseJSON(data);
	        	if (data==null) {
	        		$("#second").hide();
					$("#third").hide();
					// fetching the partner outcomes
					$("#partner_div").html("<center><div class='loader'></div></center>");
					$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+null+"/"+null+"/"+1);
	        	} else {
	        		
	        		$("#second").show();
					$("#third").show();

	        		$("#samples").html("<center><div class='loader'></div></center>");
					$("#vlOutcomes").html("<center><div class='loader'></div></center>");
					$("#justification").html("<center><div class='loader'></div></center>");
					$("#ageGroups").html("<center><div class='loader'></div></center>");
					$("#gender").html("<center><div class='loader'></div></center>");
					$("#partner_div").html("<center><div class='loader'></div></center>");

					$("#long_tracking").html("<center><div class='loader'></div></center>");
					$("#current_sup").html("<center><div class='loader'></div></center>");

					$("#samples").load("<?php echo base_url('charts/summaries/sample_types'); ?>/"+null+"/"+null+"/"+null+"/"+all);

					$("#vlOutcomes").load("<?php echo base_url('charts/summaries/vl_outcomes'); ?>/"+null+"/"+null+"/"+null+"/"+data);
					$("#justification").load("<?php echo base_url('charts/summaries/justification'); ?>/"+null+"/"+null+"/"+null+"/"+data);
					$("#ageGroups").load("<?php echo base_url('charts/summaries/age'); ?>/"+null+"/"+null+"/"+null+"/"+data);
					$("#gender").load("<?php echo base_url('charts/summaries/gender'); ?>/"+null+"/"+null+"/"+null+"/"+data);
					$("#long_tracking").load("<?php echo base_url('charts/summaries/get_patients'); ?>/"+null+"/"+null+"/"+null+"/"+data);
					$("#current_sup").load("<?php echo base_url('charts/summaries/current_suppression'); ?>/"+null+"/"+data);
					// $("#partner").load("<?php //echo base_url('charts/sites/site_outcomes'); ?>/"+null+"/"+null+"/"+data);
					$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+null+"/"+null+"/"+1+"/"+data);
					

					//$("#pat_stats").load("<?php //echo base_url('charts/summaries/get_patients');?>/"+null+"/"+null+"/"+null+"/"+data);
					//$("#pat_out").load("<?php //echo base_url('charts/summaries/get_patients_outcomes');?>/"+null+"/"+null+"/"+null+"/"+data);
					//$("#pat_graph").load("<?php //echo base_url('charts/summaries/get_patients_graph');?>/"+null+"/"+null+"/"+null+"/"+data);
	        	}
	        });
		});

		$("button").click(function () {
			var all = localStorage.getItem("my_var");
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
			    $.get("<?php echo base_url();?>partner/check_partner_select", function (data) {
					partner = data;
					
					if (partner==0) {
						$("#second").hide();
						$("#third").hide();
						// fetching the partner outcomes
						$("#partner_div").html("<center><div class='loader'></div></center>");
						$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+1+"/"+null+"/"+null+"/"+to[1]+"/"+to[0]);
					} else {
						partner = $.parseJSON(partner);
						$("#second").show();
						$("#third").show();
						
		        		$("#samples").html("<center><div class='loader'></div></center>");
						$("#vlOutcomes").html("<center><div class='loader'></div></center>");
						$("#justification").html("<center><div class='loader'></div></center>");
						$("#ageGroups").html("<center><div class='loader'></div></center>");
						$("#gender").html("<center><div class='loader'></div></center>");
						$("#partner_div").html("<center><div class='loader'></div></center>");


						$("#pat_stats").html("<center><div class='loader'></div></center>");
						$("#pat_out").html("<center><div class='loader'></div></center>");
						$("#pat_graph").html("<center><div class='loader'></div></center>");

						$("#samples").load("<?php echo base_url('charts/summaries/sample_types'); ?>/"+from[1]+"/"+null+"/"+partner+"/"+all);
						$("#vlOutcomes").load("<?php echo base_url('charts/summaries/vl_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+partner+"/"+to[1]+"/"+to[0]);
						$("#justification").load("<?php echo base_url('charts/summaries/justification'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+partner+"/"+to[1]+"/"+to[0]);
						$("#ageGroups").load("<?php echo base_url('charts/summaries/age'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+partner+"/"+to[1]+"/"+to[0]);
						$("#gender").load("<?php echo base_url('charts/summaries/gender'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+partner+"/"+to[1]+"/"+to[0]);
						$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+1+"/"+partner+"/"+null+"/"+to[1]+"/"+to[0]);

						$("#long_tracking").load("<?php echo base_url('charts/summaries/get_patients');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+partner+"/"+to[1]+"/"+to[0]);
						
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

 		var all = localStorage.getItem("my_var");
 		var posting = $.post( '<?php echo base_url();?>template/filter_date_data', { 'year': year, 'month': month } );

 		// Put the results in a div
		posting.done(function( data ) {
			obj = $.parseJSON(data);
			
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
			
			$.get("<?php echo base_url();?>partner/check_partner_select", function (data) {
				partner = data;
				
				if (partner==0) {
					$("#second").hide();
					$("#third").hide();
					// fetching the partner outcomes
					$("#partner_div").html("<center><div class='loader'></div></center>");
					$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+year+"/"+month+"/"+1);
				} else {
					partner = $.parseJSON(partner);
					$("#second").show();
					$("#third").show();
					
	        		$("#samples").html("<center><div class='loader'></div></center>");
					$("#vlOutcomes").html("<center><div class='loader'></div></center>");
					$("#justification").html("<center><div class='loader'></div></center>");
					$("#ageGroups").html("<center><div class='loader'></div></center>");
					$("#gender").html("<center><div class='loader'></div></center>");
					$("#partner_div").html("<center><div class='loader'></div></center>");


					$("#pat_stats").html("<center><div class='loader'></div></center>");
					$("#pat_out").html("<center><div class='loader'></div></center>");
					$("#pat_graph").html("<center><div class='loader'></div></center>");

					$("#samples").load("<?php echo base_url('charts/summaries/sample_types'); ?>/"+year+"/"+month+"/"+partner+"/"+all);
					$("#vlOutcomes").load("<?php echo base_url('charts/summaries/vl_outcomes'); ?>/"+year+"/"+month+"/"+null+"/"+partner);
					$("#justification").load("<?php echo base_url('charts/summaries/justification'); ?>/"+year+"/"+month+"/"+null+"/"+partner);
					$("#ageGroups").load("<?php echo base_url('charts/summaries/age'); ?>/"+year+"/"+month+"/"+null+"/"+partner);
					$("#gender").load("<?php echo base_url('charts/summaries/gender'); ?>/"+year+"/"+month+"/"+null+"/"+partner);
					$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+year+"/"+month+"/"+1+"/"+partner);

					$("#long_tracking").load("<?php echo base_url('charts/summaries/get_patients');?>/"+year+"/"+month+"/"+null+"/"+partner);
				}
			});
		});
	}

	function switch_source(){
		var all = localStorage.getItem("my_var");

		if(all == 0){
			localStorage.setItem("my_var", 1);
			all=1;
			$("#samples_heading").html('<?=lang('label.testing_trends_for_all_tests');?>');
			$("#switchButton").val('<?=lang('label.switch_routine_tests_trends');?>');
		}
		else{
			localStorage.setItem("my_var", 0);
			all=0;
			$("#samples_heading").html('<?=lang('label.testing_trends_for_routine_VL');?> ');
			$("#switchButton").val('<?=lang('label.switch_all_tests');?>');
		}

		$.get("<?php echo base_url();?>partner/check_partner_select", function (data) {

			if(data == 0){data = null;}
			data = $.parseJSON(data);
			$("#samples").load("<?php echo base_url('charts/summaries/sample_types'); ?>/"+null+"/"+null+"/"+data+"/"+all);
		});
	}

 	function ageModal()
	{
		$('#agemodal').modal('show');
		$('#CatAge').load('<?php echo base_url();?>charts/summaries/agebreakdown');
	}

	function justificationModal()
	{
		$('#justificationmodal').modal('show');
		$('#CatJust').load('<?php echo base_url();?>charts/summaries/justificationbreakdown');
	}
</script>
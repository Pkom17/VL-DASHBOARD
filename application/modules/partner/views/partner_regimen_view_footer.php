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
		$("#second").hide();
		$("#first").show();
		// $("#age_outcomes").load("<?php //echo baase_url();?>charts/ages/age_outcomes");
		$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes'); ?>");

		$("#partner").change(function(){
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
	        	if (data=='null') {
	        		$("#second").hide();
					$("#first").show();
					// fetching the partner outcomes
					$("#regimen_outcomes").html("<center><div class='loader'></div></center>");
					$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes'); ?>");
	        	} else {
	        		data = JSON.parse(data);
	        		// alert(data);
	        		$("#second").hide();
					$("#first").show();
					// fetching the partner outcomes
					$("#regimen_outcomes").html("<center><div class='loader'></div></center>");
					$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes'); ?>/"+null+"/"+null+"/"+null+"/"+null+"/"+data);
	        	}
	        });
		});

		$("#regimen").change(function(){
			reg = $(this).val();

			var posting = $.post( "<?php echo base_url();?>template/filter_partner_regimen_data", { regimen: reg } );
			posting.done(function( adata ) {
				$.get("<?php echo base_url();?>template/dates", function(data){
		    		obj = $.parseJSON(data);
			
					if(obj['month'] == "null" || obj['month'] == null){
						obj['month'] = "";
					}
					$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
					$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
		    	});
				$.get("<?php echo base_url();?>partner/check_partner_select", function (data) {
					partner = JSON.parse(data);
					if (adata==null||adata==""||adata==undefined) {
						$("#second").hide();
						$("#first").show();
						
						$("#regimen_outcomes").html("<center><div class='loader'></div></center>");
						$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes'); ?>/"+null+"/"+null+"/"+null+"/"+null+"/"+partner);
					} else {
						$("#first").hide();
						$("#second").show();

						adata = parseInt(adata);

						$("#samples").html("<center><div class='loader'></div></center>");
						$("#vlOutcomes").html("<center><div class='loader'></div></center>");
						$("#gender").html("<center><div class='loader'></div></center>");
						$("#age").html("<center><div class='loader'></div></center>");
						$("#county").html("<center><div class='loader'></div></center>");

						$("#samples").load("<?php echo base_url('charts/regimen/sample_types'); ?>/"+null+"/"+adata+"/"+partner);
						$("#vlOutcomes").load("<?php echo base_url('charts/regimen/regimen_vl_outcome'); ?>/"+null+"/"+null+"/"+adata+"/"+null+"/"+null+"/"+partner);
						$("#gender").load("<?php echo base_url('charts/regimen/regimen_gender'); ?>/"+null+"/"+null+"/"+adata+"/"+null+"/"+null+"/"+partner);
						$("#age").load("<?php echo base_url('charts/regimen/regimen_age'); ?>/"+null+"/"+null+"/"+adata+"/"+null+"/"+null+"/"+partner);
						$("#county").load("<?php echo base_url('charts/regimen/regimen_county_outcomes'); ?>/"+null+"/"+null+"/"+adata+"/"+null+"/"+null+"/"+partner);
					}
				});	
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
		    var new_range = to[1]-1+" - "+to[1];
		    $(".display_range").html(new_range);
		    var error_check = check_error_date_range(from, to);
		    
		    if (!error_check) {
			    $.get("<?php echo base_url();?>partner/check_partner_select", function (data) {
					partner = JSON.parse(data);
					
					if (partner==0) {
						$("#second").hide();
						$("#first").show();
						
						$("#regimen_outcomes").html("<center><div class='loader'></div></center>");
						$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]+"/"+null);
					} else {
						partner = $.parseJSON(partner);
						$.get("<?php echo base_url();?>partner/check_partner_regimen_select", function (data) {
							adata = JSON.parse(data);
							if (adata==0) {
								$("#second").hide();
								$("#first").show();
								
								$("#regimen_outcomes").html("<center><div class='loader'></div></center>");
								$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]+"/"+partner);
							} else {
								$("#second").show();
								$("#first").hide();

								$("#samples").html("<center><div class='loader'></div></center>");
								$("#vlOutcomes").html("<center><div class='loader'></div></center>");
								$("#gender").html("<center><div class='loader'></div></center>");
								$("#age").html("<center><div class='loader'></div></center>");
								$("#county").html("<center><div class='loader'></div></center>");

								$("#samples").load("<?php echo base_url('charts/regimen/sample_types'); ?>/"+to[1]+"/"+adata+"/"+partner);
								$("#vlOutcomes").load("<?php echo base_url('charts/regimen/regimen_vl_outcome'); ?>/"+from[1]+"/"+from[0]+"/"+adata+"/"+to[1]+"/"+to[0]+"/"+partner);
								$("#gender").load("<?php echo base_url('charts/regimen/regimen_gender'); ?>/"+from[1]+"/"+from[0]+"/"+adata+"/"+to[1]+"/"+to[0]+"/"+partner);
								$("#age").load("<?php echo base_url('charts/regimen/regimen_age'); ?>/"+from[1]+"/"+from[0]+"/"+adata+"/"+to[1]+"/"+to[0]+"/"+partner);
								$("#county").load("<?php echo base_url('charts/regimen/regimen_county_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+adata+"/"+to[1]+"/"+to[0]+"/"+partner);
							}
						});
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
			
			$.get("<?php echo base_url();?>partner/check_partner_select", function (data) {
				partner = data;
				
				if (partner==0) {
						$("#second").hide();
						$("#first").show();
						
						$("#regimen_outcomes").html("<center><div class='loader'></div></center>");
						$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes'); ?>/"+year+"/"+month+"/"+null+"/"+null);
					} else {
						partner = $.parseJSON(partner);
						$.get("<?php echo base_url();?>partner/check_partner_regimen_select", function (data) {
							adata = JSON.parse(data);
							if (adata==0) {
								$("#second").hide();
								$("#first").show();
								
								$("#regimen_outcomes").html("<center><div class='loader'></div></center>");
								$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes'); ?>/"+year+"/"+month+"/"+null+"/"+null+"/"+partner);
							} else {
								$("#second").show();
								$("#first").hide();

								$("#samples").html("<center><div class='loader'></div></center>");
								$("#vlOutcomes").html("<center><div class='loader'></div></center>");
								$("#gender").html("<center><div class='loader'></div></center>");
								$("#age").html("<center><div class='loader'></div></center>");
								$("#county").html("<center><div class='loader'></div></center>");

								$("#samples").load("<?php echo base_url('charts/regimen/sample_types'); ?>/"+year+"/"+adata+"/"+partner);
								$("#vlOutcomes").load("<?php echo base_url('charts/regimen/regimen_vl_outcome'); ?>/"+year+"/"+month+"/"+adata+"/"+null+"/"+null+"/"+partner);
								$("#gender").load("<?php echo base_url('charts/regimen/regimen_gender'); ?>/"+year+"/"+month+"/"+adata+"/"+null+"/"+null+"/"+partner);
								$("#age").load("<?php echo base_url('charts/regimen/regimen_age'); ?>/"+year+"/"+month+"/"+adata+"/"+null+"/"+null+"/"+partner);
								$("#county").load("<?php echo base_url('charts/regimen/regimen_county_outcomes'); ?>/"+year+"/"+month+"/"+adata+"/"+null+"/"+null+"/"+partner);
							}
						});
					}
			});
		});
	}
</script>
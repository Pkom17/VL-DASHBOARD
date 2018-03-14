<script type="text/javascript">
	$().ready(function () {
		$.get("<?php echo base_url();?>template/dates", function(data){
    		obj = $.parseJSON(data);
	
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
    	});
		$("#second").hide();

		$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes');?>");


		$("select").change(function(){
			em = $(this).val();
			// console.log(em);
			// Send the data using post
	        var posting = $.post( "<?php echo base_url();?>template/filter_regimen_data", { regimen: em } );
	     
	   //      // Put the results in a div
	        posting.done(function( data ) {
	        	// console.log(data);
	   //      	$.get("<?php echo base_url();?>template/breadcrum/"+data, function(data){
	   //      		$("#breadcrum").html(data);
	   //      	});
	        	$.get("<?php echo base_url();?>template/dates", function(data){
	        		obj = $.parseJSON(data);
			
					if(obj['month'] == "null" || obj['month'] == null){
						obj['month'] = "";
					}
					$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
					$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
	        	});
	        	if (data=="") {
	        		$("#second").hide();
	        		$("#first").show();

	        		$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes');?>");
	        	} else {
	        		data = $.parseJSON(data);
	        		$("#first").hide();
	        		$("#second").show();

	        		$("#samples").html("<center><div class='loader'></div></center>");
					$("#vlOutcomes").html("<center><div class='loader'></div></center>");
					$("#gender").html("<center><div class='loader'></div></center>");
					$("#age").html("<center><div class='loader'></div></center>");
					$("#countiesRegimen").html("<center><div class='loader'></div></center>");
					$("#partnersRegimen").html("<center><div class='loader'></div></center>");
					$("#subcountiesRegimen").html("<center><div class='loader'></div></center>");
					$("#FacilitiesRegimen").html("<center><div class='loader'></div></center>");
					$("#county").html("<center><div class='loader'></div></center>");
					
					$("#samples").load("<?php echo base_url('charts/regimen/sample_types'); ?>/"+null+"/"+data);
					$("#vlOutcomes").load("<?php echo base_url('charts/regimen/regimen_vl_outcome'); ?>");
					$("#gender").load("<?php echo base_url('charts/regimen/regimen_gender'); ?>/"+null+"/"+null+"/"+data);
					$("#age").load("<?php echo base_url('charts/regimen/regimen_age'); ?>/"+null+"/"+null+"/"+data);
					$("#countiesRegimen").load("<?= @base_url('charts/regimen/regimen_breakdowns'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+1);
					$("#partnersRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+null+"/"+1);
					$("#subcountiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+null+"/"+null+"/"+1); 
					$("#FacilitiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+1); 
					$("#county").load("<?php echo base_url('charts/regimen/regimen_county_outcomes'); ?>/"+null+"/"+null+"/"+data);
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
			    $.get("<?php echo base_url('regimen/check_regimen_select');?>", function( data ){
					data = JSON.parse(data);
					// console.log(data);
					if (data==0) {
						$("#second").hide();
		        		$("#first").show();

		        		$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes');?>/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);
					} else {
						$("#first").hide();
		        		$("#second").show();

						$("#samples").html("<center><div class='loader'></div></center>");
		        		$("#vlOutcomes").html("<center><div class='loader'></div></center>");
						$("#gender").html("<center><div class='loader'></div></center>");
						$("#age").html("<center><div class='loader'></div></center>");
						$("#countiesRegimen").html("<center><div class='loader'></div></center>");
						$("#partnersRegimen").html("<center><div class='loader'></div></center>");
						$("#subcountiesRegimen").html("<center><div class='loader'></div></center>");
						$("#FacilitiesRegimen").html("<center><div class='loader'></div></center>");
						$("#county").html("<center><div class='loader'></div></center>");
						
						$("#samples").load("<?php echo base_url('charts/regimen/sample_types'); ?>/"+from[1]+"/"+data);
						$("#vlOutcomes").load("<?php echo base_url('charts/regimen/regimen_vl_outcome'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]);
						$("#gender").load("<?php echo base_url('charts/regimen/regimen_gender'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]);
						$("#age").load("<?php echo base_url('charts/regimen/regimen_age'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]); 
						$("#countiesRegimen").load("<?= @base_url('charts/regimen/regimen_breakdowns'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+1);
						$("#partnersRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+null+"/"+1);
						$("#subcountiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+1);
						$("#FacilitiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+1);
						$("#county").load("<?php echo base_url('charts/regimen/regimen_county_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]);
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
 		// console.log(year+"<___>"+month);
 		var posting = $.post( '<?php echo base_url();?>template/filter_date_data', { 'year': year, 'month': month } );

 		// Put the results in a div
		posting.done(function( data ) {
			obj = $.parseJSON(data);
			
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");

			$.get("<?php echo base_url('regimen/check_regimen_select');?>", function( data ){
				data = $.parseJSON(data);
				if (data==0) {
					$("#second").hide();
	        		$("#first").show();

	        		$("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes');?>/"+year+"/"+month);
				} else {
					$("#first").hide();
	        		$("#second").show();

	        		$("#samples").html("<center><div class='loader'></div></center>");
					$("#vlOutcomes").html("<center><div class='loader'></div></center>");
					$("#gender").html("<center><div class='loader'></div></center>");
					$("#age").html("<center><div class='loader'></div></center>");
					$("#countiesRegimen").html("<center><div class='loader'></div></center>");
					$("#partnersRegimen").html("<center><div class='loader'></div></center>");
					$("#subcountiesRegimen").html("<center><div class='loader'></div></center>");
					$("#FacilitiesRegimen").html("<center><div class='loader'></div></center>");
					$("#county").html("<center><div class='loader'></div></center>");
					
					$("#samples").load("<?php echo base_url('charts/regimen/sample_types'); ?>/"+year+"/"+data);
					$("#vlOutcomes").load("<?php echo base_url('charts/regimen/regimen_vl_outcome'); ?>/"+year+"/"+month+"/"+data);
					$("#gender").load("<?php echo base_url('charts/regimen/regimen_gender'); ?>/"+year+"/"+month+"/"+data);
					$("#age").load("<?php echo base_url('charts/regimen/regimen_age'); ?>/"+year+"/"+month+"/"+data);
					$("#countiesRegimen").load("<?= @base_url('charts/regimen/regimen_breakdowns'); ?>/"+year+"/"+month+"/"+data+"/"+to[1]+"/"+to[0]+"/"+1);
					$("#partnersRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/"+year+"/"+month+"/"+data+"/"+null+"/"+null+"/"+null+"/"+1);
					$("#subcountiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/"+year+"/"+month+"/"+data+"/"+null+"/"+null+"/"+null+"/"+null+"/"+1);
					$("#FacilitiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/"+year+"/"+month+"/"+data+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+1); 
					$("#county").load("<?php echo base_url('charts/regimen/regimen_county_outcomes'); ?>/"+year+"/"+month+"/"+data);
				}
			});
			
		}); 
	}
</script>
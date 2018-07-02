<script type="text/javascript">
	$().ready(function(){
		$.get("<?php echo base_url();?>template/dates", function(data){
    		obj = $.parseJSON(data);
			console.log(obj);
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
    	});

		$("#requests").load("<?php echo base_url('charts/shortcodes/request_trends'); ?>");
		$("#counties").load("<?php echo base_url('charts/shortcodes/counties'); ?>");
		$("#subcounties").load("<?php echo base_url('charts/shortcodes/subcounties'); ?>");
		$("#facilities").load("<?php echo base_url('charts/shortcodes/facilities'); ?>"); 
		$("#partners").load("<?php echo base_url('charts/shortcodes/partner'); ?>");
		$("#facilities_requesting").load("<?php echo base_url('charts/shortcodes/facilities_requesting'); ?>");
		
		$(".display_date").load("<?php echo base_url('charts/summaries/display_date'); ?>");
		$(".display_range").load("<?php echo base_url('charts/summaries/display_range'); ?>");

		$("select").change(function(){
			em = $(this).val();

			// Send the data using post
	        var posting = $.post( "<?php echo base_url();?>template/filter_county_data", { county: em } );
	     
	        // Put the results in a div
	        posting.done(function( data ) {
	        	/*$.get("<?php echo base_url();?>template/breadcrum/"+data, function(data){
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
	        	$('#requests').html('<?=lang('label.county_sites_outcomes')?> <div class="display_date"></div>');
	        	$("#counties").html("<div><?=lang('label.loading')?></div>");
	        	$("#subcounties").html("<center><div class='loader'></div></center>");
		        $("#facilities").html("<center><div class='loader'></div></center>");
				$("#partners").html("<center><div class='loader'></div></center>");
				$("#facilities_requesting").html("<center><div class='loader'></div></center>");
				
				$("#requests").load("<?php echo base_url('charts/shortcodes/request_trends'); ?>/"+null+"/"+null+"/"+data);
				$("#counties").load("<?php echo base_url('charts/shortcodes/counties'); ?>/"+null+"/"+null+"/"+data);
				$("#subcounties").load("<?php echo base_url('charts/shortcodes/subcounties'); ?>/"+null+"/"+null+"/"+data); 
				$("#facilities").load("<?php echo base_url('charts/shortcodes/facilities'); ?>/"+null+"/"+null+"/"+data); 
				$("#partners").load("<?php echo base_url('charts/shortcodes/partner'); ?>/"+null+"/"+null+"/"+data);
				$("#facilities_requesting").load("<?php echo base_url('charts/shortcodes/facilities_requesting'); ?>/"+null+"/"+null+"/"+data);
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
			    $("#requests").html("<div><?=lang('label.loading')?></div>");
		 		$("#counties").html("<center><div class='loader'></div></center>");
		 		$("#subcounties").html("<center><div class='loader'></div></center>"); 
				$("#facilities").html("<center><div class='loader'></div></center>");
				$("#partners").html("<center><div class='loader'></div></center>");
				$("#facilities_requesting").html("<center><div class='loader'></div></center>");

				$("#requests").load("<?php echo base_url('charts/shortcodes/request_trends'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#counties").load("<?php echo base_url('charts/shortcodes/counties'); ?>/"+from[1]);
		 		$("#subcounties").load("<?php echo base_url('charts/shortcodes/subcounties'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#facilities").load("<?php echo base_url('charts/shortcodes/facilities'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]); 
				$("#partners").load("<?php echo base_url('charts/shortcodes/partner'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#facilities_requesting").load("<?php echo base_url('charts/shortcodes/facilities_requesting'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
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
 		
		$("#requests").html("<div><?=lang('label.loading')?></div>");
 		$("#counties").html("<center><div class='loader'></div></center>");
 		$("#subcounties").html("<center><div class='loader'></div></center>"); 
		$("#facilities").html("<center><div class='loader'></div></center>");
		$("#partners").html("<center><div class='loader'></div></center>");
		$("#facilities_requesting").html("<center><div class='loader'></div></center>");

		$("#requests").load("<?php echo base_url('charts/shortcodes/request_trends'); ?>/"+year+"/"+month);
		$("#counties").load("<?php echo base_url('charts/shortcodes/counties'); ?>/"+year);
 		$("#subcounties").load("<?php echo base_url('charts/shortcodes/subcounties'); ?>/"+year+"/"+month);
		$("#facilities").load("<?php echo base_url('charts/shortcodes/facilities'); ?>/"+year+"/"+month); 
		$("#partners").load("<?php echo base_url('charts/shortcodes/partner'); ?>/"+year+"/"+month);
		$("#facilities_requesting").load("<?php echo base_url('charts/shortcodes/facilities_requesting'); ?>/"+year+"/"+month);
	}
</script>
<script type="text/javascript">
	$().ready(function(){

		$.get("<?php echo base_url();?>template/dates", function(data){
    		obj = $.parseJSON(data);
			// console.log(obj);
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
    	});
    	$.get("<?php echo base_url();?>template/get_current_header", function(data){
			$(".display_current_range").html(data);
    	});

		$("#countys").html("<div><?=lang('label.loading')?></div>");
		$("#partners").html("<div><?=lang('label.loading')?></div>");
		$("#subcounty").html("<div><?=lang('label.loading')?></div>");
		$("#facilities").html("<div><?=lang('label.loading')?></div>");

		$("#countys_g").html("<div><?=lang('label.loading')?></div>");
		$("#partners_g").html("<div><?=lang('label.loading')?></div>");
		$("#subcounty_g").html("<div><?=lang('label.loading')?></div>");
		$("#facilities_g").html("<div><?=lang('label.loading')?></div>");


		$("#countys_a").html("<div><?=lang('label.loading')?></div>");
		$("#partners_a").html("<div><?=lang('label.loading')?></div>");
		$("#subcounty_a").html("<div><?=lang('label.loading')?></div>");
		$("#facilities_a").html("<div><?=lang('label.loading')?></div>");

		$("#countys_na").html("<div><?=lang('label.loading')?></div>");
		$("#partners_na").html("<div><?=lang('label.loading')?></div>");
		$("#subcounty_na").html("<div><?=lang('label.loading')?></div>");
		$("#facilities_na").html("<div><?=lang('label.loading')?></div>");


		$("#current_sup").load("<?php echo base_url('charts/summaries/current_suppression'); ?>");

		$("#countys").load("<?php echo base_url('charts/summaries/county_listing/1/1/0');?>");
		$("#subcounty").load("<?php echo base_url('charts/summaries/subcounty_listing/2/1');?>");
		$("#partners").load("<?php echo base_url('charts/summaries/partner_listing/3/1');?>");
		$("#facilities").load("<?php echo base_url('charts/summaries/site_listing/4/1');?>");

		$("#countys_g").load("<?php echo base_url('charts/summaries/county_listing_gender/1/1/0');?>");
		$("#partners_g").load("<?php echo base_url('charts/summaries/partner_listing_gender/3/1');?>");
		$("#subcounty_g").load("<?php echo base_url('charts/summaries/subcounty_listing_gender/2/1');?>");
		$("#facilities_g").load("<?php echo base_url('charts/summaries/site_listing_gender/4/1');?>");

		$("#countys_a").load("<?php echo base_url('charts/summaries/county_listing_age/1/1/0');?>");
		$("#partners_a").load("<?php echo base_url('charts/summaries/partner_listing_age/3/1');?>");
		$("#subcounty_a").load("<?php echo base_url('charts/summaries/subcounty_listing_age/2/1');?>");
		$("#facilities_a").load("<?php echo base_url('charts/summaries/site_listing_age/4/1');?>");

		$("#countys_na").load("<?php echo base_url('charts/summaries/county_listing_age_n/1/1/0');?>");
		$("#partners_na").load("<?php echo base_url('charts/summaries/partner_listing_age_n/3/1');?>");
		$("#subcounty_na").load("<?php echo base_url('charts/summaries/subcounty_listing_age_n/2/1');?>");
		$("#facilities_na").load("<?php echo base_url('charts/summaries/site_listing_age_n/4/1');?>");

		$("#long_tracking").load("<?php echo base_url('charts/summaries/get_patients'); ?>");

		$("select").change(function(){
			em = $(this).val();

			// Send the data using post
	        var posting = $.post( "<?php echo base_url();?>template/filter_county_data", { county: em } );
	     
	        // Put the results in a div
	        posting.done(function( data ) {
	        	if(data!=""){
	        		data = JSON.parse(data);
	        	}
	        	// data = $.parseJSON(data);
	        	$.get("<?php echo base_url();?>template/breadcrum/"+data, function(data){
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

	        	// alert(data);
	        	$("#long_tracking").html("<center><div class='loader'></div></center>");
	        	$("#current_sup").html("<center><div class='loader'></div></center>");

				$("#partners").html("<div><?=lang('label.loading')?></div>");
				$("#subcounty").html("<div><?=lang('label.loading')?></div>");
				$("#facilities").html("<div><?=lang('label.loading')?></div>");

				$("#partners_g").html("<div><?=lang('label.loading')?></div>");
				$("#subcounty_g").html("<div><?=lang('label.loading')?></div>");
				$("#facilities_g").html("<div><?=lang('label.loading')?></div>");	

				$("#partners_a").html("<div><?=lang('label.loading')?></div>");
				$("#subcounty_a").html("<div><?=lang('label.loading')?></div>");
				$("#facilities_a").html("<div><?=lang('label.loading')?></div>");

				$("#partners_na").html("<div><?=lang('label.loading')?></div>");
				$("#subcounty_na").html("<div><?=lang('label.loading')?></div>");
				$("#facilities_na").html("<div><?=lang('label.loading')?></div>");

				$("#subcounty").load("<?php echo base_url('charts/summaries/subcounty_listing/2/1');?>/"+data);
				$("#partners").load("<?php echo base_url('charts/summaries/partner_listing/3/1');?>/"+data);
				$("#facilities").load("<?php echo base_url('charts/summaries/site_listing/4/1');?>/"+data);

				$("#partners_g").load("<?php echo base_url('charts/summaries/partner_listing_gender/3/1');?>/"+data);
				$("#subcounty_g").load("<?php echo base_url('charts/summaries/subcounty_listing_gender/2/1');?>/"+data);
				$("#facilities_g").load("<?php echo base_url('charts/summaries/site_listing_gender/4/1');?>/"+data);


				$("#partners_a").load("<?php echo base_url('charts/summaries/partner_listing_age/3/1');?>/"+data);
				$("#subcounty_a").load("<?php echo base_url('charts/summaries/subcounty_listing_age/2/1');?>/"+data);
				$("#facilities_a").load("<?php echo base_url('charts/summaries/site_listing_age/4/1');?>/"+data);


				$("#partners_na").load("<?php echo base_url('charts/summaries/partner_listing_age_n/3/1');?>/"+data);
				$("#subcounty_na").load("<?php echo base_url('charts/summaries/subcounty_listing_age_n/2/1');?>/"+data);
				$("#facilities_na").load("<?php echo base_url('charts/summaries/site_listing_age_n/4/1');?>/"+data);

			
				$("#long_tracking").load("<?php echo base_url('charts/summaries/get_patients'); ?>/"+null+"/"+null+"/"+data);
				$("#current_sup").load("<?php echo base_url('charts/summaries/current_suppression'); ?>/"+data);
	        });
		});

		$("button").click(function () {
		    var first, second;
		    first = $(".date-picker[name=startDate]").val();
		    second = $(".date-picker[name=endDate]").val();
		    var all = localStorage.getItem("my_var");
		    
		    var new_title = set_multiple_date(first, second);

		    $(".display_date").html(new_title);
		    
		    from = format_date(first);
		    /* from is an array
		     	[0] => month
		     	[1] => year*/
		    to 	= format_date(second);
		    var error_check = check_error_date_range(from, to);
		    
		    if (!error_check) {
	        	$("#long_tracking").html("<center><div class='loader'></div></center>");
				
		 		$("#long_tracking").load("<?php echo base_url('charts/summaries/get_patients'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+null+"/"+to[1]+"/"+to[0]);
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
 		
 		$("#long_tracking").html("<center><div class='loader'></div></center>");

		$("#long_tracking").load("<?php echo base_url('charts/summaries/get_patients'); ?>/"+year+"/"+month);
	}

	function expand_modal(div_name){
		$(div_name).modal('show');
	}
	
</script>
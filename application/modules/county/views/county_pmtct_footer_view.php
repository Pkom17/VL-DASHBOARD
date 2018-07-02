<script type="text/javascript">
	$().ready(function(){
		localStorage.setItem("par_pmtct", 0);
		$.get("<?php echo base_url();?>template/dates", function(data){
    		obj = $.parseJSON(data);
	
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
    	});
		$("#second").hide();
		$("#third").hide();
		$("#fourth").hide();
		// fetching the partner outcomes
		$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>");

		$("#countydrp").change(function(){
			$("#county_pmtct").prop('selectedIndex', 0);
			em = $(this).val();
			var posting = $.post( "<?php echo base_url();?>template/filter_county_data", { county: em } );
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
	        	// Condition to dispay the proper divs based on whether a partner is selected or not
	        	data = $.parseJSON(data);
	        	if (data==null) {
	        		$("#first").show();
	        		$("#second").hide();
					$("#third").hide();
					$("#fourth").hide();
					// fetching the partner outcomes
					$("#partner_div").html("<center><div class='loader'></div></center>");
					$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+null+"/"+null+"/"+1);
	        	} else {
	        		$("#first").hide();
	        		$("#second").show();
					$("#third").hide();
					$("#fourth").hide();

					$("#pmtct_outcomes_div").html("<center><div class='loader'></div></center>");
					$("#pmtct_sup_outcomes_div").html("<center><div class='loader'></div></center>");

					$("#pmtct_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+1+"/"+null+"/"+null+"/"+null+"/"+null+"/"+data);
					$("#pmtct_sup_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+2+"/"+null+"/"+null+"/"+null+"/"+null+"/"+data);
	        	}
	        });
		});

		$("#county_pmtct").change(function(){
			em = $(this).val();
			
			localStorage.setItem("par_pmtct", em);
			$.get("<?= @base_url('county/check_county_select');?>", function(data){
				county = JSON.parse(data);
				if (county == 0) {
					alert("<?=  lang('alert_select_county')?>");
				}else {
					if (em == "NA") {
						$("#first").hide();
						$("#second").show();
						$("#third").hide();
						$("#fourth").show();

						$("#pmtct_outcomes_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_sup_outcomes_div").html("<center><div class='loader'></div></center>");
						$("pmtct_suppression_all_div").html("<center><div class='loader'></div></center>");
						$("pmtct_vl_outcomes_all_div").html("<center><div class='loader'></div></center>");

						$("#pmtct_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+1+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
						$("#pmtct_sup_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+2+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
						$("#pmtct_suppression_all_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
						$("#pmtct_vl_outcomes_all_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
					} else {
						$("#first").hide();
						$("#second").hide();
						$("#third").show();
						$("#fourth").hide();

						$("#pmtct_suppression_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_vl_outcomes_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_counties_listing_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_partners_listing_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_sites_listing_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_counties_outcomes_div").html("<center><div class='loader'></div></center>");

						$("#pmtct_suppression_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
						$("#pmtct_vl_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
						$("#pmtct_counties_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+county);
						$("#pmtct_partners_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+county);
						$("#pmtct_sites_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+county);
						$("#pmtct_counties_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+county);
					}
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
			    $.get("<?php echo base_url();?>county/check_county_select", function (data) {
					county = data;
					
					if (county==0) {
						$("#second").hide();
						$("#third").hide();
						$("#fourth").hide();
						// fetching the partner outcomes
						$("#partner_div").html("<center><div class='loader'></div></center>");
						$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+1+"/"+null+"/"+null+"/"+to[1]+"/"+to[0]);
					} else {
						county = $.parseJSON(county);
						var all = localStorage.getItem("par_pmtct");
						// console.log(all);
						if (all == 'NA') {
							$("#first").hide();
			        		$("#second").show();
							$("#third").hide();
							$("#fourth").show();

							$("#pmtct_outcomes_div").html("<center><div class='loader'></div></center>");
							$("#pmtct_sup_outcomes_div").html("<center><div class='loader'></div></center>");
							$("pmtct_suppression_all_div").html("<center><div class='loader'></div></center>");
							$("pmtct_vl_outcomes_all_div").html("<center><div class='loader'></div></center>");

							$("#pmtct_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+1+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+county);
							$("#pmtct_sup_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+2+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+county);
							$("#pmtct_suppression_all_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+county);
							$("#pmtct_vl_outcomes_all_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+county);
						} else {
							$("#first").hide();
			        		$("#second").hide();
							$("#third").show();
							$("#fourth").hide();

							$("#pmtct_suppression_div").html("<center><div class='loader'></div></center>");
							$("#pmtct_vl_outcomes_div").html("<center><div class='loader'></div></center>");
							$("#pmtct_counties_listing_div").html("<center><div class='loader'></div></center>");
							$("#pmtct_partners_listing_div").html("<center><div class='loader'></div></center>");
							$("#pmtct_sites_listing_div").html("<center><div class='loader'></div></center>");
							$("#pmtct_counties_outcomes_div").html("<center><div class='loader'></div></center>");

							$("#pmtct_suppression_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+county);
							$("#pmtct_vl_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+county);
							$("#pmtct_counties_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+county);
							$("#pmtct_partners_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+county);
							$("#pmtct_sites_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+county);
							$("#pmtct_counties_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+county);
						}
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
			
			$.get("<?php echo base_url();?>county/check_county_select", function (data) {
				county = JSON.parse(data);
				
				if (county==0) {
					$("#first").show();
					$("#second").hide();
					$("#third").hide();
					$("#fourth").hide();
					// fetching the partner outcomes
					$("#partner_div").html("<center><div class='loader'></div></center>");
					$("#partner_div").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/"+year+"/"+month+"/"+1);
				} else {
					var all = localStorage.getItem("par_pmtct");
					if (all == 'NA') {
						$("#first").hide();
		        		$("#second").show();
						$("#third").hide();
						$("#fourth").show();

						$("#pmtct_outcomes_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_sup_outcomes_div").html("<center><div class='loader'></div></center>");
						$("pmtct_suppression_all_div").html("<center><div class='loader'></div></center>");
						$("pmtct_vl_outcomes_all_div").html("<center><div class='loader'></div></center>");

						$("#pmtct_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+year+"/"+month+"/"+1+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
						$("#pmtct_sup_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+year+"/"+month+"/"+2+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
						$("#pmtct_suppression_all_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+year+"/"+month+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
						$("#pmtct_vl_outcomes_all_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+year+"/"+month+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
					} else {
						$("#first").hide();
		        		$("#second").hide();
						$("#third").show();
						$("#fourth").hide();

						$("#pmtct_suppression_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_vl_outcomes_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_counties_listing_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_partners_listing_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_sites_listing_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_counties_outcomes_div").html("<center><div class='loader'></div></center>");

						$("#pmtct_suppression_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
						$("#pmtct_vl_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+null+"/"+null+"/"+county);
						$("#pmtct_counties_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+county);
						$("#pmtct_partners_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+county);
						$("#pmtct_sites_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+county);
						$("#pmtct_counties_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+"/"+county);
					}
				}
			});
		});
	}
</script>
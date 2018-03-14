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
		$("#partner_div").load("<?php echo base_url('charts/sites/site_outcomes');?>");

		$("#site").change(function(){
			$('#site_pmtct').prop('selectedIndex',0);
			em = $(this).val();
			var posting = $.post("<?php echo base_url();?>template/filter_site_data", { site: em } );
			posting.done(function( data ) {
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
	        	// Condition to dispay the proper divs based on whether a partner is selected or not
	        	data = $.parseJSON(data);
	        	if (data==null) {
	        		$("#first").show();
	        		$("#second").hide();
					$("#third").hide();
					$("#fourth").hide();
					// fetching the partner outcomes
					$("#partner_div").html("<center><div class='loader'></div></center>");
					$("#partner_div").load("<?php echo base_url('charts/sites/site_outcomes');?>");
	        	} else {
	        		$("#first").hide();
	        		$("#second").show();
					$("#third").hide();
					$("#fourth").hide();

					$("#pmtct_outcomes_div").html("<center><div class='loader'></div></center>");
					$("#pmtct_sup_outcomes_div").html("<center><div class='loader'></div></center>");

					$("#pmtct_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+1+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+data);
					$("#pmtct_sup_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+2+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+data);
	        	}
	        });
		});

		$("#site_pmtct").change(function(){
			em = $(this).val();
			
			localStorage.setItem("par_pmtct", em);
			$.get("<?= @base_url('sites/check_site_select');?>", function(data){
				site = JSON.parse(data);
				if (site == 0) {
					alert("Select a facility first");
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

						$("#pmtct_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+1+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_sup_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+2+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_suppression_all_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_vl_outcomes_all_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
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

						$("#pmtct_suppression_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_vl_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_counties_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_partners_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_sites_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+null+"/"+null+"/"+em+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_counties_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct');?>/"+null+"/"+null+"/"+em);
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
			    $.get("<?php echo base_url();?>sites/check_site_select", function (data) {
					site = data;
					
					if (site==0) {
						$("#second").hide();
						$("#third").hide();
						$("#fourth").hide();
						// fetching the partner outcomes
						$("#partner_div").html("<center><div class='loader'></div></center>");
						$("#partner_div").load("<?php echo base_url('charts/sites/site_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
					} else {
						site = $.parseJSON(site);
						var all = localStorage.getItem("par_pmtct");
						if (all == 0 || all == '0') {
							$("#first").hide();
			        		$("#second").show();
							$("#third").hide();
							$("#fourth").show();

							$("#pmtct_outcomes_div").html("<center><div class='loader'></div></center>");
							$("#pmtct_sup_outcomes_div").html("<center><div class='loader'></div></center>");
							$("pmtct_suppression_all_div").html("<center><div class='loader'></div></center>");
							$("pmtct_vl_outcomes_all_div").html("<center><div class='loader'></div></center>");

							$("#pmtct_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+1+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
							$("#pmtct_sup_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+2+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
							$("#pmtct_suppression_all_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
							$("#pmtct_vl_outcomes_all_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
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

							$("#pmtct_suppression_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
							$("#pmtct_vl_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
							$("#pmtct_counties_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+site);
							$("#pmtct_partners_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+site);
							$("#pmtct_sites_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+site);
							$("#pmtct_counties_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct');?>/"+from[1]+"/"+from[0]+"/"+all+"/"+to[1]+"/"+to[0]);
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
			
			$.get("<?php echo base_url();?>sites/check_site_select", function (data) {
				site = JSON.parse(data);
				
				if (site==0) {
					$("#first").show();
					$("#second").hide();
					$("#third").hide();
					$("#fourth").hide();
					// fetching the partner outcomes
					$("#partner_div").html("<center><div class='loader'></div></center>");
					$("#partner_div").load("<?php echo base_url('charts/sites/site_outcomes'); ?>/"+year+"/"+month);
				} else {
					var all = localStorage.getItem("par_pmtct");
					if (all == 0 || all == '0') {
						$("#first").hide();
		        		$("#second").show();
						$("#third").hide();
						$("#fourth").show();

						$("#pmtct_outcomes_div").html("<center><div class='loader'></div></center>");
						$("#pmtct_sup_outcomes_div").html("<center><div class='loader'></div></center>");
						$("pmtct_suppression_all_div").html("<center><div class='loader'></div></center>");
						$("pmtct_vl_outcomes_all_div").html("<center><div class='loader'></div></center>");

						$("#pmtct_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+year+"/"+month+"/"+1+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_sup_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+year+"/"+month+"/"+2+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_suppression_all_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+year+"/"+month+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_vl_outcomes_all_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+year+"/"+month+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
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

						$("#pmtct_suppression_div").load("<?= @base_url('charts/pmtct/pmtct_suppression');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_vl_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_counties_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_partners_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_sites_listing_div").load("<?= @base_url('charts/pmtct/pmtct_breakdown');?>/"+year+"/"+month+"/"+all+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+site);
						$("#pmtct_counties_outcomes_div").load("<?= @base_url('charts/pmtct/pmtct');?>/"+year+"/"+month+"/"+all);
					}
				}
			});
		});
	}
</script>
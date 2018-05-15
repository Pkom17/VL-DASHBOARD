<script type="text/javascript">
	$(document).ready(function(){
		$("#first").show();
		$("#second").hide();
		$("#breadcrum").html('<?=lang('label.site_all.pmtct')?>');
		$.get("<?php echo base_url();?>template/dates", function(data){
    		obj = $.parseJSON(data);
	
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
    	});
    	$("#pmtct_outcomes").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+1+"/"+null+"/"+null+"/"+null+"/"+1);
		$("#pmtct_sup_outcomes").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+2+"/"+null+"/"+null+"/"+null+"/"+1);

		$("select").change(function(){
			em = $(this).val();
		    var posting = $.post( "<?php echo base_url();?>template/filter_pmtct_data", { pmtct: em } );
	     
	        // Put the results in a div
	        posting.done(function( data ) {
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

	        		$("#pmtct_outcomes").html("<center><div class='loader'></div></center>");
					$("#pmtct_sup_outcomes").html("<center><div class='loader'></div></center>");

	        		$("#pmtct_outcomes").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+1+"/"+null+"/"+null+"/"+null+"/"+1);
					$("#pmtct_sup_outcomes").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+null+"/"+null+"/"+2+"/"+null+"/"+null+"/"+null+"/"+1);
	        	} else {
	        		$("#first").hide();
	        		$("#second").show();

	        		$("#pmtct_testing_trends").html("<center><div class='loader'></div></center>");
					$("#pmtct_vlOutcomes").html("<center><div class='loader'></div></center>");
					$("#countiespmtct").html("<center><div class='loader'></div></center>");
					$("#partnerspmtct").html("<center><div class='loader'></div></center>");
					$("#subcountiespmtct").html("<center><div class='loader'></div></center>");
					$("#Facilitiespmtct").html("<center><div class='loader'></div></center>");

					$("#pmtct_testing_trends").load("<?= @base_url('charts/pmtct/pmtct_suppression'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+null+"/"+1);
					$("#pmtct_vlOutcomes").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+null+"/"+1);
					$("#countiespmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+1);
					$("#partnerspmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+null+"/"+null+"/"+1);
					$("#subcountiespmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+null+"/"+1);
					$("#Facilitiespmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+1);
					$("#countypmtct").load("<?= @base_url('charts/pmtct/pmtct'); ?>/"+null+"/"+null+"/"+data+"/"+null+"/"+null+"/"+1);
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
			    $.get("<?php echo base_url('pmtct/check_pmtct_select');?>", function( data ){
					data = JSON.parse(data);
					// console.log(data);
					if (data==0) {
						$("#second").hide();
		        		$("#first").show();

		        		$("#pmtct_outcomes").html("<center><div class='loader'></div></center>");
						$("#pmtct_sup_outcomes").html("<center><div class='loader'></div></center>");

		        		$("#pmtct_outcomes").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+1+"/"+to[1]+"/"+to[0]+"/"+null+"/"+1);
						$("#pmtct_sup_outcomes").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+2+"/"+to[1]+"/"+to[0]+"/"+null+"/"+1);

					} else {
						$("#first").hide();
		        		$("#second").show();

						$("#pmtct_testing_trends").html("<center><div class='loader'></div></center>");
						$("#pmtct_vlOutcomes").html("<center><div class='loader'></div></center>");
						$("#countiespmtct").html("<center><div class='loader'></div></center>");
						$("#partnerspmtct").html("<center><div class='loader'></div></center>");
						$("#subcountiespmtct").html("<center><div class='loader'></div></center>");
						$("#Facilitiespmtct").html("<center><div class='loader'></div></center>");

						$("#pmtct_testing_trends").load("<?= @base_url('charts/pmtct/pmtct_suppression'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+null+"/"+1);
						$("#pmtct_vlOutcomes").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+null+"/"+1);
						$("#countiespmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+1);
						$("#partnerspmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+1);
						$("#subcountiespmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+null+"/"+1);
						$("#Facilitiespmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+null+"/"+null+"/"+null+"/"+1);
						$("#countypmtct").load("<?= @base_url('charts/pmtct/pmtct'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]+"/"+1);
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

			$.get("<?php echo base_url('pmtct/check_pmtct_select');?>", function( data ){
				data = $.parseJSON(data);
				if (data==0) {
					$("#second").hide();
		        		$("#first").show();

		        		$("#pmtct_outcomes").html("<center><div class='loader'></div></center>");
						$("#pmtct_sup_outcomes").html("<center><div class='loader'></div></center>");

		        		$("#pmtct_outcomes").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+year+"/"+month+"/"+1+"/"+null+"/"+null+"/"+null+"/"+1);
						$("#pmtct_sup_outcomes").load("<?= @base_url('charts/pmtct/pmtct_outcomes'); ?>/"+year+"/"+month+"/"+2+"/"+null+"/"+null+"/"+null+"/"+1);
				} else {
					$("#first").hide();
		        		$("#second").show();

						$("#pmtct_testing_trends").html("<center><div class='loader'></div></center>");
						$("#pmtct_vlOutcomes").html("<center><div class='loader'></div></center>");
						$("#countiespmtct").html("<center><div class='loader'></div></center>");
						$("#partnerspmtct").html("<center><div class='loader'></div></center>");
						$("#subcountiespmtct").html("<center><div class='loader'></div></center>");
						$("#Facilitiespmtct").html("<center><div class='loader'></div></center>");

						$("#pmtct_testing_trends").load("<?= @base_url('charts/pmtct/pmtct_suppression'); ?>/"+year+"/"+month+"/"+data+"/"+null+"/"+null+"/"+null+"/"+1);
						$("#pmtct_vlOutcomes").load("<?= @base_url('charts/pmtct/pmtct_vl_outcomes'); ?>/"+year+"/"+month+"/"+data+"/"+null+"/"+null+"/"+null+"/"+1);
						$("#countiespmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+year+"/"+month+"/"+data+"/"+null+"/"+null+"/"+1);
						$("#partnerspmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+year+"/"+month+"/"+data+"/"+null+"/"+null+"/"+null+"/"+null+"/"+1);
						$("#subcountiespmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+year+"/"+month+"/"+data+"/"+null+"/"+null+"/"+null+"/"+1);
						$("#Facilitiespmtct").load("<?= @base_url('charts/pmtct/pmtct_breakdown'); ?>/"+year+"/"+month+"/"+data+"/"+null+"/"+null+"/"+null+"/"+null+"/"+null+"/"+1);
						$("#countypmtct").load("<?= @base_url('charts/pmtct/pmtct'); ?>/"+year+"/"+month+"/"+data+"/"+null+"/"+null+"/"+1);
				}
			});
			
		}); 
	}
</script>
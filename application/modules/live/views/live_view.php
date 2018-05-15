<style type="text/css">
	@import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700");
	@import url("https://fonts.googleapis.com/css?family=Roboto:400,300,500,700");
	/*
	 *
	 *   INSPINIA - Responsive Admin Theme
	 *   version 2.6.2
	 *
	*/
	h1,
	h2,
	h3,
	h4,
	h5,
	h6 {
	  font-weight: 100;
	}
	h1 {
	  font-size: 30px;
	}
	h2 {
	  font-size: 24px;
	}
	h3 {
	  font-size: 16px;
	}
	h4 {
	  font-size: 14px;
	}
	h5 {
	  font-size: 12px;
	}
	h6 {
	  font-size: 10px;
	}
	h3,
	h4,
	h5 {
	  margin-top: 5px;
	  font-weight: 600;
	}
</style>
<div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="ibox-title">
		            <h5><?=lang('label.date')?>  <span id="time_now"></span></h5>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-md-4">
		<div id="breadcrum" class="alert" style="background-color: #1BA39C;text-align: center;vertical-align: middle;" onclick="switch_source()">
      		<span id="current_source"><?=lang('label.live_viralload')?> </span> <?=lang('label.Click_to_switch')?>	
    	</div>
		     
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="ibox-title">
		            <h5><?=lang('label.live_last.update')?> <span id="last_updated"></span></h5>
		        </div>
		    </div>
		</div>
	</div>
</div>

<div class="row">
	
</div>
<div class="row">
	<div class="col-md-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="ibox float-e-margins">
		            <div class="ibox-title">
		                <h5><?=lang('label.live_year_date_month.date')?></h5>
		            </div>
		            <div class="ibox-content">
		                <h1 class="no-margins stat-percent font-bold text-success" id="year_to_date" style="font-size:24px;"><?=lang('number.year_to_date')?> </h1>
		            </div>
		        </div>
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="ibox float-e-margins">
		            <div class="ibox-title">
		                <!-- <span class="label label-success pull-right">Monthly</span> -->
		                <h5><?=lang('label.live_received.samples')?></h5>
		            </div>
		            <div class="ibox-content">
		                <h1 class="no-margins stat-percent font-bold text-success" id="received_samples"><?=lang('number.received_samples')?></h1>
		                <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>
		                <small>Total income</small> -->
		            </div>
		        </div>
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="ibox float-e-margins">
		            <div class="ibox-title">
		                <h5><?=lang('label.live_Waiting_Testing.samples')?></h5>
		            </div>
		            <div class="ibox-content">
		                <h1 class="no-margins stat-percent font-bold text-success" id="inqueue_samples"><?=lang('number.inqueue_samples')?></h1>
		            </div>
		        </div>
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="ibox float-e-margins">
		            <div class="ibox-title">
		                <h5><?=lang('label.live_in_process.samples')?></h5>
		            </div>
		            <div class="ibox-content">
		                <h1 class="no-margins stat-percent font-bold text-success" id="inprocess_samples"><?=lang('number.inprocess_samples')?></h1>
		            </div>
		        </div>
			</div>
		</div>
	</div>
	
	<div class="col-md-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="ibox float-e-margins">
		            <div class="ibox-title">
		                <h5><?=lang('label.live_results_pending.approval')?></h5>
		            </div>
		            <div class="ibox-content">
		                <h1 class="no-margins stat-percent font-bold text-success" id="pending_approval"><?=lang('number.pending_approval')?></h1>
		            </div>
		        </div>
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="ibox float-e-margins">
		            <div class="ibox-title">
		                <h5><?=lang('label.live_results_dispatched.today')?></h5>
		            </div>
		            <div class="ibox-content">
		                <h1 class="no-margins stat-percent font-bold text-success" id="dispatched_results"><?=lang('number.dispatched_results')?></h1>
		            </div>
		        </div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_sample.entry')?></h3>
			</div>
			<div class="panel-body">
				<div id="sampleEntry"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_sample_entry_vs.received')?></h3>
			</div>
			<div class="panel-body">
				<div id="sampleEntryVsReceived"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_site_entry.bylab')?></h3>
			</div>
			<div class="panel-body">
				<div id="siteEntryLab"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_received_samples.bylab')?></h3>
			</div>
			<div class="panel-body">
				<div id="receivedSampleLab"></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_inqueue_samples_by.lab')?></h3>
			</div>
			<div class="panel-body">
				<div id="inqueueLabs"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_in_process_samples_by.lab')?></h3>
			</div>
			<div class="panel-body">
				<div id="inprocessLabs"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_in_process.platform')?></h3>
			</div>
			<div class="panel-body">
				<div id="inprocessPlatform"></div>
			</div>
		</div>
	</div>
	<!-- <div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Processed Samples</h3>
			</div>
			<div class="panel-body">
				<div id="processedSamples"></div>
			</div>
		</div>
	</div> -->
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_pends.approval')?></h3>
			</div>
			<div class="panel-body">
				<div id="pendsApproval"></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_dispatched.results')?></h3>
			</div>
			<div class="panel-body">
				<div id="dispatchedResults"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_oldest_samples_in.queue')?></h3>
			</div>
			<div class="panel-body">
				<div id="oldestSamples"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_ageing_samples.national')?></h3>
			</div>
			<div class="panel-body">
				<div id="aging_samples_national"></div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?=lang('label.live_Ageing_amples_based_date.drawn')?></h3>
			</div>
			<div class="panel-body">
				<select id="lab_dropdown" name="ny" class="form-control">
					<option><?=lang('label.live_select.something')?></option>
				</select>
				
				<div id="aging_samples"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function() {
	

	localStorage.setItem("my_url", "charts/live/get_data/2/");
	localStorage.setItem("my_var", 0);
	localStorage.setItem("my_lab", 1);
	ajaxd();
    setInterval("ajaxd()", 60000);

    $("#lab_dropdown").load("<?php echo base_url('charts/live/get_dropdown');?>");
    

    $("select").change(function(){
		em = $(this).val();
		localStorage.setItem("my_lab", em);
		ajaxd();
	});
});

function switch_source(){
	var a = localStorage.getItem("my_var");

	if(a == 0){
		localStorage.setItem("my_var", 1);
		localStorage.setItem("my_url", "charts/live/get_data/1/");
		$("#current_source").html('EID');
	}
	else{
		localStorage.setItem("my_var", 0);
		localStorage.setItem("my_url", "charts/live/get_data/2/");
		$("#current_source").html('<?=lang('label.live_viralload');?>');
	}
	ajaxd();
}

function ajaxd(){
	//alert("this");
	var this_url = "<?php echo base_url();?>" + localStorage.getItem("my_url") + localStorage.getItem("my_lab");
	$.ajax({
	   type: "GET",
	   url: this_url,
	   success: function(msg){
	     var ob = JSON.parse(msg);

	     $("#time_now").html(ob.updated);
	     $("#last_updated").html(ob.updated_time);

	     $("#year_to_date").html(ob.year_to_date);
	     $("#received_samples").html(ob.receivedsamples);
	     $("#inqueue_samples").html(ob.inqueuesamples);
	     $("#inprocess_samples").html(ob.inprocesssamples);
	     $("#pending_approval").html(ob.pendingapproval);
	     $("#dispatched_results").html(ob.dispatchedresults);


	     set_graph("#sampleEntry", "column", ["<?=lang('label.entered_at_site');?>","<?=lang('label.entered_at_lab');?>"], [ob.enteredsamplesatsite, ob.enteredsamplesatlab], '<?=lang('label.samples');?>');
	     set_graph("#sampleEntryVsReceived", "column", ["<?=lang('label.entered_received_same_day');?>","<?=lang('label.entered_not_received_same_day');?>"], [ob.enteredreceivedsameday, ob.enterednotreceivedsameday], '<?=lang('label.samples');?>');
	     set_graph("#siteEntryLab", "bar", ob.labs, ob.enteredsamplesatsitea, '<?=lang('label.samples');?>');
	     set_graph("#receivedSampleLab", "bar", ob.labs, ob.receivedsamplesa, '<?=lang('label.samples');?>');
	     set_graph("#inqueueLabs", "bar", ob.labs, ob.inqueuesamplesa, '<?=lang('label.samples');?>');
	     set_graph("#inprocessLabs", "bar", ob.labs, ob.inprocesssamplesa, '<?=lang('label.samples');?>');
	     // set_graph("#processedSamples", "bar", ob.labs, ob.processedsamplesa, 'samples');
	     set_graph("#pendsApproval", "bar", ob.labs, ob.pendingapprovala, '<?=lang('label.samples');?>');
	     set_graph("#dispatchedResults", "bar", ob.labs, ob.dispatchedresultsa, '<?=lang('label.samples');?>');
	     set_graph("#oldestSamples", "bar", ob.labs, ob.oldestinqueuesamplea, '<?=lang('label.days');?>');
	     set_graph("#inprocessPlatform", "column", ob.machines, ob.minprocess, '<?=lang('label.samples');?>');
	     set_graph("#aging_samples_national", "bar", ob.age_cat, ob.age_nat, '<?=lang('label.samples');?>');
	     set_graph("#aging_samples", "bar", ob.age_cat, ob.age, '<?=lang('label.samples');?>');


	   }
	 });
}


function set_graph(div_name, chart_type, xcategories, ydata, ytitle){
	//alert(s);

	$(function () {
	    $(div_name).highcharts({
	        chart: {
	            type: chart_type
	        },
	        title: {
	            text: ''
	        },
	        xAxis: {
	            categories: xcategories
	        },
	        yAxis: {
	            min: 0,
	            title: {
	                text: ytitle
	            },
	            stackLabels: {
	            	rotation: -75,
	                enabled: true,
	                style: {
	                    fontWeight: 'bold',
	                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
	                },
	                y:-20
	            }
	        },
	        legend: {
	        	enabled: false
	        },
	        tooltip: {
	            headerFormat: '<b>{point.x}</b><br/>',
	            pointFormat: '{series.name}: {point.y}<br/>% <?=lang('label.contribution');?> {point.percentage:.1f}%'
	        },
	        plotOptions: {
	            column: {
	                stacking: 'normal',
	                dataLabels: {
	                    enabled: false,
	                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
	                    style: {
	                        textShadow: '0 0 3px black'
	                    }
	                }
	            }
	        },colors: [
		        '#1BA39C'
		    ],
	        series: [
	        			{
	        				"data": ydata,
	        				"name": "<?=lang('label.total');?>"
	        			}
	        		]
	        });
	});

}



</script>
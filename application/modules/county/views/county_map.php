<div class="col-md-12" id ="buttons">
    
        <button onclick="retrieve_map('<?=lang('label.tests')?>', 'counties_tests', '');"><?=lang('label.tests')?></button>
        <button onclick="retrieve_map('<?=lang('label.suppressed')?>', 'counties_suppressed', '%');"><?=lang('label.suppressed')?></button>
        <button  onclick="retrieve_map('<?=lang('label.non_suppressed')?>', 'counties_non_suppressed', '%');"><?=lang('label.non_suppressed')?></button>
        <button onclick="retrieve_map('<?=lang('label.rejected')?>', 'counties_rejects', '%');"><?=lang('label.rejected')?></button>
        <button onclick="retrieve_map('<?=lang('label.preganant_mothers')?>', 'counties_pregnant', '');"><?=lang('label.preganant_mothers')?></button>
        <button onclick="retrieve_map('<?=lang('label.lactating_mothers')?>', 'counties_lactating', '');"><?=lang('label.lactating_mothers')?></button>
   
</div>

<div id="counties_map" style="height: 600px; " class="col-md-6">

</div>

<div class="col-md-6">

    <div id="Table_Title"></div>

    <div id="table" ></div>
    

</div>

<div id="county_name" class="col-lg-12">

</div>

<div id="county_details" class="col-lg-12">

</div>

<script type="text/javascript">

    var this_month = null;
    var this_year = null;



    function set_summary(county_id, county_name){
        $("#table").empty().
        load("<?php echo base_url('charts/counties/county_summary'); ?>/" + county_id + "/" + this_year + "/" + this_month);

        $("#Table_Title").empty().append("<br /><h2>" + county_name + set_title() + "</h2>");
    }

    function set_table(county_id, county_name){
        set_summary(county_id, county_name);
        $("#county_details").empty().
        load("<?php echo base_url('charts/counties/county_details'); ?>/" + county_id + "/" + this_year + "/" + this_month);

        $("#county_name").empty().append("<br /><br /><h2>" + county_name + set_title() + "</h2>");
    }

    function set_title(){

        switch(
            this_month
            ){
            case 1:
                return ' <?=  lang('cal_jan')?> ' + this_year;
                break;
            case 2:
                return ' <?=  lang('cal_feb')?> ' + this_year;
                break;
            case 3:
                return ' <?=  lang('cal_mar')?> ' + this_year;
                break;
            case 4:
                return ' <?=  lang('cal_apr')?> ' + this_year;
                break;
            case 5:
                return ' <?=  lang('cal_may')?> ' + this_year;
                break;
            case 6:
                return ' <?=  lang('cal_jun')?> ' + this_year;
                break;
            case 7:
                return ' <?=  lang('cal_jul')?> ' + this_year;
                break;
            case 8:
                return ' <?=  lang('cal_aug')?> ' + this_year;
                break;
            case 9:
                return ' <?=  lang('cal_sep')?> ' + this_year;
                break;
            case 10:
                return ' <?=  lang('cal_oct')?> ' + this_year;
                break;
            case 11:
                return ' <?=  lang('cal_nov')?> ' + this_year;
                break;
            case 12:
                return ' <?=  lang('cal_dec')?> ' + this_year;
                break;
            default:
                return ' ' + this_year;
                break;
            
        }
    }


    // Uses the data to make the map
    function create_map(map_title, MapData, suffix){
        // Initiate the chart
        $('#counties_map').highcharts('Map', {
                title: {
                text: map_title + set_title()
            },
             legend: {
                enabled: true
            },
            
            
            series: [
                {
                    "name" : map_title,
                    "type": "map",
                    "mapData": kenya_map,
                    "data" : MapData,
                    "joinBy": ['id', 'id'],
                    "dataLabels": {
                            enabled: true,
                            color: '#FFFFFF',
                            format: '{point.name}'
                        },
                    "point":{
                        "events":{
                            click: function(){
                                set_table(this.id, this.name);
                            }
                        }
                    },
                    "tooltip": {
                        "valueSuffix": suffix
                    }
                }
            ],

            colorAxis: {},

            mapNavigation: {
                enabled: true,
                enableButtons: true
            },
       
        
        });
    }

    // Calls the counties model to retrieve map data
    function retrieve_map(map_title, func_path, suffix){

        $.ajax({
           url: "<?php echo base_url('charts/counties/'); ?>/" + func_path,
           
           error: function() {
              alert("<?=  lang('alert_error')?>");
           },
           dataType : "json",
           success: function(data) {
                var return_data = [];
                $.each(data, function(key, value){
                    return_data.push(value);
                });

                create_map(map_title, return_data, suffix);
                
                //$("#counties_map").append(data));
           },
           type: 'GET'
        });

    }

    function date_filter(criteria, id)
    {
        if (criteria === "monthly") {
            year = null;
            month = id;
            this_month = id;
        }else {
            year = id;
            this_year = id;
            month = null;
        }

       

        var posting = $.post( '<?php echo base_url();?>county/set_filter_date', { 'year': year, 'month': month } );

        // Put the results in a div
        posting.done(function( data ) {
            obj = $.parseJSON(data);
            
            if(obj['month'] == "null" || obj['month'] == null){
                obj['month'] = "";
            }
            $(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
        });
        
       
    }


    // Creates the default map
    $(function () {
        this_month = <?php 
            if($this->session->userdata('filter_month'))
                {echo $this->session->userdata('filter_month');
            }else{ echo 0;} ?>;

        this_year = <?php echo $this->session->userdata('filter_year'); ?>;
        retrieve_map("<?=  lang('label_suppressed')?>", "counties_suppressed", '%');

    });

    

</script>


<div class="row">
    <div style="color:red;"><center><?= lang('label.click_on_selected') ?></center></div>

    <div  class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="min-height: 4em;"> 
                <div class="chart_title vl_part_heading">
                    <?= lang('title_testing_trends'); ?>
                </div>
                <div class="display_date"></div>
            </div>
            <div class="panel-body" id="stacked_graph">
                <center><div class="loader"></div></center>
            </div>
        </div>


    </div>

    <div id="graphs">

    </div>



</div>




<script type="text/javascript">

    $().ready(function () {
        $("#year-month-filter").hide();
        $("#graphs").load("<?php echo base_url(); ?>charts/partner_trends/positive_trends");
        $("#stacked_graph").load("<?php echo base_url(); ?>charts/partner_trends/summary");


        $("select").change(function () {
            var partner_id = $(this).val();

            var posting = $.post("<?php echo base_url(); ?>template/filter_partner_data", {partner: partner_id});
            posting.done(function (data) {
                /* $.get("<?php echo base_url(); ?>template/breadcrum/"+data+"/"+1, function(data){
                 $("#breadcrum").html(data);
                 });*/
            });

            $("#graphs").load("<?php echo base_url(); ?>charts/partner_trends/positive_trends/" + partner_id);
            $("#stacked_graph").load("<?php echo base_url(); ?>charts/partner_trends/summary/" + partner_id);
        });
    });

    $("select").change(function () {
        var partner_id = $(this).val();

        $("#graphs").load("<?php echo base_url(); ?>charts/partner_trends/positive_trends/" + partner_id);
        $("#stacked_graph").load("<?php echo base_url(); ?>charts/partner_trends/summary/" + partner_id);
    });

    function get_graphs(year) {
        $.ajax({
            url: "<?php echo base_url('charts/partner_trends/positive_trends'); ?>/" + year,

            error: function (data) {
                $("#test").append(data);
            },
            dataType: "json",
            success: function (data) {


                $("#graphs").empty().append(data);
            },
            type: 'GET'
        });
    }



</script>
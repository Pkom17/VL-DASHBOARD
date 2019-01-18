<script type="text/javascript">
    $(document).ready(function () {
        localStorage.setItem("view_cpart1", 0);
        localStorage.setItem("view_cpart2", 0);
        $.get("<?php echo base_url(); ?>template/dates", function (data) {
            obj = $.parseJSON(data);

            if (obj['month'] == "null" || obj['month'] == null) {
                obj['month'] = "";
            }
            $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
            $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");
        });
        $("#partner_counties").hide();
        $("#partners_all").show();
        $("#partnerOutcomes").load("<?php echo base_url('charts/sites/site_outcomes'); ?>");

        $("select").change(function () {
            $("#partnerCounties").html("<center><div class='loader'></div></center>");
            em = $(this).val();

            // Send the data using post
            var posting = $.post("<?php echo base_url(); ?>template/filter_partner_data", {partner: em});

            posting.done(function (data) {
                if (data == "") {
                    data = 0;
                }
                /*$.get("<?php echo base_url(); ?>template/breadcrum/"+data+"/"+1, function(data){
                 
                 $("#breadcrum").html(data);
                 });*/
            });
            if (em == "NA") {
                $("#partner_counties").hide();
                $("#partners_all").show();
                $("#partnerOutcomes").html("<center><div class='loader'></div></center>");
                $("#partnerOutcomes").load("<?php echo base_url('charts/sites/site_outcomes'); ?>");
            } else {
                $("#partners_all").hide();
                $("#partner_counties").show();

                $("#partnerCounties").html("<center><div class='loader'></div></center>");
                $("#partnerCounties").load("<?php echo base_url('charts/partner_summaries/partner_counties_table'); ?>/" + null + "/" + null + "/" + em);

                $("#partnerCountyOutcomes").html("<center><div class='loader'></div></center>");
                $("#partnerCountyOutcomes").load("<?php echo base_url('charts/partner_summaries/partner_counties_outcomes'); ?>/" + null + "/" + null + "/" + em);
            }
        });

        // $("#partner_sites_excels").click(function(){
        // 	$(location).("<?php echo base_url('partner/excel_test'); ?>");
        // });
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
        to = format_date(second);
        var error_check = check_error_date_range(from, to);

        if (!error_check) {
            $.get("<?php echo base_url(); ?>partner/check_partner_select", function (data) {
                var partner = data;
                partner = $.parseJSON(partner);

                if (partner == 0) {
                    $("#partner_counties").hide();
                    $("#partners_all").show();
                    $("#partnerOutcomes").html("<center><div class='loader'></div></center>");
                    $("#partnerOutcomes").load("<?php echo base_url('charts/sites/site_outcomes'); ?>/" + from[1] + "/" + from[0] + "/" + null + "/" + to[1] + "/" + to[0]);
                } else {
                    $("#partners_all").hide();
                    $("#partner_counties").show();

                    $("#partnerCountyOutcomes").html("<center><div class='loader'></div></center>");
                    $("#partnerCountyOutcomes").load("<?php echo base_url('charts/partner_summaries/partner_counties_outcomes'); ?>/" + from[1] + "/" + from[0] + "/" + partner + "/" + to[1] + "/" + to[0]);

                    $("#partnerCounties").html("<center><div class='loader'></div></center>");
                    $("#partnerCounties").load("<?php echo base_url('charts/partner_summaries/partner_counties_table'); ?>/" + from[1] + "/" + from[0] + "/" + partner + "/" + to[1] + "/" + to[0]);
                }
            });
        }

    });
    function date_filter(criteria, id)
    {
        // console.log(criteria+":"+id);
        if (criteria === "monthly") {
            year = null;
            month = id;
        } else {
            year = id;
            month = null;
        }

        var posting = $.post('<?php echo base_url(); ?>template/filter_date_data', {'year': year, 'month': month});

        // Put the results in a div
        posting.done(function (data) {
            obj = $.parseJSON(data);

            if (obj['month'] == "null" || obj['month'] == null) {
                obj['month'] = "";
            }
            $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
            $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");

            $.get("<?php echo base_url(); ?>partner/check_partner_select", function (data) {
                var partner = data;
                partner = $.parseJSON(partner);

                if (partner == 0) {
                    $("#partner_counties").hide();
                    $("#partners_all").show();
                    $("#partnerOutcomes").html("<center><div class='loader'></div></center>");
                    $("#partnerOutcomes").load("<?php echo base_url('charts/sites/site_outcomes'); ?>/" + year + "/" + month);
                } else {
                    $("#partners_all").hide();
                    $("#partner_counties").show();

                    $("#partnerCountyOutcomes").html("<center><div class='loader'></div></center>");
                    $("#partnerCountyOutcomes").load("<?php echo base_url('charts/partner_summaries/partner_counties_outcomes'); ?>/" + year + "/" + month + "/" + partner);

                    $("#partnerCounties").html("<center><div class='loader'></div></center>");
                    $("#partnerCounties").load("<?php echo base_url('charts/partner_summaries/partner_counties_table'); ?>/" + year + "/" + month + "/" + partner);
                }
            });
        });
    }

    function switch_source_cpart1() {
        var view = localStorage.getItem("view_cpart1");
        if (view == 0) {
            localStorage.setItem("view_cpart1", 1);
            $("#vl_county_pie_tests").hide();
            $("#vl_county_pie_pat").show();
            $("#switchButton_cpart1").val('<?= lang('label.switch_all_tests') ?>');
            $(".vl_subcounty_heading").html('<?= lang('title_patient_partner_outcomes') ?> ');
            $('#vl_county_pie_pat').highcharts().reflow();
        } else {
            localStorage.setItem("view_cpart1", 0);
            $("#vl_county_pie_tests").show();
            $("#vl_county_pie_pat").hide();
            $("#switchButton_cpart1").val('<?= lang('label.switch_routine_tests_trends') ?> ');
            $(".vl_subcounty_heading").html('<?= lang('title_test_partner_outcomes') ?> ');
            $('#vl_county_pie_tests').highcharts().reflow();
        }
    }
    function switch_source_cpart2() {
        var view = localStorage.getItem("view_cpart2");
        if (view == 0) {
            localStorage.setItem("view_cpart2", 1);
            $("#vl_county_pie_testspvl").hide();
            $("#vl_county_pie_patpvl").show();
            $("#switchButton_cpart2").val('<?= lang('label.switch_all_tests') ?>');
            $(".vl_subcounty_heading").html('<?= lang('title_tested_patients_by_county') ?> ');
            $('#vl_county_pie_patpvl').highcharts().reflow();
        } else {
            localStorage.setItem("view_cpart2", 0);
            $("#vl_county_pie_testspvl").show();
            $("#vl_county_pie_patpvl").hide();
            $("#switchButton_cpart2").val('<?= lang('label.switch_routine_tests_trends') ?> ');
            $(".vl_subcounty_heading").html('<?= lang('title_test_done_by_county') ?> ');
            $('#vl_county_pie_testspvl').highcharts().reflow();
        }
    }
</script>
<script type="text/javascript">
    $().ready(function () {
        localStorage.setItem("view_cat_age", 0);
        $.get("<?php echo base_url(); ?>template/dates", function (data) {
            obj = $.parseJSON(data);

            if (obj['month'] == "null" || obj['month'] == null) {
                obj['month'] = "";
            }
            $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
            $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");
        });
        $("#second").hide();
        var view_age_cat = localStorage.getItem("view_cat_age");
        //$("#age_outcomes").load("<?php echo base_url('charts/ages/age_outcomes_sup'); ?>");
        if (view_age_cat == 0) {
            $("#age_outcomes").load("<?php echo base_url('charts/ages/age_outcomes_sup'); ?>");
        } else {
            $("#age_outcomes").load("<?php echo base_url('charts/summaries/p_age'); ?>");
        }

        $("select").change(function () {
            em = $(this).val();
            var view_age_cat = localStorage.getItem("view_cat_age");
            // console.log(em);
            // Send the data using post
            var posting = $.post("<?php echo base_url(); ?>template/filter_age_category_data", {age_cat: em});

            //      // Put the results in a div
            posting.done(function (age) {

                $.get("<?php echo base_url(); ?>template/dates", function (data2) {
                    obj = $.parseJSON(data2);

                    if (obj['month'] == "null" || obj['month'] == null) {
                        obj['month'] = "";
                    }
                    $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
                    $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");
                });
                if (age == 48 || age == "48") {
                    $("#second").hide();
                    $("#first").show();

                    $("#age_outcomes").html("<center><div class='loader'></div></center>");
                    //$("#age_outcomes").load("<?php echo base_url('charts/ages/age_outcomes_sup'); ?>");
                    if (view_age_cat == 0) {
                        $("#age_outcomes").load("<?php echo base_url('charts/ages/age_outcomes_sup'); ?>");
                    } else {
                        $("#age_outcomes").load("<?php echo base_url('charts/summaries/p_age'); ?>");
                    }
                } else {
                    $("#first").hide();
                    $("#second").show();

                    $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                    $("#gender").html("<center><div class='loader'></div></center>");
                    $("#samples").html("<center><div class='loader'></div></center>");
                    $("#county").html("<center><div class='loader'></div></center>");
                    $("#countiesAge").html("<center><div class='loader'></div></center>");
                    $("#partnersAge").html("<center><div class='loader'></div></center>");
                    $("#facilitiesAge").html("<center><div class='loader'></div></center>");
                    $("#subcountiesAge").html("<center><div class='loader'></div></center>");

                    $("#vlOutcomes").load("<?php echo base_url('charts/ages/age_vl_outcome'); ?>");
                    $("#gender").load("<?php echo base_url('charts/ages/age_gender'); ?>/" + null + "/" + null + "/" + age);
                    $("#samples").load("<?php echo base_url('charts/ages/sample_types'); ?>/" + null + "/" + age);
                    $("#county").load("<?php echo base_url('charts/ages/age_county_outcomes'); ?>/" + null + "/" + null + "/" + age);
                    $("#countiesAge").load("<?= base_url('charts/ages/age_breakdowns'); ?>/" + null + "/" + null + "/" + age + "/" + null + "/" + null + "/" + 1);
                    $("#partnersAge").load("<?php echo base_url('charts/ages/age_breakdowns'); ?>/" + null + "/" + null + "/" + age + "/" + null + "/" + null + "/" + null + "/" + 1);
                    $("#facilitiesAge").load("<?php echo base_url('charts/ages/age_breakdowns'); ?>/" + null + "/" + null + "/" + age + "/" + null + "/" + null + "/" + null + "/" + null + "/" + null + "/" + 1);
                    $("#subcountiesAge").load("<?php echo base_url('charts/ages/age_breakdowns'); ?>/" + null + "/" + null + "/" + age + "/" + null + "/" + null + "/" + null + "/" + null + "/" + 1);
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
            to = format_date(second);
            var error_check = check_error_date_range(from, to);
            var view_age_cat = localStorage.getItem("view_cat_age");
            if (!error_check) {
                $.get("<?php echo base_url('age/check_age_select'); ?>", function (data) {
                    data = $.parseJSON(data);
                    if (data == 0) {
                        $("#second").hide();
                        $("#first").show();

                        $("#age_outcomes").html("<center><div class='loader'></div></center>");
                        //$("#age_outcomes").load("<?php echo base_url('charts/ages/age_outcomes_sup'); ?>/" + from[1] + "/" + from[0] + "/" + to[1] + "/" + to[0]);
                        if (view_age_cat == 0) {
                            $("#age_outcomes").load("<?php echo base_url('charts/ages/age_outcomes_sup'); ?>/" + from[1] + "/" + from[0] + "/" + to[1] + "/" + to[0]);
                        } else {
                            $("#age_outcomes").load("<?php echo base_url('charts/summaries/p_age'); ?>/" + from[1] + "/" + from[0] + "/" + null + "/" + null + "/" + null + "/" + null + "/" + to[1] + "/" + to[0]);
                        }
                    } else {
                        $("#first").hide();
                        $("#second").show();

                        $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                        $("#gender").html("<center><div class='loader'></div></center>");
                        $("#samples").html("<center><div class='loader'></div></center>");
                        $("#county").html("<center><div class='loader'></div></center>");
                        $("#countiesAge").html("<center><div class='loader'></div></center>");
                        $("#partnersAge").html("<center><div class='loader'></div></center>");
                        $("#facilitiesAge").html("<center><div class='loader'></div></center>");
                        $("#subcountiesAge").html("<center><div class='loader'></div></center>");

                        $("#vlOutcomes").load("<?php echo base_url('charts/ages/age_vl_outcome'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0]);
                        $("#gender").load("<?php echo base_url('charts/ages/age_gender'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0]);
                        $("#samples").load("<?php echo base_url('charts/ages/sample_types'); ?>/" + from[1] + "/" + data);
                        $("#county").load("<?php echo base_url('charts/ages/age_county_outcomes'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0]);
                        $("#countiesAge").load("<?= base_url('charts/ages/age_breakdowns'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0] + "/" + 1);
                        $("#partnersAge").load("<?php echo base_url('charts/ages/age_breakdowns'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0] + "/" + null + "/" + 1);
                        $("#facilitiesAge").load("<?php echo base_url('charts/ages/age_breakdowns'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0] + "/" + null + "/" + null + "/" + null + "/" + 1);
                        $("#subcountiesAge").load("<?php echo base_url('charts/ages/age_breakdowns'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0] + "/" + null + "/" + null + "/" + 1);
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
        } else {
            year = id;
            month = null;
        }
        // console.log(year+"<___>"+month);
        var posting = $.post('<?php echo base_url(); ?>template/filter_date_data', {'year': year, 'month': month});
        var view_age_cat = localStorage.getItem("view_cat_age");
        // Put the results in a div
        posting.done(function (data) {
            obj = $.parseJSON(data);

            if (obj['month'] == "null" || obj['month'] == null) {
                obj['month'] = "";
            }
            $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
            $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");

            $.get("<?php echo base_url('age/check_age_select'); ?>", function (data) {
                data = $.parseJSON(data);
                if (data == 0) {
                    $("#second").hide();
                    $("#first").show();

                    $("#age_outcomes").html("<center><div class='loader'></div></center>");
                    // $("#age_outcomes").load("<?php echo base_url('charts/ages/age_outcomes_sup'); ?>/" + year + "/" + month);
                    if (view_age_cat == 0) {
                        $("#age_outcomes").load("<?php echo base_url('charts/ages/age_outcomes_sup'); ?>/" + year + "/" + month);
                    } else {
                        $("#age_outcomes").load("<?php echo base_url('charts/summaries/p_age'); ?>/" + year + "/" + month);
                    }
                } else {
                    $("#first").hide();
                    $("#second").show();

                    $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                    $("#gender").html("<center><div class='loader'></div></center>");
                    $("#samples").html("<center><div class='loader'></div></center>");
                    $("#county").html("<center><div class='loader'></div></center>");
                    $("#countiesAge").html("<center><div class='loader'></div></center>");
                    $("#partnersAge").html("<center><div class='loader'></div></center>");
                    $("#facilitiesAge").html("<center><div class='loader'></div></center>");
                    $("#subcountiesAge").html("<center><div class='loader'></div></center>");

                    $("#vlOutcomes").load("<?php echo base_url('charts/ages/age_vl_outcome'); ?>/" + year + "/" + month + "/" + data);
                    $("#gender").load("<?php echo base_url('charts/ages/age_gender'); ?>/" + year + "/" + month + "/" + data);
                    $("#samples").load("<?php echo base_url('charts/ages/sample_types'); ?>/" + year + "/" + data);
                    $("#county").load("<?php echo base_url('charts/ages/age_county_outcomes'); ?>/" + year + "/" + month + "/" + data);
                    $("#countiesAge").load("<?= base_url('charts/ages/age_breakdowns'); ?>/" + year + "/" + month + "/" + data + "/" + to[1] + "/" + to[0] + "/" + 1);
                    $("#partnersAge").load("<?php echo base_url('charts/ages/age_breakdowns'); ?>/" + year + "/" + month + "/" + data + "/" + null + "/" + null + "/" + null + "/" + 1);
                    $("#facilitiesAge").load("<?php echo base_url('charts/ages/age_breakdowns'); ?>/" + year + "/" + month + "/" + data + "/" + null + "/" + null + "/" + null + "/" + null + "/" + null + "/" + 1);
                    $("#subcountiesAge").load("<?php echo base_url('charts/ages/age_breakdowns'); ?>/" + year + "/" + month + "/" + data + "/" + null + "/" + null + "/" + null + "/" + null + "/" + 1);
                }
            });

        });
    }
    function switch_age_cat() {
        var view = localStorage.getItem("view_cat_age");
        if (view == 0) {
            localStorage.setItem("view_cat_age", 1);
            $("#age_outcomes").load("<?php echo base_url('charts/summaries/p_age'); ?>");
        } else {
            localStorage.setItem("view_cat_age", 0);
            $("#age_outcomes").load("<?php echo base_url('charts/ages/age_outcomes_sup'); ?>");
        }
    }
</script>
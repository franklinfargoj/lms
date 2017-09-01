<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
    <div class="page-title">
        <div class="container clearfix">
            <h3 class="text-center">Lead Performance</h3>
        </div>
    </div>
    <div class="page-content">
        <div class="container">
            <div class="performance-form-control">
                <label for="">Performance</label>
                <div class="performance-radio-control">
                    <input type="radio" name="month_year" value="active" checked="" id="month"><label>MTD</label>
                </div>
                <div class="performance-radio-control">
                    <input type="radio" name="month_year" value="inactive" id="year"><label>YTD</label>
                </div>
            </div>

            <div class="box-content">
                <!-- <a href="<?php echo site_url('leads/leads_list/all/mtd/all/Walk-in')?>"> -->
                    <div class="box box-m">
                        <img src="<?php echo base_url().ASSETS;?>images/self.png" alt="self">
                        <p>Walk-in</p>
                        <ul>
                            <li>Lead Assigned <br><span id="walkin_assign"><?php echo isset($month_lead_assigned_walkin) ? $month_lead_assigned_walkin:'';?></span></li>
                            <li>Lead Converted <br><span id="walkin_converted"><?php echo isset($month_lead_converted_walkin) ? $month_lead_converted_walkin:'';?></span></li>
                        </ul>
                    </div>
                <!-- </a> -->
                <!-- <a href="<?php echo site_url('leads/leads_list/all/mtd/all/Enquiry')?>"> -->
                    <div class="box">
                        <img src="<?php echo base_url().ASSETS;?>images/enquiry.png" alt="self">
                        <p>Enquiry</p>
                        <ul>
                            <li>Lead Assigned <br><span id="enquiry_assign" ><?php echo isset($month_lead_assigned_enquiry) ? $month_lead_assigned_enquiry :'';?></span></li>
                            <li>Lead Converted <br><span id="enquiry_converted" ><?php echo isset($month_lead_converted_enquiry) ? $month_lead_converted_enquiry:'';?></span></li>
                        </ul>
                    </div>
                <!-- </a> -->
                <!-- <a href="<?php echo site_url('leads/leads_list/all/mtd/all/Tie Ups')?>"> -->
                    <div class="box box-m">
                        <img src="<?php echo base_url().ASSETS;?>images/tie-up.png" alt="self">
                        <p>Tie Up's</p>
                        <ul>
                            <li>Lead Assigned <br><span id="tieup_assign"><?php echo isset($month_lead_assigned_tie_ups) ? $month_lead_assigned_tie_ups:'';?></span></li>
                            <li>Lead Converted <br><span id="tieup_converted"><?php echo isset($month_lead_converted_tie_ups) ? $month_lead_converted_tie_ups :'';?></span></li>
                        </ul>
                    </div>
                <!-- </a> -->
                <!-- <a href="<?php echo site_url('leads/leads_list/all/mtd/all/Analytics')?>"> -->
                    <div class="box">
                        <img src="<?php echo base_url().ASSETS;?>images/analytics.png" alt="self">
                        <p>Analytics</p>
                        <ul>
                            <li>Lead Assigned <br><span id="analytics_assign"><?php echo isset($month_lead_assigned_analytics) ? $month_lead_assigned_analytics :'';?></span></li>
                            <li>Lead Converted <br><span id="analytics_converted"><?php echo isset($month_lead_converted_analytics) ? $month_lead_converted_analytics :'';?></span></li>
                        </ul>
                    </div>
                <!-- </a> -->
            </div>
        </div>
    </div>
<script>
    $(document).ready(function () {
        $('#year').click(function () {
            var walkin_assign = '<?php echo isset($lead_assigned_walkin) ? $lead_assigned_walkin:"0";?>';
            var walkin_converted = '<?php echo isset($lead_converted_walkin) ? $lead_converted_walkin:"0";?>';

            var enquiry_assign = '<?php echo isset($lead_assigned_enquiry) ? $lead_assigned_enquiry :"0";?>';
            var enquiry_converted = '<?php echo isset($lead_converted_enquiry) ? $lead_converted_enquiry :"";?>';

            var tieup_assign = '<?php echo isset($lead_assigned_tie_ups) ? $lead_assigned_tie_ups :"0";?>';
            var tieup_converted = '<?php echo isset($lead_converted_tie_ups) ? $lead_converted_tie_ups:"0";?>';

            var analytics_assign = '<?php echo isset($lead_assigned_analytics) ? $lead_assigned_analytics:"0";?>';
            var analytics_converted = '<?php echo isset($lead_converted_analytics) ? $lead_converted_analytics:"0";?>';

            $('#walkin_assign').html(walkin_assign);
            $('#enquiry_converted').html(enquiry_converted);

            $('#enquiry_assign').html(enquiry_assign);
            $('#walkin_converted').html(walkin_converted);

            $('#tieup_assign').html(tieup_assign);
            $('#tieup_converted').html(tieup_converted);

            $('#analytics_assign').html(analytics_assign);
            $('#analytics_converted').html(analytics_converted);
        })

        $('#month').click(function () {
            var walkin_assign = '<?php echo isset($month_lead_assigned_walkin) ? $month_lead_assigned_walkin:"0";?>';
            var walkin_converted = '<?php echo isset($month_lead_converted_walkin) ? $month_lead_converted_walkin:"0";?>';

            var enquiry_assign = '<?php echo isset($month_lead_assigned_enquiry) ? $month_lead_assigned_enquiry :"0";?>';
            var enquiry_converted = '<?php echo isset($month_lead_converted_enquiry) ? $month_lead_converted_enquiry:'';?>';

            var tieup_assign = '<?php echo isset($month_lead_assigned_tie_ups) ? $month_lead_assigned_tie_ups:"0";?>';
            var tieup_converted = '<?php echo isset($month_lead_converted_tie_ups) ? $month_lead_converted_tie_ups :"0";?>';

            var analytics_assign = '<?php echo isset($month_lead_assigned_analytics) ? $month_lead_assigned_analytics :"0";?>';
            var analytics_converted = '<?php echo isset($month_lead_converted_analytics) ? $month_lead_converted_analytics :"0";?>';

            $('#walkin_assign').html(walkin_assign);
            $('#enquiry_converted').html(enquiry_converted);

            $('#enquiry_assign').html(enquiry_assign);
            $('#walkin_converted').html(walkin_converted);

            $('#tieup_assign').html(tieup_assign);
            $('#tieup_converted').html(tieup_converted);

            $('#analytics_assign').html(analytics_assign);
            $('#analytics_converted').html(analytics_converted);
        })
    })
</script>

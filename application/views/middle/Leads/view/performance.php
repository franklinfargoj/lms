<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
    <div class="page-title">
        <div class="container clearfix">
            <h3 class="text-center">
                <?php echo $title;?>
            </h3>
        </div>
    </div>

    <div class="page-content">
        <span class="bg-top"></span>
        <div class="inner-content">
        <div class="container">
            <div class="performance-form-control m-b">
                <!-- <label for="">Performance</label> -->
                <div class="performance-radio-control abbas1">
                    <input type="radio" name="month_year" value="active" checked="" id="month"><label>This Month</label>
                </div>
                <div class="performance-radio-control abbas1">
                    <input type="radio" name="month_year" value="inactive" id="year"><label>This Year</label>
                </div>
            </div>
            <?php 
                if(isset($employee_id) && !empty($employee_id)){
                    $param = '/'.encode_id($employee_id);
                }else if(isset($branch_id) && !empty($branch_id)){
                    $param = '/'.encode_id($branch_id);
                }else if(isset($zone_id) && !empty($zone_id)){
                    $param = '/'.encode_id($zone_id);
                }
            ?>
            <div class="box-content">
                <a href="<?php echo site_url('dashboard/leads_status/assigned'.$param.'/Walk-in')?>">
                    <div class="box box-m">
                        <img src="<?php echo base_url().ASSETS;?>images/self.png" alt="self">
                        <p>Walk-in</p>
                        <ul>
                            <li>Lead Assigned <br><div class="bg-red"><img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red"> <span id="walkin_assign"><?php echo isset($month_lead_assigned_walkin) ? $month_lead_assigned_walkin:'';?></span></div></li>
                            <li>Lead Converted <br><div class="bg-red"><img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red"> <span id="walkin_converted"><?php echo isset($month_lead_converted_walkin) ? $month_lead_converted_walkin:'';?></span></div></li>
                        </ul>
                    </div>
                </a>
                <a href="<?php echo site_url('dashboard/leads_status/assigned'.$param.'/Enquiry')?>">
                    <div class="box">
                        <img src="<?php echo base_url().ASSETS;?>images/enquiry.png" alt="self">
                        <p>Enquiry</p>
                        <ul>
                            <li>Lead Assigned <br><div class="bg-red"> <img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red"><span id="enquiry_assign" ><?php echo isset($month_lead_assigned_enquiry) ? $month_lead_assigned_enquiry :'';?></span></div></li>
                            <li>Lead Converted <br><div class="bg-red"><img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red"> <span id="enquiry_converted" ><?php echo isset($month_lead_converted_enquiry) ? $month_lead_converted_enquiry:'';?></span></div></li>
                        </ul>
                    </div>
                 </a>
                <a href="<?php echo site_url('dashboard/leads_status/assigned'.$param.'/Tie Ups')?>">
                    <div class="box box-m">
                        <img src="<?php echo base_url().ASSETS;?>images/tie-up.png" alt="self">
                        <p>Tie Up's</p>
                        <ul>
                            <li>Lead Assigned <br><div class="bg-red"><img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red"> <span id="tieup_assign"><?php echo isset($month_lead_assigned_tie_ups) ? $month_lead_assigned_tie_ups:'';?></span></div></li>
                            <li>Lead Converted <br><div class="bg-red"><img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red"> <span id="tieup_converted"><?php echo isset($month_lead_converted_tie_ups) ? $month_lead_converted_tie_ups :'';?></span></div></li>
                        </ul>
                    </div>
                 </a>
                <a href="<?php echo site_url('dashboard/leads_status/assigned'.$param.'/Analytics')?>">
                    <div class="box">
                        <img src="<?php echo base_url().ASSETS;?>images/analytics.png" alt="self">
                        <p>Analytics</p>
                        <ul>
                            <li>Lead Assigned <br><div class="bg-red"><img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red"> <span id="analytics_assign"><?php echo isset($month_lead_assigned_analytics) ? $month_lead_assigned_analytics :'';?></span></div></li>
                            <li>Lead Converted <br><div class="bg-red"> <img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red"><span id="analytics_converted"><?php echo isset($month_lead_converted_analytics) ? $month_lead_converted_analytics :'';?></span></div></li>
                        </ul>
                    </div>
                 </a>
            </div>
        </div>
        </div>
        <span class="bg-bottom"></span>
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

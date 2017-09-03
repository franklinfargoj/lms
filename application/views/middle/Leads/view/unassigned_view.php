<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Unassigned Leads</h3>
    </div>
</div>

<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
    <div class="container">
        <?php $status = array('Walk-in','Enquiry','Tie Ups','Analytics');
        if(!empty($unassigned_leads_count)) foreach ($unassigned_leads_count as $key => $lead_source) ?>
        <div class="box-content">
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.$status[0])?>">
            <div class="box box-m">
                <img src="<?php echo base_url().ASSETS;?>images/self.png" alt="self">
                <p>Walk-in</p>
                <ul>
                <?php $walkin = 0;
                if($lead_source['lead_source'] == 'Walk-in')
                      $walkin = $lead_source['total'];
                    echo $walkin;
                ?>
                </ul>
            </div>
            </a>
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.$status[1])?>">
            <div class="box">
                <img src="<?php echo base_url().ASSETS;?>images/enquiry.png" alt="self">
                <p>Enquiry</p>
                <ul>
                    <?php $enquiry = 0;
                    if($lead_source['lead_source'] == 'Enquiry')
                        $enquiry = $lead_source['total'];
                    echo $enquiry;
                    ?>
                </ul>
            </div>
            </a>
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.$status[2])?>">
            <div class="box box-m">
                <img src="<?php echo base_url().ASSETS;?>images/tie-up.png" alt="self">
                <p>Tie Up's</p>
                <ul>
                    <?php $tieups = 0;
                    if($lead_source['lead_source'] == 'Tie Ups')
                        $tieups = $lead_source['total'];
                    echo $tieups;
                    ?>
                </ul>
            </div>
            </a>
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.$status[3])?>">
            <div class="box">
                <img src="<?php echo base_url().ASSETS;?>images/analytics.png" alt="self">
                <p>Analytics</p>
                <ul>
                    <?php $analytics = 0;
                    if($lead_source['lead_source'] == 'Analytics')
                        $analytics = $lead_source['total'];
                    echo $analytics;
                    ?>
                </ul>
            </div>
            </a>
        </div>
    </div>
    </div>
    <span class="bg-bottom"></span>
</div>

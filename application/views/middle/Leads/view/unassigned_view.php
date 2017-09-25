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
        <?php 
        $status = array('Walk-in','Enquiry','Tie Ups','Analytics');
        if(isset($unassigned_leads_count) && !empty($unassigned_leads_count)){?>
        <div class="box-content">
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.encode_id($status[0]))?>">
            <div class="box box-m">
                <img src="<?php echo base_url().ASSETS;?>images/self.png" alt="self">
                <p>Walk-in</p>
                <div class="bg-red bg-c">
                <img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red" class="img-w">
                <ul>
                <?php $walkin = 0;
                if($unassigned_leads_count['Walk-in'] != 0)
                      $walkin = $unassigned_leads_count['Walk-in'];
                    echo $walkin;
                ?>
                </ul>
                </div>
            </div>
            </a>
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.encode_id($status[1]))?>">
            <div class="box">
                <img src="<?php echo base_url().ASSETS;?>images/enquiry.png" alt="self">
                <p>Enquiry</p>
                <div class="bg-red bg-c">
                <img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red"  class="img-w">
                <ul>
                    <?php $enquiry = 0;
                    if($unassigned_leads_count['Enquiry'] != 0)
                        $enquiry = $unassigned_leads_count['Enquiry'];
                        echo $enquiry;
                    ?>
                </ul>
                </div>
            </div>
            </a>
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.encode_id($status[2]));?>">
            <div class="box box-m">
                <img src="<?php echo base_url().ASSETS;?>images/tie-up.png" alt="self">
                <p>Tie Up's</p>
                <div class="bg-red bg-c">
                <img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red" class="img-w">
                <ul>
                    <?php $tieups = 0;
                    if($unassigned_leads_count['Tie Ups'] != 0)
                        $tieups = $unassigned_leads_count['Tie Ups'];
                    echo $tieups;
                    ?>
                </ul>
                </div>
            </div>
            </a>
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.encode_id($status[3]))?>">
            <div class="box">
                <img src="<?php echo base_url().ASSETS;?>images/analytics.png" alt="self">
                <p>Analytics</p>
                <div class="bg-red bg-c">
                <img src="<?php echo base_url().ASSETS;?>images/red-circle.png" alt="bg-red" class="img-w">
                <ul>
                    <?php $analytics = 0;
                    if($unassigned_leads_count['Analytics'] != 0)
                        $analytics = $unassigned_leads_count['Analytics'];
                    echo $analytics;
                    ?>
                </ul>
                </div>
            </div>
            </a>
        </div>
        <?php } ?>
    </div>
</div>    
    <span class="bg-bottom"></span>
</div>

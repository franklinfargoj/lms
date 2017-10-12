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
        <div class="box-content box-unasign-top">
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.encode_id($status[0]))?>">
            
            <div class="box box-m  box-m1">
            <div class="performance-box-top"></div>
                <img src="<?php echo base_url().ASSETS;?>images/man-box.jpg" alt="self" class="Pbox-img">
                <h4>Walk-in</h4>
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
            <div class="performance-box-bottom"></div>    
            </div>
            
            </a>
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.encode_id($status[1]))?>">
            <div class="box box-m1">
            <div class="performance-box-top"></div>
                <img src="<?php echo base_url().ASSETS;?>images/man2-box.jpg" alt="self" class="Pbox-img">
                <h4>Enquiry</h4>
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
                <div class="performance-box-bottom"></div> 
            </div>
            </a>
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.encode_id($status[2]));?>">
            <div class="box box-m box-m1">
            <div class="performance-box-top"></div>
                <img src="<?php echo base_url().ASSETS;?>images/hand-box.jpg" alt="self" class="Pbox-img">
                <h4>Tie Up</h4>
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
                <div class="performance-box-bottom"></div> 
            </div>
            </a>
            <a href="<?php echo site_url('leads/unassigned_leads_list/'.encode_id($status[3]))?>">
            <div class="box box-m1">
            <div class="performance-box-top"></div>
                <img src="<?php echo base_url().ASSETS;?>images/board-box.jpg" alt="self" class="Pbox-img">
                <h4>Analytics</h4>
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
                <div class="performance-box-bottom"></div> 
            </div>
            </a>
        </div>
        <?php } ?>
    </div>
</div>    
    <span class="bg-bottom"></span>
</div>

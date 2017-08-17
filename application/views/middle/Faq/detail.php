<!-- BEGIN PAGE CONTENT INNER -->
    <div class="row">
        <div class="col-md-12">
             <!-- BEGIN PRODUCT CATEGOEY-->
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <!-- <i class="fa fa-cogs font-green-sharp"></i> -->
                            <span class="caption-subject font-green-sharp bold"><?php echo $faqDetail[0]['question'];?></span>
                        </div>
                        <div class="tools">
                            <!-- <a href="javascript:;" class="collapse">
                            </a>
                            <a href="#portlet-config" data-toggle="modal" class="config">
                            </a>
                            <a href="javascript:;" class="reload">
                            </a> -->
                            <a href="<?php echo base_url('faq')?>" class="">Back
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                       <?php echo $faqDetail[0]['answer'];?>
                    </div>
                </div>
            <!-- END PRODUCT CATEGOEY-->
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->
    
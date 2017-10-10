<?php $statuses = $this->config->item('lead_status');
$assigned_to = 0;
?>
<link rel="stylesheet" href="<?php echo base_url('assets2/css/animate.min.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets2/css/timelify.css');?>">
<div class="container">
    <div class="timeline">
        <?php if(!empty($lead_data)){?>
            <br>
                Lead Id : <?php echo $lead_data[0]['id'];?>
                Customer Name : <?php echo ucwords(strtolower($lead_data[0]['lead_name']));?>
                Contact No : <?php echo ucwords(strtolower($lead_data[0]['contact_no']));?>
                Lead Source : <?php echo ucwords(strtolower($lead_data[0]['lead_source']));?>
            </span>
           <?php if (isset($lead_data[0])){?>
            <ul class="timeline-items">
            <li class="is-hidden timeline-item inverted"> <!-- Normal block, positionned to the left -->
                <h3>Lead Generated</h3>
                <hr>
                <p>
                <?php
                echo $lead_data[0]['generated'] ? "Generated By : ".ucwords(strtolower($lead_data[0]['generated'])).'<br><br>' : 'Generated By NA<br><br>';
                ?>
                </p>
                <hr>
                <span>
                    <?php echo $lead_data[0]['generated_on'] ? "Date : ".date('d-M-Y',strtotime($lead_data[0]['generated_on'])) : "Date : NA"; ?>

                </span>
            </li>
            </ul>
            <?php }
            foreach ($lead_data as $key => $lead_value){
                $right = 'inverted';
                if(($key +1) %2==0){
                    $right = '';
                }
                if (isset($lead_value['reroute_from_branch_id']) &&  $lead_value['reroute_from_branch_id']!='' && $key !=0){?>
                    <ul class="timeline-items">
                        <li class="is-hidden timeline-item <?php echo $right;?>"> <!-- Normal block, positionned to the left -->
                            <h3><?php echo (isset($lead_value['status'])) ? "Lead Status : ".ucwords(strtolower($statuses[$lead_value['status']])) :'Lead Status NA';?></h3>
                            <hr>
                            <p>
                                <?php
                                echo $lead_value['reroute_from_branch_id'] ? "Reroute To Branch Id : ".$lead_value['reroute_from_branch_id'].'<br><br>' : 'Reroute To Branch Id: NA<br><br>';
                                echo $lead_value['reroute_to_branch_id'] ? "Reroute From Branch Id : ".$lead_value['reroute_to_branch_id'].'<br><br>' : 'Reroute From Branch Id : NA<br><br>';
                                ?>

                            </p>
                            <hr>
                            <span><?php
                                echo $lead_value['date'] ? "Date : ".date('d-M-Y',strtotime($lead_value['date'])) : "Date : NA";
                                ?>
                            </span>
                        </li>
                    </ul>
                <?php }


                elseif($key!=0 && !isset($lead_value['reroute_from_branch_id'])){

                    ?>
                    <ul class="timeline-items">
                        <li class="is-hidden timeline-item <?php echo $right;?>"> <!-- Normal block, positionned to the left -->
                            <h3><?php echo (isset($lead_value['status'])) ? "Lead Status : ".ucwords(strtolower($statuses[$lead_value['status']])) :'Lead Status NA';?></h3>
                            <hr>
                            <p>
                                <?php
                                if($assigned_to == 0){
                                    echo $lead_value['employee_name'] ? "Assigned To : ".ucwords(strtolower($lead_value['employee_name'])).'<br><br>' : 'Assigned To : NA<br><br>';
                                    echo $lead_value['created_by_name'] ? "Assigned By : ".ucwords(strtolower($lead_value['created_by_name'])).'<br><br>' : 'Assigned By : NA<br><br>';
                                }else{
                                    echo $lead_value['employee_name'] ? "Status Updated By : ".ucwords(strtolower($lead_value['employee_name'])) : 'Status Updated By : NA<br><br>';
                                }

                                $assigned_to++;?>
                            </p>
                            <hr>
                            <span><?php
                                echo $lead_value['date'] ? "Date : ".date('d-M-Y',strtotime($lead_value['date'])) : "Date : NA";
                                ?></span>
                        </li>
                    </ul>
                <?php }
            }} ?>
    </div>
</div>
<script src="<?php echo base_url('assets2/js/jquery.timelify.js');?>"></script>
<script>
    $('.timeline').timelify({
        animLeft: "fadeInLeft",
        animCenter: "fadeInUp",
        animRight: "fadeInRight",
        animSpeed: 600,
        offset: 150
    });
</script>

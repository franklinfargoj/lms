
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<?php $status = array(NC,DC,AO,NI,CBC,FU,Converted,Closed);
      $status_ytd = array('not_contacted','documents_collected','account_opened','drop_not_interested','can_not_be_contacted',
      'follow_up','converted','closed');
      $status_mtd = array('month_not_contacted','month_documents_collected','month_account_opened','month_drop_not_interested',
      'month_can_not_be_contacted','month_follow_up','month_converted','month_closed');
      $title = 'MY GENERATED LEAD';
      if($this->session->userdata('admin_type') == 'BM'){
          $title = $employee_name.' GENERATED LEAD';
          $emp_id = 'Employee Id: '.$employee_id;
      }
      if($this->session->userdata('admin_type') == 'ZM'){
          $title = 'Branch Id: '.$branchid.' GENERATED LEAD';
      }
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center"><?php echo $title;?></h3>
        <?php echo isset($emp_id) ? '<h4 class="text-center">'. $emp_id .'</h4>' : "";?>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <table class="upload-table" id="sample_3">
            <thead>
            <tr class="top-header">
                <th></th>
                <th><input type="text" name="customername" placeholder="Search Status"></th>
                <th><input type="text" name="customername" placeholder="Search MTD"></th>
                <th><input type="text" name="customername" placeholder="Search YTD"></th>
            </tr>
            <tr>
                <th>Sr No</th>
                <th>Status</th>
                <th>MTD</th>
                <th>YTD</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($status as $key => $value){ ?>
            <tr>
                <td><?php echo $key+1; ?></td>
                <td><?php echo $value; ?></td>
                <td><?php echo $$status_mtd[$key]; ?></td>
                <td><?php echo $$status_ytd[$key]; ?></td>
            </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
</div>
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">

    jQuery(document).ready(function() {
        var table = $('#sample_3');
        var columns = [''];

        //Initialize datatable configuration
        initTable(table,columns);

    });

</script>

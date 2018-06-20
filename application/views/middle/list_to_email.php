<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/toggle.css" rel="stylesheet">
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN PRODUCT CATEGORY -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">To Email</h3>
    </div>
</div>

<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">

            <table id="sample_3" class="display lead-table">

                <thead>
<!--                <tr class="top-header">-->
<!--                    <th></th>-->
<!--                    <th>-->
<!--                        <input type="text" name="customername" placeholder="Search Name">-->
<!--                    </th>-->
<!---->
<!--                    <th>-->
<!--                        <input type="text" name="customername" placeholder="Search Email"></th>-->
<!--                    </th>-->
<!--                    <th></th>-->
<!--                    <th></th>-->
<!--                </tr>-->
                <tr>
                    <th style="text-align:center">Sr.No.</th>
                    <th style="text-align:left">Name</th>
                    <th style="text-align:left">Email</th>
                    <th style="text-align:left">Status</th>
                    <th style="text-align:left">Action</th>
                </tr>
                </thead>
                <thead>
                    <tr class="top-header">
                        <th></th>
                        <th>
                            <input type="text" data-column="1"  class="search-input-text" placeholder="Search Name">
                        </th>

                        <th>
                            <input type="text" data-column="2"  class="search-input-text" placeholder="Search Email"></th>
                        </th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

<!--                <tbody>-->
<!--                --><?php //if($userData){
//
//                    $i = 0;
//                    foreach ($userData as $key => $value) {
//                        ?>
<!--                        <tr>-->
<!--                            <td style="text-align:center">-->
<!--                                --><?php //echo ++$i;?>
<!--                            </td>-->
<!--                            <td style="text-align:left">-->
<!--                                --><?php //echo ucwords($value->name);?>
<!--                            </td>-->
<!--                            <td style="text-align:left">-->
<!--                                --><?php //echo $value->email_id;?>
<!--                            </td>-->
<!--                            <td>-->
<!--                                --><?php //echo ucwords($value->email_status);?>
<!--                            </td>-->
<!--                            <td style="text-align:left">-->
<!--                                <a class="" href="--><?php //echo site_url('toemail/edit/'. encode_id($value->id));?><!--">-->
<!--                                    Edit-->
<!--                                </a>-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        --><?php
//                    }
//                }?>
<!--                </tbody>-->

            </table>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>


<!-- END PRODUCT CATEGORY-->
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
//    jQuery(document).ready(function() {
//        var table = $('#sample_3');
//        var columns = [3];
//
//        //Initialize datatable configuration
//        initTable(table,columns);
//
//        $('.delete').click(function(){
//            var url = $(this).data('url');
//            var result = confirm("Are you sure want to delete?");
//            if(result == true){
//                window.location.href = url;
//            }
//        });
//    });


    $(document).ready(function () {
        $('#sample_3').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ajax":{
                "url": "<?php echo base_url('toemail/listToEmail') ?>",
                "dataType": "json",
                "type": "POST",
                "data":{  '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>' }
            },
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "email_id" },
                { "data": "email_status" },
                { "data": "action" },
            ]

        });

        var data = $('#sample_3').DataTable();

        $('.search-input-text').on( 'keyup', function () {   // for text boxes
            var i =$(this).attr('data-column');  // getting column index
            var v =$(this).val();  // getting search input value
            data.columns(i).search(v).draw();
        } );
    });
</script>

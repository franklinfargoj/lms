<?php
/**
 * Created by PhpStorm.
 * User: webwerk
 * Date: 12/9/17
 * Time: 2:14 PM
 */
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">FD calculator</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-form">
                <label>Senior Citizen:</label>
                <div class="radio-control">
                    <input type="radio" id="is_own_branch" name="is_own_branch"
                           value="1" <?php echo set_radio('is_own_branch', '1', TRUE); ?> />
                    <label>Yes</label>
                </div>
                <div class="radio-control">
                    <input type="radio" name="is_own_branch" id="is_other_branch"
                           value="0" <?php echo set_radio('is_own_branch', '0'); ?> />
                    <label>No</label>
                </div>
            </div>
        </div>
    </div>
</div>
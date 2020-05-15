<?php echo $header; ?>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
    <h1 style="background-image: url('view/image/payment.png');"><?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?php echo $entry_merchant; ?></td>
          <td><input type="text" name="webcash_merchant" value="<?php echo $webcash_merchant; ?>" />
            <?php if ($error_merchant) { ?>
            <span class="error"><?php echo $error_merchant; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_password; ?></td>
          <td><input type="text" name="webcash_password" value="<?php echo $webcash_password; ?>" />
            <?php if ($error_password) { ?>
            <span class="error"><?php echo $error_password; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><?php echo $entry_callback; ?></td>
          <td><textarea cols="40" rows="5"><?php echo $callback; ?></textarea></td>
        </tr>
        <tr>
          <td><?php echo $entry_test; ?></td>
          <td><select name="webcash_test">
              <?php if ($webcash_test == '0') { ?>
              <option value="0" selected="selected"><?php echo $text_off; ?></option>
              <?php } else { ?>
              <option value="0"><?php echo $text_off; ?></option>
              <?php } ?>
              <?php if ($webcash_test == '100') { ?>
              <option value="100" selected="selected"><?php echo $text_successful; ?></option>
              <?php } else { ?>
              <option value="100"><?php echo $text_successful; ?></option>
              <?php } ?>
              <?php if ($webcash_test == '101') { ?>
              <option value="101" selected="selected"><?php echo $text_declined; ?></option>
              <?php } else { ?>
              <option value="101"><?php echo $text_declined; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_order_status; ?></td>
          <td><select name="webcash_order_status_id">
              <?php foreach ($order_statuses as $order_status) { ?>
              <?php if ($order_status['order_status_id'] == $webcash_order_status_id) { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_geo_zone; ?></td>
          <td><select name="webcash_geo_zone_id">
              <option value="0"><?php echo $text_all_zones; ?></option>
              <?php foreach ($geo_zones as $geo_zone) { ?>
              <?php if ($geo_zone['geo_zone_id'] == $webcash_geo_zone_id) { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_status; ?></td>
          <td><select name="webcash_status">
              <?php if ($webcash_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_sort_order; ?></td>
          <td><input type="text" name="webcash_sort_order" value="<?php echo $webcash_sort_order; ?>" size="1" /></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php echo $footer; ?>
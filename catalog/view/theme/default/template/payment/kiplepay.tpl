<form action="<?php echo $action; ?>" method="post">
  <input type="hidden" name="ord_mercID" value="<?php echo $merchant; ?>" />
  <input type="hidden" name="ord_mercref" value="<?php echo $order_id; ?>" />
  <input type="hidden" name="ord_totalamt" value="<?php echo $amount; ?>" />
  <input type="hidden" name="currency" value="<?php echo $currency; ?>" />
  <input type="hidden" name="desc" value="<?php echo $description; ?>" />
  <input type="hidden" name="ord_shipname" value="<?php echo $name; ?>" />
  <input type="hidden" name="address" value="<?php echo $address; ?>" />
  <input type="hidden" name="postcode" value="<?php echo $postcode; ?>" />
  <input type="hidden" name="ord_shipcountry" value="<?php echo $country; ?>" />
  <input type="hidden" name="ord_telephone" value="<?php echo $telephone; ?>" />
  <input type="hidden" name="email" value="<?php echo $email; ?>" />
  <input type="hidden" name="testMode" value="<?php echo $test; ?>" />
  <input type="hidden" name="ord_date" value="<?php echo date("l F d, Y, h:i A");?>" />
   <input type="hidden" name="merchant_hashvalue" value="<?php echo $hashvalue; ?>" />
  <input type="hidden" name="version" value="<?php echo $version; ?>" />

  <input type="hidden" name="ord_returnURL" value="<?php echo $return_url; ?>" />
  <div class="buttons">
    <div class="pull-right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
    </div>
  </div>
</form>
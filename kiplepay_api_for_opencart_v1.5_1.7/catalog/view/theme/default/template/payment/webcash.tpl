<form action="<?php echo $action; ?>" method="post" id="payment">
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
  <input type="hidden" name="ord_date" value="<?php echo date("Y-m-d h:i:s");?>" />
  <input type="hidden" name="ord_email" value="<?php echo $email; ?>" />
  <input type="hidden" name="merchant_hashvalue" value="<?php echo $hashvalue;?>" />
  <input type="hidden" name="ord_returnURL" value="http://www.abc.com/index.php?route=payment/webcash/callback" />
  <input type="hidden" name="testMode" value="<?php echo $test; ?>" />
  <div class="buttons">
    <div class="right"><a onclick="$('#payment').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></div>
  </div>
</form>
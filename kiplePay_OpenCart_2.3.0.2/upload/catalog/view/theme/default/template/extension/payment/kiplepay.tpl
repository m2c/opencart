<form action="<?php echo $action; ?>" method="post" id="payment">
  <input type="hidden" name="ord_mercID" value="<?php echo $merchant; ?>" />
  <input type="hidden" name="ord_mercref" value="<?php echo $order_id; ?>" />
  <input type="hidden" name="ord_totalamt" value="<?php echo $amount; ?>" />
  <input type="hidden" name="ord_gstamt" value="0.00" />
  <input type="hidden" name="currency" value="<?php echo $currency; ?>" />
  <input type="hidden" name="desc" value="<?php echo $description; ?>" />
  <input type="hidden" name="ord_shipname" value="<?php echo $name; ?>" />
  <input type="hidden" name="address" value="<?php echo $address; ?>" />
  <input type="hidden" name="postcode" value="<?php echo $postcode; ?>" />
  <input type="hidden" name="ord_shipcountry" value="<?php echo $country; ?>" />
  <input type="hidden" name="ord_telephone" value="<?php echo $telephone; ?>" />
  <input type="hidden" name="ord_date" value="<?php echo $ord_date; ?>" />
  <input type="hidden" name="ord_email" value="<?php echo $email; ?>" />
  <input type="hidden" name="merchant_hashvalue" value="<?php echo $hashvalue; ?>" />

  <input type="hidden" name="ord_returnURL" value="<?php echo $return_url; ?>" />
  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
    </div>
  </div>
</form>
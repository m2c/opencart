<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" id="checkout">
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
  <input type="hidden" name="ord_date" value="<?php echo date("Y:m:d h:i:s");?>" />
  <input type="hidden" name="merchant_hashvalue" value="<?php echo $hashvalue;?>" />
  <input type="hidden" name="ord_email" value="<?php echo $email; ?>" />
  <input type="hidden" name="ord_returnURL" value="http://loalhost/index.php?route=payment/webcash/callback" />
</form>
<div class="buttons">
  <table>
    <tr>
      <td align="left"><a onclick="location = '<?php echo str_replace('&', '&amp;', $back); ?>'" class="button"><span><?php echo $button_back; ?></span></a></td>
      <td align="right"><a onclick="$('#checkout').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></td>
    </tr>
  </table>
</div>

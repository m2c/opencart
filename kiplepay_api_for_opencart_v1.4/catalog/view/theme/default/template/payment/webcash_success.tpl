<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
<meta http-equiv="refresh" content="5;url=<?php echo $continue; ?>">
<title>Webcash Payment Gateway</title>
<base href="<?php echo $base; ?>" />
</head>
<body>
<div style="text-align: center;">
  <h1>Thank you for shopping with us .... </h1>
  <p>Response from Webcash:</p>
  <div style="border: 1px solid #DDDDDD; margin-bottom: 20px; width: 350px; margin-left: auto; margin-right: auto;">
    <WPDISPLAY ITEM=banner>
  </div>
  <p>... your payment was successfully received.</p>
  <p><b><span style="color: #FF0000">Please wait...</span></b> whilst we finish processing your order.<br>If you are not automatically re-directed in 10 seconds, please click <a href="index.php">here</a>.</p>
</div>
</body>
</html>
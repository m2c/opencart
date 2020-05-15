Descriptions:
============
This is a Webcash plugin for OPENCART version 1.4.x



Release date:
=============
14-06-2010


Changelog
==========
Support OpenCart 1.4.X



Preinstallation
===============
1. Edit file \catalog\controller\payment\webcash.php

Scroll to line 13:

$this->data['action'] = 'https://staging.webcash.com.my/wcgatewayinit.php';

The above is test server url, for live url, change it to

$this->data['action'] = 'https://webcash.com.my/wcgatewayinit.php';


2. Edit file  \catalog\view\theme\default\template\payment\webcash.tpl

Scroll to line 28:

<input type="hidden" name="ord_returnURL" value="http://www.abc.com/index.php?route=payment/webcash/callback" />

Change www.abc.com to your domain name.




Installation:
=============

1. copy the folder "admin" and "catalog" to your shopping cart folder.

2. log in to your admin backend

3. go to "Extensions" --> Payment --> Webcash Payment Gateway --> Install 

4. Click "Edit" and you will see the following fields ;

Merchant ID/Code:  <-- key in your merchant code here

Merchant Hash Key:   <-- Hash key provide via webcash

Order Status:   <-- select your status for successful status

Geo Zone:       <-- regional / zone setting

Status:         <-- set to "Enable"  

Sort Order:     <-- sort order of this gateway , 0,1,2,3,4 or ...

5. Save

6. Done.
Description: 
============ 

This is a Kiplepay recurring plugin for OPENCART version 3.0.x.x
  

Release date: 
============= 
01-10-2021 


Changelog 
========== 
Support OpenCart 3.0.x.x 

01-10-2021

* Wrong Return URL generated issue fixed

Note 
========================= 
Kiplepay supports only Malaysian currency (MYR), so recommended not to use multiple currency. 


Installation Instructions 
================== 
1) Unzip the downloaded kiplePay_OpenCart_3.1.0.ZIP file into a new directory. 
2) In your OpenCart admin panel go to Extensions > Extension Installer. 
3) Click on the Upload button. 
4) Upload the file kiplepay.ocmod.zip which is in the directory you created in step 1. 
5) Go to Extensions > Payments > kiplepay and click the Install button. The page will be refreshed and Edit button will show up. 
5) Click on the Edit button. 
6) Update Merchant Id and Hash Key according to your details. 

Congratulations! kiplepay is now installed. 



Manual Installation 

================== 

1) Unzip the downloaded kiplePay_OpenCart_3.0.x.x.ZIP file into a new directory. 
2) Navigate to this directory and find the file kiplepay.ocmod.zip 
3) Extract kiplepay.ocmod.zip into a new directory 
4) Navigate to the newly extracted directory. You will notice it contains a directory called upload/ 
5) Upload the contents of the upload/ directory to your OpenCart installation, making sure to preserve the directory structure. 
6) Go to Extensions > Extensions > Payments > Kiplepay and click the Install button. The page will be refreshed and Edit button will show up. 
7) Click on the Edit button.  
8) Update Merchant Id and Hash Key according to your details. 


Configure Test mode 
================== 
a. 'Sandbox' for test transactions  
b. 'Live' for real transactions  

example: follow the document folder admin-setting 



What does it do: 
================ 
This contrib adds support for kiplepay.com payment integration.  
The checkout button will redirect the customer to the kiplepay site to pay.  
Once payment is completed, the page will redirect back to your site. 
  


Requirements: 
============== 
  * You will need to have a Kiplepay account. 
  * You will need to have MYR currency setup in your cart. 
  

For Recurring Payments: 

  * You will need to subscribe to kiplepay recurring payments. Please contact support@kiplepay.com if you don't have a subscription. 
  * Go to Extensions > Extensions > Payments > Kiplepay > Enable recurring payment status
  

Notes for Recurring Payments :  
============== 

* In Admin portal recurring profile set up 

    Recurring amounts are calculated by the frequency and duration.

    For example if you use a frequency of "month" and a duration of "2", then the user will be billed every month (1 time per month) for 2 months. 

    The duration is the number of times the user will make a payment, set this to 0 if you want recurring payments to continue until they are cancelled. 

    Kiplepay only supports monthly or quarterly frequency payments, so the frequency value should be "month" or "quarterly". 

    Kiplepay only supports 1-cycle payments, so the cycle value should be always 1.  

* Kiplepay does not support trial periods. 

* Kiplepay payment does not support products with mixed payment types in the same cart. The cart can only contain a single product with recurring payments, or products with one-time payment. 

* Kiplepay payment only supports one product in the cart with the recurring payment type. You cannot have multiple products with recurring payments in the same cart.   Multiples of the same product with a recurring payment is supported.  

* if you want to cancel the recurring payment for a customer, please login to kiplepay recurring portal which you will get after subscription and cancel the payments.   Please contact support@kiplepay.com for more info. 

* Kiplepay charges only the recurring price which is setup in the Opencart admin portal for recurring profiles. It will not charge product price nor shipping price. 

 
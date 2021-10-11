<?php
class ControllerExtensionPaymentkiplepay extends Controller
{
    protected $errors = array();

    public function index()
    {
        $this->load->model('checkout/order');

        $this->load->language('extension/payment/kiplepay');

        $this->load->model('extension/payment/kiplepay');

        $data['button_confirm'] = $this->language->get('button_confirm');

        if ($this->config->get('payment_kiplepay_test') == 'sandbox') {
            $data['action'] = 'https://uat.kiplepay.com/wcgatewayinit.php';
        } else {
            $data['action'] = 'https://kiplepay.com/wcgatewayinit.php';
        }

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        header('Set-Cookie: ' . $this->config->get('session_name') . '=' . $this->session->getId() . '; SameSite=None; Secure');
        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'],$this->config->get('payment_kiplepay_order_pending_status_id'),"Default order status");
        $data['language'] = $this->session->data['language'];
        $data['logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');

        $order_id = 'kp-'.uniqid().'-'.$this->session->data['order_id'];

        $data['merchant'] = $this->config->get('payment_kiplepay_app_id');
        $data['order_id'] = $order_id;
        $data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $data['currency'] = $order_info['currency_code'];

        if($data['currency']!="MYR" && $data['currency']!="RM"){
            echo "<div class='col-sm-12' align='center'><h1> ".$data['currency']." currency not supported in Kiplepay payment.Please choose MYR</h1></div>"; exit;
        }

        $products = '';
        foreach ($this->cart->getProducts() as $product) {
            $products .= $product['quantity'] . ' x ' . $product['name'] . ', ';
        }

        $data['description'] = $products;

        $data['name'] = $order_info['payment_firstname'] .' '.$order_info['payment_lastname'];
        if (!$order_info['payment_address_2']) {
            $data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
        } else {
            $data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_address_2'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
        }
        $data['postcode'] = $order_info['payment_postcode'];
        $data['country'] = $order_info['payment_iso_code_3'];
        $data['telephone'] = $order_info['telephone'];
        $data['email'] = $order_info['email'];

        $data['ord_date'] = date('Y-m-d h:i:s');

        $data['ord_customfield4'] = "plg_opencart_v3.1.4";

        $amountVal =  $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $amountVal = str_replace('.', '', $amountVal);


        $data['hashvalue'] = sha1($this->config->get('payment_kiplepay_merchant_private_key').$this->config->get('payment_kiplepay_app_id'). $order_id .$amountVal);
        $data['return_url'] = $this->url->link('extension/payment/kiplepay/callback', '', true);
        $data['dynamic_callback_url'] = $this->url->link('extension/payment/kiplepay/callback', '', true);
        $data['version'] = '2.0';
        

      
        
        if ($this->cart->hasRecurringProducts()) {

            if (count($this->cart->getProducts()) != count($this->cart->getRecurringProducts())) {
                echo "<div class='col-sm-12' align='center'><h1> Kiplepay payment does not support products with mixed payment types in the same cart. The cart can only contain a single product with recurring payments, or products with one-time payment.</h1></div>"; exit;
            }

            if (count($this->cart->getRecurringProducts()) > 1) {
                echo "<div class='col-sm-12' align='center'><h1> Kiplepay payment only supports one product in the cart with the recurring payment type. You cannot have multiple products with recurring payments in the same cart. Multiples of the same product with a recurring payment is supported</h1></div>"; exit;
            }
            
            foreach ($this->cart->getRecurringProducts() as $item) {
                if ($item['recurring']['trial']) {
                    echo "<div class='col-sm-12' align='center'><h1> Kiplepay payment won't support trial period</h1></div>"; exit;
                } else {
                    $trial_text = '';
                }

                $recurring_price = $this->tax->calculate($item['recurring']['price'] * $item['quantity'], $item['tax_class_id']);
                $recurring_amt = $this->currency->format($recurring_price, $this->session->data['currency']);
                $recurring_description = $trial_text . sprintf($this->language->get('text_recurring'), $recurring_amt, $item['recurring']['cycle'], $item['recurring']['frequency']);

                $item['recurring']['price'] = $recurring_price;

                if ($item['recurring']['duration'] > 0) {
                    $recurring_description .= sprintf($this->language->get('text_length'), $item['recurring']['duration']);
                }

                if (!$item['recurring']['trial']) {
                    // We need to override this value for the proper calculation in updateRecurringExpired
                    $item['recurring']['trial_duration'] = 0;
                }
                if (!in_array($item['recurring']['frequency'], ['month', 'quarterly', 'year'])) {
                    echo "<div class='col-sm-12' align='center'><h1> Kiplepay payment support only monthly/quarterly/yearly frequency in recurring payment</h1></div>"; exit;
                }
               
                if ($item['recurring']['cycle'] != 1 ) {
                    echo "<div class='col-sm-12' align='center'><h1> Recurring payment Cycle value should be 1 </h1></div>"; exit;
                }

                $recurringData = $this->model_extension_payment_kiplepay->createRecurring($item, $this->session->data['order_id'], $recurring_description, $order_id);

                $order_id = $order_id."-".$recurringData;
                $data['order_id'] = $order_id;

                $this->model_extension_payment_kiplepay->updateRecurring($recurringData,$order_id);

                $amountVal =  $this->currency->format($item['recurring']['price'] * $item['quantity'], $order_info['currency_code'], $order_info['currency_value'], false);
                $amountVal = str_replace('.', '', $amountVal);
                
                $data['hashvalue'] = sha1($this->config->get('payment_kiplepay_merchant_private_key').$this->config->get('payment_kiplepay_app_id'). $order_id .$amountVal);
                
                $duration = ($item['recurring']['duration'] == 0) ? "perpetually" : $item['recurring']['duration'];

                if ($item['recurring']['frequency'] == "month") {
                    $frequency = "monthly";
                } else if ($item['recurring']['frequency'] == "year") {
                    $frequency = "yearly";
                } else {
                    $frequency = $item['recurring']['frequency'];
                }

                if (date('d') >= 1 && date('d') <=28 ) {
                    $charge_date = date('d');
                }  else {
                    $charge_date = "end of month";
                }
               
                $data['amount'] = $this->currency->format($item['recurring']['price'] * $item['quantity'], $order_info['currency_code'], $order_info['currency_value'], false);
                $data['ord_recurring_enable'] = 1;
                $data['ord_recurring_frequency'] = $frequency;
                $data['ord_recurring_start_date'] = date('d/m/Y');
                $data['ord_recurring_recurrence'] = $duration;
                $data['ord_recurring_charging_date'] = $charge_date;
                $data['payment_code'] = "CC";

            }
        }
        header('Set-Cookie: ' . $this->config->get('session_name') . '=' . $this->session->getId() . '; SameSite=None; Secure');
        return $this->load->view('extension/payment/kiplepay', $data);
    }


    public function callback()
    {

        $err_msg = ""; $wc_tranx_id = ""; $wc_entry_id = ""; $wc_method = "";

        $ordid_kpl = explode('-', $_REQUEST['ord_mercref']);
        $ordid_kp = $ordid_kpl[2];
        $order_id = $ordid_kp;

        $returncode = $_REQUEST['returncode'];
        $key = $_REQUEST['ord_key'];
        $wc_entry_id= (!empty($_REQUEST['entry_id'])) ? $_REQUEST['entry_id'] : "";
        $wc_method = (!empty($_REQUEST['payment_type'])) ? $_REQUEST['payment_type'] : "";
        $wc_tranx_id= (!empty($_REQUEST['wcID'])) ? $_REQUEST['wcID'] : "";
        $amountVal = str_replace('.', '', $_REQUEST['ord_totalamt']);
        $amountVal = str_replace(',', '', $amountVal);
        $chkOrdKey = sha1($this->config->get('payment_kiplepay_merchant_private_key').$this->config->get('payment_kiplepay_app_id').$_REQUEST['ord_mercref'].$amountVal.$returncode);
        $invalidKey = ($key == $chkOrdKey) ? true : false;

        

        $recurringOrderId="";
        if (array_key_exists(3,$ordid_kpl)){
            $recurringOrderId = $ordid_kpl[3];
        }
        
        $this->load->model('extension/payment/kiplepay');
        $this->load->model('checkout/order');

        $order_det = $this->model_checkout_order->getOrder($order_id);

        if($order_det['order_status_id'] == $this->config->get('payment_kiplepay_order_status_id')){
        //$this->session->data['error'] = 'Payment for Ref No: '.$_REQUEST['ord_mercref'].' Already Success. Tranx Date & Time : '.gmdate("Y-m-d H:i:s",time()+(8*60*60)).' MYS';
            $this->response->redirect($this->url->link('checkout/success', '', true));
            exit;
        }
       $invalidKey = ($key == $chkOrdKey) ? true : false;
        if ($returncode == '100' && $invalidKey == true) {
            $result=true;
            $this->cart->clear();
            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_kiplepay_order_status_id'), 'Payment Success. WC Tranx ID: ('.$wc_tranx_id.'|'.$wc_entry_id.') for Ref No: '.$_REQUEST['ord_mercref'].', Method: '.$wc_method.', Tranx Date & Time : '.gmdate("Y-m-d H:i:s",time()+(8*60*60)).' MYS', true);
            if (!empty($recurringOrderId)) {

                $tran_exist = $this->model_extension_payment_kiplepay->getRecurringTransaction($_REQUEST['wcID']);

                if ($tran_exist) {
                    $this->response->redirect($this->url->link('checkout/checkout', '', true)); exit;
                }

                $this->model_extension_payment_kiplepay->addRecurringTransaction($recurringOrderId, $_REQUEST['wcID'], $amountVal, true);
            }
            $this->response->redirect($this->url->link('checkout/success'));
        } else {
            $result=false;
            if (!empty($recurringOrderId)) {

                 $tran_exist = $this->model_extension_payment_kiplepay->getRecurringTransaction($_REQUEST['wcID']);

                if ($tran_exist) {
                    $this->response->redirect($this->url->link('checkout/checkout', '', true)); exit;
                }
            
                $this->model_extension_payment_kiplepay->addRecurringTransaction($recurringOrderId, $_REQUEST['wcID'], $amountVal, false);
            }

            if ($returncode == 'E1'){
                $err_msg = (!empty($_REQUEST['error_description'])) ? $_REQUEST['error_description'] : "payment process incomplete.";
                $rest = $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_kiplepay_order_failed_status_id'), 'Payment failed due '.$err_msg.'. WC Entry ID: '.$wc_entry_id.' for Ref No: '.$_REQUEST['ord_mercref'].', Method: '.$wc_method.', Tranx Date & Time : '.gmdate("Y-m-d H:i:s",time()+(8*60*60)).' MYS', false);
            } else if ($returncode == 'E2') {
                $err_msg = (!empty($_REQUEST['error_description'])) ? $_REQUEST['error_description'] : "payment process aborted / timeout.";
                $rest = $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_kiplepay_order_canceled_status_id'), 'Payment canceled due payment process aborted / timeout. WC Entry ID: '.$wc_entry_id.' for Ref No: '.$_REQUEST['ord_mercref'].', Method: '.$wc_method.', Tranx Date & Time : '.gmdate("Y-m-d H:i:s",time()+(8*60*60)).' MYS', false);
            } elseif($order_det['order_status_id'] > 0 && !empty($order_det['order_status']) && !in_array($order_det['order_status'], array("Complete","complete","Completed","completed","Processing","processing","Shipped","shipped"))) {
                $err_msg = (!empty($returncode) && $returncode == "-1") ? "Validation Failed at payment gateway" : "Integration Failed at payment gateway";
                $rest = $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_kiplepay_order_failed_status_id'), 'Payment failed due '.$err_msg.'. Ref No: '.$_REQUEST['ord_mercref'].', Tranx Date & Time : '.gmdate("Y-m-d H:i:s",time()+(8*60*60)).' MYS', false);
            }

            if(in_array($returncode,array('E1','E2','-1'))){
                $this->session->data['error'] = 'Payment for Ref No: '.$_REQUEST['ord_mercref'].' Failed due '.$err_msg.', Method: '.$wc_method.', Tranx Date & Time : '.gmdate("Y-m-d H:i:s",time()+(8*60*60)).' MYS';
            }
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }
    }

    public function recurringCallback()
    {
        $ordid_kpl = explode('-', $_REQUEST['ord_mercref']);
        $returncode = $_REQUEST['returncode'];
        $amountVal = $_REQUEST['ord_totalamt'];
        $recurringOrderId="";
        if ($ordid_kpl[3]){
            $recurringOrderId = $ordid_kpl[3];
        }
       
        $this->load->model('extension/payment/kiplepay');

        
        if ($returncode == '100') {
                $tran_exist = $this->model_extension_payment_kiplepay->getRecurringTransaction($_REQUEST['wcID']);

                if ($tran_exist) {
                    echo "reference no already exist" ; exit;
                }

                $this->model_extension_payment_kiplepay->addRecurringTransaction($recurringOrderId, $_REQUEST['wcID'], $amountVal, true);
            
            echo "success transaction added successfully"; exit;
        } else {
            $tran_exist = $this->model_extension_payment_kiplepay->getRecurringTransaction($_REQUEST['wcID']);

                if ($tran_exist) {
                    echo "reference no already exist" ; exit;
                }
            
                $this->model_extension_payment_kiplepay->addRecurringTransaction($recurringOrderId, $_REQUEST['wcID'], $amountVal, false);
                echo "fail transaction added successfully"; exit;
        }
    }



}

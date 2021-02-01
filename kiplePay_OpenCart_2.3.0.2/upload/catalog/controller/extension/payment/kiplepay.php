<?php
class ControllerExtensionPaymentkiplepay extends Controller
{
    protected $errors = array();

    public function index()
    {
        $this->load->model('checkout/order');

        $this->load->language('extension/payment/kiplepay');

        $data['button_confirm'] = $this->language->get('button_confirm');

        if ($this->config->get('kiplepay_test') == 'sandbox') {
            $data['action'] = 'https://uat.kiplepay.com/wcgatewayinit.php';
        } else {
            $data['action'] = 'https://kiplepay.com/wcgatewayinit.php';
        }

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $data['language'] = $this->session->data['language'];
        $data['logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');

        $order_id = 'kp-'.uniqid().'-'.$this->session->data['order_id'];

        $data['merchant'] = $this->config->get('kiplepay_app_id');
        $data['order_id'] = $order_id;
        $data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $data['currency'] = $order_info['currency_code'];
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

        $amountVal =  $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $amountVal = str_replace('.', '', $amountVal);


        $data['hashvalue'] = sha1($this->config->get('kiplepay_secret').$this->config->get('kiplepay_app_id'). $order_id .$amountVal);
        $data['return_url'] = $this->url->link('extension/payment/kiplepay/callback');

        header('Set-Cookie: ' . $this->config->get('session_name') . '=' . $this->session->getId() . '; SameSite=None; Secure');
        return $this->load->view('extension/payment/kiplepay', $data);
    }


    public function callback()
    {


        $ordid_kpl = explode('-', $_REQUEST['ord_mercref']);
        $ordid_kp = $ordid_kpl[2];
        $order_id = $ordid_kp;


        $this->load->model('checkout/order');

        $order_det = $this->model_checkout_order->getOrder($order_id);

        if($order_det['order_status_id'] == $this->config->get('payment_kiplepay_order_status_id')){
            $this->response->redirect($this->url->link('checkout/success', '', true));
            exit;
        }

        $key = $_REQUEST['ord_key'];
        $returncode = $_REQUEST['returncode'];
        $amountVal = str_replace('.', '', $_REQUEST['ord_totalamt']);
        $amountVal = str_replace(',', '', $amountVal);
        $chkOrdKey = sha1($this->config->get('kiplepay_secret').$this->config->get('kiplepay_app_id').$_REQUEST['ord_mercref'].$amountVal.$returncode);
        if ($key == $chkOrdKey) {
            $invalidKey = true;
        } else {
            $invalidKey = false;
        }
        if ($returncode == '100' && $invalidKey == true) {
            $this->cart->clear();
            $result=true;
            $this->load->model('checkout/order');
            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('kiplepay_order_status_id'), 'Payment Success. Ref No: '.$_REQUEST['ord_mercref'], true);
            $this->response->redirect($this->url->link('checkout/success'));
        } else {
            $result=false;

            if ($returncode == 'E1'){
                $err_msg = (!empty($_REQUEST['error_description'])) ? $_REQUEST['error_description'] : "payment process incomplete.";
                $rest = $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('kiplepay_failed_status_id'), 'Payment failed due '.$err_msg.'. WC Entry ID: '.$wc_entry_id.' for Ref No: '.$_REQUEST['ord_mercref'].', Method: '.$wc_method.', Tranx Date & Time : '.gmdate("Y-m-d H:i:s",time()+(8*60*60)).' MYS', false);
            } else if ($returncode == 'E2') {
                $err_msg = (!empty($_REQUEST['error_description'])) ? $_REQUEST['error_description'] : "payment process aborted / timeout.";
                $rest = $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('kiplepay_canceled_status_id'), 'Payment canceled due payment process aborted / timeout. WC Entry ID: '.$wc_entry_id.' for Ref No: '.$_REQUEST['ord_mercref'].', Method: '.$wc_method.', Tranx Date & Time : '.gmdate("Y-m-d H:i:s",time()+(8*60*60)).' MYS', false);
            } elseif($order_det['order_status_id'] > 0 && !empty($order_det['order_status']) && !in_array($order_det['order_status'], array("Complete","complete","Completed","completed","Processing","processing","Shipped","shipped"))) {
                $err_msg = (!empty($returncode) && $returncode == "-1") ? "Validation Failed at payment gateway" : "Integration Failed at payment gateway";
                $rest = $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('kiplepay_failed_status_id'), 'Payment failed due '.$err_msg.'. Ref No: '.$_REQUEST['ord_mercref'].', Tranx Date & Time : '.gmdate("Y-m-d H:i:s",time()+(8*60*60)).' MYS', false);
            }

            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }
    }
}

<?php
class ControllerPaymentwebcash extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$this->load->library('encryption');
		
		$this->data['action'] = 'https://webcash.com.my/wcgatewayinit.php'; // change gateway url here to 

		$this->data['merchant'] = $this->config->get('webcash_merchant');
		$this->data['order_id'] = $order_info['order_id'];
		$this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$this->data['currency'] = $order_info['currency_code'];
		$this->data['description'] = $this->config->get('config_name') . ' - #' . $order_info['order_id'];
		$this->data['name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
		
		if (!$order_info['payment_address_2']) {
			$this->data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
		} else {
			$this->data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_address_2'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
		}
		
		$this->data['postcode'] = $order_info['payment_postcode'];
		$this->data['country'] = $order_info['payment_iso_code_2'];
		$this->data['telephone'] = $order_info['telephone'];
		$this->data['email'] = $order_info['email'];
		$this->data['test'] = $this->config->get('webcash_test');
		$amountVal = str_replace('.', '', $this->data['amount']);
		$this->data['hashvalue'] = sha1($this->config->get('webcash_password').$this->config->get('webcash_merchant').$this->data['order_id'].$amountVal);
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/webcash.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/webcash.tpl';
		} else {
			$this->template = 'default/template/payment/webcash.tpl';
		}	
		
		$this->render();
	}
	
	public function callback() 
	{
		$key = $_REQUEST['ord_key'];
		$returncode = $_REQUEST['returncode'];
		$amountVal = str_replace('.', '', $_REQUEST['ord_totalamt']);
        $amountVal = str_replace(',', '', $amountVal);
        $chkOrdKey = sha1($this->config->get('webcash_password').$this->config->get('webcash_merchant').$_REQUEST['ord_mercref'].$amountVal.$returncode);
        if($key == $chkOrdKey){
        	$invalidKey = true;	
		} else {
			$invalidKey = false;	
		}
		if($returncode == '100' && $invalidKey == true){
			$result=true;
			$this->load->model('checkout/order');
			$this->model_checkout_order->confirm($_POST['ord_mercref'], $this->config->get('webcash_order_status_id'), "Payment ok", TRUE);  
			$this->data['continue'] = (HTTPS_SERVER . 'index.php?route=checkout/success');
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/webcash_success.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/webcash_success.tpl';
			} else {
				$this->template = 'default/template/payment/webcash_success.tpl';
			}	
  			$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		} else {
			$result=false;
			$this->data['continue'] = (HTTPS_SERVER . 'index.php?route=checkout/cart');
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/webcash_failure.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/webcash_failure.tpl';
			} else {
				$this->template = 'default/template/payment/webcash_failure.tpl';
			}
	  		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		}	
	}
}
?>
<?php
class ControllerPaymentkiplepay extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if (!$this->config->get('kiplepay_test')) {
			$data['action'] = 'https://kiplepay.com/wcgatewayinit.php';
		} else {
			$data['action'] = 'https://uat.kiplepay.com/wcgatewayinit.php';
		}

		$data['merchant'] = $this->config->get('kiplepay_merchant');
		$order_id = 'kp-'.uniqid().'-'.$order_info['order_id'];
		$data['order_id'] = $order_id;
		$data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['currency'] = $order_info['currency_code'];
		$data['description'] = $this->config->get('config_name') . ' - #' . $order_info['order_id'];
		$data['name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];

		if (!$order_info['payment_address_2']) {
			$data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
		} else {
			$data['address'] = $order_info['payment_address_1'] . ', ' . $order_info['payment_address_2'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_zone'];
		}

		$data['postcode'] = $order_info['payment_postcode'];
		$data['country'] = $order_info['payment_iso_code_2'];
		$data['telephone'] = $order_info['telephone'];
		$data['email'] = $order_info['email'];
		$data['test'] = $this->config->get('kiplepay_test');

		$amountVal =  $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $amountVal = str_replace('.', '', $amountVal);


        $data['hashvalue'] = sha1($this->config->get('kiplepay_password').$this->config->get('kiplepay_merchant'). $data['order_id'] .$amountVal);
        $data['return_url'] = $this->url->link('payment/kiplepay/callback');
        $data['version'] = '2.0';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/payment/kiplepay.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/payment/kiplepay.tpl', $data);
		} else {
			return $this->load->view('/payment/kiplepay.tpl', $data);
		}
	}

	public function callback() 
		
	{
	
			$this->load->language('payment/kiplepay');

		$data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		if (!$this->request->server['HTTPS']) {
			$data['base'] = $this->config->get('config_url');
		} else {
			$data['base'] = $this->config->get('config_ssl');
		}

		$data['language'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		$data['text_response'] = $this->language->get('text_response');
		$data['text_success'] = $this->language->get('text_success');
		$data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), $this->url->link('checkout/success'));
		$data['text_failure'] = $this->language->get('text_failure');
		$data['text_failure_wait'] = sprintf($this->language->get('text_failure_wait'), $this->url->link('checkout/checkout', '', 'SSL'));
		
		
		$ordid_kpl = explode('-', $_REQUEST['ord_mercref']);
        $ordid_kpl = $ordid_kpl[2];
		$order_id = $ordid_kpl;
		
		$key = $_REQUEST['ord_key'];
		$returncode = $_REQUEST['returncode'];
		$amountVal = str_replace('.', '', $_REQUEST['ord_totalamt']);
        $amountVal = str_replace(',', '', $amountVal);
        $chkOrdKey = sha1($this->config->get('kiplepay_password').$this->config->get('kiplepay_merchant').$_REQUEST['ord_mercref'].$amountVal.$returncode);
        if($key == $chkOrdKey){
        	$invalidKey = true;	
		} else {
			$invalidKey = false;	
		}
		if($returncode == '100' && $invalidKey == true){
			$result=true;
	
			$this->load->model('checkout/order');

			// Payment Success
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('kiplepay_order_status_id'), "PAYMENT SUCCESS", TRUE);
			$data['continue'] = $this->url->link('checkout/success');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/payment/kiplepay_success.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/payment/kiplepay_success.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('/payment/kiplepay_success.tpl', $data));
			}
			
		} else {
			$data['continue'] = $this->url->link('checkout/cart');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/payment/kiplepay_failure.tpl')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/payment/kiplepay_failure.tpl', $data));
			} else {
				$this->response->setOutput($this->load->view('/payment/kiplepay_failure.tpl', $data));
			}
		}
	}
}
<?php 
class ControllerPaymentwebcash extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/webcash');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('webcash', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_successful'] = $this->language->get('text_successful');
		$this->data['text_declined'] = $this->language->get('text_declined');
		$this->data['text_off'] = $this->language->get('text_off');
		
		$this->data['entry_merchant'] = $this->language->get('entry_merchant');
		$this->data['entry_password'] = $this->language->get('entry_password');
		$this->data['entry_callback'] = $this->language->get('entry_callback');
		$this->data['entry_test'] = $this->language->get('entry_test');
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');		
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['merchant'])) {
			$this->data['error_merchant'] = $this->error['merchant'];
		} else {
			$this->data['error_merchant'] = '';
		}

 		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/webcash', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/webcash', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['webcash_merchant'])) {
			$this->data['webcash_merchant'] = $this->request->post['webcash_merchant'];
		} else {
			$this->data['webcash_merchant'] = $this->config->get('webcash_merchant');
		}
		
		if (isset($this->request->post['webcash_password'])) {
			$this->data['webcash_password'] = $this->request->post['webcash_password'];
		} else {
			$this->data['webcash_password'] = $this->config->get('webcash_password');
		}
		
		$this->data['callback'] = HTTP_CATALOG . 'index.php?route=payment/webcash/callback';

		if (isset($this->request->post['webcash_test'])) {
			$this->data['webcash_test'] = $this->request->post['webcash_test'];
		} else {
			$this->data['webcash_test'] = $this->config->get('webcash_test');
		}
		
		if (isset($this->request->post['webcash_total'])) {
			$this->data['webcash_total'] = $this->request->post['webcash_total'];
		} else {
			$this->data['webcash_total'] = $this->config->get('webcash_total'); 
		} 
				
		if (isset($this->request->post['webcash_order_status_id'])) {
			$this->data['webcash_order_status_id'] = $this->request->post['webcash_order_status_id'];
		} else {
			$this->data['webcash_order_status_id'] = $this->config->get('webcash_order_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['webcash_geo_zone_id'])) {
			$this->data['webcash_geo_zone_id'] = $this->request->post['webcash_geo_zone_id'];
		} else {
			$this->data['webcash_geo_zone_id'] = $this->config->get('webcash_geo_zone_id'); 
		} 

		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['webcash_status'])) {
			$this->data['webcash_status'] = $this->request->post['webcash_status'];
		} else {
			$this->data['webcash_status'] = $this->config->get('webcash_status');
		}
		
		if (isset($this->request->post['webcash_sort_order'])) {
			$this->data['webcash_sort_order'] = $this->request->post['webcash_sort_order'];
		} else {
			$this->data['webcash_sort_order'] = $this->config->get('webcash_sort_order');
		}

		$this->template = 'payment/webcash.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/webcash')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['webcash_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}
		
		if (!$this->request->post['webcash_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>
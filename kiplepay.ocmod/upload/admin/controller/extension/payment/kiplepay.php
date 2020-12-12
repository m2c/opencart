<?php
class ControllerExtensionPaymentKiplepay extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/kiplepay');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_kiplepay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['app_id'])) {
			$data['error_app_id'] = $this->error['app_id'];
		} else {
			$data['error_app_id'] = '';
		}

		if (isset($this->error['merchant_private_key'])) {
			$data['error_merchant_private_key'] = $this->error['merchant_private_key'];
		} else {
			$data['error_merchant_private_key'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/kiplepay', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/kiplepay', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_kiplepay_app_id'])) {
			$data['payment_kiplepay_app_id'] = $this->request->post['payment_kiplepay_app_id'];
		} else {
			$data['payment_kiplepay_app_id'] = $this->config->get('payment_kiplepay_app_id');
		}

		if (isset($this->request->post['payment_kiplepay_merchant_private_key'])) {
			$data['payment_kiplepay_merchant_private_key'] = $this->request->post['payment_kiplepay_merchant_private_key'];
		} else {
			$data['payment_kiplepay_merchant_private_key'] = $this->config->get('payment_kiplepay_merchant_private_key');
		}

		if (isset($this->request->post['payment_kiplepay_total'])) {
			$data['payment_kiplepay_total'] = $this->request->post['payment_kiplepay_total'];
		} else {
			$data['payment_kiplepay_total'] = $this->config->get('payment_kiplepay_total');
		}

		if (isset($this->request->post['payment_kiplepay_order_status_id'])) {
			$data['payment_kiplepay_order_status_id'] = $this->request->post['payment_kiplepay_order_status_id'];
		} else {
			$data['payment_kiplepay_order_status_id'] = $this->config->get('payment_kiplepay_order_status_id');
		}

		if (isset($this->request->post['payment_kiplepay_order_pending_status_id'])) {
			$data['payment_kiplepay_order_pending_status_id'] = $this->request->post['payment_kiplepay_order_pending_status_id'];
		} else {
			$data['payment_kiplepay_order_pending_status_id'] = $this->config->get('payment_kiplepay_order_pending_status_id');
		}

		if (isset($this->request->post['payment_kiplepay_order_canceled_status_id'])) {
			$data['payment_kiplepay_order_canceled_status_id'] = $this->request->post['payment_kiplepay_order_canceled_status_id'];
		} else {
			$data['payment_kiplepay_order_canceled_status_id'] = $this->config->get('payment_kiplepay_order_canceled_status_id');
		}

		if (isset($this->request->post['payment_kiplepay_order_failed_status_id'])) {
			$data['payment_kiplepay_order_failed_status_id'] = $this->request->post['payment_kiplepay_order_failed_status_id'];
		} else {
			$data['payment_kiplepay_order_failed_status_id'] = $this->config->get('payment_kiplepay_order_failed_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_kiplepay_geo_zone_id'])) {
			$data['payment_kiplepay_geo_zone_id'] = $this->request->post['payment_kiplepay_geo_zone_id'];
		} else {
			$data['payment_kiplepay_geo_zone_id'] = $this->config->get('payment_kiplepay_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_kiplepay_test'])) {
			$data['payment_kiplepay_test'] = $this->request->post['payment_kiplepay_test'];
		} else {
			$data['payment_kiplepay_test'] = $this->config->get('payment_kiplepay_test');
		}

		if (isset($this->request->post['payment_kiplepay_status'])) {
			$data['payment_kiplepay_status'] = $this->request->post['payment_kiplepay_status'];
		} else {
			$data['payment_kiplepay_status'] = $this->config->get('payment_kiplepay_status');
		}

		if (isset($this->request->post['payment_kiplepay_sort_order'])) {
			$data['payment_kiplepay_sort_order'] = $this->request->post['payment_kiplepay_sort_order'];
		} else {
			$data['payment_kiplepay_sort_order'] = $this->config->get('payment_kiplepay_sort_order');
		}

		if (isset($this->request->post['payment_kiplepay_recurring_status'])) {
			$data['payment_kiplepay_recurring_status'] = $this->request->post['payment_kiplepay_recurring_status'];
		} else {
			$data['payment_kiplepay_recurring_status'] = $this->config->get('payment_kiplepay_recurring_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/kiplepay', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/kiplepay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_kiplepay_app_id']) {
			$this->error['app_id'] = $this->language->get('error_app_id');
		}

		if (!$this->request->post['payment_kiplepay_merchant_private_key']) {
			$this->error['merchant_private_key'] = $this->language->get('error_merchant_private_key');
		}

		return !$this->error;
	}
	
	
	public function install() {
		$this->load->model('extension/payment/kiplepay');
		$this->model_extension_payment_kiplepay->install();
	}

	public function uninstall() {
		$this->load->model('extension/payment/kiplepay');
		$this->model_extension_payment_kiplepay->uninstall();
	}
}
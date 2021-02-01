<?php
class ControllerExtensionPaymentKiplepay extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/kiplepay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('kiplepay', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['entry_app_id'] = $this->language->get('entry_app_id');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_pending_status'] = $this->language->get('entry_pending_status');
        $data['entry_canceled_status'] = $this->language->get('entry_canceled_status');
        $data['entry_failed_status'] = $this->language->get('entry_failed_status');
        $data['entry_chargeback_status'] = $this->language->get('entry_chargeback_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');

        $data['text_live'] = $this->language->get('text_live');
        $data['text_sandbox'] = $this->language->get('text_sandbox');
        $data['entry_test'] = $this->language->get('entry_test');
        $data['help_test'] = $this->language->get('help_test');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_mb_id'] = $this->language->get('entry_mb_id');
        $data['entry_secret'] = $this->language->get('entry_secret');
        $data['entry_custnote'] = $this->language->get('entry_custnote');

        $data['help_total'] = $this->language->get('help_total');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

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

        if (isset($this->error['private_key'])) {
            $data['error_private_key'] = $this->error['private_key'];
        } else {
            $data['error_private_key'] = '';
        }



        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/kiplepay', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/kiplepay', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

        if (isset($this->request->post['kiplepay_app_id'])) {
            $data['kiplepay_app_id'] = $this->request->post['kiplepay_app_id'];
        } else {
            $data['kiplepay_app_id'] = $this->config->get('kiplepay_app_id');
        }

        if (isset($this->request->post['kiplepay_secret'])) {
            $data['kiplepay_secret'] = $this->request->post['kiplepay_secret'];
        } else {
            $data['kiplepay_secret'] = $this->config->get('kiplepay_secret');
        }

        if (isset($this->request->post['kiplepay_total'])) {
            $data['kiplepay_total'] = $this->request->post['kiplepay_total'];
        } else {
            $data['kiplepay_total'] = $this->config->get('kiplepay_total');
        }

        if (isset($this->request->post['kiplepay_order_status_id'])) {
            $data['kiplepay_order_status_id'] = $this->request->post['kiplepay_order_status_id'];
        } else {
            $data['kiplepay_order_status_id'] = $this->config->get('kiplepay_order_status_id');
        }

        if (isset($this->request->post['kiplepay_pending_status_id'])) {
            $data['kiplepay_pending_status_id'] = $this->request->post['kiplepay_pending_status_id'];
        } else {
            $data['kiplepay_pending_status_id'] = $this->config->get('kiplepay_pending_status_id');
        }

        if (isset($this->request->post['kiplepay_canceled_status_id'])) {
            $data['kiplepay_canceled_status_id'] = $this->request->post['kiplepay_canceled_status_id'];
        } else {
            $data['kiplepay_canceled_status_id'] = $this->config->get('kiplepay_canceled_status_id');
        }

        if (isset($this->request->post['kiplepay_failed_status_id'])) {
            $data['kiplepay_failed_status_id'] = $this->request->post['kiplepay_failed_status_id'];
        } else {
            $data['kiplepay_failed_status_id'] = $this->config->get('kiplepay_failed_status_id');
        }

        if (isset($this->request->post['kiplepay_chargeback_status_id'])) {
            $data['kiplepay_chargeback_status_id'] = $this->request->post['kiplepay_chargeback_status_id'];
        } else {
            $data['kiplepay_chargeback_status_id'] = $this->config->get('kiplepay_chargeback_status_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['kiplepay_geo_zone_id'])) {
            $data['kiplepay_geo_zone_id'] = $this->request->post['kiplepay_geo_zone_id'];
        } else {
            $data['kiplepay_geo_zone_id'] = $this->config->get('kiplepay_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['kiplepay_status'])) {
            $data['kiplepay_status'] = $this->request->post['kiplepay_status'];
        } else {
            $data['kiplepay_status'] = $this->config->get('kiplepay_status');
        }

        if (isset($this->request->post['kiplepay_sort_order'])) {
            $data['kiplepay_sort_order'] = $this->request->post['kiplepay_sort_order'];
        } else {
            $data['kiplepay_sort_order'] = $this->config->get('kiplepay_sort_order');
        }

        if (isset($this->request->post['kiplepay_rid'])) {
            $data['kiplepay_rid'] = $this->request->post['kiplepay_rid'];
        } else {
            $data['kiplepay_rid'] = $this->config->get('kiplepay_rid');
        }

        if (isset($this->request->post['kiplepay_custnote'])) {
            $data['kiplepay_custnote'] = $this->request->post['kiplepay_custnote'];
        } else {
            $data['kiplepay_custnote'] = $this->config->get('kiplepay_custnote');
        }

        if (isset($this->request->post['kiplepay_test'])) {
            $data['kiplepay_test'] = $this->request->post['kiplepay_test'];
        } else {
            $data['kiplepay_test'] = $this->config->get('kiplepay_test');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/kiplepay', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/kiplepay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['kiplepay_app_id']) {
            $this->error['app_id'] = $this->language->get('error_app_id');
        }

        if (!$this->request->post['kiplepay_secret']) {
            $this->error['private_key'] = $this->language->get('error_merchant_private_key');
        }

        return !$this->error;
    }
}

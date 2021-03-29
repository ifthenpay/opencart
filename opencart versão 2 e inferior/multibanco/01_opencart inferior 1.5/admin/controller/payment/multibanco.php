<?php
class ControllerPaymentMultibanco extends Controller {
	private $error = array(); 
	
	public function index() {
		$this->load->language('payment/multibanco');
		
		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('multibanco', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');
		
			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['entry_notice'] = $this->language->get('entry_notice');
		$this->data['error_no_number'] = $this->language->get('error_no_number');
		$this->data['error_no_number2'] = $this->language->get('error_no_number2');
		
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
				
		$this->data['entidade'] = $this->language->get('entidade');
		$this->data['sub_entidade'] = $this->language->get('sub_entidade');	
		
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
 		
		if (isset($this->error['entidade'])) {
			$this->data['error_entidade'] = $this->error['entidade'];
		} else {
			$this->data['error_entidade'] = '';
		}
 		
		if (isset($this->error['sub_entidade'])) {
			$this->data['error_sub_entidade'] = $this->error['sub_entidade'];
		} else {
			$this->data['error_sub_entidade'] = '';
		}
		
  		$this->document->breadcrumbs = array();
		
   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);
		
   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);
		
   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=payment/multibanco&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/multibanco&token=' . $this->session->data['token'];
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
		
		if (isset($this->request->post['multibanco_entidade'])) {
			$this->data['multibanco_entidade'] = $this->request->post['multibanco_entidade'];
		} else {
			$this->data['multibanco_entidade'] = $this->config->get('multibanco_entidade');
		}
		
		if (isset($this->request->post['multibanco_sub_entidade'])) {
			$this->data['multibanco_sub_entidade'] = $this->request->post['multibanco_sub_entidade'];
		} else {
			$this->data['multibanco_sub_entidade'] = $this->config->get('multibanco_sub_entidade'); 
		} 

		// if (isset($this->request->post['moneybookers_order_status_pending_id'])) {
			// $this->data['moneybookers_order_status_pending_id'] = $this->request->post['moneybookers_order_status_pending_id'];
		// } else {
			// $this->data['moneybookers_order_status_pending_id'] = $this->config->get('moneybookers_order_status_pending_id');
		// }

		// if (isset($this->request->post['moneybookers_order_status_canceled_id'])) {
			// $this->data['moneybookers_order_status_canceled_id'] = $this->request->post['moneybookers_order_status_canceled_id'];
		// } else {
			// $this->data['moneybookers_order_status_canceled_id'] = $this->config->get('moneybookers_order_status_canceled_id');
		// }

		// if (isset($this->request->post['moneybookers_order_status_failed_id'])) {
			// $this->data['moneybookers_order_status_failed_id'] = $this->request->post['moneybookers_order_status_failed_id'];
		// } else {
			// $this->data['moneybookers_order_status_failed_id'] = $this->config->get('moneybookers_order_status_failed_id');
		// }

		// if (isset($this->request->post['moneybookers_order_status_chargeback_id'])) {
			// $this->data['moneybookers_order_status_chargeback_id'] = $this->request->post['moneybookers_order_status_chargeback_id'];
		// } else {
			// $this->data['moneybookers_order_status_chargeback_id'] = $this->config->get('moneybookers_order_status_chargeback_id');
		// }
		
		//$this->load->model('localisation/order_status');
		
		// $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['multibanco_geo_zone_id'])) {
			$this->data['multibanco_geo_zone_id'] = $this->request->post['multibanco_geo_zone_id'];
		} else {
			$this->data['multibanco_geo_zone_id'] = $this->config->get('multibanco_geo_zone_id'); 
		} 	
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['multibanco_status'])) {
			$this->data['multibanco_status'] = $this->request->post['multibanco_status'];
		} else {
			$this->data['multibanco_status'] = $this->config->get('multibanco_status');
		}
		
		// if (isset($this->request->post['moneybookers_sort_order'])) {
			// $this->data['moneybookers_sort_order'] = $this->request->post['moneybookers_sort_order'];
		// } else {
			// $this->data['moneybookers_sort_order'] = $this->config->get('moneybookers_sort_order');
		// }
		
		// if (isset($this->request->post['moneybookers_rid'])) {
			// $this->data['moneybookers_rid'] = $this->request->post['moneybookers_rid'];
		// } else {
			// $this->data['moneybookers_rid'] = $this->config->get('moneybookers_rid');
		// }
		
		if (isset($this->request->post['multibanco_custnote'])) {
			$this->data['multibanco_custnote'] = $this->request->post['multibanco_custnote'];
		} else {
			$this->data['multibanco_custnote'] = $this->config->get('multibanco_custnote');
		}
		
		$this->template = 'payment/multibanco.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/multibanco')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['multibanco_entidade']) {
			$this->error['entidade'] = $this->language->get('error_entidade');
		}
		
		if (!$this->request->post['multibanco_sub_entidade']) {
			$this->error['sub_entidade'] = $this->language->get('error_sub_entidade');
		}
				
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>
<?php 
class ControllerPaymentMultibanco extends Controller {
	private $error = array(); 
	 
	public function index() { 
		$this->load->language('payment/multibanco');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('multibanco', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
				
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');		
		$this->data['entry_entidade'] = $this->language->get('entry_entidade');			
		$this->data['entry_subentidade'] = $this->language->get('entry_subentidade');	
		$this->data['entry_valorminimo'] = $this->language->get('entry_valorminimo');	
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
            'href'      => $this->url->link('payment/multibanco', 'token=' . $this->session->data['token'], 'SSL'),
              'separator' => ' :: '
           );

		
		$this->data['action'] = $this->url->link('payment/multibanco', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');	
		
		if (isset($this->request->post['multibanco_entidade'])) {
			$this->data['multibanco_entidade'] = $this->request->post['multibanco_entidade'];
		} else {
			$this->data['multibanco_entidade'] = $this->config->get('multibanco_entidade'); 
		}
		
		if (isset($this->request->post['multibanco_subentidade'])) {
			$this->data['multibanco_subentidade'] = $this->request->post['multibanco_subentidade'];
		} else {
			$this->data['multibanco_subentidade'] = $this->config->get('multibanco_subentidade'); 
		}

		if (isset($this->request->post['multibanco_valorminimo'])) {
			$this->data['multibanco_valorminimo'] = $this->request->post['multibanco_valorminimo'];
		} else {
			$this->data['multibanco_valorminimo'] = $this->config->get('multibanco_valorminimo'); 
		}
				
		if (isset($this->request->post['multibanco_order_status_id'])) {
			$this->data['multibanco_order_status_id'] = $this->request->post['multibanco_order_status_id'];
		} else {
			$this->data['multibanco_order_status_id'] = $this->config->get('multibanco_order_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
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
		
		if (isset($this->request->post['multibanco_sort_order'])) {
			$this->data['multibanco_sort_order'] = $this->request->post['multibanco_sort_order'];
		} else {
			$this->data['multibanco_sort_order'] = $this->config->get('multibanco_sort_order');
		}

		$this->template = 'payment/multibanco.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/multibanco')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>

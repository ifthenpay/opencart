<?php
class ControllerExtensionPaymentMultibanco extends Controller {
	private $error = array();

	public function index() {
		$this->checkUpdate();

		$this->load->language('extension/payment/multibanco');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		$data['url_set_modification'] = $this->url->link('extension/modification/refresh') . "&token=" . $this->session->data['token'];

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$post_info = $this->request->post;

			$this->model_setting_setting->editSetting('multibanco', $post_info);

			$callback_sent = $this->sendCallbackEmail();

			$post_info["multibanco_cb_sent"] =$callback_sent;

			$this->model_setting_setting->editSetting('multibanco', $post_info);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment/multibanco', 'token=' . $this->session->data['token'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');


		$data['text_success'] = (isset($this->session->data['success'])?$this->session->data['success']:"");

		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_order_status_complete'] = $this->language->get('entry_order_status_complete');
		$data['entry_entidade'] = $this->language->get('entry_entidade');
		$data['entry_subentidade'] = $this->language->get('entry_subentidade');
		$data['entry_valorminimo'] = $this->language->get('entry_valorminimo');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_cb'] = $this->language->get('entry_cb');
		$data['entry_url'] = $this->language->get('entry_url');
		$data['entry_ap'] = $this->language->get('entry_ap');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'href' => $this->url->link('extension/payment/multibanco', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('extension/payment/multibanco', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

		if (isset($this->request->post['multibanco_entidade'])) {
			$data['multibanco_entidade'] = $this->request->post['multibanco_entidade'];
		} else {
			$data['multibanco_entidade'] = $this->config->get('multibanco_entidade');
		}

		if (isset($this->request->post['multibanco_subentidade'])) {
			$data['multibanco_subentidade'] = $this->request->post['multibanco_subentidade'];
		} else {
			$data['multibanco_subentidade'] = $this->config->get('multibanco_subentidade');
		}

		if (isset($this->request->post['multibanco_valorminimo'])) {
			$data['multibanco_valorminimo'] = $this->request->post['multibanco_valorminimo'];
		} else {
			$data['multibanco_valorminimo'] = $this->config->get('multibanco_valorminimo');
		}

		if (isset($this->request->post['multibanco_order_status_id'])) {
			$data['multibanco_order_status_id'] = $this->request->post['multibanco_order_status_id'];
		} else {
			$data['multibanco_order_status_id'] = $this->config->get('multibanco_order_status_id');
		}

		if (isset($this->request->post['multibanco_order_status_complete_id'])) {
			$data['multibanco_order_status_complete_id'] = $this->request->post['multibanco_order_status_complete_id'];
		} else {
			$data['multibanco_order_status_complete_id'] = $this->config->get('multibanco_order_status_complete_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['multibanco_geo_zone_id'])) {
			$data['multibanco_geo_zone_id'] = $this->request->post['multibanco_geo_zone_id'];
		} else {
			$data['multibanco_geo_zone_id'] = $this->config->get('multibanco_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['multibanco_status'])) {
			$data['multibanco_status'] = $this->request->post['multibanco_status'];
		} else {
			$data['multibanco_status'] = $this->config->get('multibanco_status');
		}

		if (isset($this->request->post['multibanco_sort_order'])) {
			$data['multibanco_sort_order'] = $this->request->post['multibanco_sort_order'];
		} else {
			$data['multibanco_sort_order'] = $this->config->get('multibanco_sort_order');
		}


		$data['multibanco_show_ap'] = true;

		if (isset($this->request->post['multibanco_ap'])) {
			$data['multibanco_ap'] = $this->request->post['multibanco_ap'];
		} else {

			$anti_phishing = $this->config->get('multibanco_ap');

			if(empty($anti_phishing)) {
				$anti_phishing = substr(hash('sha512', $this->config->get('config_name') . $this->config->get('config_title') . $this->config->get('config_owner') . $this->config->get('config_email') . date("D M d, Y G:i")), -50);

				$data['multibanco_ap'] = $anti_phishing;
				$data['multibanco_show_ap'] = false;
			} else {
				$data['multibanco_ap'] = $anti_phishing;
			}


		}

		/*$url = new Url(HTTP_CATALOG, $this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG);


		$data['multibanco_url'] = $url->link('extension/payment/multibanco/callback') . "&chave=[CHAVE_ANTI_PHISHING]&entidade=[ENTIDADE]&referencia=[REFERENCIA]&valor=[VALOR]";
*/

		$data['multibanco_url'] = ($this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG) . "index.php?route=extension/payment/multibanco/callback&chave=[CHAVE_ANTI_PHISHING]&entidade=[ENTIDADE]&referencia=[REFERENCIA]&valor=[VALOR]";


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/multibanco.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/multibanco')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	private function sendCallbackEmail(){

		$entidade = $this->request->post['multibanco_entidade'];
		$subentidade = $this->request->post['multibanco_subentidade'];
		$url_cb = ($this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG) . "index.php?route=extension/payment/multibanco/callback&chave=[CHAVE_ANTI_PHISHING]&entidade=[ENTIDADE]&referencia=[REFERENCIA]&valor=[VALOR]";

		$ap_key_cb = $this->request->post['multibanco_ap'];

		$sent_ap =$this->config->get('multibanco_cb_sent') ;

		if(!empty($entidade) && !empty($subentidade) && !empty($url_cb) && !empty($ap_key_cb) && !$sent_ap){

			$store_name = $this->config->get('config_name');

			$msg = "Ativar Callback para loja Opencart \n\n";
			$msg .= "Entidade: $entidade \n\n";
			$msg .= "Subentidade: $subentidade \n\n";
			$msg .= "Chave Anti-Phishing: $ap_key_cb \n\n";
			$msg .= "Url Callback:: $url_cb \n\n\n\n\n\n";
			$msg .= "Pedido enviado automaticamente pelo sistema OpenCart da loja [$store_name]";

			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));

			$mail->setSubject("Ativar Callback");
			$mail->setText($msg);

			$mail->setTo("callback@ifthenpay.com");
			$mail->send();

			return true;
		}

		return $sent_ap;

	}

	public function install() {
		$this->load->model('extension/payment/multibanco');

		$this->model_extension_payment_multibanco->install();
	}

	public function checkUpdate() {
		$this->load->model('extension/payment/multibanco');

		$this->model_extension_payment_multibanco->update();
	}
}
?>

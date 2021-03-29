<?php

class ControllerPaymentIfthenpayMbway extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('payment/ifthenpaymbway');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->model_setting_setting->editSetting('ifthenpaymbway', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('payment/ifthenpaymbway', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_order_status_complete'] = $this->language->get('entry_order_status_complete');
        $data['entry_mbwkey'] = $this->language->get('entry_mbwkey');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_cb'] = $this->language->get('entry_cb');
		$data['entry_url'] = $this->language->get('entry_url');
        $data['entry_ap'] = $this->language->get('entry_ap');
        
        //send callback stuff
        $data['entry_button_send_cb'] = $this->language->get('entry_button_send_cb');
        $data['button_send_cb'] = $this->language->get('button_send_cb');
        $data['text_send_cb'] = $this->language->get('text_send_cb');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['tab_general'] = $this->language->get('tab_general');

        //user token
		$data['token'] = $this->session->data['token'];
		$data['email_cb_sended'] = $this->config->get('ifthenpaymbway_cb_sent');
		$data['email_confirmation'] = $this->language->get('email_confirmation');
		$data['email_sended_info'] = $this->language->get('email_sended_info');
		$data['email_success_info'] = $this->language->get('email_success_info');
		$data['email_error_info'] = $this->language->get('email_error_info');


        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/ifthenpaymbway', 'token=' . $this->session->data['token'], 'SSL')
        );


        $data['action'] = $this->url->link('payment/ifthenpaymbway', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');


        if (isset($this->request->post['ifthenpaymbway_mbwkey'])) {
            $data['ifthenpaymbway_mbwkey'] = $this->request->post['ifthenpaymbway_mbwkey'];
        } else {
            $data['ifthenpaymbway_mbwkey'] = $this->config->get('ifthenpaymbway_mbwkey');
        }


        if (isset($this->request->post['ifthenpaymbway_order_status_id'])) {
            $data['ifthenpaymbway_order_status_id'] = $this->request->post['ifthenpaymbway_order_status_id'];
        } else {
            $data['ifthenpaymbway_order_status_id'] = $this->config->get('ifthenpaymbway_order_status_id');
        }

        if (isset($this->request->post['ifthenpaymbway_order_status_complete_id'])) {
			$data['ifthenpaymbway_order_status_complete_id'] = $this->request->post['ifthenpaymbway_order_status_complete_id'];
		} else {
			$data['ifthenpaymbway_order_status_complete_id'] = $this->config->get('ifthenpaymbway_order_status_complete_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['ifthenpaymbway_geo_zone_id'])) {
            $data['ifthenpaymbway_geo_zone_id'] = $this->request->post['ifthenpaymbway_geo_zone_id'];
        } else {
            $data['ifthenpaymbway_geo_zone_id'] = $this->config->get('ifthenpaymbway_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['ifthenpaymbway_status'])) {
            $data['ifthenpaymbway_status'] = $this->request->post['ifthenpaymbway_status'];
        } else {
            $data['ifthenpaymbway_status'] = $this->config->get('ifthenpaymbway_status');
        }

        if (isset($this->request->post['ifthenpaymbway_sort_order'])) {
            $data['ifthenpaymbway_sort_order'] = $this->request->post['ifthenpaymbway_sort_order'];
        } else {
            $data['ifthenpaymbway_sort_order'] = $this->config->get('ifthenpaymbway_sort_order');
        }

        $data['ifthenpaymbway_show_ap'] = true;

        if (isset($this->request->post['ifthenpaymbway_ap'])) {
			$data['ifthenpaymbway_ap'] = $this->request->post['ifthenpaymbway_ap'];
		} else {

			$anti_phishing = $this->config->get('ifthenpaymbway_ap');

			if(empty($anti_phishing)) {
				$anti_phishing = substr(hash('sha512', $this->config->get('config_name') . $this->config->get('config_title') . $this->config->get('config_owner') . $this->config->get('config_email') . date("D M d, Y G:i")), -50);
				$data['ifthenpaymbway_ap'] = $anti_phishing;
				$data['ifthenpaymbway_show_ap'] = false;
				$this->model_setting_setting->editSetting('ifthenpaymbway',  $data);
			} else {
                $data['ifthenpaymbway_ap'] = $anti_phishing;
			}
		}
		
		$data['ifthenpaymbway_url'] = ($this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG) . "index.php?route=payment/ifthenpaymbway/callback&chave=[CHAVE_ANTI_PHISHING]&referencia=[REFERENCIA]&idpedido=[ID_TRANSACAO]&valor=[VALOR]&estado=[ESTADO]";

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/ifthenpaymbway.tpl', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/ifthenpaymbway')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function activatecallback(){
		$json = array();
		$json['sended']=false;
		//load settings model
		$this->load->model('setting/setting');

		$settings = $this->model_setting_setting->getSetting('ifthenpaymbway'); 
		$mbway_key = $settings['ifthenpaymbway_mbwkey'];
		$url_cb = ($this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG) . "index.php?route=payment/ifthenpaymbway/callback&chave=[CHAVE_ANTI_PHISHING]&referencia=[REFERENCIA]&idpedido=[ID_TRANSACAO]&valor=[VALOR]&estado=[ESTADO]";
		$ap_key_cb = $settings['ifthenpaymbway_ap'];

		if(!empty($mbway_key) && !empty($url_cb) && !empty($ap_key_cb)){
			$store_name = $this->config->get('config_name');

			$msg = "Ativar Callback MBWAY para loja Opencart \n\n";
			$msg .= "MBWAY KEY: $mbway_key \n\n";
			$msg .= "Chave Anti-Phishing: $ap_key_cb \n\n";
			$msg .= "Url Callback:: $url_cb \n\n\n\n\n\n";
			$msg .= "Pedido enviado pelo sistema OpenCart da loja [$store_name]";

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

			$mail->setSubject("Ativar Callback MBWAY");
			$mail->setText($msg);

            $mail->setTo("callback@ifthenpay.com");
			$mail->send();

			//atualizar settings
			$settings["ifthenpaymbway_cb_sent"] = true;
			$this->model_setting_setting->editSetting('ifthenpaymbway',  $settings);

			$json['sended']=true;
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
}

?>
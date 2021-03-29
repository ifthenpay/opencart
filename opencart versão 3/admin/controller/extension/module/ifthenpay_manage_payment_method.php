<?php

require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Strategy\Form\IfthenpayConfigForms;

class ControllerExtensionModuleIfthenpayManagePaymentMethod extends Controller {
	private $error = array();
	private $ifthenpayContainer;
	private $configData;
	
	public function index() {
		$this->ifthenpayContainer = new IfthenpayContainer();
		$paymentMethod = $this->request->get['paymentMethod'];
		$title = 'heading_title_' . $paymentMethod;
		$this->load->language('extension/module/ifthenpay_manage_payment_method');

		$this->load->model('setting/setting');
		$this->load->model('extension/payment/ifthenpay');

		$this->document->setTitle($this->language->get($title));
		
		$this->document->addStyle('view/stylesheet/ifthenpay/ifthenpayPaymentMethodSetup.css');
		$this->document->addScript('view/javascript/ifthenpay/adminConfigPage.js');
		

		$this->load->model('setting/setting');
    	$this->configData =  $this->model_setting_setting->getSetting('payment_ifthenpay');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !empty($this->request->post) && $this->validate()) {
			try {
				if (!empty($this->configData)) {
					$this->ifthenpayContainer->getIoc()->make(IfthenpayConfigForms::class)
					->setIfthenpayController($this)
					->setConfigData($this->configData)
					->setPaymentMethod($this->request->get['paymentMethod'])
					->processForm();
				  $this->request->post = array_merge($this->configData, $this->request->post);
				}
				$this->model_setting_setting->editSetting('payment_ifthenpay', $this->request->post);
				$this->session->data['success'] = $this->language->get('success_save');
				$this->model_extension_payment_ifthenpay->log('', 'Payment Configuration saved with success.');
			  $this->response->redirect($this->url->link('extension/payment/ifthenpay', 'user_token=' . $this->session->data['user_token'], true));
			} catch (\Throwable $th) {
				$this->session->data['error_warning'] = $th->getMessage();
				$this->model_extension_payment_ifthenpay->log($th->getMessage(), 'Error saving payment configuration');
			  	$this->response->redirect($this->url->link('extension/module/ifthenpay_manage_payment_method', 'user_token=' . $this->session->data['user_token'] . '&paymentMethod=' . $this->request->get['paymentMethod'], true));
			}
			
		}

		$data['heading_title'] = $this->language->get($title);
		$data['paymentMethod'] = $paymentMethod;
		$data['text_all_zones'] = $this->language->get('text_all_zones');

		$data['text_success'] = (isset($this->session->data['success']) ? $this->session->data['success'] : "");

		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_order_status_complete'] = $this->language->get('entry_order_status_complete');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');

		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['tab_general'] = $this->language->get('tab_general');

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
		$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_ifthenpay'),
			'href' => $this->url->link('extension/payment/ifthenpay', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get($title),
			'href' => $this->url->link('extension/module/ifthenpay_manage_payment_method', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/ifthenpay_manage_payment_method', 'user_token=' . $this->session->data['user_token'], true) . '&paymentMethod=' . $this->request->get['paymentMethod'];
		$data['cancel'] = $this->url->link('extension/payment/ifthenpay', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->post['payment_ifthenpay_activateCallback_' . $paymentMethod])) {
			$data['payment_ifthenpay_activateCallback_' . $paymentMethod] = $this->request->post['payment_ifthenpay_activateCallback_' . $paymentMethod];
		} else if (isset($this->configData['payment_ifthenpay_activateCallback_' . $paymentMethod])) {
			$data['payment_ifthenpay_activateCallback_' . $paymentMethod] = $this->configData['payment_ifthenpay_activateCallback_' . $paymentMethod];
			$this->request->post['payment_ifthenpay_activateCallback_' . $paymentMethod] = $this->configData['payment_ifthenpay_activateCallback_' . $paymentMethod];
		} else {
			$data['payment_ifthenpay_activateCallback_' . $paymentMethod] = '0';
		} 

		if (isset($this->request->post['payment_ifthenpay_' . $paymentMethod . '_order_status_id'])) {
            $data['payment_ifthenpay_' . $paymentMethod . '_order_status_id'] = $this->request->post['payment_ifthenpay_' . $paymentMethod . '_order_status_id'];
        } else {
            $data['payment_ifthenpay_' . $paymentMethod . '_order_status_id'] = $this->config->get('payment_ifthenpay_' . $paymentMethod . '_order_status_id');
        }
        
        if (isset($this->request->post['payment_ifthenpay_' . $paymentMethod . '_order_status_complete_id'])) {
			$data['payment_ifthenpay_' . $paymentMethod . '_order_status_complete_id'] = $this->request->post['payment_ifthenpay_' . $paymentMethod . '_order_status_complete_id'];
		} else {
			$data['payment_ifthenpay_' . $paymentMethod . '_order_status_complete_id'] = $this->config->get('payment_ifthenpay_' . $paymentMethod . '_order_status_complete_id');
		}

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_ifthenpay_' . $paymentMethod . '_geo_zone_id'])) {
            $data['payment_ifthenpay_' . $paymentMethod . '_geo_zone_id'] = $this->request->post['payment_ifthenpay_' . $paymentMethod . '_geo_zone_id'];
        } else {
            $data['payment_ifthenpay_' . $paymentMethod . '_geo_zone_id'] = $this->config->get('payment_ifthenpay_' . $paymentMethod . '_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_ifthenpay_' . $paymentMethod . '_sort_order'])) {
            $data['payment_ifthenpay_' . $paymentMethod . '_sort_order'] = $this->request->post['payment_ifthenpay_' . $paymentMethod . '_sort_order'];
        } else {
            $data['payment_ifthenpay_' . $paymentMethod . '_sort_order'] = $this->config->get('payment_ifthenpay_' . $paymentMethod . '_sort_order');
        }
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		try {
			$paymentFormData = $this->ifthenpayContainer->getIoc()->make(IfthenpayConfigForms::class)
			->setIfthenpayController($this)
			->setConfigData($this->configData)
			->setPaymentMethod($paymentMethod)
			->buildForm();
			$paymentFormData['spinner'] = $this->load->view('extension/payment/spinner');
			$paymentFormData['ifthenpay_chooseNewEntidadeBtn'] = $this->load->view('extension/payment/ifthenpay_chooseNewEntidade', $paymentFormData);
			$data['paymentMethodConfigForm'] = $this->load->view(
				'extension/module/ifthenpay_' . $paymentMethod . '_form', $paymentFormData
			);
			$this->model_extension_payment_ifthenpay->log('', 'Payment Form Loaded with success');
			$this->response->setOutput($this->load->view(
				'extension/module/ifthenpay_manage_payment_method', array_merge($data, $paymentFormData)
			));
		} catch (\Throwable $th) {
			$this->model_extension_payment_ifthenpay->log($th->getMessage(), 'Error Loading Payment Form');
			$data['error_warning'] = $th->getMessage();
			$this->response->setOutput($this->load->view(
				'extension/module/ifthenpay_manage_payment_method', array_merge($data)
			));
		}
		
						
		
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/ifthenpay_manage_payment_method')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function getSubEntidade()
	{
		try {
			$this->load->model('setting/setting');
			$this->load->model('extension/payment/ifthenpay');
    		$this->configData =  $this->model_setting_setting->getSetting('payment_ifthenpay');
			$this->ifthenpayContainer = new IfthenpayContainer();
			$ifthenpayGateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);
            $ifthenpayGateway->setAccount((array) unserialize($this->configData['payment_ifthenpay_userAccount']));
            $subEntidades = json_encode($ifthenpayGateway->getSubEntidadeInEntidade($this->request->post['entidade']));
			$this->model_extension_payment_ifthenpay->log('', 'SubEntidades load with sucess');
            $this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput($subEntidades);
        } catch (\Throwable $th) {
			$this->model_extension_payment_ifthenpay->log($th->getMessage(), 'Error loading SubEntidades');
            $this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput($th->getMessage());
        }
		
	}

	public function chooseNewEntidade()
    {
		$this->load->model('extension/payment/ifthenpay');
        $this->paymentMethod = $this->request->post['paymentMethod'];
        try {
			$this->ifthenpayContainer = new IfthenpayContainer();
			$this->ifthenpayContainer->getIoc()->make(IfthenpayConfigForms::class)
					->setIfthenpayController($this)
					->setPaymentMethod($this->paymentMethod)
					->deleteConfigFormValues();
			$this->model_extension_payment_ifthenpay->log('', 'Entity/SubEntity reseted with success');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode('Entity/SubEntity reseted with success'));
        } catch (\Throwable $th) {
			$this->model_extension_payment_ifthenpay->log($th->getMessage(), 'Error reseting Entity/SubEntity');
            $this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($th->getMessage()));
        }
    }
}
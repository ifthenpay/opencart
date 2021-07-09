<?php

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayUpgrade;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Strategy\Form\IfthenpayConfigForms;
use Ifthenpay\Strategy\Payments\IfthenpayPaymentStatus;


require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class ControllerExtensionPaymentMbway extends Controller {
  private $ifthenpayContainer;
  private $error = [];
  private $paymentMethod = 'mbway';

  public function index() 
  {
    $this->ifthenpayContainer = new IfthenpayContainer();
    $this->load->language('extension/payment/mbway');

    $this->document->setTitle($this->language->get('heading_title'));
      $this->document->addStyle('view/stylesheet/ifthenpay/ifthenpayConfig.css');
    $this->document->addScript('view/javascript/ifthenpay/adminConfigPage.js');

    $this->load->model('extension/payment/mbway');
    $this->load->model('setting/setting');
    $configData =  $this->model_setting_setting->getSetting('payment_mbway');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && !empty($this->request->post) && $this->validate()) {
        try {
          if (!empty($configData)) {
            $this->request->post = array_merge($configData, $this->request->post);
          }
          $this->ifthenpayContainer->getIoc()->make(IfthenpayConfigForms::class)
              ->setIfthenpayController($this)
              ->setConfigData($this->configData)
              ->setPaymentMethod($this->paymentMethod)
              ->processForm();
          $this->model_setting_setting->editSetting('payment_mbway', $this->request->post);
          $this->session->data['success'] = $this->language->get('text_success');
          $this->response->redirect($this->url->link('extension/payment/mbway', 'user_token=' . $this->session->data['user_token'], true));
          $this->model_extension_payment_mbway->log('', 'Payment Configuration saved with success.');
        } catch (\Throwable $th) {
            $this->session->data['error_warning'] = $th->getMessage();
            $this->model_extension_payment_mbway->log($th->getMessage(), 'Error saving payment configuration');
            $this->response->redirect($this->url->link('extension/module/mbway', 'user_token=' . $this->session->data['user_token'], true));
        }
	  }

    if (isset($this->error['warning']) && $this->error !== '') {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }
    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    } else {
      $data['success'] = '';
    }

    $data['text_enabled'] = $this->language->get('text_enabled');
    $data['heading_title'] = $this->language->get('heading_title');
    $data['paymentMethod'] = $this->paymentMethod;
    $data['text_all_zones'] = $this->language->get('text_all_zones');
    $data['text_success'] = (isset($this->session->data['success']) ? $this->session->data['success'] : '');
    $data['create_account_now'] = $this->language->get('create_account_now');
    $data['entry_backofficeKey'] = $this->language->get('entry_backofficeKey');
    $data['add_new_accounts'] = $this->language->get('add_new_accounts');
    $data['reset_accounts'] = $this->language->get('reset_accounts');
    $data['sandbox_help'] = $this->language->get('sandbox_help');
    $data['sandbox_mode'] = $this->language->get('sandbox_mode');
    $data['entry_status'] = $this->language->get('entry_status');
    $data['add_new_accounts'] = $this->language->get('add_new_accounts');
    $data['reset_accounts'] = $this->language->get('reset_accounts');


    $data['entry_order_status'] = $this->language->get('entry_order_status');
    $data['entry_order_status_complete'] = $this->language->get('entry_order_status_complete');
    $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
    
    $data['button_save'] = $this->language->get('button_save');
    $data['button_cancel'] = $this->language->get('button_cancel');
    $data['tab_general'] = $this->language->get('tab_general');

    //user token
    $data['user_token'] = $this->session->data['user_token'];
      
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
        'text' => $this->language->get('text_extension'),
        'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
    );

    $data['breadcrumbs'][] = array(
        'text' => $this->language->get('heading_title'),
        'href' => $this->url->link('extension/payment/mbway', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['action'] = $this->url->link('extension/payment/mbway', 'user_token=' . $this->session->data['user_token'], true);
    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

    if (isset($this->request->post['payment_mbway_backofficeKey'])) {
      $data['payment_mbway_backofficeKey'] = $this->request->post['payment_mbway_backofficeKey'];
    } else {
      $data['payment_mbway_backofficeKey'] = $this->config->get('payment_mbway_backofficeKey');
    }

    if (isset($this->request->post['payment_mbway_sandboxMode'])) {
      $data['payment_mbway_sandboxMode'] = $this->request->post['payment_mbway_sandboxMode'];
    } else {
      $data['payment_mbway_sandboxMode'] = $this->config->get('payment_mbway_sandboxMode');
    }

    if (isset($this->request->post['payment_mbway_status'])) {
        $data['payment_mbway_status'] = $this->request->post['payment_mbway_status'];
    } else {
        $data['payment_mbway_status'] = $this->config->get('payment_mbway_status');
    }

    if (isset($this->request->post['payment_mbway_activateCallback'])) {
        $data['payment_mbway_activateCallback'] = $this->request->post['payment_mbway_activateCallback'];
    } else {
        $data['payment_mbway_activateCallback'] = $this->config->get('payment_mbway_activateCallback');
    }

    if (isset($this->request->post['payment_mbway_order_status_id'])) {
        $data['payment_mbway_order_status_id'] = $this->request->post['payment_mbway_order_status_id'];
    } else {
        $data['payment_mbway_order_status_id'] = $this->config->get('payment_mbway_order_status_id');
    }
    
    if (isset($this->request->post['payment_mbway_order_status_complete_id'])) {
        $data['payment_mbway_order_status_complete_id'] = $this->request->post['payment_mbway_order_status_complete_id'];
    } else {
        $data['payment_mbway_order_status_complete_id'] = $this->config->get('payment_mbway_order_status_complete_id');
    }

    $this->load->model('localisation/order_status');

    $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

    if (isset($this->request->post['payment_mbway_geo_zone_id'])) {
        $data['payment_mbway_geo_zone_id'] = $this->request->post['payment_mbway_geo_zone_id'];
    } else {
        $data['payment_mbway_geo_zone_id'] = $this->config->get('payment_mbway_geo_zone_id');
    }

    $this->load->model('localisation/geo_zone');

    $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

    if (isset($this->request->post['payment_mbway_sort_order'])) {
        $data['payment_mbway_sort_order'] = $this->request->post['payment_mbway_sort_order'];
    } else {
        $data['payment_mbway_sort_order'] = $this->config->get('payment_mbway_sort_order');
    }
        
    $data['actionRequestAccount'] = $this->url->link('extension/payment/mbway/requestNewAccount', 'user_token=' . $this->session->data['user_token'], true);
    
    $ifthenpayUserPaymentMethods = unserialize($this->config->get('payment_mbway_userPaymentMethods'));
    if (!is_null($ifthenpayUserPaymentMethods) && is_array($ifthenpayUserPaymentMethods) && !in_array($this->paymentMethod, $ifthenpayUserPaymentMethods)) {
      $data['ifthenpayPayments'] = true;   
    }
    
    $needUpgrade = $this->ifthenpayContainer->getIoc()->make(IfthenpayUpgrade::class)->checkModuleUpgrade();
    $data['updateIfthenpayModuleAvailable'] = $needUpgrade['upgrade'];
    $data['upgradeModuleBulletPoints'] = $needUpgrade['upgrade'] ? $needUpgrade['body'] : '';
    $data['moduleUpgradeUrlDownload'] = $needUpgrade['upgrade'] ? $needUpgrade['download'] : '';
    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $data['spinner'] = $this->load->view('extension/payment/spinner');

    if ($this->config->get('payment_mbway_userPaymentMethods') && $this->config->get('payment_mbway_userAccount')) {
      try {
          $paymentFormData = $this->ifthenpayContainer->getIoc()->make(IfthenpayConfigForms::class)
          ->setIfthenpayController($this)
          ->setConfigData($configData)
          ->setPaymentMethod($this->paymentMethod)
          ->buildForm();
          $paymentFormData['spinner'] = $this->load->view('extension/payment/spinner');
          $paymentFormData['ifthenpay_updateModule'] = $this->load->view('extension/payment/ifthenpay_update_module', $data);
          $this->model_extension_payment_mbway->log('', 'Payment Form Loaded with success');
          if (!$this->config->get('payment_mbway_cronToken')) {
            $cronToken = md5((string) rand());
            $configData['payment_mbway_cronToken'] = $cronToken;
            $this->model_setting_setting->editSetting('payment_mbway', $configData);
          } else {
            $cronToken = $configData['payment_mbway_cronToken'];
          }
          
          $data['cron'] = ($this->config->get('config_secure') ? rtrim(HTTP_CATALOG, '/') : rtrim(HTTPS_CATALOG, '/')) . 
          '/index.php?route=extension/payment/mbway/cancelMbwayOrderBackend&tk=' . $cronToken;
          $this->response->setOutput($this->load->view('extension/payment/mbway', array_merge($paymentFormData, $data)));
      } catch (\Throwable $th) {
          $this->model_extension_payment_mbway->log($th->getMessage(), 'Error Loading Payment Form');
          $data['error_warning'] = $th->getMessage();
          $this->response->setOutput($this->load->view('extension/payment/mbway', $data));
      }
    } else {
      $this->response->setOutput($this->load->view('extension/payment/mbway', $data));
    }
  }

  public function install()
  {
    $this->load->model('setting/event');

    $this->model_setting_event->addEvent('ifthenpayCheckoutSuccessPage', 'catalog/view/common/success/before', 'extension/payment/mbway/changeSuccessPage');
    $this->model_setting_event->addEvent('ifthenpayFooter', 'catalog/view/common/footer/before', 'extension/payment/mbway/changeFooterScripts');
    $this->model_setting_event->addEvent('ifthenpayCatalogHeader', 'catalog/view/common/header/before', 'extension/payment/mbway/changeHeaderStyles');
    $this->model_setting_event->addEvent('ifthenpayCheckMbwayOrder', 'admin/model/sale/order/getOrders/after', 'extension/payment/mbway/checkMbwayOrder');
    $this->model_setting_event->addEvent('ifthenpayCheckoutPaymentMethodSave', 'catalog/controller/checkout/payment_method/save/after', 'extension/payment/mbway/paymentMethodSave');
    $this->model_setting_event->addEvent('ifthenpayOrderEmailAdd', 'catalog/view/mail/order_add/before', 'extension/payment/mbway/changeMailOrderAdd');          
  }

  public function uninstall() {
    $this->load->model('setting/setting');
    $this->load->model('setting/event');
    $this->load->model('extension/payment/mbway');
    $this->ifthenpayContainer = new IfthenpayContainer();
    $this->model_extension_payment_mbway->uninstall($this->ifthenpayContainer, $this->paymentMethod);
    $this->model_setting_setting->deleteSetting('payment_mbway');
    $this->model_setting_event->deleteEventByCode('ifthenpayCheckoutSuccessPage');
    $this->model_setting_event->deleteEventByCode('ifthenpayFooter');
    $this->model_setting_event->deleteEventByCode('ifthenpayCatalogHeader');
    $this->model_setting_event->deleteEventByCode('ifthenpayCheckMbwayOrder');
    $this->model_setting_event->deleteEventByCode('ifthenpayCheckoutPaymentMethodSave');
    $this->model_setting_event->deleteEventByCode('ifthenpayOrderEmailAdd');
  }

  protected function validate() {
    if (!$this->user->hasPermission('modify', 'extension/payment/mbway')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

    if (!$this->config->get('payment_mbway_backofficeKey')) {
      $backofficeKey = $this->request->post['payment_mbway_backofficeKey'];
      if (!$backofficeKey) {
        $this->error['warning'] = $this->language->get('error_backofficeKey_required');
      } else {
        try {
            if (!$this->config->get('payment_mbway_userPaymentMethods') && !$this->config->get('payment_mbway_userAccount')) {
                $ifthenpayGateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);
                $ifthenpayGateway->authenticate($backofficeKey);
                $userPaymentMethods = $ifthenpayGateway->getPaymentMethods();
                $this->request->post['payment_mbway_userPaymentMethods'] = serialize($userPaymentMethods);
                $this->request->post['payment_mbway_userAccount'] = serialize(
                    $ifthenpayGateway->getAccount()
                );
                if (in_array($this->paymentMethod, $userPaymentMethods)) {
                    $this->model_extension_payment_mbway->install($this->ifthenpayContainer, $this->paymentMethod);
                }
            }
          $this->model_extension_payment_mbway->log('', 'Backoffice key saved with success');
        } catch (\Throwable $th) {
          $this->load->model('extension/payment/mbway');
          $this->model_extension_payment_mbway->uninstall($this->ifthenpayContainer, $this->paymentMethod);
          $this->model_setting_setting->deleteSetting('payment_mbway');
          unset($this->request->post['payment_mbway_backofficeKey']);
          $this->error['warning'] = $this->language->get('error_backofficeKey_error');
          $this->model_extension_payment_mbway->log($th->getMessage(), 'Error saving backoffice key');
          return !$this->error;
        }
      }
    } 
	return !$this->error;
}

  public function requestNewAccount()
  {
    $this->load->model('setting/setting');
    $this->configData =  $this->model_setting_setting->getSetting('payment_mbway');
    $paymentMethod = $this->request->get['paymentMethod'];   

    $updateUserToken = md5((string) rand());
    $this->model_setting_setting->editSetting('payment_mbway', array_merge($this->configData, [
        'payment_mbway_updateUserAccountToken' => $updateUserToken
        ])
    );

    $updateUserAccountUrl = ($this->config->get('config_secure') ? rtrim(HTTP_CATALOG, '/') : rtrim(HTTPS_CATALOG, '/')) . '/index.php?route=extension/payment/mbway/updateUserAccount&user_token=' . $updateUserToken ;

    $store_name = $this->config->get('config_name');

    $msg = "Associar conta " . $paymentMethod . " ao contrato \n\n";
    $msg .= "backofficeKey: " . $this->config->get('payment_mbway_backofficeKey') .  "\n\n";
    $msg .= "Email Cliente: " .  $this->config->get('config_email') . "\n\n";
    $msg .= "Atualizar Conta Cliente: " . $updateUserAccountUrl . "\n\n";
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

    $mail->setSubject('Associar conta ' . $this->paymentMethod . ' ao contrato');
    $mail->setText($msg);

    $mail->setTo("ricardocarvalho@ifthenpay.com");
    $mail->send();

    return true;
  }

  public function chooseNewEntidade()
  {
    try {
        $this->load->model('extension/payment/mbway');
		$this->ifthenpayContainer = new IfthenpayContainer();
		$this->ifthenpayContainer->getIoc()->make(IfthenpayConfigForms::class)
      ->setIfthenpayController($this)
		  ->setPaymentMethod($this->paymentMethod)
			->deleteConfigFormValues();
		$this->model_extension_payment_mbway->log('', 'Entity/SubEntity reseted with success');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode('Entity/SubEntity reseted with success'));
    } catch (\Throwable $th) {
		  $this->model_extension_payment_mbway->log($th->getMessage(), 'Error reseting Entity/SubEntity');
      $this->response->addHeader('Content-Type: application/json');
		  $this->response->setOutput(json_encode($th->getMessage()));
    }
  }

  public function resetUserAccounts()
  {
    try {
      $this->load->model('extension/payment/mbway');
		  $this->load->model('setting/setting');
      $configData =  $this->model_setting_setting->getSetting('payment_mbway');

      if (!$configData['payment_mbway_backofficeKey']) {
        $this->response->addHeader('Content-Type: application/json');
			  $this->response->setOutput(json_encode($this->language->get('error_backofficeKey_required')));
      }
      $this->ifthenpayContainer = new IfthenpayContainer();
      $gateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);
      $gateway->authenticate($configData['payment_mbway_backofficeKey']);
      $this->model_extension_payment_mbway->install($this->ifthenpayContainer, $this->paymentMethod);
      $configData['payment_mbway_userPaymentMethods'] = serialize($gateway->getPaymentMethods());
      $configData['payment_mbway_userAccount'] = serialize($gateway->getAccount());
      $this->model_setting_setting->editSetting('payment_mbway', $configData);
      $this->model_extension_payment_mbway->log('Ifthenpay account reseted with success');
      $this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($this->language->get('reset_account_success')));
    } catch (\Throwable $th) {
      $this->model_extension_payment_mbway->log('Error reseting ifthenpay account - ' . $th->getMessage());
      $this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($this->language->get('reset_account_error')));
    }
  }

  public function checkMbwayOrder(&$route, &$data, &$output)
  {
    if ($this->request->get['route'] === 'sale/order') {
      try {
        $this->ifthenpayContainer = new IfthenpayContainer();
        $this->ifthenpayContainer->getIoc()->make(IfthenpayPaymentStatus::class)->setPaymentMethod('mbway')->setIfthenpayController($this)->execute();
      } catch (\Throwable $th) {
        $this->load->model('extension/payment/mbway');
        $this->model_extension_payment_mbway->log($th->getMessage(), 'Error Checking Mbway payment Status');
        throw $th;
      }
    }
  }
}
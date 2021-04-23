<?php

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Config\IfthenpayUpgrade;
use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Payments\Data\MbwayCancelOrder;

require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class ControllerExtensionPaymentIfthenpay extends Controller {
  private $configData;
  private $ifthenpayContainer;
  private $error = [];

  public function index() 
  {
    $this->ifthenpayContainer = new IfthenpayContainer();
    $this->load->language('extension/payment/ifthenpay');

		$this->document->setTitle($this->language->get('heading_title'));
    $this->document->addStyle('view/stylesheet/ifthenpay/ifthenpayConfig.css');

		$this->load->model('extension/payment/ifthenpay');
		$this->load->model('setting/setting');
    $this->configData =  $this->model_setting_setting->getSetting('payment_ifthenpay');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && !empty($this->request->post) && $this->validate()) {
      if (!empty($this->configData)) {
        $this->request->post = array_merge($this->configData, $this->request->post);
      }
      $this->model_setting_setting->editSetting('payment_ifthenpay', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/payment/ifthenpay', 'user_token=' . $this->session->data['user_token'], true));
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

    $data['heading_title'] = $this->language->get('heading_title');
    $data['text_success'] = (isset($this->session->data['success']) ? $this->session->data['success'] : '');
    $data['title_smart_payments'] = $this->language->get('title_smart_payments');
    $data['title_need_help'] = $this->language->get('title_need_help');
    $data['acess_user_documentation'] = $this->language->get('acess_user_documentation');
    $data['create_account'] = $this->language->get('create_account');
    $data['create_account_now'] = $this->language->get('create_account_now');
    $data['numerous_advantages'] = $this->language->get('numerous_advantages');
    $data['free_for_users'] = $this->language->get('free_for_users');
    $data['payments_24_day'] = $this->language->get('payments_24_day');
    $data['secure_payments'] = $this->language->get('secure_payments');
    $data['protected_user_data'] = $this->language->get('protected_user_data');
    $data['multichannel_payments'] = $this->language->get('multichannel_payments');
    $data['directly_from_user'] = $this->language->get('directly_from_user');
    $data['fully_checkout'] = $this->language->get('fully_checkout');
    $data['free_app'] = $this->language->get('free_app');
    $data['more_automation'] = $this->language->get('more_automation');
    $data['entry_backofficeKey'] = $this->language->get('entry_backofficeKey');
    $data['switch_enable'] = $this->language->get('switch_enable');
    $data['switch_disable'] = $this->language->get('switch_disable');
    $data['add_new_accounts'] = $this->language->get('add_new_accounts');
    $data['reset_accounts'] = $this->language->get('reset_accounts');
    $data['sandbox_help'] = $this->language->get('sandbox_help');
    $data['sandbox_mode'] = $this->language->get('sandbox_mode');
    $data['manage_btn'] = $this->language->get('manage_btn');
    
  

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
        'href' => $this->url->link('extension/payment/ifthenpay', 'user_token=' . $this->session->data['user_token'], true)
    );

    $data['action'] = $this->url->link('extension/payment/ifthenpay', 'user_token=' . $this->session->data['user_token'], true);
    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
    $data['configure_manage_button_url'] = $this->url->link('extension/payment/ifthenpay/configureManageButton', 'user_token=' . $this->session->data['user_token'], true);

    if (isset($this->request->post['payment_ifthenpay_backofficeKey'])) {
      $data['payment_ifthenpay_backofficeKey'] = $this->request->post['payment_ifthenpay_backofficeKey'];
    } else if (isset($this->configData['payment_ifthenpay_backofficeKey'])) {
      $data['payment_ifthenpay_backofficeKey'] = $this->configData['payment_ifthenpay_backofficeKey'];
      $data['payment_ifthenpay_userPaymentMethods'] = unserialize($this->configData['payment_ifthenpay_userPaymentMethods']);
    } else {
      $data['payment_ifthenpay_backofficeKey'] = '';
    }

    if (isset($this->request->post['payment_ifthenpay_sandboxMode'])) {
      $data['payment_ifthenpay_sandboxMode'] = $this->request->post['payment_ifthenpay_sandboxMode'];
    } else if (isset($this->configData['payment_ifthenpay_sandboxMode'])) {
      $data['payment_ifthenpay_sandboxMode'] = $this->configData['payment_ifthenpay_sandboxMode'];
    } else {
      $data['payment_ifthenpay_sandboxMode'] = '0';
    }    
    
    if (isset($data['payment_ifthenpay_userPaymentMethods'])) {
      foreach ($data['payment_ifthenpay_userPaymentMethods'] as $paymentMethod) {
        if (isset($this->request->post['payment_ifthenpay_account_' . $paymentMethod])) {
          $data['payment_ifthenpay_account_' . $paymentMethod] = $this->request->post['payment_ifthenpay_account_' . $paymentMethod];
        } else if (isset($this->configData['payment_ifthenpay_account_' . $paymentMethod])) {
          $data['payment_ifthenpay_account_' . $paymentMethod] = $this->configData['payment_ifthenpay_account_' . $paymentMethod];
        } else {
          $data['payment_ifthenpay_account_' . $paymentMethod] = '0';
        }
      }
    }
    
    if (isset($this->configData['payment_ifthenpay_isIfthenpayPaymentMethodsSaved'])) {
      $data['payment_ifthenpay_isIfthenpayPaymentMethodsSaved'] = true;
    } else {
      $data['payment_ifthenpay_isIfthenpayPaymentMethodsSaved'] = false;
    }

   $ifthenpayPayments = $this->ifthenpayContainer->getIoc()->make(Gateway::class)->getPaymentMethodsType();
    $data['ifthenpayPayments'] = [];
    foreach ($ifthenpayPayments as $paymentMethodType) {
      if (!in_array($paymentMethodType, $data['payment_ifthenpay_userPaymentMethods'])
      ) {
        $data['ifthenpayPayments'][] = $paymentMethodType;
              
      }
    }
    $data['actionRequestAccount'] = $this->url->link('extension/payment/ifthenpay/requestNewAccount', 'user_token=' . $this->session->data['user_token'], true);
    
    $needUpgrade = $this->ifthenpayContainer->getIoc()->make(IfthenpayUpgrade::class)->checkModuleUpgrade();
    $data['updateIfthenpayModuleAvailable'] = $needUpgrade['upgrade'];
    $data['upgradeModuleBulletPoints'] = $needUpgrade['upgrade'] ? $needUpgrade['body'] : '';
    $data['moduleUpgradeUrlDownload'] = $needUpgrade['upgrade'] ? $needUpgrade['download'] : '';
    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $data['spinner'] = $this->load->view('extension/payment/spinner');

    $this->response->setOutput($this->load->view('extension/payment/ifthenpay', $data));
      
  }

  public function install()
  {
    $this->load->model('setting/setting');
    $this->model_setting_setting->editSetting('payment_ifthenpay', ['payment_ifthenpay_status' => 1]);

    $this->load->model('setting/event');

    $this->model_setting_event->addEvent('ifthenpayCheckoutPaymentMethods_view', 'catalog/view/checkout/payment_method/before', 'extension/payment/ifthenpay/changePaymentMethodsInView');
    $this->model_setting_event->addEvent('ifthenpayCancelMbwayOrder', 'admin/model/sale/order/getOrders/after', 'extension/payment/ifthenpay/cancelMbwayOrder');
    $this->model_setting_event->addEvent('ifthenpayCheckoutPaymentMethodSave', 'catalog/controller/checkout/payment_method/save/after', 'extension/payment/ifthenpay/paymentMethodSave');
    $this->model_setting_event->addEvent('ifthenpayCheckoutSuccessPage', 'catalog/view/common/success/before', 'extension/payment/ifthenpay/changeSuccessPage');
    $this->model_setting_event->addEvent('ifthenpayFooter', 'catalog/view/common/footer/before', 'extension/payment/ifthenpay/changeFooterScripts');
    $this->model_setting_event->addEvent('ifthenpayOrderEmailAdd', 'catalog/view/mail/order_add/before', 'extension/payment/ifthenpay/changeMailOrderAdd');
    $this->model_setting_event->addEvent('ifthenpayCatalogHeader', 'catalog/view/common/header/before', 'extension/payment/ifthenpay/changeHeaderStyles');
     
  }

  public function uninstall() {
    $this->load->model('setting/setting');
    $this->load->model('setting/event');
    $this->load->model('extension/payment/ifthenpay');
    $this->ifthenpayContainer = new IfthenpayContainer();
    $userPaymentMethods = unserialize($this->config->get('payment_ifthenpay_userPaymentMethods'));
    $this->model_extension_payment_ifthenpay->uninstall($this->ifthenpayContainer, is_array($userPaymentMethods) ? $userPaymentMethods : []);
    $this->model_setting_setting->deleteSetting('payment_ifthenpay');
    $this->model_setting_event->deleteEventByCode('ifthenpayCheckoutPaymentMethods_view');
    $this->model_setting_event->deleteEventByCode('ifthenpayCancelMbwayOrder');
    $this->model_setting_event->deleteEventByCode('ifthenpayCheckoutPaymentMethodSave');
    $this->model_setting_event->deleteEventByCode('ifthenpayCheckoutSuccessPage');
    $this->model_setting_event->deleteEventByCode('ifthenpayFooter');
    $this->model_setting_event->deleteEventByCode('ifthenpayOrderEmailAdd');
    $this->model_setting_event->deleteEventByCode('ifthenpayCatalogHeader');
  }

  public function configureManageButton() {
		$this->load->model('extension/payment/ifthenpay');
		
		$this->model_extension_payment_ifthenpay->configureManageButton();
		
    $url = $this->url->link('extension/module/ifthenpay_manage_payment_method', 'user_token=' . $this->session->data['user_token'], true) . '&paymentMethod=' . $this->request->get['paymentMethod'];
		$this->response->redirect($url);
	}

  protected function validate() {
    if (!$this->user->hasPermission('modify', 'extension/payment/ifthenpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

    if (!isset($this->configData['payment_ifthenpay_backofficeKey'])) {
      $backofficeKey = $this->request->post['payment_ifthenpay_backofficeKey'];
      if (!$backofficeKey) {
        $this->error['warning'] = $this->language->get('error_backofficeKey_required');
      } else {
        try {
          $ifthenpayGateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);
          $ifthenpayGateway->authenticate($backofficeKey);
          $userPaymentMethods = $ifthenpayGateway->getPaymentMethods();
          $this->request->post['payment_ifthenpay_userPaymentMethods'] = serialize($userPaymentMethods);
          $this->request->post['payment_ifthenpay_userAccount'] = serialize(
            $ifthenpayGateway->getAccount()
          );
          $this->model_extension_payment_ifthenpay->install($this->ifthenpayContainer, $userPaymentMethods);
          $this->model_extension_payment_ifthenpay->log('', 'Backoffice key saved with success');
        } catch (\Throwable $th) {
          $this->load->model('extension/payment/ifthenpay');
          $this->model_extension_payment_ifthenpay->uninstall($this->ifthenpayContainer, is_array($userPaymentMethods) ? $userPaymentMethods : []);
          $this->model_setting_setting->deleteSetting('payment_ifthenpay');
          unset($this->request->post['payment_ifthenpay_backofficeKey']);
          $this->error['warning'] = $this->language->get('error_backofficeKey_error');
          $this->model_extension_payment_ifthenpay->log($th->getMessage(), 'Error saving backoffice key');
          return !$this->error;
        }
      }
    } else {
      $this->request->post['payment_ifthenpay_isIfthenpayPaymentMethodsSaved'] = true;
    }
		return !$this->error;
	}

  public function requestNewAccount()
  {
    if (isset($this->request->get['paymentMethod'])) {
        $this->load->model('setting/setting');
        $this->configData =  $this->model_setting_setting->getSetting('payment_ifthenpay');
        $paymentMethod = $this->request->get['paymentMethod'];   

        $updateUserToken = md5((string) rand());
        $this->model_setting_setting->editSetting('payment_ifthenpay', array_merge($this->configData, [
            'payment_ifthenpay_updateUserAccountToken' => $updateUserToken
          ])
        );

        $updateUserAccountUrl = ($this->config->get('config_secure') ? rtrim(HTTP_CATALOG, '/') : rtrim(HTTPS_CATALOG, '/')) . '/index.php?route=extension/payment/ifthenpay/updateUserAccount&user_token=' . $updateUserToken ;

        $store_name = $this->config->get('config_name');

        $msg = "Associar conta " . $paymentMethod . " ao contrato \n\n";
        $msg .= "backofficeKey: " . $this->config->get('payment_ifthenpay_backofficeKey') .  "\n\n";
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
    
  }

  public function cancelMbwayOrder(&$route, &$data, &$output)
  {
    $this->ifthenpayContainer = new IfthenpayContainer();
    $this->ifthenpayContainer->getIoc()->make(MbwayCancelOrder::class)->setIfthenpayController($this)->setRegistry($this->registry)->cancelOrder();
  }
}

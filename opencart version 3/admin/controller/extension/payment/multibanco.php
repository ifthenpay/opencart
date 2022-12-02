<?php

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Contracts\Utility\MailInterface;

class ControllerExtensionPaymentMultibanco extends IfthenpayController
{
  protected $paymentMethod = Gateway::MULTIBANCO;

  public function getSubEntidade()
  {
    try {
      $ifthenpayGateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);
      $ifthenpayGateway->setAccount((array) unserialize($this->config->get('payment_multibanco_userAccount')));
      $subEntidades = json_encode($ifthenpayGateway->getSubEntidadeInEntidade($this->request->post['entidade']));
      $this->model_extension_payment_multibanco->log('', 'SubEntidades load with sucess');
      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput($subEntidades);
    } catch (\Throwable $th) {
      $this->model_extension_payment_multibanco->log([
        'errorMessage' => $th->getMessage()
      ], 'Error loading SubEntidades');
      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput($th->getMessage());
    }
  }

  public function requestDynamicMultibancoAccount()
  {
    try {

      $mailService = $this->ifthenpayContainer->getIoc()->make(MailInterface::class)
        ->setIfthenpayController($this)
        ->setPaymentMethod($this->paymentMethod)
        ->setUserToken($this->createUpdateAccountUserToken())
        ->setSubject('[Opencart] Associar conta Multibanco dinamica ao contrato');
        // add the html template with data to the mail service
      $data = [
        'backofficeKey' => $this->config->get('payment_' . $this->paymentMethod . '_backofficeKey'),
        'customerEmail' => $this->config->get('config_email'),
        'paymentMethod' => 'Multibanco dinamica',
        'storeName' => $this->config->get('config_name'),
        'ecommercePlatform' => 'Opencart ' . VERSION,
        'updateUserAccountUrl' =>  $mailService->getUpdateUserAccountUrl(),
      ];

      $mailService->setHtmlMessageBody($this->load->view('extension/payment/ifthenpayAccountRequest', $data))
      ->setMessageBody("Associar conta Multibanco dinâmica ao contrato \n\n")
      ->sendEmail();


      $this->model_extension_payment_multibanco->log('Email requesting new multibanco dynamic account sent with success');
      $this->response->addHeader('Content-Type: application/json');
      $this->response->setOutput(json_encode([
        'success' => $this->language->get('request_new_account_success')
      ]));
    } catch (\Throwable $th) {
      $this->model_extension_payment_multibanco->log([
        'errorMessage' => $th->getMessage()
      ], 'Error sending email requesting new multibanco dynamic account');
      $this->response->addHeader('Content-Type: application/json');
      $this->response->addHeader('HTTP/1.0 400 Bad Request');
      $this->response->setOutput(json_encode([
        'error' => $this->language->get('request_new_account_error')
      ]));
    }
  }
}

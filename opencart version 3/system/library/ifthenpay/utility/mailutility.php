<?php

declare(strict_types=1);

namespace Ifthenpay\Utility;

use Ifthenpay\Contracts\Utility\MailInterface;
use Mail;

class MailUtility implements MailInterface
{
   private $message;
   private $messageHtml;
   private $subject;
   private $messageBody;
   private $paymentMethod;
   private $userToken;
   private $ifthenpayController;
   private $storeName;
   private $mail;



   public function __construct(\Mail $mail)
   {
      $this->mail = $mail;
   }

   public function getUpdateUserAccountUrl(): string
   {
      return ($this->ifthenpayController->config->get('config_secure') ? rtrim(HTTP_CATALOG, '/') : rtrim(HTTPS_CATALOG, '/')) .
         '/index.php?route=extension/payment/' . $this->paymentMethod . '/updateUserAccount&user_token=' . $this->userToken;
   }

   private function setDefaultMessageBody(): string
   {
      $this->storeName = $this->ifthenpayController->config->get('config_name');
      $this->messageBody .= "backofficeKey: " . $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_backofficeKey') .  "\n\n";
      $this->messageBody .= "Email Cliente: " .  $this->ifthenpayController->config->get('config_email') . "\n\n";
      $this->messageBody .= "Atualizar Conta Cliente: " . $this->getUpdateUserAccountUrl() . "\n\n";
      $this->messageBody .= "Pedido enviado automaticamente pelo sistema OpenCart da loja [$this->storeName]";
      return $this->messageBody;
   }

   public function sendEmail(): void
   {

      $this->mail = new Mail("Smtp"); // todo: ugly fix, this should be injected in the constructor

      $this->mail->protocol = $this->ifthenpayController->config->get('config_mail_engine');
      $this->mail->parameter = $this->ifthenpayController->config->get('config_mail_parameter');
      $this->mail->smtp_hostname = $this->ifthenpayController->config->get('config_mail_smtp_hostname');
      $this->mail->smtp_username = $this->ifthenpayController->config->get('config_mail_smtp_username');
      $this->mail->smtp_password = html_entity_decode($this->ifthenpayController->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
      $this->mail->smtp_port = $this->ifthenpayController->config->get('config_mail_smtp_port');
      $this->mail->smtp_timeout = $this->ifthenpayController->config->get('config_mail_smtp_timeout');
      $this->mail->setFrom($this->ifthenpayController->config->get('config_email'));
      $this->mail->setSender(html_entity_decode($this->storeName, ENT_QUOTES, 'UTF-8'));

      $this->mail->setSubject($this->subject);
      $this->mail->setText($this->message);

      if (isset($this->messageHtml) && !empty($this->messageHtml)) {
         $this->mail->setHtml($this->messageHtml);
      }

      $this->mail->setTo("suporte@ifthenpay.com");
      $this->mail->send();
   }

   public function setSubject(string $subject): MailInterface
   {
      $this->subject = $subject;

      return $this;
   }

   public function setMessageBody(string $messageBody): MailInterface
   {
      $this->message = $messageBody . $this->setDefaultMessageBody();
      return $this;
   }

   public function setHtmlMessageBody(string $messageBody): MailInterface
   {
      $this->messageHtml = $messageBody;
      return $this;
   }

   public function setPaymentMethod(string $paymentMethod): MailInterface
   {
      $this->paymentMethod = $paymentMethod;
      return $this;
   }

   public function setUserToken(string $userToken): MailInterface
   {
      $this->userToken = $userToken;

      return $this;
   }

   public function setIfthenpayController($ifthenpayController): MailInterface
   {
      $this->ifthenpayController = $ifthenpayController;

      return $this;
   }
}

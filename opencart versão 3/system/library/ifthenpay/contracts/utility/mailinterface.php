<?php

declare(strict_types=1);

namespace Ifthenpay\Contracts\Utility;


interface MailInterface
{
   public function sendEmail(): void;
   public function setSubject(string $subject): MailInterface;
   public function setMessageBody(string $messageBody): MailInterface;
   public function setPaymentMethod(string $paymentMethod): MailInterface; 
   public function setUserToken(string $userToken): MailInterface;
   public function setIfthenpayController($ifthenpayController): MailInterface;
}

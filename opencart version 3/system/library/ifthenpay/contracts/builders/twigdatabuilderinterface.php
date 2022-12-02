<?php

declare(strict_types=1);

namespace Ifthenpay\Contracts\Builders;

use Ifthenpay\Contracts\Builders\DataBuilderInterface;

interface TwigDataBuilderInterface extends DataBuilderInterface
{
    public function setOrderId(string $value): TwigDataBuilderInterface;
    public function setStatus(string $value): TwigDataBuilderInterface;
    public function setUpdateControllerUrl(string $value): TwigDataBuilderInterface;
    public function setResendControllerUrl(string $value): TwigDataBuilderInterface;
    public function setRememberControllerUrl(string $value): TwigDataBuilderInterface;
    public function setMbwayCountdownShow(bool $value): TwigDataBuilderInterface;
    public function setPaymentReturnMbwayConfirmPayment(string $value): TwigDataBuilderInterface;
    public function setPaymentReturnMbwayConfirmPaymentMinutes(string $value): TwigDataBuilderInterface;
    public function setPaymentReturnMbwayConfirmPaymentNotificationExpired(string $value): TwigDataBuilderInterface;
    public function setPaymentReturnMbwayConfirmPaymentNotificationTime(string $value): TwigDataBuilderInterface;
    public function setPaymentReturnMbwayConfirmPaymentNotificationResend(string $value): TwigDataBuilderInterface;
    public function setPaymentReturnMbwayPaymentPaid(string $value): TwigDataBuilderInterface;
    public function setPaymentReturnMbwayPaymentRefused(string $value): TwigDataBuilderInterface;
    public function setPaymentReturnMbwayPaymentError(string $value): TwigDataBuilderInterface;
    public function setPaymentReturnMbwayOrderConfirmed(string $value): TwigDataBuilderInterface;
    public function setIfthenpayPaymentPanelEntidade(string $value): TwigDataBuilderInterface;
    public function setIfthenpayPaymentPanelReferencia(string $value): TwigDataBuilderInterface;
    public function setIfthenpayPaymentPanelTotalToPay(string $value): TwigDataBuilderInterface;
    public function setIfthenpayPaymentPanelProcessed(string $value): TwigDataBuilderInterface;
    public function setIfthenpayPaymentPanelIdPedido(string $value): TwigDataBuilderInterface;
    public function setIfthenpayPaymentPanelPhone(string $value): TwigDataBuilderInterface;
    public function setIfthenpayPaymentPanelOrder(string $value): TwigDataBuilderInterface;
    public function setResendMbwayNotificationControllerUrl(string $value): TwigDataBuilderInterface;
    public function setIfthenpayPaymentPanelMbwayNotificationNotReceive(string $value): TwigDataBuilderInterface;
    public function setIfthenpayPaymentPanelMbwayResendNotification(string $value): TwigDataBuilderInterface;
    public function setIfthenpayPaymentPanelValidade(string $value): TwigDataBuilderInterface;
    public function setOrderView(bool $value): TwigDataBuilderInterface;
}

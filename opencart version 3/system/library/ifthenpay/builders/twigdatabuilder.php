<?php

declare(strict_types=1);

namespace Ifthenpay\Builders;

use Ifthenpay\Builders\DataBuilder;
use Ifthenpay\Contracts\Builders\TwigDataBuilderInterface;
use Template\Twig;

class TwigDataBuilder extends DataBuilder implements TwigDataBuilderInterface
{

    public function setOrderId(string $value): TwigDataBuilderInterface
    {
        $this->data->orderId = $value;
        return $this;
    }

    public function setStatus(string $value): TwigDataBuilderInterface
    {
        $this->data->status = $value;
        return $this;
    }

    public function setUpdateControllerUrl(string $value): TwigDataBuilderInterface
    {
        $this->data->updateControllerUrl = $value;
        return $this;
    }

    public function setResendControllerUrl(string $value): TwigDataBuilderInterface
    {
        $this->data->resendControllerUrl = $value;
        return $this;
    }

    public function setRememberControllerUrl(string $value): TwigDataBuilderInterface
    {
        $this->data->rememberControllerUrl = $value;
        return $this;
    }

    public function setPaymentReturnTitle(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnTitle = $value;
        return $this;
    }
    public function setSpinner(string $value): TwigDataBuilderInterface
    {
        $this->data->spinner = $value;
        return $this;
    }
    
    public function setPaymentReturnPaymentPanel(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnPaymentPanel = $value;
        return $this;
    } 

    public function setPaymentReturnErrorTitle(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnErrorTitle = $value;
        return $this;
    } 

    public function setPaymentReturnErrorText(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnErrorText = $value;
        return $this;
    }

    public function setIfthenpayPaymentPanelTitle(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelTitle = $value;
        return $this;
    }

    public function setIfthenpayPaymentPanelValidade(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelValidade = $value;
        return $this;
    }

    public function setMbwayCountdownShow(bool $value): TwigDataBuilderInterface
    {
        $this->data->mbwayCountdownShow = $value;
        return $this;
    }
    public function setPaymentReturnMbwayConfirmPayment(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnMbwayConfirmPayment = $value;
        return $this;
    }

    public function setPaymentReturnMbwayConfirmPaymentMinutes(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnMbwayConfirmPaymentMinutes = $value;
        return $this;
    }

    public function setPaymentReturnMbwayConfirmPaymentNotificationExpired(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnMbwayConfirmPaymentNotificationExpired = $value;
        return $this;
    }

    public function setPaymentReturnMbwayConfirmPaymentNotificationTime(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnMbwayConfirmPaymentNotificationTime = $value;
        return $this;
    }

    public function setPaymentReturnMbwayConfirmPaymentNotificationResend(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnMbwayConfirmPaymentNotificationResend = $value;
        return $this;
    }

    public function setPaymentReturnMbwayPaymentPaid(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnMbwayPaymentPaid = $value;
        return $this;
    }

    public function setPaymentReturnMbwayPaymentRefused(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnMbwayPaymentRefused = $value;
        return $this;
    }

    public function setPaymentReturnMbwayPaymentError(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnMbwayPaymentError = $value;
        return $this;
    }

    public function setPaymentReturnMbwayOrderConfirmed(string $value): TwigDataBuilderInterface
    {
        $this->data->paymentReturnMbwayOrderConfirmed = $value;
        return $this;
    }

    public function setIfthenpayPaymentPanelEntidade(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelEntidade = $value;
        return $this;
    }

    public function setIfthenpayPaymentPanelReferencia(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelReferencia = $value;
        return $this;
    }

   
    public function setIfthenpayPaymentPanelTotalToPay(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelTotalToPay = $value;
        return $this;
    }

    public function setIfthenpayPaymentPanelProcessed(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelProcessed = $value;
        return $this;
    }

    public function setIfthenpayPaymentPanelIdPedido(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelIdPedido = $value;
        return $this;
    }

    public function setIfthenpayPaymentPanelPhone(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelPhone = $value;
        return $this;
    }

    public function setIfthenpayPaymentPanelOrder(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelOrder = $value;
        return $this;
    }

    public function setResendMbwayNotificationControllerUrl(string $value): TwigDataBuilderInterface
    {
        $this->data->resendMbwayNotificationControllerUrl = $value;
        return $this;
    }

    public function setIfthenpayPaymentPanelMbwayNotificationNotReceive(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelMbwayNotificationNotReceive = $value;
        return $this;
    } 
    
    public function setIfthenpayPaymentPanelMbwayResendNotification(string $value): TwigDataBuilderInterface
    {
        $this->data->ifthenpayPaymentPanelMbwayResendNotification = $value;
        return $this;
    }
    public function setOrderView(bool $value): TwigDataBuilderInterface
    {
        $this->data->orderView = $value;
        return $this;
    }
}

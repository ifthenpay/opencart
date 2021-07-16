<?php

namespace Ifthenpay\Config;

use GuzzleHttp\Client;
use Ifthenpay\Utility\Token;
use Ifthenpay\Utility\Status;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Request\WebService;
use Ifthenpay\Utility\TokenExtra;
use Ifthenpay\Config\IfthenpaySql;
use Ifthenpay\Builders\DataBuilder;
use Illuminate\Container\Container;
use Ifthenpay\Callback\CallbackOnline;
use Ifthenpay\Config\IfthenpayUpgrade;
use Ifthenpay\Builders\TwigDataBuilder;
use Ifthenpay\Callback\CallbackOffline;
use Ifthenpay\Callback\CallbackValidate;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Payments\MbWayPaymentStatus;
use Ifthenpay\Payments\PayshopPaymentStatus;
use Ifthenpay\Factory\Payment\PaymentFactory;
use Ifthenpay\Payments\Data\MbwayCancelOrder;
use Ifthenpay\Payments\MultibancoPaymentStatus;
use Ifthenpay\Factory\Payment\OrderDetailFactory;
use Ifthenpay\Strategy\Callback\CallbackStrategy;
use Ifthenpay\Strategy\Form\IfthenpayConfigForms;
use Ifthenpay\Factory\Callback\CallbackDataFactory;
use Ifthenpay\Factory\Payment\PaymentReturnFactory;
use Ifthenpay\Factory\Payment\PaymentStatusFactory;
use Ifthenpay\Strategy\Payments\IfthenpayOrderDetail;
use Ifthenpay\Strategy\Payments\IfthenpayPaymentReturn;
use Ifthenpay\Strategy\Payments\IfthenpayPaymentStatus;
use Ifthenpay\Factory\Config\IfthenpayConfigFormFactory;



class IfthenpayContainer 
{
    private $ioc;

	public function __construct()
	{
        $this->ioc = new Container();
        $this->bindDependencies();
    }

    private function bindDependencies(): void
    {
        $this->ioc->bind(Client::class, function () {
                return new Client();
            }
        );
        $this->ioc->bind(WebService::class, function () {
                return new WebService($this->ioc->make(Client::class));
            }
        );
        $this->ioc->bind(PaymentFactory::class, function () {
                return new PaymentFactory(
                    $this->ioc, 
                    $this->ioc->make(DataBuilder::class),
                    $this->ioc->make(Webservice::class)
                );
            }
        );
        $this->ioc->bind(Gateway::class, function () {
                return new Gateway($this->ioc->make(WebService::class), $this->ioc->make(PaymentFactory::class));
            }
        );
        $this->ioc->bind(GatewayDataBuilder::class, function () {
                return new GatewayDataBuilder();
            }
        );
        $this->ioc->bind(IfthenpaySql::class, function () {
            return new IfthenpaySql();
        });
        $this->ioc->bind(IfthenpayConfigFormFactory::class, function () {
            return new IfthenpayConfigFormFactory(
                $this->ioc, 
                $this->ioc->make(GatewayDataBuilder::class), 
                $this->ioc->make(Gateway::class)
            );
        });
        $this->ioc->bind(IfthenpayConfigForms::class, function() {
            return new IfthenpayConfigForms($this->ioc->make(IfthenpayConfigFormFactory::class));
        });
        
        $this->ioc->bind(TwigDataBuilder::class, function() {
            return new TwigDataBuilder();
        });
        $this->ioc->bind(DataBuilder::class, function() {
            return new DataBuilder();
        });
        $this->ioc->bind(Status::class, function() {
            return new Status();
        });
        $this->ioc->bind(Token::class, function() {
            return new Token();
        });
        $this->ioc->bind(TokenExtra::class, function() {
            return new TokenExtra();
        });
        $this->ioc->bind(PaymentReturnFactory::class, function () {
                return new PaymentReturnFactory(
                    $this->ioc, 
                    $this->ioc->make(GatewayDataBuilder::class),
                    $this->ioc->make(Gateway::class),
                    $this->ioc->make(Token::class),
                    $this->ioc->make(Status::class)
                );
            }
        );

        $this->ioc->bind(StrategyFactory::class, PaymentReturnFactory::class);
        $this->ioc->bind(IfthenpayPaymentReturn::class, function () {
                return new IfthenpayPaymentReturn(
                    $this->ioc->make(DataBuilder::class),
                    $this->ioc->make(TwigDataBuilder::class), 
                    $this->ioc->make(PaymentReturnFactory::class) 
                );
            }
        );
        $this->ioc->bind(StrategyFactory::class, OrderDetailFactory::class);
        $this->ioc->bind(IfthenpayOrderDetail::class, function () {
                return new IfthenpayOrderDetail(
                    $this->ioc->make(DataBuilder::class),
                    $this->ioc->make(TwigDataBuilder::class), 
                    $this->ioc->make(OrderDetailFactory::class)
                );
            }
        );
        $this->ioc->bind(CallbackDataFactory::class, function() {
            return new CallbackDataFactory($this->ioc);
        });
        
        $this->ioc->bind(CallbackValidate::class, function() {
            return new CallbackValidate();
        });
    
        $this->ioc->bind(CallbackOffline::class, function() {
                return new CallbackOffline(
                    $this->ioc->make(CallbackDataFactory::class), 
                    $this->ioc->make(CallbackValidate::class)
                );
            }
        );
        $this->ioc->bind(CallbackOnline::class, function() {
                return new CallbackOnline(
                    $this->ioc->make(CallbackDataFactory::class), 
                    $this->ioc->make(CallbackValidate::class), 
                    $this->ioc->make(Status::class),
                    $this->ioc->make(Token::class),
                    $this->ioc->make(TokenExtra::class)
                );
            }
        );
        $this->ioc->bind(CallbackStrategy::class, function() {
                return new CallbackStrategy(
                    $this->ioc->make(CallbackOffline::class),
                    $this->ioc->make(CallbackOnline::class)
                );
            }
        );
        $this->ioc->bind(MbwayCancelOrder::class, function () {
            return new MbwayCancelOrder($this->ioc->make(GatewayDataBuilder::class), $this->ioc->make(MbWayPaymentStatus::class));
        });
        $this->ioc->bind(IfthenpayUpgrade::class, function () {
            return new IfthenpayUpgrade($this->ioc->make(Webservice::class));
        });
        $this->ioc->bind(MbWayPaymentStatus::class, function() {
            return new MbWayPaymentStatus($this->ioc->make(Webservice::class));
        });
        $this->ioc->bind(MultibancoPaymentStatus::class, function() {
            return new MultibancoPaymentStatus($this->ioc->make(Webservice::class));
        });
        $this->ioc->bind(PayshopPaymentStatus::class, function() {
            return new PayshopPaymentStatus($this->ioc->make(Webservice::class));
        });
        $this->ioc->bind(PaymentStatusFactory::class, function () {
                return new PaymentStatusFactory(
                    $this->ioc, 
                    $this->ioc->make(GatewayDataBuilder::class),
                    $this->ioc->make(Webservice::class)
                );
            }
        );
        $this->ioc->bind(IfthepayPaymentStatus::class, function () {
                return new IfthenpayPaymentStatus(
                    $this->ioc->make(PaymentStatusFactory::class)
                );
            }
        );
    }
    /**
     * Get the value of ioc
     */ 
    public function getIoc()
    {
        return $this->ioc;
    }
}

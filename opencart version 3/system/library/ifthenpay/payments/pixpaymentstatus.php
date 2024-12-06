<?php

declare(strict_types=1);

namespace Ifthenpay\Payments;

use Ifthenpay\Request\WebService;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentStatusInterface;
use Ifthenpay\Payments\Gateway;

class PixPaymentStatus implements PaymentStatusInterface
{
	private $data;
	private $pixPedido;
	private $webService;

	public function __construct(WebService $webService)
	{
		$this->webService = $webService;
	}

	private function checkEstado(): bool
	{
		if (isset($this->pixPedido['CodigoErro']) && $this->pixPedido['CodigoErro'] === '0') {
			return true;
		}
		return false;
	}

	// TODO: check if this is needed

	private function getPixEstado(): void
	{
		$this->pixPedido = $this->webService->getRequest(
			'https://www.ifthenpay.com/IfmbWS/WsIfmb.asmx/GetPaymentsJson',
			[
				'Chavebackoffice' => $this->data->getData()->backofficeKey,
				'Entidade' => strtoupper(Gateway::PIX),
				'Subentidade' => $this->data->getData()->pixKey,
				'dtHrInicio' => '',
				'dtHrFim' => '',
				'Referencia' => $this->data->getData()->referencia,
				'Valor' => $this->data->getData()->totalToPay,
				'Sandbox' => 0
			]
		)->getXmlConvertedResponseToArray();
	}

	public function getPaymentStatus(): bool
	{
		$this->getPixEstado();
		return $this->checkEstado();
	}

	/**
	 * Set the value of data
	 *
	 * @return  self
	 */
	public function setData(GatewayDataBuilder $data): PaymentStatusInterface
	{
		$this->data = $data;

		return $this;
	}
}

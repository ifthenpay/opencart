<?php
/**
* Ifthenpay_Payment module dependency
*
* @category    Gateway Payment
* @package     Ifthenpay_Payment
* @author      Ifthenpay
* @copyright   Ifthenpay (http://www.ifthenpay.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

declare(strict_types=1);

namespace Ifthenpay\Payments;

use Ifthenpay\Request\WebService;
use Ifthenpay\Builders\GatewayDataBuilder;
use Ifthenpay\Contracts\Payments\PaymentStatusInterface;

class PayshopPaymentStatus implements PaymentStatusInterface
{
    private $data;
    private $payshopPedido;
    private $webservice;

    public function __construct(WebService $webservice)
    {
        $this->webservice = $webservice;
    }

    private function checkEstado(): bool
    {
        if (isset($this->payshopPedido['CodigoErro ']) && $this->payshopPedido['CodigoErro '] === '0') {
            return true;
        }
        return false;
    }

    private function getPayshopEstado(): void
    {
        $this->payshopPedido = $this->webservice->postRequest(
            'https://www.ifthenpay.com/IfmbWS/WsIfmb.asmx/GetPaymentsJson',
                [
                    'Chavebackoffice' => $this->data->getData()->backofficeKey,
                    'Entidade' => 'PAYSHOP',
                    'Subentidade' => $this->data->getData()->payshopKey,
                    'dtHrInicio' => '',
                    'dtHrFim' => '',
                    'Referencia' => $this->data->getData()->referencia,
                    'Valor' => '',
                    'Sandbox' => 0
                ]
        )->getXmlConvertedResponseToArray();
    }

    public function getPaymentStatus(): bool
    {
        $this->getPayshopEstado();
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
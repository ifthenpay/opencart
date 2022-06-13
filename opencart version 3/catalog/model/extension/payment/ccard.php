<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentCcard extends IfthenpayModel
{
    protected $paymentMethod = Gateway::CCARD;
    
    public function getMethod($address, $total) 
    {
        if(!$this->config->get('payment_ccard_ccardKey')) {
            $this->paymentStatus = false;
        } else {
            $this->paymentStatus = true;
        }

        return parent::getMethod($address, $total);
    }

    public function savePayment(\stdClass $paymentDefaultData, \stdClass $dataBuilder): void
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_ccard SET requestId = '" . $dataBuilder->idPedido . "', order_id = '" . 
            $paymentDefaultData->order['order_id'] . "', status = 'pending'"
        );
    }

    public function getCCardByRequestId(string $idPedido): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_ccard WHERE requestId = '" . $this->db->escape($idPedido) . "'");
    }
}

?>
<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentPayshop extends IfthenpayModel
{
    protected $paymentMethod = Gateway::PAYSHOP;

    public function getMethod($address, $total) 
    {
        if (!$this->config->get('payment_payshop_payshopKey')) {
            $this->paymentStatus = false;
        }  else if ($this->config->get('config_currency') !== 'EUR') {
            $this->paymentStatus = false;
        } else {
            $this->paymentStatus = true;
        }

        return parent::getMethod($address, $total);
    }

    public function savePayment(\stdClass $paymentDefaultData, \stdClass $dataBuilder): void
    {

        $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_payshop SET id_transacao = '" . $dataBuilder->idPedido . "', referencia = '" . 
            $dataBuilder->referencia . "', validade = '" . $dataBuilder->validade . "', order_id = '" . 
            $paymentDefaultData->order['order_id'] . "', status = 'pending'");
    }

    public function getPayshopByIdTransacao(string $idPedido): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_payshop WHERE id_transacao = '" . $this->db->escape($idPedido) . "'");
    }

    public function updatePendingPayshop(string $id, \stdClass $paymentDefaultData, \stdClass $dataBuilder): void
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_payshop` SET `id_transacao` = '" . $dataBuilder->idPedido . 
            "', `referencia` = '" . $dataBuilder->referencia . "', `order_id` = '" . $paymentDefaultData->order['order_id'] . 
            "', `validade` = '" . $dataBuilder->validade . "', `status` = 'pending' WHERE `id_ifthenpay_payshop` = '" . $id . 
            "' LIMIT 1"
        );
    }
}

?>
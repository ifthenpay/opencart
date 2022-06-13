<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentMbway extends IfthenpayModel
{
    protected $paymentMethod = Gateway::MBWAY;
    
    public function getMethod($address, $total) {
        
        if (!$this->config->get('payment_mbway_mbwayKey')) {
            $this->paymentStatus = false;
        } else if ($this->config->get('config_currency') !== 'EUR') {
            $this->paymentStatus = false;
        } else {
            $this->paymentStatus = true;
        }

        return parent::getMethod($address, $total);
    }

    public function savePayment(\stdClass $paymentDefaultData, \stdClass $dataBuilder): void
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_mbway SET id_transacao	= '" . $dataBuilder->idPedido . "', telemovel = '" . 
                    $this->db->escape($dataBuilder->telemovel) . "', order_id = '" . $paymentDefaultData->order['order_id'] . "', status = 'pending'");
    }

    public function updatePendingMbway(string $id, \stdClass $paymentDefaultData, \stdClass $dataBuilder): void
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_mbway` SET `id_transacao` = '" . $dataBuilder->idPedido . 
            "', `telemovel` = '" . $dataBuilder->telemovel . "', `order_id` = '" . $paymentDefaultData->order['order_id'] . 
            "', `status` = 'pending' WHERE `id_ifthenpay_mbway` = '" . $id . 
            "' LIMIT 1"
        );
    }

    public function getMbwayByIdTransacao(string $idPedido): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_mbway WHERE id_transacao = '" . $this->db->escape($idPedido) . "'");
    }
}

?>
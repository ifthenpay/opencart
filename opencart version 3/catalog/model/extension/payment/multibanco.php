<?php

use Ifthenpay\Payments\Gateway;

class ModelExtensionPaymentMultibanco extends IfthenpayModel
{
    protected $paymentMethod = Gateway::MULTIBANCO;

    public function getMethod($address, $total) 
    {
        if (!$this->config->get('payment_multibanco_entidade')) {
            $this->paymentStatus = false;
        } else if (!$this->config->get('payment_multibanco_subEntidade')) {
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
        if ( isset($dataBuilder->validade) && $dataBuilder->validade != '' && $dataBuilder->validade != null) {
        // if ($dataBuilder->entidade == 'mb' || $dataBuilder->entidade == 'MB') {
            $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_multibanco SET entidade = '" . $dataBuilder->entidade . "', referencia = '" . 
                $dataBuilder->referencia . "', validade = '" . $dataBuilder->validade . "', requestId = '" . $dataBuilder->idPedido . 
                    "', order_id = '" . $paymentDefaultData->order['order_id'] . "', status = 'pending'");
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_multibanco SET entidade = '" . $dataBuilder->entidade . "', referencia = '" . 
            $dataBuilder->referencia . "', order_id = '" . $paymentDefaultData->order['order_id'] . "', status = 'pending'");
        }
             
    }


    public function getMultibancoByReferencia(string $referencia): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_multibanco WHERE referencia = '" . $this->db->escape($referencia) . "'");
    }

    public function updatePendingMultibanco(string $id, \stdClass $paymentDefaultData, \stdClass $dataBuilder): void
    {
        if ($dataBuilder->entidade == 'mb' || $dataBuilder->entidade == 'MB') {
            $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_multibanco` SET `entidade` = '" . $dataBuilder->entidade . 
                    "', `referencia` = '" . $dataBuilder->referencia . "', `order_id` = '" . $paymentDefaultData->order['order_id'] . 
                    "', `status` = 'pending', `requestId` = '" . $dataBuilder->idPedido . 
                    "', `validade` = '" . $dataBuilder->validade . "' WHERE `id_ifthenpay_multibanco` = '" . $id . 
                    "' LIMIT 1"
            );
        } else {
            $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_multibanco` SET `entidade` = '" . $dataBuilder->entidade . 
                "', `referencia` = '" . $dataBuilder->referencia . "', `order_id` = '" . $paymentDefaultData->order['order_id'] . 
                "', `status` = 'pending' WHERE `id_ifthenpay_multibanco` = '" . $id . 
                "' LIMIT 1"
            );
        }
    }
}

?>
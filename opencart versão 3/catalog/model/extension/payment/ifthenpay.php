<?php

use Ifthenpay\Builders\DataBuilder;

class ModelExtensionPaymentIfthenpay extends Model
{
    public function getMethod($address, $total) {
		$this->load->language('extension/payment/ifthenpay');
		$method_data = array();
        $this->load->model('setting/setting');
        $configData =  $this->model_setting_setting->getSetting('payment_ifthenpay');
        $ifthenpayUserPaymentsMethods = unserialize($configData['payment_ifthenpay_userPaymentMethods']);
        $payments = array();
        if($configData){
            foreach($ifthenpayUserPaymentsMethods as $payment){
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$configData['payment_ifthenpay_' . $payment . '_geo_zone_id']. "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
                //die($teknopayment['status'].'**'.$teknopayment['total'].'**'.$total);
                if ($configData['payment_ifthenpay_account_' . $payment] < 0) {
                    $status = false;
                } elseif (!$configData['payment_ifthenpay_' . $payment . '_geo_zone_id']) {
                    $status = true;
                } elseif ($query->num_rows) {
                    $status = true;
                } else {
                    $status = false;
                }
                $current_language_code = $this->session->data['language'];

                if ( $status && $configData['payment_ifthenpay_account_' . $payment]) {
                    $payments[] = array(
                        'code'       => 'ifthenpay/method_' . $payment,
                        'title'      => $this->language->get('text_title_' . $payment),
                        'terms'      => '',
                        'sort_order' => $configData['payment_ifthenpay_' . $payment . '_sort_order']
                    );
                }    
            }
            $method_data = array(
            'code'       => 'ifthenpay',
            'title'      => $this->language->get('text_title'),
            'terms'      => '',
            'sort_order' => $this->config->get('payment_cod_sort_order'),
            'payments' => $payments
            );
        }
		return $method_data;
	}

    public function savePayment(string $paymentMethod, \stdClass $paymentDefaultData, \stdClass $dataBuilder): void
    {
        switch ($paymentMethod) {
            case 'multibanco':
                $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_multibanco SET entidade = '" . $dataBuilder->entidade . "', referencia = '" . 
                    $dataBuilder->referencia . "', order_id = '" . $paymentDefaultData->order['order_id'] . "', status = 'pending'");
                break;
            case 'mbway':
                $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_mbway SET id_transacao	= '" . $dataBuilder->idPedido . "', telemovel = '" . 
                    $this->db->escape($dataBuilder->telemovel) . "', order_id = '" . $paymentDefaultData->order['order_id'] . "', status = 'pending'");
                break;
            case 'payshop':
                $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_payshop SET id_transacao = '" . $dataBuilder->idPedido . "', referencia = '" . 
                    $dataBuilder->referencia . "', validade = '" . $dataBuilder->validade . "', order_id = '" . 
                        $paymentDefaultData->order['order_id'] . "', status = 'pending'");
                break;
            case 'ccard':
                $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_ccard SET requestId = '" . $dataBuilder->idPedido . "', paymentUrl = '" . 
                    $dataBuilder->paymentUrl . "', order_id = '" . $paymentDefaultData->order['order_id'] . "', status = 'pending'");
                break;
                default:
                # code...
                break;
        }       
    }

    public function updateMbwayIdTransaction(string $orderId, string $idPedido): void
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_mbway` SET `id_transacao` = '" . $idPedido . "' WHERE `order_id` = '" . $this->db->escape($orderId) . "' LIMIT 1");
    }

    public function getPaymentByOrderId(string $paymentMethod, string $orderId): \stdClass
    {
        switch ($paymentMethod) {
            case 'multibanco':
                return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_multibanco WHERE order_id = '" . $this->db->escape($orderId) . "'");
            case 'mbway':
                return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_mbway WHERE order_id = '" . $this->db->escape($orderId) . "'");
            case 'payshop':
                return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_payshop WHERE order_id = '" . $this->db->escape($orderId) . "'");
            case 'ccard':
                return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_ccard WHERE order_id = '" . $this->db->escape($orderId) . "'");
                default:
                return [];
        }       
        
    }

    public function getMultibancoByReferencia(string $referencia): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_multibanco WHERE referencia = '" . $this->db->escape($referencia) . "'");
    }

    public function getMbwayByIdTransacao(string $idPedido): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_mbway WHERE id_transacao = '" . $this->db->escape($idPedido) . "'");
    }

    public function getPayshopByIdTransacao(string $idPedido): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_payshop WHERE id_transacao = '" . $this->db->escape($idPedido) . "'");
    }

    public function getCCardByRequestId(string $idPedido): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_ccard WHERE requestId = '" . $this->db->escape($idPedido) . "'");
    }

    public function updatePaymentStatus(string $paymentMethod, string $paymentId, string $paymentStatus): void
    {
        switch ($paymentMethod) {
            case 'multibanco':
                $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_multibanco` SET `status` = '" . $paymentStatus . "' WHERE `id_ifthenpay_multibanco` = '" . $paymentId . "' LIMIT 1");
                break;
            case 'mbway':
                $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_mbway` SET `status` = '" . $paymentStatus . "' WHERE `id_ifthenpay_mbway` = '" . $paymentId . "' LIMIT 1");
                break;
            case 'payshop':
                $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_payshop` SET `status` = '" . $paymentStatus . "' WHERE `id_ifthenpay_payshop` = '" . $paymentId . "' LIMIT 1");
                break;
            case 'ccard':
                $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_ccard` SET `status` = '" . $paymentStatus . "' WHERE `id_ifthenpay_ccard` = '" . $paymentId . "' LIMIT 1");
                break;
            default:
        }
    }

    public function log($data, $title = null) 
    {
        $log = new Log('ifthenpay.log');
        $log->write('Ifthenpay debug (' . $title . '): ' . json_encode($data));
    }
}

?>
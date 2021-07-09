<?php

use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Payments\Gateway;


require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class ModelExtensionPaymentPayshop extends Model
{
    public function getMethod($address, $total) 
    {
        $this->load->language('extension/payment/payshop');
        $ifthenpayContainer = new IfthenpayContainer();
		$gateway = $ifthenpayContainer->getIoc()->make(Gateway::class);

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_payshop_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('payment_payshop_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        if (!$this->config->get('payment_payshop_payshopKey')) {
            $status = false;
        }  else if ($this->config->get('config_currency') !== 'EUR') {
            $status = false;
        } else {
            $status = true;
        }

        $method_data = array();

        if ($status) {
              $method_data = array(
            'code'       => 'payshop',
            'title'      => $gateway->getPaymentLogo('payshop', $this->config->get('config_secure') ? rtrim(HTTP_SERVER, '/') : rtrim(HTTPS_SERVER, '/')),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_payshop_sort_order')
              );
        }

        return $method_data;
    }

    public function savePayment(\stdClass $paymentDefaultData, \stdClass $dataBuilder): void
    {

        $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_payshop SET id_transacao = '" . $dataBuilder->idPedido . "', referencia = '" . 
            $dataBuilder->referencia . "', validade = '" . $dataBuilder->validade . "', order_id = '" . 
            $paymentDefaultData->order['order_id'] . "', status = 'pending'");
    }

    public function getPaymentByOrderId(string $orderId): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_payshop WHERE order_id = '" . $this->db->escape($orderId) . "'");        
    }

    public function getPayshopByIdTransacao(string $idPedido): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_payshop WHERE id_transacao = '" . $this->db->escape($idPedido) . "'");
    }

    public function updatePaymentStatus(string $paymentId, string $paymentStatus): void
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_payshop` SET `status` = '" . $paymentStatus . "' WHERE `id_ifthenpay_payshop` = '" . $paymentId . "' LIMIT 1");
    }

    public function log($data, $title = null) 
    {
        $log = new Log('ifthenpay.log');
        $log->write('Ifthenpay debug (' . $title . '): ' . json_encode($data));
    }
}

?>
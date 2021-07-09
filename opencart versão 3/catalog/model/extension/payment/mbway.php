<?php

use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Payments\Gateway;


require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class ModelExtensionPaymentMbway extends Model
{
    public function getMethod($address, $total) {
        $this->load->language('extension/payment/mbway');
        $ifthenpayContainer = new IfthenpayContainer();
		$gateway = $ifthenpayContainer->getIoc()->make(Gateway::class);
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_mbway_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('payment_mbway_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        if (!$this->config->get('payment_mbway_mbwayKey')) {
            $status = false;
        } else if ($this->config->get('config_currency') !== 'EUR') {
            $status = false;
        } else {
            $status = true;
        }

        $method_data = array();

        if ($status) {
              $method_data = array(
            'code'       => 'mbway', 
            'title'      => $gateway->getPaymentLogo('mbway', $this->config->get('config_secure') ? rtrim(HTTP_SERVER, '/') : rtrim(HTTPS_SERVER, '/')),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_mbway_sort_order')
            );
        }

        return $method_data;
    }

    public function savePayment(\stdClass $paymentDefaultData, \stdClass $dataBuilder): void
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "ifthenpay_mbway SET id_transacao	= '" . $dataBuilder->idPedido . "', telemovel = '" . 
                    $this->db->escape($dataBuilder->telemovel) . "', order_id = '" . $paymentDefaultData->order['order_id'] . "', status = 'pending'");
    }

    public function updateMbwayIdTransaction(string $orderId, string $idPedido): void
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_mbway` SET `id_transacao` = '" . $idPedido . "' WHERE `order_id` = '" . $this->db->escape($orderId) . "' LIMIT 1");
    }

    public function getPaymentByOrderId(string $orderId): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_mbway WHERE order_id = '" . $this->db->escape($orderId) . "'");        
    }

    public function getMbwayByIdTransacao(string $idPedido): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_mbway WHERE id_transacao = '" . $this->db->escape($idPedido) . "'");
    }

    public function updatePaymentStatus(string $paymentId, string $paymentStatus): void
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_mbway` SET `status` = '" . $paymentStatus . "' WHERE `id_ifthenpay_mbway` = '" . $paymentId . "' LIMIT 1");
    }

    public function getAllMbwayPendingOrders(): array
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE `payment_code` = 'mbway' AND `order_status_id` =" . $this->config->get('payment_mbway_order_status_id'));
		
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}

    public function log($data, $title = null) 
    {
        $log = new Log('ifthenpay.log');
        $log->write('Ifthenpay debug (' . $title . '): ' . json_encode($data));
    }
}

?>
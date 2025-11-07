<?php

use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Config\IfthenpaySql;
use Ifthenpay\Payments\Gateway;
use Ifthenpay\Utility\Url;

require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

class IfthenpayModel extends Model
{
    protected $paymentMethod;
    protected $ifthenpayContainer;
    protected $paymentStatus;

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->ifthenpayContainer = new IfthenpayContainer();
    }

    public function install(string $userPaymentMethod)
    {
        $this->ifthenpayContainer->getIoc()->make(IfthenpaySql::class)->setIfthenpayModel($this)->setUserPaymentMethod($userPaymentMethod)->install();
    }

    public function uninstall(string $userPaymentMethod)
    {
        $this->ifthenpayContainer
            ->getIoc()
            ->make(IfthenpaySql::class)
            ->setIfthenpayModel($this)
            ->setUserPaymentMethod($userPaymentMethod)
            ->uninstall();
    }

    public function getAllPendingOrders(): array
    {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "order WHERE `payment_code` = '" . $this->paymentMethod . "' AND `order_status_id` =" .
                $this->config->get(
                    'payment_' . $this->paymentMethod . '_order_status_id'
                )
        );

        if ($query->num_rows) {
            return $query->rows;
        } else {
            return [];
        }
    }

    public function getPaymentByOrderId(string $orderId): \stdClass
    {
        return $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "ifthenpay_" . $this->paymentMethod .
                " WHERE order_id = '" . $this->db->escape($orderId) . "'"
        );
    }

    public function deletePaymentByOrderId(string $orderId): void
    {
        $this->db->query(
            "DELETE FROM " . DB_PREFIX . "ifthenpay_" . $this->paymentMethod .
                " WHERE order_id = '" . $this->db->escape($orderId) . "'"
        );
    }

    public function log($data, $title = null)
    {
        $log = new Log($this->paymentMethod . '.log');
        $log->write('Ifthenpay debug (' . $title . '): ' . json_encode($data));
    }

    public function deleteSettingByKey(string $key, $store_id = 0)
    {
        $this->db->query(
            "DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int) $store_id . "' AND `key` = '" .
                $this->db->escape($key) . "'"
        );
    }

    public function updatePaymentStatus(string $paymentId, string $paymentStatus): void
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_" . $this->paymentMethod . "` SET `status` = '" . $paymentStatus .
            "' WHERE `id_ifthenpay_" . $this->paymentMethod . "` = '" . $paymentId . "' LIMIT 1");
    }



    public function updatePaymentStatusByOrderId(string $orderId, string $paymentStatus): void
    {
        $this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_" . $this->paymentMethod . "` SET `status` = '" . $paymentStatus .
            "' WHERE `order_id` = '" . $orderId . "' LIMIT 1");
    }



    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/' . $this->paymentMethod);
        $gateway = $this->ifthenpayContainer->getIoc()->make(Gateway::class);

        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('payment_' .
                $this->paymentMethod . '_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] .
                "' OR zone_id = '0')"
        );



        if (
            ($this->config->get('payment_' . $this->paymentMethod . '_minimum_value') > 0 &&
                $this->config->get('payment_' . $this->paymentMethod . '_minimum_value') > $total) ||
            ($this->config->get('payment_' . $this->paymentMethod . '_maximum_value') > 0 &&
                $this->config->get('payment_' . $this->paymentMethod . '_maximum_value') < $total)
        ) {
            $this->paymentStatus = false;
        } else if (!$this->config->get('payment_' . $this->paymentMethod . '_geo_zone_id')) {
            $this->paymentStatus = true;
        } else if ($query->num_rows) {
            $this->paymentStatus = true;
        } else {
            $this->paymentStatus = false;
        }

        if (
            $this->request->get['route'] === 'api/payment/methods' &&
            !in_array($this->paymentMethod, $this->ifthenpayContainer->getIoc()->make(Gateway::class)->getPaymentMethodsCanOrderBackend())
        ) {
            $this->paymentStatus = false;
        }

        $method_data = array();

        if ($this->paymentStatus) {
            $method_data = [
                'code' => $this->paymentMethod,
                'terms' => '',
                'sort_order' => $this->config->get('payment_' . $this->paymentMethod . '_sort_order')
            ];
            if ($this->config->get('payment_' . $this->paymentMethod . '_showPaymentMethodLogo') && $this->request->get['route'] !== 'api/payment/methods') {

                $cacheBuster = $this->paymentMethod === 'ifthenpaygateway' ? '?v=' . time() : '';

                $method_data['title'] = '<img src="' . $gateway->getPaymentLogoUrl(
                    $this->paymentMethod,
                    Url::catalogUrl($this->config->get('config_secure'))
                ) . $cacheBuster . '" height="28px">';
            } else {

                $this->load->model('setting/setting');
                $configData = $this->model_setting_setting->getSetting('payment_' . $this->paymentMethod);
                $method_data['title'] = html_entity_decode($configData['payment_' . $this->paymentMethod . '_payment_method_title']);
            }
        }
        return $method_data;
    }

    public function getOrderHistory(string $orderId): \stdClass
    {
        return $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = '" .
                $this->db->escape($orderId) . "'"
        );
    }


    public function getIfthenpaygatewayByOrderId(string $orderId): \stdClass
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_ifthenpaygateway WHERE order_id = '" . $this->db->escape($orderId) . "'");
    }
}

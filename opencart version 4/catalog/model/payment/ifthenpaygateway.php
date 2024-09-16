<?php
namespace Opencart\Catalog\Model\Extension\ifthenpay\Payment;

class Ifthenpaygateway extends \Opencart\System\Engine\Model
{
	/**
	 * gets the payment method and creates the option that will be displayed on the checkout page
	 * validates the currency, address, order total and min/max order total against the configuration set in the admin
	 * @param array $address
	 * @param float $total
	 * @return array
	 */
	public function getMethods(array $address, float $total = 0.0): array
	{
		$this->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		// validate if currency is euro
		$currency = $this->session->data['currency'];
		if ($currency != 'EUR') {
			return [];
		}

		// validate address
		// default to shipping address if no payment address
		if (empty($address)) {
			$address = $this->session->data['shipping_address'];
		}

		$geoZoneId = $this->config->get('payment_ifthenpaygateway_geo_zone_id');
		if ($geoZoneId) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int) $geoZoneId . "' AND `country_id` = '" . (int) $address['country_id'] . "' AND (`zone_id` = '" . (int) $address['zone_id'] . "' OR `zone_id` = '0')");

			if (!$query->num_rows) {
				return [];
			}
		}

		// validate max/min order total
		// default to cart total if no order total in args
		if ($total == 0.0) {
			$total = $this->cart->getTotal();
		}

		$minAmount = $this->config->get('payment_ifthenpaygateway_min_value');
		$maxAmount = $this->config->get('payment_ifthenpaygateway_max_value');
		if (
			($minAmount > 0 && $minAmount > $total) ||
			($maxAmount > 0 && $maxAmount < $total) ||
			$total <= 0
		) {
			return [];
		}

		$option_data['ifthenpaygateway'] = [
			'code' => 'ifthenpaygateway.' . 'ifthenpaygateway',
			'name' => $this->config->get('payment_ifthenpaygateway_title')
		];

		$method_data = array(
			'code' => 'ifthenpaygateway',
			'name' => $this->language->get('text_pay_with_method'),
			'option' => $option_data,
			'sort_order' => $this->config->get('payment_ifthenpaygateway_sort_order')
		);

		return $method_data;
	}



	/**
	 * adds a new ifthenpaygateway record (representing an order) to the database
	 * @param array $data
	 * @return void
	 */
	public function addIfthenpaygatewayRecord(array $data): void
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_ifthenpaygateway` WHERE `order_id` = '" . $data['order_id'] . "'");

		if ($query->num_rows) {
			return;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "ifthenpay_ifthenpaygateway` SET " .
			"`order_id` = '" . $data['order_id'] . "', " .
			"`transaction_id` = '" . $data['transaction_id'] . "', " .
			"`deadline` = '" . $data['deadline'] . "', " .
			"`status` = '" . $data['status'] . "', " .
			"`date_added` = NOW()");
	}



	public function getIfthenpaygatewayRecordByTransactionId($transactionId): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_ifthenpaygateway` WHERE `transaction_id` = '" . $transactionId . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}



	public function getIfthenpayGatewayRecordByOrderId($orderId)
	{
		if ($orderId == '') {
			return [];
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_ifthenpaygateway` WHERE `order_id` = '" . $orderId . "' order by date_added desc limit 1");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}



	/**
	 * gets all ifthenpaygateway records that have status = pending from database
	 * @return array
	 */
	public function getIfthenpaygatewayRecordsByPendingStatus()
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_ifthenpaygateway` WHERE `status` = 'pending'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}



	public function updateIfthenpaygatewayRecordStatus($orderId, $status)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_ifthenpaygateway` SET `status` = '" . $status . "' WHERE `order_id` = '" . $orderId . "'");
	}



	/**
	 * updates the status of a ifthenpaygateway record
	 * @param int $order_id
	 * @param string $status
	 * @return void
	 */
	public function updateIfthenpaygatewayRecordStatusByTransactionId($transactionId, $status)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_ifthenpaygateway` SET `status` = '" . $status . "' WHERE `transaction_id` = '" . $transactionId . "'");
	}
}

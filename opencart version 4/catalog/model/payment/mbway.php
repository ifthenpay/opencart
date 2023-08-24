<?php
namespace Opencart\Catalog\Model\Extension\ifthenpay\Payment;

class Mbway extends \Opencart\System\Engine\Model
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
		$this->load->language('extension/ifthenpay/payment/mbway');

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

		$geoZoneId = $this->config->get('payment_mbway_geo_zone_id');
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

		$minAmount = $this->config->get('payment_mbway_min_value');
		$maxAmount = $this->config->get('payment_mbway_max_value');
		if (
			($minAmount > 0 && $minAmount > $total) ||
			($maxAmount > 0 && $maxAmount < $total) ||
			$total <= 0
		) {
			return [];
		}

		$option_data['mbway'] = [
			'code' => 'mbway.' . 'mbway',
			'name' => $this->config->get('payment_mbway_title')
		];

		$method_data = array(
			'code' => 'mbway',
			'name' => $this->language->get('text_pay_with_method'),
			'option' => $option_data,
			'sort_order' => $this->config->get('payment_mbway_sort_order')
		);

		return $method_data;
	}



	/**
	 * adds a new mbway record (representing an order) to the database
	 * @param array $data
	 * @return void
	 */
	public function addMbwayRecord(array $data): void
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ifthenpay_mbway` SET " .
			"`order_id` = '" . $data['order_id'] . "', " .
			"`phone_number` = '" . $data['phone_number'] . "', " .
			"`transaction_id` = '" . $data['transaction_id'] . "', " .
			"`status` = '" . $data['status'] . "', " .
			"`date_added` = NOW()");
	}



	/**
	 * gets a mbway record from the database by its transaction id
	 * @param string $order_id
	 * @return array
	 */
	public function getMbwayRecordByTransactionId($transactionId): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_mbway` WHERE `transaction_id` = '" . $transactionId . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}



	/**
	 * gets a mbway record from the database by its order id
	 * @param string $order_id
	 * @return array
	 */
	public function getMbwayRecordByOrderId($orderId): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_mbway` WHERE `order_id` = '" . $orderId . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}




	/**
	 * gets all mbway records that have status = pending from database
	 * @return array
	 */
	public function getMbwayRecordsByPendingStatus()
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_mbway` WHERE `status` = 'pending'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}



	/**
	 * updates the status of a mbway record
	 * @param int $order_id
	 * @param string $status
	 * @return void
	 */
	public function updateMbwayRecordStatus($order_id, $status): void
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_mbway` SET `status` = '" . $status . "' WHERE `order_id` = '" . $order_id . "'");
	}



	/**
	 * updates the transaction id of a mbway record
	 * @param int $order_id
	 * @param string $transactionId
	 * @return void
	 */
	public function updateMbwayRecordTransactionId($order_id, $transactionId): void
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_mbway` SET `transaction_id` = '" . $transactionId . "' WHERE `order_id` = '" . $order_id . "'");
	}
}

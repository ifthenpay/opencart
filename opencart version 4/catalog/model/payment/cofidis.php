<?php
namespace Opencart\Catalog\Model\Extension\ifthenpay\Payment;

class Cofidis extends \Opencart\System\Engine\Model
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
		$this->load->language('extension/ifthenpay/payment/cofidis');

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

		$geoZoneId = $this->config->get('payment_cofidis_geo_zone_id');
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

		$minAmount = $this->config->get('payment_cofidis_min_value');
		$maxAmount = $this->config->get('payment_cofidis_max_value');
		if (
			($minAmount > 0 && $minAmount > $total) ||
			($maxAmount > 0 && $maxAmount < $total) ||
			$total <= 0
		) {
			return [];
		}

		$option_data['cofidis'] = [
			'code' => 'cofidis.' . 'cofidis',
			'name' => $this->config->get('payment_cofidis_title')
		];

		$method_data = array(
			'code' => 'cofidis',
			'name' => $this->language->get('text_pay_with_method'),
			'option' => $option_data,
			'sort_order' => $this->config->get('payment_cofidis_sort_order')
		);

		return $method_data;
	}



	/**
	 * adds a new cofidis record (representing an order) to the database
	 * @param array $data
	 * @return void
	 */
	public function addCofidisRecord(array $data): void
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ifthenpay_cofidis` SET " .
			"`order_id` = '" . $data['order_id'] . "', " .
			"`transaction_id` = '" . $data['transaction_id'] . "', " .
			"`hash` = '" . $data['hash'] . "', " .
			"`status` = '" . $data['status'] . "', " .
			"`date_added` = NOW()");
	}


	public function getCofidisRecordByTransactionId($transactionId): array
	{
		if ($transactionId == '') {
			return [];
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_cofidis` WHERE `transaction_id` = '" . $transactionId . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}

	/**
	 * gets a cofidis record from the database by its reference
	 * @param string $order_id
	 * @return array
	 */
	public function getCofidisRecordByOrderIdAndHash(string $orderId, string $hash): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_cofidis` WHERE `order_id` = '" . $orderId . "' AND `hash` = '" . $hash . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}


	public function getRecordByOrderId(string $orderId): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_cofidis` WHERE `order_id` = '" . $orderId . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}


	/**
	 * gets all cofidis records that have status = pending from database
	 * @return array
	 */
	public function getCofidisRecordsByPendingStatus()
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_cofidis` WHERE `status` = 'pending'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}



	/**
	 * updates the status of a cofidis record
	 * @param string $order_id
	 * @param string $hash
	 * @param string $status
	 * @return void
	 */
	public function updateCofidisRecordStatusByOrderIdAndHash(string $orderId, string $hash, string $status)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_cofidis` SET `status` = '" . $status . "' WHERE `order_id` = '" . $orderId . "' AND `hash` = '" . $hash . "'");
	}


	public function updateCofidisRecordStatusByTransactionId(string $transactionId, string $status)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_cofidis` SET `status` = '" . $status . "' WHERE `transaction_id` = '" . $transactionId . "'");
	}
}

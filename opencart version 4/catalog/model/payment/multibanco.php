<?php
namespace Opencart\Catalog\Model\Extension\ifthenpay\Payment;

class Multibanco extends \Opencart\System\Engine\Model
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
		$this->load->language('extension/ifthenpay/payment/multibanco');

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

		$geoZoneId = $this->config->get('payment_multibanco_geo_zone_id');
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

		$minAmount = $this->config->get('payment_multibanco_min_value');
		$maxAmount = $this->config->get('payment_multibanco_max_value');
		if (
			($minAmount > 0 && $minAmount > $total) ||
			($maxAmount > 0 && $maxAmount < $total) ||
			$total <= 0
		) {
			return [];
		}

		$option_data['multibanco'] = [
			'code' => 'multibanco.' . 'multibanco',
			'name' => $this->config->get('payment_multibanco_title')
		];

		$method_data = array(
			'code' => 'multibanco',
			'name' => $this->language->get('text_pay_with_method'),
			'option' => $option_data,
			'sort_order' => $this->config->get('payment_multibanco_sort_order')
		);

		return $method_data;
	}



	/**
	 * adds a new multibanco record (representing an order) to the database
	 * @param array $data
	 * @return void
	 */
	public function addMultibancoRecord(array $data): void
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ifthenpay_multibanco` SET " .
			"`entity` = '" . $data['entity'] . "', " .
			"`reference` = '" . $data['reference'] . "', " .
			"`order_id` = '" . $data['order_id'] . "', " .
			"`status` = '" . $data['status'] . "', " .
			"`request_id` = '" . $data['request_id'] . "', " .
			"`deadline` = '" . $data['deadline'] . "', " .
			"`date_added` = NOW()");
	}



	/**
	 * gets a multibanco record from the database by its reference
	 * @param string $order_id
	 * @return array
	 */
	public function getMultibancoRecordByReference($reference): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_multibanco` WHERE `reference` = " . $reference);

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}



	/**
	 * gets all multibanco records that have status = pending from database
	 * @return array
	 */
	public function getMultibancoRecordsByPendingStatus()
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_multibanco` WHERE `status` = 'pending'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}



	/**
	 * updates the status of a multibanco record
	 * @param int $order_id
	 * @param string $status
	 * @return void
	 */
	public function updateMultibancoRecordStatus($order_id, $status)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_multibanco` SET `status` = '" . $status . "' WHERE `order_id` = '" . $order_id . "'");
	}
}

<?php
namespace Opencart\Admin\Model\Extension\ifthenpay\Payment;

class Cofidis extends \Opencart\System\Engine\Model
{
	public function install(): void
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ifthenpay_cofidis` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` varchar(20) NOT NULL,
			`transaction_id` varchar(50),
			`hash` varchar(20),
			`status` varchar(50) NOT NULL,
			`date_added` datetime NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");


		// add event for showing cofidis payment details (reference, amount...)  after checkout
		$eventData = [
			'code' => 'payment_ifthenpay_cofidis_catalog_success_payment_info',
			'description' => 'To display Credit Card payment information after checkout success.',
			'trigger' => 'catalog/view/common/success/after',
			'action' => 'extension/ifthenpay/payment/cofidis.success_payment_info',
			'status' => 1,
			'sort_order' => 1
		];
		$this->model_setting_event->addEvent($eventData);

	}

	/**
	 * Clears the database of the module tables and events
	 * @return void
	 */
	public function uninstall(): void
	{
		// delete ifthenpay_cofidis table
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ifthenpaycofidis`");

		// delete events
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('payment_ifthenpay_cofidis_catalog_success_payment_info');
	}



	public function getCofidisRecordByOrderId($orderId)
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_cofidis` WHERE `order_id` = '" . $orderId . "' order by date_added desc limit 1");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}


	public function updateCofidisRecordStatus($orderId, $status)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_cofidis` SET `status` = '" . $status . "' WHERE `order_id` = '" . $orderId . "'");
	}


	public function addOrderHistory($orderId, $orderStatusId, $comment)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $orderStatusId . "', date_modified = NOW() WHERE order_id = '" . (int) $orderId . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $orderId . "', order_status_id = '" . (int) $orderStatusId . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}



}

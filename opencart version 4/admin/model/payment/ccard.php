<?php
namespace Opencart\Admin\Model\Extension\ifthenpay\Payment;

class Ccard extends \Opencart\System\Engine\Model
{
	public function install(): void
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ifthenpay_ccard` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` varchar(20) NOT NULL,
			`transaction_id` varchar(50),
			`status` varchar(50) NOT NULL,
			`date_added` datetime NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");


		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ifthenpay_ccard_refund` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` varchar(20) NOT NULL,
			`amount` varchar(10) NOT NULL,
			`description` varchar(255),
			`date_added` datetime NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");

		// add event for showing ccard payment details (reference, amount...)  after checkout
		$eventData = [
			'code' => 'payment_ifthenpay_ccard_catalog_success_payment_info',
			'description' => 'To display Credit Card payment information after checkout success.',
			'trigger' => 'catalog/view/common/success/after',
			'action' => 'extension/ifthenpay/payment/ccard.success_payment_info',
			'status' => 1,
			'sort_order' => 1
		];
		$this->model_setting_event->addEvent($eventData);

		// add event for adding refund tab and form in admin order info page
		$eventData = [
			'code' => 'payment_ifthenpay_ccard_admin_refund',
			'description' => 'To display Credit Card payment refund.',
			'trigger' => 'admin/view/sale/order_info/before',
			'action' => 'extension/ifthenpay/payment/ccard.eventRenderRefundForm',
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
		// delete ifthenpay_ccard table
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ifthenpayccard`");
		// delete ifthenpay_ccard_refund table
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ifthenpay_ccard_refund`");

		// delete events
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('payment_ifthenpay_ccard_catalog_success_payment_info');
		$this->model_setting_event->deleteEventByCode('payment_ifthenpay_ccard_admin_refund');
	}



	public function getCcardRecordByOrderId($orderId)
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_ccard` WHERE `order_id` = '" . $orderId . "' order by date_added desc limit 1");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}



	public function getCcardRefundRecordByOrderId($orderId)
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_ccard_refund` WHERE `order_id` = '" . $orderId . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}




	public function getAllCcardRefundRecordsByOrderId($orderId)
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_ccard_refund` WHERE `order_id` = '" . $orderId . "'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}


	public function getTotalAmountRefunded($orderId)
	{
		$query = $this->db->query("SELECT SUM(`amount`) AS total_amount FROM `" . DB_PREFIX . "ifthenpay_ccard_refund` WHERE `order_id` = '" . $orderId . "'");

		if ($query->num_rows) {
			return $query->row['total_amount'];
		} else {
			return [];
		}
	}


	// addCcardRefundRecord
	public function addCcardRefundRecord($orderId, $amount, $description)
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ifthenpay_ccard_refund` SET `order_id` = '" . $orderId . "', `amount` = '" . $this->db->escape($amount) . "', `description` = '" . $this->db->escape($description) . "', `date_added` = NOW()");
	}


	// addCcardRefundRecord
	public function updateCcardRecordStatus($orderId, $status)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_ccard` SET `status` = '" . $status . "' WHERE `order_id` = '" . $orderId . "'");
	}


	public function addOrderHistory($orderId, $orderStatusId, $comment)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $orderStatusId . "', date_modified = NOW() WHERE order_id = '" . (int) $orderId . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $orderId . "', order_status_id = '" . (int) $orderStatusId . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}



}

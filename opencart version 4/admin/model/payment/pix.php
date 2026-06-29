<?php

namespace Opencart\Admin\Model\Extension\ifthenpay\Payment;

require_once DIR_EXTENSION . 'ifthenpay/system/library/CronTrait.php';

use Ifthenpay\CronTrait;

class Pix extends \Opencart\System\Engine\Model
{
	use CronTrait;
	
	public function install(): void
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ifthenpay_pix` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` varchar(20) NOT NULL,
			`transaction_id` varchar(50),
			`payment_url` varchar(255),
			`status` varchar(50) NOT NULL,
			`date_added` datetime NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");

		// add event for showing pix payment details (reference, amount...)  after checkout
		$this->model_setting_event->addEvent([
			'code' => 'payment_ifthenpay_pix_catalog_success_payment_info',
			'description' => 'To display Pix payment information after checkout success.',
			'trigger' => 'catalog/view/common/success/after',
			'action' => 'extension/ifthenpay/payment/pix.success_payment_info',
			'status' => 1,
			'sort_order' => 1
		]);

		$this->model_setting_event->addEvent([
			'code'        => 'payment_ifthenpay_pix_icon_injection',
			'description' => 'Injects CSS for Pix payment method icon in the checkout page.',
			'trigger'     => 'catalog/view/checkout/checkout/before',
			'action'      => 'extension/ifthenpay/payment/pix.injectIconCss',
			'status'      => 1,
			'sort_order'  => 1
		]);

		$this->installCron();
	}

	/**
	 * Clears the database of the module tables and events
	 * @return void
	 */
	public function uninstall(): void
	{
		// delete ifthenpay_pix table
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ifthenpay_pix`");

		// delete events
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('payment_ifthenpay_pix_catalog_success_payment_info');
		$this->model_setting_event->deleteEventByCode('payment_ifthenpay_pix_icon_injection');

		$this->uninstallCron();
	}



	public function getPixRecordByOrderId($orderId)
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ifthenpay_pix` WHERE `order_id` = '" . $orderId . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return [];
		}
	}



	public function updatepixRecordStatus($orderId, $status)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_pix` SET `status` = '" . $status . "' WHERE `order_id` = '" . $orderId . "'");
	}



	public function addOrderHistory($orderId, $orderStatusId, $comment)
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $orderStatusId . "', date_modified = NOW() WHERE order_id = '" . (int) $orderId . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $orderId . "', order_status_id = '" . (int) $orderStatusId . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
}

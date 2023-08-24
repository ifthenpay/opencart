<?php
namespace Opencart\Admin\Model\Extension\ifthenpay\Payment;

class Multibanco extends \Opencart\System\Engine\Model
{
	public function install(): void
	{
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ifthenpay_multibanco` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`entity` varchar(20) NOT NULL,
			`reference` varchar(50) NOT NULL,
			`order_id` varchar(20) NOT NULL,
			`status` varchar(50) NOT NULL,
			`deadline` varchar(15),
			`request_id` varchar(50),
			`date_added` datetime NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");


		// add event for showing multibanco payment details (entity, reference, amount...)  after checkout
		$eventData = [
			'code' => 'payment_ifthenpay_multibanco_catalog_success_payment_info',
			'description' => 'To display Multibanco payment information after checkout success.',
			'trigger' => 'catalog/view/common/success/after',
			'action' => 'extension/ifthenpay/payment/multibanco.success_payment_info',
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
		// delete ifthenpay_multibanco table
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ifthenpaymultibanco`");

		// delete events
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('payment_ifthenpay_multibanco_catalog_success_payment_info');
	}
}

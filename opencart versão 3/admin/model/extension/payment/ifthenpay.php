<?php

use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Config\IfthenpaySql;

class ModelExtensionPaymentIfthenpay extends Model {

	public function install(IfthenpayContainer $ifthenpayContainer, array $userPaymentMethods) {
		$ifthenpayContainer->getIoc()->make(IfthenpaySql::class)->setIfthenpayModel($this)->setUserPaymentMethods($userPaymentMethods)->install();
	}

	public function uninstall(IfthenpayContainer $ifthenpayContainer, array $userPaymentMethods) {
		if (!empty($userPaymentMethods)) {
			$ifthenpayContainer
			->getIoc()
			->make(IfthenpaySql::class)
			->setIfthenpayModel($this)
			->setUserPaymentMethods($userPaymentMethods)
			->uninstall();
		}
		
	}

	public function configureManageButton() {
		$this->load->model('user/user_group');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `code` = 'ifthenpay_manage_payment_method'");
        		
        if (empty($query->row)) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "extension SET `type` = 'module', `code` = 'ifthenpay_manage_payment_method'");

            $user_group_id = $this->user->getGroupId();
       									
			$this->model_user_user_group->addPermission($user_group_id, 'access', 'extension/module/ifthenpay_manage_payment_method');
			$this->model_user_user_group->addPermission($user_group_id, 'modify', 'extension/module/ifthenpay_manage_payment_method');
			
			$this->load->controller('extension/module/ifthenpay_manage_payment_method/install');
        }
	}

	public function getAllMbwayPendingOrders(): array
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE `payment_code` = 'ifthenpay/method_mbway' AND `order_status_id` =" . $this->config->get('payment_ifthenpay_mbway_order_status_id'));
		
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}

	public function log($data, $title = null) {
			$log = new Log('ifthenpay.log');
			$log->write('Ifthenpay debug (' . $title . '): ' . json_encode($data));
	}
}
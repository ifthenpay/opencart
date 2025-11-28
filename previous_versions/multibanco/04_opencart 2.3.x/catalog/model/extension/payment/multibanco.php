<?php
	class ModelExtensionPaymentMultibanco extends Model {
	  	public function getMethod($address, $total) {
			$this->load->language('extension/payment/multibanco');

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('multibanco_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if ($this->config->get('multibanco_valorminimo') > 0 && $this->config->get('multibanco_valorminimo') > $total) {
				$status = false;
			} elseif (!$this->config->get('multibanco_geo_zone_id')) {
				$status = true;
			} elseif ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}

			$method_data = array();

			if ($status) {
	      		$method_data = array(
				'code'       => 'multibanco',
				'title'      => $this->language->get('text_title'),
					'terms'      => '',
					'sort_order' => $this->config->get('multibanco_sort_order')
	      		);
	    	}

	    	return $method_data;
	  	}

		public function getVersion(){
			return $this->db->query("SELECT max(versao) as versao FROM `" . DB_PREFIX . "ifthenpay_version`")->row;
		}

		public function getOrderIdByIfthenpayData($entidade, $referencia, $valor){
			return $this->db->query("SELECT multibanco_id, order_id FROM `" . DB_PREFIX . "ifthenpay_multibanco` WHERE entidade='" . $this->db->escape($entidade) . "' and referencia='" . $this->db->escape($referencia) . "' and valor like '" . $this->db->escape($valor) . "%' and estado = 0 ORDER BY multibanco_id desc limit 1")->row;
		}

		public function setIfthenpayDataStatus($id){
			$this->db->query("UPDATE `" . DB_PREFIX . "ifthenpay_multibanco` SET estado='1' WHERE multibanco_id='" . $this->db->escape($id) . "'");
		}

		public function getIfthenpayData($order_id){
			return $this->db->query("SELECT * FROM " . DB_PREFIX . "ifthenpay_multibanco WHERE order_id = '" . $this->db->escape($order_id) . "'")->rows;
		}

		public function setIfthenpayData($order_id, $entidade, $referencia, $valor, $status=0){
			$this->db->query("
INSERT INTO 
" . DB_PREFIX . "ifthenpay_multibanco (order_id, entidade, referencia, valor, estado) 
VALUES 
('" . $this->db->escape($order_id) . "','" . $this->db->escape($entidade) . "','" . $this->db->escape(str_replace(" ","",$referencia)) . "','" . $this->db->escape($valor) . "','" . $this->db->escape($status) . "') 
ON DUPLICATE KEY UPDATE estado = '" . $this->db->escape($status) . "'");
		}
	}
?>

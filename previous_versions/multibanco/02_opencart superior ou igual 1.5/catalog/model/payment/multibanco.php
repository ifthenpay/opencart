<?php 
class ModelPaymentMultibanco extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('payment/multibanco');
		
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
				'sort_order' => $this->config->get('multibanco_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>

<?php

class ModelPaymentMultibanco extends Model {

    private function checkVersionExist($version)
    {
        $val = "";
        try {
            $val = $this->db->query("SELECT versao FROM `" . DB_PREFIX . "ifthenpay_version` WHERE versao = '" . $this->db->escape($version). "'")->row;
        }catch (Exception $e){
            return false;
        }

        return (empty($val["versao"])?false:$val["versao"]==$version);
    }

	public function install() {
        $this->update();
	}

    public function update() {
        $this->update510();
        $this->update511();
    }

    private function update511()
    {

        $version = "5.1.1";

        if ($this->checkVersionExist($version))
            return;

        $changes = "
		<li>Pequenas correções</li>
		";

        $this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "ifthenpay_version` (versao, observacao) VALUE ('$version', '$changes')");
    }

	private function update510(){

        $version = "5.1.0";

        if($this->checkVersionExist($version))
            return;

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ifthenpay_multibanco` (
			  `multibanco_id` int(11) NOT NULL,
			  `order_id` int(11) NOT NULL,
			  `entidade` varchar(5) NOT NULL,
			  `referencia` varchar(9) NOT NULL,
			  `valor` varchar(10) NOT NULL,
			  `estado` int(1) NOT NULL DEFAULT '0'
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;
		");

		try {
			$alter = "
			ALTER TABLE `" . DB_PREFIX . "ifthenpay_multibanco`
			  ADD PRIMARY KEY (`multibanco_id`),
			  ADD UNIQUE KEY `multibanco_order_id` (`order_id`);
		";

			$this->db->query($alter);
		}
		catch (Exception $e) {
		}

		try {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ifthenpay_multibanco`
			  MODIFY `multibanco_id` int(11) NOT NULL AUTO_INCREMENT;
		");}
		catch (Exception $e) {
		}

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ifthenpay_version` (
			  `versao` varchar(5) NOT NULL,
  			  `observacao` varchar(255) NOT NULL
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;
		");

		try {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ifthenpay_version`
			  ADD PRIMARY KEY (`versao`);
		");
		}
		catch (Exception $e) {
		}

		$changes = "
		<li>Dados multibanco agora aparecem na página de sucesso da encomenda</li>
		<li>Implementação do mecanismo de callback</li>
		";

		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "ifthenpay_version` (versao, observacao) VALUE ('$version', '$changes')");

		$modification = $this->db->query("SELECT * FROM `" . DB_PREFIX . "modification` WHERE code ='ifthenpay' AND version = '$version'");

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<modification>
    <name>Multibanco Ifthenpay</name>
    <code>ifthenpay</code>
    <version>' . $version . '</version>
    <author>Ifthenpay Lda</author>
    <link>http://www.ifthenpay.com</link>
    <file path="catalog/controller/checkout/success.php">
        <operation>
            <search><![CDATA[
            $this->load->language(\'checkout/success\');
            ]]></search>
            <add><![CDATA[

            $this->load->language(\'checkout/success\');

            $order_id = "";
            $payment_method = "";
            ]]></add>
        </operation>
        <operation>
            <search><![CDATA[
            if (isset($this->session->data[\'order_id\'])) {
            ]]></search>
            <add><![CDATA[

            if (isset($this->session->data[\'order_id\'])) {

            $order_id = $this->session->data[\'order_id\'];
            $payment_method = $this->session->data["payment_method"]["code"];
            ]]></add>
        </operation>
         <operation>
            <search><![CDATA[
            $data[\'header\'] = $this->load->controller(\'common/header\');
            ]]></search>
            <add><![CDATA[

            $data[\'header\'] = $this->load->controller(\'common/header\');

            if ($payment_method == "multibanco") {

            $this->load->model(\'payment/multibanco\');

            $multibanco_info = $this->model_payment_multibanco->getIfthenpayData($order_id);

            $referencia = $multibanco_info[0]["referencia"];

            $referencia = substr($referencia, 0, 3) . " ". substr($referencia, 3, 3) . " " . substr($referencia, 6, 3);

            $data_mb = array(\'entry_entidade\'=>\'\',\'entry_referencia\'=>\'\',\'entry_valor\'=>\'\');
		$data_mb[\'entry_entidade\'] .= $multibanco_info[0]["entidade"];
		$data_mb[\'entry_referencia\'] .= $referencia;
		$data_mb[\'entry_valor\'] .= $multibanco_info[0]["valor"];

    if (file_exists(DIR_TEMPLATE . $this->config->get(\'config_template\') . \'/template/payment/multibanco_success.tpl\')) {
      $mb_template = $this->load->view($this->config->get(\'config_template\') . \'/template/payment/multibanco_success.tpl\', $data_mb);
    } else {
      $mb_template = $this->load->view(\'/payment/multibanco_success.tpl\', $data_mb);
    }

		$data[\'text_message\'] .= $mb_template;
		}
            ]]></add>
        </operation>
    </file>
</modification>';


        if(sizeof($modification->rows) == 0){


            $this->setModification($version, $xml);


        }else{
            $this->setModification($version, $xml, true);
        }
	}

    private function setModification($version, $xml, $update=false){
        if($update){
            $this->db->query("UPDATE `" . DB_PREFIX . "modification` SET xml = '" . $this->db->escape($xml). "' WHERE code = 'ifthenpay' AND version = '" . $this->db->escape($version) . "'");
            return;
        }

        $this->db->query("INSERT INTO `" . DB_PREFIX . "modification`(name, code, author, version, link, xml, status, date_added) VALUES ('Multibanco Ifthenpay', 'ifthenpay','Ifthenpay, Lda', '" . $this->db->escape($version) . "', 'http://www.ifthenpay.com', '" . $this->db->escape($xml) . "', '1', NOW())");
    }
}

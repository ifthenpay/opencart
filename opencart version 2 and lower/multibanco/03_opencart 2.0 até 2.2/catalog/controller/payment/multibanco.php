<?php
class ControllerPaymentMultibanco extends Controller {

	public function index() {

		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['continue'] = $this->url->link('checkout/successMultibanco');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/multibanco.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/multibanco.tpl', $data);
		} else {
			return $this->load->view('/payment/multibanco.tpl', $data);
		}
	}

	public function version(){
		$this->load->model('payment/multibanco');
		echo $this->model_payment_multibanco->getVersion()["versao"];
	}

	public function callback(){
		$chave_ap_int = $this->config->get('multibanco_ap');
		$chave_ap_ext = $this->request->get['chave'];
		$entidade = $this->request->get['entidade'];
		$referencia = $this->request->get['referencia'];
		$valor = $this->request->get['valor'];

		if($chave_ap_int==$chave_ap_ext) {

			$this->load->model('payment/multibanco');

			$order_info_ip = $this->model_payment_multibanco->getOrderIdByIfthenpayData($entidade, $referencia, $valor);

			if ($order_info_ip) {
				$this->load->model('checkout/order');

				$order_info = $this->model_checkout_order->getOrder($order_info_ip["order_id"]);

				$this->model_checkout_order->addOrderHistory($order_info["order_id"], $this->config->get('multibanco_order_status_complete_id'), date("d-m-Y H:m:s"), true);

				$this->model_payment_multibanco->setIfthenpayDataStatus($order_info_ip["multibanco_id"]);
				
			} else {

				$order_info_ip = $this->model_payment_multibanco->getOrderIdByIfthenpayData($entidade, $referencia, number_format($valor));

				if($order_info_ip) {

					$this->load->model('checkout/order');

					$order_info = $this->model_checkout_order->getOrder($order_info_ip["order_id"]);

					$this->model_checkout_order->addOrderHistory($order_info["order_id"], $this->config->get('multibanco_order_status_complete_id'), date("d-m-Y H:m:s"), true);

					$this->model_payment_multibanco->setIfthenpayDataStatus($order_info_ip["multibanco_id"]);
				} else {
					echo "Encomenda n??o encontrada.";
				}
			}

			echo "Ok";
			return;
		}
		echo "NOT_OK";
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'multibanco') {
			$json = array();
            $comment = '';
			$this->load->model('checkout/order');
			$this->load->model('payment/multibanco');

			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

			$entidade = $this->config->get('multibanco_entidade');
			$referencia = $this->GenerateMbRef($this->config->get('multibanco_entidade'),$this->config->get('multibanco_subentidade'),$this->session->data['order_id'], $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));
			$valor = number_format($this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false), 2);

			$comment = '<div><table style="width: auto;min-width: 280px;max-width: 320px;padding: 5px;font-size: 11px;color: #374953;border: 1px solid #dddddd; margin-top: 10px;"><tbody><tr><td style="padding: 5px;" colspan="2"><div align="left"><img src="https://ifthenpay.com/mb.png" alt="mbway"></div></td></tr><tr><td align="left" style="padding:10px; font-weight:bold; text-align:left">Entidade:</td><td align="left" style=" padding:10px; text-align:left">' . $entidade . '</td></tr><tr><td align="left" style="padding:10px; font-weight:bold; text-align:left">Refer??ncia:</td><td align="left" style=" padding:10px; text-align:left">' . $referencia . '</td></tr><tr><td align="left" style=" padding:10px; padding-top:10px; font-weight:bold; text-align:left">Encomenda:</td><td align="left" style=" padding:10px; padding-top:10px; text-align:left">#' . $this->session->data['order_id'] . '</td></tr><tr><td align="left" style="padding:10px; padding-bottom:15px; padding-top:10px; font-weight:bold; text-align:left">Valor:</td><td style="padding:10px; padding-bottom:15px; padding-top:10px; text-align:left">' . number_format($valor, 2) . ' EUR</td></tr><tr><td style="font-size: x-small; padding:0; border: 0px; text-align:center;" colspan="2">Por favor proceda ao pagamento da sua encomenda num terminal multibanco ou homebanking. Processado por <a href="https://www.ifthenpay.com" target="_blanck">Ifthenpay</a></td></tr></tbody></table></div>';

			$teste = $this->url->link('common/home');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('multibanco_order_status_id'), $comment, true);


			$this->model_payment_multibanco->setIfthenpayData($order_info['order_id'], $entidade, $referencia, $valor);

		}
		$this->session->data['payment_method']['comment'] = $comment;

        $json['redirect'] = $this->url->link('checkout/successmbway');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
	}

	//IN??CIO TRATAMENTO DEFINI????ES REGIONAIS
	function format_number($number)
	{
		$verifySepDecimal = number_format(99,2);

		$valorTmp = $number;

		$sepDecimal = substr($verifySepDecimal, 2, 1);

		$hasSepDecimal = True;

		$i=(strlen($valorTmp)-1);

		for($i;$i!=0;$i-=1)
		{
			if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==","){
				$hasSepDecimal = True;
				$valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
				break;
			}
		}

		if($hasSepDecimal!=True){
			$valorTmp=number_format($valorTmp,2);

			$i=(strlen($valorTmp)-1);

			for($i;$i!=1;$i--)
			{
				if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==","){
					$hasSepDecimal = True;
					$valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
					break;
				}
			}
		}

		for($i=1;$i!=(strlen($valorTmp)-1);$i++)
		{
			if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)=="," || substr($valorTmp,$i,1)==" "){
				$valorTmp = trim(substr($valorTmp,0,$i)).trim(substr($valorTmp,1+$i));
				break;
			}
		}

		if (strlen(strstr($valorTmp,'@'))>0){
			$valorTmp = trim(substr($valorTmp,0,strpos($valorTmp,'@'))).trim($sepDecimal).trim(substr($valorTmp,strpos($valorTmp,'@')+1));
		}

		return $valorTmp;
	}
	//FIM TRATAMENTO DEFINI????ES REGIONAIS


	//INICIO REF MULTIBANCO

	function GenerateMbRef($ent_id, $subent_id, $order_id, $order_value)
	{
		$chk_val=0;

		$order_id ="0000".$order_id;

		$order_value =  $this->format_number($order_value);

		//Apenas sao considerados os 4 caracteres mais a direita do order_id
		$order_id = substr($order_id, (strlen($order_id) - 4), strlen($order_id));


		if ($order_value < 1){
			return "Lamentamos mas ?? imposs??vel gerar uma refer??ncia MB para valores inferiores a 1 Euro";
			return;
		}
		if ($order_value >= 1000000){
			return "<b>AVISO:</b> Pagamento fraccionado por exceder o valor limite para pagamentos no sistema Multibanco<br>";
		}
		while ($order_value >= 1000000){
			GenerateMbRef($order_id++, 999999.99);
			$order_value -= 999999.99;
		}


		//C??lculo dos check digits


		$chk_str = sprintf('%05u%03u%04u%08u', $ent_id, $subent_id, $order_id, round($order_value*100));

		$chk_array = array(3, 30, 9, 90, 27, 76, 81, 34, 49, 5, 50, 15, 53, 45, 62, 38, 89, 17, 73, 51);

		for ($i = 0; $i < 20; $i++)
		{
			$chk_int = substr($chk_str, 19-$i, 1);
			$chk_val += ($chk_int%10)*$chk_array[$i];
		}

		$chk_val %= 97;

		$chk_digits = sprintf('%02u', 98-$chk_val);

		return $subent_id." ".substr($chk_str, 8, 3)." ".substr($chk_str, 11, 1).$chk_digits;

	}
}
?>

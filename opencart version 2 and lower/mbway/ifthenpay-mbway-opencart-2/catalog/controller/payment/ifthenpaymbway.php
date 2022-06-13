<?php

class ControllerPaymentIfthenpayMbway extends Controller {

    public function index() {

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
          $minimo = '1000';

        $data['button_confirm'] = $this->language->get('button_confirm');

        //$data['continue'] = $this->url->link('checkout/success');
        $data['continue'] = $this->url->link('checkout/successmbway');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ifthenpaymbway.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/ifthenpaymbway.tpl', $data);
		} else {
			return $this->load->view('/payment/ifthenpaymbway.tpl', $data);
		}
    }

    public function confirm() {

        if ($this->session->data['payment_method']['code'] == 'ifthenpaymbway') {
            $json = array();
            $comment = '';
    
            $this->load->model('checkout/order');
            $telemovel = $this->request->get['telemovel'];
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $mbway_key = $this->config->get('ifthenpaymbway_mbwkey');


            $result = $this->callIfthenpayMbWayAPI($mbway_key, $this->session->data['order_id'], $this->config->get('config_name'), $telemovel, $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));
    
            if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                $url = $this->config->get('config_ssl');
            } else {
                $url = $this->config->get('config_url');
            }
    
            if ($result->Estado == '000') {
                $comment = '<div><table style="width: auto;min-width: 280px;max-width: 320px;padding: 5px;font-size: 11px;color: #374953;border: 1px solid #dddddd; margin-top: 10px;"><tbody><tr><td style="padding: 5px;" colspan="2"><div align="left"><img src="https://ifthenpay.com/img/mbway.png" alt="mbway"></div></td></tr><tr><td align="left" style="padding:10px; font-weight:bold; text-align:left">Telem&oacute;vel:</td><td align="left" style=" padding:10px; text-align:left">' . $telemovel . '</td></tr><tr><td align="left" style=" padding:10px; padding-top:10px; font-weight:bold; text-align:left">Encomenda:</td><td align="left" style=" padding:10px; padding-top:10px; text-align:left">#' . $this->session->data['order_id'] . '</td></tr><tr><td align="left" style="padding:10px; padding-bottom:15px; padding-top:10px; font-weight:bold; text-align:left">Valor:</td><td style="padding:10px; padding-bottom:15px; padding-top:10px; text-align:left">' . number_format($result->Valor, 2) . ' EUR</td></tr><tr><td style="font-size: x-small; padding:0; border: 0px; text-align:center;" colspan="2">Por favor verifique na App MBWAY e proceda ao pagamento da sua encomenda. <br>Processado por <a href="https://www.ifthenpay.com" target="_blanck">Ifthenpay</a></td></tr></tbody></table></div>';
            } else {                $comment = 'Ocorreu um erro: ' . $result->MsgDescricao . '. <br/>Não foi possível concluir o pagamento.';
            }
    
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('ifthenpaymbway_order_status_id'), $comment, true);
            
        }

        $this->session->data['payment_method']['comment'] = $comment;

        $json['redirect'] = $this->url->link('checkout/successmbway');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    function callIfthenpayMbWayAPI($mbw_key, $order_id, $store_name, $telemovel, $order_value)
	{
		$mbway_url_api = 'https://mbway.ifthenpay.com/IfthenPayMBW.asmx/SetPedidoJSON';
		
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $mbway_url_api . '?MbWayKey=' . $mbw_key . '&canal=03&referencia=' . $order_id . '&valor=' . $order_value . '&nrtlm=' . $telemovel . '&email=&descricao=' . urlencode('Encomenda : #' . $order_id . ' Loja: ' . $store_name), CURLOPT_USERAGENT => 'Ifthenpay Opencart Client'));
		
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		
		//Close request to clear up some resources
		curl_close($curl);

		return json_decode($resp);
    }

    //callback
	public function callback(){
		//chave=[CHAVE_ANTI_PHISHING]&referencia=[REFERENCIA]&idpedido=[ID_TRANSACAO]&valor=[VALOR]&estado=[ESTADO]
		$chave_ap_int = $this->config->get('ifthenpaymbway_ap');
		$chave_ap_ext = $this->request->get['chave'];
		$order_id = $this->request->get['referencia'];
		$valor = $this->request->get['valor'];
		$estado = $this->request->get['estado'];

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);

		if($chave_ap_int==$chave_ap_ext && $order_info['payment_code'] == 'ifthenpaymbway')
		{
            $valor_pago = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
			if ($valor == $valor_pago)
			{
				$this->model_checkout_order->addOrderHistory($order_info["order_id"], $this->config->get('ifthenpaymbway_order_status_complete_id'), date("d-m-Y H:m:s"), true);
				echo "Encomenda PAGA";
				http_response_code(200);
			}
			else
			{
				echo "Valor inválido";
				http_response_code(200);
			}
			exit();
		}
		else
		{
			echo "Chave inválida";
			http_response_code(200);
			exit();
		}
    }
    
}

?>

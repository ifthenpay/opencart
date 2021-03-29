<?php
class ControllerPaymentMultibanco extends Controller {
	protected function index() {
	
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
	
		$this->data['entry_entidade']=$this->config->get('multibanco_entidade');
		$this->data['entry_referencia'] = $this->GenerateMbRef($this->config->get('multibanco_entidade'),$this->config->get('multibanco_subentidade'),$this->session->data['order_id'], $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));
		$this->data['entry_valor'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		
		
    	$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['continue'] = $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/multibanco.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/multibanco.tpl';
		} else {
			$this->template = 'default/template/payment/multibanco.tpl';
		}	
		
		$this->render();
	}
	
	public function confirm() {
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		/*$comment  = 
		
		'<p><b>Entidade:</b> '.$this->config->get('multibanco_entidade').'</p>             
		<p><b>Refer&ecirc;ncia:</b> '.$this->GenerateMbRef($this->config->get('multibanco_entidade'),$this->config->get('multibanco_subentidade'),$this->session->data['order_id'], $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE)).'</p>
		<p><b>Valor:</b> '.$this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], TRUE).'</p>';
		
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('multibanco_order_status_id'), $comment);*/
		
		$comment  = '<div style=" border: 3px solid; margin: 10px; width: 170px; padding: 10px; ">';
		$comment .= 'Entidade: <b>' . $this->config->get('multibanco_entidade'). '</b><br /><br />';           
		$comment .= 'Refer&ecirc;ncia: <b>' . $this->GenerateMbRef($this->config->get('multibanco_entidade'),$this->config->get('multibanco_subentidade'),$this->session->data['order_id'], $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE)). '</b><br /><br />'; 
		$comment .= 'Valor: <b>' . $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], TRUE). '</b><br />'; 
		$comment .= '</div>';
		
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('multibanco_order_status_id'), $comment, true);
	}
	
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
	//FIM TRATAMENTO DEFINIÇÕES REGIONAIS


	//INICIO REF MULTIBANCO

 function GenerateMbRef($ent_id, $subent_id, $order_id, $order_value)
{
$chk_val=0;

		$order_id ="0000".$order_id;

		$order_value =  $this->format_number($order_value);

		//Apenas sao considerados os 4 caracteres mais a direita do order_id
		$order_id = substr($order_id, (strlen($order_id) - 4), strlen($order_id));


	if ($order_value < 1){
                 return "Lamentamos mas é impossível gerar uma referência MB para valores inferiores a 1 Euro";
                 return;
           }
           if ($order_value >= 1000000){
                 return "<b>AVISO:</b> Pagamento fraccionado por exceder o valor limite para pagamentos no sistema Multibanco<br>";
           }
           while ($order_value >= 1000000){
                 GenerateMbRef($order_id++, 999999.99);
                 $order_value -= 999999.99;
           }
                              
           
        //cálculo dos check digits
		
		   
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
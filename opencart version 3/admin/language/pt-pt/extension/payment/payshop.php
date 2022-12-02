<?php

require_once DIR_SYSTEM . 'library/ifthenpay/vendor/autoload.php';

use Ifthenpay\Config\IfthenpayContainer;
use Ifthenpay\Utility\Mix;

$ifthenpayContainer = new IfthenpayContainer();
$mix = $ifthenpayContainer->getIoc()->make(Mix::class);

// Heading
$_['heading_title'] = 'Payshop';
$_['heading_title_payshop'] = 'Payshop Configurações';

// Text
$_['text_extension'] = 'Extensions';
$_['text_payment'] = 'Pagamento';
$_['text_success'] = 'Sucesso: Configuração do módulo Payshop alterada!';
$_['text_payshop'] = '<a href="https:www.ifthenpay.com" target="_blank"><img src="view/image/payment/ifthenpay/payshop.png" class="ccardLogo" alt="Payshop Logo" title="Payshop" style="border: 1px solid #EEEEEE; width: 143px; height: 38px;" /><br /></a>';
$_['acess_user_documentation'] = 'Aceder ao Manual de Utilizador.';
$_['create_account_now'] = 'Crie uma conta agora!';
$_['text_home'] = 'Home';
$_['text_all_zones'] = 'Todas as regiões';
$_['entry_order_status_canceled'] = 'Estado da Encomenda Cancelado:';

//Entry
$_['entry_backoffice_key'] = 'Chave de acesso ao backoffice';
$_['help_backoffice_key'] = 'Chave de backoffice que é enviada para o seu email após a criação do contrato';
$_['help_place_holder_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['switch_enable'] = 'Ativar';
$_['switch_disable'] = 'Desativar';
$_['add_new_accounts'] = 'Adicionou uma nova conta ao seu contrato?';
$_['add_new_accounts_explain'] = 'Para escolher um conta nova prima o botão de reset, irá limpar as opções atuais deste método de pagamento, e será pedido que insira uma backoffice key associada ao seu contrato.';
$_['reset_accounts'] = 'Reset Contas';
$_['sandbox_help'] = 'Ative o modo sandbox, para poder testar o módulo sem ativar o callback.';
$_['sandbox_mode'] = 'Modo Sandbox';
$_['dontHaveAccount_payshop'] = 'Não tem conta Payshop?';
$_['requestAccount_payshop'] = 'Solicitar criação de conta Payshop';
$_['newUpdateAvailable'] = 'Nova atualização disponível!';
$_['moduleUpToDate'] = 'O módulo está atualizado!';
$_['downloadUpdateModule'] = 'Download Update Módulo';
$_['acess_user_documentation_link'] = 'https://www.ifthenpay.com/downloads/opencart/opencart_user_guide_pt.pdf';
$_['entry_minimum_value'] = 'Valor Mínimo da Encomenda';
$_['entry_maximum_value'] = 'Valor Máximo da Encomenda';
$_['resendPaymentData']	= 'Envie um email com os dados da encomenda e dados de pagamento';
$_['entry_payshop_transaction_id']	= 'ID de Transação Payshop';
$_['entry_reference']	= 'Referência';
$_['entry_amount']	= 'Valor';
$_['msg_callback_test_empty_fields']	= 'Preencha todos os campos';
$_['entry_test_callback']	= 'Testar Callback';
$_['btn_test']	= 'Testar';


// Entry
$_['activate_callback'] = 'Ativar Callback';
$_['text_enabled'] = 'Ativo';
$_['text_disabled'] = 'Desativado';
$_['entry_order_status'] = 'Estado da Encomenda:';
$_['entry_order_status_complete'] = 'Estado da Encomenda Pago:';
$_['entry_geo_zone'] = 'Geo Zone:';
$_['entry_status'] = 'Estados:';
$_['entry_sort_order'] = 'Ordem do Método de Pagamento:';
$_['entry_payshop_payshopKey'] = 'Payshop Key';
$_['entry_payshop_validade'] = 'Validade';
$_['payshop_validade_helper'] = 'Escolha o número de dias, deixe vazio se não pretender validade.';
$_['choose_key'] = 'Escolha a chave';
$_['entry_antiPhishingKey'] = 'Chave Anti-Phishing';
$_['entry_urlCallback'] = 'Url de Callback';
$_['callbackIsActivated'] = 'Callback ativado';
$_['callbackNotActivated'] = 'Callback não ativado';
$_['sandboxActivated'] = 'Modo Sandbox activo';
$_['show_paymentMethod_logo'] = 'Mostrar o Logotipo do Método de Pagamento no Checkout';
$_['dontHaveAccount_payshop'] = 'Não tem conta Payshop?';
$_['requestAccount_payshop'] = 'Solicitar criação de conta Payshop';
$_['request_new_account_success'] = 'Email a solicitar nova conta enviado com sucesso.';
$_['request_new_account_error'] = 'Erro ao enviar email a solicitar nova conta.';
$_['activate_cancelPayshopOrder'] = 'Cancelar Encomenda Multibanco';
$_['payshopOrderCancel_help'] = 'Cancele a encomenda Multibanco após a referência expirar.';


$_['label_cron_url'] = 'URL do Cron';
$_['btn_copy'] = 'Copiar';

$_['text_cron_documentation'] = 'Cron Job\'s são tarefas agendadas e executadas periodicamente. Para configurar o seu servidor, pode ler a <a href="http://docs.opencart.com/extension/cron/" target="_blank" class="alert-link"> documentação do opencart </a>.';

$_['head_cancel_cron'] = '(Cron Job) Cancelar encomendas com Payshop';
$_['text_cancel_cron_desc'] = 'Pode definir um cron job para alterar o estado da encomenda para "Cancelada", se a encomenda não for paga dentro da validade. Necessita de atribuir Validade funcionar.';
$_['text_cancel_cron_schedule'] = 'Temporize o cron job para ser executado a cada 1 minuto.';

$_['head_check_cron'] = '(Cron Job) Verificar o estado de encomendas com Payshop';
$_['text_check_cron_desc'] = 'Se não for possivel ativar o callback, pode definir o cron job abaixo para consultar o estado do pagamento.';
$_['text_check_cron_schedule'] = 'Temporize o cron job para ser executado a cada dia às 1h:00m.';


// Error
$_['error_permission'] = 'Aviso: Não tem permissões para modificar Payshop!';
$_['error_backofficeKey_required'] = 'Chave de acesso ao backoffice é obrigatória!';
$_['error_backofficeKey_already_reset'] = 'Chave de acesso ao backoffice já se encontra limpa!';
$_['error_backofficeKey_error'] = 'Erro a salvar a chave de acesso ao backoffice!';
$_['reset_account_success'] = 'Conta Ifthenpay reinicializada com sucesso!';
$_['reset_account_error'] = 'Erro a reinicializar conta Ifthenpay!';
$_['error_invalid_max_number'] = 'Aviso: Valor de encomenda máximo inválido!';
$_['error_invalid_min_number'] = 'Aviso: Valor de encomenda mínimo inválido!';
$_['error_incompatible_min_max'] = 'Aviso: Valor de encomenda mínimo e máximo não são compativeis!';
$_['error_key_required'] = 'Aviso: Chave Payshop é obrigatória!';
$_['error_invalid_expiration'] = 'Aviso: Validade inválida!';

?>
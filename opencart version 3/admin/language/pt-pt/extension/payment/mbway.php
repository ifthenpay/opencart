<?php

// Heading
$_['heading_title'] = 'MB WAY';
$_['heading_title_multibanco'] = 'Configurações MB WAY';

// Text
$_['text_extension'] = 'Extensions';
$_['text_payment'] = 'Pagamento';
$_['text_success'] = 'Sucesso: Configuração do módulo MB WAY alterada!';
$_['text_mbway'] = '<a href="https:www.ifthenpay.com" target="_blank"><img src="view/image/payment/ifthenpay/mbway.png" class="ccardLogo" alt="MB WAY Logo" title="MB WAY" style="border: 1px solid #EEEEEE; width: 82px; height: 38px;" /><br /></a>';
$_['acess_user_documentation'] = 'Aceder ao Manual de Utilizador.';
$_['create_account_now'] = 'Crie uma conta agora!';
$_['text_home'] = 'Home';
$_['text_all_zones'] = 'Todas as regiões';

//Entry
$_['entry_backoffice_key'] = 'Chave de acesso ao backoffice';
$_['help_backoffice_key'] = 'Chave de backoffice que é enviada para o seu email após a criação do contrato';
$_['help_place_holder_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['add_new_accounts'] = 'Adicionou uma nova conta ao seu contrato?';
$_['add_new_accounts_explain'] = 'Para escolher um conta nova prima o botão de reset, irá limpar as opções atuais deste método de pagamento, e será pedido que insira uma backoffice key associada ao seu contrato.';
$_['reset_accounts'] = 'Reset Contas';
$_['sandbox_help'] = 'Ative o modo sandbox, para poder testar o módulo sem ativar o callback.';
$_['sandbox_mode'] = 'Modo Sandbox';
$_['dontHaveAccount_mbway'] = 'Não tem conta MB WAY?';
$_['requestAccount_mbway'] = 'Solicitar criação de conta MB WAY';
$_['newUpdateAvailable'] = 'Nova atualização disponível!';
$_['moduleUpToDate'] = 'O módulo está atualizado!';
$_['downloadUpdateModule'] = 'Download Update Módulo';
$_['acess_user_documentation_link'] = 'https://www.ifthenpay.com/downloads/opencart/opencart_user_guide_pt.pdf';
$_['entry_minimum_value'] = 'Valor Mínimo da Encomenda';
$_['entry_maximum_value'] = 'Valor Máximo da Encomenda';
$_['error_payment_mbway_input_required'] = 'Telemóvel MB WAY é obrigatório!';
$_['error_payment_mbway_input_invalid'] = 'Telemóvel MB WAY é inválido!';
$_['mbwayPhoneNumber'] = 'Telemóvel MB WAY';
$_['adminResendMbwayNotification'] = 'Resend MB WAY notification';

$_['entry_mbway_transaction_id']	= 'ID de Transação MB WAY';
$_['entry_amount']	= 'Valor';
$_['msg_callback_test_empty_fields']	= 'Preencha todos os campos!';
$_['entry_test_callback']	= 'Testar Callback';
$_['btn_test']	= 'Testar';


// Entry
$_['activate_callback'] = 'Ativar Callback';
$_['text_enabled'] = 'Ativo';
$_['text_disabled'] = 'Desativado';
$_['switch_enable'] = 'Ativar';
$_['switch_disable'] = 'Desativar';
$_['entry_order_status'] = 'Estado da Encomenda:';
$_['entry_order_status_complete'] = 'Estado da Encomenda Pago:';
$_['entry_order_status_canceled'] = 'Estado da Encomenda Cancelado:';
$_['entry_geo_zone'] = 'Geo Zone:';
$_['entry_status'] = 'Estados:';
$_['entry_sort_order'] = 'Ordem do Método de Pagamento:';
$_['entry_mbway_mbwayKey'] = 'MB WAY key';
$_['choose_key'] = 'Escolha a chave';
$_['activate_cancelMbwayOrder'] = 'Cancelar Encomenda MB WAY';
$_['mbwayOrderCancel_help'] = 'Cancele a encomenda MB WAY após a notificação expirar.';
$_['entry_antiPhishingKey'] = 'Chave Anti-Phishing';
$_['entry_urlCallback'] = 'Url de Callback';
$_['callbackIsActivated'] = 'Callback ativado';
$_['callbackNotActivated'] = 'Callback não ativado';
$_['sandboxActivated'] = 'Modo Sandbox activo';
$_['show_paymentMethod_logo'] = 'Mostrar o Logotipo do Método de Pagamento no Checkout';
$_['dontHaveAccount_mbway'] = 'Não tem conta MB WAY?';
$_['requestAccount_mbway'] = 'Solicitar criação de conta MB WAY';
$_['request_new_account_success'] = 'Email a solicitar nova conta enviado com sucesso.';
$_['request_new_account_error'] = 'Erro ao enviar email a solicitar nova conta.';

// Error
$_['error_permission'] = 'Aviso: Não tem permissão para modificar o módulo MB WAY!';
$_['error_backofficeKey_required'] = 'Chave de acesso ao backoffice é obrigatória!';
$_['error_backofficeKey_already_reset'] = 'Chave de acesso ao backoffice já se encontra limpa!';
$_['error_backofficeKey_error'] = 'Erro a salvar a chave de acesso ao backoffice!';
$_['reset_account_success'] = 'Conta Ifthenpay reinicializada com sucesso!';
$_['reset_account_error'] = 'Erro a reinicializar conta Ifthenpay!';


$_['label_cron_url'] = 'URL do Cron';
$_['btn_copy'] = 'Copiar';

$_['text_cron_documentation'] = 'Cron Job\'s são tarefas agendadas e executadas periodicamente. Para configurar o seu servidor, pode ler a <a href="http://docs.opencart.com/extension/cron/" target="_blank" class="alert-link"> documentação do opencart </a>.';

$_['head_cancel_cron'] = '(Cron Job) Cancelar encomendas com MB WAY';
$_['text_cancel_cron_desc'] = 'Pode definir um cron job para alterar o estado da encomenda para "Cancelada", se a encomenda não for paga dentro de 30 minutos após a confirmação da encomenda.';
$_['text_cancel_cron_schedule'] = 'Temporize o cron job para ser executado a cada 1 minuto.';

$_['head_check_cron'] = '(Cron Job) Verificar o estado de encomendas com MB WAY';
$_['text_check_cron_desc'] = 'Se não for possivel ativar o callback, pode definir o cron job abaixo para consultar o estado do pagamento.';
$_['text_check_cron_schedule'] = 'Temporize o cron job para ser executado a cada dia às 1h:00m.';


$_['error_invalid_max_number'] = 'Aviso: Valor de encomenda máximo inválido!';
$_['error_invalid_min_number'] = 'Aviso: Valor de encomenda mínimo inválido!';
$_['error_incompatible_min_max'] = 'Aviso: Valor de encomenda mínimo e máximo não são compativeis!';
$_['error_key_required'] = 'Aviso: Chave MB WAY é obrigatória!';

?>
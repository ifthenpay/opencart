<?php

// Admin logo
$_['text_ifthenpaygateway'] = '<img src="' . HTTP_CATALOG . 'extension/ifthenpay/admin/view/image/ifthenpaygateway.png" alt="Ifthenpay - Ifthenpay Gateway" title="Ifthenpay - Gateway"/>';



// Heading
$_['heading_title'] = 'Ifthenpay Gateway';



// group labels
$_['text_general'] = 'Geral';
$_['text_callback'] = 'Callback';
$_['text_clear_configuration'] = 'Limpar Configuração';
$_['text_account'] = 'Conta';
$_['text_test_callback'] = 'Testar Callback';



// breadcrumb
$_['text_extension'] = 'Extensões';



// text
$_['text_callback_is_active'] = 'Callback ativo';
$_['text_callback_is_inactive'] = 'Callback inativo';
$_['text_btn_clear_configuration'] = 'Limpar Configuração';
$_['text_are_you_sure_clear'] = 'Tem a certeza de que deseja limpar a configuração? Isto irá apagar todos os dados de configuração da Ifthenpay Gateway.';
$_['text_added_new_account'] = 'Adicionou uma nova conta ao seu contrato?';
$_['text_to_set_different_account_press'] = 'Para definir uma conta diferente, clique o botão "Limpar Configuração". Ao fazer isso, irá limpar as configurações atuais deste método de pagamento e permitirá que insira uma nova chave de backoffice associada ao seu contrato.';
$_['text_transaction_id'] = 'ID de Transação';
$_['text_payment_status'] = 'Estado do Pagamento';
$_['text_order_total'] = 'Total Pago';
$_['text_no_credit_card_accounts_found'] = 'Não foram encontradas contas de Ifthenpay Gateway para este contrato.';
$_['text_to_request_a_credit_card_account'] = 'Para pedir uma conta de Ifthenpay Gateway para o seu contrato, clique no botão "Pedir Conta de Ifthenpay Gateway".';
$_['text_ifthenpay_team_will_request'] = 'Ao fazê-lo, irá notificar a equipa da Ifthenpay, que posteriormente lhe pedirá os documentos necessários por e-mail, para completar o processo.';
$_['text_request_ifthenpaygateway_account_btn'] = 'Pedir Conta de Ifthenpay Gateway';

$_['text_request_ifthenpaygateway_method_btn'] = 'Pedir Método de Ifthenpay Gateway';

$_['text_are_you_sure_request_account'] = 'Tem certeza de que deseja pedir uma conta de Ifthenpay Gateway?';
$_['text_are_you_sure_request_gateway_method'] = 'Tem certeza de que deseja pedir um método de Ifthenpay Gateway?';
$_['text_are_you_sure_refresh_accounts'] = 'Tem certeza de que deseja atualizar as contas de Ifthenpay Gateway?';




// Entry labels
$_['entry_backoffice_key'] = 'Chave de Backoffice';

$_['entry_status'] = 'Estado';
$_['entry_activate_callback'] = 'Ativar Callback';
$_['entry_enable_cancel_order_cronjob'] = 'Habilitar Cron job de Cancelar';
$_['entry_cancel_order_cronjob_url'] = 'Url de Cronjob';


$_['entry_key'] = 'Chave de Ifthenpay Gateway';
$_['entry_methods'] = 'Métodos de Pagamento';
$_['entry_method_selected_default'] = 'Método selecionado (por defeito)';
$_['entry_deadline'] = 'Validade';

$_['entry_title'] = 'Título';
$_['entry_pending_status'] = 'Estado Pendente';
$_['entry_paid_status'] = 'Estado Pago';
$_['entry_canceled_status'] = 'Estado Cancelado';
$_['entry_not_approved_status'] = 'Estado Não Aprovado';
$_['entry_geo_zone'] = 'Zona Geo';
$_['entry_min_value'] = 'Valor Mínimo da Encomenda';
$_['entry_max_value'] = 'Valor Máximo do Encomenda';

$_['entry_sort_order'] = 'Ordem de Exibição';
$_['entry_info_callback_url'] = 'URL de Callback';
$_['entry_info_anti_phishing_key'] = 'Chave Anti-Phishing';
$_['entry_order_id'] = 'ID de Encomenda';
$_['entry_amount'] = 'Valor';
$_['text_test_callback_btn'] = 'Testar';
$_['text_are_you_sure_test_callback'] = 'Tem certeza de que deseja testar o callback?';
$_['text_upgrade'] = 'Atualização';
$_['text_new_version_available'] = 'Nova versão disponível!';
$_['text_download_installer_btn'] = 'Descarregar instalador';
$_['text_user_manual_btn'] = 'Instruções';
$_['text_support_btn'] = 'Suporte';



// Entry placeholder
$_['entry_plh_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['entry_plh_key'] = 'Selecione uma chave';
$_['entry_plh_methods'] = 'Por favor seleciona uma chave de Ifthenpay Gateway para mostrar os métodos de pagamento disponíveis.';
$_['entry_plh_method_selected_default_none'] = 'nenhum';



$_['help_cron_url_cancel_order'] = 'Pode configurar o cron job para alterar o estado das encomendas para "Cancelado" se não for paga dentro da validade. Necessita de Validade de Ifthenpay Gateway atribuída para funcionar. Agende o trabalho cron para ser executado a cada 1 minuto.';

// Error messages
$_['error_permission'] = 'Aviso: Não tem permissão para modificar o pagamento por Cofidis!';
$_['error_backoffice_key_accounts_request'] = 'Erro: Não foi possível obter contas. Verifique se a chave de backoffice está correta.';
$_['error_backoffice_key_format'] = 'Erro: Formato de chave de backoffice inválido.';
$_['error_backoffice_key_empty'] = 'Erro: O campo Chave de Backoffice está vazio.';
$_['error_key_empty'] = 'Erro: O campo Chave está vazio.';
$_['error_min_value_format'] = 'Erro: Formato de valor mínimo inválido.';
$_['error_min_value_greater_than_zero'] = 'Erro: Valor mínimo deve ser maior que zero.';
$_['error_max_value_format'] = 'Erro: Formato de valor máximo inválido.';
$_['error_max_value_greater_than_zero'] = 'Erro: Valor mínimo deve ser maior que zero.';
$_['error_min_value_greater_than_max_value'] = 'Erro: O valor mínimo deve ser menor que o valor máximo.';
$_['error_order_id_empty'] = 'Erro: Campo ID de Encomenda está vazio.';
$_['error_callback_test'] = 'Erro: Erro ao testar o callback.';
$_['error_invalid_request'] = 'Erro: Request inválido.';

// Success messages
$_['success_admin_configuration'] = 'Sucesso: Configuração guardada com sucesso.';
$_['success_backoffice_key_saved'] = 'Sucesso: Chave de backoffice guardada com sucesso.';
$_['success_clear_configuration'] = 'Sucesso: Configuração limpa com sucesso.';
$_['success_request_account'] = 'Sucesso: Conta de Ifthenpay Gateway pedida com sucesso.';
$_['success_refresh_accounts'] = 'Sucesso: Contas de Ifthenpay Gateway atualizadas com sucesso.';
$_['success_callback_test'] = 'Successo: Callback testado com sucesso.';
$_['warning_callback_test_already_paid'] = 'Aviso: Callback testado com sucesso, mas o estado do pagamento já se encontra como pago.';
$_['success_request_gateway_method'] = 'Success: Método de Ifthenpay Gateway pedido com sucesso.';

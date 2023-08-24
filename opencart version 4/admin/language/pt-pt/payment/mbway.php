<?php



// Heading
$_['heading_title'] = 'MB WAY';


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
$_['text_are_you_sure_clear'] = 'Tem a certeza de que deseja limpar a configuração? Isto irá apagar todos os dados de configuração do cartão de crédito.';
$_['text_added_new_account'] = 'Adicionou uma nova conta ao seu contrato?';
$_['text_to_set_different_account_press'] = 'Para definir uma conta diferente, clique o botão "Limpar Configuração". Ao fazer isso, irá limpar as configurações atuais deste método de pagamento e permitirá que insira uma nova chave de backoffice associada ao seu contrato.';
$_['text_tab_refund'] = 'Reembolso';
$_['text_transaction_id'] = 'ID de Transação';
$_['text_payment_status'] = 'Estado do Pagamento';
$_['text_order_total'] = 'Total Pago';
$_['text_no_mbway_accounts_found'] = 'Não foram encontradas contas MB WAY para este contrato.';
$_['text_to_request_a_mbway_account'] = 'Para pedir uma conta MB WAY para o seu contrato, clique no botão "Pedir Conta MB WAY".';
$_['text_ifthenpay_team_will_create'] = 'Ao fazer-lo, irá notificar a equipa da Ifthenpay, que posteriormente procederá à criação de uma conta MB WAY para o seu contrato.';
$_['text_request_mbway_account_btn'] = 'Pedir Conta MB WAY';
$_['text_are_you_sure_request_account'] = 'Tem certeza de que deseja pedir uma conta MB WAY?';
$_['text_are_you_sure_refresh_accounts'] = 'Tem certeza de que deseja atualizar as contas MB WAY?';







// Entry labels
$_['entry_backoffice_key'] = 'Chave de Backoffice';

$_['entry_status'] = 'Estado';
$_['entry_activate_callback'] = 'Ativar Callback';
$_['entry_enable_cancel_order_cronjob'] = 'Habilitar Cron job de Cancelar';
$_['entry_show_countdown'] = 'Exibir Contagem Decrescente no Checkout';
$_['entry_show_refund_form'] = 'Habilitar Reembolso em Vendas/Encomendas';

$_['entry_cancel_order_cronjob_url'] = 'URL Cronjob';

$_['entry_key'] = 'Chave MB WAY';
$_['entry_deadline'] = 'Validade';

$_['entry_title'] = 'Titulo';
$_['entry_pending_status'] = 'Estado Pendente';
$_['entry_paid_status'] = 'Estado Pago';
$_['entry_canceled_status'] = 'Estado Cancelado';
$_['entry_refunded_status'] = 'Estado Reembolsado';
$_['entry_geo_zone'] = 'Zona Geo';
$_['entry_min_value'] = 'Valor Mínimo da Encomenda';
$_['entry_max_value'] = 'Valor Máximo do Encomenda';

$_['entry_sort_order'] = 'Ordem de Exibição';
$_['entry_info_callback_url'] = 'URL de Callback';
$_['entry_info_anti_phishing_key'] = 'Chave Anti-Phishing';
$_['entry_transaction_id'] = 'ID de Transação';
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



// Help texts
$_['help_min_value'] = 'Exibir este método de pagamento para encomendas com valor superior ou igual a este valor. Deixe em branco para permitir qualquer valor.';
$_['help_max_value'] = 'Exibir este método de pagamento para encomendas com valor inferior ou igual a este valor. Deixe em branco para permitir qualquer valor.';
$_['help_cron_url_cancel_order'] = 'Pode configurar o cron job para alterar o estado das encomendas para "Cancelado" se não for paga 30 minutos após a confirmação. Agende o trabalho cron para ser executado a cada 1 minuto.';



// Error messages
$_['error_permission'] = 'Aviso: Não tem permissão para modificar o pagamento por MB WAY!';
$_['error_backoffice_key_accounts_request'] = 'Erro: Não foi possível obter contas. Verifique se a chave de backoffice está correta.';
$_['error_backoffice_key_format'] = 'Erro: Formato de chave de backoffice inválido.';
$_['error_backoffice_key_empty'] = 'Erro: O campo Chave de Backoffice está vazio.';
$_['error_key_empty'] = 'Erro: O campo Chave está vazio.';
$_['error_min_value_format'] = 'Erro: Formato de valor mínimo inválido.';
$_['error_min_value_greater_than_zero'] = 'Erro: Valor mínimo deve ser maior que zero.';
$_['error_max_value_format'] = 'Erro: Formato de valor máximo inválido.';
$_['error_max_value_greater_than_zero'] = 'Erro: Valor mínimo deve ser maior que zero.';
$_['error_min_value_greater_than_max_value'] = 'Erro: O valor mínimo deve ser menor que o valor máximo.';
$_['error_callback_activation'] = 'Erro: Não foi possível ativar o callback.';
$_['error_transaction_id_empty'] = 'Erro: O ID de Transação está vazio.';
$_['error_amount_empty'] = 'Erro: O Valor está vazio.';
$_['error_amount_invalid'] = 'Erro: Formato de valor inválido.';
$_['error_callback_test'] = 'Erro: Não foi possível testar o callback.';


// Success messages
$_['success_admin_configuration'] = 'Sucesso: Configuração guardada com sucesso.';
$_['success_backoffice_key_saved'] = 'Sucesso: Chave de backoffice guardada com sucesso.';
$_['success_clear_configuration'] = 'Sucesso: Configuração limpa com sucesso.';
$_['success_request_account'] = 'Sucesso: Pedido de conta MB WAY efetuado com sucesso.';
$_['success_refresh_accounts'] = 'Sucesso: Contas MB WAY atualizadas com sucesso.';
$_['success_callback_test'] = 'Successo: Callback testado com sucesso.';
$_['warning_callback_test_already_paid'] = 'Aviso: Callback testado com sucesso, mas o estado do pagamento já se encontra como pago.';


// admin refund
$_['text_refund_total'] = 'Total reembolsado';
$_['entry_refund_amount'] = 'Valor a reembolsar';
$_['entry_refund_description'] = 'Descrição';
$_['text_refund_amount'] = 'Valor reembolsado';
$_['text_payment_refund'] = 'Reembolso do Pagamento';
$_['text_are_you_sure_refund'] = 'Tem certeza de que deseja reembolsar o valor de ';
$_['text_operation_irreversible'] = 'Esta operação é irreversível.';
$_['text_check_email_token'] = 'Verifique o seu email e insira o token de segurança abaixo.';
$_['text_security_code'] = 'Codigo de Segurança';
$_['text_refund_sequence'] = '#';
$_['text_refund_amount_refunded'] = 'Valor reembolsado';
$_['text_refund_date'] = 'Data';
$_['text_refund_status'] = 'Estado';
$_['text_refund_history'] = 'Histórico de Reembolsos';
$_['text_refund'] = 'Reembolso';
$_['text_btn_refund_payment'] = 'Reembolso do Pagamento';
$_['text_refund_description'] = 'Descrição';


$_['comment_refunded'] = 'MB WAY: Encomenda reembolsada.';


$_['text_btn_cancel'] = 'Cancelar';
$_['text_btn_confirm'] = 'Confirmar';


$_['success_refund'] = 'Reembolso efetuado com sucesso.';


$_['error_refund_amount_required'] = 'Erro: O campo Valor a reembolsar é obrigatório.';
$_['error_refund_amount_invalid'] = 'Erro: Formato de Valor a reembolsar inválido.';
$_['error_refund_amount_exceeds_order_amount'] = 'Erro: O valor a reembolsar excede o valor total da encomenda.';
$_['error_invalid_token'] = 'Erro: O token de segurança é inválido.';
$_['error_refund'] = 'Erro: não foi possível concluir o reembolso.';
$_['error_refund_no_funds'] = 'Erro: não existem fundos suficientes para efetuar o reembolso.';


// refund email
$_['text_your_code_is'] = 'O seu Código de Segurança é: ';
$_['text_this_code_will_be_available_for'] = 'Este código estará ativo por 30 minutos. Se não o inserir na página de Reembolsos que acabou de visitar dentro desse período de tempo, pode ser necessário iniciar o processo de devolução novamente. ';
$_['text_not_recognize'] = 'Se você não reconhecer ou esperar este email, pode sempre relatar comportamento suspeito à nossa equipe de suporte.';
$_['text_do_not_respond'] = 'Por favor, não responda a este email. O endereço de envio é apenas utilizado para transmitir mensagens automáticas.';
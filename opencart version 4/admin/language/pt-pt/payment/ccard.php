<?php



// Heading
$_['heading_title'] = 'Cartão de Crédito';



// group labels
$_['text_general'] = 'Geral';
$_['text_callback'] = 'Callback';
$_['text_clear_configuration'] = 'Limpar Configuração';
$_['text_account'] = 'Conta';



// breadcrumb
$_['text_extension'] = 'Extensões';



// text
$_['text_btn_clear_configuration'] = 'Limpar Configuração';
$_['text_are_you_sure_clear'] = 'Tem a certeza de que deseja limpar a configuração? Isto irá apagar todos os dados de configuração do cartão de crédito.';
$_['text_added_new_account'] = 'Adicionou uma nova conta ao seu contrato?';
$_['text_to_set_different_account_press'] = 'Para definir uma conta diferente, clique o botão "Limpar Configuração". Ao fazer isso, irá limpar as configurações atuais deste método de pagamento e permitirá que insira uma nova chave de backoffice associada ao seu contrato.';
$_['text_tab_refund'] = 'Reembolso';
$_['text_transaction_id'] = 'ID de Transação';
$_['text_payment_status'] = 'Estado do Pagamento';
$_['text_order_total'] = 'Total Pago';
$_['text_no_credit_card_accounts_found'] = 'Não foram encontradas contas de cartão de crédito para este contrato.';
$_['text_to_request_a_credit_card_account'] = 'Para pedir uma conta de cartão de crédito para o seu contrato, clique no botão "Pedir Conta de Cartão de Crédito".';
$_['text_ifthenpay_team_will_request'] = 'Ao fazê-lo, irá notificar a equipa da Ifthenpay, que posteriormente lhe pedirá os documentos necessários por e-mail, para completar o processo.';
$_['text_request_ccard_account_btn'] = 'Pedir Conta de Cartão de Crédito';
$_['text_are_you_sure_request_account'] = 'Tem certeza de que deseja pedir uma conta de cartão de crédito?';
$_['text_are_you_sure_refresh_accounts'] = 'Tem certeza de que deseja atualizar as contas de cartão de crédito?';
$_['text_upgrade'] = 'Atualizar';
$_['text_new_version_available'] = 'Nova versão disponível!';
$_['text_download_installer_btn'] = 'Descarregar instalador';
$_['text_user_manual_btn'] = 'Instruções';
$_['text_support_btn'] = 'Suporte';



// Entry labels
$_['entry_backoffice_key'] = 'Chave de Backoffice';

$_['entry_status'] = 'Estado';
$_['entry_show_refund_form'] = 'Habilitar Reembolso em Vendas/Encomendas';
$_['entry_cancel_order_cronjob_url'] = 'URL Cronjob';

$_['entry_key'] = 'Chave de Cartão de Crédito';
$_['entry_deadline'] = 'Validade';

$_['entry_title'] = 'Título';
$_['entry_pending_status'] = 'Estado Pendente';
$_['entry_paid_status'] = 'Estado Pago';
$_['entry_canceled_status'] = 'Estado Cancelado';
$_['entry_refunded_status'] = 'Estado Reembolsado';
$_['entry_geo_zone'] = 'Zona Geo';
$_['entry_min_value'] = 'Valor Mínimo da Encomenda';
$_['entry_max_value'] = 'Valor Máximo do Encomenda';

$_['entry_sort_order'] = 'Ordem de Exibição';



// Entry placeholder
$_['entry_plh_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['entry_plh_key'] = 'Selecione uma chave';



// Help texts
$_['help_min_value'] = 'Exibir este método de pagamento para encomendas com valor superior ou igual a este valor. Deixe em branco para permitir qualquer valor.';
$_['help_max_value'] = 'Exibir este método de pagamento para encomendas com valor inferior ou igual a este valor. Deixe em branco para permitir qualquer valor.';
$_['help_cron_url_cancel_order'] = 'Pode configurar o cron job para alterar o estado das encomendas para "Cancelado" se não for paga 30 minutos após a confirmação. Agende o trabalho cron para ser executado a cada 1 minuto.';

// Error messages
$_['error_permission'] = 'Aviso: Não tem permissão para modificar o pagamento por cartão de crédito!';
$_['error_backoffice_key_accounts_request'] = 'Erro: Não foi possível obter contas. Verifique se a chave de backoffice está correta.';
$_['error_backoffice_key_format'] = 'Erro: Formato de chave de backoffice inválido.';
$_['error_backoffice_key_empty'] = 'Erro: O campo Chave de Backoffice está vazio.';
$_['error_key_empty'] = 'Erro: O campo Chave está vazio.';
$_['error_min_value_format'] = 'Erro: Formato de valor mínimo inválido.';
$_['error_min_value_greater_than_zero'] = 'Erro: Valor mínimo deve ser maior que zero.';
$_['error_max_value_format'] = 'Erro: Formato de valor máximo inválido.';
$_['error_max_value_greater_than_zero'] = 'Erro: Valor mínimo deve ser maior que zero.';
$_['error_min_value_greater_than_max_value'] = 'Erro: O valor mínimo deve ser menor que o valor máximo.';



// Success messages
$_['success_admin_configuration'] = 'Sucesso: Configuração guardada com sucesso.';
$_['success_backoffice_key_saved'] = 'Sucesso: Chave de backoffice guardada com sucesso.';
$_['success_clear_configuration'] = 'Sucesso: Configuração limpa com sucesso.';
$_['success_request_account'] = 'Sucesso: Conta de cartão de crédito pedida com sucesso.';
$_['success_refresh_accounts'] = 'Sucesso: Contas de cartão de crédito atualizadas com sucesso.';


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

$_['comment_refunded'] = 'Cartão de Crédito: Encomenda reembolsada';


$_['text_btn_cancel'] = 'Cancelar';
$_['text_btn_confirm'] = 'Confirmar';


$_['success_refund'] = 'Reembolso efetuado com sucesso.';


$_['error_refund_amount_required'] = 'Erro: O campo Valor a reembolsar é obrigatório.';
$_['error_refund_amount_invalid'] = 'Erro: O campo Valor a reembolsar é inválido.';
$_['error_refund_amount_exceeds_order_amount'] = 'Erro: O valor a reembolsar excede o valor total da encomenda.';
$_['error_invalid_token'] = 'Erro: O token de segurança é inválido.';
$_['error_refund'] = 'Erro: não foi possível concluir o reembolso.';
$_['error_refund_no_funds'] = 'Erro: não existem fundos suficientes para efetuar o reembolso.';


// refund email
$_['text_your_code_is'] = 'O seu Código de Segurança é: ';
$_['text_this_code_will_be_available_for'] = 'Este código estará ativo por 30 minutos. Se não o inserir na página de Reembolsos que acabou de visitar dentro desse período de tempo, pode ser necessário iniciar o processo de devolução novamente. ';
$_['text_not_recognize'] = 'Se você não reconhecer ou esperar este email, pode sempre relatar comportamento suspeito à nossa equipe de suporte.';
$_['text_do_not_respond'] = 'Por favor, não responda a este email. O endereço de envio é apenas utilizado para transmitir mensagens automáticas.';
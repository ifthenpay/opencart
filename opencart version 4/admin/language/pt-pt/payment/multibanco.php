<?php



// Heading
$_['heading_title'] = 'Multibanco';



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
$_['text_are_you_sure_clear'] = 'Tem a certeza de que deseja limpar a configuração? Isto irá apagar todos os dados de configuração do Multibanco.';
$_['text_added_new_account'] = 'Adicionou uma nova conta ao seu contrato?';
$_['text_to_set_different_account_press'] = 'Para definir uma conta diferente, clique o botão "Limpar Configuração". Ao fazer isso, irá limpar as configurações atuais deste método de pagamento e permitirá que insira uma nova chave de backoffice associada ao seu contrato.';


$_['text_no_multibanco_accounts_found'] = 'Não foram encontradas contas Multibanco para este contrato.';
$_['text_to_request_a_multibanco_account'] = 'Para pedir uma conta Multibanco para o seu contrato, clique no botão "Pedir Conta Multibanco".';
$_['text_ifthenpay_team_will_create'] = 'Ao fazer-lo, irá notificar a equipa da Ifthenpay, que posteriormente procederá à criação de uma conta Multibanco para o seu contrato.';
$_['text_request_multibanco_account_btn'] = 'Pedir Conta Multibanco';
$_['text_are_you_sure_request_account'] = 'Tem certeza de que deseja pedir uma conta Multibanco?';
$_['text_are_you_sure_refresh_accounts'] = 'Tem certeza de que deseja atualizar as contas Multibanco?';

$_['text_no_dynamic_multibanco_accounts_found'] = 'Não foi encontrada conta Multibanco Dinâmica para este contrato.';
$_['text_to_request_a_dynamic_multibanco_account'] = 'Para pedir uma conta Multibanco Dinâmico para o seu contrato, clique no botão "Pedir Conta Multibanco Dinâmico".';

$_['text_ifthenpay_team_will_create'] = 'Ao fazer-lo, irá notificar a equipa da Ifthenpay, que posteriormente procederá à criação de uma conta Multibanco para o seu contrato.';
$_['text_ifthenpay_team_will_create_dynamic'] = 'Ao fazer-lo, irá notificar a equipa da Ifthenpay, que posteriormente procederá à criação de uma conta Multibanco Dinâmica para o seu contrato.';


$_['text_request_multibanco_account_btn'] = 'Pedir Conta Multibanco';
$_['text_request_dynamic_multibanco_account_btn'] = 'Pedir Conta Multibanco Dinâmica';

$_['text_are_you_sure_request_account'] = 'Tem certeza de que deseja pedir uma conta Multibanco?';
$_['text_are_you_sure_refresh_accounts'] = 'Tem certeza de que deseja atualizar as contas Multibanco?';

$_['text_upgrade'] = 'Atualização';
$_['text_new_version_available'] = 'Nova versão disponível!';
$_['text_download_installer_btn'] = 'Descarregar instalador';
$_['text_user_manual_btn'] = 'Instruções';
$_['text_support_btn'] = 'Suporte';



// Entry labels
$_['entry_backoffice_key'] = 'Chave Backoffice';

$_['entry_status'] = 'Estado';
$_['entry_activate_callback'] = 'Ativar Callback';
$_['entry_enable_cancel_order_cronjob'] = 'Habilitar Cron job de Cancelar';
$_['entry_cancel_order_cronjob_url'] = 'URL Cronjob';

$_['entry_entity'] = 'Entidade';
$_['entry_sub_entity'] = 'Sub Entidade';
$_['entry_deadline'] = 'Validade';
$_['entry_key'] = 'Key';
$_['text_no_deadline'] = 'Sem Validade';



$_['entry_title'] = 'Título';
$_['entry_pending_status'] = 'Estado Pendente';
$_['entry_paid_status'] = 'Estado Pago';
$_['entry_canceled_status'] = 'Estado Cancelado';
$_['entry_geo_zone'] = 'Zona Geo';
$_['entry_min_value'] = 'Valor Mínimo da Encomenda';
$_['entry_max_value'] = 'Valor Máximo do Encomenda';

$_['entry_sort_order'] = 'Ordem de Exibição';
$_['entry_info_callback_url'] = 'URL de Callback';
$_['entry_info_anti_phishing_key'] = 'Chave Anti-Phishing';
$_['entry_reference'] = 'Referência';
$_['entry_amount'] = 'Valor';
$_['text_test_callback_btn'] = 'Testar';
$_['text_are_you_sure_test_callback'] = 'Tem certeza de que deseja testar o callback?';
$_['text_multibanco_dynamic_references'] = 'Referências Dinâmicas de Multibanco';



// Entry placeholder
$_['entry_plh_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['entry_plh_entity'] = 'Selecione uma entidade';
$_['entry_plh_sub_entity'] = 'Selecione uma sub entidade';
$_['entry_plh_key'] = 'Selecione uma chave';



// Help texts
$_['help_min_value'] = 'Exibir este método de pagamento para encomendas com valor superior ou igual a este valor. Deixe em branco para permitir qualquer valor.';
$_['help_max_value'] = 'Exibir este método de pagamento para encomendas com valor inferior ou igual a este valor. Deixe em branco para permitir qualquer valor.';
$_['help_cron_url_cancel_order'] = 'Pode configurar o cron job para alterar o estado das encomendas para "Cancelado" se não for paga dentro da validade. Necessita de Validade de Referência Multibanco atribuída para funcionar. Agende o trabalho cron para ser executado a cada 1 minuto.';



// Error messages
$_['error_permission'] = 'Aviso: Não tem permissão para modificar o pagamento por Multibanco!';
$_['error_backoffice_key_accounts_request'] = 'Erro: Não foi possível obter contas. Verifique se a chave de backoffice está correta.';
$_['error_backoffice_key_format'] = 'Erro: Formato de chave de backoffice inválido.';
$_['error_backoffice_key_empty'] = 'Erro: O campo Chave de Backoffice está vazio.';
$_['error_entity_empty'] = 'Erro: O campo Entidade está vazio';
$_['error_sub_entity_empty'] = 'Erro: O campo Sub Entidade está vazio.';
$_['error_min_value_format'] = 'Erro: Formato de valor mínimo inválido.';
$_['error_min_value_greater_than_zero'] = 'Erro: Valor mínimo deve ser maior que zero.';
$_['error_max_value_format'] = 'Erro: Formato de valor máximo inválido.';
$_['error_max_value_greater_than_zero'] = 'Erro: Valor mínimo deve ser maior que zero.';
$_['error_min_value_greater_than_max_value'] = 'Erro: O valor mínimo deve ser menor que o valor máximo.';
$_['error_callback_activation'] = 'Erro: Não foi possível ativar o callback.';
$_['error_reference_empty'] = 'Erro: O campo Referência está vazio.';
$_['error_reference_invalid'] = 'Erro: O campo Referência é inválido.';
$_['error_amount_empty'] = 'Erro: O Valor está vazio.';
$_['error_amount_invalid'] = 'Erro: Formato de valor inválido.';
$_['error_callback_test'] = 'Erro: Não foi possível testar o callback.';


// Success messages
$_['success_admin_configuration'] = 'Sucesso: Configuração guardada com sucesso.';
$_['success_backoffice_key_saved'] = 'Sucesso: Chave de backoffice guardada com sucesso.';
$_['success_clear_configuration'] = 'Sucesso: Configuração limpa com sucesso.';
$_['success_request_account'] = 'Sucesso: Pedido de conta Multibanco efetuado com sucesso.';
$_['success_refresh_accounts'] = 'Sucesso: Contas Multibanco atualizadas com sucesso.';
$_['success_callback_test'] = 'Successo: Callback testado com sucesso.';
$_['warning_callback_test_already_paid'] = 'Aviso: Callback testado com sucesso, mas o estado do pagamento já se encontra como pago.';

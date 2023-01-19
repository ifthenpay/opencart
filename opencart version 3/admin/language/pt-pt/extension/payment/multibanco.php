<?php

// Heading
$_['heading_title'] = 'Multibanco';
$_['heading_title_multibanco'] = 'Multibanco Configurações';

// Text
$_['text_extension'] = 'Extensions';
$_['text_payment'] = 'Pagamento';
$_['text_success'] = 'Sucesso: Configuração do módulo Multibanco alterada!';
$_['text_multibanco'] = '<a href="https:www.ifthenpay.com" target="_blank"><img src="view/image/payment/ifthenpay/multibanco.png" class="ccardLogo" alt="Multibanco Logo" title="Multibanco" style="border: 1px solid #EEEEEE; width: 133px; height: 38px;" /><br /></a>';
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
$_['reset_account_success'] = 'Conta Ifthenpay reinicializada com sucesso!';
$_['reset_account_error'] = 'Erro a reinicializar conta Ifthenpay!';
$_['dontHaveAccount_multibanco'] = 'Não tem conta Multibanco?';
$_['requestAccount_multibanco'] = 'Solicitar criação de conta Multibanco';
$_['newUpdateAvailable'] = 'Nova atualização disponível!';
$_['moduleUpToDate'] = 'O módulo está atualizado!';
$_['downloadUpdateModule'] = 'Download Update Módulo';
$_['acess_user_documentation_link'] = 'https://www.ifthenpay.com/downloads/opencart/opencart_user_guide_pt.pdf';
$_['entry_minimum_value'] = 'Valor Mínimo da Encomenda';
$_['entry_maximum_value'] = 'Valor Máximo da Encomenda';
$_['resendPaymentData'] = 'Envie um email da encomenda com os dados de pagamento';
$_['entry_reference'] = 'Referência';
$_['entry_amount'] = 'Valor';
$_['msg_callback_test_empty_fields'] = 'Preencha todos os campos!';
$_['entry_test_callback'] = 'Testar Callback';
$_['btn_test'] = 'Testar';







// Error
$_['error_permission'] = 'Aviso: Não tem permissões para modificar Multibanco!';
$_['error_backofficeKey_required'] = 'Chave de acesso ao backoffice é obrigatória!';
$_['error_backofficeKey_already_reset'] = 'Chave de acesso ao backoffice já se encontra limpa!';
$_['error_backofficeKey_error'] = 'Erro a salvar a chave de acesso ao backoffice!';

$_['text_enabled'] = 'Ativo';
$_['text_disabled'] = 'Desativado';
$_['activate_callback'] = 'Ativar Callback';
$_['entry_order_status'] = 'Estado da Encomenda:';
$_['entry_order_status_complete'] = 'Estado da Encomenda Pago:';
$_['entry_geo_zone'] = 'Geo Zone:';
$_['entry_status'] = 'Estado:';
$_['entry_sort_order'] = 'Ordem do Método de Pagamento:';
$_['entry_multibanco_entidade'] = 'Entidade';
$_['entry_multibanco_SubEntidade'] = 'Sub Entidade';
$_['choose_entity'] = 'Escolha a Entidade';
$_['entry_antiPhishingKey'] = 'Chave Anti-Phishing';
$_['entry_urlCallback'] = 'Url de Callback';
$_['callbackIsActivated'] = 'Callback ativado';
$_['callbackNotActivated'] = 'Callback não ativado';
$_['sandboxActivated'] = 'Modo Sandbox activo';
$_['show_paymentMethod_logo'] = 'Mostrar o Logotipo do Método de Pagamento no Checkout';
$_['entry_multibanco_deadline'] = 'Validade Referência Multibanco';
$_['multibanco_deadline'] = 'Escolha a Validade';
$_['request_new_account_success'] = 'Email a solicitar nova conta enviado com sucesso.';
$_['request_new_account_error'] = 'Erro ao enviar email a solicitar nova conta.';
$_['dontHaveAccount_multibanco'] = 'Não tem conta Multibanco?';
$_['requestAccount_multibanco'] = 'Solicitar criação de conta Multibanco';
$_['dontHaveAccount_multibanco_dynamic'] = 'Não tem conta de Multibanco dinâmica?';
$_['requestAccount_multibanco_dynamic'] = 'Solicitar criação de conta Multibanco dinâmica';
$_['activate_cancelMultibancoOrder'] = 'Cancelar Encomenda Multibanco';
$_['multibancoOrderCancel_help'] = 'Cancele a encomenda Multibanco após a referência expirar.';
$_['payment_multibanco_deadline_help'] = 'Este campo especifica a validade, em dias, da referência gerada. Selecione 0 para que a referência expire às 23:59 do dia em que foi gerada. Selecione 1 ou mais para que a referência expire após o número de dias selecionado, a partir das 23:59 do dia em que foi gerada.';


$_['label_cron_url'] = 'URL do Cron';
$_['btn_copy'] = 'Copiar';

$_['text_cron_documentation'] = 'Cron Job\'s são tarefas agendadas e executadas periodicamente. Para configurar o seu servidor, pode ler a <a href="http://docs.opencart.com/extension/cron/" target="_blank" class="alert-link"> documentação do opencart </a>.';

$_['head_cancel_cron'] = '(Cron Job) Cancelar encomendas com Multibanco';
$_['text_cancel_cron_desc'] = 'Pode definir um cron job para alterar o estado da encomenda para "Cancelada", se a encomenda não for paga dentro da validade. Necessita de atribuir Validade da Referência Multibanco definida para funcionar.';
$_['text_cancel_cron_schedule'] = 'Temporize o cron job para ser executado a cada 1 minuto.';

$_['head_check_cron'] = '(Cron Job) Verificar o estado de encomendas com Multibanco';
$_['text_check_cron_desc'] = 'Se não for possivel ativar o callback, pode definir o cron job abaixo para consultar o estado do pagamento.';
$_['text_check_cron_schedule'] = 'Temporize o cron job para ser executado a cada dia às 1h:00m.';


$_['error_invalid_max_number'] = 'Aviso: Valor de encomenda máximo inválido!';
$_['error_invalid_min_number'] = 'Aviso: Valor de encomenda mínimo inválido!';
$_['error_incompatible_min_max'] = 'Aviso: Valor de encomenda mínimo e máximo não são compativeis!';
$_['error_entity_required'] = 'Aviso: Entidade é obrigatória!';
$_['error_sub_entity_required'] = 'Aviso: SubEntidade é obrigatória!';
$_['error_dynamic_expiration_required'] = 'Aviso: expiração da referência dinâmica obrigatória!';

?>
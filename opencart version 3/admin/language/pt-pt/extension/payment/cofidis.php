<?php

// Heading
$_['heading_title'] = 'Cofidis Pay';

// Text
$_['text_extension'] = 'Extensions';
$_['text_payment'] = 'Pagamento';
$_['text_success'] = 'Sucesso: Modificou o método de pagamento Cofidis Pay!';

$_['text_cofidis'] = '<a href="https://ifthenpay.com" target="_blank"><img src="view/image/payment/ifthenpay/cofidis.png" class="cofidisLogo" alt="cofidis pay logo" title="cofidis pay" style="border: 1px solid #EEEEEE; height: 38px;" /><br /></a>';
$_['create_account_now'] = 'Crie uma conta agora!';
$_['text_home'] = 'Home';
$_['text_all_zones'] = 'Todas as regiões';

//Entry
$_['entry_backoffice_key'] = 'Chave de acesso ao backoffice';
$_['help_backoffice_key'] = 'Chave de backoffice que é enviada para o seu email após a criação do contrato';
$_['help_place_holder_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['text_enabled'] = 'Ativo';
$_['text_disabled'] = 'Desativado';
$_['add_new_accounts'] = 'Adicionou uma nova conta ao seu contrato?';
$_['add_new_accounts_explain'] = 'Para escolher um conta nova prima o botão de reset, irá limpar as opções atuais deste método de pagamento, e será pedido que insira uma backoffice key associada ao seu contrato.';
$_['reset_accounts'] = 'Reset Contas';
$_['sandbox_help'] = 'Ative o modo sandbox, para poder testar o módulo sem ativar o callback.';
$_['sandbox_mode'] = 'Modo Sandbox';
$_['reset_account_success'] = 'Conta Ifthenpay reinicializada com sucesso!';
$_['reset_account_error'] = 'Erro a reinicializar conta Ifthenpay!';
$_['dontHaveAccount_cofidis'] = 'Não tem conta Cofidis Pay?';
$_['requestAccount_cofidis'] = 'Solicitar criação de conta Cofidis Pay';
$_['newUpdateAvailable'] = 'Nova atualização disponível!';
$_['extensionUpToDate'] = 'A extensão está atualizada!';
$_['downloadExtensionUpdate'] = 'Download Atualização da Extensão';
$_['entry_minimum_value'] = 'Valor Mínimo da Encomenda';
$_['entry_maximum_value'] = 'Valor Máximo da Encomenda';
$_['activate_cancelCofidisOrder'] = 'Cancelar Encomenda Cofidis Pay';
$_['cofidisOrderCancel_help'] = 'Cancele a encomenda Cofidis Pay após os dados de pagamento  expirarem.';
$_['entry_payment_method_title'] = 'Título do Método de Pagamento';


// Entry
$_['activate_callback'] = 'Ativar Callback';
$_['switch_enable'] = 'Ativar';
$_['switch_disable'] = 'Desativar';
$_['entry_order_status_complete'] = 'Estado da Encomenda Pago:';
$_['entry_order_status_canceled'] = 'Estado da Encomenda Cancelado:';
$_['entry_order_status_failed'] = 'Estado da Encomenda Falhou:';
$_['entry_order_status_not_approved'] = 'Estado da Encomenda Não Aprovado:';
$_['entry_geo_zone'] = 'Geo Zone:';
$_['entry_status'] = 'Estados:';
$_['entry_sort_order'] = 'Ordem do Método de Pagamento:';
$_['entry_cofidis_cofidisKey'] = 'Cofidis Key';
$_['entry_antiPhishingKey'] = 'Chave Anti-Phishing';
$_['entry_urlCallback'] = 'Url de Callback';
$_['callbackIsActivated'] = 'Callback ativado';
$_['callbackNotActivated'] = 'Callback não ativado';
$_['sandboxActivated'] = 'Modo Sandbox activo';
$_['show_paymentMethod_logo'] = 'Mostrar o Logotipo do Método de Pagamento no Checkout';
$_['request_new_account_success'] = 'Email a solicitar nova conta enviado com sucesso.';
$_['request_new_account_error'] = 'Erro ao enviar email a solicitar nova conta.';


$_['label_cron_url'] = 'URL do Cron';
$_['btn_copy'] = 'Copiar';

$_['text_cron_documentation'] = 'Cron Job\'s são tarefas agendadas e executadas periodicamente. Para configurar o seu servidor, pode ler a <a href="http://docs.opencart.com/extension/cron/" target="_blank" class="alert-link"> documentação do opencart </a>.';

$_['head_cancel_cron'] = '(Cron Job) Cancelar encomendas com Cofidis Pay';
$_['text_cancel_cron_desc'] = 'Pode definir um cron job para alterar o estado da encomenda para "Cancelada", se a encomenda não for paga dentro de 30 minutos após a confirmação da encomenda.';
$_['text_cancel_cron_schedule'] = 'Temporize o cron job para ser executado a cada 1 minuto.';


// Error
$_['error_permission'] = 'Aviso: Não tem permissão para modificar o módulo MB WAY!';
$_['error_backofficeKey_required'] = 'Chave de acesso ao backoffice é obrigatória!';
$_['error_backofficeKey_already_reset'] = 'Chave de acesso ao backoffice já se encontra limpa!';
$_['error_backofficeKey_error'] = 'Erro a salvar a chave de acesso ao backoffice!';
$_['error_invalid_max_number'] = 'Aviso: Valor de encomenda máximo inválido!';
$_['error_invalid_max_number_larger_than_ifthenpay'] = 'Aviso: Valor de encomenda máximo inválido! Deve ser menor ou igual ao valor definido no backoffice da ifthenpay.';
$_['error_invalid_min_number'] = 'Aviso: Valor de encomenda mínimo inválido!';
$_['error_invalid_min_number_less_than_ifthenpay'] = 'Aviso: Valor de encomenda mínimo inválido! Deve ser maior ou igual ao valor definido no backoffice da ifthenpay.';
$_['error_incompatible_min_max'] = 'Aviso: Valor de encomenda mínimo e máximo não são compativeis!';
$_['error_key_required'] = 'Aviso: Chave de Cofidis é obrigatória!';

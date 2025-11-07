<?php

// Heading
$_['heading_title'] = 'Payshop';

// Text
$_['text_extension'] = 'Extensiones';
$_['text_payment'] = 'Pago';
$_['text_success'] = 'Éxito: ¡Has modificado el módulo de pago Payshop!';
$_['text_payshop'] = '<a href="https://ifthenpay.com" target="_blank"><img src="view/image/payment/ifthenpay/payshop.png" alt="Payshop Logo" title="Payshop" style="border: 1px solid #EEEEEE; height: 38px;" /><br /></a>';
$_['create_account_now'] = '¡Crea una cuenta ahora!';
$_['text_home'] = 'Inicio';
$_['text_all_zones'] = 'Todas las zonas';

//Entry
$_['entry_backoffice_key'] = 'Clave de Backoffice';
$_['help_backoffice_key'] = 'Clave de Backoffice que se envía a tu correo electrónico después de crear el contrato.';
$_['help_place_holder_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['add_new_accounts'] = '¿Has añadido una nueva cuenta a tu contrato?';
$_['add_new_accounts_explain'] = 'Para configurar una cuenta diferente, presiona el botón de reinicio. Al hacerlo, se borrarán los ajustes actuales de este método de pago y podrás insertar una nueva clave de backoffice asociada a tu contrato.';
$_['reset_accounts'] = 'Reiniciar Cuentas';
$_['sandbox_help'] = 'Activa el modo sandbox para probar el módulo sin activar el callback.';
$_['sandbox_mode'] = 'Modo Sandbox';
$_['dontHaveAccount_payshop'] = '¿No tienes una cuenta de Payshop?';
$_['requestAccount_payshop'] = 'Solicitar creación de cuenta Payshop';
$_['newUpdateAvailable'] = '¡Hay una nueva actualización disponible!';
$_['extensionUpToDate'] = '¡Tu extensión está actualizada!';
$_['downloadExtensionUpdate'] = 'Descargar Módulo de Actualización';
$_['entry_minimum_value'] = 'Valor Mínimo del Pedido';
$_['help_entry_minimum_value'] = 'Mostrar este método de pago al cliente solo si el valor del pedido es igual o mayor al valor mínimo';
$_['entry_maximum_value'] = 'Valor Máximo del Pedido';
$_['help_entry_maximum_value'] = 'Mostrar este método de pago al cliente solo si el valor del pedido es igual o menor al valor máximo';
$_['resendPaymentData'] = 'Enviar correo del pedido con datos de pago';
$_['entry_payshop_transaction_id'] = 'ID de Transacción Payshop';
$_['entry_reference'] = 'Referencia';
$_['entry_amount'] = 'Valor';
$_['msg_callback_test_empty_fields'] = 'Por favor, rellena todos los campos';
$_['entry_test_callback'] = 'Probar Callback';
$_['btn_test'] = 'Probar';
$_['entry_payment_method_title'] = 'Título del Método de Pago';
$_['entry_payment_method_instruction'] = 'Instrucciones del Método de Pago';
$_['help_entry_payment_method_instruction'] = 'Texto breve mostrado antes de confirmar el pedido, se puede usar para proporcionar más instrucciones al cliente.';


// Entry
$_['activate_callback'] = 'Activar Callback';
$_['text_enabled'] = 'Habilitado';
$_['text_disabled'] = 'Deshabilitado';
$_['switch_enable'] = 'Habilitar';
$_['switch_disable'] = 'Deshabilitar';
$_['entry_order_status_pending'] = 'Estado del Pedido Pendiente:';
$_['help_entry_order_status_pending'] = 'Este estado se asigna al pedido al crearse y normalmente se establece como Pendiente.';
$_['entry_order_status_complete'] = 'Estado del Pedido Pagado:';
$_['entry_order_status_canceled'] = 'Estado del Pedido Cancelado:';
$_['entry_geo_zone'] = 'Zona Geográfica:';
$_['entry_status'] = 'Estado:';
$_['entry_sort_order'] = 'Orden de Visualización:';
$_['entry_payshop_payshopKey'] = 'Clave de Payshop';
$_['entry_payshop_validade'] = 'Fecha límite';
$_['payshop_validade_helper'] = 'Elige el número de días, deja vacío si no quieres fecha límite';
$_['entry_antiPhishingKey'] = 'Clave Anti-Phishing';
$_['entry_urlCallback'] = 'URL de Callback';
$_['callbackIsActivated'] = 'El callback está activado';
$_['callbackNotActivated'] = 'El callback no está activado';
$_['sandboxActivated'] = 'Modo Sandbox activado';
$_['show_paymentMethod_logo'] = 'Mostrar el logo del método de pago en el proceso de compra';
$_['request_new_account_success'] = 'Correo enviado con éxito solicitando una nueva cuenta.';
$_['request_new_account_error'] = 'Error al enviar el correo para solicitar una nueva cuenta.';
$_['dontHaveAccount_payshop'] = '¿No tienes una cuenta de Payshop?';
$_['requestAccount_payshop'] = 'Solicitar creación de cuenta Payshop';
$_['activate_cancelPayshopOrder'] = 'Cancelar Pedido Payshop';
$_['payshopOrderCancel_help'] = 'Cancelar el pedido de Payshop después de que los datos de pago expiren.';

// Error
$_['error_permission'] = 'Advertencia: ¡No tienes permiso para modificar Payshop!';
$_['error_backofficeKey_required'] = '¡La clave de Backoffice es obligatoria!';
$_['error_backofficeKey_already_reset'] = '¡La clave de Backoffice ya está en blanco!';
$_['error_backofficeKey_error'] = '¡Error al guardar la clave de Backoffice!';
$_['reset_account_success'] = '¡Cuenta de Ifthenpay reiniciada con éxito!';
$_['reset_account_error'] = '¡Error al reiniciar las cuentas de Ifthenpay!';


$_['label_cron_url'] = 'URL del Cron';
$_['btn_copy'] = 'Copiar';

$_['text_cron_documentation'] = 'Los trabajos Cron son tareas programadas que se ejecutan periódicamente. Para configurar tu servidor con un trabajo cron, puedes leer la <a href="http://docs.opencart.com/extension/cron/" target="_blank" class="alert-link">documentación de Opencart</a>.';

$_['head_cancel_cron'] = '(Trabajo Cron) Cancelar Pedido Payshop';
$_['text_cancel_cron_desc'] = 'Puedes configurar este cron para cambiar el estado de los pedidos a "Cancelado" si no se paga antes de la fecha límite. Requiere que la fecha límite esté configurada para funcionar.';
$_['text_cancel_cron_schedule'] = 'Programa el trabajo cron para que se ejecute cada 1 minuto.';


$_['error_invalid_max_number'] = 'Advertencia: ¡Valor Máximo del Pedido no es un número válido!';
$_['error_invalid_min_number'] = 'Advertencia: ¡Valor Mínimo del Pedido no es un número válido!';
$_['error_incompatible_min_max'] = 'Advertencia: ¡Los Valores Mínimo y Máximo del Pedido no son compatibles!';
$_['error_key_required'] = 'Advertencia: ¡Clave de Payshop obligatoria!';
$_['error_invalid_expiration'] = 'Advertencia: ¡Fecha límite inválida!';

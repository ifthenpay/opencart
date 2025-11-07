<?php

// Heading
$_['heading_title'] = 'Cofidis Pay';

// Text
$_['text_extension'] = 'Extensiones';
$_['text_payment'] = 'Pago';
$_['text_success'] = 'Éxito: ¡Has modificado el módulo de pago Cofidis Pay!';


$_['text_cofidis'] = '<a href="https://ifthenpay.com" target="_blank"><img src="view/image/payment/ifthenpay/cofidis.png" class="cofidisLogo" alt="Cofidis Pay Logo" title="Cofidis Pay" style="border: 1px solid #EEEEEE; height: 38px;" /><br /></a>';
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
$_['dontHaveAccount_cofidis'] = '¿No tienes una cuenta de Cofidis Pay?';
$_['requestAccount_cofidis'] = 'Solicitar creación de cuenta Cofidis Pay';
$_['activate_cancelCofidisOrder'] = 'Cancelar Pedido Cofidis Pay';
$_['cofidisOrderCancel_help'] = 'Cancelar el pedido de Cofidis Pay después de que los datos de pago expiren.';
$_['newUpdateAvailable'] = '¡Hay una nueva actualización disponible!';
$_['extensionUpToDate'] = '¡Tu extensión está actualizada!';
$_['downloadExtensionUpdate'] = 'Descargar Módulo de Actualización';
$_['entry_minimum_value'] = 'Valor Mínimo del Pedido';
$_['help_entry_minimum_value'] = 'Mostrar este método de pago al cliente solo si el valor del pedido es igual o mayor al valor mínimo';
$_['entry_maximum_value'] = 'Valor Máximo del Pedido';
$_['help_entry_maximum_value'] = 'Mostrar este método de pago al cliente solo si el valor del pedido es igual o menor al valor máximo';
$_['request_new_account_success'] = 'Correo enviado con éxito solicitando una nueva cuenta.';
$_['request_new_account_error'] = 'Error al enviar el correo para solicitar una nueva cuenta.';
$_['entry_cofidis_transaction_id'] = 'ID de transacción de Cofidis Pay';
$_['entry_amount'] = 'Valor';
$_['msg_callback_test_empty_fields'] = 'Por favor, rellena todos los campos';
$_['entry_test_callback'] = 'Probar Callback';
$_['btn_test'] = 'Probar';
$_['entry_payment_method_title'] = 'Título del Método de Pago';
$_['entry_payment_method_instruction'] = 'Instrucciones del Método de Pago';
$_['help_entry_payment_method_instruction'] = 'Texto breve mostrado antes de confirmar el pedido, se puede usar para proporcionar más instrucciones al cliente.';

$_['label_cron_url'] = 'URL del Cron';
$_['btn_copy'] = 'Copiar';

$_['text_cron_documentation'] = 'Los trabajos Cron son tareas programadas que se ejecutan periódicamente. Para configurar tu servidor con un trabajo cron, puedes leer la <a href="http://docs.opencart.com/extension/cron/" target="_blank" class="alert-link">documentación de Opencart</a>.';

$_['head_cancel_cron'] = '(Trabajo Cron) Cancelar Pedido Cofidis Pay';
$_['text_cancel_cron_desc'] = 'Puedes configurar este trabajo cron para cambiar el estado del pedido a "Cancelado", si no se ha pagado en los 30 minutos posteriores a la confirmación del pedido.';
$_['text_cancel_cron_schedule'] = 'Programa el trabajo cron para que se ejecute cada 1 minuto.';


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
$_['entry_order_status_failed'] = 'Estado del Pedido Fallido:';
$_['entry_order_status_not_approved'] = 'Estado del Pedido No Aprobado:';
$_['entry_geo_zone'] = 'Zona Geográfica:';
$_['entry_status'] = 'Estado:';
$_['entry_sort_order'] = 'Orden de Visualización:';
$_['entry_cofidis_cofidisKey'] = 'Clave de Cofidis Pay';
$_['entry_antiPhishingKey'] = 'Clave Anti-Phishing';
$_['entry_urlCallback'] = 'URL de Callback';
$_['callbackIsActivated'] = 'El callback está activado';
$_['callbackNotActivated'] = 'El callback no está activado';
$_['sandboxActivated'] = 'Modo Sandbox activado';
$_['show_paymentMethod_logo'] = 'Mostrar el logo del método de pago en el proceso de compra';

// Error
$_['error_permission'] = 'Advertencia: ¡No tienes permiso para modificar Cofidis Pay!';
$_['error_backofficeKey_required'] = '¡La clave de Backoffice es obligatoria!';
$_['error_backofficeKey_already_reset'] = '¡La clave de Backoffice ya está en blanco!';

$_['error_backofficeKey_error'] = '¡Error al guardar la clave de Backoffice!';
$_['reset_account_success'] = '¡Cuenta de Ifthenpay reiniciada con éxito!';
$_['reset_account_error'] = '¡Error al reiniciar las cuentas de Ifthenpay!';
$_['error_invalid_max_number'] = 'Advertencia: ¡Valor Máximo del Pedido no es un número válido!';
$_['error_invalid_max_number_larger_than_ifthenpay'] = 'Advertencia: ¡Valor Máximo del Pedido no válido! Debe ser igual o menor que el valor definido en el backoffice de Ifthenpay.';
$_['error_invalid_min_number'] = 'Advertencia: ¡Valor Mínimo del Pedido no es un número válido!';
$_['error_invalid_min_number_less_than_ifthenpay'] = 'Advertencia: ¡Valor Mínimo del Pedido no válido! Debe ser igual o mayor que el valor definido en el backoffice de Ifthenpay.';
$_['error_incompatible_min_max'] = 'Advertencia: ¡Los Valores Mínimo y Máximo del Pedido no son compatibles!';
$_['error_key_required'] = 'Advertencia: ¡Clave de Cofidis Pay obligatoria!';

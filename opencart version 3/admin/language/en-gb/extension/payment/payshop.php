<?php

// Heading
$_['heading_title'] = 'Payshop';
$_['heading_title_payshop'] = 'Payshop Configuration';

// Text
$_['text_extension'] = 'Extensions';
$_['text_payment'] = 'Payment';
$_['text_success'] = 'Success: You have modified Payshop payment module!';
$_['text_payshop'] = '<a href="https:www.ifthenpay.com" target="_blank"><img src="view/image/payment/ifthenpay/payshop.png" class="ccardLogo" alt="Payshop Logo" title="Payshop" style="border: 1px solid #EEEEEE; width: 143px; height: 38px;" /><br /></a>';
$_['acess_user_documentation'] = 'Access the user documentation.';
$_['create_account_now'] = 'Create an account now!';
$_['entry_order_status_canceled'] = 'Order Status Canceled:';

//Entry
$_['entry_backoffice_key'] = 'Backoffice Key';
$_['help_backoffice_key'] = 'Backoffice key that is sent to your email after creation of contract.';
$_['help_place_holder_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['switch_enable'] = 'Enable';
$_['switch_disable'] = 'Disable';
$_['add_new_accounts'] = 'Added a new account to your contract?';
$_['add_new_accounts_explain'] = 'To set a different account press the reset button, doing so will clear this payment method\'s current settings, and allow you to insert a new backoffice key associated with your contract.';
$_['reset_accounts'] = 'Reset Accounts';
$_['sandbox_help'] = 'Activate sandbox mode, to test the module without activating the callback.';
$_['sandbox_mode'] = 'Sandbox Mode';
$_['dontHaveAccount_payshop'] = 'Don\'t have an Payshop account?';
$_['requestAccount_payshop'] = 'Request Payshop account creation';
$_['newUpdateAvailable'] = 'New update is available!';
$_['moduleUpToDate'] = 'Your module is up to date!';
$_['downloadUpdateModule'] = 'Download Update Module';
$_['acess_user_documentation_link'] = 'https://www.ifthenpay.com/downloads/opencart/opencart_user_guide_en.pdf';
$_['entry_minimum_value'] = 'Order Minimum Value';
$_['help_entry_minimum_value'] = 'Only show customer this payment method if order value equal or greater than minimum value';
$_['entry_maximum_value'] = 'Order Maximum Value';
$_['help_entry_maximum_value'] = 'Only show customer this payment method if order value equal or less than maximum value';
$_['resendPaymentData']	= 'Send email of order with payment data';


// Entry
$_['activate_callback'] = 'Activate Callback';
$_['switch_enable'] = 'Enable';
$_['switch_disable'] = 'Disable';
$_['entry_order_status_pending'] = 'Order Status Pending:';
$_['help_entry_order_status_pending'] = 'This status is assigned to the order upon it\'s creation and normaly, it is set as Pending';
$_['entry_order_status_complete'] = 'Order Status Paid:';
$_['entry_geo_zone'] = 'Geo Zone:';
$_['entry_status'] = 'Status:';
$_['entry_sort_order'] = 'Sort Order:';
$_['entry_payshop_payshopKey'] = 'Payshop Key';
$_['entry_payshop_validade'] = 'Expiration';
$_['payshop_validade_helper'] = 'Choose the number of days, leave empty if you do not want Expiration';
$_['choose_key'] = 'Choose a key';
$_['entry_antiPhishingKey'] = 'Anti-Phishing key';
$_['entry_urlCallback'] = 'Callback Url';
$_['callbackIsActivated'] = 'Callback is activated';
$_['callbackNotActivated'] = 'Callback not activated';
$_['sandboxActivated'] = 'Sandbox mode activated';
$_['show_paymentMethod_logo'] = 'Show Payment Method Logo on Checkout';
$_['request_new_account_success'] = 'Email requesting new account send with success.';
$_['request_new_account_error'] = 'Error sending email requesting new account.';
$_['dontHaveAccount_payshop'] = 'Don\'t have an Payshop account?';
$_['requestAccount_payshop'] = 'Request Payshop account creation';
$_['activate_cancelPayshopOrder'] = 'Cancel Payshop Order';
$_['payshopOrderCancel_help'] = 'Cancel Payshop order after reference expire.';

// Error
$_['error_permission'] = 'Warning: No permission to modify  Payshop!';
$_['error_backofficeKey_required'] = 'Backoffice key is required!';
$_['error_backofficeKey_already_reset'] = 'Backoffice key already blank!';
$_['error_backofficeKey_error'] = 'Error saving Backoffice key!';
$_['reset_account_success'] = 'Ifthenpay account reset with success!';
$_['reset_account_error'] = 'Error Reseting Ifthenpay Accounts!';
$_['text_cron_1'] = 'Cron Job\'s are scheduled tasks that are run periodically. To setup your servers to use cron job you can read the <a href="http://docs.opencart.com/extension/cron/" target="_blank" class="alert-link">opencart documentation</a> page.';
$_['text_cron_2'] = 'You need to set your Cron task to run every minute.';
$_['text_cron_3'] = 'When the callback fails, you can set a cron job to check the payment status.';
$_['text_cron_4'] = 'You need to set Cron to run every day at 1:00 am.';
$_['entry_cron'] = 'Cron URL';
$_['button_copy'] = 'Copy';
$_['text_instruction'] = 'CRON Instructions';
$_['error_invalid_max_number'] = 'Warning: Order Maximum Value invalid number!';
$_['error_invalid_min_number'] = 'Warning: Order Minimum Value invalid number!';
$_['error_incompatible_min_max'] = 'Warning: Order Minimum and Maximum Values are not compatible!';
$_['error_key_required'] = 'Warning: Payshop key required!';
$_['error_invalid_expiration'] = 'Warning: Invalid expiration!';
?>
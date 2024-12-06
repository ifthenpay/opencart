<?php

// Heading
$_['heading_title'] = 'Pix';

// Text
$_['text_extension'] = 'Extensions';
$_['text_payment'] = 'Payment';
$_['text_success'] = 'Success: You have modified Pix payment module!';

$_['text_pix'] = '<a href="https:www.ifthenpay.com" target="_blank"><img src="view/image/payment/ifthenpay/pix.png" class="pixLogo" alt="Pix Logo" title="Pix" style="border: 1px solid #EEEEEE; width: 117px; height: 38px;" /><br /></a>';
$_['create_account_now'] = 'Create an account now!';
$_['text_home'] = 'Home';
$_['text_all_zones'] = 'All zones';

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
$_['reset_account_success'] = 'Ifthenpay account reset with success!';
$_['reset_account_error'] = 'Error Reseting Ifthenpay Accounts!';
$_['dontHaveAccount_pix'] = 'Don\'t have an Pix account?';
$_['requestAccount_pix'] = 'Request Pix account creation';
$_['activate_cancelPixOrder'] = 'Cancel Pix Order';
$_['pixOrderCancel_help'] = 'Cancel Pix order after payment data expire.';
$_['newUpdateAvailable'] = 'New update is available!';
$_['moduleUpToDate'] = 'Your module is up to date!';
$_['downloadUpdateModule'] = 'Download Update Module';
$_['entry_minimum_value'] = 'Order Minimum Value';
$_['help_entry_minimum_value'] = 'Only show customer this payment method if order value equal or greater than minimum value';
$_['entry_maximum_value'] = 'Order Maximum Value';
$_['help_entry_maximum_value'] = 'Only show customer this payment method if order value equal or less than maximum value';
$_['request_new_account_success'] = 'Email requesting new account send with success.';
$_['request_new_account_error'] = 'Error sending email requesting new account.';
$_['entry_pix_transaction_id'] = 'Pix transaction ID';
$_['entry_amount'] = 'Amount';
$_['msg_callback_test_empty_fields'] = 'Please fill all fields';
$_['entry_test_callback'] = 'Test Callback';
$_['btn_test'] = 'Test';
$_['entry_payment_method_title'] = 'Payment Method Title';

$_['label_cron_url'] = 'Cron URL';
$_['btn_copy'] = 'Copy';

$_['text_cron_documentation'] = 'Cron Job\'s are scheduled tasks that are run periodically. To setup your servers to use cron job you can read the <a href="http://docs.opencart.com/extension/cron/" target="_blank" class="alert-link">opencart documentation</a> page.';

$_['head_cancel_cron'] = '(Cron Job) Cancel Pix Order';
$_['text_cancel_cron_desc'] = 'You can set up this cron job to change orders status to "Canceled", if order is not payed within 30 minutes after order confirmation.';
$_['text_cancel_cron_schedule'] = 'Schedule the cron job to run every 1 minute.';


// Entry
$_['activate_callback'] = 'Activate Callback';
$_['switch_enable'] = 'Enable';
$_['switch_disable'] = 'Disable';
$_['entry_order_status_pending'] = 'Order Status Pending:';
$_['help_entry_order_status_pending'] = 'This status is assigned to the order upon it\'s creation and normaly, it is set as Pending';
$_['entry_order_status_complete'] = 'Order Status Paid:';
$_['entry_order_status_canceled'] = 'Order Status Canceled:';
$_['entry_geo_zone'] = 'Geo Zone:';
$_['entry_status'] = 'Status:';
$_['entry_sort_order'] = 'Sort Order:';
$_['entry_pix_pixKey'] = 'Pix Key';
$_['choose_key'] = 'Choose a key';
$_['entry_antiPhishingKey'] = 'Anti-Phishing key';
$_['entry_urlCallback'] = 'Callback Url';
$_['callbackIsActivated'] = 'Callback is activated';
$_['callbackNotActivated'] = 'Callback not activated';
$_['sandboxActivated'] = 'Sandbox mode activated';
$_['show_paymentMethod_logo'] = 'Show Payment Method Logo on Checkout';

// Error
$_['error_permission'] = 'Warning: No permission to modify Pix!';
$_['error_backofficeKey_required'] = 'Backoffice key is required!';
$_['error_backofficeKey_already_reset'] = 'Backoffice key already blank!';

$_['error_backofficeKey_error'] = 'Error saving Backoffice key!';
$_['error_invalid_max_number'] = 'Warning: Order Maximum Value invalid number!';
$_['error_invalid_max_number_larger_than_ifthenpay'] = 'Warning: Order Maximum Value invalid number! Must be equal or less than value defined in ifthenpay\'s backoffice.';
$_['error_invalid_min_number'] = 'Warning: Order Minimum Value invalid number!';
$_['error_invalid_min_number_less_than_ifthenpay'] = 'Warning: Order Minimum Value invalid number! Must be equal or greater than value defined in ifthenpay\'s backoffice.';
$_['error_incompatible_min_max'] = 'Warning: Order Minimum and Maximum Values are not compatible!';
$_['error_key_required'] = 'Warning: Pix key required!';
?>

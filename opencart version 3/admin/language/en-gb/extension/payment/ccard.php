<?php

// Heading
$_['heading_title'] = 'Credit Card';

// Text
$_['text_extension'] = 'Extensions';
$_['text_payment'] = 'Payment';
$_['text_success'] = 'Success: You have modified Credit Card payment module!';


$_['text_ccard'] = '<a href="https://ifthenpay.com" target="_blank"><img src="view/image/payment/ifthenpay/ccard.png" alt="Credit Card Logo" title="Credit Card" style="border: 1px solid #EEEEEE; height: 38px;" /><br /></a>';
$_['create_account_now'] = 'Create an account now!';
$_['text_home'] = 'Home';
$_['text_all_zones'] = 'All zones';

//Entry
$_['entry_backoffice_key'] = 'Backoffice Key';
$_['help_backoffice_key'] = 'Backoffice key that is sent to your email after creation of contract.';
$_['help_place_holder_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['add_new_accounts'] = 'Added a new account to your contract?';
$_['add_new_accounts_explain'] = 'To set a different account press the reset button, doing so will clear this payment method\'s current settings, and allow you to insert a new backoffice key associated with your contract.';
$_['reset_accounts'] = 'Reset Accounts';
$_['sandbox_help'] = 'Activate sandbox mode, to test the module without activating the callback.';
$_['sandbox_mode'] = 'Sandbox Mode';
$_['dontHaveAccount_ccard'] = 'Don\'t have an Credit Card account?';
$_['requestAccount_ccard'] = 'Request Credit Card account creation';
$_['activate_cancelCcardOrder'] = 'Cancel Credit Card Order';
$_['ccardOrderCancel_help'] = 'Cancel Credit Card order after payment data expire.';
$_['newUpdateAvailable'] = 'New update is available!';
$_['extensionUpToDate'] = 'Your extension is up to date!';
$_['downloadExtensionUpdate'] = 'Download Extension Update';
$_['entry_minimum_value'] = 'Order Minimum Value';
$_['help_entry_minimum_value'] = 'Only show customer this payment method if order value equal or greater than minimum value';
$_['entry_maximum_value'] = 'Order Maximum Value';
$_['help_entry_maximum_value'] = 'Only show customer this payment method if order value equal or less than maximum value';
$_['request_new_account_success'] = 'Email requesting new account send with success.';
$_['request_new_account_error'] = 'Error sending email requesting new account.';
$_['entry_payment_method_title'] = 'Payment Method Title';
$_['entry_payment_method_instruction'] = 'Payment Method Instruction';
$_['help_entry_payment_method_instruction'] = 'Small text shown before confirming order, can be used to provide further instructions to customer.';


$_['label_cron_url'] = 'Cron URL';
$_['btn_copy'] = 'Copy';

$_['text_cron_documentation'] = 'Cron Job\'s are scheduled tasks that are run periodically. To setup your servers to use cron job you can read the <a href="http://docs.opencart.com/extension/cron/" target="_blank" class="alert-link">opencart documentation</a> page.';

$_['head_cancel_cron'] = '(Cron Job) Cancel Credit Card Order';
$_['text_cancel_cron_desc'] = 'You can set up this cron job to change orders status to "Canceled", if order is not payed within 30 minutes after order confirmation.';
$_['text_cancel_cron_schedule'] = 'Schedule the cron job to run every 1 minute.';

// Entry
$_['text_enabled'] = 'Enabled';
$_['text_disabled'] = 'Disabled';
$_['switch_enable'] = 'Enable';
$_['switch_disable'] = 'Disable';
$_['entry_order_status_pending'] = 'Order Status Pending:';
$_['help_entry_order_status_pending'] = 'This status is assigned to the order upon it\'s creation and normaly, it is set as Pending';
$_['entry_order_status_complete'] = 'Order Status Paid:';
$_['entry_order_status_canceled'] = 'Order Status Canceled:';
$_['entry_order_status_failed'] = 'Order Status Failed:';
$_['entry_geo_zone'] = 'Geo Zone:';
$_['entry_status'] = 'Status:';
$_['entry_sort_order'] = 'Sort Order:';
$_['entry_ccard_ccardKey'] = 'Credit Card Key';
$_['entry_antiPhishingKey'] = 'Anti-Phishing key';
$_['sandboxActivated'] = 'Sandbox mode activated';
$_['show_paymentMethod_logo'] = 'Show Payment Method Logo on Checkout';

// Error
$_['error_permission'] = 'Warning: No permission to modify Credit Card!';
$_['error_backofficeKey_required'] = 'Backoffice key is required!';
$_['error_backofficeKey_already_reset'] = 'Backoffice key already blank!';

$_['error_backofficeKey_error'] = 'Error saving Backoffice key!';
$_['reset_account_success'] = 'Ifthenpay account reset with success!';
$_['reset_account_error'] = 'Error Reseting Ifthenpay Accounts!';
$_['error_invalid_max_number'] = 'Warning: Order Maximum Value invalid number!';
$_['error_invalid_min_number'] = 'Warning: Order Minimum Value invalid number!';
$_['error_incompatible_min_max'] = 'Warning: Order Minimum and Maximum Values are not compatible!';
$_['error_key_required'] = 'Warning: Ccard key required!';

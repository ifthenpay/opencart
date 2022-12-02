<?php
// Heading
$_['heading_title'] = 'MB WAY';
$_['heading_title_multibanco'] = 'MB WAY Configuration';

// Text
$_['text_extension'] = 'Extensions';
$_['text_payment'] = 'Payment';
$_['text_success'] = 'Success: You have modified MB WAY payment module!';
$_['text_mbway'] = '<a href="https:www.ifthenpay.com" target="_blank"><img src="view/image/payment/ifthenpay/mbway.png" class="ccardLogo" alt="MB WAY Logo" title="MB WAY" style="border: 1px solid #EEEEEE; width: 82px; height: 38px;" /><br /></a>';
$_['acess_user_documentation'] = 'Access the user documentation.';
$_['create_account_now'] = 'Create an account now!';

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
$_['dontHaveAccount_mbway'] = 'Don\'t have an MB WAY account?';
$_['requestAccount_mbway'] = 'Request MB WAY account creation';
$_['newUpdateAvailable'] = 'New update is available!';
$_['moduleUpToDate'] = 'Your module is up to date!';
$_['downloadUpdateModule'] = 'Download Update Module';
$_['acess_user_documentation_link'] = 'https://www.ifthenpay.com/downloads/opencart/opencart_user_guide_en.pdf';
$_['entry_minimum_value'] = 'Order Minimum Value';
$_['help_entry_minimum_value'] = 'Only show customer this payment method if order value equal or greater than minimum value';
$_['entry_maximum_value'] = 'Order Maximum Value';
$_['help_entry_maximum_value'] = 'Only show customer this payment method if order value equal or less than maximum value';
$_['error_payment_mbway_input_required'] = 'MB WAY phone is required!';
$_['error_payment_mbway_input_invalid'] = 'MB WAY phone is invalid!';
$_['mbwayPhoneNumber'] = 'MB WAY Phone Number';
$_['adminResendMbwayNotification'] = 'Resend MB WAY notification';
$_['entry_mbway_transaction_id']	= 'MB WAY transaction ID';
$_['entry_amount']	= 'Amount';
$_['msg_callback_test_empty_fields']	= 'Please fill all fields';
$_['entry_test_callback']	= 'Test Callback';
$_['btn_test']	= 'Test';

// Entry
$_['activate_callback'] = 'Activate Callback';
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
$_['entry_mbway_mbwayKey'] = 'MB WAY key';
$_['choose_key'] = 'Choose a key';
$_['activate_cancelMbwayOrder'] = 'Cancel MB WAY Order';
$_['mbwayOrderCancel_help'] = 'Cancel MB WAY order after notification expire.';
$_['entry_antiPhishingKey'] = 'Anti-Phishing key';
$_['entry_urlCallback'] = 'Callback Url';
$_['callbackIsActivated'] = 'Callback is activated';
$_['callbackNotActivated'] = 'Callback not activated';
$_['sandboxActivated'] = 'Sandbox mode activated';
$_['show_paymentMethod_logo'] = 'Show Payment Method Logo on Checkout';
$_['request_new_account_success'] = 'Email requesting new account send with success.';
$_['request_new_account_error'] = 'Error sending email requesting new account.';
$_['dontHaveAccount_mbway'] = 'Don\'t have an MB WAY account?';
$_['requestAccount_mbway'] = 'Request MB WAY account creation';


// Error
$_['error_permission'] = 'Warning: No permission to modify  MB WAY!';
$_['error_backofficeKey_required'] = 'Backoffice key is required!';
$_['error_backofficeKey_already_reset'] = 'Backoffice key already blank!';
$_['error_backofficeKey_error'] = 'Error saving Backoffice key!';

$_['reset_account_success'] = 'Ifthenpay account reset with success!';
$_['reset_account_error'] = 'Error Reseting Ifthenpay Accounts!';


$_['label_cron_url'] = 'Cron URL';
$_['btn_copy'] = 'Copy';

$_['text_cron_documentation'] = 'Cron Job\'s are scheduled tasks that are run periodically. To setup your servers to use cron job you can read the <a href="http://docs.opencart.com/extension/cron/" target="_blank" class="alert-link">opencart documentation</a> page.';

$_['head_cancel_cron'] = '(Cron Job) Cancel MB WAY Order';
$_['text_cancel_cron_desc'] = 'You can set up this cron job to change orders status to "Canceled", if order is not payed within 30 minutes after order confirmation.';
$_['text_cancel_cron_schedule'] = 'Schedule the cron job to run every 1 minute.';

$_['head_check_cron'] = '(Cron Job) Check Status of MB WAY Order';
$_['text_check_cron_desc'] = 'If it is not possible to activate the Callback, you can set up this cron job to check the payment status of the orders instead.';
$_['text_check_cron_schedule'] = 'Schedule the cron job to run every day at 1:00 am.';


$_['error_invalid_max_number'] = 'Warning: Order Maximum Value invalid number!';
$_['error_invalid_min_number'] = 'Warning: Order Minimum Value invalid number!';
$_['error_incompatible_min_max'] = 'Warning: Order Minimum and Maximum Values are not compatible!';
$_['error_key_required'] = 'Warning: MB WAY key required!';
?>
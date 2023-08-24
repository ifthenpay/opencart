<?php



// Heading
$_['heading_title'] = 'MB WAY';



// group labels
$_['text_general'] = 'General';
$_['text_callback'] = 'Callback';
$_['text_clear_configuration'] = 'Clear Configuration';
$_['text_account'] = 'Account';
$_['text_test_callback'] = 'Test Callback';


// breadcrumb
$_['text_extension'] = 'Extensions';



// text
$_['text_callback_is_active'] = 'Callback is active';
$_['text_callback_is_inactive'] = 'Callback is inactive';
$_['text_btn_clear_configuration'] = 'Clear Configuration';
$_['text_are_you_sure_clear'] = 'Are you sure you want to clear the configuration? This will delete all the MB WAY configuration data.';
$_['text_added_new_account'] = 'Added a new account to your contract?';
$_['text_to_set_different_account_press'] = 'To set a different account press the reset button, doing so will clear this payment method\'s current settings, and allow you to insert a new backoffice key associated with your contract.';
$_['text_tab_refund'] = 'Refund';
$_['text_transaction_id'] = 'Transaction ID';
$_['text_payment_status'] = 'Payment Status';
$_['text_order_total'] = 'Total Paid';
$_['text_no_mbway_accounts_found'] = 'No MB WAY accounts found for this contract.';
$_['text_to_request_a_mbway_account'] = 'To request a MB WAY account for your contract click the button "Request MB WAY Account".';
$_['text_ifthenpay_team_will_create'] = 'By doing so, you will notify ifthenpay\'s team, who will subsequently proceed to create a MB WAY account for your contract.';
$_['text_request_mbway_account_btn'] = 'Request MB WAY Account';
$_['text_are_you_sure_request_account'] = 'Are you sure you want to request a MB WAY account?';
$_['text_are_you_sure_refresh_accounts'] = 'Are you sure you want to refresh the MB WAY accounts?';







// Entry labels
$_['entry_backoffice_key'] = 'Backoffice Key';

$_['entry_status'] = 'Status';
$_['entry_activate_callback'] = 'Activate Callback';
$_['entry_enable_cancel_order_cronjob'] = 'Enable Cancel Order Cron job';
$_['entry_show_countdown'] = 'Show Countdown in Checkout';
$_['entry_show_refund_form'] = 'Enable Refund in Sales/Orders';

$_['entry_cancel_order_cronjob_url'] = 'Cronjob url';

$_['entry_key'] = 'MB WAY Key';
$_['entry_deadline'] = 'Deadline';

$_['entry_title'] = 'Title';
$_['entry_pending_status'] = 'Pending Status';
$_['entry_paid_status'] = 'Paid Status';
$_['entry_canceled_status'] = 'Canceled Status';
$_['entry_refunded_status'] = 'Refunded Status';
$_['entry_geo_zone'] = 'Geo Zone';
$_['entry_min_value'] = 'Order Minimum Value';
$_['entry_max_value'] = 'Order Maximum Value';

$_['entry_sort_order'] = 'Sort Order';
$_['entry_info_callback_url'] = 'Callback URL';
$_['entry_info_anti_phishing_key'] = 'Anti-Phishing Key';
$_['entry_transaction_id'] = 'Transaction ID';
$_['entry_amount'] = 'Amount';
$_['text_test_callback_btn'] = 'Test';
$_['text_are_you_sure_test_callback'] = 'Are you sure you want to test the callback?';
$_['text_upgrade'] = 'Upgrade';
$_['text_new_version_available'] = 'New version available!';
$_['text_download_installer_btn'] = 'Download installer';
$_['text_user_manual_btn'] = 'Instructions';
$_['text_support_btn'] = 'Support';

// Entry placeholder
$_['entry_plh_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['entry_plh_key'] = 'Select an key';



// Help texts
$_['help_min_value'] = 'Display this payment method for orders with value greater or equal to this value. Leave empty to allow any.';
$_['help_max_value'] = 'Display this payment method for orders with value lesser or equal to this value. Leave empty to allow any.';
$_['help_cron_url_cancel_order'] = 'You can set up this cron job to change orders status to "Canceled" if order is not payed 30 minutes after confirmation. Schedule the cron job to run every 1 minute.';



// Error messages
$_['error_permission'] = 'Warning: You do not have permission to modify payment mbway!';
$_['error_backoffice_key_accounts_request'] = 'Error: Could not get accounts. Please check your backoffice key.';
$_['error_backoffice_key_format'] = 'Error: Backoffice key format is invalid.';
$_['error_backoffice_key_empty'] = 'Error: Backoffice key field is empty.';
$_['error_key_empty'] = 'Error: Key field is empty.';
$_['error_min_value_format'] = 'Error: Minimum value format is invalid.';
$_['error_min_value_greater_than_zero'] = 'Error: Minimum value must be greater than zero.';
$_['error_max_value_format'] = 'Error: Maximum value format is invalid.';
$_['error_max_value_greater_than_zero'] = 'Error: Maximum value must be greater than zero.';
$_['error_min_value_greater_than_max_value'] = 'Error: Minimum value must be lesser than maximum value.';
$_['error_callback_activation'] = 'Error: Unable to activate Callback.';
$_['error_transaction_id_empty'] = 'Error: Transaction ID field is empty.';
$_['error_amount_empty'] = 'Error: Amount field is empty.';
$_['error_amount_invalid'] = 'Error: Amount field is invalid.';
$_['error_callback_test'] = 'Error: MB WAY callback test error.';


// Success messages
$_['success_admin_configuration'] = 'Success: Configuration saved.';
$_['success_backoffice_key_saved'] = 'Success: Backoffice Key saved successfully.';
$_['success_clear_configuration'] = 'Success: Configuration cleared successfully.';
$_['success_request_account'] = 'Success: MB WAY account requested successfully.';
$_['success_refresh_accounts'] = 'Success: MB WAY accounts refreshed successfully.';
$_['success_callback_test'] = 'Success: MB WAY callback tested successfully.';
$_['warning_callback_test_already_paid'] = 'Warning: MB WAY callback tested, but order has already been set to paid.';


// admin refund

$_['text_refund_total'] = 'Total Refunded';
$_['entry_refund_amount'] = 'Amount to refund';
$_['entry_refund_description'] = 'Description';
$_['text_refund_amount'] = 'Refund Amount';
$_['text_payment_refund'] = 'Payment Refund';
$_['text_are_you_sure_refund'] = 'Are you sure you want to refund the amount of ';
$_['text_operation_irreversible'] = 'This operation is irreversible.';
$_['text_check_email_token'] = 'Check your email and enter the security token below.';
$_['text_security_code'] = 'Security Code';
$_['text_refund_sequence'] = '#';
$_['text_refund_amount_refunded'] = 'Amount refunded';
$_['text_refund_date'] = 'Date';
$_['text_refund_status'] = 'Status';
$_['text_refund_history'] = 'Refund history';
$_['text_refund'] = 'Refund';
$_['text_btn_refund_payment'] = 'Refund Payment';
$_['text_refund_description'] = 'Description';


$_['comment_refunded'] = 'MB WAY: Order refunded.';


$_['text_btn_cancel'] = 'Cancel';
$_['text_btn_confirm'] = 'Confirm';


$_['success_refund'] = 'Refund successful.';


$_['error_refund_amount_required'] = 'Error: Amount to refund is required.';
$_['error_refund_amount_invalid'] = 'Error: Amount to refund format is invalid.';
$_['error_refund_amount_exceeds_order_amount'] = 'Error: Amount to refund exceeds order total.';
$_['error_invalid_token'] = 'Error: Submitted Token is invalid.';
$_['error_refund'] = 'Error: unable to complete refund.';
$_['error_refund_no_funds'] = 'Error: not enough funds to complete refund.';


// refund email

$_['text_your_code_is'] = 'Your Security Code is ';
$_['text_this_code_will_be_available_for'] = 'This code will be active for 30 minutes. If you do not enter it on the Returns page you just visited within that time frame, you may have to start the return process. ';
$_['text_not_recognize'] = 'If you do not recognize or expect this email, you can always report suspicious behavior to our support team.';
$_['text_do_not_respond'] = 'Please do not reply to this email. The sending address is only used to transmit automated messages.';
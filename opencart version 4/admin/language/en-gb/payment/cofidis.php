<?php

// Admin logo
$_['text_cofidis'] = '<img src="' . HTTP_CATALOG . 'extension/ifthenpay/admin/view/image/cofidis.png" alt="Ifthenpay - Cofidis Pay" title="Ifthenpay - Cofidis Pay"/>';


// Heading
$_['heading_title'] = 'Cofidis Pay';



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
$_['text_are_you_sure_clear'] = 'Are you sure you want to clear the configuration? This will delete all the Cofidis Pay configuration data.';
$_['text_added_new_account'] = 'Added a new account to your contract?';
$_['text_to_set_different_account_press'] = 'To set a different account press the reset button, doing so will clear this payment method\'s current settings, and allow you to insert a new backoffice key associated with your contract.';
$_['text_transaction_id'] = 'Transaction ID';
$_['text_payment_status'] = 'Payment Status';
$_['text_order_total'] = 'Total Paid';
$_['text_no_credit_card_accounts_found'] = 'No Cofidis Pay accounts found for this contract.';
$_['text_to_request_a_credit_card_account'] = 'To request a Cofidis Pay account for your contract click the button "Request Cofidis Pay Account".';
$_['text_ifthenpay_team_will_request'] = 'By doing so, you will notify ifthenpay\'s team, who will subsequently proceed to request the required documents through e-mail, in order to complete the process.';
$_['text_request_cofidis_account_btn'] = 'Request Cofidis Pay Account';
$_['text_are_you_sure_request_account'] = 'Are you sure you want to request a Cofidis Pay account?';
$_['text_are_you_sure_refresh_accounts'] = 'Are you sure you want to refresh the Cofidis Pay accounts?';



// Entry labels
$_['entry_backoffice_key'] = 'Backoffice Key';

$_['entry_status'] = 'Status';
$_['entry_activate_callback'] = 'Activate Callback';

$_['entry_key'] = 'Cofidis Pay Key';

$_['entry_title'] = 'Title';
$_['entry_pending_status'] = 'Pending Status';
$_['entry_paid_status'] = 'Paid Status';
$_['entry_canceled_status'] = 'Canceled Status';
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
$_['help_min_value'] = 'Display this payment method for orders with value greater or equal to this value. This field will load the value set in ifthenpay\'s backoffice. When editing this value make sure to keep within the limits of the values set in ifthenpay\'s backoffice.';
$_['help_max_value'] = 'Display this payment method for orders with value lesser or equal to this value. This field will load the value set in ifthenpay\'s backoffice. When editing this value make sure to keep within the limits of the values set in ifthenpay\'s backoffice.';



// Error messages
$_['error_permission'] = 'Warning: You do not have permission to modify payment Cofidis Pay!';
$_['error_backoffice_key_accounts_request'] = 'Error: Could not get accounts. Please check your backoffice key.';
$_['error_backoffice_key_format'] = 'Error: Backoffice key format is invalid.';
$_['error_backoffice_key_empty'] = 'Error: Backoffice key field is empty.';
$_['error_key_empty'] = 'Error: Key field is empty.';
$_['error_min_value_format'] = 'Error: Minimum value format is invalid.';
$_['error_min_value_greater_than_zero'] = 'Error: Minimum value must be greater than zero.';
$_['error_max_value_format'] = 'Error: Maximum value format is invalid.';
$_['error_max_value_greater_than_zero'] = 'Error: Maximum value must be greater than zero.';
$_['error_min_value_greater_than_max_value'] = 'Error: Minimum value must be lesser than maximum value.';
$_['error_min_value_less_than_ifthenpay_value'] = 'Error: Order Minimum Value can not be lesser than ifthenpay\'s defined value.';
$_['error_max_value_less_than_ifthenpay_value'] = 'Error: Order Maximum Value can not be greater than ifthenpay\'s defined value.';
$_['error_unable_to_get_min_max_values_from_ifthenpay'] = 'Error: Could not get ifthenpay\'s min and max values.';



// Success messages
$_['success_admin_configuration'] = 'Success: Configuration saved.';
$_['success_backoffice_key_saved'] = 'Success: Backoffice Key saved successfully.';
$_['success_clear_configuration'] = 'Success: Configuration cleared successfully.';
$_['success_request_account'] = 'Success: Cofidis Pay account requested successfully.';
$_['success_refresh_accounts'] = 'Success: Cofidis Pay accounts refreshed successfully.';
$_['success_callback_test'] = 'Success: Cofidis Pay callback tested successfully.';
$_['warning_callback_test_already_paid'] = 'Warning: Cofidis Pay callback tested, but order has already been set to paid.';

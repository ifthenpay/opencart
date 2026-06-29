<?php

// Admin logo
$_['text_pix'] = '<img src="' . HTTP_CATALOG . 'extension/ifthenpay/admin/view/image/pix.png" height="30" alt="Ifthenpay - Pix" title="Ifthenpay - Pix"/>';


// Heading
$_['heading_title'] = 'Pix';



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
$_['text_are_you_sure_clear'] = 'Are you sure you want to clear the configuration? This will delete all the Pix configuration data.';
$_['text_added_new_account'] = 'Added a new account to your contract?';
$_['text_to_set_different_account_press'] = 'To set a different account press the reset button, doing so will clear this payment method\'s current settings, and allow you to insert a new backoffice key associated with your contract.';
$_['text_transaction_id'] = 'Transaction ID';
$_['text_payment_status'] = 'Payment Status';
$_['text_order_total'] = 'Total Paid';
$_['text_no_pix_accounts_found'] = 'No Pix accounts found for this contract.';
$_['text_to_request_a_pix_account'] = 'To request a Pix account for your contract click the button "Request Pix Account".';
$_['text_ifthenpay_team_will_create'] = 'By doing so, you will notify ifthenpay\'s team, who will subsequently proceed to create a Pix account for your contract.';
$_['text_request_pix_account_btn'] = 'Request Pix Account';
$_['text_are_you_sure_request_account'] = 'Are you sure you want to request a Pix account?';
$_['text_are_you_sure_refresh_accounts'] = 'Are you sure you want to refresh the Pix accounts?';
$_['text_accounts_label'] = 'Accounts';
$_['text_btn_refresh_accounts'] = 'Refresh';







// Entry labels
$_['entry_backoffice_key'] = 'Backoffice Key';

$_['entry_status'] = 'Status';
$_['entry_activate_callback'] = 'Activate Callback';
$_['entry_enable_cancel_order_cronjob'] = 'Enable Cancel Order Cron job';

$_['entry_cancel_order_cronjob_url'] = 'Cronjob url';

$_['entry_key'] = 'Pix Key';

$_['entry_title'] = 'Title';
$_['entry_enable_show_icon_checkout'] = 'Show Payment Method Icon';
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
$_['text_update_btn'] = 'Update';
$_['text_are_you_sure_update'] = 'Are you sure you want to update the extension? This will replace the extension files with the latest version.';
$_['text_user_manual_btn'] = 'Instructions';
$_['text_support_btn'] = 'Support';

// Entry placeholder
$_['entry_plh_backoffice_key'] = 'xxxx-xxxx-xxxx-xxxx';
$_['entry_plh_key'] = 'Select an key';



// Help texts
$_['help_enable_show_icon_checkout'] = 'Display this payment method icon at checkout (this may not work as expected with all themes).';
$_['help_min_value'] = 'Display this payment method for orders with value greater or equal to this value. Leave empty to allow any.';
$_['help_max_value'] = 'Display this payment method for orders with value lesser or equal to this value. Leave empty to allow any.';
$_['help_cron_url_cancel_order'] = 'You can set up this cron job to change orders status to "Canceled" if order is not payed 30 minutes after confirmation. Schedule the cron job to run every 1 minute.';



// Error messages
$_['error_permission'] = 'Warning: You do not have permission to modify payment pix!';
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
$_['error_upgrade'] = 'Error: Extension update failed.';
$_['error_upgrade_download'] = 'Error: Failed to download the update.';
$_['error_upgrade_extract'] = 'Error: Failed to extract the update.';
$_['error_upgrade_url_invalid'] = 'Error: Invalid update URL.';
$_['error_transaction_id_empty'] = 'Error: Transaction ID field is empty.';
$_['error_amount_empty'] = 'Error: Amount field is empty.';
$_['error_amount_invalid'] = 'Error: Amount field is invalid.';
$_['error_refresh_accounts'] = 'Unable to refresh accounts from Ifthenpay. Please try again later, or contact Ifthenpay support if the problem persists.';


// Success messages
$_['success_admin_configuration'] = 'Success: Configuration saved.';
$_['success_backoffice_key_saved'] = 'Success: Backoffice Key saved successfully.';
$_['success_clear_configuration'] = 'Success: Configuration cleared successfully.';
$_['success_upgrade'] = 'Success: Extension updated successfully.';
$_['success_request_account'] = 'Success: Pix account requested successfully.';
$_['success_refresh_accounts'] = 'Success: Pix accounts refreshed successfully.';


// callback test messages
$_['success_callback_test'] = 'Success: Pix callback tested successfully.';
$_['warning_callback_test_already_paid'] = 'Warning: Pix callback tested, but order has already been set to paid.';
$_['error_callback_test'] = 'Error: Pix callback test error.';
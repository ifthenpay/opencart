<?php

declare(strict_types=1);

namespace Ifthenpay\Forms;

use Ifthenpay\Forms\ConfigForm;
use Ifthenpay\Payments\Gateway;

class IfthenpaygatewayConfigForm extends ConfigForm
{
    protected $paymentMethod = Gateway::IFTHENPAYGATEWAY;
    protected $paymentMethodDefaultTitle = 'Ifthenpay Gateway';


    protected function checkIfEntidadeSubEntidadeIsSet(): bool
    {
        if (
            !isset($this->configData['payment_ifthenpaygateway_ifthenpaygatewayKey']) && !isset($this->data['payment_ifthenpaygateway_ifthenpaygatewayKey']) &&
            !isset($this->data['payment_ifthenpaygateway_ifthenpaygatewayKey'])
        ) {
            return false;
        }
        return true;
    }

    public function addToOptions(): void
    {
        $this->ifthenpayGateway->setAccount((array) unserialize($this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_userAccount')));


        if ($this->paymentMethod === Gateway::IFTHENPAYGATEWAY) {

            $this->options = [];

            $accounts = $this->ifthenpayGateway->getIfthenpayGatewayAccounts();
            if ($accounts) {

                foreach ($accounts as $account) {
                    $this->options[] = [
                        'value' => $account['GatewayKey'],
                        'name' => $account['Alias'],
                        'type' => $account['Tipo']
                    ];
                }
            }
        }
    }

    public function getForm(): array
    {
        $this->data['entry_ifthenpaygateway_ifthenpaygatewayKey'] = $this->ifthenpayController->language->get('entry_ifthenpaygateway_ifthenpaygatewayKey');
        if (
            $this->ifthenpayController->config->get('payment_ifthenpaygateway_userPaymentMethods') &&
            $this->ifthenpayController->config->get('payment_ifthenpaygateway_userAccount')
        ) {
            $this->addToOptions();
            $this->setHasCallback();
            $this->setGatewayBuilderData();
            $this->setIfthenpayCallback();
        } else {
            $this->setDefaultGatewayBuilderData();
        }
        return $this->data;
    }

    public function setGatewayBuilderData(): void
    {

        if (isset($this->ifthenpayController->request->post['payment_ifthenpaygateway_ifthenpaygatewayKey']) && $this->ifthenpayController->request->post['payment_ifthenpaygateway_ifthenpaygatewayKey']) {
            $this->data['payment_ifthenpaygateway_ifthenpaygatewayKey'] = $this->ifthenpayController->request->post['payment_ifthenpaygateway_ifthenpaygatewayKey'];
        } else if (isset($this->configData['payment_ifthenpaygateway_ifthenpaygatewayKey'])) {
            $this->data['payment_ifthenpaygateway_ifthenpaygatewayKey'] = $this->configData['payment_ifthenpaygateway_ifthenpaygatewayKey'];
        }


        if (!empty($this->options)) {
            $this->data['ifthenpaygateway_ifthenpaygatewayKeys'] = $this->options;
        }


        // paymentMethodsSelect
        $ifthenpaygatewayKey = $this->data['payment_ifthenpaygateway_ifthenpaygatewayKey'] ?? '';
        $backofficeKey = '';
        if (isset($this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_backofficeKey'])) {
            $backofficeKey = $this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_backofficeKey'];
        } else {
            $backofficeKey = $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_backofficeKey');
        }


        if ($backofficeKey !== '') {
            if (isset($this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_payment_method_title'])) {
                $this->data['payment_' . $this->paymentMethod . '_payment_method_title'] = $this->ifthenpayController
                    ->request->post['payment_' . $this->paymentMethod . '_payment_method_title'];
            } else {
                $paymentMethodTitleFromConfig = $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_payment_method_title');

                $this->data['payment_' . $this->paymentMethod . '_payment_method_title'] = $paymentMethodTitleFromConfig != '' ? $paymentMethodTitleFromConfig : $this->paymentMethodDefaultTitle;
            }

            if (isset($this->ifthenpayController->request->post['payment_ifthenpaygateway_btn_close_text'])) {
                $this->data['payment_' . $this->paymentMethod . '_btn_close_text'] = $this->ifthenpayController
                    ->request->post['payment_' . $this->paymentMethod . '_btn_close_text'];
            } else {

                $btnCloseText = $this->ifthenpayController->config->get('payment_' . $this->paymentMethod . '_btn_close_text');
                if (empty($btnCloseText)) {
                    $btnCloseText = $this->ifthenpayController->language->get('btn_close_text');
                }

                $this->data['payment_' . $this->paymentMethod . '_btn_close_text'] = $btnCloseText;
            }
        }


        if (
            $ifthenpaygatewayKey !== '' && $backofficeKey !== ''
        ) {


            // add the payment method selection

            // get selectable
            $gatewayMethods = $this->ifthenpayGateway->getIfthenpayGatewayPaymentMethodsDataByBackofficeKeyAndGatewayKey($backofficeKey, $ifthenpaygatewayKey);

            // get stored (selected)
            $storedIfthenpayGatewayMethodsArray = $this->ifthenpayController->config->get('payment_ifthenpaygateway_methods') ?? [];


            // get gatewayKeySettings
            $gatewayKeySettings = $this->getGatewayKeySettingsFromConfig($ifthenpaygatewayKey);

            // generate the html
            $this->data['payment_ifthenpaygateway_method_accounts_html'] = $this->generateIfthenpaygatewayPaymentMethodsHtml($gatewayMethods, $gatewayKeySettings, $storedIfthenpayGatewayMethodsArray);



            // add the default payment method selection

            $storedDefaultPaymentMethod = $this->ifthenpayController->config->get('payment_ifthenpaygateway_default_method') ?? '';
            $this->data['payment_ifthenpaygateway_selected_default_html'] = $this->generateSelectedDefaultHtml($gatewayMethods, $storedIfthenpayGatewayMethodsArray, $storedDefaultPaymentMethod);


            // add deadline

            if (isset($this->ifthenpayController->request->post['payment_' . $this->paymentMethod . '_deadline'])) {
                $this->data['payment_' . $this->paymentMethod . '_deadline'] = $this->ifthenpayController
                    ->request->post['payment_' . $this->paymentMethod . '_deadline'];
            } else {
                $this->data['payment_' . $this->paymentMethod . '_deadline'] = $this->ifthenpayController
                    ->config->get('payment_' . $this->paymentMethod . '_deadline');
            }
        }



        parent::setGatewayBuilderData();
        if (isset($this->data['payment_ifthenpaygateway_ifthenpaygatewayKey'])) {
            $this->gatewayDataBuilder->setEntidade(strtoupper($this->paymentMethod));
            $this->gatewayDataBuilder->setSubEntidade($this->data['payment_ifthenpaygateway_ifthenpaygatewayKey']);
        }
    }

    public function deleteConfigValues(): void
    {
        $this->ifthenpayController->load->model('extension/payment/ifthenpaygateway');
        $this->ifthenpayController->model_extension_payment_ifthenpaygateway->deleteSettingByKey('payment_ifthenpaygateway_urlCallback');
        $this->ifthenpayController->model_extension_payment_ifthenpaygateway->deleteSettingByKey('payment_ifthenpaygateway_chaveAntiPhishing');
        $this->ifthenpayController->model_extension_payment_ifthenpaygateway->deleteSettingByKey('payment_ifthenpaygateway_callback_activated');
        $this->ifthenpayController->model_extension_payment_ifthenpaygateway->deleteSettingByKey('payment_ifthenpaygateway_activateCallback');
        $this->ifthenpayController->model_extension_payment_ifthenpaygateway->deleteSettingByKey('payment_ifthenpaygateway_ifthenpaygatewayKey');
    }


    public function getGatewayKeySettingsFromConfig(string $gatewayKey): array
    {
        $ifthenpaygatewayAccounts = (array) unserialize($this->ifthenpayController->config->get('payment_ifthenpaygateway_userAccount')) ?? [];

        $gatewayKeySettings = array_filter($ifthenpaygatewayAccounts, function ($item) use ($gatewayKey) {
            if ($item['GatewayKey'] === $gatewayKey) {
                return true;
            }
        });

        $gatewayKeySettings = reset($gatewayKeySettings);
        return $gatewayKeySettings;
    }



    private function isGatewayKeyStatic(array $gatewayKeySettings): bool
    {
        return $gatewayKeySettings['Tipo'] === 'Estáticas';
    }

    public function generateIfthenpaygatewayPaymentMethodsHtml(array $paymentMethodGroupArray, array $gatewayKeySettings, array $storedMethods = []): string
    {
        $this->ifthenpayController->language->get('extension/ifthenpay/payment/ifthenpaygateway');

        $isStaticGatewayKey = $this->isGatewayKeyStatic($gatewayKeySettings);
        $html = '';

        $hiddenInputs = '';
        foreach ($paymentMethodGroupArray as $paymentMethodGroup) {
            $accountOptions = '';
            $hiddenInput = '';

            $entity = $paymentMethodGroup['Entity']; // unique identifier code like 'MB' or 'MULTIBANCO'


            $index = 0;
            foreach ($paymentMethodGroup['accounts'] as $account) {

                if ($index === 0) {
                    $hiddenInput = '<input type="hidden" name="payment_ifthenpaygateway_methods[' . $paymentMethodGroup['Entity'] . '][account]" value="' . $account['Conta'] . '">';
                }
                $index++;

                // set selected payment method key
                $selectedStr = '';
                if (isset($storedMethods[$entity]['account'])) {
                    $selectedStr = $account['Conta'] == $storedMethods[$entity]['account'] ? 'selected' : '';
                    $hiddenInput = '<input type="hidden" name="payment_ifthenpaygateway_methods[' . $paymentMethodGroup['Entity'] . '][account]" value="' . $account['Conta'] . '">';
                }


                $accountOptions .= '<option value="' . $account['Conta'] . '" ' . $selectedStr . '>' . $account['Alias'] . '</option>';
            }

            if ($hiddenInput !== '') {
                $hiddenInputs .= $hiddenInput;
            }



            $checkDisabledStr = $accountOptions === '' ? 'disabled' : '';
            $selectDisabledStr = ($accountOptions === '' || $isStaticGatewayKey) ? 'disabled' : '';
            $checkedStr = '';


            if ($accountOptions !== '') {
                // show method account select


                $selectOrActivate = '<select ' . $selectDisabledStr . ' name="payment_ifthenpaygateway_methods[' . $paymentMethodGroup['Entity'] . '][account]" id="' . $paymentMethodGroup['Entity'] . '" class="form-control">
					' . $accountOptions . '
				</select>';

                // add hidden select inputs
                if ($selectDisabledStr === 'disabled') {
                    $selectOrActivate .= $hiddenInputs;
                }

                // if the isActive is saved use it
                $checkedStr = (isset($storedMethods[$entity]['is_active']) && $storedMethods[$entity]['is_active'] == '1') || !$storedMethods ? 'checked' : '';
            } else {
                // show request button
                $selectOrActivate = '<button type="button" title="request payment method" class="btn btn-primary min_w_300 request_ifthenpaygateway_method" data-method="' . $paymentMethodGroup['Entity'] . '">
				' . $this->ifthenpayController->language->get('text_request_ifthenpaygateway_method_btn') . ' ' . $paymentMethodGroup['Method'] . '
					<span class="glyphicon glyphicon glyphicon-send"></span>
				</button>';
            }


            $html .= '<div class="method_line">
				<input type="hidden" name="payment_ifthenpaygateway_methods[' . $paymentMethodGroup['Entity'] . '][method_name]" value="' . $paymentMethodGroup['Method'] . '"/>
				<input type="hidden" name="payment_ifthenpaygateway_methods[' . $paymentMethodGroup['Entity'] . '][image_url]" value="' . $paymentMethodGroup['SmallImageUrl'] . '"/>
				<div class="method_checkbox">
					<label>
						<input type="hidden" name="payment_ifthenpaygateway_methods[' . $paymentMethodGroup['Entity'] . '][is_active]" value="0"/>
						<input type="checkbox" name="payment_ifthenpaygateway_methods[' . $paymentMethodGroup['Entity'] . '][is_active]" value="1" ' . $checkedStr . ' ' . $checkDisabledStr . ' data-method="' . $paymentMethodGroup['Entity'] . '" class="method_checkbox_input"/>
						<img src="' . $paymentMethodGroup['ImageUrl'] . '" alt="' . $paymentMethodGroup['Method'] . '"/>
					</label>
				</div>
				<div class="method_select">
					' . $selectOrActivate . '
				</div>
			</div>';
        }

        return $html;
    }



    public function generateSelectedDefaultHtml(array $paymentMethodGroupArray, array $storedMethods = [], string $storedDefaultPaymentMethod): string
    {
        $this->ifthenpayController->language->get('extension/ifthenpay/payment/ifthenpaygateway');

        $html = '';

        $index = 0;
        $accountOptions = '<option value="' . $index . '">' . $this->ifthenpayController->language->get('entry_plh_method_selected_default_none') . '</option>';

        foreach ($paymentMethodGroupArray as $paymentMethodGroup) {
            $index++;

            $isDisabled = '';
            if (isset($storedMethods[$paymentMethodGroup['Entity']]['is_active'])) {
                $isDisabled = $storedMethods[$paymentMethodGroup['Entity']]['is_active'] ? '' : 'disabled';
            }
            // disable option if no accounts exist
            if (empty($paymentMethodGroup['accounts'])) {
                $isDisabled = 'disabled';
            }

            $selectedStr = $index == $storedDefaultPaymentMethod ? 'selected' : '';

            $accountOptions .= '<option value="' . $index . '" data-method="' . $paymentMethodGroup['Entity'] . '" ' . $selectedStr . ' ' . $isDisabled . '>' . $paymentMethodGroup['Method'] . '</option>';
        }


        $html = '<select name="payment_ifthenpaygateway_default_method" id="payment_ifthenpaygateway_default" class="form-control">
			' . $accountOptions . '
		</select>';


        return $html;
    }
}

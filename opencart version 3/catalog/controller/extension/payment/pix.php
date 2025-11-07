<?php

use Ifthenpay\Payments\Gateway;
use Ifthenpay\Utility\Mix;


class ControllerExtensionPaymentPix extends IfthenpayControllerCatalog
{
    protected $paymentMethod = Gateway::PIX;

    public function index()
    {
        $variablesForJavascript = [
            'pixName' => [
                'required' => $this->language->get('error_payment_pix_name_required'),
                'invalid' => $this->language->get('error_payment_pix_name_invalid'),
            ],
            'pixCpf' => [
                'required' => $this->language->get('error_payment_pix_cpf_required'),
                'invalid' => $this->language->get('error_payment_pix_cpf_invalid'),
            ],
            'pixEmail' => [
                'required' => $this->language->get('error_payment_pix_email_required'),
                'invalid' => $this->language->get('error_payment_pix_email_invalid'),
            ],
            'pixPhone' => [
                'invalid' => $this->language->get('error_payment_pix_phone_invalid'),
            ],
            'pixAddress' => [
                'invalid' => $this->language->get('error_payment_pix_address_invalid'),
            ],
            'pixStreetNumber' => [
                'invalid' => $this->language->get('error_payment_pix_streetnumber_invalid'),
            ],
            'pixCity' => [
                'invalid' => $this->language->get('error_payment_pix_city_invalid'),
            ],
            'pixZipCode' => [
                'invalid' => $this->language->get('error_payment_pix_zipcode_invalid'),
            ],
            'pixState' => [
                'invalid' => $this->language->get('error_payment_pix_state_invalid'),
            ],

        ];
        $mix = $this->ifthenpayContainer->getIoc()->make(Mix::class);

        $data = $this->getCustomerData();

        $data['pixScript'] = 'catalog/view/javascript/ifthenpay/' . $mix->create('checkoutPixPage.js');
        $data['pixStyle'] = 'catalog/view/theme/default/stylesheet/ifthenpay/' . $mix->create('paymentOptions.css');
        $data['phpVariables'] = json_encode($variablesForJavascript);
        $data['showPaymentInstruction'] = $this->config->get('payment_' . $this->paymentMethod . '_show_instruction');

        return $this->load->view('extension/payment/pix', $data);
    }


    private function getCustomerData(): array
    {
        $this->load->model('checkout/order');
        $orderId = $this->session->data['order_id'] ?? '';
        $orderInfo = $this->model_checkout_order->getOrder($orderId);

        $customerData = [];

        if (!empty($orderInfo)) {

            // name
            $firstName = isset($orderInfo['firstname']) ? $orderInfo['firstname'] : '';
            $lastName = isset($orderInfo['lastname']) ? $orderInfo['lastname'] : '';
            if ($firstName . $lastName != '') {
                $customerData['customerName'] = trim($firstName . ' ' . $lastName);
            }

            // email
            $customerData['customerEmail'] = $orderInfo['email'] ?? '';

            // TODO: leaving commented as result of conclusion that most users will most likely clear the fields since they are optional
            // // phone
            // $customerData['customerPhone'] = $orderInfo['telephone'] ?? '';

            // // address
            // $addressOne = $orderInfo['payment_address_1'] ?? '';
            // $addressTwo = $orderInfo['payment_address_2'] ?? '';
            // if($addressOne . $addressTwo != ''){
            // 	$customerData['customerAddress'] = trim($addressOne . ' ' . $addressTwo);
            // }

            // // city
            // $customerData['customerCity'] = $orderInfo['payment_city'] ?? '';
        }

        return $customerData;
    }
}

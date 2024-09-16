<?php

namespace Ifthenpay;

use Opencart\System\Library\Log;
use Opencart\System\Engine\Registry;
use Opencart\System\Library\Request;

class CallbackService
{

	const PAYMENT_METHODS_WITH_CALLBACK = [
		'mbway',
		'multibanco',
		'payshop',
		'cofidis',
	];

	public Registry $registry; // opencart registry, used to get models
	private Log $logger;

	private $isCallbackActive;
	private $antiPhishingKey;

	public function __construct(Registry $registry)
	{
		$this->registry = $registry;
		$this->registry->load->model('checkout/order');
		$this->logger = new Log('ifthenpay.log');
	}



	public function HandleFromMultibanco(Request $request)
	{

		try {

			// set this config values to instance to later validate against callback data
			$this->isCallbackActive = $this->registry->config->get('payment_multibanco_activate_callback');
			$this->antiPhishingKey = $this->registry->config->get('payment_multibanco_anti_phishing_key');

			$reference = isset($request->get['reference']) ? $request->get['reference'] : '';
			$orderId = isset($request->get['order_id']) ? $request->get['order_id'] : '';

			// check if there is a record in multibanco table
			$this->registry->load->model('extension/ifthenpay/payment/multibanco');
			$storedData = $this->registry->model_extension_ifthenpay_payment_multibanco->getMultibancoRecordByReference($reference);

			if (!empty($storedData)) {
				$this->processCallbackForMultibanco($request, $storedData);
			} else {


				$this->registry->load->model('extension/ifthenpay/payment/ifthenpaygateway');
				$storedData = $this->registry->model_extension_ifthenpay_payment_ifthenpaygateway->getIfthenpayGatewayRecordByOrderId($orderId);

				if (!empty($storedData)) {

					$this->processCallbackForIfthenpayGateway($request, $storedData);
				}

				if (empty($storedData)) {
					// no payment method data was found in local tables of ifthenpay
					throw new \Exception('StoredPaymentData not found in local table.', 10);
				}
			}
		} catch (\Throwable $th) {
			$this->logger->write('IFTHENPAY - HandleFromMultibanco - ERROR : ' . $th->getMessage());

			$code = $th->getCode() ?? '000';

			http_response_code(400);
			die('fail - ' . $code);
		}

		http_response_code(200);
		die('ok');
	}



	public function HandleFromMbway(Request $request)
	{

		try {

			// set this config values to instance to later validate against callback data
			$this->isCallbackActive = $this->registry->config->get('payment_mbway_activate_callback');
			$this->antiPhishingKey = $this->registry->config->get('payment_mbway_anti_phishing_key');

			$transactionId = isset($request->get['transaction_id']) ? $request->get['transaction_id'] : '';
			$orderId = isset($request->get['order_id']) ? $request->get['order_id'] : '';

			// check if there is a record in mbway table
			$this->registry->load->model('extension/ifthenpay/payment/mbway');
			$storedData = $this->registry->model_extension_ifthenpay_payment_mbway->getMbwayRecordByTransactionId($transactionId);

			if (!empty($storedData)) {
				$this->processCallbackForMbway($request, $storedData);
			} else {


				$this->registry->load->model('extension/ifthenpay/payment/ifthenpaygateway');
				$storedData = $this->registry->model_extension_ifthenpay_payment_ifthenpaygateway->getIfthenpayGatewayRecordByOrderId($orderId);

				if (!empty($storedData)) {

					$this->processCallbackForIfthenpayGateway($request, $storedData);
				}

				if (empty($storedData)) {
					// no payment method data was found in local tables of ifthenpay
					throw new \Exception('StoredPaymentData not found in local table.', 10);
				}
			}
		} catch (\Throwable $th) {
			$this->logger->write('IFTHENPAY - HandleFromMbway - ERROR : ' . $th->getMessage());

			$code = $th->getCode() ?? '000';

			http_response_code(400);
			die('fail - ' . $code);
		}

		http_response_code(200);
		die('ok');
	}



	public function HandleFromPayshop(Request $request)
	{

		try {

			// set this config values to instance to later validate against callback data
			$this->isCallbackActive = $this->registry->config->get('payment_payshop_activate_callback');
			$this->antiPhishingKey = $this->registry->config->get('payment_payshop_anti_phishing_key');

			$reference = isset($request->get['reference']) ? $request->get['reference'] : '';
			$orderId = isset($request->get['order_id']) ? $request->get['order_id'] : '';

			// check if there is a record in payshop table
			$this->registry->load->model('extension/ifthenpay/payment/payshop');
			$storedData = $this->registry->model_extension_ifthenpay_payment_payshop->getPayshopRecordByReference($reference);

			if (!empty($storedData)) {
				$this->processCallbackForPayshop($request, $storedData);
			} else {


				$this->registry->load->model('extension/ifthenpay/payment/ifthenpaygateway');
				$storedData = $this->registry->model_extension_ifthenpay_payment_ifthenpaygateway->getIfthenpayGatewayRecordByOrderId($orderId);

				if (!empty($storedData)) {

					$this->processCallbackForIfthenpayGateway($request, $storedData);
				}

				if (empty($storedData)) {
					// no payment method data was found in local tables of ifthenpay
					throw new \Exception('StoredPaymentData not found in local table.', 10);
				}
			}
		} catch (\Throwable $th) {
			$this->logger->write('IFTHENPAY - HandleFromPayshop - ERROR : ' . $th->getMessage());

			$code = $th->getCode() ?? '000';

			http_response_code(400);
			die('fail - ' . $code);
		}

		http_response_code(200);
		die('ok');
	}



	public function HandleFromCofidis(Request $request)
	{

		try {

			// set this config values to instance to later validate against callback data
			$this->isCallbackActive = $this->registry->config->get('payment_cofidis_activate_callback');
			$this->antiPhishingKey = $this->registry->config->get('payment_cofidis_anti_phishing_key');

			$transactionId = isset($request->get['transaction_id']) ? $request->get['transaction_id'] : '';
			$orderId = isset($request->get['order_id']) ? $request->get['order_id'] : '';

			// check if there is a record in cofidis table
			$this->registry->load->model('extension/ifthenpay/payment/cofidis');
			$storedData = $this->registry->model_extension_ifthenpay_payment_cofidis->getCofidisRecordByTransactionId($transactionId);

			if (!empty($storedData)) {
				$this->processCallbackForCofidis($request, $storedData);
			} else {


				$this->registry->load->model('extension/ifthenpay/payment/ifthenpaygateway');
				$storedData = $this->registry->model_extension_ifthenpay_payment_ifthenpaygateway->getIfthenpayGatewayRecordByOrderId($orderId);

				if (!empty($storedData)) {

					$this->processCallbackForIfthenpayGateway($request, $storedData);
				}

				if (empty($storedData)) {
					// no payment method data was found in local tables of ifthenpay
					throw new \Exception('StoredPaymentData not found in local table.', 10);
				}
			}
		} catch (\Throwable $th) {
			$this->logger->write('IFTHENPAY - HandleFromCofidis - ERROR : ' . $th->getMessage());

			$code = $th->getCode() ?? '000';

			http_response_code(400);
			die('fail - ' . $code);
		}

		http_response_code(200);
		die('ok');
	}



	public function HandleFromIfthenpayGateway(Request $request)
	{

		try {

			// set this config values to instance to later validate against callback data
			$this->isCallbackActive = $this->registry->config->get('payment_ifthenpaygateway_activate_callback');
			$this->antiPhishingKey = $this->registry->config->get('payment_ifthenpaygateway_anti_phishing_key');


			$orderId = isset($request->get['order_id']) ? $request->get['order_id'] : '';

			// check if there is a record in ifthenpaygateway table
			$this->registry->load->model('extension/ifthenpay/payment/ifthenpaygateway');
			$storedDataIfthenpaygateway = $this->registry->model_extension_ifthenpay_payment_ifthenpaygateway->getIfthenpayGatewayRecordByOrderId($orderId);

			if (!empty($storedDataIfthenpaygateway)) {
				$this->processCallbackForIfthenpayGateway($request, $storedDataIfthenpaygateway);
			} else {


				// in this function the payment method is unknown, so we are forced to check all payment method tables that work with callback
				foreach (self::PAYMENT_METHODS_WITH_CALLBACK as $paymentMethod) {

					$modelName = 'model_extension_ifthenpay_payment_' . $paymentMethod;
					$this->registry->load->model('extension/ifthenpay/payment/' . $paymentMethod);
					$storedData = $this->registry->$modelName->getRecordByOrderId($orderId);


					if (!empty($storedData)) {


						if ($storedData['status'] === 'paid') {
							http_response_code(200);
							die('ok - encomenda já se encontra paga');
						}


						switch ($paymentMethod) {
							case 'multibanco':
								$this->processCallbackForMultibanco($request, $storedData);
								break;
							case 'mbway':
								$this->processCallbackForMbway($request, $storedData);
								break;
							case 'payshop':
								$this->processCallbackForPayshop($request, $storedData);
								break;
							case 'cofidis':
								$this->processCallbackForCofidis($request, $storedData);
								break;

							default:
								throw new \Exception('Payment method unknown.', 5);
								break;
						}

						break;
					}
				}

				if (empty($storedDataIfthenpaygateway)) {
					// no payment method data was found in local tables of ifthenpay
					throw new \Exception('StoredPaymentData not found in local table.', 10);
				}
			}
		} catch (\Throwable $th) {
			$this->logger->write('IFTHENPAY - HandleFromIfthenpayGateway - ERROR : ' . $th->getMessage());

			$code = $th->getCode() ?? '000';

			http_response_code(400);
			die('fail - ' . $code);
		}

		http_response_code(200);
		die('ok');
	}







	/* -------------------------------------------------------------------------- */
	/*                                 MULTIBANCO                                 */
	/* -------------------------------------------------------------------------- */



	private function processCallbackForMultibanco(Request $request, array $storedData)
	{
		$this->registry->load->model('extension/ifthenpay/payment/multibanco');


		$this->validateCallbackMultibanco($request->get, $storedData);

		// update order history status
		$this->registry->model_checkout_order->addHistory($storedData['order_id'], (int) $this->registry->config->get('payment_multibanco_paid_status_id'), $this->registry->language->get('comment_paid'), true);

		// update multibanco table record
		$this->registry->model_extension_ifthenpay_payment_multibanco->updateMultibancoRecordStatus($storedData['order_id'], 'paid');

		http_response_code(200);
		die('ok');
	}



	private function validateCallbackMultibanco($callbackData, $storedPaymentData): void
	{
		if (!isset($callbackData['reference'])) {
			throw new \Exception('Reference not present in callback data.', 20);
		}

		if ($callbackData['reference'] != $storedPaymentData['reference']) {
			throw new \Exception('Reference not present in callback data.', 25);
		}

		// is callback active?
		if (!$this->isCallbackActive) {
			throw new \Exception('Callback is not active.', 30);
		}
		// is anti-phishing key valid?
		if (($callbackData['phish_key'] == '') || ($callbackData['phish_key'] != $this->antiPhishingKey)) {
			throw new \Exception('Invalid anti-phishing key.', 40);
		}

		// is order id valid? does it exist?
		$order = $this->registry->model_checkout_order->getOrder($storedPaymentData['order_id']);
		if (!$order) {
			throw new \Exception('Order not found.', 50);
		}

		// is order amount valid?
		$callbackAmount = $callbackData['amount'];
		$formatedAmount = $this->registry->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
		$formatedAmount = (string) round($formatedAmount, 2);

		if ($callbackAmount != $formatedAmount) {
			throw new \Exception('Invalid amount.', 60);
		}
	}



	/* -------------------------------------------------------------------------- */
	/*                                 MB WAY                                     */
	/* -------------------------------------------------------------------------- */



	private function processCallbackForMbway(Request $request, array $storedData)
	{
		$this->registry->load->model('extension/ifthenpay/payment/mbway');


		$this->validateCallbackMbway($request->get, $storedData);

		// update order history status
		$this->registry->model_checkout_order->addHistory($storedData['order_id'], (int) $this->registry->config->get('payment_mbway_paid_status_id'), $this->registry->language->get('comment_paid'), true);

		// update mbway table record
		$this->registry->model_extension_ifthenpay_payment_mbway->updateMbwayRecordStatus($storedData['order_id'], 'paid');

		http_response_code(200);
		die('ok');
	}



	private function validateCallbackMbway($callbackData, $storedPaymentData): void
	{

		if (!isset($callbackData['transaction_id'])) {
			throw new \Exception('Transaction not present in callback data.', 20);
		}


		if ($callbackData['transaction_id'] != $storedPaymentData['transaction_id']) {
			throw new \Exception('Transaction ID not present in callback data.', 25);
		}

		// is callback active?
		if (!$this->isCallbackActive) {
			throw new \Exception('Callback is not active.', 30);
		}
		// is anti-phishing key valid?
		if (($callbackData['phish_key'] == '') || ($callbackData['phish_key'] != $this->antiPhishingKey)) {
			throw new \Exception('Invalid anti-phishing key.', 40);
		}

		// is order id valid? does it exist?
		$order = $this->registry->model_checkout_order->getOrder($storedPaymentData['order_id']);
		if (!$order) {
			throw new \Exception('Order not found.', 50);
		}

		// is order amount valid?
		$callbackAmount = $callbackData['amount'];
		$formatedAmount = $this->registry->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
		$formatedAmount = (string) round($formatedAmount, 2);

		if ($callbackAmount != $formatedAmount) {
			throw new \Exception('Invalid amount.', 60);
		}
	}




	/* -------------------------------------------------------------------------- */
	/*                                PAYSHOP                                     */
	/* -------------------------------------------------------------------------- */



	private function processCallbackForPayshop(Request $request, array $storedData)
	{
		$this->registry->load->model('extension/ifthenpay/payment/payshop');


		$this->validateCallbackPayshop($request->get, $storedData);

		// update order history status
		$this->registry->model_checkout_order->addHistory($storedData['order_id'], (int) $this->registry->config->get('payment_payshop_paid_status_id'), $this->registry->language->get('comment_paid'), true);

		// update payshop table record
		$this->registry->model_extension_ifthenpay_payment_payshop->updatePayshopRecordStatus($storedData['order_id'], 'paid');

		http_response_code(200);
		die('ok');
	}



	private function validateCallbackPayshop($callbackData, $storedPaymentData): void
	{

		if (!isset($callbackData['reference'])) {
			throw new \Exception('Reference not present in callback data.', 20);
		}


		if ($callbackData['reference'] != $storedPaymentData['reference']) {
			throw new \Exception('Reference not present in callback data.', 25);
		}

		// is callback active?
		if (!$this->isCallbackActive) {
			throw new \Exception('Callback is not active.', 30);
		}
		// is anti-phishing key valid?
		if (($callbackData['phish_key'] == '') || ($callbackData['phish_key'] != $this->antiPhishingKey)) {
			throw new \Exception('Invalid anti-phishing key.', 40);
		}

		// is order id valid? does it exist?
		$order = $this->registry->model_checkout_order->getOrder($storedPaymentData['order_id']);
		if (!$order) {
			throw new \Exception('Order not found.', 50);
		}

		// is order amount valid?
		$callbackAmount = $callbackData['amount'];
		$formatedAmount = $this->registry->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
		$formatedAmount = (string) round($formatedAmount, 2);

		if ($callbackAmount != $formatedAmount) {
			throw new \Exception('Invalid amount.', 60);
		}
	}



	/* -------------------------------------------------------------------------- */
	/*                                COFIDIS                                     */
	/* -------------------------------------------------------------------------- */



	private function processCallbackForCofidis(Request $request, array $storedData)
	{
		$this->registry->load->model('extension/ifthenpay/payment/cofidis');


		$this->validateCallbackCofidis($request->get, $storedData);

		// update order history status
		$this->registry->model_checkout_order->addHistory($storedData['order_id'], (int) $this->registry->config->get('payment_cofidis_paid_status_id'), $this->registry->language->get('comment_paid'), true);

		// update cofidis table record
		$this->registry->model_extension_ifthenpay_payment_cofidis->updateCofidisRecordStatusByTransactionId($storedData['transaction_id'], 'paid');

		http_response_code(200);
		die('ok');
	}



	private function validateCallbackCofidis($callbackData, $storedPaymentData): void
	{

		if (!isset($callbackData['transaction_id'])) {
			throw new \Exception('Transaction not present in callback data.', 20);
		}


		if ($callbackData['transaction_id'] != $storedPaymentData['transaction_id']) {
			throw new \Exception('Transaction ID not present in callback data.', 25);
		}

		// is callback active?
		if (!$this->isCallbackActive) {
			throw new \Exception('Callback is not active.', 30);
		}
		// is anti-phishing key valid?
		if (($callbackData['phish_key'] == '') || ($callbackData['phish_key'] != $this->antiPhishingKey)) {
			throw new \Exception('Invalid anti-phishing key.', 40);
		}

		// is order id valid? does it exist?
		$order = $this->registry->model_checkout_order->getOrder($storedPaymentData['order_id']);
		if (!$order) {
			throw new \Exception('Order not found.', 50);
		}

		// is order amount valid?
		$callbackAmount = $callbackData['amount'];
		$formatedAmount = $this->registry->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
		$formatedAmount = (string) round($formatedAmount, 2);

		if ($callbackAmount != $formatedAmount) {
			throw new \Exception('Invalid amount.', 60);
		}
	}



	/* -------------------------------------------------------------------------- */
	/*                              IFTHENPAYGATEWAY                              */
	/* -------------------------------------------------------------------------- */



	private function processCallbackForIfthenpayGateway(Request $request, array $storedDataIfthenpaygateway)
	{
		$orderId = isset($request->get['order_id']) ? $request->get['order_id'] : '';


		// validate callback
		$this->validateCallbackIfthenpayGateway($storedDataIfthenpaygateway, $request->get);

		// execute ifthenpaygateway callback strategy
		$this->registry->load->model('extension/ifthenpay/payment/ifthenpaygateway');
		$this->registry->load->language('extension/ifthenpay/payment/ifthenpaygateway');

		// update order history status
		$this->registry->model_checkout_order->addHistory($orderId, (int) $this->registry->config->get('payment_ifthenpaygateway_paid_status_id'), $this->registry->language->get('comment_paid'), true);

		// update ifthenpaygateway table record
		$this->registry->model_extension_ifthenpay_payment_ifthenpaygateway->updateIfthenpaygatewayRecordStatus($storedDataIfthenpaygateway['order_id'], 'paid');



		$this->logger->write('IFTHENPAY -  IFTHENPAYGATEWAY - callback success: ' . json_encode(['orderId' => $storedDataIfthenpaygateway['order_id'], 'status' => 'paid']));
	}



	private function validateCallbackIfthenpayGateway(array $storedDataIfthenpaygateway, $callbackData)
	{

		if ($storedDataIfthenpaygateway['status'] === 'paid') {
			http_response_code(200);
			die('ok - encomenda já se encontra paga');
		}

		// is callback active?
		if (!$this->isCallbackActive) {
			throw new \Exception('Callback is not active.', 30);
		}
		// is anti-phishing key valid?
		if (($callbackData['phish_key'] == '') || ($callbackData['phish_key'] != $this->antiPhishingKey)) {
			throw new \Exception('Invalid anti-phishing key.', 40);
		}

		// is order id valid? does it exist?
		$order = $this->registry->model_checkout_order->getOrder($storedDataIfthenpaygateway['order_id']);
		if (!$order) {
			throw new \Exception('Order not found.', 50);
		}

		// is order amount valid?
		$callbackAmount = $callbackData['amount'];
		$formatedAmount = $this->registry->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
		$formatedAmount = (string) round($formatedAmount, 2);

		if ($callbackAmount != $formatedAmount) {
			throw new \Exception('Invalid amount.', 60);
		}
	}
}

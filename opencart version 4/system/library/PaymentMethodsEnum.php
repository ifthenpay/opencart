<?php

namespace Ifthenpay;

enum PaymentMethodsEnum: string
{
	case MULTIBANCO = 'multibanco';
	case MBWAY = 'mbway';
	case PAYSHOP = 'payshop';
	case CCARD = 'ccard';
	case COFIDIS = 'cofidis';
	case IFTHENPAYGATEWAY = 'ifthenpaygateway';
	case PIX = 'pix';
}
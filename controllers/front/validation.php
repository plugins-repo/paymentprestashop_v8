<?php

class PaymentPrestashop_v8ValidationModuleFrontController extends ModuleFrontController
{
    function generateRandomString($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function postProcess()
    {
        $merchantId = Configuration::get('PAYMENTPRESTASHOP_V8_MERCHANT_ID');
        $partnerName = Configuration::get('PAYMENTPRESTASHOP_V8_PARTNER_NAME');
        $paymentUrl = Configuration::get('PAYMENTPRESTASHOP_V8_TEST_URL');
        $secureKey = Configuration::get('PAYMENTPRESTASHOP_V8_SECUREKEY');
        $redirectUrl = Configuration::get('PAYMENTPRESTASHOP_V8_REDIRECT_URL');

        $merchantTransactionId = $this->generateRandomString();
        $amount = (float)$this->context->cart->getOrderTotal(true, Cart::BOTH);
        $data = $merchantId . '|' . $partnerName . '|' . $amount . '|' . $merchantTransactionId . '|' . $redirectUrl. '|' . $secureKey;
        $checksum = md5($data);

        $payload = array(
            'toid' => $merchantId,
            'totype' => $partnerName,
            'description' => $merchantTransactionId,
            'amount' => (float)$this->context->cart->getOrderTotal(true, Cart::BOTH),
            'currency' => $this->context->currency->iso_code,
            'merchantRedirectUrl' => $redirectUrl,
            'checksum' => $checksum,
        );

        $this->context->smarty->assign(array(
            'paymentUrl' => $paymentUrl,
            'payload' => $payload,
        ));


        $this->setTemplate('module:paymentprestashop_v8/views/templates/front/redirect.tpl');
    }
}


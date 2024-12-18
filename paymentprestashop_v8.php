<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class PaymentPrestashop_v8 extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'paymentprestashop_v8';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.0';
        $this->author = 'PaymentPrestashop_v8';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PaymentPrestashop_v8 Prestashop');
        $this->description = $this->l('Accept payments for your products via paymentprestashop_v8 Prestashop.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('PAYMENTPRESTASHOP_V8_MERCHANT_ID')) {
            $this->warning = $this->l('No merchant ID provided');
        }
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('paymentOptions');
    }


    public function uninstall()
    {
        return parent::uninstall() &&
            Configuration::deleteByName('PAYMENTPRESTASHOP_V8_MERCHANT_ID') &&
            Configuration::deleteByName('PAYMENTPRESTASHOP_V8_PARTNER_NAME') &&
            Configuration::deleteByName('PAYMENTPRESTASHOP_V8_REDIRECT_URL') &&
            Configuration::deleteByName('PAYMENTPRESTASHOP_V8_SECUREKEY') &&
            Configuration::deleteByName('PAYMENTPRESTASHOP_V8_TEST_URL');
    }

    public function getContent()
    {
        $output = null;

        // $logoUrl = '../modules/' . $this->name . '/views/img/logo.png';
        // $output .= '<img src="' . $logoUrl . '" style="max-width: 150px; margin-bottom: 20px;" />';

        if (Tools::isSubmit('submit' . $this->name)) {
            $merchantId = strval(Tools::getValue('PAYMENTPRESTASHOP_V8_MERCHANT_ID'));
            $partnerName = strval(Tools::getValue('PAYMENTPRESTASHOP_V8_PARTNER_NAME'));
            $redirectUrl = strval(Tools::getValue('PAYMENTPRESTASHOP_V8_REDIRECT_URL'));
            $secureKey = strval(Tools::getValue('PAYMENTPRESTASHOP_V8_SECUREKEY'));
            if (!$merchantId || empty($merchantId)) {
                $output .= $this->displayError($this->l('Invalid Merchant ID'));
            } else {
                Configuration::updateValue('PAYMENTPRESTASHOP_V8_MERCHANT_ID', $merchantId);
                Configuration::updateValue('PAYMENTPRESTASHOP_V8_PARTNER_NAME', $partnerName);
                Configuration::updateValue('PAYMENTPRESTASHOP_V8_REDIRECT_URL', $redirectUrl);
                Configuration::updateValue('PAYMENTPRESTASHOP_V8_SECUREKEY', $secureKey);
                Configuration::updateValue('PAYMENTPRESTASHOP_V8_TEST_URL', Tools::getValue('PAYMENTPRESTASHOP_V8_TEST_URL'));
                
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Merchant ID'),
                        'name' => 'PAYMENTPRESTASHOP_V8_MERCHANT_ID',
                        'size' => 20,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Partner Name'),
                        'name' => 'PAYMENTPRESTASHOP_V8_PARTNER_NAME',
                        'size' => 20,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Secure Key'),
                        'name' => 'PAYMENTPRESTASHOP_V8_SECUREKEY',
                        'size' => 50,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Redirect Url'),
                        'name' => 'PAYMENTPRESTASHOP_V8_REDIRECT_URL',
                        'size' => 50,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Test Mode URL'),
                        'name' => 'PAYMENTPRESTASHOP_V8_TEST_URL',
                        'size' => 50,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right'
                )
            )
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'PAYMENTPRESTASHOP_V8_MERCHANT_ID' => Tools::getValue('PAYMENTPRESTASHOP_V8_MERCHANT_ID', Configuration::get('PAYMENTPRESTASHOP_V8_MERCHANT_ID')),
            'PAYMENTPRESTASHOP_V8_PARTNER_NAME' => Tools::getValue('PAYMENTPRESTASHOP_V8_PARTNER_NAME', Configuration::get('PAYMENTPRESTASHOP_V8_PARTNER_NAME')),
            'PAYMENTPRESTASHOP_V8_SECUREKEY' => Tools::getValue('PAYMENTPRESTASHOP_V8_SECUREKEY', Configuration::get('PAYMENTPRESTASHOP_V8_SECUREKEY')),
            'PAYMENTPRESTASHOP_V8_REDIRECT_URL' => Tools::getValue('PAYMENTPRESTASHOP_V8_REDIRECT_URL', Configuration::get('PAYMENTPRESTASHOP_V8_REDIRECT_URL')),
            'PAYMENTPRESTASHOP_V8_TEST_URL' => Tools::getValue('PAYMENTPRESTASHOP_V8_TEST_URL', Configuration::get('PAYMENTPRESTASHOP_V8_TEST_URL')),
        );
    }

    
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        $payment_options = [
            $this->getExternalPaymentOption(),
        ];

        return $payment_options;
    }

    public function getExternalPaymentOption()
    {
        $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $newOption->setCallToActionText($this->l('Pay using Paymentprestashop_v8 Payment Gateway'))
                  ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
                  ->setAdditionalInformation($this->context->smarty->fetch('module:paymentprestashop_v8/views/templates/hook/payment.tpl'));

        return $newOption;
    }
}

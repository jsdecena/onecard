<?php
/*
*
* Author: Jeff Simons Decena @2013
*
*/

if (!defined('_PS_VERSION_'))
	exit;

class Onecard extends PaymentModule
{
	private $form_action;
	private $merchant_id;
	private $trans_key;
	private $password;
	private $keyword;
	private $trans_time;
	private $curr;

	public function __construct()
	{
	$this->name = 'onecard';
	$this->tab = 'payments_gateways';
	$this->version = '0.1';
	$this->author = 'Jeff Simons Decena';
	$this->need_instance = 0;
	$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');	

	$this->form_action 			= Configuration::get('ONECARD-LINK');
	$this->merchant_id 			= Configuration::get('ONECARD-MERCHANT-ID');
	$this->trans_key 			= Configuration::get('ONECARD-TRANSKEY-ID');
	$this->password 			= Configuration::get('ONECARD-PASSWORD-ID');
	$this->keyword 				= Configuration::get('ONECARD-KEYWORD-ID');
	$this->trans_time			= time() * 1000;

	parent::__construct();

	$this->curr 				= Currency::getDefaultCurrency();

	$this->displayName = $this->l('OneCard Module');
	$this->description = $this->l('OneCard configuration module');

	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

	if (!Configuration::get('ONECARD-LINK'))      
	  $this->warning = $this->l('No link provided');
	}

	public function install()
	{
	  return parent::install() &&
	  	Configuration::updateValue('ONECARD-LINK', 'http://onecard.n2vsb.com/customer/integratedPayment.html') &&
	  	$this->registerHook('payment') &&
	  	$this->registerHook('footer') &&
	  	Configuration::updateValue('PS_OS_ONECARD_PAYMENT', $this->create_order_state('OneCard Payment', 'oc_payment', 'blue') );
	}	

	public function uninstall()
	{
	  return parent::uninstall() && 
	  	Configuration::deleteByName('ONECARD-LINK') &&
	  	Configuration::deleteByName('ONECARD-MERCHANT-ID') &&
	  	Configuration::deleteByName('ONECARD-TRANSKEY-ID') &&
	  	Configuration::deleteByName('ONECARD-KEYWORD-ID') &&
	  	Configuration::deleteByName('ONECARD-PASSWORD-ID');
	}

	public function getContent()
	{
	    $output = null;
	 
	    if (Tools::isSubmit('submit'. $this->name))
	    {
            Configuration::updateValue('ONECARD-LINK', Tools::getValue('ONECARD-LINK'));
            Configuration::updateValue('ONECARD-MERCHANT-ID', Tools::getValue('ONECARD-MERCHANT-ID'));
            Configuration::updateValue('ONECARD-TRANSKEY-ID', Tools::getValue('ONECARD-TRANSKEY-ID'));
            Configuration::updateValue('ONECARD-PASSWORD-ID', Tools::getValue('ONECARD-PASSWORD-ID'));
            Configuration::updateValue('ONECARD-KEYWORD-ID', Tools::getValue('ONECARD-KEYWORD-ID'));
            $output .= $this->displayConfirmation($this->l('Settings updated'));
	    }
	    return $output.$this->displayForm();
	}

	public function displayForm()
	{
	    // Get default Language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('Settings'),
	        ),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('ONECARD-LINK'),
	                'name' => 'ONECARD-LINK',
	                'size' => 20,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('ONECARD-MERCHANT-ID'),
	                'name' => 'ONECARD-MERCHANT-ID',
	                'size' => 20,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('ONECARD-TRANSKEY-ID'),
	                'name' => 'ONECARD-TRANSKEY-ID',
	                'size' => 20,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('ONECARD-PASSWORD-ID'),
	                'name' => 'ONECARD-PASSWORD-ID',
	                'size' => 20,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('ONECARD-KEYWORD-ID'),
	                'name' => 'ONECARD-KEYWORD-ID',
	                'size' => 20,
	                'required' => true
	            )	            
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'button'
	        )
	    );
	     
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	     
	    // Load current value
	    $helper->fields_value['ONECARD-LINK'] 			= $this->form_action;	    
	    $helper->fields_value['ONECARD-MERCHANT-ID'] 	= Configuration::get('ONECARD-MERCHANT-ID');
	    $helper->fields_value['ONECARD-TRANSKEY-ID'] 	= Configuration::get('ONECARD-TRANSKEY-ID');
	    $helper->fields_value['ONECARD-PASSWORD-ID'] 	= Configuration::get('ONECARD-PASSWORD-ID');
	    $helper->fields_value['ONECARD-KEYWORD-ID'] 	= Configuration::get('ONECARD-KEYWORD-ID');
	     
	    return $helper->generateForm($fields_form);
	}

	public function hookPayment($params)
	{
		$this->smarty->assign(
			array(
				'link' 			=> $this->form_action,
				'merchant_id'	=> $this->merchant_id,
				'trans_id' 		=> $this->context->cart->id,
				'amount' 		=> $this->context->cart->getOrderTotal(),
				'currency'		=> $this->curr->iso_code,
				'time'			=> $this->trans_time,
				'return_url'	=> $this->context->link->getModuleLink('onecard', 'validation'),
				'hash'			=> $this->calcHash()
			)
		);

		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookFooter($params)
	{
		$this->context->controller->addJS($this->_path.'onecard.js');
	}

	private function calcHash()
	{
		$hash = MD5 (
			$this->merchant_id .
			$this->context->cart->id . //ID CART USED FOR TRANS REFERENCE ID
			$this->context->cart->getOrderTotal() .
			$this->curr->iso_code .
			$this->trans_time . 
			$this->trans_key
		);

		return $hash;
	}

    public static function create_order_state($label = 'PS_NEW_STATUS', $template = null, $color = 'DarkOrange')
    {
        //Create the new status
        $os = new OrderState();
        $os->name = array(
            '1' => $label,
            '2' => '',
            '3' => ''
        );

        $os->invoice = false;
        $os->unremovable = true;
        $os->color = $color;
        $os->template = $template;
        $os->send_email = false;

        $os->save();
        
        return $os->id;
    }	
}
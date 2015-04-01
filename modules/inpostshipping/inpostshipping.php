<?php
//
// 2015 InPost UK Ltd.
//
// NOTICE OF LICENCE
//
// This source file is created and maintained by InPost. It is provided 'as is'
// with no promises or guarentees.
//
// DISCLAIMER
//
// Do not edit or add to this file.
//
// @copyright 2015 InPost UK Ltd.
//

if (!defined('_PS_VERSION_'))
{
	exit;
}

include_once dirname(__FILE__).'/classes/InpostParcelDetails.php';
include_once dirname(__FILE__).'/classes/InpostParcelsTools.php';

class InpostShipping extends Module
{
	private $_configuration_values = array(
		'IP_UK_API_URL',
		'IP_UK_API_KEY',
		'IP_UK_LABEL_FORMAT',
		'IP_UK_REVERSE_RETURN',
		'IP_UK_AUTO_REVERSE_RETURN'
	);

	///
	// __construct
	//
	public function __construct()
	{
		$this->name          = 'inpostshipping';
		$this->tab           = 'shipping_logistics';
		$this->version       = '1.0';
		$this->author        = 'InPost UK';
		$this->className     = 'InpostParcelDetails';
		$this->need_instance = 1;
		$this->secure_key    = Tools::encrypt($this->name);

		parent::__construct();

		$this->displayName = $this->l('InPost Shipping');
		$this->ps_versions_compliancy = array('min' => '1.6',
			'max' => _PS_VERSION_);
		$this->description = $this->l('Allow customers to ship to InPost lockers.');
	}

	///
	// install
	//
	public function install()
	{
		// Check to see if the module is already installed.
		// Do nothing if it is already installed.
		if (Db::getInstance()->getValue('SELECT `id_module` FROM `'.
			_DB_PREFIX_.'module` WHERE name =\''.
			pSQL($this->name).'\''))
		{
			return true;
		}

		if (!$this->installDb() ||
			!$this->installTab() ||
			!$this->setupGlobals() ||
			!parent::install() ||
			!$this->registerHook('displayBackOfficeHeader') ||
			!$this->registerHook('actionOrderStatusPostUpdate'))
		{
			return false;
		}

		return true;
	}

	///
	// uninstall
	//
	public function uninstall()
	{
		if (!parent::uninstall() ||
			!$this->uninstallTab() ||
			!$this->removeGlobals() ||
			!$this->uninstallDb() ||
			!$this->unregisterHook('displayBackOfficeHeader') ||
			!$this->unregisterHook('actionOrderStatusPostUpdate'))
		{
			return false;
		}

		return true;
	}

	///
	// installTab
	//
	public function installTab()
	{
		$tab             = new Tab();
		$tab->active     = 1;
		$tab->class_name = 'AdminInpostShipping';
		$tab->name       = array();
		foreach (Language::getLanguages(true) as $lang)
		{
			$tab->name[$lang['id_lang']] = 'Inpost Orders';
		}
		$tab->id_parent  = (int)Tab::getIdFromClassName('AdminOrders');
		$tab->module     = $this->name;

		return $tab->add();
	}

	///
	// uninstallTab
	//
	public function uninstallTab()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminInpostShipping');
		if ($id_tab)
		{
			$tab = new Tab($id_tab);
			return $tab->delete();
		}
		else
		{
			return false;
		}
	}

	///
	// install Db
	//
	public function installDb()
	{
		$return = true;
		include(dirname(__FILE__).'/sql_install.php');
		foreach ($sql as $s)
		{
			$return &= Db::getInstance()->execute($s);
		}
		return $return;
	}

	///
	// uninstallDb
	//
	public function uninstallDb()
	{
		include(dirname(__FILE__).'/sql_install.php');
		foreach ($sql as $name => $v)
		{
			Db::getInstance()->execute('DROP TABLE '.$name);
		}
		return true;
	}

	///
	// setupGlobals
	//
	// @brief Set up the global InPost configuration values
	//
	public function setupGlobals()
	{
		$ret = true;

		foreach($this->_configuration_values as $value)
		{
			$ret = Configuration::updateGlobalValue($value);

			if(!$ret)
			{
				error_log('Failed to create / update GlobalValue ' . $value);
				break;
			}
		}

		return $ret;
	}

	///
	// removeGlobals
	//
	// @brief Remove the global InPost configuration values
	//
	public function removeGlobals()
	{
		$ret = true;

		foreach($this->_configuration_values as $value)
		{
			$ret = Configuration::deleteByName($value);

			if(!$ret)
			{
				error_log('Failed to delete GlobalValue ' . $value);
				break;
			}
		}

		return $ret;
	}

	///
	// isUpdating
	//
	public function isUpdating()
	{
		$db_version = Db::getInstance()->getValue('SELECT `version` FROM `'._DB_PREFIX_.'module` WHERE `name` = \''.pSQL($this->name).'\'');

		return version_compare($this->version, $db_version, '>');
	}

	///
	// hookdisplayBackOfficeHeader
	//
	public function hookdisplayBackOfficeHeader($params)
	{
		// Check if currently updatingcheck if module is currently
		// processing update
		if ($this->isUpdating() || !Module::isEnabled($this->name))
		{
			return false;
		}

		if (method_exists($this->context->controller, 'addJquery'))
		{
			$this->context->controller->addJquery();
			$this->context->controller->addCss($this->_path.'views/css/inpostshipping.css');

			$js_str = '';

			return '<script>
				var ids_ps_advice = new Array('.rtrim($js_str, ',').');
				var admin_inpostshipping_ajax_url = \''.$this->context->link->getAdminLink('AdminInpostshipping').'\';
				var current_id_tab = '.(int)$this->context->controller->id.';
			</script>';
		}
	}

	///
	// getContent
	//
	public function getContent()
	{
		$output = null;

		if (Tools::isSubmit('submit'.$this->name))
		{
			// Check the POST values of the fields.
			$url    = strval(Tools::getValue('IP_UK_API_URL'));
			$key    = strval(Tools::getValue('IP_UK_API_KEY'));
			$label  = strval(Tools::getValue('IP_UK_LABEL_FORMAT'));
			$return = Tools::getValue('IP_UK_REVERSE_RETURN');
			$auto   = Tools::getValue('IP_UK_AUTO_REVERSE_RETURN');

			if (!$url || empty($url) || !Validate::isUrl($url))
			{
				$output .= $this->displayError($this->l('URL must be filled.'));
			}
			if (!$key || empty($key) || !Validate::isString($key))
			{
				$output .= $this->displayError($this->l('API KEY must be filled.'));
			}
			if (!$label || empty($label) || !Validate::isString($label))
			{
				$output .= $this->displayError($this->l('API KEY must be filled.'));
			}
			if (!Validate::isBool($return))
			{
				$output .= $this->displayError($this->l('Please Select if return labels are allowed.'));
			}
			if (!Validate::isBool($auto))
			{
				$output .= $this->displayError($this->l('Please select if automatic return labels are to be created.'));
			}

			if (!$return && $auto)
			{
				$output .= $this->displayError($this->l('Cannot create automatic returns with returns switched off.'));
			}

			if(!$output)
			{
				// Save the values into the configuration
				// table.
				Configuration::updateValue('IP_UK_API_URL', $url);
				Configuration::updateValue('IP_UK_API_KEY', $key);
				Configuration::updateValue('IP_UK_LABEL_FORMAT', $label);
				Configuration::updateValue('IP_UK_REVERSE_RETURN', $return);
				Configuration::updateValue('IP_UK_AUTO_REVERSE_RETURN', $auto);
				$output .= $this->displayConfirmation($this->l('Settings Updated'));
			}
		}
		return $output.$this->displayForm();
	}

	///
	// displayForm
	//
	public function displayForm()
	{
		// Get default language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		// Options for the label format select
		$label_format = array(
			array('id_option' => 'Pdf',
				'name' => 'PDF'),
			array('id_option' => 'Epl2',
				'name' => 'Epl2')
		);
		// Options for the Return label select
		$yes_no_option = array(
			array('id_option' => '',
				'name' => '-Please Select-'),
			array('id_option' => '1',
				'name' => 'Yes'),
			array('id_option' => '0',
				'name' => 'No')
		);

		$data_label_format = Configuration::get('IP_UK_LABEL_FORMAT');
		$data_reverse      = Configuration::get('IP_UK_REVERSE_RETURN');
		$data_auto_reverse = Configuration::get('IP_UK_AUTO_REVERSE_RETURN');

		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
			),
			'input' => array(
				array(
					'type'     => 'text',
					'label'    => $this->l('API URL'),
					'name'     => 'IP_UK_API_URL',
					'size'     => 40,
					'required' => true
				),
				array(
					'type'     => 'text',
					'label'    => $this->l('API Key'),
					'name'     => 'IP_UK_API_KEY',
					'size'     => 40,
					'required' => true
				),
				array(
					'type'     => 'select',
					'label'    => $this->l('Label Format'),
					'name'     => 'IP_UK_LABEL_FORMAT',
					'required' => true,
					'default_value' => $data_label_format,
					'options'  => array(
						'query' => $label_format,
						'id'    => 'id_option',
						'name'  => 'name'
					)
				),
				array(
					'type'     => 'select',
					'label'    => $this->l('Allow Reverse Returns'),
					'name'     => 'IP_UK_REVERSE_RETURN',
					'required' => true,
					'default_value' => $data_reverse,
					'options'  => array(
						'query' => $yes_no_option,
						'id'    => 'id_option',
						'name'  => 'name'
					),
				),
				array(
					'type'     => 'select',
					'label'    => $this->l('Auto Create Reverse Returns'),
					'name'     => 'IP_UK_AUTO_REVERSE_RETURN',
					'required' => true,
					'default_value' => $data_auto_reverse,
					'options'  => array(
						'query' => $yes_no_option,
						'id'    => 'id_option',
						'name'  => 'name'
					),
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module          = $this;
		$helper->name_controller = $this->name;
		$helper->token           = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex    = AdminController::$currentIndex.'&configure='.$this->name;

		// Language
		$helper->default_form_language     = $default_lang;
		$helper->allow_employess_form_lang = $default_lang;

		// Title and toolbar
		$helper->title          = $this->displayName;
		$helper->show_toolbar   = true;
		$helper->toolbar_scroll = true;
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' => array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back')
			)
		);

		// Load current value
		$helper->fields_value['IP_UK_API_URL']        = Configuration::get('IP_UK_API_URL');
		$helper->fields_value['IP_UK_API_KEY']        = Configuration::get('IP_UK_API_KEY');
		$helper->fields_value['IP_UK_LABEL_FORMAT']   = Configuration::get('IP_UK_LABEL_FORMAT');
		$helper->fields_value['IP_UK_REVERSE_RETURN'] = Configuration::get('IP_UK_REVERSE_RETURN');
		$helper->fields_value['IP_UK_AUTO_REVERSE_RETURN'] = Configuration::get('IP_UK_AUTO_REVERSE_RETURN');

		return $helper->generateForm($fields_form);
	}

	///
	// hookactionOrderStatusPostUpdate
	//
	// @param Mixed
	// @brief Check if the order was completed still as an InPost one.
	//
	public function hookactionOrderStatusPostUpdate($params)
	{
		$order_id   = $params['id_order'];
		$cart_id    = $params['cart']->id;
		$carrier_id = $params['cart']->id_carrier;
		$email      = $params['cookie']->__get('email');

		$carrier = new Carrier($carrier_id);

		if (strcasecmp($carrier->name, 'InPost') != 0)
		{
			// Not an InPost order do nothing.
			return;
		}

		$ids = array();

		// Now check to see if there is a possible InPost order for
		// the cart.
		$ids = InpostParcelDetails::check_inpost_cart_order_exists($cart_id);

		if (count($ids) == 0)
		{
			// No possible orders found stop processing.
			return;
		}

		// Load the actual InPost order
		$inpost_order = new InpostParcelDetails((int)$ids[0]);

		// Get the target locker's address details.
		$address = InpostParcelsTools::get_machine_address(
				$inpost_order->parcel_target_machine_id);

		// Now change it's status and remove the tmp_id.
		$inpost_order->order_id              = $order_id;
		$inpost_order->parcel_status         = 'Created';
		$inpost_order->parcel_receiver_email = $email;
		$inpost_order->parcel_description    = 'Order #: ' . $order_id;
		$inpost_order->parcel_tmp_id         = '';
		$inpost_order->api_source            = InpostParcelsTools::getCurrentApi();
		$inpost_order->parcel_target_machine_town     = $address['city'];
		$inpost_order->parcel_target_machine_street   = $address['street'];
		$inpost_order->parcel_target_machine_building = $address['building_number'];
		// We are not ready for fill the data in for these fields.
		$inpost_order->sticker_creation_date = NULL;
		$inpost_order->return_parcel_expiry  = NULL;
		$inpost_order->return_parcel_created = NULL;

		$inpost_order->save();
	}

}

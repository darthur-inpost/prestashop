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

class InpostShippingCarrier extends CarrierModule
{
	public  $id_carrier;

	private $_html = '';
	private $_postErrors = array();
	private $_moduleName = 'inpostshippingcarrier';

	///
	// __construct
	//
	public function __construct()
	{
		$this->name    = 'inpostshippingcarrier';
		$this->tab     = 'shipping_logistics';
		$this->version = '1.0';
		$this->author  = 'InPost UK';
		$this->limited_countries = array('uk');

		parent::__construct();

		$this->displayName = $this->l('InPost Shipping Carrier');
		$this->ps_versions_compliancy = array('min' => '1.6',
			'max' => _PS_VERSION_);
		$this->description = $this->l('Allow customers to ship to InPost lockers.');

		if (self::isInstalled($this->name))
		{
			// Getting carrier list
			global $cookie;

			$carriers = Carrier::getCarriers($cookie->id_lang, true, false, false, NULL, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);

			// Saving id carrier list
			$id_carrier_list = array();
			foreach($carriers as $carrier)
			{
				$id_carrier_list[] .= $carrier['id_carrier'];
			}

			// Testing if Carrier Id exists
			$warning = array();
			if (!in_array((int)(Configuration::get('INPOSTSHIPPING_CARRIER_ID')), $id_carrier_list))
			{
				$warning[] .= $this->l('"InPost Carrier"').' ';
			}
			if (!Configuration::get('INPOST_UK_OVERCOST'))
			{
				$warning[] .= $this->l('"InPost Carrier Overcost"').' ';
			}
			if (count($warning))
				$this->warning .= implode(' , ',$warning).$this->l('must be configured to use this module correctly').' ';
		}
	}

	///
	// install
	//
	public function install()
	{
		$carrierConfig = array(
			0 => array(
				'name'               => 'InPost',
				'id_tax_rules_group' => 0,
				'active'             => true,
				'deleted'            => 0,
				'shipping_handling'  => false,
				'range_behavior'     => 1,
				'delay'              => array(
					'fr' => '48 hr delivery',
					'en' => '48 hr delivery',
					Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) => '48 hr delivery'),
				'id_zone'            => 1,
				'is_module'          => true,
				'shipping_external'  => true,
				'external_module_name' => 'inpostshippingcarrier',
				'need_range'         => true,
				'width'              => 38,
				'height'             => 38,
				'depth'              => 64,
				'weight'             => 15.0
			)
		);

		$id_carrier1 = $this->installExternalCarrier($carrierConfig[0]);
		Configuration::updateValue('INPOSTSHIPPING_CARRIER_ID',
			(int)$id_carrier1);

		if (!parent::install() ||
			!Configuration::updateGlobalValue('INPOST_UK_OVERCOST', '') ||
			!$this->registerHook('updateCarrier')
		)
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
			!Configuration::deleteByName('INPOST_UK_OVERCOST') ||
			!$this->unregisterHook('updateCarrier'))
		{
			return false;
		}

		// Delete External Carrier
		$Carrier1 = new Carrier((int)(Configuration::get('INPOSTSHIPPING_CARRIER_ID')));

		// If external carrier is default set other one as default
		if (Configuration::get('PS_CARRIER_DEFAULT') == (int)($Carrier1->id))
		{
			global $cookie;

			$carriersD = Carrier::getCarriers($cookie->id_lang, true, false, false, NULL, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);

			foreach($carriersD as $carrierD)
			{
				if ($carrierD['active'] &&
					!$carrierD['deleted'] &&
				       	($carrierD['name'] != $this->_config['name']))
				{
					Configuration::updateValue('PS_CARRIER_DEFAULT', $carrierD['id_carrier']);
				}
			}
		}

		// Then delete Carrier
		$Carrier1->deleted = 1;
		if (!$Carrier1->update())
		{
			return false;
		}

		return true;
	}

	///
	// installExternalCarrier
	//
	// @param The details of the carrier to be created in array format
	//
	public static function installExternalCarrier($config)
	{
		$carrier = new Carrier();
		$carrier->name      = $config['name'];
		$carrier->id_tax_rules_group = $config['id_tax_rules_group'];
		$carrier->id_zone   = $config['id_zone'];
		$carrier->active    = $config['active'];
		$carrier->deleted   = $config['deleted'];
		$carrier->delay     = $config['delay'];
		$carrier->shipping_handling = $config['shipping_handling'];
		$carrier->range_behavior = $config['range_behavior'];
		$carrier->is_module = $config['is_module'];
		$carrier->shipping_external = $config['shipping_external'];
		$carrier->external_module_name = $config['external_module_name'];
		$carrier->need_range = $config['need_range'];

		$carrier->max_width  = $config['width'];
		$carrier->max_height = $config['height'];
		$carrier->max_depth  = $config['depth'];
		$carrier->max_weight = $config['weight'];

		$languages = Language::getLanguages(true);
		foreach ($languages as $language)
		{
			if ($language['iso_code'] == 'fr')
			{
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
			}
			if ($language['iso_code'] == 'en')
			{
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
			}
			if ($language['iso_code'] == Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')))
			{
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
			}
		}

		if ($carrier->add())
		{
			$groups = Group::getGroups(true);
			foreach ($groups as $group)
			{
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_group', array('id_carrier' => (int)($carrier->id), 'id_group' => (int)($group['id_group'])), 'INSERT');
			}

			$rangePrice = new RangePrice();
			$rangePrice->id_carrier = $carrier->id;
			$rangePrice->delimiter1 = '0';
			$rangePrice->delimiter2 = '100';
			$rangePrice->add();

			$rangeWeight = new RangeWeight();
			$rangeWeight->id_carrier = $carrier->id;
			$rangeWeight->delimiter1 = '0';
			$rangeWeight->delimiter2 = '20';
			$rangeWeight->add();

			$zones = Zone::getZones(true);
			foreach ($zones as $zone)
			{
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_zone', array('id_carrier' => (int)($carrier->id), 'id_zone' => (int)($zone['id_zone'])), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => (int)($rangePrice->id), 'id_range_weight' => NULL, 'id_zone' => (int)($zone['id_zone']), 'price' => '1.50'), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => NULL, 'id_range_weight' => (int)($rangeWeight->id), 'id_zone' => (int)($zone['id_zone']), 'price' => '1.50'), 'INSERT');
			}

			// Copy Logo
			if (!copy(dirname(__FILE__).'/carrier.jpg', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg'))
			{
				error_log('Failed to find the logo');
				return false;
			}

			// Return ID Carrier
			return (int)($carrier->id);
		}

		return false;
	}

	///
	// getContent
	//
	public function getContent()
	{
		$this->_html .= '<h2>' . $this->l('InPost Shipping').'</h2>';

		if (!empty($_POST) AND Tools::isSubmit('submitSave'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
			{
				$this->_postProcess();
			}
			else
			{
				foreach ($this->_postErrors AS $err)
				{
					$this->_html .= '<div class="alert error"><img src="'._PS_IMG_.'admin/forbbiden.gif" alt="nok" />&nbsp;'.$err.'</div>';
				}
			}
		}
		$this->_displayForm();
		return $this->_html;
	}

	///
	// _displayForm
	//
	private function _displayForm()
	{
		$this->_html .= '<fieldset>
		<legend><img src="'.$this->_path.'logo.png" alt="" /> '.$this->l('InPost Shipping Module Status').'</legend>';

		$alert = array();
		if (!Configuration::get('INPOST_UK_OVERCOST') || Configuration::get('INPOST_UK_OVERCOST') == '')
		{
			$alert['carrier1'] = 1;
		}

		if (!count($alert))
		{
			$this->_html .= '<img src="'._PS_IMG_.'admin/module_install.png" /><strong>'.$this->l('InPost Shipping is configured and online!').'</strong>';
		}
		else
		{
			$this->_html .= '<img src="'._PS_IMG_.'admin/warn2.png" /><strong>'.$this->l('My Carrier is not configured yet, please:').'</strong>';
			$this->_html .= '<br />'.(isset($alert['carrier1']) ? '<img src="'._PS_IMG_.'admin/warn2.png" />' : '<img src="'._PS_IMG_.'admin/module_install.png" />').' 1) '.$this->l('Configure the carrier 1 overcost');
		}

		$this->_html .= '</fieldset><div class="clear">&nbsp;</div>
			<style>
				#tabList { clear: left; }
				.tabItem { display: block; background: #FFFFF0; border: 1px solid #CCCCCC; padding: 10px; padding-top: 20px; }
			</style>
			<div id="tabList">
				<div class="tabItem">
				<form action="index.php?tab='.Tools::getValue('tab').'&configure='.Tools::getValue('configure').'&token='.Tools::getValue('token').'&tab_module='.Tools::getValue('tab_module').'&module_name='.Tools::getValue('module_name').'&id_tab=1&section=general" method="post" class="form" id="configForm">

				<fieldset style="border: 0px;">
				<h4>'.$this->l('General configuration').' :</h4>
					<label>'.$this->l('My Carrier1 overcost').' : </label>
					<div class="margin-form"><input type="text" size="20" name="mycarrier1_overcost" value="'.Tools::getValue('mycarrier1_overcost', Configuration::get('INPOST_UK_OVERCOST')).'" /></div>
					</div>
					<br /><br />
				</fieldset>				
				<div class="margin-form"><input class="button" name="submitSave" type="submit"></div>
			</form>
		</div></div>';
	}

	///
	// _postValidation
	//
	private function _postValidation()
	{
		// Check configuration values
		if (Tools::getValue('mycarrier1_overcost') == '')
		{
			$this->_postErrors[]  = $this->l('You have to configure at least one carrier');
		}
	}

	///
	// _postProcess
	//
	private function _postProcess()
	{
		// Saving new configurations
		if (Configuration::updateValue('INPOST_UK_OVERCOST', Tools::getValue('mycarrier1_overcost')))
		{
			$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
		}
		else
		{
			$this->_html .= $this->displayErrors($this->l('Settings failed'));
		}
	}

	///
	// hookUpdateCarrier
	//
	// @param List of parameters
	//
	public function hookupdateCarrier($params)
	{
		$id_carrier_old = (int)($params['id_carrier']);
		$id_carrier_new = (int)($params['carrier']->id);

		if ($id_carrier_old == (int)(Configuration::get('INPOSTSHIPPING_CARRIER_ID')))
		{
			Configuration::updateValue('INPOSTSHIPPING_CARRIER_ID', $id_carrier_new);
		}
	}

	///
	// getOrderShippingCost
	//
	public function getOrderShippingCost($params, $shipping_cost)
	{
		if ($this->id_carrier == (int)(Configuration::get('INPOSTSHIPPING_CARRIER_ID'))
			&& Configuration::get('INPOST_UK_OVERCOST') > 1)
		{
			return (float)(Configuration::get('INPOST_UK_OVERCOST'));
		}

		return false; // carrier is not known
	}

	///
	// getOrderShippingCostExternal
	//
	public function getOrderShippingCostExternal($params)
	{
		if ($this->id_carrier == (int)(Configuration::get('INPOSTSHIPPING_CARRIER_ID'))
			&& Configuration::get('INPOST_UK_OVERCOST') > 1)
		{
			return (float)(Configuration::get('INPOST_UK_OVERCOST'));
		}

		return false; // carrier is not known
	}
}


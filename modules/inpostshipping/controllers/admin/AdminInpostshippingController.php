<?php

include_once dirname(__FILE__).'/../../classes/InpostParcelsTools.php';
include_once dirname(__FILE__).'/../../classes/InpostParcelDetails.php';

class AdminInpostShippingController extends ModuleAdminController
{
	///
	// __construct
	//
	public function __construct()
	{
		$this->bootstrap  = true;
		$this->context    = Context::getContext();
		$this->table      = 'inpostshipping';

		$this->className  = 'InpostParcelDetails';
		$this->identifier = 'id_inpostshipping';
		$this->lang       = false;
		$this->is_template_list  = false;
		$this->multishop_context = Shop::CONTEXT_ALL;

		// Determine how the page is rendered.
		$this->display       = 'list';
		$this->toolbar_title = array($this->l('InPost Shipping Orders'));
		$this->meta_title    = array($this->l('InPost Shipping Orders'));

		$this->addRowAction('edit');
		$this->list_no_link = true;

		$this->fields_list = array(
			'id_inpostshipping' => array(
				'title' => $this->l('ID'),
				'align' => 'text-center',
			),
			'order_id' => array(
				'title' => $this->l('Order ID'),
			),
			'parcel_id' => array(
				'title' => $this->l('Parcel ID'),
				'align' => 'text-right',
			),
			'order_status' => array(
				'title'    => $this->l('Order Status'),
				'align'    => 'text-center',
				//'callback' => 'getSalesOrderStatus',
			),
			'parcel_status' => array(
				'title' => $this->l('Parcel Status'),
				'align' => 'text-center',
			),
			'parcel_target_machine_id' => array(
				'title' => $this->l('Machine ID'),
				'align' => 'text-center',
			),
			'sticker_creation_date' => array(
				'title' => $this->l('Sticker Creation Date'),
				'align' => 'text-right',
				'type'  => 'datetime',
			),
			'creation_date' => array(
				'title' => $this->l('Ceation Date'),
				'align' => 'text-right',
				'type'  => 'datetime',
			)
		);
		$this->shopLinkType = 'shop';

		// Set up the Bulk actions
		$this->bulk_actions = array(
			'label' => array(
				'text' => $this->l('Print Label'),
				'confirm' => $this->l('Print labels for selected items?'),
				'icon' => 'icon-print'
			)
		);

		parent::__construct();
		if (!$this->module->active)
		{
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
		}
	}

	public function initToolBarTitle()
	{
		$this->toolbar_title[] = $this->l('Administration');
		$this->toolbar_title[] = $this->l('InPost Shipping Orders');
	}

	///
	// initPageHeaderToolbar
	//
	// @brief Add the main page header toolbar
	//
	public function initPageHeaderToolbar()
	{
		//unset($this->page_header_toolbar_btn['new']);
		parent::initPageHeaderToolbar();
	}

	public function processRefreshData()
	{
		return $this->module->refreshData();
	}

	///
	// setMedia
	//
	public function setMedia()
	{
		parent::setMedia();
		$this->addJs(_PS_MODULE_DIR_.'inpostshipping/views/js/admininpostshipping.js');
	}

	///
	// getList
	//
	public function getList($id_lang, $order_by = null, $order_way = null,
		$start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		// Check the status of a row and remove the Edit button if
		// the status is wrong.
		$nb_items = count($this->_list);

		for ($i = 0; $i < $nb_items; $i++)
		{
			$can_create_parcel = false;

			// Retrieve the order status for the line.
			$ret = $this->getSalesOrderStatus($this->_list[$i]['order_id'],
					$can_create_parcel);

			$this->_list[$i]['order_status'] = $ret;

			// if the current state doesn't allow order edit,
			// skip the edit action
			if (!$can_create_parcel)
			{
				$this->addRowActionSkipList('edit',
					$this->_list[$i]['id_inpostshipping']);
			}
		}
	}

	///
	// renderList
	//
	public function renderList()
	{
		if (!($this->fields_list && is_array($this->fields_list)))
			return false;
		$this->getList($this->context->language->id);

		$helper = new HelperList();
		
		// Empty list is ok
		if (!is_array($this->_list))
		{
			$this->displayWarning($this->l('Bad SQL query', 'Helper').'<br />'.htmlspecialchars($this->_list_error));
			return false;
		}

		// Try and remove the new order button from the local toolbar
		unset($this->toolbar_btn['new']);

		$this->setHelperDisplay($helper);
		$helper->tpl_vars = $this->tpl_list_vars;
		$helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;
		// Allow the user to filter and sort.
		$helper->simple_header = false;

		// For compatibility reasons, we have to check standard actions in class attributes
		foreach ($this->actions_available as $action)
		{
			if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action)
				$this->actions[] = $action;
		}
		$helper->is_cms = $this->is_cms;
		$skip_list = array();

		foreach ($this->_list as $row)
		{
			if (isset($row['id_order']) && is_numeric($row['id_order']))
			{
				$skip_list[] = $row['id_cart'];
			}
		}

		if (array_key_exists('delete', $helper->list_skip_actions))
		{
			$helper->list_skip_actions['delete'] = array_merge($helper->list_skip_actions['delete'], (array)$skip_list);
		}
		else
		{
			$helper->list_skip_actions['delete'] = (array)$skip_list;
		}

		$list = $helper->generateList($this->_list, $this->fields_list);
		return $list;
	}

	///
	// postProcess
	//
	public function postProcess()
	{
		if (Tools::isSubmit('submitAddinpostshipping'))
		{
			unset($_POST['submitAddinpostshipping']);

			// If the return creation date is 0000/00/00 00:00:00
			// set it to something that will pass the form
			// validation.
			if (Tools::getValue('return_parcel_expiry') == 0)
			{
				$_POST['return_parcel_expiry'] = NULL;
			}
			if (Tools::getValue('sticker_creation_date') == 0)
			{
				$_POST['sticker_creation_date'] = NULL;
			}
			if (Tools::getValue('return_parcel_created') == 0)
			{
				$_POST['return_parcel_created'] = NULL;
			}

			// Create a parcel if the status is 'Created' and the
			// tmp_id is empty
			if (Tools::getValue('parcel_status') == 'Created' &&
				Tools::getValue('parcel_tmp_id') == '')
			{
				$error = '';

				if (!InpostParcelsTools::create_new_parcel($_POST, $error))
				{
					// We can create a parcel ID but fail
					// to pay for it.
					if (trim($_POST['parcel_id']) != '')
					{
						$this->errors[] = Tools::displayWarning($this->l('Failed to pay for parcel.').$error);
					}
					else
					{
						// Display error to the user
						// and don't save the data.
						$this->errors[] = Tools::displayError($this->l('Failed to create parcel.').$error);

						return;
					}
				}

				// Make sure that the user can only change
				// certain values.
				$_POST['parcel_status'] = 'Prepared';
			}
			// Change the parcel details if the user has asked us
			// to and a parcel has been created previously.
			elseif (Tools::getValue('parcel_id') != '')
			{
				$error = '';

				if (!InpostParcelsTools::inpost_update_parcel($_POST, $error))
				{
					// Display error to the user and don't
					// save the data.
					$this->errors[] = Tools::displayError($this->l('Failed to update parcel. ').$error);
					return;
				}
			}
		}

		parent::postProcess();
	}

	///
	// getSalesOrderStatus
	//
	// @param The order number to look for
	// @param Once the order is paid for allow parcel creation
	//
	public function getSalesOrderStatus($order_id, &$can_create_parcel=false)
	{
		$ret = '';

		$order = new Order((int)$order_id);

		if (!is_object($order))
		{
			$ret = '';
		}
		else
		{
			if ($order->hasBeenPaid())
			{
				$can_create_parcel = true;
			}

			$order_state = $order->getCurrentStateFull(
				$this->context->language->id);

			$ret = $order_state['name'];
		}

		return $ret;
	}

	///
	// renderForm
	//
	public function renderForm()
	{
		if (!($obj = $this->loadObject(true)))
		{
			return;
		}

		$default_machine = '';

		// Get the list of machines
		$machine_list = $this->_getMachineList();
		$size_list = array(
			array('id_option' => 'A',
				'name' => 'Small'),
			array('id_option' => 'B',
				'name' => 'Medium'),
			array('id_option' => 'C',
				'name' => 'Large'),
		);
		$status_list = array(
			array('id_option' => 'Prepared',
				'name' => 'Prepared'),
			array('id_option' => 'Created',
				'name' => 'Created'),
			array('id_option' => 'Cancelled',
				'name' => 'Cancelled'),
		);

		// Retrieve the current row's ID, then use it to get the rest
		// of the details for the InPost order line.
		$parcel_order_id = Tools::getValue('id_inpostshipping');
		$parcel_order = new InpostParcelDetails($parcel_order_id);

		if (is_object($parcel_order))
		{
			// If the parcel order line is OK.
			$default_machine = $parcel_order->parcel_target_machine_id;
		}
		else
		{
			error_log('Failed to find the InPost Parcel details to edit.');
		}

		// Put the data values in here
		$this->fields_value = array(
			'order_id'              => $parcel_order->order_id,
			'parcel_id'             => $parcel_order->parcel_id,
			'parcel_description'    => $parcel_order->parcel_description,
			'parcel_receiver_phone' => $parcel_order->parcel_receiver_phone,
			'parcel_receiver_email' => $parcel_order->parcel_receiver_email,
			'parcel_size'           => $parcel_order->parcel_size,
			'parcel_status'         => $parcel_order->parcel_status,
			'parcel_tmp_id'         => $parcel_order->parcel_tmp_id,
			'parcel_target_machine_id' => $default_machine,
			'return_parcel_id'      => $parcel_order->return_parcel_id,
			'return_parcel_expiry'  => $parcel_order->return_parcel_expiry,
		);

		// If the user is creating a parcel allow them to edit most
		// fields.
		$description_readonly = false;
		$phone_readonly       = false;
		$email_readonly       = false;
		$size_readonly        = false;
		$status_readonly      = false;
		$machine_readonly     = false;

		// Once a parcel has gone beyond don't allow much editing.
		switch ($parcel_order->parcel_status)
		{
			case 'Prepared': // Paid and Label printed
				$phone_readonly       = true;
				$email_readonly       = true;
				$machine_readonly     = true;
				break;
			case 'Created': // Can edit anything.
				break;
			case 'Cancelled':
			default: // Allow viewing only
				$description_readonly = true;
				$phone_readonly       = true;
				$email_readonly       = true;
				$size_readonly        = true;
				$status_readonly      = true;
				$machine_readonly     = true;
				break;
		}

		// Because we need to be able to disable certain fields we
		// change the types here.
		if ($size_readonly)
		{
			$input_parcel_size = array(
				'type'     => 'text',
				'label'    => $this->l('Parcel Size'),
				'name'     => 'parcel_size',
				'required' => true,
				'hint'     => $this->l('The default size of the parcel.'),
				'readonly' => true,
			);
		}
		else
		{
			$input_parcel_size = array(
				'type'     => 'select',
				'label'    => $this->l('Parcel Size'),
				'name'     => 'parcel_size',
				'required' => true,
				'options'   => array(
					'query' => $size_list,
					'id'    => 'id_option',
					'name'  => 'name'
				),
				'hint'    => $this->l('The default size of the parcel.'),
			);
		}
		if ($status_readonly)
		{
			$input_status = array(
				'type'     => 'text',
				'label'    => $this->l('Status'),
				'name'     => 'parcel_status',
				'required' => true,
				'hint'     => $this->l('You can cancel a parcel by changing it\' status.'),
				'disabled' => $status_readonly,
			);
		}
		else
		{
			$input_status = array(
				'type'     => 'select',
				'label'    => $this->l('Status'),
				'name'     => 'parcel_status',
				'required' => true,
				'options'  => array(
					'query' => $status_list,
					'id'    => 'id_option',
					'name'  => 'name'
				),
				'hint'     => $this->l('You can cancel a parcel by changing it\' status.'),
			);
		}
		if ($machine_readonly)
		{
			$input_parcel_target_machine_id = array(
				'type'     => 'text',
				'label'    => $this->l('Target Parcel Machine'),
				'name'     => 'parcel_target_machine_id',
				'required' => true,
				'hint'    => $this->l('Either confirm the customers selection or change to a new Lokcer ID.'),
				'disabled' => $machine_readonly,
			);
			$input_map = array(
				'type'  => 'html',
				'label' => $this->l('Map'),
				'name'  => 'map',
				'html_content' => 'Cannot change Parcel Machine',
				'hint' => 'Click on the link and the Geo Widget will open. It allows you to select a new locker terminal using a map',
			);
		}
		else
		{
			$input_parcel_target_machine_id = array(
				'type'     => 'select',
				'label'    => $this->l('Target Parcel Machine'),
				'name'     => 'parcel_target_machine_id',
				'required' => true,
				'options'   => array(
					'query' => $machine_list,
					'id'    => 'id_option',
					'name'  => 'name'
				),
				'hint'    => $this->l('Either confirm the customers selection or change to a new Lokcer ID.'),
				'disabled' => $machine_readonly,
			);

			$input_map = array(
				'type'  => 'html',
				'label' => $this->l('Map'),
				'name'  => 'map',
				'html_content' => '<script type="text/javascript" src="https://geowidget.inpost.co.uk/dropdown.php?field_to_update=inpost_name&amp;field_to_update2=address&amp;user_function=user_function"></script>
			<a href=\'#\' onClick=\'openMap(); return false;\' title=\'Change machine id\'>Click Here to Change Machine</a>',
				'hint' => 'Click on the link and the Geo Widget will open. It allows you to select a new locker terminal using a map',
			);
		}

		// The array structure for the field data.
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('InPost Order'),
				'icon'  => 'icon-user'
			),
			'input' => array(
				array(
					'type'  => 'hidden',
					'name'  => 'id_inpostshipping',
				),
				array(
					'type'  => 'hidden',
					'name'  => 'parcel_id',
				),
				array(
					'type'  => 'hidden',
					'name'  => 'inpost_name',
				),
				array(
					'type'  => 'hidden',
					'name'  => 'address',
				),
				array(
					'type'  => 'text',
					'label' => $this->l('Sales Order Number'),
					'name'  => 'order_id',
					'required' => false,
					'class'    => 't',
					'readonly' => true,
				),
				array(
					'type'  => 'textarea',
					'label' => $this->l('Description'),
					'name'  => 'parcel_description',
					'required' => true,
					'readonly' => $description_readonly,
				),
				array(
					'type'  => 'text',
					'label' => $this->l('Receiver Phone'),
					'name'  => 'parcel_receiver_phone',
					'required' => true,
					'hint'    => $this->l('The last nine digits of the customer\'s mobile number.'),
					'readonly' => $phone_readonly,
				),
				array(
					'type'  => 'text',
					'label' => $this->l('Receiver Email'),
					'name'  => 'parcel_receiver_email',
					'required' => true,
					'readonly' => $email_readonly,
				),
				$input_parcel_size,
				$input_status,
				array(
					'type'     => 'text',
					'label'    => $this->l('Tmp id'),
					'name'     => 'parcel_tmp_id',
					'required' => true,
					'readonly' => true,
				),
				$input_parcel_target_machine_id,
				$input_map,
				array(
					'type'  => 'text',
					'label' => $this->l('Return ID'),
					'name'  => 'return_parcel_id',
					'readonly' => true,
				),
				array(
					'type'  => 'text',
					'label' => $this->l('Return Expiry'),
					'name'  => 'return_parcel_expiry',
					'readonly' => true,
				),
			),
			'submit' => array(
				'title' => $this->l('Save')
			)
		);

		return parent::renderForm();
	}

	///
	// _getMachineList
	//
	// @brief Get an array of the currently opperating locker machines.
	//
	private function _getMachineList()
	{
		$result = array();

		$params = array(
			'url'   => Configuration::get('IP_UK_API_URL').'machines',
			'methodType' => 'GET',
			'params' => array(
				'status' => 'Operating'
			)
		);

		$ret = InpostParcelsTools::connectInpostparcels($params);
		
		$info = $ret['info'];

		if ($info['http_code'] == 200)
		{
			// The data is good.
			foreach($ret['result'] as $key => $row)
			{
				$result[] = array(
					'id_option' => $row->id,
					'name'      => $row->id.', '.$row->address->street.', '.$row->address->city.', '.$row->address->post_code
				);
			}
		}

		return $result;
	}

	///
	// processBulkLabel
	//
	// @brief Get all of the parcels and print their labels
	//
	protected function processBulkLabel()
	{
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			$parcels = array();

			$dbQuery = Db::getInstance();
			foreach ($this->boxes as $id_order)
			{
				$result = $dbQuery->query('SELECT parcel_id from '._DB_PREFIX_.'inpostshipping WHERE id_inpostshipping = '.$id_order);

				// We should only get back one row.
				$row = $dbQuery->nextRow($result);

				if (trim($row['parcel_id']) != '')
				{
					$parcels[] = $row['parcel_id'];
				}
			}

			if (count($parcels) > 0)
			{
				$label = '';
				$error = '';

				if (!InpostParcelsTools::inpost_create_labels($parcels, $label, $error))
				{
					$this->errors[] = Tools::displayError('Failed to create label. '.$error);
					return;
				}

				if (ob_get_contents())
				{
					$this->Error('Some data has already been output, can\'t send PDF file');
					return;
				}
				header('Content-Description: File Transfer');
				if (headers_sent())
				{
					$this->Error('Some data has already been output to browser, can\'t send PDF file');
				}

				$this->_update_sticker_creation_date($parcels);

				header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
				header('Pragma: public');
				header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
				// force download dialog
				if (strpos(php_sapi_name(), 'cgi') === false)
				{
					header('Content-Type: application/force-download');
					header('Content-Type: application/octet-stream', false);
					header('Content-Type: application/download', false);
					header('Content-Type: application/pdf', false);
				}
				else
				{
					header('Content-Type: application/pdf');
				}

				// use the Content-Disposition header to supply
				// a recommended filename
				header('Content-Disposition: attachment; filename="Label_'.date('Ymd_Hi').'.pdf"');
				header('Content-Transfer-Encoding: binary');
				// Restrict the number of characters sent to
				// the PDF file.
				header('Content-Length: '.strlen($label));

				echo $label;
			}
		}
	}

	///
	// _update_sticker_creation_date
	//
	// @param Array of parcel id's
	//
	private function _update_sticker_creation_date($parcels)
	{
		$parcel_id = implode('\',\'', $parcels);

		$dbQuery = Db::getInstance();
		$result = $dbQuery->update('inpostshipping',
			array('sticker_creation_date' => date('Y-m-d H:i:s')),
			'parcel_id in (\''.$parcel_id.'\')',
			1);
	}

}

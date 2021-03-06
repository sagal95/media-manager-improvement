<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View for the component configuration
 *
 * @since  3.2
 */
class ConfigViewComponentHtml extends ConfigViewCmsHtml
{
	public $state;

	public $form;

	public $component;

	/**
	 * Display the view
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   3.2
	 *
	 */
	public function render()
	{
		$form = null;
		$component = null;

		try
		{
			$form = $this->model->getForm();
			$component = $this->model->getComponent();
			$user = JFactory::getUser();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		// Bind the form to the data.
		if ($form && $component->params)
		{
			$form->bind($component->params);
		}

		$this->fieldsets   = $form->getFieldsets();
		$this->formControl = $form->getFormControl();

		// Don't show permissions fieldset if not authorised.
		if (!$user->authorise('core.admin', $component->option) && isset($this->fieldsets['permissions']))
		{
			unset($this->fieldsets['permissions']);
		}

		$this->form = &$form;
		$this->component = &$component;

		$this->components = ConfigHelperConfig::getComponentsWithConfig();

		$this->userIsSuperAdmin = $user->authorise('core.admin');
		$this->currentComponent = JFactory::getApplication()->input->get('component');
		$this->return = JFactory::getApplication()->input->get('return', '', 'base64');

		$this->addToolbar();

		return parent::render();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_($this->component->option . '_configuration'), 'equalizer config');
		JToolbarHelper::saveGroup(
			[
				['apply', 'config.save.component.apply'],
				['save', 'config.save.component.save']
			],
			'btn-success'
		);
		JToolbarHelper::divider();
		JToolbarHelper::cancel('config.cancel.component');
		JToolbarHelper::divider();
		JToolbarHelper::help('JHELP_COMPONENTS_' . $this->currentComponent . '_OPTIONS');
	}
}

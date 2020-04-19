<?php
/**
 *
 * Confirm Email extension for the phpBB Forum Software package
 *
 * @copyright (c) 2020, Kailey Truscott, https://www.layer-3.org/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kinerity\confirmemail\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Confirm Email event listener
 */
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/**
	 * Constructor
	 *
	 * @param \phpbb\language\language  $language
	 * @param \phpbb\request\request    $request
	 * @param \phpbb\template\template  $template
	 */
	public function __construct(\phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template)
	{
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.ucp_register_data_before'	=> 'ucp_register_data_before',
			'core.ucp_register_data_after'	=> 'ucp_register_data_after',
		];
	}

	public function ucp_register_data_before($event)
	{
		$this->language->add_lang('common', 'kinerity/confirmemail');

		$event->update_subarray('data', 'email_confirm', strtolower($this->request->variable('email_confirm', '')));
	}

	public function ucp_register_data_after($event)
	{
		$error = $event['error'];

		if (!empty($event['submit']) && $event['data']['email'] !== $event['data']['email_confirm'])
		{
			$error[] = $this->language->lang('CONFIRM_EMAIL_ERROR');
		}

		$this->template->assign_var('EMAIL_CONFIRM', $event['data']['email_confirm']);

		$event['error'] = $error;
	}
}

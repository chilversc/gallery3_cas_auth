<?php defined("SYSPATH") or die("No direct script access.");

class Admin_CAS_Settings_Controller extends Admin_Controller
{
	function index()
	{
		$this->_show_form($this->_get_admin_form());
	}

	function save()
	{
		access::verify_csrf();
		$form = $this->_get_admin_form();
		if ($form->validate()) {
			$settings = cas_auth::get_settings();
			$settings->host = $form->cas_host->value;
			$settings->port = $form->cas_port->value;
			$settings->context = $form->cas_context->value;
			cas_auth::set_settings($settings);

			message::success(t("CAS settings updated"));
			url::redirect("admin/cas_settings");
		} else {
			$this->_show_form($form);
		}
	}

	function enable()
	{
		access::verify_csrf();
		cas_auth::set_enabled(true);
		url::redirect("admin/cas_settings");
	}

	function disable()
	{
		access::verify_csrf();
		cas_auth::set_enabled(false);
		url::redirect("admin/cas_settings");
	}


	private function _show_form($form)
	{
		$view = new Admin_View("admin.html");
		$view->page_title = t("CAS settings");
		$view->content = new View("admin_cas_settings.html");
		$view->content->form = $form;
		$view->content->cas_enabled = cas_auth::is_enabled();
		print $view;
	}

	private function _get_admin_form()
	{
		$settings = cas_auth::get_settings();

		$form = new Forge("admin/cas_settings/save", "", "post", array("id" => "g-cas-settings-form"));

		$form->input("cas_host")
			->label(t("CAS Host"))
			->value($settings->host)
			->rules("required");

		$form->input("cas_port")
			->label(t("CAS Port"))
			->value($settings->port)
			->rules("required");

		$form->input("cas_context")
			->label(t("CAS Context"))
			->value($settings->context);

		$form->submit("save")->value(t("Save"));

		return $form;
	}
}

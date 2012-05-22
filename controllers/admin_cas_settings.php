<?php defined("SYSPATH") or die("No direct script access.");

/**
 * Copyright (c) 2012 infinite Group Ltd.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

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

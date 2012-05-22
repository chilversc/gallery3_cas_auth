<?php defined("SYSPATH") or die("No direct script access.");

class Reauthenticate_Controller extends Controller
{
  public function index()
  {
    // TODO: somehow make use of phpCAS::renewAuthentication

    $is_ajax = Session::instance()->get_once("is_ajax_request", request::is_ajax());

    if (!phpCAS::forceAuthentication()) {
      $this->_fail($is_ajax);
      return;
    }

    if (!identity::active_user()->admin) {
      $this->_fail($is_ajax);
      return;
    }

    $this->_continue($is_ajax);
  }

  private function _fail($is_ajax)
  {
    if ($is_ajax) {
       // We should never be able to get here since Admin_Controller::_reauth_check() won't work
       // for non-admins.
       access::forbidden();
     } else {
       url::redirect(item::root()->abs_url());
     }
  }

  private function _continue($is_ajax)
  {
    module::event("user_auth", identity::active_user());

    if ($is_ajax) {
      message::success(t("Successfully re-authenticated!"));
    }

    $url = Session::instance()->get_once("continue_url");
    if ($url === false)
      $url = item::root()->abs_url();

    url::redirect($url);
  }
}

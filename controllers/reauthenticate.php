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

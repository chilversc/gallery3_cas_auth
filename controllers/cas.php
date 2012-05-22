<?php

class CAS_Controller extends Controller
{
  function login()
  {
    phpCAS::forceAuthentication();
    url::redirect(item::root()->abs_url());
  }

  function logout()
  {
    if (cas_auth::is_logged_in()) {
      access::verify_csrf();
      auth::logout();
      phpCAS::logout();
    }
    url::redirect(item::root()->abs_url());
  }
}

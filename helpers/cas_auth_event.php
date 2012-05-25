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

class cas_auth_event
{
  // Record if system.post_routing was handled in case Gallery3 starts forwarding system events in future versions
  private static $post_routing_handled = false;

  static function gallery_ready()
  {
    if (!cas_auth::is_enabled())
      return;

    Event::add("system.post_routing", array("cas_auth_event", "system_post_routing"));

    self::_init_cas();
    phpCAS::handleLogoutRequests();
  }

  static function system_post_routing()
  {
    if (self::$post_routing_handled)
      return;

    if (!cas_auth::is_enabled())
      return;

    self::$post_routing_handled = true;

    // Do not apply CAS authentication to rest requests as these have their own authentication handling using API keys.
    if (Router::$controller == "rest")
      return;

    // Only issue checkAuthentication for get requests because it can cause a redirect to CAS to verify the token
    // This should be OK, because of single sign out should destroy the session when the user logs out causing
    // isAuthenticated to return false. Also the token is only valid for a limited length of time before it needs
    // to be verified with the CAS server.
    if (request::method() == "get")
      phpCAS::checkAuthentication();

    self::_sync_cas_auth_with_gallery();
  }

  static function user_menu($menu, $theme)
  {
    if (!cas_auth::is_enabled())
      return;

    $menu->remove("user_menu_login");
    $menu->remove("user_menu_logout");

    if (cas_auth::is_logged_in()) {
      $csrf = access::csrf_token();
      $menu->append(Menu::factory("link")
        ->id("user_menu_logout")
        ->css_id("g-logout-link")
        ->url(url::site("cas/logout?csrf=$csrf"))
        ->label(t("Logout")));
    } else {
      $menu->append(Menu::factory("link")
        ->id("user_menu_login")
        ->css_id("g-login-link")
        ->url(url::site("cas/login"))
        ->label(t("Login")));
    }
  }

  static function admin_menu($menu, $theme)
  {
    $menu->get("settings_menu")
      ->append(Menu::factory("link")
      ->id("cas_settings")
      ->label(t("CAS settings"))
      ->url(url::site("admin/cas_settings")));
  }


  static function user_logout($user)
  {
    if (!cas_auth::is_enabled())
      return;
    self::_clear_missing_user();
    phpCAS::logout();
  }


  static function post_authenticate_callback()
  {
    self::_clear_missing_user();
    self::_sync_cas_auth_with_gallery();
  }


  private static function _init_cas()
  {
    require_once Kohana::find_file("vendor", "phpCAS/CAS");

    $settings = cas_auth::get_settings();

    phpCAS::client(SAML_VERSION_1_1, $settings->host, $settings->port, $settings->context, true);

    // TODO: replace this with setCasServerCACert
    phpCAS::setNoCasServerValidation();

    phpCAS::setPostAuthenticateCallback(array("cas_auth_event", "post_authenticate_callback"));
    phpCAS::setSessionHandler(new CAS_Gallery_Session_Adapter());
  }

  private static function _sync_cas_auth_with_gallery()
  {
    if (phpCAS::isAuthenticated()) {
      self::_sign_in_gallery_user();
    } else {
      self::_sign_out_gallery_user();
    }
  }

  private static function _sign_in_gallery_user()
  {
    $current_user = identity::active_user();
    $cas_user_name = phpCAS::getUser();

    if ($current_user->guest) {
      $new_user = self::_find_user($cas_user_name, true);
      if ($new_user != null)
        auth::login($new_user);
    } else if ($current_user->name != $cas_user_name) {
      $new_user = self::_find_user($cas_user_name, true);
      if ($new_user == null) {
        self::_sign_out_gallery_user();
      } else {
        auth::login($new_user);
      }
    }
  }

  private static function _sign_out_gallery_user()
  {
    $isGuest = identity::active_user()->guest;
    if (!$isGuest) {
      auth::logout();
      url::redirect(item::root()->abs_url());
    }
  }

  private static function _find_user($name)
  {
    // suppress repeatedly raising cas_auth_missing_user event every request for the same user.
    $missing = session::instance()->get("cas_auth_missing_user", null);
    if ($name == $missing)
      return null;

    $user = identity::lookup_user_by_name($name);
    if ($user != null)
      return $user;

    Kohana_Log::add("info", "Could not authenticate user '$name': No matching user found in gallery database");
    session::instance()->set("cas_auth_missing_user", $name);
    module::event("cas_auth_missing_user", $name);

    $user = identity::lookup_user_by_name($name);
    return $user;
  }

  private static function _clear_missing_user()
  {
    session::instance()->delete("cas_auth_missing_user");
  }
}

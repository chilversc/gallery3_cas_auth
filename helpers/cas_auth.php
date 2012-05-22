<?php

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

class cas_auth_Core
{
  static function is_enabled()
  {
    return (bool)module::get_var("cas_auth", "enabled", false);
  }

  static function set_enabled($enabled)
  {
    module::set_var("cas_auth", "enabled", (bool)$enabled);
  }

  static function get_settings()
  {
    return (object)array(
      "host" => module::get_var("cas_auth", "cas_host"),
      "port" => (int)module::get_var("cas_auth", "cas_port", 443),
      "context" => module::get_var("cas_auth", "cas_context", ""),
    );
  }

  static function set_settings($settings)
  {
    module::set_var("cas_auth", "cas_host", $settings->host);
    module::set_var("cas_auth", "cas_port", $settings->port);
    module::set_var("cas_auth", "cas_context", $settings->context);
  }

  static function is_logged_in()
  {
    $user = identity::active_user();
    return ($user != null && !$user->guest) || phpCAS::isAuthenticated();
  }
}

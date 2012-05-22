<?php

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
}

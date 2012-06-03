CAS Authentication Integration
====

This integrates [Jasig CAS](http://www.jasig.org/cas) with Gallery3.

The users will still have to have an account in Gallery3 and their user names must match the user name used with CAS.

To automatically create missing users, create a custom module that handles the 'cas_auth_missing_user' event. This event
receives a single parameter containing the missing user name.

Limitations
====

* The admin re-authentication is disabled while using the cas_auth plugin since the user probably won't know what their
  Gallery3 password is. For security, it's advisable to have separate user/administrator accounts.
* Gallery3 user accounts still need a password, even though the users will not be using Gallery3 to authenticate.
* Replaces 404 error page to remove login form, other modules/themes that replace the 404 page may conflict.
* Replaces login_ajax page to remove login form, other modules/themes that replace this page may conflict.
* Extends auth_Core, so will not work with other modules that also extend auth_Core
* Performs some session management, hooks in early in the pipeline before sessions have been loaded.
  Other modules that hook in early and load a session will cause problems.
* Currently only supports SAML Version 1.1, need to add settings to allow selecting other protocols.
* Currently does not support CAS proxies, need to add settings to allow configuring a proxy.

TODO
====
* Add support for other protocols (this just needs a drop down in the settings, and someone with an appropriate CAS server to test the change).
* Add support for CAS proxies, not sure on what's required; I have not used CAS in a proxy configuration.
* Add feature to test set-up before enabling (would be nice to ensure you're not about to lock yourself out).
* A nicer settings page wouldn't hurt.
* Confirmation prompt before enabling/disabling.

Troubleshooting
====

If the CAS settings are incorrect when you enable the CAS plug you will not be able to access Gallery3 to disable the
plugin. To disable CAS use the following SQL.

    UPDATE gal_vars SET value = '0' WHERE module_name = 'cas_auth' AND name = 'enabled';
    DELETE FROM gal_caches WHERE `key` = 'var_cache';

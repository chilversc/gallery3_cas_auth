CAS Authentication Integration
====

This integrates [Jasig CAS](http://www.jasig.org/cas) with Gallery3.

The users will still have to have an account in Gallery3 and their user names must match the user name used with CAS.

To automatically create missing users, create a custom module that handles the 'cas_auth_missing_user' event. This event
receives a single parameter containing the missing user name.

Limitations
====

* The admin re-authentication is disabled while using the cas_auth plugin since the user probably won't know what their
Gallery3 password is.
* Gallery3 user accounts still need a password, even though the users will not be using Gallery3 to authenticate.
* Replaces 404 error page to remove login form, other modules/themes that replace the 404 page may conflict.

Troubleshooting
====

If the CAS settings are incorrect when you enable the CAS plug you will not be able to access Gallery3 to disable the
plugin. To disable CAS use the following SQL.

    UPDATE gal_vars SET value = '0' WHERE module_name = 'cas_auth' AND name = 'enabled';
    DELETE FROM gal_caches WHERE `key` = 'var_cache';

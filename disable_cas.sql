UPDATE gal_vars SET value = '0' WHERE module_name = 'cas_auth' AND name = 'enabled';
DELETE FROM gal_caches WHERE "key" = 'var_cache';

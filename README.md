# Multiple website store code url
This module enabled you to use the same store_code multiple times for different websites in the url by removing the website_code from the store_code before adding it to the url. So "example.com/website_en/", will become "example.com/en/".

The module has configuration to enable/disable it in the web/url section.
 
**Important: the store code should include the website code otherwise it won't do anything.**

# Changelog:
- 2.0.0 added compatibility for magento 2.3.*.
- 2.0.1 fixed issues with not being able to navigate on a not default store + fixed storeswitcher url redirect

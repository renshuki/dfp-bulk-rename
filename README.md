## DFP Bulk Rename

This is a PHP CLI script using *Google Ads API Client Library* to bulk rename Ad Units.  

> Please follow the [Google Ads API PHP Client Library](https://github.com/googleads/googleads-php-lib) guide if you need further help to install the library.  

### Getting started

1.  Git clone the repository: 
    `git clone https://github.com/renshuki/dfp-bulk-rename.git`

2.  Go to the project:
    `cd dfp-bulk-rename`

3.  Install the latest version of [Composer](https://getcomposer.org/download). 

3.  Install the dependencies: `php composer.phar require googleads/googleads-php-lib`

4.  Rename `adsapi_php_sample.ini` to `adsapi_php.ini` and configure it.

    *   [DFP
        adsapi_php.ini](https://github.com/googleads/googleads-php-lib/blob/master/examples/Dfp/adsapi_php.ini)

### Configuration  

Edit `BulkRenameAdUnits.php` file and go to the configuration section.  

```php
/*########################
#                        #
#     Configuration      #
#                        #
########################*/

/*** Ad Unit name to match ***/
private static $name_matcher = "%"; // SQL regular expression

/*** Status to match ***/
private static $status_matcher = InventoryStatus::ARCHIVED; // InventoryStatus::ACTIVE, InventoryStatus::INACTIVE and InventoryStatus::ARCHIVED are allowed.

/*** Suffix to append to renamed Ad Units ***/
private static $suffix = "_archive";
```

Modify the configuration options for your needs.


### Usage

Run `php BulkRenameAdUnits.php` to execute the script.

> This is an experimental script so I don't take any responsibility for the usage of this script.  
> It's at your own risks.  

### Todo

    *   Implement dryrun
    *   Add confirmation before execution of the script
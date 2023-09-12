# encora/module-productclone

- Product copy all the existing simple products Module. 

- Tested on Magento Community Edition  `Magento 2.4.6`.

## Composer Install module
1. Run `composer config repositories.encora-product-clone vcs https://github.com/vashy89/encora-product-clone`

2. Run `composer require encora/module-productclone`

3. Run `bin/magento setup:db-declaration:generate-whitelist --module-name=encora_module-productclone`

3. Run `php bin/magento setup:upgrade`

4. Run `php bin/magento setup:di:compile`

5. Run `php bin/magento s:s:d en_US`

6. Run `php bin/magento c:c`


## Composer Uninstall module

1. Run `composer remove encora/module-productclone`

2. Run `php bin/magento setup:di:compile`

3. Run `php bin/magento s:s:d en_US`

4. Run `php bin/magento c:c`


### Download Zip file

 - Unzip the zip file in `app/code/Encora`
 - Enable the module by running `php bin/magento module:enable Encora_ProductClone`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`
 - Occasionally require `php bin/magento indexer:reindex`

## Attributes

 - Product Attribute - condition (condition)
 
## Console Command
`php bin/magento encora:product:clone` 

## Preview Clone With single SKU, using argument by append sku id

![with_sku.png](/readme-images/with_sku.png "with_sku.png")

## Preview Clone with Lot and Batch wise, using options with --lot and --batch or simply with --lot

![batch_wise.png](/readme-images/batch_wise.png "batch_wise.png")

## Preview Clone all products

![clone_all.png](/readme-images/clone_all.png "clone_all.png")

## Preview Cloned Products Admin->Catalog->Products

![cloned_products_BE.png](/readme-images/cloned_products_BE.png "cloned_products_BE.png")

## Preview Admin Product Attribute

![prod_attr_be.png](/readme-images/prod_attr_be.png "prod_attr_be.png")

## Preview Logs in var/log/clone.log

![logs.png](/readme-images/logs.png "logs.png")

## Module Admin Configuration Settings

**Go to : Admin->store->configuration->encora->clone_simple_products->options->enable**


## Exception Handling

**Throws Error if not simple product(s)**

![not_simple_err.png](/readme-images/not_simple_err.png "not_simple_err.png")

**Throws Error if simple product has parent(s)**

![if_parent.png](/readme-images/if_parent.png "if_parent.png")

**Throws Error if already cloned product(s) retry to clone with same sku**

![known_err.png](/readme-images/known_err.png "known_err.png")



## Developer Information
- Vashishtha Chauhan
- Engineering Team Lead 
- Email `vashishtha.prime@gmail.com`
- Mobile `+91 9898121095`
- Linkein `www.linkedin.com/in/vashishtha-chauhan`

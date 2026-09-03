# Ezzar Delivery Cost

`Ezzar_DeliveryCost` overrides Magento's Flat Rate shipping price with the highest configured delivery charge found among the products in the cart.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.x
- PHP 8.1–8.4
- Flat Rate shipping
- A product select attribute with code `delivery_charge`

## Installation

```bash
composer config repositories.ezzaro14-magento2-dynamic-delivery-cost vcs https://github.com/Ezzaro14/magento2-dynamic-delivery-cost.git
composer require ezzaro14/magento2-dynamic-delivery-cost:dev-main
bin/magento module:enable Ezzar_DeliveryCost
bin/magento setup:upgrade
bin/magento cache:clean
```

Configure rates under **Stores > Configuration > Ezzar > Delivery Rates**. Products without a mapped delivery class retain Magento's normal Flat Rate price.

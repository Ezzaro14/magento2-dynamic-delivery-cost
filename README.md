# Ezzar Delivery Cost

`Ezzar_DeliveryCost` overrides Magento's Flat Rate shipping price with the highest configured delivery charge found among the products in the cart.
<img width="1106" height="419" alt="image" src="https://github.com/user-attachments/assets/a1688a05-ccff-405d-a62f-fea96df14cd7" />
<img width="383" height="111" alt="image" src="https://github.com/user-attachments/assets/57eaa193-cfe4-4908-9bfc-502c3d06e512" />

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

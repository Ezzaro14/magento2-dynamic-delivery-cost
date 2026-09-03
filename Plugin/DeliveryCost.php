<?php
declare(strict_types=1);

namespace Ezzar\DeliveryCost\Plugin;

use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\OfflineShipping\Model\Carrier\Flatrate;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Store\Model\ScopeInterface;

class DeliveryCost
{
    private const ATTR_CODE = 'delivery_charge';
    private const CONFIG_RATES_PATH = 'ezzar_deliverycost/general/rates';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly ProductResource $productResource
    ) {
    }

    public function afterCollectRates(Flatrate $subject, $result, RateRequest $request)
    {
        if (!$result) {
            return $result;
        }

        $storeId = (int) $request->getStoreId();

        $rows = $this->scopeConfig->getValue(
            self::CONFIG_RATES_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (is_string($rows) && $rows !== '') {
            $decoded = json_decode($rows, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $rows = $decoded;
            }
        }

        $map = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $optionId = (string) ($row['option_id'] ?? '');
                if ($optionId === '') {
                    continue;
                }
                $map[$optionId] = (float) ($row['price'] ?? 0);
            }
        }

        if (!$map) {
            return $result;
        }

        $max = null;

        foreach ($request->getAllItems() as $item) {
            // We'll check BOTH child and parent (configurable/bundle cases)
            $candidates = [];

            $product = $item->getProduct();
            if ($product && $product->getId()) {
                $candidates[] = (int) $product->getId();
            }

            $parentItem = $item->getParentItem();
            if ($parentItem && $parentItem->getProduct() && $parentItem->getProduct()->getId()) {
                $candidates[] = (int) $parentItem->getProduct()->getId();
            }

            $candidates = array_values(array_unique($candidates));
            if (!$candidates) {
                continue;
            }

            foreach ($candidates as $productId) {
                // Raw value avoids "attribute not loaded" issues
                $raw = $this->productResource->getAttributeRawValue(
                    $productId,
                    self::ATTR_CODE,
                    $storeId
                );

                // Normalize raw value to a single option id string
                if (is_array($raw)) {
                    // sometimes raw comes back like [attr_code => value]
                    $raw = $raw[self::ATTR_CODE] ?? reset($raw) ?? '';
                }

                $optionId = is_scalar($raw) ? (string) $raw : '';

                if ($optionId === '' || !array_key_exists($optionId, $map)) {
                    continue;
                }

                $price = (float) $map[$optionId];
                $max = $max === null ? $price : max($max, $price);
            }
        }

        // No matching items => keep Flat Rate fallback price
        if ($max === null) {
            return $result;
        }

        foreach ($result->getAllRates() as $rate) {
            if ($rate->getCarrier() !== 'flatrate') {
                continue;
            }
            $rate->setPrice($max);
            $rate->setCost($max);
        }

        return $result;
    }
}

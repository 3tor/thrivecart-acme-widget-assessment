<?php

namespace App\Models;

use App\Contracts\ProductInterface;
use App\Enums\Offer;
use App\Services\BuyOneGetSecondHalfService;
use InvalidArgumentException;

class Basket
{
    protected $productCatalog;

    /**
     * @var array<ProductInterface>
     */
    protected array $products = [];

    /**
     * @var array<array{limit: int, cost: float}>
     */
    protected array $deliveryRules = [];

    protected array $offers = [];

    public function __construct(ProductCatalog $productCatalog)
    {
        $this->productCatalog = $productCatalog;
    }

    /**
     * Get the products in the basket
     * @return ProductInterface[]
     */
    public function getProducts(): array 
    {
        return $this->products;
    }

    /**
     * Add a delivery rule
     * @param int $limit
     * @param float $cost
     * @return void
     */
    public function addDeliveryRule(int $limit, float $cost): void 
    {
        if ($limit <= 0 || $cost < 0) {
            throw new \InvalidArgumentException("Invalid delivery rule.");
        }

        $this->deliveryRules[] = ['limit' => $limit, 'cost' => $cost];
    }

    /**
     * Get delivery rules
     */
    public function getDeliveryRules(): array 
    {
        return $this->deliveryRules;
    }

    /**
     * Add a special offer
     */
    public function addOffer(Offer $offer): void
    {
        $offerType = $offer->type();
        if (is_null($offerType)) {
            throw new InvalidArgumentException("Offer type not supported");
        }

        $this->offers[] = $offerType;
    }

    /**
     * Add a product to basket with code
     */
    public function add(string $productCode): void
    {
        $product = $this->productCatalog->findProductByCode($productCode);

        if (empty($productCode) || is_null($product)) {
            throw new InvalidArgumentException("Product with code {$productCode} not found");
        }

        $this->products[] = $product;
    }

    /**
     * Calculate total amount
     * @return float
     */
    public function total(): float
    {
        $total = 0.0;

        foreach ($this->products as $product) {
            $total += $product->getPrice();
        }

        $total -= $this->calculateOffers(); 
        $total += $this->calculateDeliveryCharges($total);

        return $total;
    }

    /**
     * Calculate special product offers
     * @return float
     */
    public function calculateOffers(): float
    {
        $discount = 0.0;

        foreach ($this->offers as $offer) {
            if ($offer === Offer::BUY_ONE_GET_SECOND_HALF->type()) {
                $discount += new BuyOneGetSecondHalfService()->calculateDiscount($this);
            }
        }

        return $discount;
    }

    /**
     * Calculate delivery charges
     * @return float
     */
    public function calculateDeliveryCharges(float $total): float
    {
        usort($this->deliveryRules, function($a, $b) {
            return $a['limit'] <=> $b['limit'];
        });
      
        foreach ($this->deliveryRules as $rule) {
            if ($total < $rule['limit']) {
                return $rule['cost'];
            }
        }

        return 0.0;
    }

    /**
     * Find a product using product code
     * @param string $code
     * @return ProductInterface|null
     */
    public function findProductByCode(string $code): ?ProductInterface 
    {
        $result = null;

        array_map(function($product) use ($code, &$result) {
            if ($product->getCode() === $code) {
                $result = $product;
            }
        }, $this->products);

        return $result;
    }
}
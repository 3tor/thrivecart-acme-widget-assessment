<?php

namespace App\Models;

use App\Contracts\ProductInterface;

class ProductCatalog
{
    /**
     * @var array<ProductInterface>
    */
    protected array $products = [];

    /**
     * Add product to catalog
     * @param ProductInterface $product
     */
    public function addProduct(ProductInterface $product): void 
    {
        $this->products[$product->getCode()] = $product;
    }

    /**
     * Get all products in catalog
     * @return ProductInterface[]
     */
    public function getProducts(): array 
    {
        return $this->products;
    }

    /**
     * Find a product by code
     * @param string $productCode
     * @return ProductInterface|null
     */
    public function findProductByCode(string $productCode): ?ProductInterface
    {
        return $this->products[$productCode] ?? null;
    }
}
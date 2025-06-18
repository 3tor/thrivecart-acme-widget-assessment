<?php

namespace tests;

use App\Enums\Offer;
use App\Models\Basket;
use App\Models\Product;
use App\Models\ProductCatalog;
use PHPUnit\Framework\TestCase;

class BasketTest extends TestCase
{
    protected Basket $basket;
    protected ProductCatalog $productCatalog;

    protected function setUp(): void 
    {
        $this->productCatalog = new ProductCatalog();
        $this->basket = new Basket($this->productCatalog);

        $this->productCatalog->addProduct(new Product('Red Widget', 32.95, 'R01'));
        $this->productCatalog->addProduct(new Product('Green Widget', 24.95, 'G01'));
        $this->productCatalog->addProduct(new Product('Blue Widget', 7.95, 'B01'));
    }

    public function testAddProductToBasket(): void 
    {
        $product = $this->productCatalog->findProductByCode('R01');
        $this->basket->add($product->getCode());

        $this->assertCount(1, $this->basket->getProducts());
        $this->assertSame($product, $this->productCatalog->getProducts()['R01']);
    }

    public function testTotalCostWithoutSpecialOffer(): void
    {
        $this->basket->add('B01');
        $this->basket->add('G01');
        $this->basket->addDeliveryRule(50, 4.95);
        $this->basket->addDeliveryRule(90, 2.95);

        $this->assertEquals(37.85, $this->basket->total());
    }

    public function testTotalCostWithSpecialOfferAndDelivery(): void
    {
        $this->basket->add('B01');
        $this->basket->add('B01');
        $this->basket->add('R01');
        $this->basket->add('R01');
        $this->basket->add('R01');
        $this->basket->addDeliveryRule(50, 4.95);
        $this->basket->addDeliveryRule(90, 2.95);
        $this->basket->addOffer(Offer::BUY_ONE_GET_SECOND_HALF);

        $this->assertEquals(98.27, $this->basket->total());
    }
}
<?php

namespace App\Commands;

use App\Enums\Offer;
use App\Models\Basket;
use App\Models\Product;
use App\Models\ProductCatalog;

class BasketCommand
{
    protected ProductCatalog $productCatalog;
    protected Basket $basket;

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $this->productCatalog = new ProductCatalog();
        $this->basket = new Basket($this->productCatalog);
    }

    public function handle(): void
    {
        while (true) {
            $this->displayMenu();
            $choice = $this->prompt("\nEnter your option: ");
            switch ($choice) {
                case 1:
                    $this->addProductToBasket();
                    break;
                case 2:
                    $this->calculate();
                     break;
                case 3:
                    $this->clearBasket();
                    break;
                case 4:
                    $this->displayProductCatalog();
                    break;
                case 5:
                    $this->displayBasket();
                    break;
                case 6:
                    return;
                default:
                    $this->display("Invalid option. Please try again.", true, 'red');
            }
        }
    }

    /**
     * Print the menu
     */
    protected function displayMenu(): void 
    {
        $items = [
            'Add products to the basket',
            'Calculate total cost',
            'Clear basket',
            'Display product catalog',
            'Display basket',
            'Exit'
        ];

        $this->display("\nMenu\n", true);

        foreach ($items as $index => $item) {
            $this->display(($index + 1) . ". $item", true);
        }

    }

    /**
     * Display message to the console
     * @param string $message
     * @param bool $addNewLine
     * @param string $color
     */
    private function display(string $message, bool $addNewLine, string $color = 'default'): void 
    {
        $colors = [
        'red' => "\033[0;31m",
        'green' => "\033[0;32m",
        'default' => "\033[0m"
        ];

        echo $colors[$color] . $message . $colors['default'] . ($addNewLine ? "\n" : '');
    }

    /**
     * Prompt the user for input
     * @param string $message
     * @return string
     */
    private function prompt(string $message): string 
    {
        $this->display($message, false);

        $input = fgets(STDIN);
        return trim($input !== false ? $input : '');
    }

    /**
     * Set default product catalog
     */
    protected function setDefaultCatalogData(): void 
    {   
        $items = [
            ['name' => 'Red Widget', 'price' => 32.95, 'code' => 'R01'],
            ['name' => 'Green Widget', 'price' => 24.95, 'code' => 'G01'],
            ['name' => 'Blue Widget', 'price' => 7.95, 'code' => 'B01'],
        ];

        foreach ($items as $item) {
            $product = new Product($item['name'], $item['price'], $item['code']);
            $this->productCatalog->addProduct($product);
        }
    }

    /**
     * Set default delivery rules
     */
    protected function setDeliveryRules(): void
    {
        $deliveryRules = [
            ['limit' => 50, 'cost' => 4.95],
            ['limit' => 90, 'cost' => 2.95],
        ];

        foreach ($deliveryRules as $rules) {
            $this->basket->addDeliveryRule($rules['limit'], $rules['cost']);
        }
    }

    /**
     * Set special offer
     */
    protected function setOffer(): void
    {
        $this->basket->addOffer(Offer::BUY_ONE_GET_SECOND_HALF);
    }

    /**
     * Display product catalog
     */
    protected function displayProductCatalog(): void
    {
        if (empty($this->productCatalog->getProducts())) {
            $this->display('Product catalog is empty', true);
            return;
        }

        $this->display("Our product catalogue: ", true);
        foreach ($this->productCatalog->getProducts() as $product) {
            $this->display($product->getCode() . " - " . $product->getName() . " ($" . $product->getPrice() . ")", true);
        }
    }

    /**
     * Display delivery rules
     */
    protected function displayDeliveryRules(): void
    {
        if (empty($this->basket->getDeliveryRules())) {
            $this->display('Delivery rules is empty', true);
            return;
        }

        $this->display("Our delivery rules: ", true);
        foreach ($this->basket->getDeliveryRules() as $rules) {
            $this->display("Limit: {$rules['limit']}, Cost: {$rules['cost']}", true);
        }
    }

    /**
     * Initialize the product catalog data
     */
    protected function initializeData(): void
    {
        $this->setDefaultCatalogData();
        $this->setDeliveryRules();
        $this->setOffer();
    }

    /**
     * Add product to basket
     */
    public function addProductToBasket(): void
    {
        $this->initializeData();

        if (empty($this->productCatalog->getProducts())) {
            $this->display("No products found in catalog", true, 'red');
            return;
        }

        $this->displayProductCatalog();

        while (true) {
            $productCode = $this->prompt("Type product code to add to basket or press Enter to finish:: ");

            try {
                if (empty($productCode)) {
                    break;
                }

                $this->basket->add($productCode);
                $this->display("Product added to basket.", true, 'green');
    
                $continue = $this->prompt("Do you want to add another product? (yes/no): ");
                if (strtolower($continue) !== 'yes') {
                    break;
                }
            } catch (\Exception $e) {
                $this->display("Error: " . $e->getMessage(), true, 'red');
            }
        }
    }

    /**
     * Display contents of basket
     */
    protected function displayBasket(): bool 
    {
        if (empty($this->basket->getProducts())) {
            $this->display("Basket is empty.", true);
            return false;
        }
    
        $this->display("Your product basket: ", true);
        foreach ($this->basket->getProducts() as $product) {
            $this->display($product->getCode() . " - " . $product->getName() . " ($" . $product->getPrice() . ")", true);
        }
    
        return true;
    }

    /**
     * Calculate the total price
     */
    protected function calculate(): void 
    {
        if (!$this->displayBasket()) {
            return;
        }
        $this->display("Total: $" . $this->basket->total(), true, 'green');
    }

    /**
     * Calculate the total price
     */
    protected function clearBasket(): void 
    {
        $this->init();
        $this->display("Basket reset. Start again.", true, 'green');
    }

}
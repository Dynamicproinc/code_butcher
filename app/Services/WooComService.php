<?php 

use Automattic\WooCommerce\Client;

class WooComService
{
    protected $woocommerce;

    public function __construct()
    {
        $this->woocommerce = new Client(
            config('services.woocommerce.url'),           // e.g., https://yourstore.com
            config('services.woocommerce.key'),           // WooCommerce Consumer Key
            config('services.woocommerce.secret'),        // WooCommerce Consumer Secret
            [
                'version' => 'wc/v3',
                'timeout' => 30,
            ]
        );
    }

    // Update product stock quantity
    
    /**
     * Update a single simple product
     *
     * @param int $productId
     * @param array $data
     * @return array
     */
    public function updateProduct(int $productId, array $data)
    {
        return $this->woocommerce->put("products/{$productId}", $data);
    }

    /**
     * Update a product variation
     *
     * @param int $productId Parent product ID
     * @param int $variationId Variation ID
     * @param array $data
     * @return array
     */
    public function updateProductVariation(int $productId, int $variationId, array $data)
    {
        return $this->woocommerce->put("products/{$productId}/variations/{$variationId}", $data);
    }

    /**
     * Get current stock of a simple product
     *
     * @param int $productId
     * @return int|null
     */
    public function getProductStock(int $productId)
    {
        $product = $this->woocommerce->get("products/{$productId}");
        return $product['stock_quantity'] ?? null;
    }

    /**
     * Get all variations of a variable product with current stock
     *
     * @param int $productId
     * @return array
     */
    public function getProductVariationsStock(int $productId)
    {
        return $this->woocommerce->get("products/{$productId}/variations");
    }

    /**
     * Bulk update product variations
     *
     * @param int $productId Parent variable product ID
     * @param array $variations Array of variations to update
     *        Format: [
     *            ['id' => 123, 'stock_quantity' => 10, 'manage_stock' => true],
     *        ]
     * @return array
     */
    public function bulkUpdateVariations(int $productId, array $variations)
    {
        $payload = ['update' => $variations];

        return $this->woocommerce->post("products/{$productId}/variations/batch", $payload);
    }
}
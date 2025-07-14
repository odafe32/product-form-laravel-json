<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ProductController extends Controller
{
    private $jsonStoragePath;

    public function __construct()
    {
        // Path where the product data will be saved in JSON format
        $this->jsonStoragePath = storage_path('app/products.json');
    }

    
    //  Display the product management interface.
    
    public function index()
    {
        return view('products.home');
    }

    // Save a new product entry to the JSON file.
   
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0'
        ]);

        $products = $this->loadProducts();

        $product = [
            'id' => uniqid('prod_', true),
            'product_name' => $validated['product_name'],
            'quantity' => (int) $validated['quantity'],
            'price' => (float) $validated['price'],
            'submitted_at' => Carbon::now()->toIso8601String(),
            'total_value' => (int)$validated['quantity'] * (float)$validated['price']
        ];

        $products[] = $product;
        $this->saveProducts($products);

        return response()->json([
            'success' => true,
            'message' => 'New product added.',
            'product' => $product
        ]);
    }

    //  Retrieve all products and the total value sum.
    
    public function getAll(): JsonResponse
    {
        $products = $this->loadProducts();

        usort($products, fn($a, $b) => strtotime($b['submitted_at']) <=> strtotime($a['submitted_at']));

        $totalSum = array_reduce($products, fn($carry, $product) => $carry + $product['total_value'], 0);

        return response()->json([
            'products' => $products,
            'total_value_sum' => $totalSum
        ]);
    }

    // Update an existing product by its ID.
     
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0'
        ]);

        $products = $this->loadProducts();

        $updated = false;
        foreach ($products as &$product) {
            if ($product['id'] === $id) {
                $product['product_name'] = $validated['product_name'];
                $product['quantity'] = (int)$validated['quantity'];
                $product['price'] = (float)$validated['price'];
                $product['total_value'] = (int)$validated['quantity'] * (float)$validated['price'];
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $this->saveProducts($products);

        return response()->json([
            'success' => true,
            'message' => 'Product updated.',
            'product' => $product
        ]);
    }

    //  Remove a product by its ID.
    
    public function destroy($id): JsonResponse
    {
        $products = $this->loadProducts();

        $initialCount = count($products);
        $products = array_values(array_filter($products, fn($product) => $product['id'] !== $id));

        if ($initialCount === count($products)) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $this->saveProducts($products);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }

//    Load all products from the JSON file.
  
    private function loadProducts(): array
    {
        if (!file_exists($this->jsonStoragePath)) {
            return [];
        }

        $contents = file_get_contents($this->jsonStoragePath);
        return json_decode($contents, true) ?? [];
    }

//   Persist products array back to the JSON file.
 
    private function saveProducts(array $products): void
    {
        $directory = dirname($this->jsonStoragePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $jsonData = json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if (file_put_contents($this->jsonStoragePath, $jsonData) === false) {
            throw new \RuntimeException('Failed to write products data to file.');
        }
    }
}

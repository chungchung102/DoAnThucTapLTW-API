<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductService;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\News;
use App\Models\User;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class MigrateData extends Command
{
    protected $signature = 'migrate:data';
    protected $description = 'Migrate data from API and session to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->migrateProductsAndNews();
        $this->migrateSessionData();
        $this->info('Data migration completed successfully.');
    }

    protected function migrateProductsAndNews()
    {
        $productService = app(ProductService::class);
        $data = $productService->fetchData(100, 'all');

        if (isset($data['error'])) {
            $this->error('Failed to fetch data from API.');
            return;
        }

        foreach ($data['products'] as $product) {
            $category = Category::firstOrCreate(['name' => $product['category']]);
            $prod = Product::updateOrCreate(
                ['id' => $product['id']],
                [
                    'category_id' => $category->id,
                    'title' => $product['tieude'],
                    'price' => $product['gia'] ?? 0,
                    'main_image' => $product['hinhdaidien'],
                ]
            );

            if (!empty($product['hinhanh'])) {
                foreach ($product['hinhanh'] as $image) {
                    ProductImage::updateOrCreate(
                        ['product_id' => $prod->id, 'image_url' => $image['hinhdaidien']],
                        ['image_url' => $image['hinhdaidien']]
                    );
                }
            }

            $detail = $productService->fetchProductDetail($product['id']);
            if (!isset($detail['error'])) {
                $prod->update(['detail_content' => $detail['noidungchitiet'] ?? null]);
                if (!empty($detail['hinhlienquan'])) {
                    foreach ($detail['hinhlienquan'] as $image) {
                        ProductImage::updateOrCreate(
                            ['product_id' => $prod->id, 'image_url' => $image['hinhdaidien']],
                            ['image_url' => $image['hinhdaidien']]
                        );
                    }
                }
            }
        }

        foreach ($data['news'] as $news) {
            News::updateOrCreate(
                ['id' => $news['id']],
                [
                    'title' => $news['tieude'],
                    'main_image' => $news['hinhdaidien'],
                    'summary' => $news['tomtat'] ?? null,
                    'content' => $news['noidungchitiet'] ?? null,
                    'published_at' => $news['ngaydang'] ?? now(),
                ]
            );
        }

        $this->info('Products and news migrated successfully.');
    }

    protected function migrateSessionData()
    {
        $userEmail = Session::get('user_email');
        if (!$userEmail) {
            $this->warn('No user session found.');
            return;
        }

        $user = User::where('email', $userEmail)->first();
        if (!$user) {
            $user = User::create([
                'full_name' => 'Imported User',
                'email' => $userEmail,
                'password' => Hash::make('default_password'),
                'phone' => '0900000000',
            ]);
        }

        $wishlist = Session::get('wishlist', []);
        foreach ($wishlist as $id => $item) {
            if (Product::find($id)) {
                Wishlist::updateOrCreate(
                    ['user_id' => $user->id, 'product_id' => $id],
                    ['product_id' => $id]
                );
            }
        }

        $cart = Session::get('cart', []);
        foreach ($cart as $id => $item) {
            if (Product::find($id)) {
                Cart::updateOrCreate(
                    ['user_id' => $user->id, 'product_id' => $id],
                    ['quantity' => $item['quantity']]
                );
            }
        }

        $purchaseHistory = Session::get('purchase_history', []);
        foreach ($purchaseHistory as $orderData) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_id' => $orderData['order_id'],
                'total' => $orderData['total'],
                'full_name' => $orderData['payment_data']['full_name'],
                'email' => $orderData['payment_data']['email'],
                'phone' => $orderData['payment_data']['phone'],
                'address' => $orderData['payment_data']['address'],
                'order_date' => $orderData['created_at'],
            ]);

            foreach ($orderData['items'] as $id => $item) {
                if (Product::find($id)) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $id,
                        'quantity' => $item['quantity'],
                        'price' => $item['gia'],
                    ]);
                }
            }
        }

        Session::forget(['wishlist', 'cart', 'purchase_history']);
        $this->info('Session data migrated successfully.');
    }
}
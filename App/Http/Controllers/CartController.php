<?php
namespace App\Http\Controllers;
use App\Services\ProductService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected $productService;
    protected $cartService;

    public function __construct(ProductService $productService, CartService $cartService)
    {
        $this->productService = $productService;
        $this->cartService = $cartService;
    }

   public function add($productId, Request $request)
{
    Log::info("Attempting to add product to cart", ['productId' => $productId]);

    if (!is_numeric($productId) || $productId <= 0) {
        Log::error("Invalid product ID", ['productId' => $productId]);
        return redirect()->back()->with('error', 'ID sản phẩm không hợp lệ');
    }

    $product = $this->productService->fetchProductDetail($productId);
    Log::info("Product fetch result", ['productId' => $productId, 'product' => $product]);

    if (isset($product['error'])) {
        Log::error("Failed to fetch product detail", ['productId' => $productId, 'error' => $product['error']]);
        return redirect()->back()->with('error', 'Sản phẩm không tồn tại');
    }

    if (empty($product['hinhdaidien']) || strpos($product['hinhdaidien'], 'placeholder') !== false) {
        Log::error("Invalid product image", ['productId' => $productId, 'hinhdaidien' => $product['hinhdaidien']]);
        return redirect()->back()->with('error', 'Sản phẩm không có hình ảnh hợp lệ');
    }

    $quantity = (int) $request->input('quantity', 1);
    $success = $this->cartService->addToCart($product, $quantity);

    Log::info("Cart after adding", ['cart' => session('cart')]);

    return redirect()->route('products.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng');
}


   public function view()
{
    return view('cart.view', [
        'cart' => $this->cartService->getCart(),
        'total' => $this->cartService->getCartTotal(),
        'quantity' => $this->cartService->getTotalQuantity(),
    ]);
}


   public function update(Request $request)
{
    $productId = (int) $request->input('product_id');
    $quantity = (int) $request->input('quantity');

    $success = $this->cartService->updateCart($productId, $quantity);

    if ($success) {
        return redirect()->route('cart.view')->with('success', 'Cập nhật giỏ hàng thành công');
    }

    return redirect()->route('cart.view')->with('error', 'Không tìm thấy sản phẩm trong giỏ');
}


   public function remove($productId)
{
    $success = $this->cartService->removeFromCart($productId);

    return redirect()->route('cart.view')->with(
        $success ? 'success' : 'error',
        $success ? 'Đã xóa sản phẩm khỏi giỏ hàng' : 'Không tìm thấy sản phẩm trong giỏ'
    );
}

   public function clear()
{
    $this->cartService->clearCart();
    return redirect()->route('cart.view')->with('success', 'Đã xóa toàn bộ giỏ hàng');
}

}
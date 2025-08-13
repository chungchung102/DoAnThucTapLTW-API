<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class CartService
{
    protected $sessionKey = 'cart';

    /**
     * Lấy toàn bộ sản phẩm trong giỏ hàng
     */
    public function getCart(): array
    {
        return session($this->sessionKey, []);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    // Trong CartService
public function addToCart(array $product, int $quantity = 1): bool
{
    $cart = session('cart', []);
    $productId = $product['id'];

    $cart[$productId] = [
        'tieude' => $product['tieude'],
        'gia' => $product['gia'] ?? 0,
        'quantity' => ($cart[$productId]['quantity'] ?? 0) + $quantity,
        'hinhdaidien' => $product['hinhdaidien']
    ];

    session(['cart' => $cart]);
    return true;
}

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function updateCart(int $productId, int $quantity): bool
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            if ($quantity > 0) {
                $cart[$productId]['quantity'] = $quantity;
            } else {
                unset($cart[$productId]);
            }

            session([$this->sessionKey => $cart]);
            return true;
        }

        return false;
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart(int $productId): bool
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session([$this->sessionKey => $cart]);
            return true;
        }

        return false;
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart(): void
    {
        session()->forget($this->sessionKey);
    }

    /**
     * Tính tổng số lượng sản phẩm trong giỏ
     */
    public function getTotalQuantity(): int
    {
        $cart = $this->getCart();
        return array_sum(array_column($cart, 'quantity'));
    }

    /**
     * Tính tổng tiền giỏ hàng
     */
    public function getCartTotal(): float
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += ($item['gia'] * $item['quantity']);
        }

        return $total;
    }

    /**
     * Kiểm tra xem sản phẩm đã có trong giỏ chưa
     */
    public function hasProduct(int $productId): bool
    {
        return isset($this->getCart()[$productId]);
    }
}

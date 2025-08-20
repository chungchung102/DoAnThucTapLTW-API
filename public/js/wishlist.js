// ====== Cookie Helper ======
function getCookie(name) {
    let dc = document.cookie;
    let prefix = name + "=";
    let begin = dc.indexOf("; " + prefix);
    if (begin === -1) {
        begin = dc.indexOf(prefix);
        if (begin !== 0) return null;
    } else {
        begin += 2;
    }
    let end = document.cookie.indexOf(";", begin);
    if (end === -1) {
        end = dc.length;
    }
    return decodeURIComponent(dc.substring(begin + prefix.length, end));
}

function setCookie(name, value, days = 365) {
    let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (encodeURIComponent(value) || "")  + expires + "; path=/";
}

// ====== Đảm bảo có cookie WishlistMabaogia từ API ngoài ======
(function(){
    let wishlistCookie = getCookie("WishlistMabaogia");
    if (!wishlistCookie) {
        $.ajax({
            url: "/api/proxy-cookie",
            method: "GET",
            dataType: "json",
            success: function(res) {
                let wishlistValue = null;
                if (Array.isArray(res)) {
                    for (let i = 0; i < res.length; i++) {
                        if (typeof res[i].WishlistMabaogia !== 'undefined' && res[i].WishlistMabaogia) {
                            wishlistValue = res[i].WishlistMabaogia;
                            break;
                        }
                    }
                }
                if (wishlistValue) {
                    setCookie("WishlistMabaogia", wishlistValue, 365);
                } else {
                    let randomId = Date.now().toString(36) + Math.random().toString(36).substring(2, 12);
                    setCookie("WishlistMabaogia", randomId, 365);
                }
            },
            error: function() {
                let randomId = Date.now().toString(36) + Math.random().toString(36).substring(2, 12);
                setCookie("WishlistMabaogia", randomId, 365);
            }
        });
    }
})();

// ====== Cập nhật số lượng wishlist trên header ======
function reloadWishlistCount() {
    $.get('/wishlist/count', function(res){
        $('#wishlist-count').text(res.count);
    });
}

// ====== Cập nhật số lượng giỏ hàng trên header ======
function reloadCartCount() {
    $.get('/cart/count', function(res){
        $('#cart-count').text(res.count);
    });
}

// ====== Thêm sản phẩm vào wishlist (AJAX) ======
function addToWishlist(productId, callback) {
    let isLoggedIn = $('meta[name="user-logged"]').attr('content') === '1';
    let requestData = { productId: productId };
    if (isLoggedIn) {
        requestData.userid = $('meta[name="user-id"]').attr('content');
        requestData.pass = $('meta[name="user-pass"]').attr('content');
    }
    let wishlistCookie = getCookie('WishlistMabaogia');
    $.ajax({
        url: '/api/proxy-wishlist-add',
        method: 'POST',
        data: { productId, wishlistCookie },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            reloadWishlistCount();
            showCustomToast('Đã thêm vào yêu thích!');
            if (typeof callback === "function") callback(true);
        },
        error: function() {
            showCustomToast('Thêm vào yêu thích thất bại!');
            if (typeof callback === "function") callback(false);
        }
    });
}

// ====== Xoá sản phẩm khỏi wishlist (AJAX) ======
function removeFromWishlist(productId, callback) {
    let wishlistCookie = getCookie('WishlistMabaogia');
    $.ajax({
        url: '/api/proxy-wishlist-remove',
        method: 'POST',
        data: { productId, wishlistCookie },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            reloadWishlistCount();
            showCustomToast('Đã xóa khỏi yêu thích!');
            if (typeof callback === "function") callback(true);
        },
        error: function() {
            showCustomToast('Xóa khỏi yêu thích thất bại!');
            if (typeof callback === "function") callback(false);
        }
    });
}

// ====== Hiển thị toast/thông báo ======
function showCustomToast(msg) {
    let toast = $('#custom-toast');
    if (toast.length === 0) {
        toast = $('<div id="custom-toast"></div>').appendTo('body');
        toast.css({
            position: 'fixed',
            bottom: '60px',
            left: '50%',
            transform: 'translateX(-50%)',
            background: '#222',
            color: '#fff',
            padding: '12px 24px',
            borderRadius: '24px',
            zIndex: 99999,
            fontSize: '15px',
            display: 'none'
        });
    }
    toast.html(msg).fadeIn(300);
    setTimeout(function(){ toast.fadeOut(300); }, 2000);
}

// ====== Gắn sự kiện cho nút yêu thích ======
$(function(){
    reloadWishlistCount();

    $(document).on('click', '.btn-add-wishlist', function(e){
        e.preventDefault();
        let productId = $(this).data('id');
        addToWishlist(productId, function(success){
            if(success) {
                $(`.btn-add-wishlist[data-id="${productId}"]`).addClass('active');
            }
        });
    });

    $(document).on('click', '.btn-remove-wishlist', function(e){
        e.preventDefault();
        let productId = $(this).data('id');
        removeFromWishlist(productId, function(success){
            if(success) {
                $(`.btn-add-wishlist[data-id="${productId}"]`).removeClass('active');
            }
            $(`#row-wishlist-${productId}`).remove();
        });
    });

    // Thêm sản phẩm vào giỏ hàng (AJAX)
    $(document).on('click', '.btn-add-cart', function(e){
        e.preventDefault();
        var productId = $(this).data('id');
        $.ajax({
            url: '/cart/add',
            method: 'POST',
            data: {
                product_id: productId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res){
                showCustomToast('Đã thêm vào giỏ hàng!');
                reloadCartCount(); // <-- Gọi hàm này ở đây
            },
            error: function(){
                showCustomToast('Thêm vào giỏ hàng thất bại!');
            }
        });
    });

    // Xoá sản phẩm khỏi giỏ hàng (AJAX)
    $(document).on('click', '.btn-remove-cart', function(e){
        e.preventDefault();
        var productId = $(this).data('id');
        $.ajax({
            url: '/cart/remove/' + productId,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res){
                showCustomToast('Đã xóa khỏi giỏ hàng!');
                if (typeof reloadCartCount === "function") reloadCartCount();
                $('#row-cart-' + productId).remove();
            },
            error: function(){
                showCustomToast('Xóa khỏi giỏ hàng thất bại!');
            }
        });
    });

    $(document).on('click', '.btn-buy-wishlist', function(e){
        e.preventDefault();
        var productId = $(this).data('id');
        $.ajax({
            url: '/cart/add',
            method: 'POST',
            data: {
                product_id: productId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res){
                // Sau khi thêm vào giỏ hàng thành công, chuyển sang trang giỏ hàng
                window.location.href = '/cart';
            },
            error: function(){
                showCustomToast('Thêm vào giỏ hàng thất bại!');
            }
        });
    });
});

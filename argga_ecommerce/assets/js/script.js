// Configuration
const API_URL = 'http://localhost/arggaEcommerce/backend/api/';
let currentUser = null;

// Check if user is logged in (check session via PHP include)
async function checkUserSession() {
    try {
        const response = await fetch('pages/check_session.php');
        const data = await response.json();
        if (data.logged_in) {
            currentUser = data.user;
            document.getElementById('authButtons').style.display = 'none';
            document.getElementById('userWelcome').style.display = 'flex';
            document.getElementById('usernameDisplay').innerText = currentUser.name;
            loadCartCount();
        }
    } catch (error) {
        console.error('Session check error:', error);
    }
}

// Load cart count for header
async function loadCartCount() {
    if (!currentUser) return;
    try {
        const response = await fetch(`${API_URL}get_cart.php`);
        const data = await response.json();
        if (data.success) {
            document.getElementById('cartCountBadge').innerText = data.item_count || 0;
        }
    } catch (error) {
        console.error('Cart count error:', error);
    }
}

// Add to cart function
async function addToCart(productId, productName, productPrice) {
    if (!currentUser) {
        showToast('Please login first!', true);
        window.location.href = 'pages/login.php';
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}add_to_cart.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        });
        const result = await response.json();
        
        if (result.success) {
            showToast(result.message);
            loadCartCount();
        } else {
            showToast(result.message, true);
        }
    } catch (error) {
        showToast('Error adding to cart', true);
    }
}

// Show toast message
function showToast(message, isError = false) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.background = isError ? '#e74c3c' : '#1e6b42';
    toast.innerHTML = `<i class="fas ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    checkUserSession();
    
    // Add to cart button listeners
    document.querySelectorAll('.add-to-cart-btn, .add-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productPrice = this.dataset.price;
            addToCart(productId, productName, productPrice);
        });
    });
});
const API_URL = 'http://localhost/project_folder/admin/';
let currentUser = null;

// Show toast message
function showToast(message, isError = false) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.background = isError ? '#e74c3c' : '#1e6b42';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Check admin authentication
async function checkAuth() {
    const response = await fetch(`${API_URL}login.php?check=1`);
    const data = await response.json();
    if (!data.success) {
        window.location.href = 'index.html';
    } else {
        currentUser = data.user;
        document.getElementById('adminName').textContent = currentUser.name;
        loadProducts();
        loadStats();
    }
}

// Load product statistics
async function loadStats() {
    const response = await fetch(`${API_URL}get_products.php`);
    const products = await response.json();
    const totalProducts = products.length;
    const totalStock = products.reduce((sum, p) => sum + (p.stock || 0), 0);
    const totalValue = products.reduce((sum, p) => sum + (p.price * (p.stock || 0)), 0);
    
    document.getElementById('totalProducts').textContent = totalProducts;
    document.getElementById('totalStock').textContent = totalStock;
    document.getElementById('totalValue').textContent = '৳' + totalValue.toFixed(2);
}

// Load products table
async function loadProducts() {
    const response = await fetch(`${API_URL}get_products.php`);
    const products = await response.json();
    
    const tbody = document.getElementById('productsTableBody');
    tbody.innerHTML = products.map(product => `
        <tr>
            <td>${product.id}</td>
            <td><img src="${product.image ? '../backend/uploads/' + product.image : 'https://via.placeholder.com/50'}" class="product-img" onerror="this.src='https://via.placeholder.com/50'"></td>
            <td>${product.name}</td>
            <td>৳${product.price}</td>
            <td>${product.stock || 0}</td>
            <td>${product.category || '-'}</td>
            <td>
                <div class="action-buttons">
                    <button class="edit-btn" onclick="editProduct(${product.id})">Edit</button>
                    <button class="delete-btn" onclick="deleteProduct(${product.id})">Delete</button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Open add product modal
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Product';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('productModal').style.display = 'flex';
}

// Edit product
async function editProduct(id) {
    const response = await fetch(`${API_URL}get_products.php?id=${id}`);
    const product = await response.json();
    
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.name;
    document.getElementById('productPrice').value = product.price;
    document.getElementById('productOldPrice').value = product.old_price || '';
    document.getElementById('productDiscount').value = product.discount || '';
    document.getElementById('productCategory').value = product.category || '';
    document.getElementById('productStock').value = product.stock || 0;
    document.getElementById('productDescription').value = product.description || '';
    
    if (product.image) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = `<img src="../backend/uploads/${product.image}" style="max-width:150px; border-radius:8px;">`;
        preview.style.display = 'block';
    } else {
        document.getElementById('imagePreview').style.display = 'none';
    }
    
    document.getElementById('productModal').style.display = 'flex';
}

// Delete product
async function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        const response = await fetch(`${API_URL}delete_product.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        const result = await response.json();
        if (result.success) {
            showToast('Product deleted successfully');
            loadProducts();
            loadStats();
        } else {
            showToast('Failed to delete product', true);
        }
    }
}

// Save product (add/edit)
async function saveProduct(event) {
    event.preventDefault();
    
    const productId = document.getElementById('productId').value;
    const formData = new FormData();
    formData.append('name', document.getElementById('productName').value);
    formData.append('price', document.getElementById('productPrice').value);
    formData.append('old_price', document.getElementById('productOldPrice').value);
    formData.append('discount', document.getElementById('productDiscount').value);
    formData.append('category', document.getElementById('productCategory').value);
    formData.append('stock', document.getElementById('productStock').value);
    formData.append('description', document.getElementById('productDescription').value);
    
    const imageFile = document.getElementById('productImage').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    let url = `${API_URL}add_product.php`;
    if (productId) {
        formData.append('id', productId);
        url = `${API_URL}update_product.php`;
    }
    
    const response = await fetch(url, {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    if (result.success) {
        showToast(productId ? 'Product updated successfully' : 'Product added successfully');
        document.getElementById('productModal').style.display = 'none';
        loadProducts();
        loadStats();
    } else {
        showToast(result.message || 'Failed to save product', true);
    }
}

// Image preview
document.getElementById('productImage')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = `<img src="${event.target.result}" style="max-width:150px; border-radius:8px;">`;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Close modal
function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}

// Logout
function logout() {
    window.location.href = `${API_URL}login.php?logout=1`;
}

// Initialize
checkAuth();
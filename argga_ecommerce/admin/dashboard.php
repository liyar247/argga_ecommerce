<?php
session_start();
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['is_admin'])) {
    header('Location: index.html');
    exit;
}

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'argga_ecommerce';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get statistics
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_stock = $conn->query("SELECT SUM(stock) as total FROM products")->fetch_assoc()['total'];
$total_value = $conn->query("SELECT SUM(price * stock) as value FROM products")->fetch_assoc()['value'];
$total_categories = $conn->query("SELECT COUNT(DISTINCT category) as count FROM products WHERE category IS NOT NULL AND category != ''")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - argGa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            background: #f0f2f5;
        }
        .admin-container {
            display: flex;
        }
        .sidebar {
            width: 260px;
            background: #0b4f2e;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
        }
        .sidebar h2 {
            padding: 0 20px 20px;
            border-bottom: 1px solid #2e7d5a;
            margin-bottom: 20px;
        }
        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar nav a:hover, .sidebar nav a.active {
            background: #1e6b42;
        }
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 20px;
        }
        .header {
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
            color: #0b4f2e;
        }
        .products-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .btn-add {
            background: #0b4f2e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .search-box {
            display: flex;
            gap: 10px;
        }
        .search-box input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 250px;
        }
        .search-box button {
            padding: 8px 15px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .edit-btn, .delete-btn {
            padding: 5px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .edit-btn {
            background: #3498db;
            color: white;
        }
        .delete-btn {
            background: #e74c3c;
            color: white;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: white;
            width: 90%;
            max-width: 550px;
            border-radius: 12px;
            padding: 25px;
            max-height: 85vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .close {
            font-size: 28px;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .image-preview {
            margin-top: 10px;
            max-width: 150px;
        }
        .image-preview img {
            width: 100%;
            border-radius: 8px;
        }
        .btn-submit {
            background: #0b4f2e;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
            }
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2><i class="fas fa-store"></i> argGa Admin</h2>
            <nav>
                <a href="#" class="active" onclick="showSection('products')"><i class="fas fa-box"></i> Products</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="#" onclick="showSection('customers')"><i class="fas fa-users"></i> Customers</a>
            </nav>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h2>Welcome, <span id="adminName"><?php echo $_SESSION['admin_name']; ?></span></h2>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats">
                <div class="stat-card">
                    <h3><i class="fas fa-box"></i> Total Products</h3>
                    <div class="number"><?php echo $total_products; ?></div>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-chart-line"></i> Total Stock</h3>
                    <div class="number"><?php echo $total_stock ?: 0; ?></div>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-dollar-sign"></i> Inventory Value</h3>
                    <div class="number">৳<?php echo number_format($total_value ?: 0, 2); ?></div>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-tags"></i> Categories</h3>
                    <div class="number"><?php echo $total_categories ?: 0; ?></div>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-shopping-cart"></i> Total Orders</h3>
                    <div class="number"><?php echo $total_orders ?: 0; ?></div>
                </div>
                <div class="stat-card">
                    <h3><i class="fas fa-clock"></i> Pending Orders</h3>
                    <div class="number"><?php echo $pending_orders ?: 0; ?></div>
                </div>
            </div>
            
            <!-- Products Section -->
            <div id="productsSection" class="products-section">
                <div class="section-header">
                    <h3><i class="fas fa-box"></i> Product Management</h3>
                    <div style="display: flex; gap: 10px;">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Search products...">
                            <button onclick="searchProducts()"><i class="fas fa-search"></i></button>
                        </div>
                        <button class="btn-add" onclick="openAddModal()"><i class="fas fa-plus"></i> Add Product</button>
                    </div>
                </div>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <tr><td colspan="7" style="text-align:center;">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Customers Section -->
            <div id="customersSection" style="display:none;" class="products-section">
                <h3><i class="fas fa-users"></i> Customers</h3>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr><th>ID</th><th>Name</th><th>Email</th><th>Registered</th><th>Orders</th></tr>
                        </thead>
                        <tbody id="customersTableBody">
                            <tr><td colspan="5" style="text-align:center;">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Product</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <input type="hidden" id="productId" name="id">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" id="productName" name="name" required>
                </div>
                <div class="form-group">
                    <label>Price (৳) *</label>
                    <input type="number" id="productPrice" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Old Price (৳)</label>
                    <input type="number" id="productOldPrice" name="old_price" step="0.01">
                </div>
                <div class="form-group">
                    <label>Discount (%)</label>
                    <input type="text" id="productDiscount" name="discount" placeholder="e.g., 35% OFF">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select id="productCategory" name="category">
                        <option value="">Select Category</option>
                        <option value="medicine">Medicine</option>
                        <option value="supplements">Supplements</option>
                        <option value="nutrition">Nutrition</option>
                        <option value="baby">Baby & Mom Care</option>
                        <option value="herbal">Herbal</option>
                        <option value="beauty">Beauty</option>
                        <option value="healthcare">Healthcare</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" id="productStock" name="stock" value="10">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="productDescription" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" id="productImage" name="image" accept="image/*">
                    <div id="imagePreview" class="image-preview" style="display:none;"></div>
                </div>
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Save Product</button>
            </form>
        </div>
    </div>
    
    <script>
        const API_URL = '';
        
        function showToast(message, isError = false) {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.style.background = isError ? '#e74c3c' : '#1e6b42';
            toast.innerHTML = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
        
        async function loadStats() {
            const response = await fetch('get_products.php');
            const products = await response.json();
            // Stats are loaded from PHP already
        }
        
        async function loadProducts(search = '') {
            let url = 'get_products.php';
            if (search) url += '?search=' + encodeURIComponent(search);
            const response = await fetch(url);
            const products = await response.json();
            const tbody = document.getElementById('productsTableBody');
            
            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No products found</td></tr>';
                return;
            }
            
            tbody.innerHTML = products.map(product => `
                <tr>
                    <td>${product.id}</td>
                    <td><img src="../backend/uploads/${product.image || 'placeholder.jpg'}" class="product-img" onerror="this.src='https://via.placeholder.com/50'"></td>
                    <td><strong>${escapeHtml(product.name)}</strong></td>
                    <td>৳${parseFloat(product.price).toFixed(2)}</td>
                    <td>${product.stock || 0}</td>
                    <td>${product.category || '-'}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="edit-btn" onclick="editProduct(${product.id})"><i class="fas fa-edit"></i> Edit</button>
                            <button class="delete-btn" onclick="deleteProduct(${product.id})"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        
        function searchProducts() {
            const searchTerm = document.getElementById('searchInput').value;
            loadProducts(searchTerm);
        }
        
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('productModal').style.display = 'flex';
        }
        
        async function editProduct(id) {
            const response = await fetch(`get_products.php?id=${id}`);
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
                document.getElementById('imagePreview').innerHTML = `<img src="../backend/uploads/${product.image}" style="max-width:150px;">`;
                document.getElementById('imagePreview').style.display = 'block';
            }
            document.getElementById('productModal').style.display = 'flex';
        }
        
        async function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                const response = await fetch('delete_product.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Product deleted successfully');
                    loadProducts();
                } else {
                    showToast('Failed to delete product', true);
                }
            }
        }
        
        document.getElementById('productForm').addEventListener('submit', async (e) => {
            e.preventDefault();
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
            if (imageFile) formData.append('image', imageFile);
            let url = 'add_product.php';
            if (productId) {
                formData.append('id', productId);
                url = 'update_product.php';
            }
            const response = await fetch(url, { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                showToast(productId ? 'Product updated' : 'Product added');
                closeModal();
                loadProducts();
            } else {
                showToast(result.message || 'Failed', true);
            }
        });
        
        document.getElementById('productImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('imagePreview').innerHTML = `<img src="${event.target.result}" style="max-width:150px;">`;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
        
        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }
        
        async function loadCustomers() {
            const response = await fetch('../backend/api/get_users.php');
            const customers = await response.json();
            const tbody = document.getElementById('customersTableBody');
            if (customers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No customers found</td></tr>';
                return;
            }
            tbody.innerHTML = customers.map(c => `
                <tr>
                    <td>${c.id}</td>
                    <td>${escapeHtml(c.name)}</td>
                    <td>${c.email}</td>
                    <td>${c.created_at || '-'}</td>
                    <td>0</td>
                </tr>
            `).join('');
        }
        
        function showSection(section) {
            document.getElementById('productsSection').style.display = section === 'products' ? 'block' : 'none';
            document.getElementById('customersSection').style.display = section === 'customers' ? 'block' : 'none';
            if (section === 'customers') loadCustomers();
        }
        
        window.onclick = function(event) {
            if (event.target === document.getElementById('productModal')) closeModal();
        }
        
        loadProducts();
        loadStats();
    </script>
</body>
</html>
<?php
// Load configuration and autoloader first
require_once __DIR__ . '/../config/paths.php';

// Require admin access
Session::start();

// Check if user is logged in and is an admin
if (!Session::isLoggedIn() || (Session::getCurrentUser()['role'] ?? '') !== 'admin') {
    // Redirect to login page or an access denied page
    redirect('login.php');
}

// Initialize models
$productModel = new Product();
$categoryModel = new Category();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];

    try {
        switch ($_POST['action']) {
            case 'create':
                if (empty($_POST['name'])) {
                    throw new Exception('Product name is required');
                }
                if (empty($_POST['price'])) {
                    throw new Exception('Product price is required');
                }
                if (empty($_POST['category_id'])) {
                    throw new Exception('Product category is required');
                }

                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'] ?? ''),
                    'price' => (float)$_POST['price'],
                    'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
                    'category_id' => (int)$_POST['category_id'],
                    'stock' => (int)($_POST['stock'] ?? 0),
                    'sku' => trim($_POST['sku'] ?? ''),
                    'status' => $_POST['status'] ?? 'active',
                    'featured' => isset($_POST['featured']) ? 1 : 0
                ];

                // Handle image upload
                if (!empty($_FILES['image']['name'])) {
                    $uploadDir = ASSETS_PATH . '/uploads/products/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                    if (!in_array($fileExtension, $allowedExtensions)) {
                        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowedExtensions));
                    }

                    $fileName = uniqid() . '.' . $fileExtension;
                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $data['image_url'] = ASSETS_URL . '/uploads/products/' . $fileName;
                    } else {
                        throw new Exception('Failed to upload image');
                    }
                }

                $id = $productModel->create($data);
                $response = [
                    'success' => true,
                    'message' => 'Product created successfully',
                    'product' => $productModel->getById($id)
                ];
                break;

            case 'update':
                if (empty($_POST['id']) || empty($_POST['name'])) {
                    throw new Exception('Product ID and name are required');
                }
                if (empty($_POST['price'])) {
                    throw new Exception('Product price is required');
                }
                if (empty($_POST['category_id'])) {
                    throw new Exception('Product category is required');
                }

                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'] ?? ''),
                    'price' => (float)$_POST['price'],
                    'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
                    'category_id' => (int)$_POST['category_id'],
                    'stock' => (int)($_POST['stock'] ?? 0),
                    'sku' => trim($_POST['sku'] ?? ''),
                    'status' => $_POST['status'] ?? 'active',
                    'featured' => isset($_POST['featured']) ? 1 : 0
                ];

                // Handle image upload
                if (!empty($_FILES['image']['name'])) {
                    $uploadDir = ASSETS_PATH . '/uploads/products/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                    if (!in_array($fileExtension, $allowedExtensions)) {
                        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowedExtensions));
                    }

                    $fileName = uniqid() . '.' . $fileExtension;
                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        // Delete old image if exists
                        $oldProduct = $productModel->getById((int)$_POST['id']);
                        if (!empty($oldProduct['image_url'])) {
                            $oldImagePath = str_replace(ASSETS_URL, ASSETS_PATH, $oldProduct['image_url']);
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                        $data['image_url'] = ASSETS_URL . '/uploads/products/' . $fileName;
                    } else {
                        throw new Exception('Failed to upload image');
                    }
                }

                $productModel->update((int)$_POST['id'], $data);
                $response = [
                    'success' => true,
                    'message' => 'Product updated successfully',
                    'product' => $productModel->getById((int)$_POST['id'])
                ];
                break;

            case 'delete':
                if (empty($_POST['id'])) {
                    throw new Exception('Product ID is required');
                }
                
                $id = (int)$_POST['id'];
                $product = $productModel->getById($id);
                
                // Delete product image if exists
                if (!empty($product['image_url'])) {
                    $imagePath = str_replace(ASSETS_URL, ASSETS_PATH, $product['image_url']);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                
                $productModel->delete($id);
                $response = [
                    'success' => true,
                    'message' => 'Product deleted successfully'
                ];
                break;

            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}

// Get all products and categories for display
$products = $productModel->getAllWithCategory();
$categories = $categoryModel->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo SITE_NAME; ?> - Admin Products</title>
    
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/admin.css">

    <!-- Custom CSS for Products Page -->
    <style>
        .admin-main { padding: 20px; }
        .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .table th, .table td { vertical-align: middle; }
        .btn-sm { margin-right: 5px; }
        .modal-header { background-color: #f8f9fa; }
        .product-table { width: 100%; }
        .action-buttons { white-space: nowrap; }
        .product-image { max-width: 50px; max-height: 50px; }
        .product-image-preview { max-width: 200px; max-height: 200px; }
        @media (max-width: 768px) {
            .admin-main { padding: 10px; }
            .action-buttons .btn { margin-bottom: 5px; }
        }
    </style>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/logo.png">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../includes/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1>Products</h1>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">
                    <i class="fa fa-plus"></i> Add Product
                </button>
            </header>

            <div class="card">
                <div class="card-body">
                    <table class="table table-hover product-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($product['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="product-image">
                                        <?php else: ?>
                                            <img src="<?php echo ASSETS_URL; ?>/img/no-image.png" 
                                                 alt="No image" 
                                                 class="product-image">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                    <td>
                                        <?php if (!empty($product['sale_price'])): ?>
                                            <span class="text-muted text-decoration-line-through">
                                                $<?php echo number_format($product['price'], 2); ?>
                                            </span>
                                            <span class="text-danger">
                                                $<?php echo number_format($product['sale_price'], 2); ?>
                                            </span>
                                        <?php else: ?>
                                            $<?php echo number_format($product['price'], 2); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo (int)$product['stock']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $product['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($product['status']); ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $product['id']; ?>">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $product['id']; ?>">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Product Modal -->
            <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="addProductForm" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="productName">Product Name</label>
                                            <input type="text" class="form-control" id="productName" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="productCategory">Category</label>
                                            <select class="form-control" id="productCategory" name="category_id" required>
                                                <option value="">Select Category</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>">
                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="productPrice">Price</label>
                                            <input type="number" class="form-control" id="productPrice" name="price" step="0.01" min="0" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="productSalePrice">Sale Price (Optional)</label>
                                            <input type="number" class="form-control" id="productSalePrice" name="sale_price" step="0.01" min="0">
                                        </div>
                                        <div class="form-group">
                                            <label for="productStock">Stock</label>
                                            <input type="number" class="form-control" id="productStock" name="stock" min="0" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label for="productSku">SKU</label>
                                            <input type="text" class="form-control" id="productSku" name="sku">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="productDescription">Description</label>
                                            <textarea class="form-control" id="productDescription" name="description" rows="4"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="productImage">Product Image</label>
                                            <input type="file" class="form-control" id="productImage" name="image" accept="image/*">
                                            <img id="imagePreview" src="#" alt="Preview" class="product-image-preview mt-2" style="display: none;">
                                        </div>
                                        <div class="form-group">
                                            <label for="productStatus">Status</label>
                                            <select class="form-control" id="productStatus" name="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="productFeatured" name="featured">
                                                <label class="custom-control-label" for="productFeatured">Featured Product</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveProductBtn">Save Product</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editProductForm" enctype="multipart/form-data">
                                <input type="hidden" id="editProductId" name="id">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="editProductName">Product Name</label>
                                            <input type="text" class="form-control" id="editProductName" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="editProductCategory">Category</label>
                                            <select class="form-control" id="editProductCategory" name="category_id" required>
                                                <option value="">Select Category</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>">
                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="editProductPrice">Price</label>
                                            <input type="number" class="form-control" id="editProductPrice" name="price" step="0.01" min="0" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="editProductSalePrice">Sale Price (Optional)</label>
                                            <input type="number" class="form-control" id="editProductSalePrice" name="sale_price" step="0.01" min="0">
                                        </div>
                                        <div class="form-group">
                                            <label for="editProductStock">Stock</label>
                                            <input type="number" class="form-control" id="editProductStock" name="stock" min="0">
                                        </div>
                                        <div class="form-group">
                                            <label for="editProductSku">SKU</label>
                                            <input type="text" class="form-control" id="editProductSku" name="sku">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="editProductDescription">Description</label>
                                            <textarea class="form-control" id="editProductDescription" name="description" rows="4"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="editProductImage">Product Image</label>
                                            <input type="file" class="form-control" id="editProductImage" name="image" accept="image/*">
                                            <img id="editImagePreview" src="#" alt="Preview" class="product-image-preview mt-2">
                                        </div>
                                        <div class="form-group">
                                            <label for="editProductStatus">Status</label>
                                            <select class="form-control" id="editProductStatus" name="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="editProductFeatured" name="featured">
                                                <label class="custom-control-label" for="editProductFeatured">Featured Product</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="updateProductBtn">Update Product</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/vendor/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to show error message
            function showError(message) {
                alert(message); // Replace with a better UI notification system
            }

            // Function to refresh the table
            function refreshTable() {
                location.reload(); // Simple reload for now, could be optimized with AJAX
            }

            // Image preview for add form
            $('#productImage').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Image preview for edit form
            $('#editProductImage').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#editImagePreview').attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Add product
            $('#saveProductBtn').click(function() {
                const form = $('#addProductForm');
                const formData = new FormData(form[0]);
                formData.append('action', 'create');

                $.ajax({
                    url: window.location.href,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#addProductModal').modal('hide');
                            form[0].reset();
                            $('#imagePreview').hide();
                            refreshTable();
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function() {
                        showError('An error occurred while creating the product');
                    }
                });
            });

            // Edit product
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                
                $('#editProductId').val(id);
                $('#editProductName').val(row.find('td:eq(1)').text());
                $('#editProductCategory option').each(function() {
                    if ($(this).text() === row.find('td:eq(2)').text()) {
                        $(this).prop('selected', true);
                        return false;
                    }
                });

                // Handle price display (regular and sale price)
                const priceText = row.find('td:eq(3)').html();
                if (priceText.includes('text-decoration-line-through')) {
                    const prices = priceText.match(/\$(\d+\.\d+)/g);
                    $('#editProductPrice').val(prices[0].replace('$', ''));
                    $('#editProductSalePrice').val(prices[1].replace('$', ''));
                } else {
                    $('#editProductPrice').val(priceText.replace('$', ''));
                    $('#editProductSalePrice').val('');
                }

                $('#editProductStock').val(row.find('td:eq(4)').text());
                $('#editProductStatus').val(row.find('.badge').text().toLowerCase());
                $('#editProductFeatured').prop('checked', row.find('.badge').hasClass('badge-success'));
                
                // Set current image preview
                const currentImage = row.find('.product-image').attr('src');
                $('#editImagePreview').attr('src', currentImage).show();
                
                $('#editProductModal').modal('show');
            });

            // Update product
            $('#updateProductBtn').click(function() {
                const form = $('#editProductForm');
                const formData = new FormData(form[0]);
                formData.append('action', 'update');

                $.ajax({
                    url: window.location.href,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editProductModal').modal('hide');
                            refreshTable();
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function() {
                        showError('An error occurred while updating the product');
                    }
                });
            });

            // Delete product
            $(document).on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this product?')) {
                    const id = $(this).data('id');
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', id);

                    $.ajax({
                        url: window.location.href,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                refreshTable();
                            } else {
                                showError(response.message);
                            }
                        },
                        error: function() {
                            showError('An error occurred while deleting the product');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
```
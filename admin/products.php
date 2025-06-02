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
        .table-responsive {
            margin: 0;
            padding: 0;
        }
        .product-table {
            margin-bottom: 0;
        }
        .product-name {
            font-weight: 500;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
        }
        .product-image {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        .product-image-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: contain;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .action-buttons .btn-group {
            display: flex;
            gap: 2px;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
        }
        .badge {
            padding: 0.5em 0.75em;
            font-size: 0.75em;
        }
        .table td {
            vertical-align: middle;
        }
        .text-danger {
            color: #dc3545 !important;
        }
        .text-decoration-line-through {
            text-decoration: line-through;
        }
        .form-text {
            font-size: 0.875rem;
        }
        .custom-control {
            padding-left: 1.75rem;
        }

        /* Mobile-specific styles */
        @media (max-width: 768px) {
            .admin-main {
                padding: 10px;
            }
            .card-body {
                padding: 10px;
            }
            .table td, .table th {
                padding: 0.5rem;
            }
            .action-buttons .btn-group {
                flex-direction: column;
            }
            .action-buttons .btn {
                margin: 1px 0;
                width: 100%;
            }
            .badge {
                padding: 0.4em 0.6em;
                font-size: 0.7em;
            }
            .product-name {
                font-size: 0.9rem;
            }
            .small {
                font-size: 0.8rem;
            }
        }

        /* Ensure table is scrollable on mobile */
        @media (max-width: 576px) {
            .table-responsive {
                border: 0;
                margin-bottom: 0;
            }
            .table {
                margin-bottom: 0;
            }
            .table td, .table th {
                white-space: nowrap;
            }
            .product-name {
                white-space: normal;
            }
        }

        .product-image-container {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .product-details .row {
            margin-bottom: 0.5rem;
        }

        .product-details .font-weight-bold {
            color: #495057;
        }

        #viewProductModal .modal-body {
            padding: 1.5rem;
        }

        #viewProductModal .badge {
            font-size: 0.875rem;
            padding: 0.5em 0.75em;
        }

        @media (max-width: 768px) {
            .product-image-container {
                margin-bottom: 1rem;
            }
            
            .product-details .row {
                margin-bottom: 0.75rem;
            }
            
            .product-details .font-weight-bold {
                margin-bottom: 0.25rem;
            }
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
                    <div class="table-responsive">
                        <table class="table table-hover product-table">
                            <thead>
                                <tr>
                                    <th class="d-none d-md-table-cell">Image</th>
                                    <th>Name</th>
                                    <th class="d-none d-md-table-cell">Category</th>
                                    <th class="d-none d-lg-table-cell">SKU</th>
                                    <th>Price</th>
                                    <th class="d-none d-md-table-cell">Stock</th>
                                    <th>Status</th>
                                    <th class="d-none d-lg-table-cell">Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td class="d-none d-md-table-cell">
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
                                    <td>
                                        <div class="product-name">
                                            <?php if (!empty($product['image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                     class="product-image d-md-none mr-2" style="width: 30px; height: 30px;">
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </div>
                                        <div class="d-md-none small text-muted">
                                            <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                        </div>
                                        <?php if (!empty($product['description'])): ?>
                                            <small class="text-muted d-none d-md-block"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="d-none d-md-table-cell"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                    <td class="d-none d-lg-table-cell"><?php echo htmlspecialchars($product['sku'] ?? '-'); ?></td>
                                    <td>
                                        <?php if (!empty($product['sale_price'])): ?>
                                            <div class="text-danger font-weight-bold">
                                                $<?php echo number_format($product['sale_price'], 2); ?>
                                            </div>
                                            <small class="text-muted text-decoration-line-through">
                                                $<?php echo number_format($product['price'], 2); ?>
                                            </small>
                                        <?php else: ?>
                                            <div class="font-weight-bold">
                                                $<?php echo number_format($product['price'], 2); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="d-md-none small">
                                            Stock: <span class="badge badge-<?php echo (int)$product['stock'] > 10 ? 'success' : ((int)$product['stock'] > 0 ? 'warning' : 'danger'); ?>">
                                                <?php echo (int)$product['stock']; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="badge badge-<?php echo (int)$product['stock'] > 10 ? 'success' : ((int)$product['stock'] > 0 ? 'warning' : 'danger'); ?>">
                                            <?php echo (int)$product['stock']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $product['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($product['status']); ?>
                                        </span>
                                        <?php if ($product['featured']): ?>
                                            <span class="badge badge-info d-lg-none"><i class="fa fa-star"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <?php if ($product['featured']): ?>
                                            <span class="badge badge-info"><i class="fa fa-star"></i> Featured</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info view-btn" data-id="<?php echo $product['id']; ?>" title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $product['id']; ?>" title="Edit Product">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $product['id']; ?>" title="Delete Product">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
                                            <label for="productName">Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="productName" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="productCategory">Category <span class="text-danger">*</span></label>
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
                                            <label for="productSku">SKU</label>
                                            <input type="text" class="form-control" id="productSku" name="sku" placeholder="Enter product SKU">
                                            <small class="form-text text-muted">Leave blank to auto-generate</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="productPrice">Regular Price <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" class="form-control" id="productPrice" name="price" step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="productSalePrice">Sale Price</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" class="form-control" id="productSalePrice" name="sale_price" step="0.01" min="0">
                                            </div>
                                            <small class="form-text text-muted">Leave empty if no sale price</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="productStock">Stock Quantity</label>
                                            <input type="number" class="form-control" id="productStock" name="stock" min="0" value="0">
                                            <small class="form-text text-muted">Enter 0 for out of stock</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="productDescription">Description</label>
                                            <textarea class="form-control" id="productDescription" name="description" rows="4" placeholder="Enter product description"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="productImage">Product Image</label>
                                            <input type="file" class="form-control" id="productImage" name="image" accept="image/*">
                                            <img id="imagePreview" src="#" alt="Preview" class="product-image-preview mt-2" style="display: none;">
                                            <small class="form-text text-muted">Recommended size: 800x800 pixels</small>
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
                                                <small class="form-text text-muted d-block">Featured products will be highlighted on the homepage</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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

            <!-- Add View Product Modal -->
            <div class="modal fade" id="viewProductModal" tabindex="-1" role="dialog" aria-labelledby="viewProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewProductModalLabel">Product Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="product-image-container text-center mb-3">
                                        <img id="viewProductImage" src="" alt="Product Image" class="img-fluid product-image-preview">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h4 id="viewProductName" class="mb-3"></h4>
                                    <div class="product-details">
                                        <div class="row mb-2">
                                            <div class="col-sm-4 font-weight-bold">Category:</div>
                                            <div class="col-sm-8" id="viewProductCategory"></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 font-weight-bold">SKU:</div>
                                            <div class="col-sm-8" id="viewProductSku"></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 font-weight-bold">Price:</div>
                                            <div class="col-sm-8">
                                                <span id="viewProductPrice"></span>
                                                <span id="viewProductSalePrice" class="text-danger ml-2"></span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 font-weight-bold">Stock:</div>
                                            <div class="col-sm-8">
                                                <span id="viewProductStock" class="badge"></span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 font-weight-bold">Status:</div>
                                            <div class="col-sm-8">
                                                <span id="viewProductStatus" class="badge"></span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 font-weight-bold">Featured:</div>
                                            <div class="col-sm-8">
                                                <span id="viewProductFeatured" class="badge badge-info"></span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 font-weight-bold">Description:</div>
                                            <div class="col-sm-8" id="viewProductDescription"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-warning" id="editFromViewBtn">Edit Product</button>
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

            // View product details
            $(document).on('click', '.view-btn', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                
                // Get product data from the row
                const productData = {
                    id: id,
                    name: row.find('.product-name').text().trim(),
                    category: row.find('td:eq(2)').text().trim(),
                    sku: row.find('td:eq(3)').text().trim(),
                    price: row.find('td:eq(4) .font-weight-bold').text().trim(),
                    salePrice: row.find('td:eq(4) .text-decoration-line-through').text().trim(),
                    stock: row.find('.badge').first().text().trim(),
                    status: row.find('td:eq(6) .badge').text().trim(),
                    featured: row.find('.badge-info').length > 0,
                    description: row.find('.text-muted').text().trim(),
                    image: row.find('.product-image').attr('src')
                };

                // Populate the view modal
                $('#viewProductImage').attr('src', productData.image || '<?php echo ASSETS_URL; ?>/img/no-image.png');
                $('#viewProductName').text(productData.name);
                $('#viewProductCategory').text(productData.category);
                $('#viewProductSku').text(productData.sku || '-');
                
                // Handle price display
                if (productData.salePrice) {
                    $('#viewProductPrice').html('<span class="text-decoration-line-through text-muted">' + productData.salePrice + '</span>');
                    $('#viewProductSalePrice').text(productData.price);
                } else {
                    $('#viewProductPrice').text(productData.price);
                    $('#viewProductSalePrice').text('');
                }

                // Set stock with appropriate badge color
                const stockBadge = $('#viewProductStock');
                const stockNum = parseInt(productData.stock);
                stockBadge.text(productData.stock);
                stockBadge.removeClass('badge-success badge-warning badge-danger')
                        .addClass(stockNum > 10 ? 'badge-success' : (stockNum > 0 ? 'badge-warning' : 'badge-danger'));

                // Set status badge
                const statusBadge = $('#viewProductStatus');
                statusBadge.text(productData.status)
                          .removeClass('badge-success badge-secondary')
                          .addClass(productData.status === 'active' ? 'badge-success' : 'badge-secondary');

                // Set featured status
                const featuredBadge = $('#viewProductFeatured');
                if (productData.featured) {
                    featuredBadge.html('<i class="fa fa-star"></i> Featured').show();
                } else {
                    featuredBadge.hide();
                }

                // Set description
                $('#viewProductDescription').text(productData.description || 'No description available');

                // Store product ID for edit button
                $('#editFromViewBtn').data('id', id);

                // Show the modal
                $('#viewProductModal').modal('show');
            });

            // Edit from view modal
            $('#editFromViewBtn').click(function() {
                const id = $(this).data('id');
                $('#viewProductModal').modal('hide');
                // Trigger the edit button click for this product
                $(`.edit-btn[data-id="${id}"]`).click();
            });
        });
    </script>
</body>
</html>
```
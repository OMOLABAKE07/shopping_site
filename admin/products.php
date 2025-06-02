<?php
// Load configuration and autoloader first
require_once __DIR__ . '/../config/paths.php';

// Require admin access
Session::start();

// Check if user is logged in and is an admin
if (!Session::isLoggedIn() || (Session::getCurrentUser()['role'] ?? '') !== 'admin') {
    // Redirect to login page or an access denied page
    redirect('login.php'); // Or a dedicated access denied page
}

$productModel = new Product();
$categoryModel = new Category();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Validate and create new product
                $data = Form::sanitize($_POST);
                // Remove keys not meant for the INSERT query (e.g. action, csrf_token)
                unset($data['action'], $data['csrf_token']);
                $data['status'] = 'active'; // Default status

                // Define validation rules
                $rules = [
                    'name' => 'required|max:255',
                    'category_id' => 'required|numeric',
                    'description' => '',
                    'price' => 'required|numeric',
                    'sale_price' => 'numeric',
                    'stock' => 'required|numeric',
                    'sku' => 'required|max:50|unique:products,sku',
                    'featured' => '',
                ];

                $errors = Form::validate($data, $rules);

                // Handle file upload
                $image_url = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['image'];
                    $file_name = $file['name'];
                    $file_tmp = $file['tmp_name'];
                    $file_size = $file['size'];
                    $file_error = $file['error'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    $max_file_size = 5 * 1024 * 1024; // 5MB

                    if (in_array($file_ext, $allowed_extensions) && $file_size <= $max_file_size) {
                        $new_file_name = uniqid('', true) . '.' . $file_ext;
                        $upload_path = ASSETS_PATH . '/img/' . $new_file_name;
                        
                        // Ensure the upload directory exists
                        if (!is_dir(ASSETS_PATH . '/img')) {
                            mkdir(ASSETS_PATH . '/img', 0777, true);
                        }

                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            $image_url = $new_file_name; // Store just the filename
                        } else {
                            $errors['image'] = 'Failed to upload image file.';
                        }
                    } else {
                        $errors['image'] = 'Invalid file type or size. Allowed: jpg, jpeg, png, gif (max 5MB).';
                    }
                } else if (!isset($_POST['current_image']) || empty($_POST['current_image'])) {
                    // If no new image is uploaded and no current image exists (for create)
                    // Consider making image required for new products or handle appropriately
                }

                // Add image_url to data if upload was successful or keeping current image
                if ($image_url !== null) {
                    $data['image_url'] = $image_url;
                } else if (isset($_POST['current_image'])) {
                    $data['image_url'] = $_POST['current_image'];
                } else {
                    $data['image_url'] = null; // Or a default image path
                }

                // Check for errors before creating
                if (empty($errors)) {
                    // Handle checkbox value (will be 'on' if checked, need boolean or 1/0)
                    $data['featured'] = isset($data['featured']) ? 1 : 0;

                    if ($productModel->create($data)) {
                        Session::setFlash('success', 'Product created successfully');
                    } else {
                        Session::setFlash('error', 'Failed to create product. Database error.');
                    }
                } else {
                    // Store old data and errors in session to pre-fill the form on redirect
                    Session::set('form_errors', $errors);
                    Session::setOld($data);
                    Session::set('old_action', 'create');
                    Session::setFlash('error', 'Please fix the errors in the form.');
                }

                // Redirect back to the products page
                redirect('products.php');
                break;

            case 'update':
                // Validate and update product
                $productId = $_POST['id'] ?? null;
                if (!$productId) {
                    Session::setFlash('error', 'Invalid product ID for update.');
                    redirect('products.php');
                    break;
                }

                $data = Form::sanitize($_POST);
                // Remove keys not meant for the UPDATE query (e.g. action, csrf_token, id)
                unset($data['action'], $data['csrf_token'], $data['id']);

                // Define validation rules for update
                $rules = [
                    'name' => 'required|max:255',
                    'category_id' => 'required|numeric',
                    'description' => '',
                    'price' => 'required|numeric',
                    'sale_price' => 'numeric',
                    'stock' => 'required|numeric',
                    'sku' => 'required|max:50|unique:products,sku,' . $productId,
                    'featured' => '',
                ];

                $errors = Form::validate($data, $rules);

                // Handle file upload for update (similar to create)
                $image_url = null;
                $current_image = $_POST['current_image'] ?? null;

                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['image'];
                    $file_name = $file['name'];
                    $file_tmp = $file['tmp_name'];
                    $file_size = $file['size'];
                    $file_error = $file['error'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    $max_file_size = 5 * 1024 * 1024; // 5MB

                    if (in_array($file_ext, $allowed_extensions) && $file_size <= $max_file_size) {
                        $new_file_name = uniqid('', true) . '.' . $file_ext;
                        $upload_path = ASSETS_PATH . '/img/' . $new_file_name;
                        
                        // Ensure the upload directory exists
                        if (!is_dir(ASSETS_PATH . '/img')) {
                            mkdir(ASSETS_PATH . '/img', 0777, true);
                        }

                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            $image_url = $new_file_name; // Store just the filename

                            // Delete old image if it exists and is not the new one
                            if ($current_image && $current_image !== $image_url) {
                                $old_image_path = ASSETS_PATH . '/img/' . $current_image;
                                if (file_exists($old_image_path)) {
                                    unlink($old_image_path);
                                }
                            }
                        } else {
                            $errors['image'] = 'Failed to upload new image file.';
                        }
                    } else {
                        $errors['image'] = 'Invalid file type or size for new image. Allowed: jpg, jpeg, png, gif (max 5MB).';
                    }
                } else {
                    // No new image uploaded, keep the current one if exists
                    $image_url = $current_image;
                }

                // Add image_url to data
                $data['image_url'] = $image_url; // Can be null if no image

                // Check for errors before updating
                if (empty($errors)) {
                    // Handle checkbox value
                    $data['featured'] = isset($data['featured']) ? 1 : 0;

                    if ($productModel->update($productId, $data)) {
                        Session::setFlash('success', 'Product updated successfully');
                    } else {
                        Session::setFlash('error', 'Failed to update product. Database error.');
                    }
                } else {
                    // Store old data and errors in session
                    Session::set('form_errors', $errors);
                    Session::setOld($data);
                    Session::set('old_action', 'update');
                    Session::setFlash('error', 'Please fix the errors in the form.');
                }

                // Redirect back to the products page
                redirect('products.php');
                break;

            case 'delete':
                // Delete product
                $productId = $_POST['id'] ?? null;
                if ($productId) {
                    // Get product info to delete the image file
                    $product = $productModel->find($productId);

                    if ($productModel->delete($productId)) {
                        // Delete associated image file if exists
                        if ($product && $product['image_url']) {
                            $image_path = ASSETS_PATH . '/img/' . $product['image_url'];
                            if (file_exists($image_path)) {
                                unlink($image_path);
                            }
                        }
                        Session::setFlash('success', 'Product deleted successfully');
                    } else {
                        Session::setFlash('error', 'Failed to delete product. Database error.');
                    }
                } else {
                    Session::setFlash('error', 'Invalid product ID for deletion.');
                }
                
                // Redirect back to the products page
                redirect('products.php');
                break;
        }
    }
}

// Get all products with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$allProducts = $productModel->all(); // This should ideally fetch with limit/offset
$products = array_slice($allProducts, $offset, $limit);
$totalProducts = count($allProducts); // Total count for pagination
$totalPages = ceil($totalProducts / $limit);

// Get categories for the form
$categories = $categoryModel->all();
if (empty($categories)) {
    Session::setFlash('error', 'No categories available. Please add categories first.');
}

// Handle displaying validation errors and old data after redirect
$errors = Session::get('form_errors');
Session::remove('form_errors');
$old_data = Session::get('old');
Session::remove('old');

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

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/favicon.png">
    <link rel="shortcut icon" type="image/png" href="<?php echo ASSETS_URL; ?>/img/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo ASSETS_URL; ?>/img/favicon.png">
    <meta name="msapplication-TileImage" content="<?php echo ASSETS_URL; ?>/img/favicon.png">
    <meta name="msapplication-TileColor" content="#ffffff">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <img src="<?php echo ASSETS_URL; ?>/img/logo.png" alt="<?php echo SITE_NAME; ?> Logo">
                <h3>Admin Panel</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active"><a href="products.php"><i class="fa fa-shopping-bag"></i> Products</a></li>
                <li><a href="orders.php"><i class="fa fa-shopping-cart"></i> Orders</a></li>
                <li><a href="categories.php"><i class="fa fa-tags"></i> Categories</a></li>
                <li><a href="users.php"><i class="fa fa-users"></i> Users</a></li>
                <li><a href="reviews.php"><i class="fa fa-star"></i> Reviews</a></li>
                <li><a href="<?php echo BASE_URL; ?>/"><i class="fa fa-home"></i> View Site</a></li>
                <li><a href="<?php echo url('logout.php'); ?>"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1>Products</h1>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">
                    <i class="fa fa-plus"></i> Add New Product
                </button>
            </header>

            <?php if ($flash = Session::getFlash()): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
            <?php endif; ?>

            <!-- Products Table -->
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if ($product['image_url']): ?>
                                        <img src="<?php echo ASSETS_URL; ?>/img/<?php echo htmlspecialchars($product['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             class="product-thumbnail"
                                             style="width: 50px; height: auto;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $product['stock'] < 10 ? 'warning' : 'success'; ?>">
                                        <?php echo $product['stock']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $product['status'] === 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($product['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-product" 
                                            data-toggle="modal" data-target="#editProductModal"
                                            data-id="<?php echo $product['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                            data-category_id="<?php echo htmlspecialchars($product['category_id']); ?>"
                                            data-description="<?php echo htmlspecialchars($product['description']); ?>"
                                            data-price="<?php echo htmlspecialchars($product['price']); ?>"
                                            data-sale_price="<?php echo htmlspecialchars($product['sale_price']); ?>"
                                            data-stock="<?php echo htmlspecialchars($product['stock']); ?>"
                                            data-sku="<?php echo htmlspecialchars($product['sku']); ?>"
                                            data-featured="<?php echo htmlspecialchars($product['featured']); ?>"
                                            data-image_url="<?php echo htmlspecialchars($product['image_url']); ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-product" 
                                            data-toggle="modal" data-target="#deleteProductModal"
                                            data-id="<?php echo $product['id']; ?>">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="csrf_token" value="<?php echo Form::generateCSRFToken(); ?>">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Product</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="add_name">Name</label>
                            <input type="text" name="name" class="form-control" id="add_name" required value="<?php echo Form::old('name'); ?>">
                            <?php if (isset($errors['name'])): ?><small class="form-text text-danger"><?php echo $errors['name']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_category_id">Category</label>
                            <select name="category_id" class="form-control" id="add_category_id">
                                <option value="" disabled selected>Select a Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>" 
                                        <?php echo (Form::old('category_id') == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['category_id'])): ?><small class="form-text text-danger"><?php echo $errors['category_id']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_description">Description</label>
                            <textarea name="description" class="form-control" id="add_description" rows="3"><?php echo Form::old('description'); ?></textarea>
                            <?php if (isset($errors['description'])): ?><small class="form-text text-danger"><?php echo $errors['description']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_price">Price</label>
                            <input type="number" name="price" class="form-control" id="add_price" step="0.01" required value="<?php echo Form::old('price'); ?>">
                            <?php if (isset($errors['price'])): ?><small class="form-text text-danger"><?php echo $errors['price']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_sale_price">Sale Price</label>
                            <input type="number" name="sale_price" class="form-control" id="add_sale_price" step="0.01" value="<?php echo Form::old('sale_price'); ?>">
                            <?php if (isset($errors['sale_price'])): ?><small class="form-text text-danger"><?php echo $errors['sale_price']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_stock">Stock</label>
                            <input type="number" name="stock" class="form-control" id="add_stock" required value="<?php echo Form::old('stock'); ?>">
                            <?php if (isset($errors['stock'])): ?><small class="form-text text-danger"><?php echo $errors['stock']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_sku">SKU</label>
                            <input type="text" name="sku" class="form-control" id="add_sku" required value="<?php echo Form::old('sku'); ?>">
                            <?php if (isset($errors['sku'])): ?><small class="form-text text-danger"><?php echo $errors['sku']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_image">Image</label>
                            <input type="file" name="image" class="form-control" id="add_image" accept="image/*">
                            <?php if (isset($errors['image'])): ?><small class="form-text text-danger"><?php echo $errors['image']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="add_featured">
                                <input type="checkbox" name="featured" id="add_featured" value="1" <?php echo Form::old('featured') ? 'checked' : ''; ?>> Featured
                            </label>
                            <?php if (isset($errors['featured'])): ?><small class="form-text text-danger"><?php echo $errors['featured']; ?></small><?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_product_id">
                    <input type="hidden" name="csrf_token" value="<?php echo Form::generateCSRFToken(); ?>">
                    <input type="hidden" name="current_image" id="edit_current_image">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Product</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">Name</label>
                            <input type="text" name="name" class="form-control" id="edit_name" required value="<?php echo Form::old('name'); ?>">
                            <?php if (isset($errors['name'])): ?><small class="form-text text-danger"><?php echo $errors['name']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_category_id">Category</label>
                            <select name="category_id" class="form-control" id="edit_category_id">
                                <option value="" disabled>Select a Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>" 
                                        <?php echo (Form::old('category_id', $category['id']) == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['category_id'])): ?><small class="form-text text-danger"><?php echo $errors['category_id']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_description">Description</label>
                            <textarea name="description" class="form-control" id="edit_description" rows="3"><?php echo Form::old('description'); ?></textarea>
                            <?php if (isset($errors['description'])): ?><small class="form-text text-danger"><?php echo $errors['description']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_price">Price</label>
                            <input type="number" name="price" class="form-control" id="edit_price" step="0.01" required value="<?php echo Form::old('price'); ?>">
                            <?php if (isset($errors['price'])): ?><small class="form-text text-danger"><?php echo $errors['price']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_sale_price">Sale Price</label>
                            <input type="number" name="sale_price" class="form-control" id="edit_sale_price" step="0.01" value="<?php echo Form::old('sale_price'); ?>">
                            <?php if (isset($errors['sale_price'])): ?><small class="form-text text-danger"><?php echo $errors['sale_price']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_stock">Stock</label>
                            <input type="number" name="stock" class="form-control" id="edit_stock" required value="<?php echo Form::old('stock'); ?>">
                            <?php if (isset($errors['stock'])): ?><small class="form-text text-danger"><?php echo $errors['stock']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_sku">SKU</label>
                            <input type="text" name="sku" class="form-control" id="edit_sku" required value="<?php echo Form::old('sku'); ?>">
                            <?php if (isset($errors['sku'])): ?><small class="form-text text-danger"><?php echo $errors['sku']; ?></small><?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_image">Image</label>
                            <input type="file" name="image" class="form-control" id="edit_image" accept="image/*">
                            <?php if (isset($errors['image'])): ?><small class="form-text text-danger"><?php echo $errors['image']; ?></small><?php endif; ?>
                            <div id="edit_current_image_preview" style="margin-top: 10px;"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_featured">
                                <input type="checkbox" name="featured" id="edit_featured" value="1" <?php echo Form::old('featured') ? 'checked' : ''; ?>> Featured
                            </label>
                            <?php if (isset($errors['featured'])): ?><small class="form-text text-danger"><?php echo $errors['featured']; ?></small><?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Product Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteProductId">
                    <input type="hidden" name="csrf_token" value="<?php echo Form::generateCSRFToken(); ?>">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Product</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    
                    <div class="modal-body">
                        <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/vendor/bootstrap.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/admin.js"></script>

    <script>
        $(document).ready(function() {
            // Populate Edit Product Modal when edit button is clicked
            $('.edit-product').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var category_id = $(this).data('category_id');
                var description = $(this).data('description');
                var price = $(this).data('price');
                var sale_price = $(this).data('sale_price');
                var stock = $(this).data('stock');
                var sku = $(this).data('sku');
                var featured = $(this).data('featured');
                var image_url = $(this).data('image_url');

                $('#edit_product_id').val(id);
                $('#edit_name').val(name);
                $('#edit_category_id').val(category_id);
                $('#edit_description').val(description);
                $('#edit_price').val(price);
                $('#edit_sale_price').val(sale_price);
                $('#edit_stock').val(stock);
                $('#edit_sku').val(sku);
                $('#edit_featured').prop('checked', featured == 1);
                $('#edit_current_image').val(image_url);

                // Display current image preview
                var imagePreview = $('#edit_current_image_preview');
                imagePreview.empty();
                if (image_url) {
                    imagePreview.append('<img src="<?php echo ASSETS_URL; ?>/img/' + image_url + '" alt="Current Image" style="max-width: 100px; height: auto;">');
                }

                // Ensure the select element is not disabled
                $('#edit_category_id').prop('disabled', false);
            });

            // Populate Delete Product Modal when delete button is clicked
            $('.delete-product').on('click', function() {
                var productId = $(this).data('id');
                $('#deleteProductId').val(productId);
            });

            // Debug select element changes
            $('#add_category_id, #edit_category_id').on('change', function() {
                console.log('Selected category ID:', $(this).val());
            });

            // Re-show validation errors on modal after redirect
            <?php if (!empty($errors) && Session::get('old_action') === 'update'): ?>
                $('#editProductModal').modal('show');
            <?php elseif (!empty($errors)): ?>
                $('#addProductModal').modal('show');
            <?php endif; ?>
        });
    </script>
</body>
</html>
```
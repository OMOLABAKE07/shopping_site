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

// Initialize Category model
$categoryModel = new Category();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];

    try {
        switch ($_POST['action']) {
            case 'create':
                if (empty($_POST['name'])) {
                    throw new Exception('Category name is required');
                }
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'] ?? ''),
                    'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null
                ];
                $id = $categoryModel->create($data);
                $response = [
                    'success' => true,
                    'message' => 'Category created successfully',
                    'category' => $categoryModel->getByIdWithParent($id)
                ];
                break;

            case 'update':
                if (empty($_POST['id']) || empty($_POST['name'])) {
                    throw new Exception('Category ID and name are required');
                }
                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description'] ?? ''),
                    'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null
                ];
                $categoryModel->update((int)$_POST['id'], $data);
                $response = [
                    'success' => true,
                    'message' => 'Category updated successfully',
                    'category' => $categoryModel->getByIdWithParent((int)$_POST['id'])
                ];
                break;

            case 'delete':
                if (empty($_POST['id'])) {
                    throw new Exception('Category ID is required');
                }
                $id = (int)$_POST['id'];
                
                // Check if category has children
                if ($categoryModel->hasChildren($id)) {
                    throw new Exception('Cannot delete category with subcategories');
                }
                
                // Check if category has products
                if ($categoryModel->hasProducts($id)) {
                    throw new Exception('Cannot delete category with associated products');
                }
                
                $categoryModel->delete($id);
                $response = [
                    'success' => true,
                    'message' => 'Category deleted successfully'
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

// Get all categories for display
$categories = $categoryModel->getAllWithParent();
$parentCategories = $categoryModel->getParentCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo SITE_NAME; ?> - Admin Categories</title>
    
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/admin.css">

    <!-- Custom CSS for Categories Page -->
    <style>
        .admin-main { padding: 20px; }
        .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .table th, .table td { vertical-align: middle; }
        .btn-sm { margin-right: 5px; }
        .modal-header { background-color: #f8f9fa; }
        .category-table { width: 100%; }
        .action-buttons { white-space: nowrap; }
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
                <h1>Categories</h1>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                    <i class="fa fa-plus"></i> Add Category
                </button>
            </header>

            <div class="card">
                <div class="card-body">
                    <table class="table table-hover category-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Parent</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoryTableBody">
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['id']); ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($category['parent_name'] ?? 'None'); ?></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $category['id']; ?>">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $category['id']; ?>">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add Category Modal -->
            <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="addCategoryForm">
                                <div class="form-group">
                                    <label for="categoryName">Category Name</label>
                                    <input type="text" class="form-control" id="categoryName" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="categoryDescription">Description</label>
                                    <textarea class="form-control" id="categoryDescription" name="description" rows="4"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="parentCategory">Parent Category</label>
                                    <select class="form-control" id="parentCategory" name="parent_id">
                                        <option value="">None</option>
                                        <?php foreach ($parentCategories as $parent): ?>
                                            <option value="<?php echo $parent['id']; ?>">
                                                <?php echo htmlspecialchars($parent['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveCategoryBtn">Save Category</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Category Modal -->
            <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editCategoryForm">
                                <input type="hidden" id="editCategoryId" name="id">
                                <div class="form-group">
                                    <label for="editCategoryName">Category Name</label>
                                    <input type="text" class="form-control" id="editCategoryName" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="editCategoryDescription">Description</label>
                                    <textarea class="form-control" id="editCategoryDescription" name="description" rows="4"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="editParentCategory">Parent Category</label>
                                    <select class="form-control" id="editParentCategory" name="parent_id">
                                        <option value="">None</option>
                                        <?php foreach ($parentCategories as $parent): ?>
                                            <option value="<?php echo $parent['id']; ?>">
                                                <?php echo htmlspecialchars($parent['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="updateCategoryBtn">Update Category</button>
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

            // Add category
            $('#saveCategoryBtn').click(function() {
                const form = $('#addCategoryForm');
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
                            $('#addCategoryModal').modal('hide');
                            form[0].reset();
                            refreshTable();
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function() {
                        showError('An error occurred while creating the category');
                    }
                });
            });

            // Edit category
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                
                $('#editCategoryId').val(id);
                $('#editCategoryName').val(row.find('td:eq(1)').text());
                $('#editCategoryDescription').val(row.find('td:eq(2)').text());
                
                // Set parent category if exists
                const parentName = row.find('td:eq(3)').text();
                if (parentName !== 'None') {
                    $('#editParentCategory option').each(function() {
                        if ($(this).text() === parentName) {
                            $(this).prop('selected', true);
                            return false;
                        }
                    });
                } else {
                    $('#editParentCategory').val('');
                }
                
                $('#editCategoryModal').modal('show');
            });

            // Update category
            $('#updateCategoryBtn').click(function() {
                const form = $('#editCategoryForm');
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
                            $('#editCategoryModal').modal('hide');
                            refreshTable();
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function() {
                        showError('An error occurred while updating the category');
                    }
                });
            });

            // Delete category
            $(document).on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this category?')) {
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
                            showError('An error occurred while deleting the category');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
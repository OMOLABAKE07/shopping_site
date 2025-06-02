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

// --- Page Specific Logic Here ---
// Example: Fetch categories from the database
// $categoryModel = new Category();
// $categories = $categoryModel->all();
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoryTableBody">
                            <!-- Categories will be populated via JavaScript or PHP -->
                            <?php
                            // Example placeholder for categories (replace with actual database fetch)
                            // foreach ($categories as $category) {
                            //     echo "<tr>
                            //         <td>{$category['id']}</td>
                            //         <td>{$category['name']}</td>
                            //         <td>{$category['description']}</td>
                            //         <td class='action-buttons'>
                            //             <button class='btn btn-sm btn-warning edit-btn' data-id='{$category['id']}'><i class='fa fa-edit'></i> Edit</button>
                            //             <button class='btn btn-sm btn-danger delete-btn' data-id='{$category['id']}'><i class='fa fa-trash'></i> Delete</button>
                            //         </td>
                            //     </tr>";
                            // }
                            ?>
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
                                    <input type="text" class="form-control" id="categoryName" required>
                                </div>
                                <div class="form-group">
                                    <label for="categoryDescription">Description</label>
                                    <textarea class="form-control" id="categoryDescription" rows="4"></textarea>
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
                                <input type="hidden" id="editCategoryId">
                                <div class="form-group">
                                    <label for="editCategoryName">Category Name</label>
                                    <input type="text" class="form-control" id="editCategoryName" required>
                                </div>
                                <div class="form-group">
                                    <label for="editCategoryDescription">Description</label>
                                    <textarea class="form-control" id="editCategoryDescription" rows="4"></textarea>
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
            // Sample data (replace with AJAX call to fetch categories)
            let categories = [
                { id: 1, name: 'Electronics', description: 'Devices and gadgets' },
                { id: 2, name: 'Clothing', description: 'Apparel and accessories' }
            ];

            // Function to populate table
            function populateTable() {
                const tbody = $('#categoryTableBody');
                tbody.empty();
                categories.forEach(category => {
                    tbody.append(`
                        <tr>
                            <td>${category.id}</td>
                            <td>${category.name}</td>
                            <td>${category.description}</td>
                            <td class="action-buttons">
                                <button class="btn btn-sm btn-warning edit-btn" data-id="${category.id}"><i class="fa fa-edit"></i> Edit</button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${category.id}"><i class="fa fa-trash"></i> Delete</button>
                            </td>
                        </tr>
                    `);
                });
            }

            // Initial table population
            populateTable();

            // Add category
            $('#saveCategoryBtn').click(function() {
                const name = $('#categoryName').val().trim();
                const description = $('#categoryDescription').val().trim();
                if (!name) {
                    alert('Category name is required');
                    return;
                }
                // Simulate adding category (replace with AJAX call)
                const newCategory = {
                    id: categories.length + 1,
                    name: name,
                    description: description
                };
                categories.push(newCategory);
                populateTable();
                $('#addCategoryModal').modal('hide');
                $('#addCategoryForm')[0].reset();
                // TODO: Add AJAX call to save to server
            });

            // Edit category
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                const category = categories.find(c => c.id === id);
                if (category) {
                    $('#editCategoryId').val(category.id);
                    $('#editCategoryName').val(category.name);
                    $('#editCategoryDescription').val(category.description);
                    $('#editCategoryModal').modal('show');
                }
            });

            // Update category
            $('#updateCategoryBtn').click(function() {
                const id = parseInt($('#editCategoryId').val());
                const name = $('#editCategoryName').val().trim();
                const description = $('#editCategoryDescription').val().trim();
                if (!name) {
                    alert('Category name is required');
                    return;
                }
                // Simulate updating category (replace with AJAX call)
                const index = categories.findIndex(c => c.id === id);
                if (index !== -1) {
                    categories[index] = { id, name, description };
                    populateTable();
                    $('#editCategoryModal').modal('hide');
                    // TODO: Add AJAX call to update on server
                }
            });

            // Delete category
            $(document).on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this category?')) {
                    const id = $(this).data('id');
                    // Simulate deleting category (replace with AJAX call)
                    categories = categories.filter(c => c.id !== id);
                    populateTable();
                    // TODO: Add AJAX call to delete from server
                }
            });
        });
    </script>
</body>
</html>
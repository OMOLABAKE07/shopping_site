Admin.js$(document).ready(function() {
    // Handle product edit button click
    $('.edit-product').click(function() {
        var product = $(this).data('product');
        var modal = $('#editProductModal');
        
        // Populate form fields
        modal.find('[name="id"]').val(product.id);
        modal.find('[name="name"]').val(product.name);
        modal.find('[name="category_id"]').val(product.category_id);
        modal.find('[name="description"]').val(product.description);
        modal.find('[name="price"]').val(product.price);
        modal.find('[name="sale_price"]').val(product.sale_price);
        modal.find('[name="stock"]').val(product.stock);
        modal.find('[name="sku"]').val(product.sku);
        modal.find('[name="featured"]').prop('checked', product.featured == 1);
        
        // Show modal
        modal.modal('show');
    });
    
    // Handle product delete button click
    $('.delete-product').click(function() {
        var productId = $(this).data('id');
        var modal = $('#deleteProductModal');
        
        // Set product ID in form
        modal.find('#deleteProductId').val(productId);
        
        // Show modal
        modal.modal('show');
    });
    
    // Handle order status change
    $('select[name="status"]').change(function() {
        $(this).closest('form').submit();
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Handle flash message auto-hide
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
}); 

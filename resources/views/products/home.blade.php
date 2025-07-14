<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .highlight-row {
            background-color: #f0f0f0;
        }
        .summary-row {
            background-color: #dfe6e9;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Inventory Management Dashboard</h2>

        <!-- Success/error messages -->
        <div id="notification-area"></div>

        <!-- Product form -->
        <div class="card mb-3">
            <div class="card-header">Add Product</div>
            <div class="card-body">
                <form id="addProductForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="name" placeholder="Product Name" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control" id="stock" placeholder="Quantity" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control" id="unitPrice" placeholder="Price per Item" step="0.01" min="0" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-3" id="addBtn">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </form>
            </div>
        </div>

        <!-- Current inventory -->
        <div class="card">
            <div class="card-header">Product Inventory</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Submitted At</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">
                        <i class="fas fa-edit"></i> Edit Product
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" id="editProductId">
                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="editProductName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductStock" class="form-label">Quantity in Stock</label>
                            <input type="number" class="form-control" id="editProductStock" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductPrice" class="form-label">Price per Item</label>
                            <input type="number" class="form-control" id="editProductPrice" step="0.01" min="0" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveEditBtn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // CSRF token 
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });

            // Initial load of inventory data
            fetchInventory();

            // Handle form submission for adding a product
            $('#addProductForm').submit(function(e) {
                e.preventDefault();

                // Show loading on add button
                const addBtn = $('#addBtn');
                addBtn.prop('disabled', true);
                addBtn.html('<i class="fas fa-spinner fa-spin"></i> Adding...');

                let data = {
                    product_name: $('#name').val(),
                    quantity: $('#stock').val(),
                    price: $('#unitPrice').val()
                };

                $.post('{{ route("products.store") }}', data, function(response) {
                    showNotification('Product added successfully!', 'success');
                    $('#addProductForm')[0].reset();
                    fetchInventory();
                }).fail(handleAjaxError).always(function() {
                    // Reset button
                    addBtn.prop('disabled', false);
                    addBtn.html('<i class="fas fa-plus"></i> Add Item');
                });
            });

            // Handle edit form submission
            $('#saveEditBtn').click(function() {
                const saveBtn = $(this);
                const productId = $('#editProductId').val();
                
                // Show loading on save button
                saveBtn.prop('disabled', true);
                saveBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                const data = {
                    product_name: $('#editProductName').val(),
                    quantity: $('#editProductStock').val(),
                    price: $('#editProductPrice').val()
                };

                $.ajax({
                    url: `/products/${productId}`,
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            showNotification('Product updated successfully!', 'success');
                            $('#editProductModal').modal('hide');
                            fetchInventory();
                        }
                    },
                    error: handleAjaxError,
                    complete: function() {
                        // Reset button
                        saveBtn.prop('disabled', false);
                        saveBtn.html('<i class="fas fa-save"></i> Save Changes');
                    }
                });
            });

            // Fetch and display inventory
            function fetchInventory() {
                $.get('{{ route("products.getAll") }}', function(response) {
                    const tbody = $('#inventoryBody');
                    tbody.empty();

                    if (!response.products.length) {
                        tbody.append('<tr><td colspan="6" class="text-center">No products yet</td></tr>');
                        return;
                    }

                    // Populate table rows with product data
                    response.products.forEach(item => {
                        tbody.append(`
                            <tr data-product-id="${item.id}">
                                <td>${item.product_name}</td>
                                <td>${item.quantity}</td>
                                <td>$${parseFloat(item.price).toFixed(2)}</td>
                                <td>${new Date(item.submitted_at).toLocaleString()}</td>
                                <td>$${parseFloat(item.total_value).toFixed(2)}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning me-1" onclick="editItem('${item.id}')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteItem('${item.id}')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        `);
                    });

                    // Total value of all products
                    tbody.append(`
                        <tr class="summary-row">
                            <td colspan="4" class="text-end">Total Value:</td>
                            <td colspan="2">$${parseFloat(response.total_value_sum).toFixed(2)}</td>
                        </tr>
                    `);
                });
            }

            // Edit product - open modal with current data
            window.editItem = function(id) {
                // Find the product row
                const row = $(`tr[data-product-id="${id}"]`);
                
                if (row.length === 0) {
                    showNotification('Product not found!', 'danger');
                    return;
                }

                // Extract current values from the table row
                const productName = row.find('td:eq(0)').text();
                const quantity = row.find('td:eq(1)').text();
                const price = row.find('td:eq(2)').text().replace('$', '');

                // Populate the edit form
                $('#editProductId').val(id);
                $('#editProductName').val(productName);
                $('#editProductStock').val(quantity);
                $('#editProductPrice').val(price);

                // Show the modal
                $('#editProductModal').modal('show');
            }

            // Delete product from inventory
            window.deleteItem = function(id) {
                if (!confirm('Are you sure you want to delete this product?')) return;

                // Find and disable the delete button
                const deleteBtn = $(`button[onclick="deleteItem('${id}')"]`);
                deleteBtn.prop('disabled', true);
                deleteBtn.html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

                $.ajax({
                    url: `/products/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            showNotification('Product deleted successfully!', 'success');
                            fetchInventory();
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr);
                        // Reset button on error
                        deleteBtn.prop('disabled', false);
                        deleteBtn.html('<i class="fas fa-trash"></i> Delete');
                    }
                });
            }

            // Show alert notification
            function showNotification(message, type) {
                $('#notification-area').html(`
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);

                // Auto-hide success messages after 3 seconds
                if (type === 'success') {
                    setTimeout(function() {
                        $('.alert').alert('close');
                    }, 3000);
                }
            }

            // Handle errors from AJAX calls
            function handleAjaxError(xhr) {
                let errorMsg = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showNotification(errorMsg, 'danger');
            }
        });
    </script>
</body>
</html>
<?php include_once 'layouts/session.php'; ?>
<?php include_once 'layouts/main.php'; ?>
<?php include_once 'layouts/config.php'; ?>

<?php
// Include your session and DB config
include_once 'layouts/session.php';
include_once 'layouts/config.php';

// header('Content-Type: application/json');

// Check if it's a POST request for processing the order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_items'])) {
    $order_items = json_decode($_POST['order_items'], true);
    $user_id = $_SESSION['id'];
    $sql = "SELECT useremail FROM users WHERE id = $user_id";
    $result = $link->query($sql);
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Order details
    $order_date = date('Y-m-d H:i:s');
    $order_id = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

    // Begin a transaction to ensure data integrity
    $link->begin_transaction();

    // Prepare the SQL statement for inserting the order
    $sql = "INSERT INTO orders (
        order_id, 
        customer_name, 
        medicine_name, 
        quantity, 
        price, 
        total,
        status,
        order_date
    ) VALUES (?, ?, ?, ?, ?, ?, 'Processing', ?)";
    
    $stmt = $link->prepare($sql);

    // Loop through each order item and insert it
    foreach ($order_items as $item) {
        $stmt->bind_param('sssdids', 
            $order_id, 
            $user['useremail'], 
            $item['name'], 
            $item['quantity'], 
            $item['price'], 
            $item['total'], 
            $order_date
        );
        $stmt->execute();
    }

    // Commit the transaction
    $link->commit();

    // Respond back with success and the order ID
    echo json_encode(['success' => true, 'order_id' => $order_id]);
    exit; // End the script
}
?>



<?php
    // Delete medicine
    if(isset($_GET['delete_id'])) {
        $delete_id = (int)$_GET['delete_id'];
        $delete_sql = "DELETE FROM medicine WHERE id = $delete_id";
        
        if($link->query($delete_sql)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Medicine deleted successfully.'
                    });
                });
            </script>";
        }
    }
    
    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get and sanitize form data
        $Medicine_Name = $link->real_escape_string(trim($_POST['Medicine_Name']));
        $Generic_Name = $link->real_escape_string(trim($_POST['Generic_Name']));
        $Category_Name = $link->real_escape_string(trim($_POST['Category_Name']));
        $Dosage_Form = $link->real_escape_string(trim($_POST['Dosage_Form']));
        $Manufacturer = $link->real_escape_string(trim($_POST['Manufacturer']));
        $Batch_Number = $link->real_escape_string(trim($_POST['Batch_Number']));
        $Expiring_Date = $link->real_escape_string(trim($_POST['Expiring_Date']));
        $Price = $link->real_escape_string(trim($_POST['Price']));
        $Stocking_Date = $link->real_escape_string(trim($_POST['Stocking_Date']));
        
        if (isset($_POST['edit_id'])) {
            // Update existing medicine
            $id = $link->real_escape_string($_POST['edit_id']);
            $sql = "UPDATE medicine SET 
                    Medicine_Name = '$Medicine_Name',
                    Generic_Name = '$Generic_Name',
                    Category_Name = '$Category_Name',
                    Dosage_Form = '$Dosage_Form',
                    Manufacturer = '$Manufacturer',
                    Batch_Number = '$Batch_Number',
                    Expiring_Date = '$Expiring_Date',
                    Price = '$Price',
                    Stocking_Date = '$Stocking_Date'
                    WHERE id = '$id'";
            // Insert new medicine
            $sql = "INSERT INTO medicine (Medicine_Name, Generic_Name, Category_Name, Dosage_Form, Manufacturer, Batch_Number, Expiring_Date, Price, Stocking_Date)
                    VALUES ('$Medicine_Name', '$Generic_Name', '$Category_Name', '$Dosage_Form', '$Manufacturer', '$Batch_Number', '$Expiring_Date', $Price, '$Stocking_Date')";
        }

        if ($link->query($sql) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit;
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Database error: " . $link->error . "'
                });
            </script>";
        }
    }

    // Success message after redirect
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Medicine operation completed successfully.'
            });
        </script>";
    }
?>

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', array('title' => 'Medicine Management')); ?>
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <?php include 'layouts/head-css.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div id="layout-wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                <?php includeFileWithVariables('layouts/page-title.php', array('pagetitle' => 'Medicine', 'title' => 'Medicine Details')); ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Medicine Management</h4>
                                </div>
                                
                                <div class="card-body">
                                    <div class="listjs-table" id="medicineList">
                                        <div class="row g-4 mb-3">
                                            <div class="col-sm-auto">
                                                <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal">
                                                    <i class="ri-add-line align-bottom me-1"></i> Make Order
                                                </button>
                                            </div>
                                            <div class="col-sm">
                                                <div class="d-flex justify-content-sm-end">
                                                    <div class="search-box ms-2">
                                                        <input type="text" class="form-control search" placeholder="Search...">
                                                        <i class="ri-search-line search-icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table align-middle table-nowrap" id="medicineTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col" style="width: 50px;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="checkAll">
                                                            </div>
                                                        </th>
                                                        <th class="sort" data-sort="medicine_name">Medicine Name</th>
                                                        <th class="sort" data-sort="generic_name">Generic Name</th>
                                                        <th class="sort" data-sort="category">Category</th>
                                                        <th class="sort" data-sort="dosage">Dosage Form</th>
                                                        <th class="sort" data-sort="manufacturer">Manufacturer</th>
                                                        <th class="sort" data-sort="batch">Batch Number</th>
                                                        <th class="sort" data-sort="expiry">Expiring Date</th>
                                                        <th class="sort" data-sort="expiry">Price</th>
                                                        <th class="sort" data-sort="stocking">Stocking Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sql = "SELECT m.*, mc.Regulatory 
                                                            FROM medicine m 
                                                            LEFT JOIN medicine_category mc ON m.Category_Name = mc.Category_Name";
                                                    $result = $link->query($sql);
                                                    while ($row = $result->fetch_assoc()): 
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox">
                                                            </div>
                                                        </td>
                                                        <td class="medicine_name"><?= htmlspecialchars($row['Medicine_Name']) ?></td>
                                                        <td class="generic_name"><?= htmlspecialchars($row['Generic_Name']) ?></td>
                                                        <td class="category"><?= htmlspecialchars($row['Category_Name']) ?></td>
                                                        <td class="dosage"><?= htmlspecialchars($row['Dosage_Form']) ?></td>
                                                        <td class="manufacturer"><?= htmlspecialchars($row['Manufacturer']) ?></td>
                                                        <td class="batch"><?= htmlspecialchars($row['Batch_Number']) ?></td>
                                                        <td class="expiry"><?= htmlspecialchars($row['Expiring_Date']) ?></td>
                                                        <td class="expiry"><?= htmlspecialchars($row['Price']) ?></td>
                                                        <td class="stocking"><?= htmlspecialchars($row['Stocking_Date']) ?></td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                            <div class="noresult" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                                    <!-- <p class="text-muted mb-0">We've searched more than 150+ Orders We did not find any orders for you search.</p> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="pagination-wrap hstack gap-2">
                                                <a class="page-item pagination-prev disabled" href="javascript:void(0);">
                                                    Previous
                                                </a>
                                                <ul class="pagination listjs-pagination mb-0"></ul>
                                                <a class="page-item pagination-next" href="javascript:void(0);">
                                                    Next
                                                </a>
                                            </div>
                                        </div>
                                        </div>
                                        <!-- Search and Select Medicine Modal -->
<div class="modal fade modal-xl" id="showModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="exampleModalLabel">Select Medicines</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <!-- Search Section -->
                <div class="search-box mb-3">
                    <input type="text" class="form-control" id="medicineSearch" placeholder="Search medicines...">
                </div>

                <!-- Medicine Selection Table -->
                <div class="table-responsive">
                    <table class="table" id="medicineSelectionTable">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Medicine Name</th>
                                <th>Category</th>
                                <th>Manufacturer</th>
                                <th>Available Stock</th>
                                <th>Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM medicine";
                            $result = $link->query($sql);
                            while ($row = $result->fetch_assoc()): 
                            ?>
                            <tr>
                                <td>
                                <input type="checkbox" class="medicine-select" 
       data-id="<?= $row['id'] ?>" 
       data-name="<?= htmlspecialchars($row['Medicine_Name']) ?>"
       data-stock="<?= $row['Stock_Quantity'] ?>">

                                </td>
                                <td><?= htmlspecialchars($row['Medicine_Name']) ?></td>
                                <td><?= htmlspecialchars($row['Category_Name']) ?></td>
                                <td><?= htmlspecialchars($row['Manufacturer']) ?></td>
                                <td><?= htmlspecialchars($row['Stock_Quantity']) ?></td>
                                <td><?= htmlspecialchars($row['Price']) ?></td>
                                <td>
                <input type="number" class="form-control quantity-input" 
                       min="1" value="1" disabled
                       max="<?= $row['Stock_Quantity'] ?>">
            </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Selected Medicines Summary -->
                <div class="selected-medicines mt-3">
                    <h6>Selected Medicines:</h6>
                    <div id="selectedMedicinesList" class="list-group">
                        <!-- Selected medicines will be displayed here -->
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmSelection">Confirm Selection</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="totalModal" tabindex="-1" aria-labelledby="totalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="totalModalLabel">Order Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="selected-items-list">
                    <!-- Dynamically filled with selected items -->
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <strong>Total:</strong>
                    <span id="orderTotal">$0.00</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Proceed to Payment</button>
            </div>
        </div>
    </div>
</div>



                                        <!-- Delete Modal -->
                                        <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mt-2 text-center">
                                                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                                                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                                                <h4>Are you Sure?</h4>
                                                                <p class="text-muted mx-4 mb-0">Are you Sure You want to Remove this Medicine?</p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                                            <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn w-sm btn-danger" id="delete-record">Yes, Delete It!</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    
    <script src="assets/libs/prismjs/prism.js"></script>
    <script src="assets/libs/list.js/list.min.js"></script>
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
// Enhanced JavaScript with stock validation
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('medicineSearch');
    const tableRows = document.querySelectorAll('#medicineSelectionTable tbody tr');
    const selectedList = document.getElementById('selectedMedicinesList');
    const selectedMedicines = new Map();

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

// Handle checkbox selection
document.querySelectorAll('.medicine-select').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const row = this.closest('tr');
        const quantityInput = row.querySelector('.quantity-input');
        const medicineId = this.dataset.id;
        const medicineName = this.dataset.name;
        // Get stock from the "Available Stock" column (5th column)
        const stockCell = row.cells[4]; // Index 4 for the 5th column
        const stockQuantity = stockCell.textContent.trim();

        quantityInput.disabled = !this.checked;

        if (this.checked) {
            selectedMedicines.set(medicineId, {
                name: medicineName,
                quantity: quantityInput.value,
                stock: stockQuantity
            });
        } else {
            selectedMedicines.delete(medicineId);
        }
        updateSelectedList();
    });
});

    // Handle quantity changes with stock validation
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const row = this.closest('tr');
            const checkbox = row.querySelector('.medicine-select');
            const medicineId = checkbox.dataset.id;
            const stockQuantity = parseInt(checkbox.dataset.stock);
            const requestedQuantity = parseInt(this.value);

            if (requestedQuantity > stockQuantity) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Quantity',
                    text: `Only ${stockQuantity} units available in stock!`
                });
                this.value = stockQuantity;
            }

            if (selectedMedicines.has(medicineId)) {
                selectedMedicines.get(medicineId).quantity = this.value;
                updateSelectedList();
            }
        });
    });
// Update selected medicines list with clear labeling
function updateSelectedList() {
    selectedList.innerHTML = '';
    selectedMedicines.forEach((details, id) => {
        const listItem = document.createElement('div');
        listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        listItem.innerHTML = `
            <span>${details.name.split('data-stock=')[0]}</span>
            <div>
                <span class="badge bg-primary rounded-pill">Order Quantity: ${details.quantity}</span>
                <span class="badge bg-info rounded-pill">Stock: ${details.stock}</span>
            </div>
        `;
        selectedList.appendChild(listItem);
    });
}

    // Handle confirm selection with final stock validation
    document.getElementById('confirmSelection').addEventListener('click', function() {
        let isValid = true;
        selectedMedicines.forEach((details, id) => {
            if (parseInt(details.quantity) > details.stock) {
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Stock Error',
                    text: `Available Stock is ${details.stock}`
                });
            }
        });

        if (isValid) {
            const selectedData = Array.from(selectedMedicines.entries()).map(([id, details]) => ({
                id: id,
                name: details.name,
                quantity: details.quantity,
                stock: details.stock
            }));
            
            console.log(selectedData);
            const modal = bootstrap.Modal.getInstance(document.getElementById('showModal'));
            modal.hide();
        }
    });
});

// Handle confirm selection button
document.getElementById('confirmSelection').addEventListener('click', function() {
    // Hide the selection modal
    const selectionModal = bootstrap.Modal.getInstance(document.getElementById('showModal'));
    selectionModal.hide();

    // Show the total modal
    const totalModal = new bootstrap.Modal(document.getElementById('totalModal'));
    totalModal.show();
});
// Handle confirm selection button to make the total price and display it in the modal
document.getElementById('confirmSelection').addEventListener('click', function() {
    let total = 0;
    const summaryItems = [];

    // Get selected items and calculate totals
    document.querySelectorAll('.medicine-select:checked').forEach(checkbox => {
        const row = checkbox.closest('tr');
        const quantityInput = row.querySelector('.quantity-input');
        const price = parseFloat(row.cells[5].textContent); // Price column
        const quantity = parseInt(quantityInput.value);
        const itemTotal = price * quantity;

        total += itemTotal;
        
        summaryItems.push({
            name: row.cells[1].textContent, // Medicine Name column
            quantity: quantity,
            price: price,
            total: itemTotal
        });
    });

    // Update total modal content
    const itemsList = document.querySelector('.selected-items-list');
    itemsList.innerHTML = summaryItems.map(item => `
        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
            <div>
                <span class="fw-bold">${item.name}</span>
                <br>
                <small class="text-muted">${item.quantity} x $${item.price.toFixed(2)}</small>
            </div>
            <span class="text-end fw-bold">$${item.total.toFixed(2)}</span>
        </div>
    `).join('');

    // Display grand total
    document.getElementById('orderTotal').textContent = `$${total.toFixed(2)}`;

});

/////////////***************************ADD Here */
document.querySelector('#totalModal .btn-primary').addEventListener('click', function() {
    const orderItems = [];
    
    // Get all checked medicines and their details
    document.querySelectorAll('.medicine-select:checked').forEach(checkbox => {
        const row = checkbox.closest('tr');
        const quantityInput = row.querySelector('.quantity-input');
        const price = parseFloat(row.cells[5].textContent);
        const quantity = parseInt(quantityInput.value);
        
        orderItems.push({
            name: row.cells[1].textContent.trim(),
            quantity: quantity,
            price: price,
            total: price * quantity
        });
    });

    // Send data to process-order.php
    fetch('process-order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `order_items=${encodeURIComponent(JSON.stringify(orderItems))}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Order Placed Successfully',
                text: `Order ID: ${data.order_id}`
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.message || 'Order failed');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Order Failed',
            text: error.message || 'Something went wrong. Please try again.'
        });
    });
});


        //  THE MODAL FOR PERFORMING SEARCH ENDS HERE
        // Initialize delete functionality
        let deleteId;
        document.querySelectorAll('.remove-item-btn').forEach(button => {
            button.addEventListener('click', function() {
                deleteId = this.getAttribute('data-id');
            });
        });

        document.getElementById('delete-record').addEventListener('click', function() {
            if (deleteId) {
                window.location.href = `Medicine.php?delete_id=${deleteId}`;
            }
        });

        // Initialize edit functionality
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-item-btn');
            const modalTitle = document.querySelector('#exampleModalLabel');
            const submitButton = document.querySelector('#add-btn');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    modalTitle.textContent = 'Edit Medicine';
                    submitButton.textContent = 'Update Medicine';
                    
                    document.getElementById('edit_id').value = this.dataset.id;
                    document.getElementById('Medicine_Name').value = this.dataset.medicine;
                    document.getElementById('Generic_Name').value = this.dataset.generic;
                    document.getElementById('Category_Name').value = this.dataset.category;
                    document.getElementById('Dosage_Form').value = this.dataset.dosage;
                    document.getElementById('Manufacturer').value = this.dataset.manufacturer;
                    document.getElementById('Batch_Number').value = this.dataset.batch;
                    document.getElementById('Expiring_Date').value = this.dataset.expiry;
                    document.getElementById('Price').value = this.dataset.price;
                    document.getElementById('Stocking_Date').value = this.dataset.stocking;
                });
            });

            // Reset form on modal close
            document.querySelector('.btn-close').addEventListener('click', function() {
                document.querySelector('form').reset();
                modalTitle.textContent = 'Add Medicine';
                submitButton.textContent = 'Add Medicine';
            });
        });

        // Search functionality
const searchInput = document.querySelector('.search');
const noResultDiv = document.querySelector('.noresult');
const tableBody = document.querySelector('tbody');

searchInput.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = tableBody.getElementsByTagName('tr');
    let hasResults = false;

    Array.from(tableRows).forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
            hasResults = true;
        } else {
            row.style.display = 'none';
        }
    });

    noResultDiv.style.display = hasResults ? 'none' : 'block';
});


// Sorting functionality
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('categoryTable');
    const headers = table.querySelectorAll('th.sort');
    
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.dataset.sort;
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            const currentOrder = this.classList.contains('asc') ? 'desc' : 'asc';
            
            headers.forEach(h => h.classList.remove('asc', 'desc'));
            this.classList.add(currentOrder);
            
            rows.sort((a, b) => {
                const aValue = a.querySelector(`.${column}`).textContent.trim();
                const bValue = b.querySelector(`.${column}`).textContent.trim();
                return currentOrder === 'asc' 
                    ? aValue.localeCompare(bValue)
                    : bValue.localeCompare(aValue);
            });
            
            const tbody = table.querySelector('tbody');
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        });
    });
});


// Pagination
const rowsPerPage = 10;
let currentPage = 1;

function setupPagination() {
    const table = document.getElementById('categoryTable');
    const rows = table.querySelectorAll('tbody tr');
    const pageCount = Math.ceil(rows.length / rowsPerPage);
    const paginationList = document.querySelector('.listjs-pagination');
    
    paginationList.innerHTML = '';
    for (let i = 1; i <= pageCount; i++) {
        const li = document.createElement('li');
        li.innerHTML = `<a href="#" class="page" data-page="${i}">${i}</a>`;
        paginationList.appendChild(li);
    }

    document.querySelectorAll('.page').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = parseInt(button.dataset.page);
            showPage(currentPage);
            updatePaginationButtons();
        });
    });

    showPage(1);
    updatePaginationButtons();
}

function showPage(page) {
    const table = document.getElementById('categoryTable');
    const rows = table.querySelectorAll('tbody tr');
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.forEach((row, index) => {
        row.style.display = (index >= start && index < end) ? '' : 'none';
    });
}

function updatePaginationButtons() {
    const buttons = document.querySelectorAll('.listjs-pagination .page');
    const prevButton = document.querySelector('.pagination-prev');
    const nextButton = document.querySelector('.pagination-next');

    buttons.forEach(button => {
        button.parentElement.classList.toggle('active', 
            parseInt(button.dataset.page) === currentPage);
    });

    prevButton.classList.toggle('disabled', currentPage === 1);
    nextButton.classList.toggle('disabled', 
        currentPage === Math.ceil(document.querySelectorAll('#categoryTable tbody tr').length / rowsPerPage));
}

document.addEventListener('DOMContentLoaded', setupPagination);
    </script>
</body>
</html>

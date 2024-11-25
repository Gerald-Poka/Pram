<?php include_once 'layouts/session.php'; ?>
<?php include_once 'layouts/main.php'; ?>
<?php include_once 'layouts/config.php'; ?>
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
                                                    <i class="ri-add-line align-bottom me-1"></i> Add Medicine
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
                                                        <th>Actions</th>
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
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-sm btn-success edit-item-btn" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#showModal"
                                                                    data-id="<?= $row['id'] ?>"
                                                                    data-medicine="<?= htmlspecialchars($row['Medicine_Name']) ?>"
                                                                    data-generic="<?= htmlspecialchars($row['Generic_Name']) ?>"
                                                                    data-category="<?= htmlspecialchars($row['Category_Name']) ?>"
                                                                    data-dosage="<?= htmlspecialchars($row['Dosage_Form']) ?>"
                                                                    data-manufacturer="<?= htmlspecialchars($row['Manufacturer']) ?>"
                                                                    data-batch="<?= htmlspecialchars($row['Batch_Number']) ?>"
                                                                    data-expiry="<?= htmlspecialchars($row['Expiring_Date']) ?>"
                                                                    data-price="<?= htmlspecialchars($row['Price']) ?>"
                                                                    data-stocking="<?= htmlspecialchars($row['Stocking_Date']) ?>">
                                                                    Edit
                                                                </button>
                                                                <button class="btn btn-sm btn-danger remove-item-btn" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#deleteRecordModal" 
                                                                    data-id="<?= $row['id'] ?>">
                                                                    Remove
                                                                </button>
                                                            </div>
                                                        </td>
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
                                        <!-- Add/Edit Modal -->
                                        <div class="modal fade" id="showModal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-light p-3">
                                                        <h5 class="modal-title" id="exampleModalLabel">Add Medicine</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form class="needs-validation" novalidate action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="edit_id" id="edit_id">
                                                            
                                                            <div class="mb-3">
                                                                <label for="Medicine_Name" class="form-label">Medicine Name</label>
                                                                <input type="text" class="form-control" name="Medicine_Name" id="Medicine_Name" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="Generic_Name" class="form-label">Generic Name</label>
                                                                <input type="text" class="form-control" name="Generic_Name" id="Generic_Name" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="Category_Name" class="form-label">Category</label>
                                                                <select class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" type="button" name="Category_Name" id="Category_Name"  aria-expanded="true" required>
                                                                    <?php
                                                                    $cat_sql = "SELECT Category_Name FROM medicine_category";
                                                                    $cat_result = $link->query($cat_sql);
                                                                    while($cat_row = $cat_result->fetch_assoc()) {
                                                                        echo "<option value='".$cat_row['Category_Name']."'>".$cat_row['Category_Name']."</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="Dosage_Form" class="form-label">Dosage Form</label>
                                                                <input type="text" class="form-control" name="Dosage_Form" id="Dosage
                                                                <label for="Dosage_Form" class="form-label">Dosage Form</label>
                                                                <input type="text" class="form-control" name="Dosage_Form" id="Dosage_Form" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="Manufacturer" class="form-label">Manufacturer</label>
                                                                <input type="text" class="form-control" name="Manufacturer" id="Manufacturer" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="Batch_Number" class="form-label">Batch Number</label>
                                                                <input type="text" class="form-control" name="Batch_Number" id="Batch_Number" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="Expiring_Date" class="form-label">Expiring Date</label>
                                                                <input type="date" class="form-control" name="Expiring_Date" id="Expiring_Date" required>
                                                            </div>

                                                             <div class="mb-3">
                                                                <label for="Price" class="form-label">Price</label>
                                                                <input type="number" class="form-control" name="Price" id="Price" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="Stocking_Date" class="form-label">Stocking Date</label>
                                                                <input type="date" class="form-control" name="Stocking_Date" id="Stocking_Date" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-success" id="add-btn">Add Medicine</button>
                                                        </div>
                                                    </form>
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

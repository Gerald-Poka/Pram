<?php include_once 'layouts/session.php'; ?>
<?php include_once 'layouts/main.php'; ?>
<?php include_once 'layouts/config.php'; ?>
<?php
    // Delete category
    if(isset($_GET['delete_id'])) {
        $delete_id = (int)$_GET['delete_id'];
        $delete_sql = "DELETE FROM medicine_category WHERE id = $delete_id";
        
        if($link->query($delete_sql)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Category deleted successfully.'
                    });
                });
            </script>";
        }
    }
    
    // Handle form submissions
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
          // Debug output
    var_dump($_POST);

        // Get and sanitize form data
        $Category_Name = $link->real_escape_string(trim($_POST['Category_Name']));
        $Category_Description = $link->real_escape_string(trim($_POST['Category_Description']));
        $Associated_Drug = $link->real_escape_string(trim($_POST['Associated_Drug']));
        $Application = $link->real_escape_string(trim($_POST['Application']));
        $Regulatory = $link->real_escape_string(trim($_POST['Regulatory']));
        $Side_Effects = $link->real_escape_string(trim($_POST['Side_Effects']));
        
        if (!empty($Category_Name) && !empty($Category_Description) && !empty($Associated_Drug) && !empty($Application) && !empty($Regulatory) && !empty($Side_Effects)) {
            // Insert into database
            $sql = "INSERT INTO medicine_category (Category_Name, Category_Description, Associated_Drug, Application, Regulatory, Side_Effects)
            VALUES ('$Category_Name', '$Category_Description', '$Associated_Drug', '$Application', '$Regulatory', '$Side_Effects')";
   if ($link->query($sql) === TRUE) {
    // Redirect to avoid duplicate submissions
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
} else {
echo "<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Please fill in all fields.'
    });
</script>";
}
    }
        // Fetching categories from database
        if (isset($_POST['edit_id'])) {
            // Update existing category
            $id = $link->real_escape_string($_POST['edit_id']);
            $sql = "UPDATE medicine_category SET 
                    Category_Name = '$Category_Name',
                    Category_Description = '$Category_Description',
                    Associated_Drug = '$Associated_Drug',
                    Application = '$Application',
                    Regulatory = '$Regulatory',
                    Side_Effects = '$Side_Effects'
                    WHERE id = '$id'";
                
            if ($link->query($sql)) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Category updated successfully'
                    }).then(() => {
                        window.location = 'Medicine_Category.php';
                    });
                </script>";
            }
        } else {

    }

    // Success message after redirect
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Category added successfully.'
            });
        </script>";
    }
?>

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', array('title' => 'Medicine Categories')); ?>
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
                                    <h4 class="card-title mb-0">Medicine Categories</h4>
                                </div>
                                
                                <div class="card-body">
                                    <div class="listjs-table" id="categoryList">
                                        <div class="row g-4 mb-3">
                                            <div class="col-sm-auto">
                                                <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal">
                                                    <i class="ri-add-line align-bottom me-1"></i> Add Category
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
                                            <table class="table align-middle table-nowrap" id="categoryTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col" style="width: 50px;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="checkAll">
                                                            </div>
                                                        </th>
                                                        <th class="sort" data-sort="category_name">Category Name</th>
                                                        <th class="sort" data-sort="description">Description</th>
                                                        <th class="sort" data-sort="drug">Associated Drug</th>
                                                        <th class="sort" data-sort="application">Application</th>
                                                        <th class="sort" data-sort="regulatory">Regulatory</th>
                                                        <th class="sort" data-sort="side_effects">Side Effects</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sql = "SELECT * FROM medicine_category";
                                                    $result = $link->query($sql);
                                                    while ($row = $result->fetch_assoc()): 
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox">
                                                            </div>
                                                        </td>
                                                        <td class="category_name"><?= htmlspecialchars($row['Category_Name']) ?></td>
                                                        <td class="description"><?= htmlspecialchars($row['Category_Description']) ?></td>
                                                        <td class="drug"><?= htmlspecialchars($row['Associated_Drug']) ?></td>
                                                        <td class="application"><?= htmlspecialchars($row['Application']) ?></td>
                                                        <td class="regulatory"><?= htmlspecialchars($row['Regulatory']) ?></td>
                                                        <td class="side_effects"><?= htmlspecialchars($row['Side_Effects']) ?></td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-sm btn-success edit-item-btn" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#showModal"
                                                                    data-id="<?= $row['id'] ?>"
                                                                    data-category="<?= htmlspecialchars($row['Category_Name']) ?>"
                                                                    data-description="<?= htmlspecialchars($row['Category_Description']) ?>"
                                                                    data-drug="<?= htmlspecialchars($row['Associated_Drug']) ?>"
                                                                    data-application="<?= htmlspecialchars($row['Application']) ?>"
                                                                    data-regulatory="<?= htmlspecialchars($row['Regulatory']) ?>"
                                                                    data-side-effects="<?= htmlspecialchars($row['Side_Effects']) ?>">
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
                                                        <h5 class="modal-title" id="exampleModalLabel">Add Medicine Category</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form class="needs-validation" novalidate action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="edit_id" id="edit_id">
                                                            
                                                            <div class="mb-3">
                                                                <label for="Category_Name" class="form-label">Category Name</label>
                                                                <input type="text" class="form-control" name="Category_Name" id="Category_Name" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="Category_Description" class="form-label">Description</label>
                                                                <textarea class="form-control" name="Category_Description" id="Category_Description" required></textarea>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="Associated_Drug" class="form-label">Associated Drug</label>
                                                                <input type="text" class="form-control" name="Associated_Drug" id="Associated_Drug" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="Application" class="form-label">Application</label>
                                                                <input type="text" class="form-control" name="Application" id="Application" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="Regulatory" class="form-label">Regulatory</label>
                                                                <textarea class="form-control" name="Regulatory" id="Regulatory" required></textarea>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="Side_Effects" class="form-label">Side Effects</label>
                                                                <textarea class="form-control" name="Side_Effects" id="Side_Effects" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-success" id="add-btn">Add Category</button>
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
                                                                <p class="text-muted mx-4 mb-0">Are you Sure You want to Remove this Category?</p>
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
    <script>
let deleteId;

// Handle delete button clicks
document.querySelectorAll('.remove-item-btn').forEach(button => {
    button.addEventListener('click', function() {
        deleteId = this.getAttribute('data-id');
    });
});

// Handle delete confirmation
document.getElementById('delete-record').addEventListener('click', function() {
    if (deleteId) {
        window.location.href = `Medicine_Category.php?delete_id=${deleteId}`;
    }
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

// Edit functionality
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-item-btn');
    const modalTitle = document.querySelector('#exampleModalLabel');
    const submitButton = document.querySelector('#add-btn');
    const form = document.querySelector('form');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            modalTitle.textContent = 'Edit Medicine Category';
            submitButton.textContent = 'Update Category';
            
            let idInput = form.querySelector('input[name="edit_id"]');
            if (!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'edit_id';
                form.appendChild(idInput);
            }
            idInput.value = this.dataset.id;

            // Fill form with category data
            document.getElementById('Category_Name').value = this.dataset.category;
            document.getElementById('Category_Description').value = this.dataset.description;
            document.getElementById('Associated_Drug').value = this.dataset.drug;
            document.getElementById('Application').value = this.dataset.application;
            document.getElementById('Regulatory').value = this.dataset.regulatory;
            document.getElementById('Side_Effects').value = this.dataset.sideEffects;
        });
    });

    // Reset form when closing modal
    const resetForm = () => {
        const form = document.querySelector('form');
        const modalTitle = document.querySelector('#exampleModalLabel');
        const submitButton = document.querySelector('#add-btn');
        
        form.reset();
        modalTitle.textContent = 'Add Medicine Category';
        submitButton.textContent = 'Add Category';
        
        // Remove any existing edit_id input
        const editInput = form.querySelector('input[name="edit_id"]');
        if (editInput) {
            editInput.remove();
        }
    };
            
    // Attach reset handlers to both close buttons
    document.querySelector('#close-modal').addEventListener('click', resetForm);
    document.querySelector('.btn-close').addEventListener('click', resetForm);
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


    <!-- END layout-wrapper -->
    <?php include 'layouts/customizer.php'; ?>

    <?php include 'layouts/vendor-scripts.php'; ?>
    <!-- prismjs plugin -->
    <script src="assets/libs/prismjs/prism.js"></script>
    <script src="assets/libs/list.js/list.min.js"></script>
    <!-- <script src="assets/libs/list.pagination.js/list.pagination.min.js"></script> -->

    <!-- listjs init -->
    <script src="assets/js/pages/listjs.init.js"></script>

    <!-- Sweet Alerts js -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- App js -->
    <script src="assets/js/app.js"></script>


</body>

</html>
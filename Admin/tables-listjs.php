<?php include_once 'layouts/session.php'; ?>
<?php include_once 'layouts/main.php'; ?>
<?php include_once 'layouts/config.php'; ?>
<?php
    //deleting single user
    if(isset($_GET['delete_id'])) {
        $delete_id = (int)$_GET['delete_id'];
        $delete_sql = "DELETE FROM users WHERE id = $delete_id AND role = 1";
        
        if($link->query($delete_sql)) {
           // At the top of the file, before any HTML output
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Customer added successfully.'
            });
        });
    </script>";
}

        }
    }
    
    // Fetching users from database
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fullname = $link->real_escape_string(trim($_POST['fullname']));
        $useremail = $link->real_escape_string(trim($_POST['useremail']));
        $username = $link->real_escape_string(trim($_POST['username']));
        $address = $link->real_escape_string(trim($_POST['Address']));
        $phone = $link->real_escape_string(trim($_POST['phone']));
    
        if (isset($_POST['edit_id'])) {
            // Update existing user
            $id = $link->real_escape_string($_POST['edit_id']);
            $sql = "UPDATE users SET 
                    fullname = '$fullname',
                    useremail = '$useremail',
                    username = '$username',
                    address = '$address',
                    phone = '$phone'
                    WHERE id = '$id' AND role = 1";
                    
            if ($link->query($sql)) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Customer updated successfully'
                    }).then(() => {
                        window.location = 'tables-listjs.php';
                    });
                </script>";
            }
        } else {
            // Your existing insert code
        }
    }
    

if ($_SERVER["REQUEST_METHOD"] == "POST") {

   // Check if the form was submitted
    
    // Get form data and sanitize
    $fullname = $link->real_escape_string(trim($_POST['fullname']));
    $useremail = $link->real_escape_string(trim($_POST['useremail']));
    $username = $link->real_escape_string(trim($_POST['username']));
    $address = $link->real_escape_string(trim($_POST['Address']));
    $phone = $link->real_escape_string(trim($_POST['phone']));

    // Default values
    $pass = "Pharmacy";
    $password = password_hash($pass, PASSWORD_DEFAULT); // Create a secure password hash
    $token = bin2hex(random_bytes(50)); // Generate unique token
    $role = 1; // Default role
    $description = "Just User"; // Default description

        // Validate data
    if (!empty($fullname) && !empty($useremail) && !empty($username) && !empty($address) && !empty($phone)) {
        // Insert into database
        $sql = "INSERT INTO users (fullname, useremail, username, address, description, role, phone, password, token) 
                VALUES ('$fullname', '$useremail', '$username', '$address', '$description', $role, '$phone', '$password', '$token')";

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

// Show success message after redirection
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Customer added successfully.'
        });
    </script>";
}


$link->close();

?>


<head>

    <?php includeFileWithVariables('layouts/title-meta.php', array('title' => 'Listjs')); ?>

    <!-- Sweet Alert css-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <?php include 'layouts/head-css.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php include 'layouts/menu.php'; ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Add, Edit & Remove</h4>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="listjs-table" id="customerList">
                                        <div class="row g-4 mb-3">
                                            <div class="col-sm-auto">
                                                <div>
                                                    <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal"><i class="ri-add-line align-bottom me-1"></i> Add</button>
                                                    <!-- <button class="btn btn-soft-danger" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button> -->
                                                </div>
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

                                        <div class="table-responsive table-card mt-3 mb-1">
                                            <table class="table align-middle table-nowrap" id="customerTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col" style="width: 50px;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                                            </div>
                                                        </th>
                                                        <th class="sort" data-sort="customer_name">Customer</th>
                                                        <th class="sort" data-sort="email">Email</th>
                                                        <th class="sort" data-sort="phone">Phone</th>
                                                        <th class="sort" data-sort="date">Address</th>
                                                        <th class="sort" data-sort="action">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                                                      <?php
                                                $sql = "SELECT * FROM `users` WHERE role = 1 ";
                                                    $result = $link->query($sql);

                                                 while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <th scope="row">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="chk_child" value="option1">
                                                            </div>
                                                        </th>
                                                        <td class="id" style="display:none;"><a href="javascript:void(0);" class="fw-medium link-primary"><?= $row['id'] ?></a></td>
                                                        <td class="customer_name"><?= $row['fullname'] ?></td>
                                                        <td class="email"><?= $row['useremail'] ?></td>
                                                        <td class="phone"><?= $row['phone'] ?></td>
                                                        <td class="date"><?= $row['Address'] ?></td>
                                                        <td>
                                                          <div class="d-flex gap-2">
                                                          <div class="edit">
    <button class="btn btn-sm btn-success edit-item-btn" 
        data-bs-toggle="modal" 
        data-bs-target="#showModal"
        data-id="<?= $row['id'] ?>"
        data-fullname="<?= $row['fullname'] ?>"
        data-email="<?= $row['useremail'] ?>"
        data-username="<?= $row['username'] ?>"
        data-address="<?= $row['Address'] ?>"
        data-phone="<?= $row['phone'] ?>">
        Edit
    </button>
</div>

                                                                <div class="remove">
    <button class="btn btn-sm btn-danger remove-item-btn" data-bs-toggle="modal" data-bs-target="#deleteRecordModal" data-id="<?= $row['id'] ?>">Remove</button>
</div>

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
                                </div><!-- end card -->
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-light p-3">
                                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                                </div>
                                  <form class="needs-validation" novalidate action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <div class="modal-body">

                                        <div class="mb-3 ">
                                            <label for="fullname" class="form-label">Full Name <span class="text-danger"></span></label>
                                            <input type="text" class="form-control" name="fullname" value="" id="fullname" placeholder="Enter Fullname" required>  
                                         
                                            <div class="invalid-feedback">
                                                Please enter Fullname 
                                            </div>
                                        </div>

                                        <div class="mb-3 ">
                                            <label for="useremail" class="form-label">Email <span class="text-danger"></span></label>
                                            <input type="email" class="form-control" name="useremail" value="" id="useremail" placeholder="Enter email address" required>  
                                         
                                            <div class="invalid-feedback">
                                                Please enter email
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username <span class="text-danger"></span></label>
                                            <input type="text" class="form-control" name="username" value="" id="username" placeholder="Enter username" required>
                                          
                                            <div class="invalid-feedback">
                                                Please enter username
                                            </div>
                                        </div>

                                         <div class="mb-3 ">
                                            <label for="Address" class="form-label">Address <span class="text-danger"></span></label>
                                            <input type="text" class="form-control" name="Address" value="" id="Address" placeholder="Enter Address" required>
                                            
                                            <div class="invalid-feedback">
                                                Please enter Address
                                            </div>
                                        </div>

                                        <div class="mb-3 ">
                                            <label for="phone" class="form-label">phone<span class="text-danger"></span></label>
                                            <input type="phone" class="form-control" name="phone" value="" id="phone" placeholder="Enter Phone" required>
                                           
                                            <div class="invalid-feedback">
                                                Please enter Phone
                                            </div>

                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success" id="add-btn">Add Customer</button>
                                            <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                                        </div>
                                    </div>
                                </form> 
                            </div>
                        </div>
                    </div>
                <!--End of the the floating form -->
                <!-- Modal -->
                    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btn-close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mt-2 text-center">
                                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                            <h4>Are you Sure ?</h4>
                                            <p class="text-muted mx-4 mb-0">Are you Sure You want to Remove this Record ?</p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                        <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn w-sm btn-danger " id="delete-record">Yes, Delete It!</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end modal -->

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?php include 'layouts/footer.php'; ?>
        </div>
        <!-- end main content-->

    </div>
    <script>
let deleteUserId;

// Capture ID when delete button is clicked
document.querySelectorAll('.remove-item-btn').forEach(button => {
    button.addEventListener('click', function() {
        deleteUserId = this.getAttribute('data-id');
        console.log('Selected ID:', deleteUserId); // For debugging
    });
});

// Handle deletion confirmation
document.getElementById('delete-record').addEventListener('click', function() {
    if (deleteUserId) {
        window.location.href = `tables-listjs.php?delete_id=${deleteUserId}`;
    }
});
</script>
<script>
// Get the search input and noresult div
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

    // Show/hide the no results message
    noResultDiv.style.display = hasResults ? 'none' : 'block';
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-item-btn');
    const modalTitle = document.querySelector('#exampleModalLabel');
    const submitButton = document.querySelector('#add-btn');
    const form = document.querySelector('form');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Change modal title and button text
            modalTitle.textContent = 'Edit Customer';
            submitButton.textContent = 'Update Customer';
            
            // Add hidden input for ID
            let idInput = form.querySelector('input[name="edit_id"]');
            if (!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'edit_id';
                form.appendChild(idInput);
            }
            idInput.value = this.dataset.id;

            // Fill form fields with existing data
            document.getElementById('fullname').value = this.dataset.fullname;
            document.getElementById('useremail').value = this.dataset.email;
            document.getElementById('username').value = this.dataset.username;
            document.getElementById('Address').value = this.dataset.address;
            document.getElementById('phone').value = this.dataset.phone;
        });
    });

    // Reset form when modal is closed
    const resetForm = () => {
        modalTitle.textContent = 'Add Customer';
        submitButton.textContent = 'Add Customer';
        form.reset();
        const idInput = form.querySelector('input[name="edit_id"]');
        if (idInput) idInput.remove();
    };

    document.querySelector('#close-modal').addEventListener('click', resetForm);
    document.querySelector('.btn-close').addEventListener('click', resetForm);
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('customerTable');
    const headers = table.querySelectorAll('th.sort');
    
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.dataset.sort;
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            const currentOrder = this.classList.contains('asc') ? 'desc' : 'asc';
            
            // Reset all headers
            headers.forEach(h => h.classList.remove('asc', 'desc'));
            this.classList.add(currentOrder);
            
            // Sort rows
            rows.sort((a, b) => {
                const aValue = a.querySelector(`.${column}`).textContent.trim();
                const bValue = b.querySelector(`.${column}`).textContent.trim();
                return currentOrder === 'asc' 
                    ? aValue.localeCompare(bValue)
                    : bValue.localeCompare(aValue);
            });
            
            // Update table
            const tbody = table.querySelector('tbody');
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
        });
    });
});
</script>


<script>
const rowsPerPage = 10; // Adjust number of rows per page as needed
let currentPage = 1;

function setupPagination() {
    const table = document.getElementById('customerTable');
    const rows = table.querySelectorAll('tbody tr');
    const pageCount = Math.ceil(rows.length / rowsPerPage);
    const paginationList = document.querySelector('.listjs-pagination');
    
    // Create pagination buttons
    paginationList.innerHTML = '';
    for (let i = 1; i <= pageCount; i++) {
        const li = document.createElement('li');
        li.innerHTML = `<a href="#" class="page" data-page="${i}">${i}</a>`;
        paginationList.appendChild(li);
    }

    // Handle pagination clicks
    paginationList.querySelectorAll('.page').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = parseInt(button.dataset.page);
            showPage(currentPage);
            updatePaginationButtons();
        });
    });

    // Previous button
    document.querySelector('.pagination-prev').addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
            updatePaginationButtons();
        }
    });

    // Next button
    document.querySelector('.pagination-next').addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage < pageCount) {
            currentPage++;
            showPage(currentPage);
            updatePaginationButtons();
        }
    });

    showPage(1);
    updatePaginationButtons();
}

function showPage(page) {
    const table = document.getElementById('customerTable');
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
        currentPage === Math.ceil(document.querySelectorAll('#customerTable tbody tr').length / rowsPerPage));
}

// Initialize pagination when document loads
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
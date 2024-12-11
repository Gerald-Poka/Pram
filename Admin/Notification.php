<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>
<?php include_once 'layouts/config.php'; ?>
<?php
// Delete notification
if(isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $delete_sql = "DELETE FROM notification WHERE id = $delete_id";
    
    if($link->query($delete_sql)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Notification deleted successfully.'
                });
            });
        </script>";
    }
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $link->real_escape_string(trim($_POST['title']));
    $message = $link->real_escape_string(trim($_POST['message']));
    $state = $link->real_escape_string(trim($_POST['state']));
    
    try {
        if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
            $id = $link->real_escape_string($_POST['edit_id']);
            $sql = "UPDATE notification SET 
                    title = ?, 
                    message = ?, 
                    State = ? 
                    WHERE id = ?";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("sssi", $title, $message, $state, $id);
        } else {
            $sql = "INSERT INTO notification (title, message, State) VALUES (?, ?, ?)";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("sss", $title, $message, $state);
        }
        
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit;
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Database Error',
                text: 'Error: " . $e->getMessage() . "'
            });
        </script>";
    }
}
?>

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', array('title' => 'Notifications')); ?>
    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
    <div id="layout-wrapper">
        <?php include 'layouts/menu.php'; ?>
        
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Notifications</h4>
                        </div>
                        <div class="card-body">
                            <div class="listjs-table" id="notificationList">
                                <div class="row g-4 mb-3">
                                    <div class="col-sm-auto">
                                        <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal">
                                            <i class="ri-add-line align-bottom me-1"></i> Add Notification
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
                                    <table class="table align-middle table-nowrap" id="notificationTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" style="width: 50px;">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                                    </div>
                                                </th>
                                                <th class="sort" data-sort="title">Title</th>
                                                <th class="sort" data-sort="message">Message</th>
                                                <th class="sort" data-sort="state">State</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM notification";
                                            $result = $link->query($sql);
                                            while ($row = $result->fetch_assoc()): 
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox">
                                                    </div>
                                                </td>
                                                <td class="title"><?= htmlspecialchars($row['title']) ?></td>
                                                <td class="message"><?= htmlspecialchars($row['message']) ?></td>
                                                <td class="state"><?= htmlspecialchars($row['State']) ?></td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-sm btn-success edit-item-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#showModal"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-title="<?= htmlspecialchars($row['title']) ?>"
                                                            data-message="<?= htmlspecialchars($row['message']) ?>"
                                                            data-state="<?= htmlspecialchars($row['State']) ?>">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Add Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="needs-validation" novalidate action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" id="title" required>
                            <div class="invalid-feedback">Please enter a title.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" name="message" id="message" required></textarea>
                            <div class="invalid-feedback">Please enter a message.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="state" class="form-label">State</label>
                            <select class="form-control" name="state" id="state" required>
                                <option value="">Select State</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback">Please select a state.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="add-btn">Add Notification</button>
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
                            <p class="text-muted mx-4 mb-0">Are you Sure You want to Remove this Notification?</p>
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

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>
    <script src="assets/js/app.js"></script>
    
    <!-- Add this script at the bottom of the file -->
    <script>

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

    document.addEventListener('DOMContentLoaded', function() {
        // Edit button functionality
        const editButtons = document.querySelectorAll('.edit-item-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Debug line - check if data is being read
                console.log('Edit button clicked:', this.dataset);
                
                // Update modal title
                document.getElementById('exampleModalLabel').textContent = 'Edit Notification';
                
                // Fill form with existing data
                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('title').value = this.dataset.title;
                document.getElementById('message').value = this.dataset.message;
                document.getElementById('state').value = this.dataset.state;
                
                // Change button text
                document.getElementById('add-btn').textContent = 'Update Notification';
            });
        });

        // Delete functionality
        let deleteUserId = null;
        const deleteButtons = document.querySelectorAll('.remove-item-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                deleteUserId = this.dataset.id;
            });
        });

        document.getElementById('delete-record').addEventListener('click', function() {
            if (deleteUserId) {
                window.location.href = `Notification.php?delete_id=${deleteUserId}`;
            }
        });

        // Reset form when adding new notification
        document.getElementById('create-btn').addEventListener('click', function() {
            document.getElementById('exampleModalLabel').textContent = 'Add Notification';
            document.getElementById('add-btn').textContent = 'Add Notification';
            document.querySelector('form').reset();
            document.getElementById('edit_id').value = '';
        });
    });
    </script>
</body>
</html>

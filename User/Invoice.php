<?php include 'layouts/session.php'; ?>
<?php include 'layouts/main.php'; ?>

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', array('title' => 'Invoice Details')); ?>
    <?php include 'layouts/head-css.php'; ?>
</head>

<body>
    <div id="layout-wrapper">
        <?php include 'layouts/menu.php'; ?>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?php includeFileWithVariables('layouts/page-title.php', array('pagetitle' => 'Invoices', 'title' => 'Invoice Details')); ?>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">My Orders</h4>
                                </div>
                                <div class="card-body">
                                    <?php
                                    include 'layouts/config.php';
                                    
                                    // Get user's orders
                                    $user_id = $_SESSION['id'];
                                    $orders_sql = "SELECT 
                                        o.order_id,
                                        o.order_date,
                                        o.status,
                                        o.customer_name,
                                        SUM(o.total) as total_amount 
                                        FROM orders o 
                                        WHERE o.customer_name = (SELECT useremail FROM users WHERE id = ?)
                                        GROUP BY o.order_id 
                                        ORDER BY o.order_date DESC";

                                    $stmt = $link->prepare($orders_sql);
                                    $stmt->bind_param('i', $user_id);
                                    $stmt->execute();
                                    $orders_result = $stmt->get_result();
                                    ?>

                                    <div class="table-responsive">
                                        <table class="table table-centered table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Invoice No</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($order = $orders_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                                                    <td><?= date('d M, Y h:iA', strtotime($order['order_date'])) ?></td>
                                                    <td><span class="badge bg-success-subtle text-success"><?= htmlspecialchars($order['status']) ?></span></td>
                                                    <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary view-order" 
                                                                data-order-id="<?= $order['order_id'] ?>"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#orderDetailModal">
                                                            View Details
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Detail Modal -->
                    <div class="modal fade" id="orderDetailModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Order Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="orderDetails">
                                        <!-- Order details will be loaded here -->
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="window.print()">Print Invoice</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                                                <div class="alert alert-info">
                                                    <p class="mb-0"><span class="fw-semibold">NOTES:</span>
                                                        <span id="note">All Bills are to be paid within 7 days from receipt of invoice. To be paid by cheque or
                                                            credit card or direct payment online. If account is not paid within 7
                                                            days the credits details supplied as confirmation of work undertaken
                                                            will be charged the agreed quoted fee noted above.
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>

                </div>
            </div>
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>

    <?php include 'layouts/customizer.php'; ?>
    <?php include 'layouts/vendor-scripts.php'; ?>

    <script>
    document.querySelectorAll('.view-order').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            fetch(`get-order-details.php?order_id=${orderId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('orderDetails').innerHTML = html;
                });
        });
    });
    </script>

    <script src="assets/js/app.js"></script>
</body>
</html>

<div class="invoice-details">
    <div class="row mb-4">
        <div class="col-md-6">
            <h5>Order Information</h5>
            <p>Order ID: <?= htmlspecialchars($order['order_id']) ?></p>
            <p>Date: <?= date('d M, Y h:iA', strtotime($order['order_date'])) ?></p>
            <p>Status: <span class="badge bg-success-subtle text-success"><?= htmlspecialchars($order['status']) ?></span></p>
        </div>
        <div class="col-md-6">
            <h5>Customer Details</h5>
            <p>Name: <?= htmlspecialchars($order['username']) ?></p>
            <p>Email: <?= htmlspecialchars($order['useremail']) ?></p>
            <p>Phone: <?= htmlspecialchars($order['phone']) ?></p>
            <p>Address: <?= htmlspecialchars($order['address']) ?></p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                $grand_total = 0;
                while ($item = $items->fetch_assoc()):
                    $grand_total += $item['total'];
                ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($item['medicine_name']) ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td>$<?= number_format($item['total'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                    <td><strong>$<?= number_format($grand_total, 2) ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

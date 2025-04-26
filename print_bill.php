<?php
require_once 'config.php';

if (empty($_SESSION['bill_items'])) {
    header("Location: bill.php");
    exit();
}

// Update stock quantities and create bill record
$bill_number = date('Ymd') . rand(1000, 9999);
$total_amount = 0;
foreach ($_SESSION['bill_items'] as $item) {
    $total_amount += $item['total'];
}

$gst_rate = 18; // 18% GST
$gst_amount = ($total_amount * $gst_rate) / 100;
$final_amount = $total_amount + $gst_amount;

// Insert bill record
$sql = "INSERT INTO bills (bill_number, total_amount, gst_amount, final_amount) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sddd", $bill_number, $total_amount, $gst_amount, $final_amount);
$stmt->execute();
$bill_id = $conn->insert_id;

// Insert bill items and update stock
foreach ($_SESSION['bill_items'] as $item) {
    // Insert bill item
    $sql = "INSERT INTO bill_items (bill_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $bill_id, $item['id'], $item['quantity'], $item['price'], $item['total']);
    $stmt->execute();

    // Update stock
    $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $item['quantity'], $item['id']);
    $stmt->execute();
}

$bill_date = date('d-m-Y h:i A');
$bill_items = $_SESSION['bill_items'];

// Clear the bill
$_SESSION['bill_items'] = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill #<?php echo $bill_number; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        .bill-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .bill-header {
            text-align: center;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #eee;
            margin-bottom: 2rem;
        }
        .bill-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .bill-contact {
            color: #666;
            font-size: 0.9rem;
        }
        .bill-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .bill-table {
            margin: 2rem 0;
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .table th {
            background: #2c3e50;
            color: white;
            font-weight: 500;
        }
        .bill-summary {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 2rem;
        }
        .bill-total {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .bill-footer {
            text-align: center;
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 2px solid #eee;
        }
        .qr-code {
            text-align: right;
            margin-top: 1rem;
        }
        @media print {
            body {
                background: white;
            }
            .bill-container {
                box-shadow: none;
                margin: 0;
                padding: 1rem;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="bill-container">
        <div class="bill-header">
            <div class="bill-title">
                <i class="bi bi-lightning-charge-fill"></i>Shivay Electrician 
            </div>
            <div class="bill-contact">
                <p class="mb-1">Professional Electrical Services</p>
                <p class="mb-1">Phone: +91 9904386715</p>
                <p class="mb-0">Email: rutvikbaraiya@gmail.com</p>
            </div>
        </div>

        <div class="bill-meta">
            <div>
                <strong>Bill No:</strong> <?php echo $bill_number; ?><br>
                <strong>Date:</strong> <?php echo $bill_date; ?>
            </div>
            <div class="text-end">
                <strong>GST No:</strong> 29AADCB2230M1ZX<br>
                <strong>PAN:</strong> AADCB2230M
            </div>
        </div>

        <div class="bill-table">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sr.</th>
                        <th>Item Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach ($bill_items as $item): 
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                        <td class="text-end">₹<?php echo number_format($item['price'], 2); ?></td>
                        <td class="text-end">₹<?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="bill-summary">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Payment Method:</strong> Cash/UPI/Card</p>
                    <p class="mb-0"><strong>Terms & Conditions:</strong></p>
                    <ul class="small">
                        <li>Goods once sold cannot be returned</li>
                        <li>All disputes are subject to local jurisdiction</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <td>Sub Total:</td>
                            <td class="text-end">₹<?php echo number_format($total_amount, 2); ?></td>
                        </tr>
                        <tr>
                            <td>GST (<?php echo $gst_rate; ?>%):</td>
                            <td class="text-end">₹<?php echo number_format($gst_amount, 2); ?></td>
                        </tr>
                        <tr class="bill-total">
                            <td>Grand Total:</td>
                            <td class="text-end">₹<?php echo number_format($final_amount, 2); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="bill-footer">
            <p class="mb-1">Thank you for your business!</p>
            <p class="mb-0 small">This is a computer generated bill, no signature required.</p>
        </div>

        <div class="qr-code">
            <!-- Add QR code for digital payments -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=upi://pay?pa=your.upi@bank&pn=ElectricianPro&am=<?php echo $final_amount; ?>" alt="Payment QR Code">
        </div>
    </div>

    <div class="text-center mt-4 mb-4 no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Print Bill
        </button>
        <a href="bill.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Billing
        </a>
    </div>

    <script>
    // Automatically open print dialog
    window.onload = function() {
        window.print();
    }
    </script>
</body>
</html>

<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "kusumba_db";

// Initialize variables
$propertyData = null;

if(isset($_GET['id'])) {
    try {
        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($conn->connect_error) {
            throw new Exception('Connection Failed: ' . $conn->connect_error);
        }
        
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT * FROM property_tax WHERE id = ?");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $propertyData = $result->fetch_assoc();
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        error_log("Database Error in home_taxBill.php: " . $e->getMessage());
    }
}

// Function to convert number to words (English, for demo)
function numberToWords($num) {
    if ($num == 0) return 'Zero';
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $result = '';
    if ($num >= 1000) {
        $result .= $ones[floor($num / 1000)] . ' Thousand ';
        $num %= 1000;
    }
    if ($num >= 100) {
        $result .= $ones[floor($num / 100)] . ' Hundred ';
        $num %= 100;
    }
    if ($num >= 20) {
        $result .= $tens[floor($num / 10)] . ' ';
        $num %= 10;
    } elseif ($num >= 10) {
        $result .= $teens[$num - 10] . ' ';
        return trim($result);
    }
    if ($num > 0) {
        $result .= $ones[$num] . ' ';
    }
    return trim($result);
}

// Calculate totals
$previousTotal = isset($propertyData['previous_home_tax']) ? floatval($propertyData['previous_home_tax']) : 0;
$currentTotal = isset($propertyData['total_home_tax']) ? floatval($propertyData['total_home_tax']) : 0;

// Determine payment date (use today's date as payment date)
$paymentDate = new DateTime(); // You can replace this with actual payment date if available
$year = date('Y');
$marchFirst = new DateTime("$year-03-01");

// Calculate penalty or discount
$penalty = 0;
$discount = 0;
$penaltyOrDiscountLabel = '';
$penaltyOrDiscountValue = 0;

if ($paymentDate > $marchFirst) {
    // After March 1st: 4% penalty
    $penalty = round($currentTotal * 0.04, 2);
    $penaltyOrDiscountLabel = 'Penalty (4%)';
    $penaltyOrDiscountValue = $penalty;
} else {
    // On or before March 1st: 4% discount
    $discount = round($currentTotal * 0.04, 2);
    $penaltyOrDiscountLabel = 'Discount (4%)';
    $penaltyOrDiscountValue = $discount;
}

$dueAmount = $currentTotal - $discount + $penalty;
$totalAmount = $previousTotal + $dueAmount;

// Optionally insert bill data into the database (table: home_tax_bills)
try {
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception('Connection Failed: ' . $conn->connect_error);
    }
    $stmt = $conn->prepare("INSERT INTO home_tax_bills (property_id, previous_total, current_total, discount, penalty, due_amount, total_amount, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    if ($stmt) {
        $propertyId = isset($propertyData['id']) ? $propertyData['id'] : null;
        $stmt->bind_param("iddddddd", $propertyId, $previousTotal, $currentTotal, $discount, $penalty, $dueAmount, $totalAmount);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();
} catch (Exception $e) {
    error_log("Database Insert Error in home_taxBill.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Tax Receipt</title>
    <style>
        body {
            font-family: 'Noto Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            line-height: 1.6;
            color: #333;
        }
        .receipt {
            width: 100%;
            max-width: 800px;
            background-color: #FFD700;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 28px;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .address {
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 25px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .owner-line {
            margin-bottom: 25px;
            font-size: 18px;
        }
        .owner-name {
            font-weight: 500;
            /* border-bottom: 1px solid #666; */ /* Removed underline */
            padding-bottom: 2px;
            margin-left: 5px;
        }
        .details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .details div {
            flex: 1;
            font-size: 16px;
            font-weight: 500;
        }
        .detail-value {
            color: #444;
            margin-left: 5px;
            border-bottom: 1px solid #666;
            padding-bottom: 2px;
        }
        .period {
            text-align: right;
            font-weight: bold;
            color: #333;
        }
        .description {
            margin: 15px 0;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 15px;
        }
        th, td {
            border: 1px solid #444;
            padding: 10px;
            text-align: center;
        }
        .amount {
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }
        th {
            font-weight: bold;
        }
        .amount-in-words {
            margin: 25px 0;
            font-size: 16px;
            font-weight: 500;
        }
        .amount-words {
            color: #444;
            border-bottom: 1px solid #666;
            padding-bottom: 2px;
            margin-left: 5px;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .date {
            display: flex;
            align-items: center;
        }
        .date {
            font-size: 16px;
            font-weight: 500;
        }
        .date-value {
            border-bottom: 1px solid #666;
            padding: 0 10px 2px;
            margin: 0 5px;
        }
        .signature {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items:center;
            min-width: 200px;
        }
        .signature-text {
            font-size: 14px;
            margin-top: 5px;
            font-weight: bold;
        }
        .reference {
            margin-top: 20px;
            display: flex;
        }
        .reference span {
            margin-right: 10px;
        }
        .reference input {
            border: none;
            border-bottom: 1px solid black;
            background-color: transparent;
            margin: 0 10px;
        }
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .action-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .print-btn {
            background-color: #4CAF50;
            color: white;
        }
        .download-btn {
            background-color: #2196F3;
            color: white;
        }
        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        @media print {
            .action-buttons {
                display: none;
            }
            body {
                background: white;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div>Form No. 10</div>
            <div>Receipt No: <?php echo ($propertyData !== null && isset($propertyData['id'])) ? $propertyData['id'] : ''; ?></div>
        </div>
        <div class="title">Home Tax Receipt</div>
        <div class="address">Gram Panchayat Kusumba Tal. Dist. Dhule</div>
        <div class="owner-line">
            Mr./Mrs.<span class="owner-name"><?php echo htmlspecialchars($propertyData['owner_name'] ?? ''); ?></span>
        </div>
        <div class="details">
            <div>Reg.No: <span class="detail-value"><?php echo ($propertyData !== null && isset($propertyData['property_number'])) ? $propertyData['property_number'] : ''; ?></span></div>
            <div>Bill No: <span class="detail-value"><?php echo ($propertyData !== null && isset($propertyData['id'])) ? $propertyData['id'] : ''; ?></span></div>
            <div>House No: <span class="detail-value"><?php echo ($propertyData !== null && isset($propertyData['property_number'])) ? $propertyData['property_number'] : ''; ?></span></div>
            <div class="period">For Year <?php echo date('Y'); ?> to <?php echo date('Y')+1; ?></div>
        </div>
        <div class="description">
            Received the following tax amounts for this year
        </div>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Tax Names</th>
                    <th rowspan="2">Previous Balance</th>
                    <th colspan="4">Collected Amount<br>Current Tax</th>
                    <th rowspan="2">Total Amount</th>
                </tr>
                <tr>
                    <th>Tax</th>
                    <th>Discount</th>
                    <th>Penalty</th>
                    <th>Due Amount<br>(3-4+5)</th>
                </tr>
                <tr>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>7(2+6)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Home Tax</td>
                    <td class="amount"><?php echo number_format($previousTotal, 2); ?></td>
                    <td class="amount"><?php echo number_format($currentTotal, 2); ?></td>
                    <td class="amount"><?php echo number_format($discount, 2); ?></td>
                    <td class="amount"><?php echo number_format($penalty, 2); ?></td>
                    <td class="amount"><?php echo number_format($dueAmount, 2); ?></td>
                    <td class="amount"><?php echo number_format($totalAmount, 2); ?></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td class="amount"><?php echo number_format($previousTotal, 2); ?></td>
                    <td class="amount"><?php echo number_format($currentTotal, 2); ?></td>
                    <td class="amount"><?php echo number_format($discount, 2); ?></td>
                    <td class="amount"><?php echo number_format($penalty, 2); ?></td>
                    <td class="amount"><?php echo number_format($dueAmount, 2); ?></td>
                    <td class="amount"><?php echo number_format($totalAmount, 2); ?></td>
                </tr>
            </tbody>
        </table>
        <div class="amount-in-words">
            Amount in words: <span class="amount-words"><?php echo numberToWords($totalAmount); ?> Rupees Only</span>
        </div>
        <?php if ($penaltyOrDiscountValue > 0): ?>
            <div class="amount-in-words" style="color:<?php echo $penalty ? 'red' : 'green'; ?>;">
                <?php echo $penaltyOrDiscountLabel; ?>: <?php echo number_format($penaltyOrDiscountValue, 2); ?>
            </div>
        <?php endif; ?>
        <div class="footer">
            <div class="date">
                Date: <span class="date-value"><?php echo date('d/m/Y'); ?></span>
            </div>
            <div class="signature">
                <span class="signature-text">Signature of Collecting Clerk</span>
            </div>
        </div>
    </div>
    <div class="action-buttons">
        <button onclick="window.print()" class="action-btn print-btn">Print Bill</button>
        <button onclick="alert('PDF download not implemented in this demo.')" class="action-btn download-btn">Download PDF</button>
    </div>
</body>
</html>
<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kusumba_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the ID from the URL parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the marriage certificate data
$sql = "SELECT * FROM approved_marriage_db WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $certificate = $result->fetch_assoc();
} else {
    die("Certificate not found");
}

$stmt->close();
$conn->close();

// Format date function
function formatDate($date) {
    if (!$date) return '';
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}

// Generate QR code data
$qrData = "Certificate No: " . $certificate['certificate_number'] . "\n" .
          "Husband Name: " . strtoupper($certificate['husband_name']) . "\n" .
          "Wife Name: " . strtoupper($certificate['wife_name']) . "\n" .
          "Date of Marriage: " . formatDate($certificate['marriage_date']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Certificate</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .page-container {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background-color: white;
            position: relative;
            padding: 15mm;
            box-sizing: border-box;
        }
        
        .certificate {
            width: 100%;
            height: 100%;
            border: 1px solid #888;
            background-color: #ccc;
            padding: 10px;
            box-sizing: border-box;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            position: relative;
        }
        
        .emblem {
            width: 80px;
            margin: 0 auto;
            display: block;
        }
        
        .photos {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .photo {
            border: 1px solid #999;
            width: 80px;
            height: 90px;
            background-color: #f8d7e3;
        }
        
        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .form-title {
            text-align: center;
            margin: 10px 0;
        }
        
        .certificate-content {
            background-color: white;
            padding: 15px;
            border-top: 1px solid #888;
        }
        
        .certificate-number {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .details {
            margin-bottom: 15px;
        }
        
        .bilingual {
            margin-bottom: 10px;
        }
        
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .qr-code {
            width: 100px;
            height: 100px;
            border: 1px solid black;
        }
        
        .signature {
            text-align: center;
            width: 300px;
        }
        
        @media print {
            body {
                background-color: white;
            }
            
            .page-container {
                width: 100%;
                height: 100%;
                padding: 0;
                box-shadow: none;
            }
            
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="certificate">
            <div class="header">
                <div class="photos">
                    <div class="photo">
                        <?php if (!empty($certificate['husband_photo'])): ?>
                        <img src="../uploads/marriage_certificates/<?php echo $certificate['husband_photo']; ?>" alt="Husband Photo" style="width:100%; height:100%; object-fit:cover;">
                        <?php endif; ?>
                    </div>
                    <div class="emblem">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAEkklEQVR4nO2Z609bVRzH+Rf4C3wBC7Q8lJaWx6Vl5dULBcrLUWAblCIMaiyMuUw3cHFMjVvidPG26OJMXKL7wxLn5raYLdPoNOlEusuci1kQaLn19vj73XJuOb33nN7SFlni+Sa/tOHcc+73c87v9/uePlOmLMmSJiFziI1eP9aQCXQSbUYvaEX2olfOEPtC7O8sG3+F2C/EXi24Nkyw1/G5RTR+hNgfxIJFrAdxPkCs3zPmClc1/jWxYJGMj+W1Ek8kKOVcJNZSVOMd9uA5p8eTNHLFapQs42uJ3SEWKeQCXy9qFGdFLUXKQkHGO2tqKNjcTKO3b9O36+v0z84O7e3vU/rRI/ry6lXq4+PgfLFcicvVP4hNKhn/JhsmI+Pj9ODhQ73xGXv1KtUhVuycYg1x4/9hL/bYg9f3789Yam+PXiuFnFIYiDEQ/C7PBh9EpUUaGjo2lrZZEK+2tVGgo4NCvb3U19dHPfX1sqbgcDy5TNNT5MvWVj0Dvf398sb7O5JU5vNRyWwKiVCkm0FTl/SXnZ5K63/Jit8RFfq6ro4aQ6HUS0JkR3GqxWLPNJybm9Z/R7rTvHXwN3FpUqayYi7DGO5S6GU+yrGxopHBLXnuvZwXKxmPRqORBBYWRJ+FQu2y8RDi+YYG8oyMPC3HxQsK+RnPmrQwhvFZibsB43FZPtvfTytMCPQghASxWvnvS7t7exRfXaXbS0s0MztLcyyzWJjbkhmPhRYAOc/XJu4Gb6pZbLJUIsFcaDRnGlYp1HQOTdWpVBnC/f10c3GRfkmlaHd3VzdKYYRhQOgWt+bm6GJfn+yCqJYpxgeCMMjpDOKMV3BTGFQe4KrQ6BdJqAhFk5zcCIk4fNbX18eTJO2trVnJOrhY+yiRkIpWERKPBNpCYGJxseiR04NRtSyS/XK6yVQfDtOD7W3p7yvLyxQQu6jPR87BweypKk2jsMxFQQh8wmhg3lBIEhcIqOaXUvHB/EYPXyabLvNDqZo4LR4OkDzjPIcMNcZjJrAJCMNCbI5iMxL6hOTCovOAKbz3e2eHbo6NqXaueMGDG0WEwaKGRGGZX+Nj9hbyGyvD2loKA4/HI3eheawbRofm+dNwXCFn12ERKSbMfIg7LIIQIZlZwOGHRxJBqKoAhg8XQHt9PXmDQao4e1bOLG01NdQSjeoNA0NJIBvxjMVbCFPIr9aKsNBjplg0cY6UtYXQ2P0ibCG0v59nGn9TNGpSuQ2fBIThy51Oo+NvGh5/E3fJ/9Ge5YzHXLnLJnvXNbvYHnMT4Sz0M/tFNzKHmBgJl4teZ15+9nTz5u0ZjGcPMWk2sYOgW7nKDkBnxhkRJxZNxjwLZxyzk6E+V6FpUg96cH42ajS68SLZPo8TObOvVrHJXOyB8WPErlmI+bI9/JlAYrZlYsrW49NRYnfZVZFbQVwDtGMvELzAtqEKRZxfsCNwxegF/3O5Wb3qz0iijtJOMI5+Qc/zU0Q3wTKcQoZxSZZkyX9X/wIrO/c+b1dHuwAAAABJRU5ErkJggg==" alt="Emblem">
                    </div>
                    <div class="photo" style="background-color: #ffcece;">
                        <?php if (!empty($certificate['wife_photo'])): ?>
                        <img src="../uploads/marriage_certificates/<?php echo $certificate['wife_photo']; ?>" alt="Wife Photo" style="width:100%; height:100%; object-fit:cover;">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="title">महाराष्ट्र शासन</div>
                <div class="subtitle">भाग चार- ब) महाराष्ट्र शासन राजपत्र, मे 20, 1999/ वैशाख 30, शके 1921</div>
                <div class="form-title">नमूना -'इ' / FORM "E"</div>
                <div class="form-title">विवाह नोंदणीचे प्रमाणपत्र</div>
                <div class="form-title">CERTIFICATE OF REGISTRATION OF MARRIAGE</div>
                <div class="form-title">(पहा कलम 6 (1) आणि नियम 5)/(See Section 6 (1) and Rule 5)</div>
            </div>
            
            <div class="certificate-content">
                <div class="certificate-number">
                    <div>प्रमाणपत्र क्र. : <?php echo $certificate['certificate_number']; ?></div>
                    <div>Certificate No. : <?php echo $certificate['certificate_number']; ?></div>
                </div>
                
                <div class="bilingual">
                    <div>प्रमाणित करण्यात येते की,</div>
                    <div>Certified that the marriage between,</div>
                </div>
                
                <div class="details">
                    <div class="bilingual">
                        <div>पतीचे नांव:- <?php echo $certificate['husband_name']; ?> राहणार <?php echo $certificate['husband_address']; ?> आणि</div>
                        <div>Husband Name:- <?php echo strtoupper($certificate['husband_name']); ?> Residing at <?php echo strtoupper($certificate['husband_address']); ?> and</div>
                    </div>
                    
                    <div class="bilingual" style="margin-top: 15px;">
                        <div>पत्नीचे नांव:- <?php echo $certificate['wife_name']; ?> राहणार <?php echo $certificate['wife_address']; ?> and</div>
                        <div>Wife Name:- <?php echo strtoupper($certificate['wife_name']); ?> Residing at <?php echo strtoupper($certificate['wife_address']); ?> and</div>
                    </div>
                    
                    <div class="bilingual" style="margin-top: 15px;">
                        <div>यांचा विवाह दिनांक <?php echo formatDate($certificate['marriage_date']); ?> रोजी इत - <?php echo $certificate['marriage_place']; ?> येथे विवाह विधी संपन्न झाला.</div>
                        <div>Solemnized on at dated on <?php echo formatDate($certificate['marriage_date']); ?></div>
                    </div>
                    
                    <div class="bilingual" style="margin-top: 15px;">
                        <div>त्यांची महाराष्ट्र विवाह मंडळांचे विनियमन आणि विवाह नोंद विषयक</div>
                        <div>Registered of Marriages maintained under the Maharashtra Regulation of Marriage Bureaus and</div>
                    </div>
                    
                    <div class="bilingual" style="margin-top: 15px;">
                        <div>१९९८ अन्वये ठेवण्यात आलेल्या नोंदवहीच्या खंड क्रमांक २०२१ च्या अनुक्रमांक <?php echo $certificate['registration_number']; ?> वर</div>
                        <div>Registration of Marriages Act 1998, of Volume 2021 at Serial No <?php echo $certificate['registration_number']; ?></div>
                    </div>
                    
                    <div class="bilingual" style="margin-top: 15px;">
                        <div>दिनांक <?php echo formatDate($certificate['registration_date']); ?> रोजी माझ्याकडून नोंदणी करण्यात आली आहे.</div>
                        <div>On Date <?php echo formatDate($certificate['registration_date']); ?> registered by me.</div>
                    </div>
                    
                    <div class="bilingual" style="margin-top: 15px;">
                        <div>शेरा :<?php echo $certificate['registration_number']; ?></div>
                        <div>Remark :<?php echo $certificate['registration_number']; ?></div>
                    </div>
                </div>
                
                <div class="footer">
                    <div>
                        <div class="bilingual">
                            <div>ठिकाण : कुसुंबा</div>
                            <div>Place : Kusumba</div>
                        </div>
                        <div class="bilingual">
                            <div>दिनांक : <?php echo date('d/m/Y'); ?></div>
                            <div>Date : <?php echo date('d/m/Y'); ?></div>
                        </div>
                    </div>
                    
                    <div class="qr-code">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo urlencode($qrData); ?>" alt="QR Code">
                    </div>
                    
                    <div class="signature">
                        <div>Registar of Marriage</div>
                        <div>Gramsevak / Village Development officer</div>
                        <div>Grampanchayat:Kusumba Tal.Dhule Dist.Dhule</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Print Certificate</button>
    </div>
</body>
</html>


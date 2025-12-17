<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'Dbconnection.php';

// ✅ TOGGLE STATUS
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);

    // Fetch current status
    $sql = "SELECT status FROM `discounts` WHERE id = $id";
    $result = mysqli_query($con, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $current_status = $row['status'];

        // Flip status
        $new_status = ($current_status == 'active') ? 'inactive' : 'active';

        // Update query
        $update_query = "UPDATE `discounts` SET status = '$new_status' WHERE id = $id";
        if (mysqli_query($con, $update_query)) {
            $msg = ($new_status == 'active') ? '✅ Coupon activated!' : '🚫 Coupon deactivated!';
            echo "<script>
                    alert('$msg');
                    window.location.href='admin.php';
                  </script>";
            exit;
        } else {
            echo "<script>alert('❌ Error updating status: " . mysqli_error($con) . "');</script>";
        }
    }
}

// ✅ DELETE COUPON
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_query = "DELETE FROM `discounts` WHERE id = $id";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>
                alert('🗑️ Coupon deleted successfully!');
                window.location.href='discount.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('❌ Error deleting coupon: " . mysqli_error($con) . "');</script>";
    }
}

// ✅ Insert Coupon
if (isset($_POST['Dsubmit'])) {
    $code   = mysqli_real_escape_string($con, $_POST['couponCode']);
    $type   = mysqli_real_escape_string($con, $_POST['discountType']);
    $value  = (int) $_POST['discountValue'];
    $expiry = mysqli_real_escape_string($con, $_POST['expiryDate']);
    $limit  = isset($_POST['usageLimit']) ? (int) $_POST['usageLimit'] : 0;

    $sql = "INSERT INTO `discounts` (`code`, `type`, `value`, `expiry`, `usage_limit`, `used_count`, `status`)
            VALUES ('$code', '$type', '$value', '$expiry', '$limit', 0, 'active')";

    if (mysqli_query($con, $sql)) {
        echo "<script>
                alert('✅ Coupon created successfully!');
                window.location.href = window.location.href;
              </script>";
    } else {
        echo "<script>alert('❌ Error: " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Discounts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
      body {
          background-color: #f8f9fa;
          font-family: 'Poppins', sans-serif;
      }
      .card {
          border: none;
          box-shadow: 0 0 12px rgba(0,0,0,0.1);
          border-radius: 12px;
      }
      .form-label {
          font-weight: 500;
      }
      .btn-primary {
          background-color: #667eea;
          border: none;
      }
      .btn-primary:hover {
          background-color: #556cd6;
      }
      .badge.bg-success-soft {
          background-color: rgba(40,167,69,0.1);
          border: 1px solid #28a745;
      }
  </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6 mb-0">Manage Discounts</h1>
        <button type="button" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Coupon
        </button>
    </div>

    <div class="row g-4">
        <!-- 🧩 Existing Coupons -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Existing Discount Codes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Used</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
             <?php
$sqld = "SELECT * FROM `discounts` ORDER BY id DESC";
$rea  = mysqli_query($con, $sqld);

if ($rea && mysqli_num_rows($rea) > 0) {
    while ($row = mysqli_fetch_assoc($rea)) {

        // Dynamic button text
        $action_text = ($row['status'] == 'active') ? 'Deactivate' : 'Activate';
        $badge_class = ($row['status'] == 'active') ? 'text-success bg-success-soft' : 'text-danger bg-danger-subtle';

        echo "<tr>
            <td class='fw-bold'>" . htmlspecialchars($row['code']) . "</td>
            <td>" . htmlspecialchars($row['type']) . "</td>
            <td>" . htmlspecialchars($row['value']) . "</td>
            <td><span class='badge $badge_class'>" . htmlspecialchars($row['status']) . "</span></td>
            <td>" . htmlspecialchars($row['used_count']) . "/" . htmlspecialchars($row['usage_limit']) . "</td>
            <td>
                <div class='dropdown'>
                    <button class='btn btn-sm btn-outline-secondary' type='button' data-bs-toggle='dropdown'>
                        <i class='fas fa-ellipsis-h'>Option </i>
                    </button>
                    <ul class='dropdown-menu'>
                        <li>
                            <a class='dropdown-item' href='?toggle=" . $row['id'] . "'>$action_text</a>
                        </li>
                        <li><hr class='dropdown-divider'></li>
                        <li>
                            <a class='dropdown-item text-danger' href='?delete=" . $row['id'] . "'
                               onclick=\"return confirm('Are you sure you want to delete this coupon?');\">
                               Delete
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center text-muted'>No coupons found</td></tr>";
}
?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🧾 Create Coupon -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Create Coupon</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="couponCode" class="form-label">Discount Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="couponCode" name="couponCode" placeholder="e.g., SAVE15" required>
                                <button class="btn btn-outline-secondary" type="button" id="generateBtn">Generate</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Discount Type</label>
                            <select class="form-select" name="discountType">
                                <option value="percentage" selected>Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₹)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="discountValue" class="form-label">Value</label>
                            <input type="number" class="form-control" id="discountValue" name="discountValue" placeholder="15" required>
                        </div>

                        <div class="mb-3">
                            <label for="expiryDate" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiryDate" name="expiryDate" required>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="usageLimitCheck">
                            <label class="form-check-label" for="usageLimitCheck">
                                Limit total number of uses
                            </label>
                        </div>

                        <div class="mb-3 d-none" id="usageLimitField">
                            <label for="usageLimit" class="form-label">Usage Limit</label>
                            <input type="number" class="form-control" id="usageLimit" name="usageLimit" placeholder="100">
                        </div>

                        <button type="submit" name="Dsubmit" class="btn btn-primary w-100">Save Coupon</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById("generateBtn").addEventListener("click", function() {
        let random = Math.random().toString(36).substring(2, 8).toUpperCase();
        document.getElementById("couponCode").value = "SAVE" + random;
    });

    document.getElementById("usageLimitCheck").addEventListener("change", function() {
        document.getElementById("usageLimitField").classList.toggle("d-none", !this.checked);
    });
</script>

</body>
</html>

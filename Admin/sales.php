<?php
include 'Dbconnection.php';

if (isset($_POST['ssb'])) {
  // Sanitize all inputs
  $type = mysqli_real_escape_string($con, $_POST['type']);
  $target_id = mysqli_real_escape_string($con, $_POST['target_id']);
  $discount_percent = is_numeric($_POST['discount_percent']) ? $_POST['discount_percent'] : 0;
  $start_date = mysqli_real_escape_string($con, $_POST['start_date']);
  $start_time = mysqli_real_escape_string($con, $_POST['start_time']);
  $end_date = mysqli_real_escape_string($con, $_POST['end_date']);
  $end_time = mysqli_real_escape_string($con, $_POST['end_time']);
  $festival_season = mysqli_real_escape_string($con, $_POST['festival_season']);

  // Combine date + time into full datetime (assuming seconds=00)
  $start_full = date('Y-m-d H:i:s', strtotime($start_date . ' ' . $start_time));
  $end_full = date('Y-m-d H:i:s', strtotime($end_date . ' ' . $end_time));

  // Validation: Ensure end datetime is after start datetime
  if (strtotime($end_full) <= strtotime($start_full)) {
    echo "<div class='alert alert-danger mt-3'>❌ End date-time must be after start date-time.</div>";
  } else {
    // Insert Query (DB में `start_datetime` और `end_datetime` DATETIME columns होने चाहिए)
    $sql_sale = "INSERT INTO `sales` 
      (`type`, `target_id`, `discount_percent`, `start_datetime`, `end_datetime`, `festival_season`)
      VALUES 
      ('$type', '$target_id', '$discount_percent', '$start_full', '$end_full', '$festival_season')";

    $res_sale = mysqli_query($con, $sql_sale);

    if ($res_sale) {
      echo "<div class='alert alert-success mt-3'>✅ Sale Added Successfully with Time Slots!</div>";
    } else {
      echo "<div class='alert alert-danger mt-3'>❌ Error: " . mysqli_error($con) . "</div>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sale Section (with Time Slots)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .card { margin-bottom: 20px; }
    .time-input { font-size: 1.1em; }
  </style>
</head>

<body>
  <div class="container py-5">
    <!-- Form to Add New Sale -->
    <form method="post" action="" class="row g-4" id="saleForm">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Add a New Sale (Date + Time Slots)</h1>
        <div>
          <button type="reset" class="btn btn-outline-secondary">Discard</button>
          <button type="submit" name="ssb" class="btn btn-primary">Publish Sale</button>
        </div>
      </div>

      <!-- Left column: Sale Details -->
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-header bg-white">
            <h5 class="mb-0">Sale Information</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Sale Type</label>
              <select class="form-select" name="type" required>
                <option value="product">Product</option>
                <option value="category">Category</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Target (Product or Category)</label>
              <select class="form-select" name="target_id" required>
                <option value="">Select...</option>
                <!-- Fetch Products -->
                <optgroup label="Products">
                  <?php
                  $sql_products = "SELECT pid, pname FROM `product`";
                  $res_products = mysqli_query($con, $sql_products);
                  if ($res_products) {
                    while ($row = mysqli_fetch_assoc($res_products)) {
                      echo "<option value='{$row['pid']}'>{$row['pname']}</option>";
                    }
                  }
                  ?>
                </optgroup>
                <!-- Fetch Categories -->
                <optgroup label="Categories">
                  <?php
                  $sql_categories = "SELECT DISTINCT audience FROM `catagory`";
                  $res_categories = mysqli_query($con, $sql_categories);
                  if ($res_categories) {
                    while ($row = mysqli_fetch_assoc($res_categories)) {
                      echo "<option value='{$row['audience']}'>{$row['audience']}</option>";
                    }
                  }
                  ?>
                </optgroup>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Discount Percent (%)</label>
              <input type="number" class="form-control" name="discount_percent" placeholder="e.g., 20" min="0" max="100" required>
            </div>
            
            <!-- Date-Time Inputs -->
            <div class="row">
              <div class="col-md-3 mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Start Time</label>
                <input type="time" class="form-control time-input" name="start_time" required value="09:00">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">End Time</label>
                <input type="time" class="form-control time-input" name="end_time" required value="18:00">
              </div>
            </div>
            <div class="alert alert-info">
              <strong>Tip:</strong> Sale will be active only in the selected time slots (e.g., 9 AM to 6 PM daily from start to end date).
            </div>
            
            <div class="mb-3">
              <label class="form-label">Festival/Season Name</label>
              <input type="text" class="form-control" name="festival_season" placeholder="e.g., Diwali Happy Hour" required>
            </div>
          </div>
        </div>
      </div>

      <!-- Right column: Preview -->
      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-header bg-white">
            <h5 class="mb-0">Sale Preview</h5>
          </div>
          <div class="card-body">
            <p><strong>Active Period:</strong> <span id="previewPeriod">Select dates & times</span></p>
            <p><strong>Discount:</strong> <span id="previewDiscount">0%</span></p>
            <p>Sale applies to specific hours (e.g., hourly discounts or time-bound offers).</p>
          </div>
        </div>
      </div>
    </form>

    <!-- Table to List All Sales -->
    <div class="row mt-5">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-white">
            <h5 class="mb-0">Existing Sales (with Time Slots)</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Target</th>
                    <th>Discount (%)</th>
                    <th>Start DateTime</th>
                    <th>End DateTime</th>
                    <th>Festival/Season</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sql = "SELECT * FROM `sales` ORDER BY start_datetime DESC"; // अब datetime से sort
                  $res = mysqli_query($con, $sql);
                  if ($res) {
                    while ($row = mysqli_fetch_assoc($res)) {
                      $start_formatted = date('d/m/Y H:i', strtotime($row['start_datetime']));
                      $end_formatted = date('d/m/Y H:i', strtotime($row['end_datetime']));
                      echo "<tr>
                        <td>{$row['sale_id']}</td>
                        <td>" . ucfirst($row['type']) . "</td>
                        <td>" . htmlspecialchars($row['target_id']) . "</td>
                        <td>{$row['discount_percent']}%</td>
                        <td>{$start_formatted}</td>
                        <td>{$end_formatted}</td>
                        <td>" . htmlspecialchars($row['festival_season']) . "</td>
                        <td>
                          <i class='fa-solid fa-pen-to-square text-primary editBtn' style='cursor:pointer;' data-id='{$row['sale_id']}'></i>
                          <i class='fa-solid fa-trash text-danger deleteBtn' data-id='{$row['sale_id']}'></i>
                        </td>
                      </tr>";
                    }
                  } else {
                    echo "<tr><td colspan='8' class='text-center text-danger'>No sales found or query failed: " . mysqli_error($con) . "</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Live Preview
    $('#saleForm input, #saleForm select').on('change', function() {
      const discount = $('[name="discount_percent"]').val() || 0;
      const startDate = $('[name="start_date"]').val();
      const startTime = $('[name="start_time"]').val();
      const endDate = $('[name="end_date"]').val();
      const endTime = $('[name="end_time"]').val();
      
      if (startDate && startTime && endDate && endTime) {
        $('#previewPeriod').text(`${startDate} ${startTime} to ${endDate} ${endTime}`);
      }
      $('#previewDiscount').text(discount + '%');
    });

    // Client-side Validation for Time
    $('[name="end_date"], [name="end_time"]').on('change', function() {
      const startFull = new Date($('[name="start_date"]').val() + 'T' + $('[name="start_time"]').val());
      const endFull = new Date($('[name="end_date"]').val() + 'T' + $('[name="end_time"]').val());
      if (endFull <= startFull) {
        alert('❌ End time must be after start time!');
        $(this).val('');
      }
    });

    // Edit/Delete Placeholders
    $('.editBtn').on('click', function() {
      const id = $(this).data('id');
      // AJAX Edit Modal (implement similar to products)
      alert('Edit Sale ID: ' + id + ' (Add modal for update with times)');
    });
    $('.deleteBtn').on('click', function() {
      if (confirm('Delete this sale?')) {
        const id = $(this).data('id');
        // AJAX Delete
        $.post('delete_sale.php', {id: id}, function() { location.reload(); });
      }
    });
  </script>
</body>

</html>
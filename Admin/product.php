<?php
  include 'Dbconnection.php';
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['psb'])) {

  $pName   = mysqli_real_escape_string($con, $_POST['pName']);
$pDesc   = mysqli_real_escape_string($con, $_POST['pDesc']);
$price   = is_numeric($_POST['price']) ? $_POST['price'] : 0;
$pSKU    = mysqli_real_escape_string($con, $_POST['pSKU']);
$pStock  = is_numeric($_POST['pStock']) ? $_POST['pStock'] : 0;
$pstatus = isset($_POST['pstatus']) ? 1 : 0;
$pcata   = mysqli_real_escape_string($con, $_POST['pcata']);
$pbrand  = mysqli_real_escape_string($con, $_POST['pbrand']);
$ptag    = mysqli_real_escape_string($con, $_POST['ptag']);
$sizes   = isset($_POST['sizes']) ? implode(",", $_POST['sizes']) : "";

// ✅ safe handling for colors
if (isset($_POST['colors'])) {
  $colors = is_array($_POST['colors']) ? implode(",", $_POST['colors']) : $_POST['colors'];
} else {
  $colors = "";
}

  // Handle images
  $uploaded_images = [];
  if (!empty($_FILES['images']['name'][0])) {
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
      $file_name = $_FILES['images']['name'][$key];
      $file_tmp  = $_FILES['images']['tmp_name'][$key];
      $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

      $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
      if (in_array($ext, $allowed_ext)) {
        $new_name = uniqid("IMG_", true) . "." . $ext;
        move_uploaded_file($file_tmp, "../Admin/Assets/" . $new_name);
        $uploaded_images[] = $new_name;
      }
    }
  }

  $image_list = implode(",", $uploaded_images);
  $firstImage = !empty($image_list) ? explode(",", $image_list)[0] : 'default.jpg';

  $sql_product = "INSERT INTO `product` 
    (`pname`, `pdesc`, `price`, `psku`, `pstock`, `pstatus`, `pcata`, `pbrand`, `ptag`, `sizes`, `colors`, `image`)
    VALUES 
    ('$pName', '$pDesc', '$price', '$pSKU', '$pStock', $pstatus, '$pcata', '$pbrand', '$ptag', '$sizes', '$colors', '$firstImage')";

  $res_product = mysqli_query($con, $sql_product);
echo "<pre>$sql_product</pre>";
echo mysqli_error($con);

  if ($res_product) {
    
    echo "<div class='alert alert-success mt-3'>✅ Product Added Successfully</div>";
  } else {
    echo "<div class='alert alert-danger mt-3'>❌ Error: " . mysqli_error($con) . "</div>";
  }
}
?>




  <style>
    .image-grid {
      width: 220px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .image-slot {
      width: 100px;
      height: 100px;
      border: 2px dashed #dee2e6;
      border-radius: 6px;
      cursor: pointer;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      overflow: hidden;
      background-color: #f8f9fa;
      transition: border-color 0.2s ease;
    }

    .image-slot span.plus {
      font-size: 2rem;
      color: #adb5bd;
    }

    .image-slot:hover {
      border-color: #0d6efd;
    }

    .image-slot img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      top: 0;
      left: 0;
    }

    .remove-btn {
      position: absolute;
      top: 2px;
      right: 2px;
      background: rgba(0, 0, 0, 0.5);
      border: none;
      color: #fff;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      line-height: 18px;
      text-align: center;
      font-size: 12px;
      cursor: pointer;
      display: none;
      z-index: 2;
    }

    .image-slot:hover .remove-btn {
      display: block;
    }

    .file-input {
      display: none;
    }
  </style>

  <div class="container py-5">
    <form method="post" action="" class="row g-4" enctype="multipart/form-data">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Add a New Product</h1>
        <div>
          <button type="reset" class="btn btn-outline-secondary">Discard</button>
          <button type="submit" name="psb" class="btn btn-primary">Publish Product</button>
        </div>
      </div>

      <!-- Left column -->
      <div class="col-lg-8">
        <!-- Product Info -->
        <div class="card mb-4">
          <div class="card-header bg-white">
            <h5 class="mb-0">Product Information</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Product Name</label>
              <input type="text" class="form-control" name="pName" placeholder="e.g., Premium Leather Wallet" required>
            </div>
            <div>
              <label class="form-label">Product Description</label>
              <textarea class="form-control" name="pDesc" rows="4"></textarea>
            </div>
          </div>
        </div>

        <!-- Pricing -->
        <div class="card mb-4">
          <div class="card-header bg-white">
            <h5 class="mb-0">Pricing & Inventory</h5>
          </div>
          <div class="card-body row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Price (₹)</label>
              <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" class="form-control" name="price" placeholder="99.99" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">SKU</label>
              <input type="text" class="form-control" name="pSKU" placeholder="e.g., WALLET-BLK-01">
            </div>
            <div class="col-md-6">
              <label class="form-label">Stock Quantity</label>
              <input type="number" name="pStock" class="form-control" placeholder="150" required>
            </div>
          </div>
        </div>

        <!-- Images -->
        <div class="card mb-4">
          <div class="card-header bg-white d-flex justify-content-between">
            <h5 class="mb-0">Images</h5>
            <button type="button" id="addMore" class="btn btn-sm btn-primary">+ Add Image</button>
          </div>
          <div class="card-body row" id="imageInputs">
            <div class="col-md-4 mb-3">
              <input type="file" name="images[]" class="form-control" required>
            </div>
          </div>
        </div>

        
        <!-- Sizes -->
        <div class="card mb-4">
          <div class="card-header bg-white">
            <h5 class="mb-0">Available Sizes</h5>
          </div>
          <div class="card-body d-flex flex-wrap gap-2">
            <?php
            $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
            foreach ($sizes as $s) {
              echo '
              <input type="checkbox" class="btn-check" id="size-' . $s . '" name="sizes[]" value="' . $s . '">
              <label class="btn btn-outline-primary rounded-pill" for="size-' . $s . '">' . $s . '</label>
              ';
            }
            ?>
          </div>
        </div>
      </div>

      <!-- Right column -->
      <div class="col-lg-4">
        <!-- Product Status -->
        <div class="card mb-4">
          <div class="card-header bg-white">
            <h5 class="mb-0">Product Status</h5>
          </div>
          <div class="card-body">
            <div class="form-check form-switch">
              <input class="form-check-input" name="pstatus" type="checkbox" role="switch" id="publishStatus" checked>
              <label class="form-check-label" for="publishStatus">Published</label>
            </div>
          </div>
        </div>

        <!-- Category & Brand -->
        <div class="card mb-4">
          <div class="card-header bg-white">
            <h5 class="mb-0">Organization</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Category</label>
              <select class="form-select" name="pcata">
                <?php
                // 👇 Sirf unique audience fetch kar rahe hain
                $sql_catagory = "SELECT DISTINCT audience FROM `catagory`";
                $res_catagory = mysqli_query($con, $sql_catagory);
                if ($res_catagory) {
                  while ($row = mysqli_fetch_assoc($res_catagory)) {
                    echo "<option value='{$row['audience']}'>{$row['audience']}</option>";
                  }
                }
                ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Brand</label>
              <select class="form-select" name="pbrand">
                <?php
                $sql_brand = "SELECT * FROM `brand`";
                $res_brand = mysqli_query($con, $sql_brand);
                if ($res_brand) {
                  while ($row = mysqli_fetch_assoc($res_brand)) {
                    echo "<option value='{$row['bname']}'>{$row['bname']}</option>";
                  }
                }
                ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Tags</label>
              <input type="text" class="form-control" name="ptag" placeholder="e.g., leather, gift, wallet">
            </div>
          </div>
        </div>

        <!-- Colors -->
        <div class="card">
          <div class="card-header bg-white">
            <h5 class="mb-0">Colors</h5>
          </div>
          <div class="card-body">
            <input id="colorInput" type="text" name="tempColor" class="form-control" placeholder="Enter colors (press Enter)">
            <ul id="colorList" class="chips d-flex flex-wrap gap-2 mt-2"></ul>

            <input type="hidden" name="colors" id="colorHidden">
          </div>
        </div>
      </div>

    </form>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    // --- Colors logic (chips) ---
    const colorInput = document.getElementById('colorInput');
    const colorList = document.getElementById('colorList');
    const colors = new Map();

    const escapeHTML = (str) =>
      str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;").replace(/'/g, "&#039;");

    const renderColorChip = (label) => {
      const trimmed = label.trim();
      if (!trimmed) return;
      const key = trimmed.toLowerCase();
      if (colors.has(key)) return;
      colors.set(key, trimmed);

      const li = document.createElement('li');
      li.className = 'badge text-bg-light border rounded-pill px-2 py-2';
      // li.innerHTML = `
      //   <span class="me-1">${escapeHTML(trimmed)}</span>
      //   <button type="button" class="btn btn-sm btn-link link-danger p-0 lh-1 remove">
      //     <span aria-hidden="true">&times;</span>
      //   </button>
      //   <input type="hidden" name="color[]" value="${escapeHTML(trimmed)}">
      // `;

      li.innerHTML = `
  <span class="me-1">${escapeHTML(trimmed)}</span>
  <button type="button" class="btn btn-sm btn-link link-danger p-0 lh-1 remove">
    <span aria-hidden="true">&times;</span>
  </button>
  <input type="hidden" name="colors[]" value="${escapeHTML(trimmed)}">
`;

      li.querySelector('.remove').addEventListener('click', () => {
        colors.delete(key);
        li.remove();
      });
      colorList.appendChild(li);
    };

    const addColorsFromInput = () => {
      const raw = colorInput.value;
      if (!raw.trim()) return;
      raw.split(',').map(s => s.trim()).filter(Boolean).forEach(renderColorChip);
      colorInput.value = '';
    };

    colorInput.addEventListener('blur', addColorsFromInput);
    colorInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addColorsFromInput();
      }
    });

    document.getElementById("addMore").addEventListener("click", function() {
      let div = document.createElement("div");
      div.classList.add("col-md-4", "mb-3");
      div.innerHTML = `<input type="file" name="images[]" class="form-control" >`;
      document.getElementById("imageInputs").appendChild(div);
    });
  </script>

<?php
include 'Dbconnection.php';

/* 🗑️ DELETE PRODUCT */
if (isset($_GET['delete'])) {
    $pid = intval($_GET['delete']);
    $sql = "DELETE FROM `product` WHERE `pid` = '$pid'";
    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Product deleted successfully!'); window.location='admin.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting product: " . mysqli_error($con) . "');</script>";
    }
}

/* ✏️ UPDATE PRODUCT */
if (isset($_POST['updateProduct'])) {
    $pid = $_POST['pid'];
    $pName = mysqli_real_escape_string($con, $_POST['pName']);
    $pDesc = mysqli_real_escape_string($con, $_POST['pDesc']);
    $price = $_POST['price'];
    $pSKU = mysqli_real_escape_string($con, $_POST['pSKU']);
    $pStock = $_POST['pStock'];
    $pSizes = mysqli_real_escape_string($con, $_POST['sizes'] ?? '');

    // Get old images
    $query = mysqli_query($con, "SELECT image FROM `product` WHERE `pid`='$pid'");
    $row = mysqli_fetch_assoc($query);
    $oldImages = $row ? explode(",", $row['image']) : [];

    // Upload new images
    $uploadedImages = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $filename = basename($_FILES['images']['name'][$key]);
            $targetPath = "Assets/" . $filename;
            if (move_uploaded_file($tmp_name, $targetPath)) {
                $uploadedImages[] = $filename;
            }
        }
    }

    // Merge old + new images
    $allImages = array_merge($oldImages, $uploadedImages);
    $image_list = implode(",", $allImages);

    $sql = "UPDATE product SET 
        pname='$pName', pdesc='$pDesc', price='$price', psku='$pSKU',
        pstock='$pStock', sizes='$pSizes', image='$image_list'
        WHERE pid='$pid'";

    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Product updated successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "Error updating product: " . mysqli_error($con);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>

<body class="bg-light">

    <div class="container-fluid py-4">
        <h1 class="mb-4 display-6">Dashboard Overview</h1>
        <div class="row g-4">
            <!-- Total Sales -->
            <div class="col-lg-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-circle bg-success-soft"><i class="fas fa-indian-rupee-sign"></i></div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Total Sales</h6>
                            <h4 class="mb-0 fw-bold">
                                ₹<?php
                                    $sql_sales = "SELECT SUM(gsum) AS total_sales FROM `order` WHERE status='Completed'";
                                    $res_sales = mysqli_query($con, $sql_sales);
                                    if ($res_sales) {
                                        $row = mysqli_fetch_assoc($res_sales);
                                        echo number_format($row['total_sales'] ?? 0, 2);
                                    } else echo "0.00";
                                    ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Sales -->
            <div class="col-lg-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-circle bg-danger-soft"><i class="fas fa-indian-rupee-sign"></i></div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Pending Sales</h6>
                            <h4 class="mb-0 fw-bold">
                                ₹<?php
                                    $sql_pending = "SELECT SUM(gsum) AS pending_sales FROM `order` WHERE status='Pending'";
                                    $res_pending = mysqli_query($con, $sql_pending);
                                    if ($res_pending) {
                                        $row = mysqli_fetch_assoc($res_pending);
                                        echo number_format($row['pending_sales'] ?? 0, 2);
                                    } else echo "0.00";
                                    ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="col-lg-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-circle bg-info-soft"><i class="fas fa-users"></i></div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Total Users</h6>
                            <h4 class="mb-0 fw-bold">
                                <?php
                                $sqlu = "SELECT COUNT(*) AS total_users FROM sign_up";
                                $resu = mysqli_query($con, $sqlu);
                                if ($resu) {
                                    $row = mysqli_fetch_assoc($resu);
                                    echo $row['total_users'];
                                } else echo "0";
                                ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="col-lg-3 col-md-6">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-circle bg-success-soft"><i class="fas fa-box-open"></i></div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Total Orders</h6>
                            <h4 class="mb-0 fw-bold">
                                <?php
                                $sqlo = "SELECT COUNT(*) AS total_orders FROM `order`";
                                $reso = mysqli_query($con, $sqlo);
                                if ($reso) {
                                    $row = mysqli_fetch_assoc($reso);
                                    echo $row['total_orders'];
                                } else echo "0";
                                ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Products Table -->
        <h2 class="mt-5 mb-3">All Products</h2>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Sno</th>
                                <th>Product Name</th>
                                <th>Logo</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>SKU</th>
                                <th>Stock</th>
                                <th>Sizes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM product";
                            $res = mysqli_query($con, $sql);
                            if ($res) {
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($res)) {
                                    $images = explode(',', $row['image']);
                                    $first_img = !empty($images[0]) ? trim($images[0]) : 'default.jpg';
                                    echo "
                            <tr>
                                <td>{$i}</td>
                                <td>" . htmlspecialchars($row['pname']) . "</td>
                                <td><img src='Assets/" . htmlspecialchars($first_img) . "' width='50' height='50' style='object-fit:contain;'></td>
                                <td>" . substr(htmlspecialchars($row['pdesc']), 0, 50) . "...</td>
                                <td>₹" . $row['price'] . "</td>
                                <td>" . htmlspecialchars($row['psku']) . "</td>
                                <td>" . $row['pstock'] . "</td>
                                <td>" . htmlspecialchars($row['sizes']) . "</td>
                                <td>
                                    <i class='fa-solid fa-pen-to-square text-primary editBtn' style='cursor:pointer;'
                                        data-id='{$row['pid']}'
                                        data-name='" . htmlspecialchars($row['pname'], ENT_QUOTES) . "'
                                        data-desc='" . htmlspecialchars($row['pdesc'], ENT_QUOTES) . "'
                                        data-price='{$row['price']}'
                                        data-sku='" . htmlspecialchars($row['psku'], ENT_QUOTES) . "'
                                        data-stock='{$row['pstock']}'
                                        data-sizes='" . htmlspecialchars($row['sizes'], ENT_QUOTES) . "'
                                        data-image='" . htmlspecialchars($row['image'], ENT_QUOTES) . "'></i>
                                    &nbsp;
                                    <a href='admin.php?delete={$row['pid']}' onclick='return confirm(\"Delete this product?\")'>
                                        <i class='fa-solid fa-trash text-danger'></i>
                                    </a>
                                </td>
                            </tr>";
                                    $i++;
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-danger'>Query failed</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="pid" id="edit_pid">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="pName" id="edit_pName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="pDesc" id="edit_pDesc" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="pSKU" id="edit_pSKU" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="pStock" id="edit_pStock" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sizes</label>
                            <input type="text" name="sizes" id="edit_sizes" class="form-control" placeholder="S,M,L,XL">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Images</label>
                            <input type="file" name="images[]" id="editImages" class="form-control" multiple>
                            <div id="existingImages" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="updateProduct">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('edit_pid').value = btn.dataset.id;
                document.getElementById('edit_pName').value = btn.dataset.name;
                document.getElementById('edit_pDesc').value = btn.dataset.desc;
                document.getElementById('edit_price').value = btn.dataset.price;
                document.getElementById('edit_pSKU').value = btn.dataset.sku;
                document.getElementById('edit_pStock').value = btn.dataset.stock;
                document.getElementById('edit_sizes').value = btn.dataset.sizes || '';

                const imageContainer = document.getElementById('existingImages');
                imageContainer.innerHTML = '';
                btn.dataset.image.split(',').forEach(img => {
                    if (img.trim() !== '') {
                        const imgTag = document.createElement('img');
                        imgTag.src = 'Assets/' + img.trim();
                        imgTag.width = 60;
                        imgTag.height = 60;
                        imgTag.style.objectFit = 'contain';
                        imgTag.className = 'border rounded';
                        imageContainer.appendChild(imgTag);
                    }
                });

                new bootstrap.Modal(document.getElementById('editProductModal')).show();
            });
        });
    </script>

</body>

</html>
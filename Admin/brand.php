<?php

include 'Dbconnection.php';

if (isset($_POST['bsb'])) {
    $bname = $_POST["bname"];
    $description = $_POST["brandDescription"];
    $status = isset($_POST['brandStatus']) ? 1 : 0;
    $img1 = $_FILES['blogo']['name'];
    $img1_tmp = $_FILES['blogo']['tmp_name'];

    if (!empty($img1) && move_uploaded_file($img1_tmp, 'Assets/' . $img1)) {
        $sqlb = "INSERT INTO `brand` (`bname`, `brand_img`, `description`, `status`) VALUES ('$bname', '$img1','$description', $status)";

        $res = mysqli_query($con, $sqlb);

        echo $res ? "<div class='alert alert-success mt-3'>Brand Added Successfully</div>"
            : "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($con) . "</div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Image upload failed.</div>";
    }
}

// Handle Edit
if (isset($_POST['edit_brand_id'])) {
    $id = intval($_POST['edit_brand_id']);
    $bname = $_POST["edit_bname"];
    $description = $_POST["edit_description"];
    $status = isset($_POST['edit_status']) ? 1 : 0;

    $img1 = $_FILES['edit_blogo']['name'];
    $img1_tmp = $_FILES['edit_blogo']['tmp_name'];

    if (!empty($img1)) {
        move_uploaded_file($img1_tmp, 'Assets/' . $img1);
        $sql_update = "UPDATE `brand` 
                       SET bname='$bname',
                           brand_img='$img1',
                           description='$description',
                           status=$status 
                       WHERE bid=$id";
    } else {
        $sql_update = "UPDATE `brand` 
                       SET bname='$bname',
                           description='$description',
                           status=$status 
                       WHERE bid=$id";
    }

    $res_update = mysqli_query($con, $sql_update);

    echo $res_update
        ? "<div class='alert alert-success mt-3'>Brand Updated Successfully</div>"
        : "<div class='alert alert-danger mt-3'>Error: " . mysqli_error($con) . "</div>";
}


?>

    <style>
        .stat-card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.75rem;
        }
    </style>

<body>
    <div class="container-fluid py-4">
        <div class="row">

            <div class="col-lg-7 mb-4">
                <div class="card stat-card">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for a brand...">
                            <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Sno</th>
                                        <th>Brand Name</th>
                                        <th>Logo</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM `brand`";
                                    $res = mysqli_query($con, $sql);
                                    if ($res) {
                                        $i = 1;
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            echo "<tr>
                                                <td>{$i}</td>
                                                <td>" . htmlspecialchars($row['bname']) . "</td>
                                                <td><img src='Assets/" . htmlspecialchars($row['brand_img']) . "' width='50' height='50' style='object-fit:contain;'></td>
                                                <td>" . htmlspecialchars($row['description']) . "</td>
                                                <td>" . ($row['status'] ? 'Active' : 'Inactive') . "</td>
                                                <td>
                                                <i class='fa-solid fa-pen-to-square text-primary editBtn' style='cursor:pointer;'
                                                   data-id='" . $row['bid'] . "'
                                                   data-name='" . htmlspecialchars($row['bname'], ENT_QUOTES) . "'
                                                   data-img='" . htmlspecialchars($row['brand_img'], ENT_QUOTES) . "'
                                                   data-desc='" . htmlspecialchars($row['description'], ENT_QUOTES) . "'
                                                   data-status='" . $row['status'] . "'>
                                                </i>
                                                <i class='fa-solid fa-trash text-danger deleteBtn' data-id='" . $row['bid'] . "'></i>
                                                </td>
                                            </tr>";
                                            $i++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center text-danger'>Query failed: " . mysqli_error($con) . "</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Brand -->
            <div class="col-lg-5">
                <div class="card stat-card">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">Add New Brand</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="brandNameInput" class="form-label">Brand Name</label>
                                <input type="text" name="bname" class="form-control" id="brandNameInput" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Brand Logo</label>
                                <div class="text-center">
                                    <img id="logo-preview" src="https://placehold.co/150x150" class="rounded mb-2" style="width:150px; height:150px; object-fit:contain; border:1px solid #ddd;" alt="Brand Preview">
                                    <input type="file" name="blogo" class="form-control" id="brandLogoInput" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="brandDescription" class="form-label">Description (Optional)</label>
                                <textarea class="form-control" name="brandDescription" id="brandDescription" rows="3"></textarea>
                            </div>
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="brandStatus" value="1" id="brandStatus" checked>
                                <label class="form-check-label" for="brandStatus">Active</label>
                            </div>
                            <button type="submit" name="bsb" class="btn btn-primary w-100">Save Brand</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Brand Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Brand</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_brand_id" id="edit_brand_id">

                        <div class="mb-3">
                            <label class="form-label">Brand Name</label>
                            <input type="text" name="edit_bname" id="edit_bname" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Brand Logo</label>
                            <div class="text-center">
                                <img id="edit_logo_preview" src="https://placeholder.co/150"
                                    class="rounded mb-2"
                                    style="width:150px; height:150px; object-fit:contain; border:1px solid #ddd;"
                                    alt="Brand Logo Preview">
                                <input type="file" name="edit_blogo" class="form-control" id="edit_blogo">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="edit_description" id="edit_description" rows="3"></textarea>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="edit_status" value="1" id="edit_status">
                            <label class="form-check-label" for="edit_status">Active</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this brand?
                        <input type="hidden" name="delete_brand_id" id="delete_brand_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    
    <script>
        // Preview logo
        $('#brandLogoInput').on('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                $('#logo-preview').attr('src', URL.createObjectURL(file));
            }
        });

        // Delete confirmation
        document.querySelectorAll('.deleteBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                let id = this.getAttribute('data-id');
                document.getElementById('delete_brand_id').value = id;
                let modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            });
        });

        // Open edit modal and fill data
        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Get values from button attributes
                let id = this.getAttribute('data-id');
                let name = this.getAttribute('data-name');
                let img = this.getAttribute('data-img');
                let desc = this.getAttribute('data-desc');
                let status = this.getAttribute('data-status');

                // Fill modal form
                document.getElementById('edit_brand_id').value = id;
                document.getElementById('edit_bname').value = name;
                document.getElementById('edit_description').value = desc;
                document.getElementById('edit_logo_preview').src = "Assets/" + img;
                document.getElementById('edit_status').checked = (status == 1);

                // Show modal
                let modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            });
        });

        // Preview new logo
        $('#edit_blogo').on('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                $('#edit_logo_preview').attr('src', URL.createObjectURL(file));
            }
        });
    </script>



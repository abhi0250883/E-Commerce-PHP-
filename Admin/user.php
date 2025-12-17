<?php
    
include "Dbconnection.php";

// Set how many entries per page
$limit = 15;

// Get the current page number from the URL, default is 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

// Calculate the starting row for the query
$offset = ($page - 1) * $limit;

// Count total records
$total_query = "SELECT COUNT(*) AS total FROM sign_up";
$total_result = mysqli_query($con, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];

// Calculate total pages
$total_pages = ceil($total_records / $limit);

// Fetch paginated data
$sql = "SELECT * FROM sign_up LIMIT $limit OFFSET $offset";
$res = mysqli_query($con, $sql);
?>

<div class="container-fluid">
    <div class="container mt-4">
        <table class="table table-striped table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Sno</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Email ID</th>
                    <th scope="col">Phone Number</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($res && mysqli_num_rows($res) > 0) {
                    $sno = $offset + 1;
                    while ($row = mysqli_fetch_assoc($res)) {
                        echo "<tr>
                            <td>{$sno}</td>
                            <td>" . htmlspecialchars($row['First_name']) . "</td>
                            <td>" . htmlspecialchars($row['Last_name']) . "</td>
                            <td>" . htmlspecialchars($row['Email']) . "</td>
                            <td>" . htmlspecialchars($row['Mobile']) . "</td>
                        </tr>";
                        $sno++;
                    }
                } else {
                    echo "<tr><td colspan='5'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php
                // Show numbered pages
                for ($i = 1; $i <= $total_pages; $i++) {
                    $active = $i == $page ? 'active' : '';
                    echo "<li class='page-item $active'><a class='page-link' href='?page=$i'>$i</a></li>";
                }
                ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

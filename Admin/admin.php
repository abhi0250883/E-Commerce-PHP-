<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Agar admin login nahi hai to redirect
require("Dbconnection.php");


if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -16rem;
            transition: all 0.3s ease-out;
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
            width: 16rem;
        }

        /* Added width for consistency */
        #sidebar-wrapper .sidebar-heading {
            padding: 1.25rem 1.25rem;
            font-size: 1.4rem;
            font-weight: 600;
            color: #343a40;
            border-bottom: 1px solid #dee2e6;
            transition: all 0.3s ease-out;
        }

        #sidebar-wrapper .list-group {
            width: 100%;
        }

        /* Changed to 100% to adapt to parent width */
        .list-group-item-action {
            font-weight: 500;
            color: #495057;
            padding: 1rem 1.25rem;
            border: 0;
            border-left: 4px solid transparent;
            transition: all 0.3s ease-out;
        }

        .list-group-item-action:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
            border-left: 4px solid #0d6efd;
        }

        .list-group-item-action.active {
            background-color: #e9ecef;
            color: #0d6efd;
            font-weight: 600;
            border-left: 4px solid #0d6efd;
        }

        .list-group-item-action .fa-fw {
            width: 1.5em;
        }

        #page-content-wrapper {
            min-width: 100vw;
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* New styles for collapsed (mini) sidebar */
        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
                width: 16rem;
            }

            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }

            #wrapper.toggled #sidebar-wrapper {
                width: 5rem;
                /* Mini width */
                margin-left: 0;
                /* Keep visible but narrow */
            }

            #wrapper.toggled .sidebar-heading {
                padding: 1rem;
                /* Reduced padding */
                font-size: 1rem;
                /* Smaller font */
                text-align: center;
            }

            #wrapper.toggled .list-group-item-action {
                padding: 1rem 0;
                /* Center padding */
                text-align: center;
                border-left: none;
                /* Remove border for mini view */
            }

            #wrapper.toggled .list-group-item-action:hover {
                border-left: none;
                /* No border on hover in mini */
            }

            #wrapper.toggled .list-group-item-action.active {
                border-left: none;
                /* No border for active in mini */
            }

            #wrapper.toggled .menu-text {
                display: none;
                /* Hide text in mini mode */
            }

            #wrapper.toggled .sidebar-heading .menu-text {
                display: none;
                /* Hide heading text in mini mode */
            }

            #wrapper.toggled .fa-fw {
                margin: 0;
                /* Center icons */
            }
        }

        /* Existing styles continued */
        .content-section {
            display: none;
            min-height: 80vh;
            background-color: #fff;
            /* ensures not black */
            padding: 20px;
        }


        #dashboard {
            display: block;
        }

        .stat-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 1.5rem;
            color: #fff;
        }

        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.2);
            color: #0d6efd !important;
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.2);
            color: #198754 !important;
        }

        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.2);
            color: #0dcaf0 !important;
        }

        .bg-danger-soft {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545 !important;
        }

        .admin-page {
            text-decoration: none;
        }

        .list-group {
            cursor: pointer;
        }
    </style>


</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading text-center">
                <a href="admin.php" class="admin-page">
                    <i class="fas fa-store me-2"></i><span class="menu-text"><strong>Admin</strong>Panel</span>
                </a>
            </div>
            <div class="list-group list-group-flush mt-3">

                <a class="list-group-item list-group-item-action nav-link active" data-target="dashboard">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i><span class="menu-text">Dashboard</span>
                </a>
                <a class="list-group-item list-group-item-action nav-link" data-target="add-product">
                    <i class="fas fa-plus-circle fa-fw me-2"></i><span class="menu-text">Add Product</span>
                </a>
                <a class="list-group-item list-group-item-action nav-link" data-target="add-brand">
                    <i class="fas fa-copyright fa-fw me-2"></i><span class="menu-text">Add Brand</span>
                </a>
                <a class="list-group-item list-group-item-action nav-link" data-target="category">
                    <i class="fas fa-list fa-fw me-2"></i><span class="menu-text">Category</span>
                </a>
                <a class="list-group-item list-group-item-action nav-link" data-target="discount-card">
                    <i class="fas fa-tags fa-fw me-2"></i><span class="menu-text">Discount Card</span>
                </a>
                <a class="list-group-item list-group-item-action nav-link" data-target="user">
                    <i class="fa-solid fa-user fa-fw me-2"></i><span class="menu-text">User</span>
                </a>
            </div>
        </div>


        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4">
                <button class="btn btn-outline-primary" id="menu-toggle"><i class="fas fa-align-left"></i></button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="d-flex ms-auto my-2 my-lg-0">
                        <div class="input-group">
                            <input class="form-control" type="search" placeholder="Search for..." aria-label="Search">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>

                    <ul class="navbar-nav ms-3">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="admin.php" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user fa-fw"></i> Admin
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="profile.php">Profile</a>
                                <a class="dropdown-item" href="">Settings</a>
                                <div class="dropdown-divider"></div>
                                <!-- ✅ Logout link -->
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <!-- Dashboard Section -->
                <!-- Add Dashboard Section -->
                <div id="dashboard" class="content-section">
                    <h2>All Products</h2>
                    <?php include 'display.php'; ?>
                </div>
                
                <!-- Add Product Section -->
                <div id="add-product" class="content-section">
                    <h2>Add New Product</h2>
                    <?php include 'product.php'; ?>
                </div>
                
                <!-- Add Brand Section -->
                <div id="add-brand" class="content-section">
                    <h2>Add New Brand</h2>
                    <?php include 'brand.php'; ?>
                </div>
                
                <!-- Discount Section -->
                <div id="discount-card" class="content-section">
                    <h2>Manage Discount Cards</h2>
                    <?php include "discount.php"; ?>
                </div>
                
                <!-- User Section -->
                <div id="user" class="content-section">
                    <h2>User Details</h2>
                    <?php include 'user.php'; ?>
                    </div>
                    
                    <!-- Category Section -->
                    <div id="category" class="content-section">
                        <h2>Manage Categories</h2>
                        <?php include 'catagory.php'; ?>
                    </div>
                    
                    

            </div>
        </div>
    </div>

    <!-- Scripts -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Sidebar toggle
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    // Hide all content sections initially
    $(".content-section").hide();

    // Function to show section based on target
    function showSection(target) {
        $(".content-section").hide();
        $(".nav-link").removeClass("active");

        const section = $("#" + target);
        if (section.length) {
            section.fadeIn(200);
            $(`.nav-link[data-target='${target}']`).addClass("active");
        } else {
            console.warn("⚠️ Section not found:", target);
        }
    }

    // ✅ Show last opened section after reload
const lastSection = sessionStorage.getItem("lastSection") || "dashboard";
showSection(lastSection);

// Save clicked section
$(".nav-link").on("click", function(e) {
  e.preventDefault();
  const target = $(this).data("target").trim();
  sessionStorage.setItem("lastSection", target);
  showSection(target);
});

    // ✅ Sidebar navigation click
    $(".nav-link").on("click", function(e) {
        e.preventDefault();
        const target = $(this).data("target").trim();
        showSection(target);

        // ✅ Remove hash from the URL without reloading
        history.pushState(null, null, window.location.pathname);

        // Scroll to top smoothly
        $("html, body").animate({ scrollTop: 0 }, "fast");
    });
});

</script>


</body>

</html>
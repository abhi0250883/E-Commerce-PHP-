<?php
session_start();
require('Dbconnection.php');

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: admin.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $sql = "SELECT * FROM admin WHERE name='$username' AND password='$password' LIMIT 1";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin_id']   = $row['id'];
        $_SESSION['admin_name'] = $row['username'];
        $_SESSION['is_admin']   = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login — FashionHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        rel="stylesheet">

    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.3);
            --radius: 20px;
            --shadow: rgba(0, 0, 0, 0.1);
            --transition: 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0eafc, #cfdef3);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Floating circles */
        .circle {
            position: absolute;
            border-radius: 50%;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            animation: float 6s alternate infinite;
        }

        .circle.one {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 15%;
        }

        .circle.two {
            width: 150px;
            height: 150px;
            bottom: 10%;
            right: 20%;
        }

        .circle.three {
            width: 100px;
            height: 100px;
            top: 40%;
            right: 10%;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #777;
            font-size: 1.1rem;
            pointer-events: none;
        }

        .form-control.ps-5 {
            padding-left: 2.5rem !important;
            /* or adjust to match icon width */
        }

        @keyframes float {
            to {
                transform: translateY(-20px);
            }
        }

        /* Glassy login card */
        .login-card {
            position: relative;
            z-index: 1;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(20px);
            border-radius: var(--radius);
            padding: 2.5rem 2rem;
            box-shadow: 0 8px 32px var(--shadow);
            width: 100%;
            max-width: 380px;
            animation: fadeInUp 0.8s var(--transition);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card h3 {
            text-align: center;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .login-card .form-label {
            font-weight: 500;
            color: #444;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: .75rem 1rem;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            outline: none;
        }

        .login-card .input-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #777;
            font-size: 1rem;
        }

        .position-relative {
            position: relative;
        }

        .btn-login {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 30px;
            padding: .75rem;
            width: 100%;
            font-weight: 500;
            transition: all var(--transition);
        }

        .btn-login:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--shadow);
        }

        .text-danger {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <!-- Decorative circles -->
    <div class="circle one"></div>
    <div class="circle two"></div>
    <div class="circle three"></div>

    <div class="login-card">
        <h3><i class="fas fa-lock"></i> Admin Login</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <!-- Username -->
            <div class="mb-3 position-relative">
                <i class="fas fa-user input-icon"></i>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control ps-5"
                    placeholder="Username"
                    required>
            </div>

            <!-- Password -->
            <div class="mb-4 position-relative">
                <i class="fas fa-lock input-icon"></i>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control ps-5"
                    placeholder="Password"
                    required>
            </div>

            <button type="submit" name="login" class="btn btn-login w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>
        </form>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
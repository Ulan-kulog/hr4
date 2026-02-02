<?php

use PHPMailer\PHPMailer\PHPMailer;

session_start();
include("connection.php");

$baseUrl = isset($_SERVER['HTTPS']) ? "https://" : "http://";
$baseUrl .= $_SERVER['HTTP_HOST'] . 'localhost';
// Alternatively, if you have a specific base path:
// $baseUrl = "http://yourdomain.com/yourproject/";

// Check if image exists (optional but recommended)
$imagePath = 'images/hotel3.jpg';
$imageExists = file_exists($imagePath);

// Database connections
// $usm_connection = $connections["rest_soliera_usm"];
// $cr2_usm = $connections["rest_core_2_usm"];

// Initialize variables
$employee_ID = trim($_POST["employee_id"] ?? '');
$password = trim($_POST["password"] ?? '');

// DB connection to main app database
$conn = $connections['hr4_hr_4'] ?? null;
// dd($conn);

$loginAttemptsKey = "login_attempts_$employee_ID";

// === Function: Log user login attempts ===
// function logAttempt($conn, $Employee_ID, $Role, $Log_Status, $Attempt_Count, $Cooldown)
// {
//     $date = date('Y-m-d H:i:s');
//     $sql = "INSERT INTO department_logs (department_account_id, status, attempt_count, Cooldown) 
//             VALUES (?, ?, ?, ?)";
//     $stmt = mysqli_prepare($conn, $sql);
//     mysqli_stmt_bind_param($stmt, "ssss", $Employee_ID,  $Log_Status, $Attempt_Count, $Cooldown);
//     mysqli_stmt_execute($stmt);
// }

// function logDepartmentAttempt($conn, $Department_ID, $employee_ID, $Name,  $Log_Status, $Attempt_type, $Attempt_Count, $Failure_reason, $Cooldown_Until)
// {
//     $Log_Date_Time = date('Y-m-d H:i:s');
//     $sql = "INSERT INTO department_logs (dept_id, employee_id, employee_name, role, log_status, log_type, attempt_count, failure_reason, cooldown, date)
//             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
//     $stmt = mysqli_prepare($conn, $sql);
//     mysqli_stmt_bind_param($stmt, "ssssssisss", $Department_ID, $employee_ID, $Name, $Role, $Log_Status, $Attempt_type, $Attempt_Count, $Failure_reason, $Cooldown_Until, $Log_Date_Time);
//     mysqli_stmt_execute($stmt);
// }

// // Simple department_logs inserter matching current schema
// function log_department_log($conn, $department_account_id, $status, $attempt_count = 0, $details = '')
// {
//     $created_at = date('Y-m-d H:i:s');
//     $sql = "INSERT INTO department_logs (department_account_id, status, attempt_count, details, created_at) VALUES (?, ?, ?, ?, ?)";
//     $stmt = mysqli_prepare($conn, $sql);
//     if (!$stmt) return false;
//     mysqli_stmt_bind_param($stmt, 'isiss', $department_account_id, $status, $attempt_count, $details, $created_at);
//     return mysqli_stmt_execute($stmt);
// }

// === Function: Increment login attempts ===
function incrementLoginAttempts($employee_ID)
{
    $key = "login_attempts_$employee_ID";
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'last' => time()];
    } else {
        $_SESSION[$key]['count']++;
        $_SESSION[$key]['last'] = time();
    }
}

// // === Function: Send OTP via email ===
function sendOTP($email, $otp)
{
    // Use Composer autoloader which includes PHPMailer
    require_once __DIR__ . '/vendor/autoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'soliera.restaurant@gmail.com';
    $mail->Password = 'rpyo ncni ulhv lhpx';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('Soliera_Hotel&Restaurant@gmail.com', 'Soliera 2FA Authenticator');
    $mail->addAddress($email);
    $mail->Subject = 'Soliera 2FA Verification Code';

    // Email content
    $header = "<h2 style='color:#4CAF50; font-family: Arial, sans-serif;'>Soliera Hotel & Restaurant</h2>
               <hr style='border:1px solid #ddd;'>";
    $message = "<p style='font-family: Arial, sans-serif; font-size:14px;'>
                    <br>
                    We received a request to verify your login to <strong>Soliera Hotel & Restaurant</strong>.
                    Please use the one-time verification code below to complete your login:
                </p>
                <p style='font-size:22px; font-weight:bold; color:#333; letter-spacing:2px;'>
                    $otp
                </p>
                <p style='font-family: Arial, sans-serif; font-size:14px; color:#555;'>
                    This code will expire in <strong>5 minutes</strong> for your security.
                    If you did not request this code, please ignore this email or contact our support team immediately.
                </p>";
    $footer = "<hr style='border:1px solid #ddd;'>
               <p style='font-size:12px; color:#777; font-family: Arial, sans-serif;'>
                    Thank you for choosing Soliera.<br>
                    📞 Hotline: +63-900-123-4567 | 📧 support@soliera.com<br>
                    <em>This is an automated message. Please do not reply directly to this email.</em>
               </p>";

    $mail->isHTML(true);
    $mail->Body = $header . $message . $footer;

    return $mail->send();
}

// === Cooldown enforcement ===
if ($employee_ID !== '' && isset($_SESSION[$loginAttemptsKey]) && $_SESSION[$loginAttemptsKey]['count'] >= 5) {
    $lastAttempt = $_SESSION[$loginAttemptsKey]['last'];
    $remaining = 3600 - (time() - $lastAttempt);
    if ($remaining > 0) {
        $minutes = ceil($remaining / 60);
        $cooldownUntil = date('Y-m-d H:i:s', $lastAttempt + 3600);
        $_SESSION["loginError"] = "Your account is temporarily banned. Try again in $minutes minute(s).";
        header("Location: index.php");
        exit();
    } else {
        unset($_SESSION[$loginAttemptsKey]);
    }
}

// === Main Login Logic ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && $employee_ID && $password) {
    // dd("Login attempt for Employee ID: $employee_ID");
    if (!$conn) {
        $_SESSION["loginError"] = "Database connection not available.";
        header("Location: index.php");
        exit();
    }

    // cooldown check
    if (isset($_SESSION[$loginAttemptsKey]) && $_SESSION[$loginAttemptsKey]['count'] >= 5) {
        $lastAttempt = $_SESSION[$loginAttemptsKey]['last'];
        $remaining = 3600 - (time() - $lastAttempt);
        if ($remaining > 0) {
            $minutes = ceil($remaining / 60);
            $_SESSION["loginError"] = "Your account is temporarily banned. Try again in $minutes minute(s).";
            header("Location: index.php");
            exit();
        } else {
            unset($_SESSION[$loginAttemptsKey]);
        }
    }
    // dd($_POST);

    // Lookup user in department_accounts
    $sql = "SELECT employee_id, dept_id, employee_name, role, email, status, password FROM department_accounts WHERE employee_id = ? LIMIT 1";
    // dd($sql);
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        // Prepare failed — log DB error and surface it for local debugging
        $dberr = mysqli_error($conn);
        error_log("DB prepare failed: $dberr | SQL: $sql");
        // For local debugging show DB error; remove or simplify in production
        $_SESSION['loginError'] = 'Database error: ' . htmlspecialchars($dberr);
        header('Location: index.php');
        exit();
    }
    // dd($stmt);

    mysqli_stmt_bind_param($stmt, 's', $employee_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    // dd($result);
    if ($result && mysqli_num_rows($result) > 0) {
        // dd("User found");
        $row = mysqli_fetch_assoc($result);
        // $department_account_id = (int)$row['id'];
        $Department_ID = $row['dept_id'];
        $Role = $row['role'];
        $Name = $row['employee_name'];
        // dd($row);
        if ($password === $row['password']) {
            // Successful login
            $_SESSION['employee_id'] = $employee_ID;
            $_SESSION['role'] = $Role;
            $_SESSION['Dept_id'] = $row['dept_id'];
            $_SESSION['email'] = $row['email'] ?? '';
            // $_SESSION['department_account_id'] = $department_account_id;
            // dd($Role);
            unset($_SESSION[$loginAttemptsKey]);
            $otp = rand(100000, 999999);
            $_SESSION["otp"] = (string)$otp;
            $_SESSION["otp_expiry"] = time() + 300; // 5 minutes expiry

            // Store pending login info
            $_SESSION["pending_employee_id"] = $employee_ID;
            $_SESSION["pending_role"] = $Role;
            $_SESSION["pending_Dept_id"] = $row["dept_id"];
            $_SESSION["pending_email"] = $row["email"];
            $_SESSION["otp_attempts"] = 0;
            $_SESSION["auth_method"] = "2FA";

            if (sendOTP($row["email"], $otp)) {
                // logAttempt($conn, $employee_ID, $Name, $Role, 'Authenticating', 'Login', 0, 'Authenticating', '');
                // logDepartmentAttempt($conn, $Department_ID, $employee_ID, $Name, $Role, 'Success', 'Login', 0, 'Login Successful', '');
                header("Location: USM/2fa_verify.php");
                exit();
            } else {
                // logAttempt($conn, $employee_ID, $Name, $Role, 'Failed', 'Login', 0, 'Failed to send OTP email', '');
                $_SESSION["loginError"] = "Failed to send OTP email.";
                header("Location: index.php");
                exit();
            }
        } else {
            // failed password
            incrementLoginAttempts($employee_ID);
            // log_department_log($conn, $department_account_id, 'failed', $_SESSION[$loginAttemptsKey]['count'] ?? 1, 'Incorrect password');
            $_SESSION['loginError'] = 'Incorrect employee ID or password.';
            header('Location: index.php');
            exit();
        }
    } else {
        // user not found
        incrementLoginAttempts($employee_ID);
        // log_department_log($conn, 0, 'failed', $_SESSION[$loginAttemptsKey]['count'] ?? 1, 'Employee not found');
        $_SESSION['loginError'] = 'Invalid employee ID or password.';
        header('Location: index.php');
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Soliera Hotel - Department Login</title>

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        /* Custom styles */
        .bg-fallback {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>

<body>


    <section class="relative w-full h-screen">
        <!-- Background image with overlay -->
        <div class="z-0 absolute inset-0 bg-cover bg-center" style="background-image: url('<?php echo $imageExists ? $imagePath : ''; ?>');">
            <!-- Fallback in case image doesn't load -->
            <?php if (!$imageExists): ?>
                <div class="bg-fallback w-full h-full">
                    <div>Soliera Hotel & Restaurant</div>
                </div>
            <?php endif; ?>
        </div>
        <div class="z-10 absolute inset-0 bg-black/40"></div>
        <div class="z-10 absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/70"></div>

        <!-- Content container -->
        <div class="z-10 relative flex justify-center items-center p-4 w-full h-full">
            <div class="max-md:hidden flex justify-center items-center w-1/2">
                <div class="p-8 max-w-lg">
                    <!-- Hotel & Restaurant Illustration -->
                    <div class="mb-8 text-center">
                        <a href="/images/soliera_S.png">
                            <img data-aos="zoom-in" data-aos-delay="100"
                                class="w-screen max-h-60 hover:scale-105 transition-all"
                                src="/images/tagline_no_bg.png"
                                alt="Soliera Hotel Logo">
                        </a>
                        <h1 data-aos="zoom-in-up" data-aos-delay="200"
                            class="mb-2 font-bold text-white text-3xl">
                            Welcome to <span class="text-[#F7B32B]">Soliera</span> Hotel & Restaurant
                        </h1>
                        <p data-aos="zoom-in-up" data-aos-delay="300" class="text-white/80">
                            Savor The Stay, Dine With Elegance
                        </p>
                    </div>


                    <!-- Features List -->
                    <div data-aos="zoom-in-up" data-aos-delay="400" class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400 lucide lucide-star">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-white">5-Star Restaurant Quality</p>
                                <p class="text-white/70 text-sm">Award-winning cuisine prepared by internationally trained chefs</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400 lucide lucide-utensils">
                                    <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2" />
                                    <path d="M7 2v20" />
                                    <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-white">Authentic Filipino Cuisine</p>
                                <p class="text-white/70 text-sm">Traditional recipes with a modern gourmet twist</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400 lucide lucide-calendar-check">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                    <line x1="16" x2="16" y1="2" y2="6" />
                                    <line x1="8" x2="8" y1="2" y2="6" />
                                    <line x1="3" x2="21" y1="10" y2="10" />
                                    <path d="m9 16 2 2 4-4" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-white">Seamless Reservations</p>
                                <p class="text-white/70 text-sm">Book tables, events, and services with just a few clicks</p>
                            </div>
                        </div>





                    </div>
                </div>
            </div>

            <div class="flex justify-center items-center w-1/2 max-md:w-full">
                <div class="bg-white/10 shadow-2xl backdrop-blur-lg p-6 border border-white/20 rounded-xl w-full max-w-md">
                    <!-- Card Header -->
                    <div class="flex flex-col justify-center items-center mb-6 text-center">
                        <h2 class="font-bold text-white text-2xl">Sign in to your account</h2>
                        <p class="mt-1 text-white/80">Enter your credentials to continue</p>
                    </div>

                    <!-- Card Body -->
                    <div>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <!-- Employee ID Input -->
                            <div class="mb-4">
                                <label class="block mb-2 font-medium text-white/90 text-sm" for="employee_id">
                                    Employee ID
                                </label>
                                <div class="relative">
                                    <div class="left-0 absolute inset-y-0 flex items-center pl-3 pointer-events-none">
                                        <i class='text-white/50 bx bx-user'></i>
                                    </div>
                                    <input
                                        id="employee_id"
                                        type="text"
                                        class="bg-white/5 py-3 pr-3 pl-10 border border-white/20 focus:border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 w-full text-white placeholder-white/50"
                                        placeholder="Your ID"
                                        required
                                        name="employee_id"
                                        value="<?php echo htmlspecialchars($employee_ID); ?>">
                                </div>
                            </div>

                            <!-- Password Input with Toggle -->
                            <div class="mb-6">
                                <label class="block mb-2 font-medium text-white/90 text-sm" for="password">
                                    Password
                                </label>
                                <div class="relative">
                                    <div class="left-0 absolute inset-y-0 flex items-center pl-3 pointer-events-none">
                                        <i class='text-white/50 bx bx-key'></i>
                                    </div>
                                    <input
                                        id="password"
                                        type="password"
                                        class="bg-white/5 py-3 pr-10 pl-10 border border-white/20 focus:border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/50 w-full text-white placeholder-white/50"
                                        placeholder="Password"
                                        required
                                        name="password">
                                    <button
                                        type="button"
                                        class="right-0 absolute inset-y-0 flex items-center pr-3 focus:outline-none text-white/50 hover:text-white"
                                        onclick="togglePasswordVisibility()">
                                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                        <svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" class="hidden w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex justify-between items-center mb-6">

                                <div class="text-sm">
                                    <a href="javascript:void(0)" onclick="toggleForgotModal(true)" class="font-medium text-blue-400 hover:text-blue-300">
                                        Forgot password?
                                    </a>
                                </div>
                            </div>

                            <!-- Google reCAPTCHA widget -->
                            <!-- <div class="mb-4">
                                <div class="g-recaptcha" data-sitekey="6Ld4W8ArAAAAAK3qsDWjdvj6MNiXFJDPMgHGfhrw"></div>
                            </div> -->

                            <!-- Sign In Button -->
                            <button
                                type="submit"
                                value="Login"
                                class="bg-[#EDB886] hover:bg-[#F7B32B] px-4 py-3 rounded-lg w-full font-bold text-white transition duration-300">
                                Login
                            </button>
                        </form>


                    </div>
                </div>
            </div>
        </div>

        <!-- Forgot Password Modal -->
        <div id="forgot-modal" class="hidden z-50 fixed inset-0 justify-center items-center bg-black/20 backdrop-blur-sm">
            <div class="bg-white/90 shadow-xl backdrop-blur-md p-6 border border-white/20 rounded-xl w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-bold text-gray-800 text-xl">Reset your password</h2>
                    <button onclick="toggleForgotModal(false)" class="text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="forgot_password.php" method="POST">
                    <div class="mb-4">
                        <label class="block mb-2 font-medium text-gray-700 text-sm">Email address</label>
                        <input type="email" name="email" required
                            class="px-3 py-2 border border-gray-300 focus:border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="toggleForgotModal(false)"
                            class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg text-gray-800 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-white transition">
                            Send Reset Link
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bottom-5 left-5 z-30 absolute text-white text-sm">Build By: BSIT - 4102 | Cluster 2</div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Toggle password visibility
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }

        // Toggle forgot password modal
        function toggleForgotModal(show) {
            const modal = document.getElementById("forgot-modal");
            if (show) {
                modal.classList.remove("hidden");
                modal.classList.add("flex");
            } else {
                modal.classList.add("hidden");
                modal.classList.remove("flex");
            }
        }
    </script>

    <?php if (isset($_SESSION["loginError"])): ?>
        <script>
            alert('<?= htmlspecialchars($_SESSION["loginError"], ENT_QUOTES); ?>');
        </script>
    <?php
        unset($_SESSION["loginError"]);
    endif; ?>
</body>

</html>
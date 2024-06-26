<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/includes/classes/file-utils.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/includes/session-handler.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/includes/classes/session-manager.php');
include_once FileUtils::normalizeFilePath(__DIR__ . '/includes/session-exchange.php');
require_once FileUtils::normalizeFilePath(__DIR__ . '/includes/classes/db-connector.php');

SessionManager::checkUserRoleAndRedirect();

$token = $_GET['token'];
$token_hash = hash('sha256', $token);

$connection = DatabaseConnection::connect();

$sql = "SELECT * FROM voter WHERE reset_token_hash = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === NULL) {
    $_SESSION['error_message'] = 'Reset link was not found.';
    header("Location: voter-login.php");
    exit();
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    $_SESSION['error_message'] = 'Reset link has expired.';
    header("Location: voter-login.php");
    exit();
}

?>

<!--Modify the html and css of this. This page is for resetting the password-->
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Fontawesome Link for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Online Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Akronim&family=Anton&family=Aoboshi+One&family=Audiowide&family=Black+Han+Sans&family=Braah+One&family=Bungee+Outline&family=Hammersmith+One&family=Krona+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="styles/dist/landing.css">
    <link rel="stylesheet" href="styles/orgs/<?php echo $org_name; ?>.css">
    <link rel="icon" href="images/resc/ivote-favicon.png" type="image/x-icon">
    <title>Login</title>
</head>

<body class="login-body reset-password-body">

    <nav class="navbar navbar-expand-lg fixed-top" id="login-navbar">
        <div class="container-fluid d-flex justify-content-center align-items-center">
            <a href="landing-page.php"><img src="images/resc/iVOTE-Landing2.png" id="ivote-logo-landing-header" alt="ivote-logo"></a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-6 reset-password-form">
                <form method="post" action="includes/process-reset-password.php" class="needs-validation" id="reset-password-form" novalidate action="BABAGUHIN ITU.php" enctype="multipart/form-data">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">


                    <div class="form-group">
                        <h4 class="reset-password-title" id="<?php echo strtolower($org_name); ?>SignUP">Set your new password</h4>
                        <p class="reset-password-subtitle">Let's keep your account safe! Please choose a strong password for added security.</p>

                        <div class="row mt-5 mb-3 reset-pass">
                            <div class="col-md-8 mb-2 position-relative">
                                <div class="input-group" id="reset-password">
                                    <input type="password" class="form-control reset-password-password" name="password" placeholder="Enter a strong password" id="password" required>
                                    <label for="password" class="new-password  translate-middle-y" id="<?php echo strtolower($org_name); ?>SignUP">NEW PASSWORD</label>
                                    <button class="btn btn-secondary reset-password-password" type="button" id="reset-password-toggle-1">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-8 mb-2 mt-4 position-relative">
                                <div class="input-group" id="reset-password">
                                    <input type="password" class="form-control reset-password-password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
                                    <label for="password_confirmation" class="new-password  translate-middle-y" id="<?php echo strtolower($org_name); ?>SignUP">CONFIRM PASSWORD</label>
                                    <button class="btn btn-secondary reset-password-password" type="button" id="reset-password-toggle-2">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-12 reset-pass">
                            <button class="btn login-sign-in-button mt-4" id="<?php echo strtoupper($org_name); ?>-login-button" type="submit" name="" id="reset-password-submit">Set Password</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>



    <script src="../vendor/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Updated script for password toggle -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword1 = document.querySelector("#reset-password-toggle-1");
            const togglePassword2 = document.querySelector("#reset-password-toggle-2");
            const passwordInput1 = document.querySelector("#password");
            const passwordInput2 = document.querySelector("#password_confirmation");
            const eyeIcon1 = togglePassword1.querySelector("i");
            const eyeIcon2 = togglePassword2.querySelector("i");

            togglePassword1.addEventListener("click", function() {
                const type =
                    passwordInput1.getAttribute("type") === "password" ?
                    "text" :
                    "password";
                passwordInput1.setAttribute("type", type);

                // Toggle eye icon classes
                eyeIcon1.classList.toggle("fa-eye-slash");
                eyeIcon1.classList.toggle("fa-eye");
            });

            togglePassword2.addEventListener("click", function() {
                const type =
                    passwordInput2.getAttribute("type") === "password" ?
                    "text" :
                    "password";
                passwordInput2.setAttribute("type", type);

                // Toggle eye icon classes
                eyeIcon2.classList.toggle("fa-eye-slash");
                eyeIcon2.classList.toggle("fa-eye");
            });
        });
    </script>

</body>

</html>
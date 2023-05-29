<?php

require_once('configs.php');
require_once('database.php');

$isLoggedIn = false;
$userName = '';

if (isset($_COOKIE['sessionId'])) {
    $sessionId = $_COOKIE['sessionId'];
    $configs = getConfigs();
    $mysqli;

    try {
        $mysqli = getMysqli($configs);
    } catch (Exception $e) {
        // log out $e->getMessage()
    }

    $sql = "SELECT userName
FROM users AS u
JOIN sessions AS s ON s.userId = u.id
WHERE s.sessionId = '{$sessionId}'";

    if ($results = $mysqli->query($sql)) {
        $user = $results->fetch_object();

        if ($user) {
            $isLoggedIn = true;
            $userName = $user->userName;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome!</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-3.7.0.min.js"></script>
    <script src="js/index.js"></script>

    <style>
        :root {
            --dark-blue-color: #123456;
            --light-blue-color: #CCFFFF;
        }
        .bg-dark-blue {
            background: var(--dark-blue-color);
        }
        .text-dark-blue {
            color: var(--dark-blue-color);
        }
        .text-light-blue {
            /* color: #EBF4FA; */
            color: var(--light-blue-color);
        }
        .btn-selected {
            opacity: 0.8;
        }
    </style>

    <script>
        let isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false' . "\n"; ?> 
        let userName = <?php echo "'{$userName}'\n"; ?>
    </script>

</head> 
<body>
    <!-- BEGIN login/signup offcanvas -->
    <div id="sidebar" class="offcanvas offcanvas-start">
        <div class="offcanvas-header">
            <h1 class="offcanvas-title">Log in</h1> <!-- or sign in -->
            <button id="closeOffcanvasButton" type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <!-- BEGIN toggle between login/signup -->
            <div class="btn-group btn-group-sm">
                <button id="loginGroupButton" type="button" class="btn btn-primary" disabled>Login</button>
                <button id="signupGroupButton" type="button" class="btn btn-primary">Sign up</button>
            </div> <!-- END toggle between login/signup -->
            <!-- BEGIN login section -->
            <div id="loginSection">
                <form id="loginForm" action="login.php">
                    <div class="form-floating mb-3 mt-3">
                        <input id="email" class="form-control" type="text" name="email" placeholder="Enter email">
                        <label class="form-label" for="email">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input id="password" class="form-control" type="password" name="password" placeholder="Enter password">
                        <label class="form-label" for="password">Password</label>
                    </div>
                    <button id="loginFormButton" class="btn btn-primary" type="submit">Login</button>
                </form>
            </div> <!-- END login section -->
            <!-- BEGIN signup section -->
            <div id="signupSection">
                <p>user name</p>
                <p>email address</p>
                <p>password</p>
                <p>re-enter password</p>
            </div> <!-- END signup section -->
        </div>
    </div>
    <!-- END login/signup offcanvas -->

    <div class="container-fluid bg-dark-blue text-white pt-4 pb-5 text-center">
        <div class="text-end">
            <button id="logoutButton" class="text-light-blue btn btn-link" style="display: none;">log out</button>
            <button id="signinButton" class="text-light-blue btn btn-link" data-bs-toggle="offcanvas" data-bs-target="#sidebar">sign in</button>
        </div>
        <h1 id="bannerHeading">Welcome!</h1>
        <p id="bannerMessage">Login or create a new account!</p>
    </div>

    <div class="container col-m-6 mt-5 text-center">
        <h2 class="text-dark-blue">Lorem ipsum dolor sit amet...</h2>
        <p>...consectetur adipiscing elit. Maecenas vitae nunc fermentum, mollis arcu in, venenatis mi. Morbi vestibulum, felis eget sollicitudin eleifend, ipsum sapien venenatis eros, ut feugiat purus odio nec odio. Aliquam tempor mi ut urna lobortis pellentesque. Integer est ex, volutpat non placerat sit amet, dictum quis elit. Cras in lobortis lectus, eget ornare ante. Fusce odio metus, ultricies a nibh quis, dapibus ultrices est. Sed auctor, libero a dapibus mattis, nulla felis pharetra mauris, eget cursus neque turpis at lacus. Vivamus eu lacus sed orci consectetur facilisis. Proin aliquam sed ex eu congue. Nunc eleifend porta ornare. Fusce nec nisl aliquam nunc semper vehicula. Nam efficitur ultrices urna, aliquet viverra lectus vehicula sit amet. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>
    </div>
</body>
</html>


<!-- loading button diabled:
<button class="btn btn-primary" disabled>
    <span class="spinner-border spinner-border-sm"></span>
    Loading..
  </button> -->
  
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ./auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Newsweek</title>
    <!-- bootstrap stuff -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- local stuff -->
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="favicon.png">
    <script src="script.js"></script>
</head>
<body class="d-flex flex-column vh-100">
<div id="messageBox" style="display:none; font-weight: bold;" class="position-absolute top-0 end-0 text-light p-3 m-3 rounded"></div>
<div id="header" class="container-fluid">
    <div class="row justify-content-between">
        <div id="time" class="col-2 align-self-baseline"></div>
        <h1 class="col-8 align-self-baseline" style="font-size: 88px">Newsweek</h1>
    </div>
</div>
<nav id="nav" class="container-fluid">
    <div class="row justify-content-between p-2">
        <button id="home" onclick="changeContent('pages/home.php')" class="col-3 btn fs-2">Home</button>
        <button id="us" onclick="changeContent('pages/us.php')" class="col-3 btn fs-2">U.S.</button>
        <button id="world" onclick="changeContent('pages/world.php')" class="col-3 btn fs-2">World</button>
        <button id="admin" onclick="changeContent('pages/administration.php')" class="col-3 btn fs-2">Administration</button>
    </div>
</nav>
<div id="content" class="flex-grow-1 w-100 overflow-y-scroll overflow-x-hidden"></div>
</body>
<footer class="container-fluid">
    ©Newsweek - made by Pero Marić
</footer>
</html>
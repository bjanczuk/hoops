<!DOCTYPE html>
<html>
<head>
  <title>Hoops Habit</title>
  <link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
  <link href="https://fonts.googleapis.com/css?family=Rubik+Mono+One|Krona+One" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" type="text/css" href="/Hoops/Loading/style.css">
  <script type="text/javascript" src="./js/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="./js/jquery.autocomplete.min.js"></script>
  <script type="text/javascript" src="./js/names-autocomplete.js"></script>
  <script type="text/javascript" src="/Hoops/Loading/loading.js"></script>
</head>

<script>
  function get_started() {
    document.getElementById("header").style.display = "block";
    document.getElementById("gs_text").style.display = "none";
  }
</script>

<body>
  <?php
    include './functions.php';
    include './Search/get_names.php';
    include './Search/option_form.php';
  ?>

  <div>
    <p class="hoops">Hoops</p>
    <p class="habit">Habit</p>
    <p class='description'>a project by Bart Janczuk</p>
  </div>

  <p id="gs_text" onclick="get_started()">Get Started</p>

</body>

<div id="loading">
  <img id="loading-image" src="/Hoops/Loading/loader.gif" alt="Loading..." />
</div>

</html>
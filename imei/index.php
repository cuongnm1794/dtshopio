<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    .form-container {
      max-width: 600px;
      margin: 0 auto;
    }
    .form-group {
      margin-bottom: 1.5rem;
    }
  </style>
  <title>FMI OFF</title>
</head>
<body>
  <div class="container mt-5 form-container">
    <h2 class="text-center mb-4">FMI OFF with PET</h2>
    <form method="post">
      <div class="form-group">
        <label for="appleID">Apple ID:</label>
        <input type="text" class="form-control" id="appleID" name="appleID" required>
      </div>
      <div class="form-group">
        <label for="PET">PET Token:</label>
        <input type="text" class="form-control" id="PET" name="PET" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block" name="OFF">Submit</button>
    </form>
	<br><br>
    <h2 class="text-center mb-4">FMI OFF via VPS </h2>
    <form method="post">
      <div class="form-group">
        <label for="regAppleID">Apple ID:</label>
        <input type="text" class="form-control" id="regAppleID" name="regAppleID" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block" name="register">Submit</button>
    </form>
	<br><br>

    <div align="center" class="text-center">
	<?php
		error_reporting(0);
		require_once "./class/FMI.php";
		$fmi = new FMI;
		
		if (isset($_POST['register'])) {
			$regAppleID = $_POST['regAppleID'];
			echo $fmi->apiRemove($regAppleID);
		} elseif (isset($_POST['OFF'])) {
			$appleID = $_POST['appleID'];
			$PET = $_POST['PET'];
			echo $fmi->removePETv2($appleID, $PET);
		} 

	?>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>

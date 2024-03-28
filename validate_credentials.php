<?php
// Retrieve the username, password, and user type sent from the JavaScript function
$username = $_POST["username"];
$password = $_POST["password"];
$type = $_POST["type"];

// Establish a connection to the MySQL database
$servername = "localhost"; 
$username_db = "root"; 
$password_db = ""; 
$dbname = "mydatabase"; 

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Construct the SQL query based on user type
if ($type === "admin") {
  $table = "admin_login";
  $welcomePage = "welcome_admin.html";
} 
else if ($type === "user") {
  $table = "user_login";
  $welcomePage = "welcome_user.html";
} 
else {
  // Invalid user type, redirect to error page
  header("Location: JSONuser.html");
  exit();
}

// Prepare the SQL query using prepared statements to prevent SQL injection
$sql = "SELECT * FROM $table WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

// Redirect based on query result
if ($result->num_rows > 0) {
  $stmt->close();
  $conn->close();
  header("Location: $welcomePage");
  exit();
} 
else {
  $stmt->close();
  $conn->close();
  echo "<script>alert('Invalid username or password. Please try again.'); window.location.href = 'index.html';</script>";
  exit();
}
?>

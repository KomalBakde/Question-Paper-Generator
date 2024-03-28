<?php
ob_clean();
require('fpdf.php');

$connection = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connection, 'mydatabase');

// Check if the "Finish and Generate PDF" button is clicked
if (isset($_POST['finish'])) {
  // Check if any questions are selected
  if (isset($_POST['selectedQuestions']) && !empty($_POST['selectedQuestions'])) {

    $selectedQuestions = $_POST['selectedQuestions'];

    $pdf = new FPDF();
    $pdf->AddPage();

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Shri Ramdeobaba College of Engineering and Management', 0, 1, 'C');
    $pdf->Ln(2);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Information Technology (2022-2023)', 0, 1, 'C');
    $pdf->Cell(0, 10, 'IV Semester', 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 10, 'Instructions :', 0, 1, 'L');
    
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 10, '1. Attempt all questions.', 0, 1, 'L');
    $pdf->Cell(0, 10, '2. Make suitable assumptions wherever necessary.', 0, 1, 'L');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Marks', 0, 0, 'C');
    $pdf->Cell(150, 10, 'Question', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);

    foreach ($selectedQuestions as $selectedQuestion) {
      $questionData = explode("|", $selectedQuestion);

      if (count($questionData) >= 2) {
        $question = $questionData[0];
        $marks = $questionData[1];

        $pdf->Cell(40, 10, $marks, 0, 0, 'C');
        $pdf->MultiCell(150, 10, $question, 0, 'L');
      }
    }

    foreach ($selectedQuestions as $selectedQuestion) {
      $questionQuery = "SELECT Question, Marks FROM questions WHERE Question = ?";
      $questionStmt = mysqli_prepare($connection, $questionQuery);
      mysqli_stmt_bind_param($questionStmt, "s", $selectedQuestion);
      mysqli_stmt_execute($questionStmt);
      $questionResult = mysqli_stmt_get_result($questionStmt);
      $questionRow = mysqli_fetch_assoc($questionResult);

      if ($questionRow) {
        $question = $questionRow['Question'];
        $marks = $questionRow['Marks'];

        $pdf->Cell(40, 10, $marks, 0, 0, 'C');
        $pdf->MultiCell(150, 10, $question, 0, 'L');
      }
    }

    ob_clean();
    $pdf->Output('Question_Paper.pdf', 'D');
    exit();
  } 
  else {
    echo '<html>
            <head>
                <title>No Questions</title>
                <style>
                    body {
                        background-color: #f2f2f2;
                        font-family: Arial, sans-serif;
                    }
                    .container {
                        width: 60%;
                        margin: 0 auto;
                        padding: 20px;
                        background-color: #ffffff;
                        border-radius: 5px;
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                    }
                    h2 {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    p {
                        text-align: center;
                        font-size: 18px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h2>No Questions Selected</h2>
                    <p>Please select questions before generating the PDF.</p>
                </div>
            </body>
        </html>';
  }
} 
else {
  // Check if the "Add Question" button is clicked
  if (isset($_GET['subject']) && isset($_GET['unit']) && isset($_GET['marks'])) {
    $subject = $_GET['subject'];
    $unit = $_GET['unit'];
    $marks = $_GET['marks'];

    $query = "SELECT * FROM questions WHERE Subject=? AND Unit=? AND Marks=? ORDER BY RAND() LIMIT 1";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "sss", $subject, $unit, $marks);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_array($result);
      $question = $row['Question'];
      echo $question;
    } 
    else {
      echo "No question found.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Link to Bootstrap CSS -->
</head>
<body>
<div class="container mt-5">

<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'university');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize the variable for editing
$courseToEdit = null;
$editStmt = null; // Initialize to avoid undefined variable error

// Handle edit request
if (isset($_GET['edit'])) {
    $courseId = $_GET['edit'];
    
    // Prepare the statement
    $editStmt = $conn->prepare("SELECT course_id, course_name, budget, building FROM Course WHERE course_id = ?");
    if ($editStmt) {
        $editStmt->bind_param("i", $courseId);
        $editStmt->execute();
        $result = $editStmt->get_result();
        
        // Check if any course was found
        if ($result->num_rows > 0) {
            $courseToEdit = $result->fetch_assoc();
        } else {
            echo '<div class="alert alert-warning" role="alert">No course found with ID: ' . htmlspecialchars($courseId) . '</div>';
        }
        $editStmt->close(); // Close the statement after use
    } else {
        echo '<div class="alert alert-danger" role="alert">Error preparing statement: ' . $conn->error . '</div>';
    }
}

// Handle create or edit request (same as before)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseName = $_POST['course_name'] ?? '';
    $budget = $_POST['budget'] ?? '';
    $building = $_POST['building'] ?? ''; // Get building from dropdown
    $courseId = $_POST['course_id'] ?? null;

    // Use prepared statements to prevent SQL injection
    if ($courseId) {
        // Update existing course
        $stmt = $conn->prepare("UPDATE Course SET course_name = ?, budget = ?, building = ? WHERE course_id = ?");
        $stmt->bind_param("sssi", $courseName, $budget, $building, $courseId);
    } else {
        // Insert new course
        $stmt = $conn->prepare("INSERT INTO Course (course_name, budget, building) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $courseName, $budget, $building);
    }

    if ($stmt->execute()) {
        echo '<div class="alert alert-success" role="alert">Course saved successfully.</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// Read courses
$result = $conn->query("SELECT course_id, course_name, budget, building FROM Course");

// Display courses
echo "<h2>Courses</h2>";
echo "<table class='table table-bordered'>";
echo "<thead><tr><th>ID</th><th>Name</th><th>Budget</th><th>Building</th><th>Actions</th></tr></thead>";
echo "<tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['course_id'] . "</td>";
    echo "<td>" . $row['course_name'] . "</td>";
    echo "<td>" . $row['budget'] . "</td>";
    echo "<td>" . $row['building'] . "</td>";
    echo "<td>
            <a href='?edit=" . $row['course_id'] . "' class='btn btn-warning btn-sm'>Edit</a> 
            <a href='?delete=" . $row['course_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this course?\");'>Delete</a>
          </td>";
    echo "</tr>";
}
echo "</tbody></table>";

// Handle delete
if (isset($_GET['delete'])) {
    $courseId = $_GET['delete'];
    $deleteStmt = $conn->prepare("DELETE FROM Course WHERE course_id = ?");
    $deleteStmt->bind_param("i", $courseId);
    if ($deleteStmt->execute()) {
        echo '<div class="alert alert-success" role="alert">Course deleted successfully.</div>';
        header("Location: course.php"); // Redirect to avoid re-submission
        exit;
    } else {
        echo '<div class="alert alert-danger" role="alert">Error: ' . $deleteStmt->error . '</div>';
    }
    $deleteStmt->close();
}

// Fetch buildings for dropdown
$buildingResult = $conn->query("SELECT DISTINCT building FROM Course");
$buildings = [];
while ($row = $buildingResult->fetch_assoc()) {
    $buildings[] = $row['building'];
}
?>

<!-- Form for adding or editing a course -->
<form method="POST" class="mt-4">
    <input type="hidden" name="course_id" value="<?php echo $courseToEdit['course_id'] ?? ''; ?>">
    <div class="form-group">
        <label for="course_name">Course Name</label>
        <input type="text" name="course_name" class="form-control" placeholder="Course Name" value="<?php echo $courseToEdit['course_name'] ?? ''; ?>" required>
    </div>
    <div class="form-group">
        <label for="budget">Budget</label>
        <input type="text" name="budget" class="form-control" placeholder="Budget" value="<?php echo $courseToEdit['budget'] ?? ''; ?>" required>
    </div>
    <div class="form-group">
        <label for="building">Building</label>
        <select name="building" class="form-control" required>
    <option value="">Select a building</option>
    <?php foreach ($buildings as $building) : ?>
        <option value="<?php echo htmlspecialchars($building); ?>" <?php echo (isset($courseToEdit) && $courseToEdit['building'] == $building) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($building); ?>
        </option>
    <?php endforeach; ?>
</select>

    </div>
    <button type="submit" class="btn btn-primary">
        <?php echo isset($courseToEdit) ? 'Update Course' : 'Add Course'; ?>
    </button>
</form>

<!-- Back button -->
<button onclick="window.location.href='index.php';" class="btn btn-secondary mt-3">Back</button>

<?php
// Close the connection
$conn->close();
?>

</div> <!-- End of container -->
</body>
</html>

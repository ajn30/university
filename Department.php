<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> <!-- Link to Bootstrap 5.3.0 CSS -->
    <style>
        body {
            background-color: #f8f9fa; /* Light background for the body */
        }
        .navbar {
            margin-bottom: 20px; /* Margin for navbar */
        }
        .card-header {
            background-color: #007bff; /* Primary color for card header */
            color: white; /* White text */
        }
        .table th, .table td {
            vertical-align: middle; /* Center-align cell content */
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">University Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="department.php">Departments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="classroom.php">Classrooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">

    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'university');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Initialize variable for department editing
    $departmentToEdit = null;

    // Handle edit request
    if (isset($_GET['edit'])) {
        $departmentId = $_GET['edit'];
        $editStmt = $conn->prepare("SELECT department_id, department_name, budget, building FROM Department WHERE department_id = ?");
        $editStmt->bind_param("i", $departmentId);
        $editStmt->execute();
        $result = $editStmt->get_result();
        $departmentToEdit = $result->fetch_assoc();
        $editStmt->close();
    }

    // Create or Edit
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $departmentName = $_POST['department_name'];
        $budget = $_POST['budget'];
        $building = $_POST['building'];
        $departmentId = $_POST['department_id'] ?? null;

        // Use prepared statements to prevent SQL injection
        if ($departmentId) {
            // Update existing department
            $stmt = $conn->prepare("UPDATE Department SET department_name = ?, budget = ?, building = ? WHERE department_id = ?");
            $stmt->bind_param("sisi", $departmentName, $budget, $building, $departmentId);
        } else {
            // Insert new department
            $stmt = $conn->prepare("INSERT INTO Department (department_name, budget, building) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $departmentName, $budget, $building);
        }
        
        if ($stmt->execute()) {
            echo '<div class="alert alert-success" role="alert">Department saved successfully.</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }

    // Read departments
    $result = $conn->query("SELECT department_id, department_name, budget, building FROM Department");

    // Display departments
    echo "<h2>Departments</h2>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>ID</th><th>Name</th><th>Budget</th><th>Building</th><th>Actions</th></tr></thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['department_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['department_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['budget']) . "</td>";
        echo "<td>" . htmlspecialchars($row['building']) . "</td>";
        echo "<td>
                <a href='?edit=" . $row['department_id'] . "' class='btn btn-warning btn-sm'>Edit</a> 
                <a href='?delete=" . $row['department_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this department?\");'>Delete</a>
              </td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";

    // Delete department
    if (isset($_GET['delete'])) {
        $departmentId = $_GET['delete'];
        $deleteStmt = $conn->prepare("DELETE FROM Department WHERE department_id = ?");
        $deleteStmt->bind_param("i", $departmentId);
        if ($deleteStmt->execute()) {
            echo '<div class="alert alert-success" role="alert">Department deleted successfully.</div>';
            header("Location: department.php"); // Redirect to avoid re-submission
            exit;
        } else {
            echo '<div class="alert alert-danger" role="alert">Error: ' . $deleteStmt->error . '</div>';
        }
        $deleteStmt->close();
    }
    ?>

    <!-- Form for adding or editing a department -->
    <div class="card mt-4">
        <div class="card-header">
            <h5><?php echo $departmentToEdit ? 'Edit Department' : 'Add Department'; ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="department_id" value="<?php echo $departmentToEdit['department_id'] ?? ''; ?>">
                <div class="mb-3">
                    <label for="department_name" class="form-label">Department Name</label>
                    <input type="text" name="department_name" class="form-control" placeholder="Department Name" value="<?php echo htmlspecialchars($departmentToEdit['department_name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="budget" class="form-label">Budget</label>
                    <input type="number" name="budget" class="form-control" placeholder="Budget" value="<?php echo htmlspecialchars($departmentToEdit['budget'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="building" class="form-label">Building</label>
                    <input type="text" name="building" class="form-control" placeholder="Building" value="<?php echo htmlspecialchars($departmentToEdit['building'] ?? ''); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <?php echo $departmentToEdit ? 'Update Department' : 'Add Department'; ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Back button -->
    <button onclick="window.location.href='index.php';" class="btn btn-secondary mt-3">Back</button>

    <?php
    // Close the connection
    $conn->close();
    ?>

    </div> <!-- End of container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Link to Bootstrap JS -->
</body>
</html>

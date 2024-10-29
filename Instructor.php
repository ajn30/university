<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Management</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #ffffff;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
    </style>
</head>
<body>
<div class="d-flex">

    <!-- Sidebar -->
    <nav class="sidebar p-3">
        <h2 class="text-white">University Management</h2>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="classroom.php">Classroom Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="instructor.php">Instructor Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="course.php">Course Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="timeslot.php">Time Slot Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php">Dashboard</a>
            </li>
        </ul>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Instructor Management</h2>

        <?php
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'university');

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Create or Edit
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $firstName = $_POST['first_name'];
            $middleInitial = $_POST['middle_initial'];
            $lastName = $_POST['last_name'];
            $streetNumber = $_POST['street_number'];
            $streetName = $_POST['street_name'];
            $aptNumber = $_POST['apt_number'];
            $city = $_POST['city'];
            $state = $_POST['state'];
            $postalCode = $_POST['postal_code'];
            $dateOfBirth = $_POST['date_of_birth'];
            $departmentId = $_POST['department_id'] ?: null; // Allow department_id to be null
            $instructorId = $_POST['instructor_id'] ?? null; // Get instructor ID if editing

            // Use prepared statements to prevent SQL injection
            if ($instructorId) {
                // Update existing instructor
                $stmt = $conn->prepare("UPDATE Instructor SET first_name=?, middle_initial=?, last_name=?, street_number=?, street_name=?, apt_number=?, city=?, state=?, postal_code=?, date_of_birth=?, department_id=? WHERE instructor_id=?");
                $stmt->bind_param("ssssssssssii", $firstName, $middleInitial, $lastName, $streetNumber, $streetName, $aptNumber, $city, $state, $postalCode, $dateOfBirth, $departmentId, $instructorId);
            } else {
                // Insert new instructor
                $stmt = $conn->prepare("INSERT INTO Instructor (first_name, middle_initial, last_name, street_number, street_name, apt_number, city, state, postal_code, date_of_birth, department_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssssi", $firstName, $middleInitial, $lastName, $streetNumber, $streetName, $aptNumber, $city, $state, $postalCode, $dateOfBirth, $departmentId);
            }

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Instructor saved successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }

        // Read instructors
        $result = $conn->query("SELECT * FROM Instructor");

        // Display instructors
        echo "<h3 class='my-4'>Current Instructors</h3>";
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead><tr><th>ID</th><th>First Name</th><th>Middle Initial</th><th>Last Name</th><th>Address</th><th>City</th><th>State</th><th>Postal Code</th><th>Date of Birth</th><th>Department ID</th><th>Actions</th></tr></thead>";
        echo "<tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['instructor_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['middle_initial']) . "</td>";
            echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['street_number']) . " " . htmlspecialchars($row['street_name']) . " " . htmlspecialchars($row['apt_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['city']) . "</td>";
            echo "<td>" . htmlspecialchars($row['state']) . "</td>";
            echo "<td>" . htmlspecialchars($row['postal_code']) . "</td>";
            echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
            echo "<td>" . htmlspecialchars($row['department_id']) . "</td>";
            echo "<td>
                    <a href='?edit=" . $row['instructor_id'] . "' class='btn btn-warning btn-sm'>Edit</a> 
                    <a href='?delete=" . $row['instructor_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this instructor?\");'>Delete</a>
                  </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";

        // Delete instructor
        if (isset($_GET['delete'])) {
            $instructorId = $_GET['delete'];
            $deleteStmt = $conn->prepare("DELETE FROM Instructor WHERE instructor_id = ?");
            $deleteStmt->bind_param("i", $instructorId);
            if ($deleteStmt->execute()) {
                echo "<div class='alert alert-success'>Instructor deleted successfully.</div>";
                header("Location: instructor.php"); // Redirect to avoid re-submission
                exit;
            } else {
                echo "<div class='alert alert-danger'>Error: " . $deleteStmt->error . "</div>";
            }
            $deleteStmt->close();
        }

        // Edit instructor
        $instructorToEdit = null;
        if (isset($_GET['edit'])) {
            $instructorId = $_GET['edit'];
            $editStmt = $conn->prepare("SELECT * FROM Instructor WHERE instructor_id = ?");
            $editStmt->bind_param("i", $instructorId);
            $editStmt->execute();
            $instructorToEdit = $editStmt->get_result()->fetch_assoc();
            $editStmt->close();
        }
        ?>

        <!-- Form for adding or editing an instructor -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><?php echo $instructorToEdit ? 'Edit Instructor' : 'Add New Instructor'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="instructor_id" value="<?php echo htmlspecialchars($instructorToEdit['instructor_id'] ?? ''); ?>">

                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" class="form-control" placeholder="First Name" value="<?php echo htmlspecialchars($instructorToEdit['first_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="middle_initial">Middle Initial</label>
                        <input type="text" name="middle_initial" class="form-control" placeholder="Middle Initial" value="<?php echo htmlspecialchars($instructorToEdit['middle_initial'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" class="form-control" placeholder="Last Name" value="<?php echo htmlspecialchars($instructorToEdit['last_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="street_number">Street Number</label>
                        <input type="text" name="street_number" class="form-control" placeholder="Street Number" value="<?php echo htmlspecialchars($instructorToEdit['street_number'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="street_name">Street Name</label>
                        <input type="text" name="street_name" class="form-control" placeholder="Street Name" value="<?php echo htmlspecialchars($instructorToEdit['street_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="apt_number">Apt Number</label>
                        <input type="text" name="apt_number" class="form-control" placeholder="Apt Number" value="<?php echo htmlspecialchars($instructorToEdit['apt_number'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" name="city" class="form-control" placeholder="City" value="<?php echo htmlspecialchars($instructorToEdit['city'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" name="state" class="form-control" placeholder="State" value="<?php echo htmlspecialchars($instructorToEdit['state'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control" placeholder="Postal Code" value="<?php echo htmlspecialchars($instructorToEdit['postal_code'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($instructorToEdit['date_of_birth'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="department_id">Department</label>
                        <select name="department_id" class="form-control">
                            <option value="">Select Department</option>
                            <?php
                            // Populate department options
                            $departments = $conn->query("SELECT department_id, department_name FROM Department");
                            while ($dept = $departments->fetch_assoc()) {
                                $selected = (isset($instructorToEdit) && $instructorToEdit['department_id'] == $dept['department_id']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($dept['department_id']) . "' $selected>" . htmlspecialchars($dept['department_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?php echo $instructorToEdit ? 'Update Instructor' : 'Add Instructor'; ?>
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
</div> <!-- End of flex -->
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Slot Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
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
                <a class="nav-link active" href="timeslot.php">Time Slot Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="course.php">Course Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php">Dashboard</a>
            </li>
        </ul>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Time Slot Management</h2>

        <?php
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'university');

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Create or Edit
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $startTime = $_POST['start_time'];
            $endTime = $_POST['end_time'];
            $day = $_POST['day']; // Get the selected day
            $timeSlotId = $_POST['time_slot_id'] ?? null;

            // Use prepared statements to prevent SQL injection
            if ($timeSlotId) {
                // Update existing time slot
                $stmt = $conn->prepare("UPDATE timeslot SET start_time = ?, end_time = ?, day = ? WHERE time_slot_id = ?");
                $stmt->bind_param("ssii", $startTime, $endTime, $day, $timeSlotId);
            } else {
                // Insert new time slot
                $stmt = $conn->prepare("INSERT INTO timeslot (start_time, end_time, day) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $startTime, $endTime, $day);
            }

            if ($stmt->execute()) {
                echo '<div class="alert alert-success" role="alert">Time slot saved successfully.</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Error: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        }

        // Read time slots
        $result = $conn->query("SELECT time_slot_id, start_time, end_time, day FROM timeslot");


        // Check for query error
        if (!$result) {
            die("Query failed: " . $conn->error);
        }

        // Display time slots in a table
        echo "<h3 class='mt-4'>Current Time Slots</h3>";
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>ID</th><th>Start Time</th><th>End Time</th><th>Day</th><th>Actions</th></tr></thead>";
        echo "<tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['time_slot_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['day']) . "</td>"; // Display the day
            echo "<td>
                    <a href='?edit=" . $row['time_slot_id'] . "' class='btn btn-warning btn-sm'>Edit</a> 
                    <a href='?delete=" . $row['time_slot_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this time slot?\");'>Delete</a>
                  </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";

        // Delete time slot
        if (isset($_GET['delete'])) {
            $timeSlotId = $_GET['delete'];
            $deleteStmt = $conn->prepare("DELETE FROM timeslot WHERE time_slot_id = ?");
            $deleteStmt->bind_param("i", $timeSlotId);
            if ($deleteStmt->execute()) {
                echo '<div class="alert alert-success" role="alert">Time slot deleted successfully.</div>';
                header("Location: timeslot.php"); // Redirect to avoid re-submission
                exit;
            } else {
                echo '<div class="alert alert-danger" role="alert">Error: ' . $deleteStmt->error . '</div>';
            }
            $deleteStmt->close();
        }

        // Edit time slot
        $timeSlotToEdit = null;
        if (isset($_GET['edit'])) {
            $timeSlotId = $_GET['edit'];
            $editStmt = $conn->prepare("SELECT * FROM timeslot WHERE time_slot_id = ?");
            $editStmt->bind_param("i", $timeSlotId);
            $editStmt->execute();
            $timeSlotToEdit = $editStmt->get_result()->fetch_assoc();
            $editStmt->close();
        }
        ?>

        <!-- Form for adding or editing a time slot -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><?php echo $timeSlotToEdit ? 'Edit Time Slot' : 'Add New Time Slot'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="time_slot_id" value="<?php echo htmlspecialchars($timeSlotToEdit['time_slot_id'] ?? ''); ?>">
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="time" name="start_time" class="form-control" value="<?php echo htmlspecialchars($timeSlotToEdit['start_time'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="time" name="end_time" class="form-control" value="<?php echo htmlspecialchars($timeSlotToEdit['end_time'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="day">Day</label>
                        <select name="day" class="form-control" required>
                            <option value="">Select Day</option>
                            <option value="Monday" <?php echo (isset($timeSlotToEdit) && $timeSlotToEdit['day'] == 'Monday') ? 'selected' : ''; ?>>Monday</option>
                            <option value="Tuesday" <?php echo (isset($timeSlotToEdit) && $timeSlotToEdit['day'] == 'Tuesday') ? 'selected' : ''; ?>>Tuesday</option>
                            <option value="Wednesday" <?php echo (isset($timeSlotToEdit) && $timeSlotToEdit['day'] == 'Wednesday') ? 'selected' : ''; ?>>Wednesday</option>
                            <option value="Thursday" <?php echo (isset($timeSlotToEdit) && $timeSlotToEdit['day'] == 'Thursday') ? 'selected' : ''; ?>>Thursday</option>
                            <option value="Friday" <?php echo (isset($timeSlotToEdit) && $timeSlotToEdit['day'] == 'Friday') ? 'selected' : ''; ?>>Friday</option>
                            <option value="Saturday" <?php echo (isset($timeSlotToEdit) && $timeSlotToEdit['day'] == 'Saturday') ? 'selected' : ''; ?>>Saturday</option>
                            <option value="Sunday" <?php echo (isset($timeSlotToEdit) && $timeSlotToEdit['day'] == 'Sunday') ? 'selected' : ''; ?>>Sunday</option>
                        </select

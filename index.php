<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <header class="text-center my-4">
            <h1>University Management System</h1>
        </header>
        
        <main>
            <h2 class="text-center">Welcome</h2>
            <p class="text-center">Select an option from the cards below to manage your data.</p>

            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Instructors</h5>
                            <p class="card-text"></p>
                            <a href="instructor.php" class="btn btn-primary">Go to Instructors</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Departments</h5>
                            <p class="card-text"></p>
                            <a href="department.php" class="btn btn-primary">Go to Departments</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Courses</h5>
                            <p class="card-text"></p>
                            <a href="course.php" class="btn btn-primary">Go to Courses</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Classrooms</h5>
                            <p class="card-text">Manage the classrooms available for courses.</p>
                            <a href="classroom.php" class="btn btn-primary">Go to Classrooms</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Time Slots</h5>
                            <p class="card-text">Manage time slots for courses and classes.</p>
                            <a href="timeslot.php" class="btn btn-primary">Go to Time Slots</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Students</h5>
                            <p class="card-text"></p>
                            <a href="student.php" class="btn btn-primary">Go to Students</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="text-center my-4">
            <p>&copy; <?php echo date("Y"); ?> University Management System</p>
        </footer>
    </div>

    <!-- Bootstrap JS and dependencies (Optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

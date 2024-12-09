<?php
// File path to the "database"
$file = __DIR__ . '/data/employees.txt';

// Ensure the data folder exists
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data');
}

// Load all records from the text file
function loadEmployees($file) {
    if (!file_exists($file)) {
        return [];
    }
    $data = file_get_contents($file);
    return $data ? unserialize($data) : [];
}

// Save all records to the text file
function saveEmployees($file, $employees) {
    file_put_contents($file, serialize($employees));
}

// Initialize employees array
$employees = loadEmployees($file);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'create') {
        // Create a new employee record
        $employees[] = [
            'recid' => uniqid(),
            'fullname' => $_POST['fullname'],
            'address' => $_POST['address'],
            'birthdate' => $_POST['birthdate'],
            'age' => (int)$_POST['age'],
            'gender' => $_POST['gender'],
            'civilstat' => $_POST['civilstat'],
            'contactnum' => $_POST['contactnum'],
            'salary' => (float)$_POST['salary'],
            'isactive' => isset($_POST['isactive']) ? 1 : 0,
        ];
        saveEmployees($file, $employees);
    } elseif ($action === 'delete') {
        // Delete an employee record
        $recid = $_POST['recid'];
        $employees = array_filter($employees, fn($e) => $e['recid'] !== $recid);
        saveEmployees($file, $employees);
    } elseif ($action === 'update') {
        // Update an existing employee record
        $recid = $_POST['recid'];
        foreach ($employees as &$employee) {
            if ($employee['recid'] === $recid) {
                $employee['fullname'] = $_POST['fullname'];
                $employee['address'] = $_POST['address'];
                $employee['birthdate'] = $_POST['birthdate'];
                $employee['age'] = (int)$_POST['age'];
                $employee['gender'] = $_POST['gender'];
                $employee['civilstat'] = $_POST['civilstat'];
                $employee['contactnum'] = $_POST['contactnum'];
                $employee['salary'] = (float)$_POST['salary'];
                $employee['isactive'] = isset($_POST['isactive']) ? 1 : 0;
                break;
            }
        }
        saveEmployees($file, $employees);
    }
}

// Find the employee to edit (if any)
$editEmployee = null;
if (isset($_GET['edit'])) {
    $recid = $_GET['edit'];
    foreach ($employees as $employee) {
        if ($employee['recid'] === $recid) {
            $editEmployee = $employee;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD with Vanilla PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
            margin-top: 20px;
        }
        h2 {
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .container {
            width: 50%; /* Adjusted width for center alignment */
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        form {
            padding: 20px;
        }
        input, select, button {
            padding: 10px;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        td a {
            color: #4CAF50;
            text-decoration: none;
        }
        td form button {
            background-color: #f44336;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
        }
        td form button:hover {
            background-color: #e53935;
        }
        label {
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .radio-group {
            margin-top: 10px;
        }
        .radio-group input[type="radio"] {
            margin: 0 10px 5px 0; /* Adjust the margins for spacing */
        }
        .radio-group label {
            display: block; /* This makes each radio button appear on its own line */
            margin-bottom: 5px; /* Adds space between label and button */
        }
        .checkbox-group {
            margin-top: 10px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons form button {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Employee Management</h1>

        <!-- Create Form -->
        <h2>Add New Employee</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label>Full Name: <input type="text" name="fullname" required></label>
            </div>
            <div class="form-group">
                <label>Address: <input type="text" name="address"></label>
            </div>
            <div class="form-group">
                <label>Birthdate: <input type="date" name="birthdate"></label>
            </div>
            <div class="form-group">
                <label>Age: <input type="number" name="age" required></label>
            </div>

            <!-- Gender Radio Buttons -->
            <div class="form-group radio-group">
                <label>Gender:</label>
                <input type="radio" name="gender" value="Male" id="male" required>
                <label for="male">Male</label>
                <input type="radio" name="gender" value="Female" id="female">
                <label for="female">Female</label>
                <input type="radio" name="gender" value="Other" id="other">
                <label for="other">Other</label>
            </div>

            <!-- Civil Status ComboBox -->
            <div class="form-group">
                <label>Civil Status:
                    <select name="civilstat" required>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Separated">Separated</option>
                        <option value="Widowed">Widowed</option>
                    </select>
                </label>
            </div>

            <!-- Contact No. Only Numbers -->
            <div class="form-group">
                <label>Contact No.:
                    <input type="text" name="contactnum" pattern="\d*" title="Only numbers are allowed" required>
                </label>
            </div>

            <div class="form-group">
                <label>Salary: <input type="number" step="0.01" name="salary"></label>
            </div>
            <div class="form-group checkbox-group">
                <label>Active: <input type="checkbox" name="isactive"></label>
            </div>
            <button type="submit">Add Employee</button>
        </form>

        <!-- Edit Employee Form -->
        <?php if ($editEmployee): ?>
            <h2>Edit Employee</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="recid" value="<?= htmlspecialchars($editEmployee['recid']) ?>">
                <div class="form-group">
                    <label>Full Name: <input type="text" name="fullname" value="<?= htmlspecialchars($editEmployee['fullname']) ?>" required></label>
                </div>
                <div class="form-group">
                    <label>Address: <input type="text" name="address" value="<?= htmlspecialchars($editEmployee['address']) ?>"></label>
                </div>
                <div class="form-group">
                    <label>Birthdate: <input type="date" name="birthdate" value="<?= htmlspecialchars($editEmployee['birthdate']) ?>"></label>
                </div>
                <div class="form-group">
                    <label>Age: <input type="number" name="age" value="<?= htmlspecialchars($editEmployee['age']) ?>" required></label>
                </div>

                <!-- Gender Radio Buttons -->
                <div class="form-group radio-group">
                    <label>Gender:</label>
                    <input type="radio" name="gender" value="Male" <?= $editEmployee['gender'] === 'Male' ? 'checked' : '' ?> id="male_edit">
                    <label for="male_edit">Male</label>
                    <input type="radio" name="gender" value="Female" <?= $editEmployee['gender'] === 'Female' ? 'checked' : '' ?> id="female_edit">
                    <label for="female_edit">Female</label>
                    <input type="radio" name="gender" value="Other" <?= $editEmployee['gender'] === 'Other' ? 'checked' : '' ?> id="other_edit">
                    <label for="other_edit">Other</label>
                </div>

                <!-- Civil Status ComboBox -->
                <div class="form-group">
                    <label>Civil Status:
                        <select name="civilstat" required>
                            <option value="Single" <?= $editEmployee['civilstat'] === 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Married" <?= $editEmployee['civilstat'] === 'Married' ? 'selected' : '' ?>>Married</option>
                            <option value="Separated" <?= $editEmployee['civilstat'] === 'Separated' ? 'selected' : '' ?>>Separated</option>
                            <option value="Widowed" <?= $editEmployee['civilstat'] === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                        </select>
                    </label>
                </div>

                <div class="form-group">
                    <label>Contact No.:
                        <input type="text" name="contactnum" value="<?= htmlspecialchars($editEmployee['contactnum']) ?>" pattern="\d*" title="Only numbers are allowed" required>
                    </label>
                </div>

                <div class="form-group">
                    <label>Salary: <input type="number" step="0.01" name="salary" value="<?= htmlspecialchars($editEmployee['salary']) ?>"></label>
                </div>
                <div class="form-group checkbox-group">
                    <label>Active: <input type="checkbox" name="isactive" <?= $editEmployee['isactive'] ? 'checked' : '' ?>></label>
                </div>
                <button type="submit">Update Employee</button>
            </form>
        <?php endif; ?>

        <!-- Employee List -->
        <h2>Employee List</h2>
        <table>
            <tr>
                <th>Full Name</th>
                <th>Address</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Civil Status</th>
                <th>Contact No.</th>
                <th>Salary</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($employees as $employee): ?>
            <tr>
                <td><?= htmlspecialchars($employee['fullname']) ?></td>
                <td><?= htmlspecialchars($employee['address']) ?></td>
                <td><?= htmlspecialchars($employee['age']) ?></td>
                <td><?= htmlspecialchars($employee['gender']) ?></td>
                <td><?= htmlspecialchars($employee['civilstat']) ?></td>
                <td><?= htmlspecialchars($employee['contactnum']) ?></td>
                <td><?= htmlspecialchars($employee['salary']) ?></td>
                <td><?= $employee['isactive'] ? 'Active' : 'Inactive' ?></td>
                <td class="action-buttons">
                    <a href="?edit=<?= $employee['recid'] ?>">Edit</a>
                    <form method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="recid" value="<?= $employee['recid'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>

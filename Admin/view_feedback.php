<?php
session_start();
include '../db.php'; // Ensure this path is correct for your setup

// Handle deletion
if (isset($_POST['delete'])) {
    $feedback_id = $_POST['feedback_id'];

    // Prepare and execute delete statement
    $stmt = $pdo->prepare("DELETE FROM feedback WHERE feedback_id = :feedback_id");
    $stmt->bindParam(':feedback_id', $feedback_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        // Redirect to the same page to see changes
        header("Location: view_feedback.php");
        exit();
    } else {
        echo "Error deleting record.";
    }
}

// Fetch all feedback entries
$stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC");
$feedbackEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            color: #343a40;
            padding: 50px 0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

    .container h1 {
        text-align: center;
        margin-bottom: 20px;
    }
    .container img {
        display: flex;
        width: 40%;
        padding: 3px 8px;
        align-items: center;
        justify-content: center;
        margin-left: 28%;


    }
        table {
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            text-align: left;
            padding: 12px;
        }
        th {
            background-color: #007bff;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .btn {
            border-radius: 30px;
            padding: 12px 24px;
            margin: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-primary {
            background-color: #007bff;
            color: #ffffff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: orange;
            color: #ffffff;
            border: none;
        }
        .btn-danger:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="/SMW/Admin/Images/start.jpeg" alt="">
        <h1>Feedback Entries</h1>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Feedback</th>
                    <th>Submitted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($feedbackEntries): ?>
                    <?php foreach ($feedbackEntries as $entry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry['feedback_id']); ?></td>
                            <td><?php echo htmlspecialchars($entry['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($entry['feedback_text']); ?></td>
                            <td><?php echo htmlspecialchars($entry['created_at']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="feedback_id" value="<?php echo htmlspecialchars($entry['feedback_id']); ?>">
                                    <button type="submit" name="delete" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No feedback entries found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="./admin_dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> Back to Dashboard</a>
    </div>

    <!-- Bootstrap JS and dependencies (Optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
$host = 'localhost';
$dbname = 'attendance_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$today = date('Y-m-d');

// Check if already submitted task today
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = ?");
$stmt->execute([$user_id, $today]);
$today_task = $stmt->fetch();

// Submit work task
if(isset($_POST['submit_task'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $work_timing = $_POST['work_timing'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("INSERT INTO attendance (user_id, date, title, description, work_timing, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $today, $title, $description, $work_timing, $status]);
    
    header("Location: dashboard.php");
    exit();
}

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Infyskill Work Task</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
        }
        .navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .brand {
            font-size: 24px;
            font-weight: bold;
            color: #4a90e2;
        }
        .brand small {
            font-size: 14px;
            color: #666;
        }
        .user {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .user span {
            font-weight: bold;
            color: #333;
        }
        .logout {
            background: #ff4444;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .logout:hover { background: #cc0000; }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .header {
            background: #4a90e2;
            color: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 18px;
            opacity: 0.9;
        }
        .form-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .status {
            display: flex;
            gap: 30px;
            margin-top: 10px;
        }
        .status label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: normal;
        }
        .status input {
            width: auto;
        }
        .btn {
            background: #4a90e2;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
        }
        .btn:hover { background: #357abd; }
        .btn:disabled { background: #ccc; cursor: not-allowed; }
        .task-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 2px solid #4a90e2;
        }
        .present { background: #d4edda; color: #155724; padding: 5px 15px; border-radius: 20px; display: inline-block; }
        .absent { background: #f8d7da; color: #721c24; padding: 5px 15px; border-radius: 20px; display: inline-block; }
        .admin-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            background: #28a745;
            color: white;
            padding: 12px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="brand">Infyskill <small>Work Task</small></div>
        <div class="user">
            <span><?php echo $_SESSION['username']; ?></span>
            <a href="?logout=1" class="logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="header">
            <h1>Infyskill</h1>
            <p>Work Task - <?php echo date('l, F j, Y'); ?></p>
        </div>
        
        <?php if($today_task): ?>
            <div class="task-card">
                <h2 style="margin-bottom: 15px;">Today's Work Task</h2>
                <p><strong>Status:</strong> 
                    <span class="<?php echo $today_task['status']; ?>">
                        <?php echo ucfirst($today_task['status']); ?>
                    </span>
                </p>
                <p><strong>Title:</strong> <?php echo $today_task['title']; ?></p>
                <p><strong>Description:</strong> <?php echo $today_task['description']; ?></p>
                <p><strong>Timing:</strong> <?php echo $today_task['work_timing']; ?></p>
                <p><strong>Submitted:</strong> <?php echo $today_task['marked_at']; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="form-box">
            <h2 style="text-align: center; color: #4a90e2; margin-bottom: 20px;">
                <?php echo $today_task ? 'Task Already Submitted' : 'Submit Today\'s Work Task'; ?>
            </h2>
            
            <?php if(!$today_task): ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Task Title</label>
                        <input type="text" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Task Description</label>
                        <textarea name="description" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Work Timing</label>
                        <input type="text" name="work_timing" placeholder="e.g., 9:00 AM - 4:00 PM" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <div class="status">
                            <label><input type="radio" name="status" value="present" required> Present</label>
                            <label><input type="radio" name="status" value="absent" required> Absent</label>
                        </div>
                    </div>
                    
                    <button type="submit" name="submit_task" class="btn">Submit Work Task</button>
                </form>
            <?php endif; ?>
            
            <?php if($role == 'admin'): ?>
                <a href="admin.php" class="admin-link">Admin Panel - View All Tasks</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
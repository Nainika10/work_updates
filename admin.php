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

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$tasks = $pdo->query("SELECT a.*, u.username FROM attendance a JOIN users u ON a.user_id = u.id ORDER BY a.date DESC")->fetchAll();

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Infyskill Admin</title>
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
        .back {
            background: #4a90e2;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .logout {
            background: #ff4444;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .header {
            background: #4a90e2;
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section h2 {
            color: #4a90e2;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4a90e2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #4a90e2;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover { background: #f5f5f5; }
        .present { color: #28a745; font-weight: bold; }
        .absent { color: #dc3545; font-weight: bold; }
        .admin-badge { background: #ffc107; color: #333; padding: 3px 8px; border-radius: 3px; }
        .user-badge { background: #17a2b8; color: white; padding: 3px 8px; border-radius: 3px; }
        .password { background: #f0f0f0; padding: 5px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="brand">Infyskill <small>Admin</small></div>
        <div class="user">
            <span>Admin: <?php echo $_SESSION['username']; ?></span>
            <a href="dashboard.php" class="back">← Back</a>
            <a href="?logout=1" class="logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="header">
            <h1>Infyskill</h1>
            <p>Work Task - Admin Panel</p>
        </div>
        
        <div class="section">
            <h2>Users</h2>
            <table>
                <tr>
                    <th>ID</th><th>Username</th><th>Email</th><th>Password</th><th>Role</th><th>Registered</th>
                </tr>
                <?php foreach($users as $user): ?>
                <tr>
                    <td>#<?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><span class="password"><?php echo $user['password']; ?></span></td>
                    <td><span class="<?php echo $user['role'] == 'admin' ? 'admin-badge' : 'user-badge'; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                    <td><?php echo $user['created_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <div class="section">
            <h2>All Work Tasks</h2>
            <table>
                <tr>
                    <th>Date</th><th>User</th><th>Title</th><th>Description</th><th>Timing</th><th>Status</th><th>Submitted</th>
                </tr>
                <?php foreach($tasks as $task): ?>
                <tr>
                    <td><?php echo $task['date']; ?></td>
                    <td><?php echo $task['username']; ?></td>
                    <td><?php echo $task['title']; ?></td>
                    <td><?php echo substr($task['description'], 0, 30); ?>...</td>
                    <td><?php echo $task['work_timing']; ?></td>
                    <td class="<?php echo $task['status']; ?>"><?php echo ucfirst($task['status']); ?></td>
                    <td><?php echo $task['marked_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
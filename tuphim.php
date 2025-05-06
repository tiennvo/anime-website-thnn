<?php
session_start();
include_once 'dbconnect.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy thông tin người dùng
$user_id = $_SESSION['user_id'];
$user_query = "SELECT username, email, password FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
if ($stmt === false) {
    die('Error preparing statement: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
if ($user_result === false) {
    die('Error executing query: ' . $stmt->error);
}
$user = $user_result->fetch_assoc();

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Cập nhật thông tin người dùng
    $update_query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    if ($stmt === false) {
        die('Error preparing update statement: ' . $conn->error);
    }
    $stmt->bind_param("ssi", $username, $email, $user_id);
    $stmt->execute();
    if ($stmt->error) {
        die('Error executing update query: ' . $stmt->error);
    }
    $_SESSION['username'] = $username;
    $successMsg = 'Thông tin đã được cập nhật thành công.';
}

// Xử lý thay đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Lấy mật khẩu hiện tại từ cơ sở dữ liệu
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Error preparing statement: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $hashed_password = $user['password'];

    // Kiểm tra mật khẩu hiện tại
    if (!password_verify($current_password, $hashed_password)) {
        $errorMsg = 'Mật khẩu hiện tại không chính xác.';
    } elseif ($new_password !== $confirm_password) {
        $errorMsg = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
    } else {
        // Cập nhật mật khẩu mới
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        if ($stmt === false) {
            die('Error preparing update statement: ' . $conn->error);
        }
        $stmt->bind_param("si", $new_hashed_password, $user_id);
        $stmt->execute();
        if ($stmt->error) {
            die('Error executing update query: ' . $stmt->error);
        }
        $successMsg = 'Mật khẩu đã được thay đổi thành công.';
    }
}

// Lấy danh sách phim đã lưu
$saved_movies_query = "SELECT movies.id, movies.title, movies.image_url FROM saved_movies 
    JOIN movies ON saved_movies.movie_id = movies.id 
    WHERE saved_movies.user_id = ?";
$stmt = $conn->prepare($saved_movies_query);
if ($stmt === false) {
    die('Error preparing saved movies statement: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$saved_movies_result = $stmt->get_result();
if ($saved_movies_result === false) {
    die('Error executing saved movies query: ' . $stmt->error);
}
$saved_movies = [];
if ($saved_movies_result->num_rows > 0) {
    while ($row = $saved_movies_result->fetch_assoc()) {
        $saved_movies[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'image_url' => 'admin/view/img/' . $row['image_url']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tủ phim của bạn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- header -->
    <?php include_once 'header.php'; ?>

    <div class="container my-5">
        <!-- Phim đã lưu -->
        <div id="saved-movies" class="section mb-5">
            <h3 class="guide__title">Phim đã lưu</h3>
            <div class="row">
                <?php if (!empty($saved_movies)): ?>
                    <?php foreach ($saved_movies as $movie): ?>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card">
                                <a href="info.php?id=<?php echo $movie['id']; ?>">
                                    <img src="<?php echo $movie['image_url']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                                    <a href="playvideo.php?id=<?php echo $movie['id']; ?>" class="btn btn-primary">Xem</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có phim nào được lưu.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

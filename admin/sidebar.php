

<div class="sidebar">
    <h4 class="text-white">Quản lý Trang</h4>
    <a href="/animethnn/index.php">Trang chủ</a>
    <a href="/animethnn/admin/index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="/animethnn/admin/manage_movies/index.php"><i class="fas fa-film"></i> Quản lý Phim</a>
    <a href="/animethnn/admin/manage_episodes/index.php"><i class="fas fa-tv"></i> Quản lý Tập</a>
    <a href="/animethnn/admin/manage_genres/index.php"><i class="fas fa-tags"></i> Quản lý Thể loại</a>
    <a href="/animethnn/admin/manage_nation/index.php"><i class="fas fa-globe"></i> Quản lý Quốc Gia Phim</a>
    <a href="/animethnn/admin/manage_users/index.php"><i class="fas fa-users"></i> Quản lý Người dùng</a>
    <a href="/animethnn/admin/manage_comments/index.php"><i class="fas fa-comments"></i> Quản lý Bình luận</a>
    <a href="/animethnn/admin/manage_ratings/index.php"><i class="fas fa-star"></i> Quản lý Đánh giá</a>

    <!-- Thêm nút đăng nhập hoặc đăng xuất -->
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Đăng Nhập</a>
    <?php else: ?>
        <div>
            <p class="text-white">Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <form method="post" style="display:inline;">
                <button type="submit" name="logout" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Đăng Xuất
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

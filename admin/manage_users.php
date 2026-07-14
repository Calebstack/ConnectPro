<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access. Admins only.";
    header("Location: ../users/dashboard.php");
    exit();
}

require_once "../classes/User.php";
$userObj = new User();

// Process deletion self-handler
if (isset($_POST['delete_user'])) {
    $delete_id = (int)$_POST['user_id'];
    
    if ($delete_id === (int)$_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account.";
    } else {
        if ($userObj->deleteUser($delete_id)) {
            $_SESSION['success'] = "User account deleted successfully.";
        } else {
            $_SESSION['error'] = "Could not delete user account.";
        }
    }
    header("Location: manage_users.php");
    exit();
}

$users = $userObj->getAllUsers();
require_once "../partials/header.php";
?>

<main class="container my-5 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="font-weight-bold text-white"><i class="fa-solid fa-users-gear me-2 text-indigo"></i>Moderate Users</h2>
            <p class="text-muted">Manage all clients, providers, and administrator accounts on the platform.</p>
        </div>
        <a href="index.php" class="btn btn-secondary-custom">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <!-- Feedback messages -->
    <div class="row">
        <div class="col-12">
            <?php require_once "../partials/feedback.php"; ?>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass-card">
        <h4 class="font-weight-bold mb-4 text-white">Registered Platform Accounts</h4>
        
        <?php if (count($users) > 0): ?>
            <div class="table-responsive">
                <table class="table table-dark table-hover border-0 align-middle">
                    <thead>
                        <tr class="text-muted border-secondary">
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Role</th>
                            <th scope="col">Joined Date</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr class="border-secondary">
                                <td>
                                    <strong class="text-white"><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                    <?php if ((int)$user['id'] === (int)$_SESSION['user_id']): ?>
                                        <span class="badge bg-secondary ms-1">You</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : '<span class="text-muted">N/A</span>'; ?></td>
                                <td>
                                    <span class="badge text-capitalize <?php 
                                        if ($user['role'] === 'admin') echo 'bg-danger';
                                        elseif ($user['role'] === 'provider') echo 'bg-success';
                                        else echo 'bg-secondary';
                                    ?>">
                                        <?php echo htmlspecialchars($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date("M d, Y", strtotime($user['created_at'])); ?></td>
                                <td class="text-end">
                                    <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>
                                        <form action="manage_users.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This will also remove any related bookings and provider details.');" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1">
                                                <i class="fa-solid fa-trash-can me-1"></i> Delete
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4">No registered accounts found.</p>
        <?php endif; ?>
    </div>
</main>

<?php require_once "../partials/footer.php"; ?>

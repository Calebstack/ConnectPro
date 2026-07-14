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

require_once "../partials/header.php";
require_once "../classes/User.php";
require_once "../classes/ProviderService.php";
require_once "../classes/Booking.php";

$userObj = new User();
$providerService = new ProviderService();
$bookingObj = new Booking();

$users = $userObj->getAllUsers();
$providers = $providerService->getAllProviders();
$bookings = $bookingObj->getAllBookings();

// Compute stats
$totalClients = 0;
$totalProviders = count($providers);
foreach ($users as $u) {
    if ($u['role'] === 'client') {
        $totalClients++;
    }
}
$totalBookings = count($bookings);
?>

<main class="container my-5 flex-grow-1">
    
    <div class="row mb-4">
        <div class="col-md-10">
            <h2 class="font-weight-bold text-white"><i class="fa-solid fa-gauge-high me-2 text-indigo"></i>Admin Control Panel</h2>
            <p class="text-muted">Oversee Connect Pro platform metrics, users, and service bookings.</p>
        </div>
        <div class="col-md-2 text-md-end align-self-center">
            <a href="manage_users.php" class="btn btn-secondary-custom">
                <i class="fa-solid fa-users-gear"></i> Moderate Users
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <div class="row">
        <div class="col-12">
            <?php require_once "../partials/feedback.php"; ?>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="glass-card text-center py-4">
                <i class="fa-solid fa-users text-indigo fs-1 mb-2"></i>
                <h5 class="text-muted">Total Clients</h5>
                <h2 class="text-white font-weight-bold m-0"><?php echo $totalClients; ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card text-center py-4">
                <i class="fa-solid fa-user-tie text-indigo fs-1 mb-2"></i>
                <h5 class="text-muted">Active Providers</h5>
                <h2 class="text-white font-weight-bold m-0"><?php echo $totalProviders; ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card text-center py-4">
                <i class="fa-solid fa-calendar-check text-indigo fs-1 mb-2"></i>
                <h5 class="text-muted">Overall Bookings</h5>
                <h2 class="text-white font-weight-bold m-0"><?php echo $totalBookings; ?></h2>
            </div>
        </div>
    </div>

    <!-- Global Bookings Log -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card">
                <h4 class="font-weight-bold mb-4"><i class="fa-solid fa-list-check me-2 text-indigo"></i>Global Booking Log</h4>
                
                <?php if ($totalBookings > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover border-0 align-middle">
                            <thead>
                                <tr class="text-muted border-secondary">
                                    <th scope="col">Booking ID</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Provider</th>
                                    <th scope="col">Specialty</th>
                                    <th scope="col">Schedule</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Moderate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $b): ?>
                                    <tr class="border-secondary">
                                        <td>#<?php echo $b['id']; ?></td>
                                        <td class="text-white"><?php echo htmlspecialchars($b['client_name']); ?></td>
                                        <td class="text-white"><?php echo htmlspecialchars($b['provider_name']); ?></td>
                                        <td>
                                            <span class="badge bg-indigo-subtle text-indigo" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;"><?php echo htmlspecialchars($b['category']); ?></span>
                                        </td>
                                        <td>
                                            <span class="text-white d-block"><?php echo date("M d, Y", strtotime($b['booking_date'])); ?></span>
                                            <small class="text-muted"><?php echo date("g:i A", strtotime($b['booking_time'])); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge-custom badge-<?php echo $b['status']; ?>">
                                                <?php echo ucfirst($b['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($b['status'] !== 'cancelled'): ?>
                                                <form action="../action/update_booking.php" method="POST" onsubmit="return confirm('Cancel this booking as administrator?');">
                                                    <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <input type="hidden" name="redirect" value="admin/index.php">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-3 rounded-pill">
                                                        Cancel
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
                    <p class="text-muted text-center py-4">No booking events registered in the database.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</main>

<?php require_once "../partials/footer.php"; ?>

<?php
require_once "../partials/header.php";
require_once "../classes/User.php";
require_once "../classes/ProviderService.php";
require_once "../classes/Booking.php";

// Security check - only providers can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    $_SESSION['error'] = "Unauthorized access. Providers only.";
    header("Location: ../users/dashboard.php");
    exit();
}

$providerService = new ProviderService();
$profile = $providerService->getProviderDetailsByUserId($_SESSION['user_id']);
$categories = ProviderService::getCategoriesList();

// If provider hasn't set up profile, redirect to setup
if (!$profile) {
    header("Location: ../users/dashboard.php");
    exit();
}

$bookingObj = new Booking();
$incomingJobs = $bookingObj->getBookingsByProviderId($profile['id']);
$show_edit = isset($_GET['edit']);
?>

<main class="container my-5 flex-grow-1">
    
    <!-- Feedbacks -->
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <?php require_once "../partials/feedback.php"; ?>
        </div>
    </div>

    <!-- Provider Active Dashboard -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="glass-card p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="../uploads/<?php echo htmlspecialchars($profile['profile_image']); ?>" alt="Profile" class="rounded-circle border border-secondary" style="width: 70px; height: 70px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($profile['full_name']); ?>&background=4f46e5&color=fff'">
                    <div>
                        <h2 class="font-weight-bold text-white m-0">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
                        <p class="text-muted m-0">You are listed as a <strong><?php echo htmlspecialchars($profile['category']); ?></strong> in <strong><?php echo htmlspecialchars($profile['location']); ?></strong></p>
                    </div>
                </div>
                <div>
                    <a href="provider_dashboard.php?edit=1" class="btn btn-secondary-custom">
                        <i class="fa-solid fa-user-pen"></i> Edit Profile Details
                    </a>
                </div>
            </div>
        </div>

        <?php if ($show_edit): ?>
            <!-- EDIT PROFILE -->
            <div class="col-12 mb-4">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="font-weight-bold text-white m-0">Edit Profile Details</h4>
                        <a href="provider_dashboard.php" class="btn-close text-white shadow-none"></a>
                    </div>
                    
                    <form action="../action/setup_provider.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label form-label-custom">Specialty / Category</label>
                                <select name="category" class="form-select form-control-custom" required>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $profile['category'] === $cat ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-custom">Hourly Service Fee ($)</label>
                                <input type="number" step="0.01" name="price_per_hour" class="form-control form-control-custom" value="<?php echo $profile['price_per_hour']; ?>" required min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-custom">Years of Experience</label>
                                <input type="number" name="experience_years" class="form-control form-control-custom" value="<?php echo $profile['experience_years']; ?>" required min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-custom">Work Location / City</label>
                                <input type="text" name="location" class="form-control form-control-custom" value="<?php echo htmlspecialchars($profile['location']); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label form-label-custom">Professional Biography / Bio</label>
                                <textarea name="bio" class="form-control form-control-custom" rows="4" required><?php echo htmlspecialchars($profile['bio']); ?></textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="setup_profile" class="btn btn-primary-custom px-4 py-2">
                                Save Changes
                            </button>
                            <a href="provider_dashboard.php" class="btn btn-secondary-custom px-4 py-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-12">
            <div class="glass-card">
                <h4 class="font-weight-bold mb-4"><i class="fa-solid fa-briefcase me-2 text-indigo"></i>Client Service Requests</h4>
                
                <?php if (count($incomingJobs) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover border-0 align-middle">
                            <thead>
                                <tr class="text-muted border-secondary">
                                    <th scope="col">Client Details</th>
                                    <th scope="col">Date & Time</th>
                                    <th scope="col">Client Notes</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($incomingJobs as $job): ?>
                                    <tr class="border-secondary">
                                        <td>
                                            <strong class="text-white d-block"><?php echo htmlspecialchars($job['client_name']); ?></strong>
                                            <small class="text-muted"><i class="fa-solid fa-phone me-1"></i><?php echo htmlspecialchars($job['client_phone']); ?></small>
                                            <small class="text-muted d-block"><i class="fa-solid fa-envelope me-1"></i><?php echo htmlspecialchars($job['client_email']); ?></small>
                                        </td>
                                        <td>
                                            <span class="text-white d-block"><?php echo date("M d, Y", strtotime($job['booking_date'])); ?></span>
                                            <span class="text-muted small"><?php echo date("g:i A", strtotime($job['booking_time'])); ?></span>
                                        </td>
                                        <td>
                                            <span class="text-muted small d-inline-block text-wrap" style="max-width: 250px;">
                                                <?php echo !empty($job['notes']) ? htmlspecialchars($job['notes']) : 'No notes provided'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge-custom badge-<?php echo $job['status']; ?>">
                                                <?php echo ucfirst($job['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($job['status'] === 'pending'): ?>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="../action/update_booking.php?booking_id=<?php echo $job['id']; ?>&status=approved&redirect=users/provider_dashboard.php" class="btn btn-success btn-sm rounded-pill px-3 py-1">
                                                        Approve
                                                    </a>
                                                    <a href="../action/update_booking.php?booking_id=<?php echo $job['id']; ?>&status=cancelled&redirect=users/provider_dashboard.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1" onclick="return confirm('Reject booking?');">
                                                        Reject
                                                    </a>
                                                </div>
                                            <?php elseif ($job['status'] === 'approved'): ?>
                                                <a href="../action/update_booking.php?booking_id=<?php echo $job['id']; ?>&status=cancelled&redirect=users/provider_dashboard.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1" onclick="return confirm('Cancel approved booking?');">
                                                    Cancel Job
                                                </a>
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
                    <div class="text-center py-5">
                        <i class="fa-solid fa-folder-open text-muted fs-1 mb-3"></i>
                        <h5 class="text-white">No Requests Yet</h5>
                        <p class="text-muted">You don't have any incoming booking requests at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once "../partials/footer.php"; ?>
<?php
require_once "../partials/header.php";
require_once "../classes/User.php";
require_once "../classes/ProviderService.php";
require_once "../classes/Booking.php";

$is_logged_in = isset($_SESSION['user_id']);
$role = $is_logged_in ? $_SESSION['role'] : '';

// Redirect if admin is accessing
if ($is_logged_in && $role === 'admin') {
    header("Location: ../admin/index.php");
    exit();
}

$categories = ProviderService::getCategoriesList();
?>

<main class="container my-5 flex-grow-1">
    
    <!-- Feedbacks -->
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <?php require_once "../partials/feedback.php"; ?>
        </div>
    </div>

    <?php if (!$is_logged_in): 
        $show_register = isset($_GET['register']);
    ?>
        <!-- AUTHENTICATION FORM -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="glass-card shadow">
                    <div class="d-flex justify-content-center gap-4 mb-4 border-bottom border-secondary pb-3">
                        <a href="dashboard.php" class="fs-5 text-decoration-none font-weight-bold <?php echo !$show_register ? 'text-white border-bottom border-indigo border-2 pb-2' : 'text-muted'; ?>">Sign In</a>
                        <a href="dashboard.php?register=1" class="fs-5 text-decoration-none font-weight-bold <?php echo $show_register ? 'text-white border-bottom border-indigo border-2 pb-2' : 'text-muted'; ?>">Create Account</a>
                    </div>

                    <?php if (!$show_register): ?>
                        <form action="../action/login.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label form-label-custom">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="name@example.com" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label form-label-custom">Password</label>
                                <input type="password" name="password" class="form-control form-control-custom" placeholder="Password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary-custom w-100 py-3">
                                Sign In <i class="fa-solid fa-right-to-bracket ms-1"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <form action="../action/register.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label form-label-custom">Full Name</label>
                                <input type="text" name="full_name" class="form-control form-control-custom" placeholder="John Doe" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-custom">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="john@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-custom">Phone Number</label>
                                <input type="tel" name="phone" class="form-control form-control-custom" placeholder="e.g. 08012345678" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label form-label-custom">Register As</label>
                                <select name="role" class="form-select form-control-custom" required>
                                    <option value="client">Client (Hire Professionals)</option>
                                    <option value="provider">Service Provider (Offer Services)</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label form-label-custom">Choose Password</label>
                                <input type="password" name="password" class="form-control form-control-custom" placeholder="Create a password" required>
                            </div>
                            <button type="submit" name="register" class="btn btn-primary-custom w-100 py-3">
                                Complete Registration <i class="fa-solid fa-user-plus ms-1"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <?php elseif ($role === 'client'): 
        $bookingObj = new Booking();
        $myBookings = $bookingObj->getBookingsByClientId($_SESSION['user_id']);
    ?>
        <!-- CLIENT DASHBOARD -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="glass-card p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="font-weight-bold text-white m-0">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
                        <p class="text-muted m-0">Track and manage your service requests below.</p>
                    </div>
                    <a href="search.php" class="btn btn-primary-custom">
                        <i class="fa-solid fa-magnifying-glass"></i> Find New Service
                    </a>
                </div>
            </div>

            <div class="col-12">
                <div class="glass-card">
                    <h4 class="font-weight-bold mb-4"><i class="fa-solid fa-calendar-days me-2 text-indigo"></i>My Booking History</h4>
                    
                    <?php if (count($myBookings) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover border-0 align-middle">
                                <thead>
                                    <tr class="text-muted border-secondary">
                                        <th scope="col">Provider</th>
                                        <th scope="col">Service</th>
                                        <th scope="col">Date & Time</th>
                                        <th scope="col">Hourly Price</th>
                                        <th scope="col">Notes</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myBookings as $b): ?>
                                        <tr class="border-secondary">
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <strong class="text-white"><?php echo htmlspecialchars($b['provider_name']); ?></strong>
                                                </div>
                                                <small class="text-muted"><?php echo htmlspecialchars($b['provider_phone']); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-indigo-subtle text-indigo" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;"><?php echo htmlspecialchars($b['category']); ?></span>
                                            </td>
                                            <td>
                                                <span class="text-white d-block"><?php echo date("M d, Y", strtotime($b['booking_date'])); ?></span>
                                                <span class="text-muted small"><?php echo date("g:i A", strtotime($b['booking_time'])); ?></span>
                                            </td>
                                            <td>$<?php echo number_format($b['price_per_hour'], 2); ?>/hr</td>
                                            <td>
                                                <span class="text-muted small d-inline-block text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($b['notes']); ?>">
                                                    <?php echo !empty($b['notes']) ? htmlspecialchars($b['notes']) : 'No notes provided'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-custom badge-<?php echo $b['status']; ?>">
                                                    <?php echo ucfirst($b['status']); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <?php if ($b['status'] === 'pending' || $b['status'] === 'approved'): ?>
                                                    <form action="../action/update_booking.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                                        <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <input type="hidden" name="redirect" value="users/dashboard.php">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1">
                                                            Cancel Booking
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
                        <div class="text-center py-5">
                            <i class="fa-solid fa-calendar-xmark text-muted fs-1 mb-3"></i>
                            <h5 class="text-white">No Bookings Yet</h5>
                            <p class="text-muted">You haven't requested any services yet.</p>
                            <a href="search.php" class="btn btn-primary-custom py-2 px-4 mt-2">Find a Pro</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <?php elseif ($role === 'provider'): 
        $providerService = new ProviderService();
        $profile = $providerService->getProviderDetailsByUserId($_SESSION['user_id']);
        
        if (!$profile):
    ?>
            <!-- PROVIDER SETUP PROFILE -->
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8">
                    <div class="glass-card">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-user-gear text-indigo fs-1 mb-2"></i>
                            <h3 class="font-weight-bold text-white">Setup Your Provider Profile</h3>
                            <p class="text-muted">Complete your details so clients can find and book your services.</p>
                        </div>
                        
                        <form action="../action/setup_provider.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label form-label-custom">Specialty / Category</label>
                                    <select name="category" class="form-select form-control-custom" required>
                                        <option value="" disabled selected>Select specialty...</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-custom">Hourly Service Fee ($)</label>
                                    <input type="number" step="0.01" name="price_per_hour" class="form-control form-control-custom" placeholder="e.g. 25.00" required min="1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-custom">Years of Experience</label>
                                    <input type="number" name="experience_years" class="form-control form-control-custom" placeholder="e.g. 5" required min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-custom">Work Location / City</label>
                                    <input type="text" name="location" class="form-control form-control-custom" placeholder="e.g. Lekki, Lagos" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label form-label-custom">Professional Biography / Bio</label>
                                    <textarea name="bio" class="form-control form-control-custom" rows="4" placeholder="Introduce yourself, your skillsets, and availability..." required></textarea>
                                </div>
                            </div>
                            
                            <button type="submit" name="setup_profile" class="btn btn-primary-custom w-100 py-3 mt-4">
                                Publish Provider Profile <i class="fa-solid fa-paper-plane ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        <?php else: 
            $bookingObj = new Booking();
            $incomingJobs = $bookingObj->getBookingsByProviderId($profile['id']);
            $show_edit = isset($_GET['edit']);
        ?>
            <!-- PROVIDER ACTIVE DASHBOARD -->
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
                            <a href="dashboard.php?edit=1" class="btn btn-secondary-custom">
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
                                <a href="dashboard.php" class="btn-close text-white shadow-none"></a>
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
                                    <a href="dashboard.php" class="btn btn-secondary-custom px-4 py-2">Cancel</a>
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
                                                            <a href="../action/update_booking.php?booking_id=<?php echo $job['id']; ?>&status=approved&redirect=users/dashboard.php" class="btn btn-success btn-sm rounded-pill px-3 py-1">
                                                                Approve
                                                            </a>
                                                            <a href="../action/update_booking.php?booking_id=<?php echo $job['id']; ?>&status=cancelled&redirect=users/dashboard.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1" onclick="return confirm('Reject booking?');">
                                                                Reject
                                                            </a>
                                                        </div>
                                                    <?php elseif ($job['status'] === 'approved'): ?>
                                                        <a href="../action/update_booking.php?booking_id=<?php echo $job['id']; ?>&status=cancelled&redirect=users/dashboard.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1" onclick="return confirm('Cancel approved booking?');">
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
        <?php endif; ?>

    <?php endif; ?>
</main>

<?php require_once "../partials/footer.php"; ?>

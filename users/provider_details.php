<?php
require_once "../partials/header.php";
require_once "../classes/ProviderService.php";

$providerService = new ProviderService();
$provider_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$provider = $providerService->getProviderDetailsById($provider_id);

if (!$provider) {
    $_SESSION['error'] = "Provider not found.";
    header("Location: search.php");
    exit();
}
?>

<main class="container my-5">
    <div class="row">
        <!-- Provider details card -->
        <div class="col-lg-8 mb-4">
            <div class="glass-card mb-4">
                <div class="d-flex flex-wrap align-items-center gap-4 mb-4">
                    <img src="../uploads/<?php echo htmlspecialchars($provider['profile_image']); ?>" alt="<?php echo htmlspecialchars($provider['full_name']); ?>" class="rounded-circle border border-secondary" style="width: 120px; height: 120px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($provider['full_name']); ?>&background=4f46e5&color=fff'">
                    <div>
                        <span class="badge bg-indigo-subtle text-indigo mb-2" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; font-size: 0.9rem;"><?php echo htmlspecialchars($provider['category']); ?></span>
                        <h2 class="font-weight-bold text-white m-0"><?php echo htmlspecialchars($provider['full_name']); ?></h2>
                        <p class="text-muted m-0 mt-1"><i class="fa-solid fa-location-dot me-2 text-indigo"></i><?php echo htmlspecialchars($provider['location']); ?></p>
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <h4 class="font-weight-bold mb-3 text-white">About the Professional</h4>
                <p class="text-muted leading-relaxed"><?php echo nl2br(htmlspecialchars($provider['bio'])); ?></p>
                
                <div class="row g-4 mt-2">
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 rounded bg-secondary-subtle border border-secondary text-center" style="background: rgba(255,255,255,0.02);">
                            <span class="text-black d-block small">Experience</span>
                            <strong class="text-black fs-5"><?php echo $provider['experience_years']; ?> Years</strong>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 rounded bg-secondary-subtle border border-secondary text-center" style="background: rgba(255,255,255,0.02);">
                            <span class="text-black d-block small">Service Fee</span>
                            <strong class="text-black fs-5">$<?php echo number_format($provider['price_per_hour'], 2); ?>/hr</strong>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 rounded bg-secondary-subtle border border-secondary text-center" style="background: rgba(255,255,255,0.02);">
                            <span class="text-black d-block small">Availability</span>
                            <strong class="text-black fs-5">Mon - Sat</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking sidebar form -->
        <div class="col-lg-4">
            <div class="glass-card">
                <h4 class="font-weight-bold mb-4"><i class="fa-solid fa-calendar-check me-2 text-indigo"></i>Book Service</h4>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'client'): ?>
                        <form action="../action/book.php" method="POST">
                            <input type="hidden" name="provider_id" value="<?php echo $provider['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label form-label-custom">Preferred Date</label>
                                <input type="date" name="booking_date" class="form-control form-control-custom" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label form-label-custom">Preferred Time</label>
                                <input type="time" name="booking_time" class="form-control form-control-custom" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label form-label-custom">Notes / Special Instructions</label>
                                <textarea name="notes" class="form-control form-control-custom" rows="3" placeholder="Briefly describe what you need done..."></textarea>
                            </div>

                            <button type="submit" name="book_service" class="btn btn-primary-custom w-100 py-3">
                                Request Booking
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning border-0" role="alert" style="background-color: rgba(245, 158, 11, 0.15); color: var(--warning);">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Only accounts registered as <strong>Client</strong> can request bookings.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning border-0 text-center py-4" role="alert" style="background-color: rgba(245, 158, 11, 0.15); color: var(--warning); border-radius: 12px;">
                        <i class="fa-solid fa-lock fs-3 mb-2 d-block"></i>
                        Please <strong>Sign In</strong> or <strong>Register</strong> to schedule a booking with this professional.
                        <div class="mt-3">
                            <a href="dashboard.php" class="btn btn-primary-custom py-2 px-4 w-100">Sign In Now</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once "../partials/footer.php"; ?>

<?php
require_once "partials/header.php";
require_once "classes/ProviderService.php";

$providerService = new ProviderService();
$categories = ProviderService::getCategoriesList();

// Get top 3 providers for featured section
$featuredProviders = array_slice($providerService->searchProviders(), 0, 3);
?>

<main class="container my-5">
    <!-- Hero Section -->
    <section class="hero-section glass-card mb-5">
        <h1 class="hero-title">Find & Book Trusted <span>Local Professionals</span></h1>
        <p class="hero-subtitle">Hire experts for cleaning, tutoring, photography, plumbing, beauty services, and more. Transparent pricing, instant confirmation.</p>
        
        <form action="users/search.php" method="GET" class="row justify-content-center g-2 max-width-600 mx-auto">
            <div class="col-sm-8">
                <input type="text" name="query" class="form-control form-control-custom w-100 py-3" placeholder="What service are you looking for? (e.g., haircut, leak, math)...">
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-primary-custom w-100 py-3">
                    <i class="fa-solid fa-magnifying-glass"></i> Find Pro
                </button>
            </div>
        </form>
    </section>

    <!-- Categories Grid -->
    <section class="mb-5">
        <h2 class="text-center font-weight-bold mb-4">Popular Categories</h2>
        <div class="row g-4 justify-content-center">
            <?php
            // Assign font-awesome icons matching category names
            $icons = [
                'Barber' => 'fa-scissors',
                'Cleaner' => 'fa-broom',
                'Photographer' => 'fa-camera',
                'Plumber' => 'fa-faucet-drip',
                'Makeup Artist' => 'fa-wand-magic-sparkles',
                'Tutor' => 'fa-graduation-cap',
                'Electrician' => 'fa-bolt',
                'Mechanic' => 'fa-screwdriver-wrench'
            ];
            
            foreach ($categories as $category): 
                $icon = isset($icons[$category]) ? $icons[$category] : 'fa-handshake';
            ?>
                <div class="col-6 col-md-3">
                    <a href="users/search.php?category=<?php echo urlencode($category); ?>" class="category-card text-decoration-none">
                        <div class="category-icon">
                            <i class="fa-solid <?php echo $icon; ?>"></i>
                        </div>
                        <h5 class="m-0 font-weight-semibold"><?php echo htmlspecialchars($category); ?></h5>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Featured Providers -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold m-0">Featured Service Providers</h2>
            <a href="users/search.php" class="nav-link-custom py-2 px-3">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php if (count($featuredProviders) > 0): ?>
                <?php foreach ($featuredProviders as $provider): ?>
                    <div class="col-md-4">
                        <div class="glass-card h-100 d-flex flex-column justify-content-between p-4">
                            <div>
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img src="uploads/<?php echo htmlspecialchars($provider['profile_image']); ?>" alt="<?php echo htmlspecialchars($provider['full_name']); ?>" class="rounded-circle border border-secondary" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($provider['full_name']); ?>&background=4f46e5&color=fff'">
                                    <div>
                                        <h5 class="m-0 font-weight-bold"><?php echo htmlspecialchars($provider['full_name']); ?></h5>
                                        <span class="badge bg-indigo-subtle text-indigo" style="font-size: 0.8rem; background: rgba(99, 102, 241, 0.15); color: #818cf8;"><?php echo htmlspecialchars($provider['category']); ?></span>
                                    </div>
                                </div>
                                <p class="text-muted small line-clamp-3 mb-3"><?php echo htmlspecialchars($provider['bio']); ?></p>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center border-top border-secondary pt-3 mt-3">
                                    <div>
                                        <span class="text-muted small">Hourly Rate</span>
                                        <h5 class="m-0 text-white font-weight-bold">$<?php echo number_format($provider['price_per_hour'], 2); ?></h5>
                                    </div>
                                    <a href="users/provider_details.php?id=<?php echo $provider['id']; ?>" class="btn btn-primary-custom py-2">
                                        View Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">No featured providers listed yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once "partials/footer.php"; ?>

<?php
require_once "../partials/header.php";
require_once "../classes/ProviderService.php";

$providerService = new ProviderService();
$categories = ProviderService::getCategoriesList();

// 1. GET FILTER PARAMETERS FROM URL
// 'category' represents the selected category filter dropdown (e.g. 'Barber').
// 'query' represents the search keyword text input (e.g. name, location, or bio).
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

/* 
 * 2. EXPLAINING HOW THE SEARCH WORKS:
 * The ProviderService->searchProviders($category, $keyword) method executes:
 * - A SQL query: SELECT p.*, u.full_name, u.email FROM providers p INNER JOIN users u ON p.user_id = u.id
 * - If category filter is chosen, it appends: "AND p.category = ?" and binds the category parameter.
 * - If keyword search query is typed, it appends: "AND (u.full_name LIKE ? OR p.bio LIKE ? OR p.location LIKE ?)"
 *   The query is wrapped in wildcards (e.g. "%keyword%") to find any partial match in name, bio, or location.
 * - Returns matching rows ordered by experience years descending.
 */
$providers = $providerService->searchProviders($selectedCategory, $searchQuery);
?>

<main class="container my-5">
    <div class="row">
        <!-- Sidebar Filter Form -->
        <div class="col-lg-3 mb-4">
            <div class="glass-card p-4">
                <h4 class="font-weight-bold mb-4"><i class="fa-solid fa-sliders me-2 text-indigo"></i>Filter Pro</h4>
                
                <!-- This form submits GET requests back to search.php, appending filters to the URL query string -->
                <form action="search.php" method="GET">
                    <!-- Text Keyword Search input -->
                    <div class="mb-4">
                        <label class="form-label form-label-custom">Keyword Search</label>
                        <input type="text" name="query" class="form-control form-control-custom" placeholder="Name, bio, location..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <small class="text-muted d-block mt-1">Searches professional names, bio statements, or work locations.</small>
                    </div>

                    <!-- Specialty Category Dropdown -->
                    <div class="mb-4">
                        <label class="form-label form-label-custom">Category</label>
                        <select name="category" class="form-select form-control-custom">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $selectedCategory === $cat ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-3">
                        Apply Filters
                    </button>
                    
                    <a href="search.php" class="btn btn-secondary-custom w-100 py-3 mt-2">
                        Reset Filters
                    </a>
                </form>
            </div>
        </div>

        <!-- Search Results Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-weight-bold m-0">
                    <?php if ($selectedCategory): ?>
                        <?php echo htmlspecialchars($selectedCategory); ?>s
                    <?php else: ?>
                        All Professional Services
                    <?php endif; ?>
                </h3>
                <span class="text-muted"><?php echo count($providers); ?> experts found</span>
            </div>

            <div class="row g-4">
                <?php if (count($providers) > 0): ?>
                    <?php foreach ($providers as $provider): ?>
                        <div class="col-12">
                            <div class="glass-card p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center text-md-start mb-3 mb-md-0">
                                        <img src="../uploads/<?php echo htmlspecialchars($provider['profile_image']); ?>" alt="<?php echo htmlspecialchars($provider['full_name']); ?>" class="rounded-circle border border-secondary img-fluid" style="width: 100px; height: 100px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($provider['full_name']); ?>&background=4f46e5&color=fff'">
                                    </div>
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                            <h4 class="m-0 font-weight-bold"><?php echo htmlspecialchars($provider['full_name']); ?></h4>
                                            <span class="badge bg-indigo-subtle text-indigo" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;"><?php echo htmlspecialchars($provider['category']); ?></span>
                                        </div>
                                        <p class="text-muted small mb-2"><i class="fa-solid fa-location-dot me-2"></i><?php echo htmlspecialchars($provider['location']); ?> &bull; <i class="fa-solid fa-briefcase me-2"></i><?php echo $provider['experience_years']; ?> years exp</p>
                                        <p class="text-muted m-0 small"><?php echo htmlspecialchars(substr($provider['bio'], 0, 180)); ?>...</p>
                                    </div>
                                    <div class="col-md-4 text-center text-md-end border-start border-secondary ps-md-4">
                                        <span class="text-muted small d-block">Hourly Rate</span>
                                        <h3 class="text-white font-weight-bold mb-3">$<?php echo number_format($provider['price_per_hour'], 2); ?></h3>
                                        <a href="provider_details.php?id=<?php echo $provider['id']; ?>" class="btn btn-primary-custom w-100">
                                            View & Book Pro
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="glass-card py-5">
                            <i class="fa-solid fa-user-slash fs-1 text-muted mb-3"></i>
                            <h4 class="text-white">No Providers Found</h4>
                            <p class="text-muted">Try adjusting your filters or searching for another keyword.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once "../partials/footer.php"; ?>

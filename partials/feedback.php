<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: rgba(16, 185, 129, 0.15); color: var(--success); border-left: 4px solid var(--success) !important; border-radius: 12px;">
        <i class="fa-solid fa-circle-check me-2"></i>
        <?php 
            echo htmlspecialchars($_SESSION['success']); 
            unset($_SESSION['success']);
        ?>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: rgba(239, 68, 68, 0.15); color: var(--danger); border-left: 4px solid var(--danger) !important; border-radius: 12px;">
        <i class="fa-solid fa-circle-exclamation me-2"></i>
        <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']);
        ?>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

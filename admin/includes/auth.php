<?php

declare(strict_types=1);

function admin_is_logged_in(): bool
{
    return isset($_SESSION['admin_id']) && (int) $_SESSION['admin_id'] > 0;
}

// Alias for upload handlers
function is_admin_logged_in(): bool
{
    return admin_is_logged_in();
}

function require_admin_login(): void
{
    if (!admin_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

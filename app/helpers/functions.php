<?php
function isActive($pageName) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    if ($currentPage === $pageName) {
        return 'active';
    }
    return '';
}
?>
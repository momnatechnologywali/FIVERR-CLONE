<?php
// logout.php - Simple logout with JS redirection.
session_start();
session_destroy();
?>
<script>
    window.location.href = 'index.php';
</script>

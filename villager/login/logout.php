<?php
session_start();
session_destroy();

// Redirect with a script to clear login state
echo "<script>
    sessionStorage.removeItem('isLoggedIn');
    window.location.href = '../Main/index.html';
</script>";
exit();
?>
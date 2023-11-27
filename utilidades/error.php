<?php
function mostrarError($error) {
    if ($error != '') {
        echo '<div class="error"><p>' . $error . '</p></div>';
    }

    if (isset($_SESSION['error'])) {
        echo '<div class="error"><p>' . $_SESSION['error'] . '</p></div>';
        unset($_SESSION['error']);
    }
}
?>
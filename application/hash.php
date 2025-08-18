<?php
$plain = "admin123";
$hash = '$2y$10$tvO3zCxQ4o/vno79rc7esO9iOOpDyjA8QegixRVCDb0OcvLNXL/BW';

if (password_verify($plain, $hash)) {
    echo "✅ Le mot de passe est correct !";
} else {
    echo "❌ Le mot de passe est incorrect !";
}

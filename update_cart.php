<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'], $_POST['quantity'])) {
    $productId = $_POST['id'];
    $newQuantity = intval($_POST['quantity']);

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] = $newQuantity;
                break;
            }
        }
    }
}
?>


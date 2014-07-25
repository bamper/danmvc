<?php 
/* @var $user User */ 
$baseUrl = isset($baseUrl) ? $baseUrl : '';
$isLoggedIn = isset($isLoggedIn) ? $isLoggedIn : false;
?>
<!DOCTYPE html>
<html>
    <head>

    </head>
    <body>

        <nav>
            <ul><a href="<?= $baseUrl ?>">Home</a></ul>
            <?php if ($isLoggedIn): ?>
                <?php if($user->isInRole(Role::ROLE_AUTHOR)): ?>
                <ul><a href="<?= $baseUrl ?>/createPost">New Post</a></ul>
                <?php endif;?>
            
                <ul><a href="<?= $baseUrl ?>/logout">Logout</a></ul>
            <?php else: ?>
                <ul><a href="<?= $baseUrl ?>/login">Login</a></ul>
            <?php endif; ?>

        </nav>
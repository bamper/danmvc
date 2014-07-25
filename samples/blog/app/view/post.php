<?php /* @var $post BlogPost */ ?>
<?php /* @var $user User */ ?>
<?php $this->render('includes/header') ?>
<h1><?= $post->getTitle() ?></h1>

<?php if ($isLoggedIn && $user->isInRole(Role::ROLE_ADMIN)): ?>
    <a href="<?= $baseUrl ?>/posts/<?= $post->getId() ?>/delete">Delete</a>
    <a href="<?= $baseUrl ?>/posts/<?= $post->getId() ?>/edit">Edit</a>
<?php endif; ?>

<p>Posted on <?= $post->getPubDate()->format(DateTime::RFC1036) ?></p>

<p><?= $post->getContent() ?></p>

<?php if (count($post->getCategories()) > 0): ?>
    <p>Under:
    <ul class="categoryList">
        <?php foreach ($post->getCategories() as $cat): ?>
            <?php /* @var $cat Category */ ?>
            <li><a href="<?= $baseUrl ?>/category/<?= $cat->getName() ?>"><?= $cat->getName() ?></a></li>
        <?php endforeach; ?>
    </ul>
    </p>
<?php endif; ?>

<?php $this->render('includes/footer') ?>
<?php $this->render('includes/header') ?>
<h1>Home</h1>



<div>
    <?php foreach ($blogPosts as $post): ?>
        <?php /* @var $post BlogPost */ ?>
        <div class="post">
            <div class="title"><a href="<?= $baseUrl ?>/posts/<?= $post->getId() ?>" ><?= $post->getTitle() ?></a></div>
            <div class="author">by: <?= $post->getAuthor()->getFirstName() ?> <?= $post->getAuthor()->getLastName() ?></div>
            <div class="date">Posted on: <?= $post->getPubDate()->format("Y-m-d H:i:s") ?></div>
            <div class="content">
                <p><?= $post->getContent() ?></p>
            </div>
            <div>
                
            </div>
            <hr />
        </div>

    <?php endforeach; ?>
</div>




<?php $this->render('includes/footer') ?>
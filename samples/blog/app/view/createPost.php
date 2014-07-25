<?php $this->render('includes/header') ?>
<h1>Create Post</h1>

<form id="newPostForm" method="POST">
    <div>
        <div>
            <label for="title">Title:</label>
        </div>
        <div>
            <input type="text" id="title" name="title" value="<?= $title ?>" />
        </div>
    </div>
    <div>
        <div>
            <label for="categories">Categories (Comma separated):</label>
        </div>
        <div>
            <input type="text" id="categories" name="categories" value="<?= $categories ?>" />
        </div>
    </div>
    <div>
        <div>
            <label for="content">Content:</label>
        </div>
        <div>
            <textarea id="content" name="content" ><?= $content ?></textarea>
        </div>
    </div>
    <div>
        <input type="submit" value="Create Post" />
    </div>
</form>

<?php $this->render('includes/footer') ?>
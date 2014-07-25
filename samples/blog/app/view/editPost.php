<?php $this->render('includes/header') ?>
<?php /* @var $post BlogPost */ ?>

<?php 
    $catNames = array();
    foreach($post->getCategories() as $cat){
        /* @var $cat Category */
        $catNames[] = $cat->getName();
    }
    $catString = implode(',', $catNames);
?>

<h1>Edit Post</h1>

<form id="newPostForm" method="POST">
    <div>
        <div>
            <label for="title">Title:</label>
        </div>
        <div>
            <input type="text" id="title" name="title" value="<?= $post->getTitle() ?>" />
        </div>
    </div>
    <div>
        <div>
            <label for="categories">Categories (Comma separated):</label>
        </div>
        <div>
            <input type="text" id="categories" name="categories" value="<?= $catString ?>" />
        </div>
    </div>
    <div>
        <div>
            <label for="content">Content:</label>
        </div>
        <div>
            <textarea id="content" name="content" ><?= $post->getContent() ?></textarea>
        </div>
    </div>
    <div>
        <input type="submit" value="Update Post" />
    </div>
</form>

<?php $this->render('includes/footer') ?>
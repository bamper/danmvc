<?php $this->render('includes/header') ?>
    <h1>Login</h1>


    <?php if (isset($errorMessage)): ?>
        <div class="error">
            <p><?= $errorMessage ?></p>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="email">Email:</label>
            <input type="text" name="email" id="email" />
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" />
        </div>
        <div>
            <input type="submit" value="Login" />
        </div>
    </form>

<?php $this->render('includes/footer') ?>
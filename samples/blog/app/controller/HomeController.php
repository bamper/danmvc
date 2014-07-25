<?php

use danperron\danmvc\Core\Application;
use danperron\danmvc\Core\Log\Logger;
use danperron\danmvc\Core\MvcException;

/**
 * Description of HomeController
 *
 * @author dan
 */
class HomeController extends AbstractBlogController {

    /**
     *
     * @var BlogPostDAO
     */
    private $blogPostDAO = null;

    public function __construct(Application $app) {
        parent::__construct($app);
        $pdo = $app->getProperty('PDO');
        $this->blogPostDAO = new BlogPostDAO($pdo);
    }

    public function showHome() {

        try {
            $blogPosts = $this->blogPostDAO->fetchAll();

            $this->view->assign('blogPosts', $blogPosts);

            $this->view->render('home');
        } catch (Exception $exc) {
            Logger::getInstance()->logException($exc);
            throw new MvcException('Error fetching posts.', 0, $exc);
        }
    }

}

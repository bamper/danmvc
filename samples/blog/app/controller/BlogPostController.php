<?php

use danperron\danmvc\Core\Application;
use danperron\danmvc\Core\Log\Logger;
use danperron\danmvc\Core\MvcException;
use danperron\danmvc\Http\HttpHeaders;

/**
 * Description of BlogPostController
 *
 * @author dan
 */
class BlogPostController extends AbstractBlogController {

    /**
     *
     * @var BlogPostDAO
     */
    private $blogPostDAO;

    /**
     *
     * @var CategoryDAO
     */
    private $categoryDAO;

    /**
     * 
     * @param Application $app
     */
    public function __construct(Application $app) {
        parent::__construct($app);
        $pdo = $app->getProperty('PDO');
        /* @var $pdo PDO */
        $this->blogPostDAO = new BlogPostDAO($pdo);
        $this->categoryDAO = new CategoryDAO($pdo);
    }

    public function viewBlogPost($postId) {
        $post = $this->blogPostDAO->findById($postId);

        if (!$post) {
            header(HttpHeaders::HTTP_NOT_FOUND);
            $this->view->render('notFoundView');
            return;
        }

        $this->view->assign('post', $post);
        $this->view->render('post');
    }

    /**
     * GET: /posts/:postId/delete
     * 
     * @param type $postId
     */
    public function deletePost($postId) {
        $this->requireRole(Role::ROLE_ADMIN);
        try {
            $post = $this->blogPostDAO->findById($postId);
            if (!$post) {
                header(HttpHeaders::HTTP_NOT_FOUND);
                $this->view->render('notFoundView');
                return;
            }

            $this->blogPostDAO->remove($post);
            $this->view->render('postDeleted');
        } catch (Exception $e) {
            throw new MvcException('Error while trying to delete post', 0, $e);
        }
    }

    /**
     * GET: /createPost
     */
    public function showCreateBlogPostForm() {

        $user = $this->session->get('user');
        /* @var $user User */

        $this->requireRole(Role::ROLE_AUTHOR);

        $this->view->assign('title', '');
        $this->view->assign('categories', '');
        $this->view->assign('content', '');
        $this->view->render('createPost');
    }

    /**
     * GET: /category/:categoryName
     * 
     * @param type $categoryName
     */
    public function listByCategory($categoryName){
        
    }
    
    /**
     * GET: /posts/:postId/edit
     * 
     * @param type $postId
     */
    public function showEditPost($postId) {
        try {
            $post = $this->blogPostDAO->findById($postId);

            $this->view->assign('post', $post);

            $this->view->render('editPost');
        } catch (Exception $e) {
            throw new MvcException('Error', 0, $e);
        }
    }

    /**
     * POST: /posts/:postId/edit
     * 
     * @param int $postId
     */
    public function doEditPost($postId) {
        try {
            $post = $this->blogPostDAO->findById($postId);
            /* @var $post BlogPost */

            $post->setTitle(trim($this->input->getPostField('title')));
            $post->setContent(trim($this->input->getPostField('content')));

            $categoryString = trim($this->input->getPostField('categories'));
            $catNames = explode(',', $categoryString);
            $post->clearCategories();
            foreach ($catNames as $catName) {
                $category = new Category();
                $category->setName($catName);
                $post->addCategory($category);
            }
            
            $this->blogPostDAO->update($post);
            $this->app->redirect($this->app->getBaseUri() . '/posts/' . $post->getId());
            
        } catch (Exception $e) {
            throw new MvcException('Error', 0, $e);
        }
    }

    /**
     * POST: /createPost
     */
    public function doCreateBlogPost() {
        $user = $this->session->get('user');
        /* @var $user User */

        $this->requireRole(Role::ROLE_AUTHOR);

        try {

            $title = trim($this->input->getPostField('title'));
            $categories = trim($this->input->getPostField('categories'));
            $content = trim($this->input->getPostField('content'));


            $blogPost = new BlogPost();
            $blogPost->setAuthor($user);
            $blogPost->setPubDate(new DateTime());
            $blogPost->setTitle($title);
            $blogPost->setContent($content);

            $categoryNames = explode(',', $categories);

            foreach ($categoryNames as $catName) {
                $category = new Category();
                $category->setName(trim($catName));
                $blogPost->addCategory($category);
            }

            $this->view->assign('title', $title);
            $this->view->assign('categories', $categories);
            $this->view->assign('content', $content);

            $this->blogPostDAO->create($blogPost);

            $this->app->redirect($this->app->getBaseUri() . '/posts/' . $blogPost->getId());
        } catch (Exception $e) {
            Logger::getInstance()->logException($e);
            $this->view->assign('errorMessage', 'Unable to create blog post.');
        }
        $this->view->render('createPost');
    }

}

<?php

use danperron\danmvc\Core\MvcException;

/**
 * Description of BlogPostDAO
 *
 * @author dan
 */
class BlogPostDAO extends AbstractDAO {

    /**
     *
     * @var UserDAO
     */
    private $userDAO;

    /**
     *
     * @var CategoryDAO
     */
    private $categoryDAO;

    /**
     * 
     * @param PDO $pdo
     */
    function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userDAO = new UserDAO($pdo);
        $this->categoryDAO = new CategoryDAO($pdo);
    }

    public function create($value) {
        /* @var $value BlogPost */
        try {
            $sql = 'INSERT INTO BlogPost (userId, title, pubDate, content) VALUES (:userId, :title, :pubDate, :content);';

            $params = array();
            $params['userId'] = $value->getAuthor()->getId();
            $params['title'] = $value->getTitle();
            $params['pubDate'] = $value->getPubDate()->format(DateTime::W3C);
            $params['content'] = $value->getContent();


            $statement = $this->pdo->prepare($sql);
            $this->pdo->beginTransaction();
            $statement->execute($params);
            $value->setId($this->pdo->lastInsertId());
            $this->updateCategories($value);
            $this->pdo->commit();
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new MvcException('Error creating blogpost', 0, $e);
        }
    }

    /**
     * 
     * @param int $id
     * @return \BlogPost|null
     * @throws MvcException
     */
    public function findById($id) {
        try {
            $sql = 'SELECT postId, userId, title, pubDate, content FROM BlogPost WHERE postId = :postId;';
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array('postId' => $id));

            if ($statement->rowCount() > 0) {
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                $blogPost = new BlogPost();
                $blogPost->setId($row['postId']);
                $blogPost->setTitle($row['title']);
                $blogPost->setPubDate(new DateTime($row['pubDate']));
                $blogPost->setContent($row['content']);

                $author = $this->userDAO->findById($row['userId']);
                $blogPost->setAuthor($author);
                $this->fetchPostCategories($blogPost);

                return $blogPost;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new MvcException('Error finding blog post', 0, $e);
        }
    }

    /**
     * 
     * @param BlogPost $value
     * @throws MvcException
     */
    public function remove($value) {
        /* @var $value BlogPost */
        try {
            $sql = 'DELETE FROM BlogPost WHERE postId = :postId;';
            $statement = $this->pdo->prepare($sql);

            $this->pdo->beginTransaction();
            $statement->execute(array('postId' => $value->getId()));
            $this->pdo->commit();
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new MvcException('Error deleting post', 0, $e);
        }
    }

    /**
     * 
     * @param BlogPost $value
     * @throws MvcException
     */
    public function update($value) {
        /* @var $value BlogPost */
        try {
            $sql = 'UPDATE BlogPost SET userId=:userId, title=:title, pubDate=:pubDate, content=:content WHERE postId = :postId;';
            $statement = $this->pdo->prepare($sql);

            $updateValues = array();
            $updateValues['postId'] = $value->getId();
            $updateValues['userId'] = $value->getAuthor()->getId();
            $updateValues['title'] = $value->getTitle();
            $updateValues['pubDate'] = $value->getPubDate()->format(DateTime::W3C);
            $updateValues['content'] = $value->getContent();

            $this->pdo->beginTransaction();
            $statement->execute($updateValues);
            $this->updateCategories($value);
            $this->pdo->commit();
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new MvcException('Error updating post', 0, $e);
        }
    }

    public function fetchAll() {
        try {

            $users = array();

            $sql = 'SELECT postId, userId, title, pubDate, content '
                    . 'FROM BlogPost '
                    . 'ORDER BY pubDate DESC;';

            $categorySQL = 'SELECT c.categoryId, c.name FROM Category c '
                    . 'LEFT JOIN BlogPost_Category bc ON c.categoryId = bc.categoryId '
                    . 'WHERE bc.postId = :postId';

            $statement = $this->pdo->prepare($sql);

            $catStatement = $this->pdo->prepare($categorySQL);

            $statement->execute();

            $blogPosts = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $blogPost = new BlogPost();
                $blogPost->setId($row['postId']);
                $blogPost->setTitle($row['title']);
                $blogPost->setContent($row['content']);
                $blogPost->setPubDate(new DateTime($row['pubDate']));

                $catStatement->execute(array('postId' => $blogPost->getId()));

                while ($catRow = $catStatement->fetch(PDO::FETCH_ASSOC)) {
                    $category = new Category();
                    $category->setId($catRow['categoryId']);
                    $category->setName($catRow['name']);
                    $blogPost->addCategory($category);
                }

                if (!isset($users[$row['userId']])) {
                    $users[$row['userId']] = $this->userDAO->findById($row['userId']);
                }

                $blogPost->setAuthor($users[$row['userId']]);
                $blogPosts[] = $blogPost;
            }
            return $blogPosts;
        } catch (PDOException $e) {
            throw new MvcException('Unable to fetch posts.', 0, $e);
        }
    }

    private function fetchPostCategories(BlogPost $blogPost) {
        $sql = 'SELECT c.categoryId AS id, c.name AS name FROM Category c '
                . 'LEFT JOIN BlogPost_Category bc ON c.categoryId = bc.categoryId '
                . 'WHERE bc.postId = :postId;';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(array('postId' => $blogPost->getId()));
        
        while($category = $statement->fetchObject('Category')){
            $blogPost->addCategory($category);
        }
        
    }

    /**
     * 
     * @param BlogPost $blogPost
     */
    private function createOrFindCategories(BlogPost $blogPost) {
        $categories = $blogPost->getCategories();
        $blogPost->clearCategories();
        foreach ($categories as $category) {
            /* @var $category Category */
            $category = $this->categoryDAO->findOrCreate($category->getName());
            $blogPost->addCategory($category);
        }
    }

    /**
     * 
     * @param BlogPost $blogPost
     * @throws MvcException
     */
    private function updateCategories(BlogPost $blogPost) {
        try {
            $this->createOrFindCategories($blogPost);
            $sql = 'DELETE FROM BlogPost_Category WHERE postId = :postId';
            $statement = $this->pdo->prepare($sql);
            $statement->execute(array('postId' => $blogPost->getId()));

            $sql2 = 'INSERT INTO BlogPost_Category (postId, categoryId) VALUES (:postId, :categoryId);';
            $statement2 = $this->pdo->prepare($sql2);

            foreach ($blogPost->getCategories() as $category) {
                /* @var $category Category */
                $statement2->execute(array('postId' => $blogPost->getId(), 'categoryId' => $category->getId()));
            }
        } catch (Exception $exc) {
            throw new MvcException('Error attaching categories.', 0, $exc);
        }
    }

}

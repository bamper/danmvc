<?php

/**
 * Description of Comment
 *
 * @author dan
 */
class Comment {
    /**
     *
     * @var int 
     */
    private $id = 0;
    
    /**
     *
     * @var BlogPost
     */
    private $blogPost = null;
    
    /**
     *
     * @var User
     */
    private $author = null;
    
    /**
     *
     * @var DateTime
     */
    private $pubDate = null;
    
    /**
     *
     * @var string
     */
    private $content = '';
    
    public function getId() {
        return $this->id;
    }

    public function getBlogPost() {
        return $this->blogPost;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getPubDate() {
        return $this->pubDate;
    }

    public function getContent() {
        return $this->content;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setBlogPost(BlogPost $blogPost) {
        $this->blogPost = $blogPost;
    }

    public function setAuthor(User $author) {
        $this->author = $author;
    }

    public function setPubDate(DateTime $pubDate) {
        $this->pubDate = $pubDate;
    }

    public function setContent($content) {
        $this->content = $content;
    }
    
}

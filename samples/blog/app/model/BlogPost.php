<?php

/**
 * Description of BlogPost
 *
 * @author dan
 */
class BlogPost {
    
    /**
     *
     * @var int
     */
    private $id = 0;
    
    /**
     *
     * @var string 
     */
    private $title = '';
    
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
    
    /**
     *
     * @var array
     */
    private $categories = array();
    
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
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

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setAuthor(User $author = null) {
        $this->author = $author;
    }

    public function setPubDate(DateTime $pubDate) {
        $this->pubDate = $pubDate;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function addCategory(Category $category){
        $this->categories[] = $category;
    }
    
    public function clearCategories(){
        $this->categories = array();
    }
    
    public function getCategories(){
        return $this->categories;
    }
    
}

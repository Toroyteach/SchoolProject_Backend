<?php
class NewsInformation
{
    // Connection
    private $conn;

    // Db connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getNewsCategories()
    {
        $query = " SELECT * FROM tbl_news_category ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'category' => $row['category'],
                'description' => $row['description']
            );
        }

        return $data;
    }

    public function getlatestNews()
    {
        $query = " SELECT tbl_news_article.*, tbl_news_category.category FROM tbl_news_article INNER JOIN tbl_news_category ON tbl_news_category.id = tbl_news_article.category_id WHERE status = 1 ORDER BY created_at DESC ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'title' => $row['title'],
                'category' => $row['category'],
                'body' => $row['body'],
                'image' => $row['image'],
                'status' => $row['status'],
                'created_at' => $row['created_at']
            );
        }

        $response = array(
            "data" => $data,
            "status" => true,
        );

        return $response;
    }
}

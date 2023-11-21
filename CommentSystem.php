<?php

class CommentSystem {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addComment($postId, $userId, $comment) {
        $comment = $this->sanitizeInput($comment);
        if($this->validateInput($comment)) {
            $stmt = $this->db->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $postId, $userId, $comment);
            $stmt->execute();
            return true;
        }
        return false;
    }

    public function editComment($commentId, $userId, $newComment) {
        $newComment = $this->sanitizeInput($newComment);
        if($this->validateInput($newComment)) {
            $stmt = $this->db->prepare("UPDATE comments SET comment = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sii", $newComment, $commentId, $userId);
            $stmt->execute();
            return true;
        }
        return false;
    }

    public function deleteComment($commentId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $commentId, $userId);
        $stmt->execute();
    }

    public function displayComments($postId) {
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE post_id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function sanitizeInput($input) {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        return $input;
    }

    private function validateInput($input) {
        if(empty($input)) {
            return false;
        }
        return true;
    }
}
?>
<?php
session_start();
require_once DIR . '/../config/database.php';
require_once DIR . '/../models/BuddyMatch.php';
class BuddyController {
    private $buddyMatch;
    public function __construct() {
        $this->buddyMatch = new BuddyMatch();
    }
    public function index() {
        if (!isset($_SESSION['user_id'])) { header('Location: /auth/login.php'); exit(); }
        $currentUserId = $_SESSION['user_id'];
        $suggestedBuddies = $this->buddyMatch->getSuggestedBuddies($currentUserId);
        require DIR . '/../views/buddy/matches.php';
    }
    public function requestMatch($buddyId) {
        if (!isset($_SESSION['user_id'])) { header('Location: /auth/login.php'); exit(); }
        $requesterId = $_SESSION['user_id'];
        $this->buddyMatch->sendRequest($requesterId, $buddyId);
        header('Location: /buddy/matches'); exit();
    }
}
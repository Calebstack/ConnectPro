<?php
require_once "Db.php";

class ProviderService extends Db {
    private $dbconn;

    public function __construct() {
        $this->dbconn = $this->connect();
    }

    public function setupProviderProfile($user_id, $category, $bio, $price_per_hour, $experience_years, $location) {
        try {
            // Check if profile exists
            $checkSql = "SELECT id FROM providers WHERE user_id = ?";
            $checkStmt = $this->dbconn->prepare($checkSql);
            $checkStmt->execute([$user_id]);
            $exists = $checkStmt->fetch();

            if ($exists) {
                $sql = "UPDATE providers SET category = ?, bio = ?, price_per_hour = ?, experience_years = ?, location = ? WHERE user_id = ?";
                $stmt = $this->dbconn->prepare($sql);
                return $stmt->execute([$category, $bio, $price_per_hour, $experience_years, $location, $user_id]);
            } else {
                $sql = "INSERT INTO providers (user_id, category, bio, price_per_hour, experience_years, location) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->dbconn->prepare($sql);
                return $stmt->execute([$user_id, $category, $bio, $price_per_hour, $experience_years, $location]);
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error updating provider details: " . $e->getMessage();
            return false;
        }
    }

    public function getProviderDetailsByUserId($user_id) {
        try {
            $sql = "SELECT p.*, u.full_name, u.email, u.phone, u.profile_image 
                    FROM providers p 
                    INNER JOIN users u ON p.user_id = u.id 
                    WHERE p.user_id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getProviderDetailsById($provider_id) {
        try {
            $sql = "SELECT p.*, u.full_name, u.email, u.phone, u.profile_image 
                    FROM providers p 
                    INNER JOIN users u ON p.user_id = u.id 
                    WHERE p.id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$provider_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function searchProviders($category = null, $keyword = null) {
        try {
            $sql = "SELECT p.*, u.full_name, u.email, u.phone, u.profile_image 
                    FROM providers p 
                    INNER JOIN users u ON p.user_id = u.id 
                    WHERE 1=1";
            $params = [];

            if ($category && $category !== 'All') {
                $sql .= " AND p.category = ?";
                $params[] = $category;
            }

            if ($keyword) {
                $sql .= " AND (u.full_name LIKE ? OR p.bio LIKE ? OR p.location LIKE ?)";
                $searchWildcard = "%" . $keyword . "%";
                $params[] = $searchWildcard;
                $params[] = $searchWildcard;
                $params[] = $searchWildcard;
            }

            $sql .= " ORDER BY p.experience_years DESC";
            
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllProviders() {
        try {
            $sql = "SELECT p.*, u.full_name, u.email, u.phone, u.profile_image 
                    FROM providers p 
                    INNER JOIN users u ON p.user_id = u.id 
                    ORDER BY p.id DESC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function deleteProvider($provider_id) {
        try {
            $sql = "DELETE FROM providers WHERE id = ?";
            $stmt = $this->dbconn->prepare($sql);
            return $stmt->execute([$provider_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getCategoriesList() {
        return [
            'Barber',
            'Cleaner',
            'Photographer',
            'Plumber',
            'Makeup Artist',
            'Tutor',
            'Electrician',
            'Mechanic'
        ];
    }
}
?>

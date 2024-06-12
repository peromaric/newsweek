<?php
global $dbc;
function connectToDB()
{
    global $dbc;
    $dbc = mysqli_connect("db", "root", "root", "newsweek", "3306");
    $createTableQuery = "CREATE TABLE IF NOT EXISTS article (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        image_path VARCHAR(255) NOT NULL,
                        title VARCHAR(255) NOT NULL,
                        content MEDIUMBLOB NOT NULL,
                        country VARCHAR(6) NOT NULL
                    )";

    $dbc->query("USE newsweek");
    $dbc->query($createTableQuery);
    // Check if connection was successful
    if ($dbc->connect_error) {
        die("Connection failed: " . $dbc->connect_error);
    }
}

function disconnectFromDB()
{
    global $dbc;
    mysqli_close($dbc);
}

function GUID()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function createArticle($image_path, $title, $content, $country)
{
    global $dbc;
    $stmt = $dbc->prepare("INSERT INTO article (image_path, title, content, country) VALUES (?, ?, ?, ?)");

    $stmt->bind_param("ssss", $image_path, $title, $content, $country);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function readArticles()
{
    global $dbc;

    $stmt = $dbc->prepare("SELECT * FROM article");

    $stmt->execute();

    $result = $stmt->get_result();

    $articles = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    return $articles;
}

function updateArticle($id, $title, $content, $country)
{
    global $dbc;

    $stmt = $dbc->prepare("UPDATE article SET title=?, content=?, country=? WHERE id=?");

    $stmt->bind_param("sssi", $title, $content, $country, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function deleteArticle($id)
{
    global $dbc;

    $stmt = $dbc->prepare("DELETE FROM article WHERE id=?");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        return true;
    } else {
        return false;
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        connectToDB();
        header('Content-Type: application/json');
        $result = readArticles();
        disconnectFromDB();
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(["error" => "Error fetching articles"]);
        }
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../uploads/';
            $new_image_name = GUID();
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 7777, true);
            }
            $image_upload_path = $upload_dir . $new_image_name;
            $relative_database_path = 'uploads/' . $new_image_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_upload_path)) {
                $title = $_POST['title'];
                $content = $_POST['article-text'];
                $country = $_POST['country'];

                connectToDB();
                createArticle($relative_database_path, $title, $content, $country);
                disconnectFromDB();
                echo json_encode(["success" => "Article created successfully!"]);
            } else {
                echo json_encode(["error" => "Error uploading image"]);
            }
        } else {
            echo json_encode(["error" => "No image uploaded or upload error"]);
        }
    } else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        connectToDB();
        try {
            $id = $_GET['id'];
            connectToDB();
            deleteArticle($id);
            disconnectFromDB();
            echo json_encode(["success" => $id]);
        } catch (Exception $ex) {
            echo json_encode(["error" => $ex->getMessage()]);
        }
    } else if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
        $rawInput = file_get_contents("php://input");
        $input = json_decode($rawInput, true);

        // Validate received data
        $id = isset($input['id']) ? $input['id'] : null;
        $title = isset($input['title']) ? $input['title'] : null;
        $content = isset($input['content']) ? $input['content'] : null;
        $country = isset($input['country']) ? $input['country'] : null;

        if ($id === null || $title === null || $content === null) {
            echo json_encode(["error" => "One or more required fields are missing."]);
            exit;
        }

        connectToDB();
        $update_attempt = updateArticle($id, $title, $content, $country);
        disconnectFromDB();

        if ($update_attempt) {
            echo json_encode(["success" => "Successfully updated article"]);
        } else {
            echo json_encode(["error" => "Failed to update article"]);
        }
    } else {
        echo json_encode(["error" => "Invalid request method"]);
    }

} catch (Exception $e) {
    echo "Database error! Check connection!";
}


// Disconnect from the database
?>
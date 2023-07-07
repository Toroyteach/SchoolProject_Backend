<?php
ob_start();
require('../../top.inc.php');
isAdmin();
$category = '';
$categoryDescription = '';
$newsTitle = '';
$newCategory = '';
$newsShortExerpt = '';
$newsBody = '';
$newsImage = '';

$dateTime = new DateTime();
$date = $dateTime->format('Y-m-d H:i:s');

$msg = '';
// if (isset($_GET['id']) && $_GET['id'] != '') {
//     $image_required = '';
//     $id = get_safe_value($con, $_GET['id']);
//     $res = mysqli_query($con, "SELECT * FROM tbl_member WHERE id='$id'");
//     $check = mysqli_num_rows($res);
//     if ($check > 0) {
//         $row = mysqli_fetch_assoc($res);
//         $username = $row['username'];
//         $email = $row['email'];
//         $phone = $row['phone'];
//         $password = $row['password'];
//     } else {
//         header('location: createInformation.php');
//         die();
//     }
// }

if (isset($_POST['submitCategory'])) {
    $category = get_safe_value($con, $_POST['category']);
    $categoryDescription = get_safe_value($con, $_POST['categoryDescription']);


    if(mysqli_query($con, "INSERT INTO tbl_news_category( category, description, created_at) VALUES ('$category','$categoryDescription','$date')")) {
        
        header('location: createInformation.php');

    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }

}

if (isset($_POST['submitNews'])) {
    $newsTitle = get_safe_value($con, $_POST['newsTitle']);
    $newCategory = get_safe_value($con, $_POST['newsCategory']);
    $newsBody = get_safe_value($con, $_POST['newsBody']);

    if (isset($_FILES['newsImage'])) {
        $file = $_FILES['newsImage'];
    
        // Extract file details
        $filename = $file['name'];
        $tmpPath = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];
    
        // Generate unique name for the image
        $uniqueName = uniqid() . "_" . $filename;
    
        // Move the image to the uploads folder
        $targetPath = "./images/news/" . $uniqueName;
        // var_dump($targetPath);
        // die();
        if(move_uploaded_file($tmpPath, $targetPath)){
            // Store file details in the database
            if(mysqli_query($con, "INSERT INTO tbl_news_article( title, category_id, body, image, created_at) VALUES ('$newsTitle','$newCategory','$newsBody','$targetPath', '$date')")) {
            
                header('location: createInformation.php');
        
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($connection);
            }
        }
    
    } else {

        if(mysqli_query($con, "INSERT INTO tbl_news_article( title, category_id, body, created_at) VALUES ('$newsTitle','$newCategory','$newsBody', '$date')")) {
        
            header('location: createInformation.php');
    
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($connection);
        }
    }
}

if(isset($_GET['id']) && isset($_GET['table'])) {
    $id = $_GET['id'];
    $table = $_GET['table'];

    // Validate and sanitize the table name to prevent SQL injection
    $allowedTables = ['tbl_news_category', 'tbl_news_article'];
    if(in_array($table, $allowedTables)) {
        // Perform the deletion query
        
        if($table == 'tbl_news_article'){

            $query = "DELETE FROM $table WHERE id = '$id'";

        } elseif($table == 'tbl_news_category') {

            $query = "DELETE FROM $table WHERE id = '$id'";

        }
        
        $result = mysqli_query($con, $query);

        if($result) {
            // Deletion was successful, redirect back to the current page
            header("Location: createInformation.php");
            exit();
        } else {
            // Deletion failed
            echo "Error deleting the row: " . mysqli_error($con);
        }
    } else {
        // Invalid table name
        echo "Invalid table name.";
    }
}

$categoryOptionsSql = "SELECT id, category FROM tbl_news_category";
$categoryOptions = $con->query($categoryOptionsSql);
$options = "";
if ($categoryOptions->num_rows > 0) {
    while ($row = $categoryOptions->fetch_assoc()) {
        $optionId = $row['id'];
        $optionName = $row['category'];
        $options .= "<option value='$optionId'>$optionName</option>";
    }
}
?>
<div class="content pb-0" style="position: relative; max-height: 77vh; overflow-y: auto;">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header"><strong>CREATE NEWS CATEGORY</strong><small> </small></div>
                    <form method="post">
                        <div class="card-body card-block">


                            <div class="form-group">
                                <label for="category" class=" form-control-label">Category</label>
                                <input type="text" name="category" id="category" placeholder="Enter Category" class="form-control" required >
                            </div>
                            <div class="form-group">
                                <label for="categoryDescription" class=" form-control-label">Description</label>
                                <textarea id="categoryDescription" name="categoryDescription" rows="4" cols="50" required></textarea>
                            </div>


                            <button id="payment-button" name="submitCategory" type="submit" class="btn btn-lg btn-info">
                                <span id="payment-button-amount">SUBMIT</span>
                            </button>
                            <div class="field_error"><?php echo $msg ?></div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-header"><strong>CREATE NEWS ARTICLE</strong><small> </small></div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="card-body card-block">


                            <div class="form-group">
                                <label for="newsTitle" class=" form-control-label">Title</label>
                                <input type="text" name="newsTitle" id="newsTitle" placeholder="Enter Title" class="form-control" required value="">
                            </div>

                            <div class="form-group">
                                <select name="newsCategory" id="newsCategory">
                                    <option value="">--- Choose a category ---</option>
                                    <?php echo $options; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="newsBody" class=" form-control-label">Body</label>
                                <textarea id="newsBody" name="newsBody" rows="4" cols="50" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="newsImage" class=" form-control-label">Image</label>
                                <input type="file" name="newsImage" id="newsImage">
                            </div>

                            <button id="payment-button" name="submitNews" type="submit" class="btn btn-lg btn-info">
                                <span id="payment-button-amount">SUBMIT</span>
                            </button>
                            <div class="field_error"><?php echo $msg ?></div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="container mt-5">
        <h2> News Information</h2>
        <hr>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#category">Category</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#news">News</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content mt-3" style="position:relative; max-height: 50vh; overflow-y: auto;">
            <div class="tab-pane container active" id="category">
                <?php
                // Fetch and display user locations from the database
                $stmt = $con->prepare("SELECT * FROM tbl_news_category ORDER BY created_at DESC");
                $stmt->execute();
                $result = $stmt->get_result();
                $categorys = $result->fetch_all(MYSQLI_ASSOC);


                if ($result->num_rows > 0) {
                    echo "<table class='table table-bordered'>";
                    echo "<thead><tr><th>Id</th><th>Category</th><th>Description</th><th>Created Date</th><th>Action</th></tr></thead>";
                    echo "<tbody>";
                    $id = 1;
                    foreach ($categorys as $category) {
                        $categoryN = $category['category'];
                        $description = $category['description'];
                        $createdDate = $category['created_at'];
                        $dataId = $category['id'];

                        echo "<tr>";
                        echo "<td>$id</td>";
                        echo "<td>$categoryN</td>";
                        echo "<td>$description</td>";
                        echo "<td>$createdDate</td>";
                        echo "<td><a href='createInformation.php?table=tbl_news_category&id=" . $dataId . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></td>";
                        echo "</tr>";

                        $id++;
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No Category to show.</p>";
                }
                ?>
            </div>
            <div class="tab-pane container fade" id="news">
                <?php
                // Fetch and display user locations from the database
                $stmt = $con->prepare("SELECT tbl_news_article.*, tbl_news_category.category FROM tbl_news_article INNER JOIN tbl_news_category ON tbl_news_category.id = tbl_news_article.category_id ORDER BY created_at DESC");
                $stmt->execute();
                $result = $stmt->get_result();
                $news = $result->fetch_all(MYSQLI_ASSOC);


                if ($result->num_rows > 0) {
                    echo "<table class='table table-bordered'>";
                    echo "<thead><tr><th>Id</th><th>Title</th><th>Category</th><th>Body</th><th>Image</th><th>Status</th><th>Created</th><th>Action</th></tr></thead>";
                    echo "<tbody>";
                    $id = 1;
                    foreach ($news as $newsItem) {
                        $title = $newsItem['title'];
                        $category = $newsItem['category'];
                        $body = $newsItem['body'];
                        $image = $newsItem['image'];
                        $status = $newsItem['status'];
                        $createdDate = $newsItem['created_at'];
                        $dataId = $newsItem['id'];

                        echo "<tr>";
                        echo "<td>$id</td>";
                        echo "<td>$title</td>";
                        echo "<td>$category</td>";
                        echo "<td>$body</td>";
                        echo "<td><img src='" . $image . "' style='max-width: 100px;'></td>";
                        echo "<td>$status</td>";
                        echo "<td>$createdDate</td>";
                        echo "<td><a href='createInformation.php?table=tbl_news_article&id=" . $dataId . "' onclick='return confirm(\"Are you sure you want to delete this data?\")'>Delete</a></td>";
                        echo "</tr>";

                        $id++;
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No News to show.</p>";
                }
                ?>
            </div>
        </div>


    </div>
</div>



<?php
require('../../footer.inc.php');
ob_end_flush();
?>
<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

include("db_connect.php");

$post_id = $_GET["id"];


$sql = "SELECT * FROM posts WHERE id = $post_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Post not found";
    exit();
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="eng">
<?php include('components/head.php') ?>

<body class="js">


    <!-- Eshop Color Plate -->

    <!-- /End Color Plate -->

    <!-- Header -->
    <?php include('components/header.php') ?>
    <!--/ End Header -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script>
    tinymce.init({
      selector: '#content',
      menubar: false,
      toolbar: 'undo redo | formatselect | bold italic underline superscript subscript | alignleft aligncenter alignright | bullist numlist outdent indent | table',
      plugins: 'lists',
      branding: false
    });
  </script>

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="index.php">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active">Edit Post</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <div class="container pt-3">
        <div class="form-main">
            <!-- <h1>Edit Product</h1> -->
            <form action="update_blog_post.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $post_id; ?>">
                <div class="row g-3">
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <input class="form-control rounded px-3 py-2" placeholder="Title" type="text" name="title" id="title" value="<?php echo htmlspecialchars($post["title"]); ?>" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <textarea class="form-control rounded px-3 py-2" placeholder="Content" name="content" id="content" required><?php echo htmlspecialchars($post["content"]); ?></textarea>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="form-group">
                            <select class="form-control form-select" id="category" name="category" required>
                                <option value="" disabled>Select Category</option>
                                <?php
                                $sql_categories = "SELECT id, name FROM blog_categories";
                                $result_categories = $conn->query($sql_categories);
                                if ($result_categories->num_rows > 0) {
                                    while ($row_category = $result_categories->fetch_assoc()) {
                                        $selected = ($post["category_id"] == $row_category["id"]) ? "selected" : "";
                                        echo "<option value='" . $row_category["id"] . "' $selected>" . htmlspecialchars($row_category["name"]) . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No categories available</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <input class="mx-3" type="file" name="image" id="image">
                    <?php if (!empty($post["image_path"])): ?>
                        <div class="mt-2">
                            <img src="assets/images/<?php echo htmlspecialchars($post["image_path"]); ?>"
                                alt="Blog Image" style="max-width: 100px;">
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn rounded m-3 text-white">Update Post</button>
                </div>
            </form>
        </div>
    </div>
    <!--/ End Blog Single -->

    <!-- Start Footer Area -->
    <?php include('components/footer.php') ?>
    <!-- /End Footer Area -->

    <!-- Jquery -->
    <?php include('components/script.php') ?>
</body>

</html>
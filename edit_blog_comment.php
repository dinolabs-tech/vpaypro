<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: blog_post_details.php");
    exit();
}

include("db_connect.php");

$comment_id = $_GET["id"];

$sql = "SELECT * FROM comments WHERE id = $comment_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Comment not found";
    exit();
}

$comment = $result->fetch_assoc();
$post_id = $comment["post_id"];
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
                            <li><a href="blog.php">Posts<i class="ti-arrow-right"></i></a></li>
                            <li class="active">Edit Comment</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Blog Single -->
    <div class="container pt-3">
        <div class="form-main">
            <!-- <h1>Edit Product</h1> -->
            <form action="update_blog_comment.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $comment_id; ?>">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <div class="row g-3">
                    <div class="col-lg-4 col-12">
                        <textarea class="form-control" id="comment" name="comment" placeholder="Enter Post Comment" required>
                        <?php echo htmlspecialchars($comment["content"]); ?></textarea>
                    </div>
                    <button type="submit" class="btn rounded m-3 text-white">Update Comment</button>
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
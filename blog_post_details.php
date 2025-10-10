<?php
session_start();
include("db_connect.php");

$post_id = $_GET["id"];

$sql = "SELECT posts.*, login.username FROM posts INNER JOIN login ON posts.author_id = login.id WHERE posts.id = $post_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
	echo "Post not found";
	exit();
}

$post = $result->fetch_assoc();

$update_views_sql = "UPDATE posts SET views = views + 1 WHERE id = $post_id";
$conn->query($update_views_sql);

// Fetch categories
$sql_categories = "SELECT * FROM blog_categories";
$result_blog_categories = $conn->query($sql_categories);

// Get filters
$search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
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

	<!-- Breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="bread-inner">
						<ul class="bread-list">
							<li><a href="index.php">Home<i class="ti-arrow-right"></i></a></li>
							<li class="active">Blog Post Details</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- Start Blog Single -->
	<section class="blog-single section">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 col-12">
					<div class="blog-single-main">
						<div class="row">
							<div class="col-12">
								<h2 class="blog-title"><?php echo $post["title"]; ?></h2>
								<div class="blog-meta">
									<span>Posted on: <?php echo date('jS F Y, h:i a', strtotime($post["created_at"])); ?> &nbsp;<br> By: <strong><?php echo $post["username"]; ?> </strong></span>
								</div>
								<div class="image">
									<?php if ($post["image_path"]) { ?>
										<img src="assets/images/<?php echo $post["image_path"]; ?>" alt="Blog Image"
											class="img-fluid rounded mb-3" style="height: 300px">
									<?php } ?>
								</div>
								<div class="blog-detail">
									<div class="content">
										<p><?php echo $post["content"]; ?></p>
									</div><br>
								</div>
								<div class="share-social">
									<div class="row">
										<div class="col-12">
											<p>Views: <?php echo $post['views']; ?> | Likes: <?php echo $post['likes']; ?></p><br>
											<?php
											if (isset($_SESSION['user_id'])) {
												$user_id = $_SESSION['user_id'];
												$post_id_check = $post['id'];
												$stmt = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
												$stmt->bind_param("ii", $user_id, $post_id_check);
												$stmt->execute();
												$result = $stmt->get_result();
												if ($result->num_rows == 0) {
													echo '<a href="like_post.php?id=' . $post['id'] . '" class="btn rounded pt-1 pb-1 px-3 text-white"> <i class="fa fa-thumbs-up"></i></a>';
												} else {
													echo '<button class="btn rounded btn-success pt-2 pb-2 px-3 text-white" disabled> <i class="fa fa-thumbs-up"></i></button>';
												}
												$stmt->close();
											}
											?>
											<!-- Sharing buttons -->
											<a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn rounded fa fa-whatsapp pt-2 pb-2 px-3 text-white" ></a>
											<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="text-white fa fa-twitter btn rounded pt-2 pb-2 px-3"></i></a>
											<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="text-white btn rounded fa fa-facebook pt-2 pb-2 px-3"></a>



											<?php if (isset($_SESSION["username"])) { ?>
												<?php if ($_SESSION['role_id'] == 0 || $_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 3) { ?>
													<a href="edit_blog_post.php?id=<?php echo $post["id"]; ?>"
														class="btn rounded pt-2 pb-2 px-3 fa fa-edit text-white"></a>
													<a href="delete_blog_post.php?id=<?php echo $post["id"]; ?>" class="btn rounded fa fa-trash pt-2 pb-2 px-3 text-white"></a>
												<?php } ?>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12">
								<div class="comments">
									<h3 class="comment-title">Comments</h3>
									<!-- Single Comment -->
									<div class="mb-5">
										<?php
										$sql = "SELECT * FROM comments WHERE post_id = $post_id ORDER BY created_at DESC";
										$comments_result = $conn->query($sql);

										if ($comments_result->num_rows > 0) {
											while ($comment = $comments_result->fetch_assoc()) { ?>
												<div class='d-flex mb-4'>
												<div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; margin-right: 10px;">
												<i class="fa fa-user"></i></div>
												<div class='ps-3'>
												<h6><strong><?= htmlspecialchars($comment["name"]) ?></strong>
												<?php if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == true) { ?>
													<small> <?= htmlspecialchars($comment["email"]) ?> </small>
												<?php } ?>
												<small><i><?= date('jS F Y, h:i a', strtotime($comment["created_at"])) ?></i></small></h6>

												<p style='margin-left: 30px;'><?=htmlspecialchars($comment["content"]) ?> </p>
												<?php if (isset($_SESSION["username"])) {
													if ($_SESSION['role_id'] == 1 || $_SESSION['role_id'] == 0 || $_SESSION['role_id'] == 3) { ?> 
													<a href='edit_blog_comment.php?id=<?= $comment["id"] ?>' class='btn rounded pt-2 pb-2 px-3 mt-3 text-white fa fa-edit'></a>
													<a href='delete_blog_comment.php?id=<?= $comment["id"] ?>' class='btn rounded pt-2 pb-2 px-3 text-white fa fa-trash'></a>
													<?php } ?>
												 <?php } ?>
												</div>
												</div>
											<?php } ?>
										<?php } else { ?>
											<p>No comments yet</p>
										<?php } ?>
									</div>
									<!-- End Single Comment -->
								</div>
							</div>
							<div class="col-12">
								<div class="reply">
									<div class="reply-head">
										<h2 class="reply-title">Leave a Comment</h2>
										<!-- Comment Form -->
										<form action="add_blog_comment.php" method="post">
											<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
											<div class="row">
												<div class="col-lg-6 col-md-6 col-12">
													<div class="form-group">
														<input type="text" class="form-control" id="name" name="name"
															placeholder="Your Name" required>
													</div>
												</div>
												<div class="col-lg-6 col-md-6 col-12">
													<div class="form-group">
														<input type="email" class="form-control" id="email"
															name="email" placeholder="Your Email" required>
													</div>
												</div>
												<div class="col-12">
													<div class="form-group">
														<textarea class="form-control" id="comment" name="comment"
															placeholder="Comment" required></textarea>
													</div>
												</div>
												<div class="col-12">
													<button type="submit" class="btn rounded text-white">Post comment</button>
												</div>
											</div>
										</form>
										<!-- End Comment Form -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-12">
					<div class="main-sidebar">
						<!-- Single Widget -->
						<form method="GET" action="blog.php" class="mb-3">
							<div class="single-widget search">
								<div class="input-group">
									<input type="text" class="form-control" placeholder="Search Here..." name="search" value="<?php echo htmlspecialchars($search); ?>">
									<button class="btn" type="submit"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</form>
						<!--/ End Single Widget -->
						<!-- Single Widget -->
						<div class="single-widget category">
							<h3 class="title">Categories</h3>
							<ul class="categor-list">
								<?php
								$cats = $conn->query("SELECT c.id, c.name, COUNT(p.id) AS count FROM blog_categories c LEFT JOIN posts p ON p.category_id=c.id GROUP BY c.id ORDER BY c.name");
								while ($rc = $cats->fetch_assoc()): ?>
									<li>
										<a class="h5 fw-semi-bold bg-light rounded py-2 px-3 mb-2 d-flex align-items-center" href="blog.php?category=<?php echo $rc['id']; ?>">
											<i class="bi bi-arrow-right me-2"></i>
											<span><?php echo htmlspecialchars($rc['name']); ?> (<?php echo $rc['count']; ?>)</span>
										</a>
									</li>
								<?php endwhile; ?>
							</ul>
						</div>
						<!--/ End Single Widget -->
						<!-- Single Widget -->
						<div class="single-widget recent-post">
							<h3 class="title">Recent post</h3>
							<!-- Single Post -->
							<div class="single-post">
								<ul>
									<?php
									$recent = $conn->query("SELECT id,title,image_path FROM posts ORDER BY created_at DESC LIMIT 5");
									while ($rp = $recent->fetch_assoc()):
										$img = $rp['image_path'] ? 'assets/images/' . htmlspecialchars($rp['image_path']) : 'img/placeholder.jpg';
									?>
										<li>
											<a class="h5 fw-semi-bold bg-light rounded py-2 px-3 mb-2 d-flex align-items-center" style="width: 300px" href="blog_post_details.php?id=<?php echo $rp['id']; ?>">
												<img src="<?php echo $img; ?>" class="rounded" style="width:40px;height:40px;object-fit:cover; margin-right: 10px;" alt="<?php echo htmlspecialchars($rp['title']); ?>"><br>
												<span><?php echo htmlspecialchars($rp['title']); ?></span>
											</a>
										</li>
									<?php endwhile; ?>
								</ul>
							</div>
							<!-- End Single Post -->
						</div>
						<!--/ End Single Widget -->
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--/ End Blog Single -->

	<!-- Start Footer Area -->
	<?php include('components/footer.php') ?>
	<!-- /End Footer Area -->

	<!-- Jquery -->
	<?php include('components/script.php') ?>
</body>

</html>
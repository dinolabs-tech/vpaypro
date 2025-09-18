<?php
session_start();
include('db_connect.php');
?>

<?php
include('components/head.php');

// Pagination settings
$posts_per_page = 6;
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// Get filters
$search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build WHERE clause
$where = [];
$params = [];
if ($search !== '') {
	$where[]  = "title LIKE ?";
	$params[] = "%{$search}%";
}
if ($category !== '') {
	$where[]  = "category_id = ?";
	$params[] = $category;
}
$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// MAIN POSTS QUERY
$sql = "SELECT * FROM posts {$where_clause} ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Combine params + pagination
$params_all = $params;
$params_all[] = $posts_per_page;
$params_all[] = $offset;

// Build types string: 's' for each filter + 'ii' for limit offset
$types = str_repeat('s', count($params)) . 'ii';
array_unshift($params_all, $types);

// Convert to references
$refs = array();
foreach ($params_all as $key => $value) {
	$refs[$key] = &$params_all[$key];
}

// Bind and execute
call_user_func_array([$stmt, 'bind_param'], $refs);
$stmt->execute();
$result = $stmt->get_result();

// Close statement (result is buffered)
$stmt->close();

// COUNT TOTAL POSTS FOR PAGINATION
$sql_count = "SELECT COUNT(*) AS total FROM posts {$where_clause}";
$stmt2 = $conn->prepare($sql_count);
if ($params) {
	$types_count = str_repeat('s', count($params));
	$params_count = $params;
	array_unshift($params_count, $types_count);
	$refs2 = array();
	foreach ($params_count as $key => $value) {
		$refs2[$key] = &$params_count[$key];
	}
	call_user_func_array([$stmt2, 'bind_param'], $refs2);
}
$stmt2->execute();
$total = $stmt2->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $posts_per_page);
$stmt2->close();
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
							<li class="active">Blog</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- Start Blog Grid -->
	<div class="blog-single shop-blog grid section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="row">
						<?php if ($result->num_rows): ?>
							<?php while ($row = $result->fetch_assoc()): ?>
								<div class="col-lg-4 col-md-6 col-12">
									<?php
									// Fetch category name
									$cat_stmt = $conn->prepare("SELECT name FROM blog_categories WHERE id = ?");
									$cat_stmt->bind_param('i', $row['category_id']);
									$cat_stmt->execute();
									$cat = $cat_stmt->get_result()->fetch_assoc();
									$category_name = $cat['name'] ?? 'Uncategorized';
									$cat_stmt->close();
									?>
									<!-- Start Single Blog  -->
									<div class="shop-single-blog">
										<img class="rounded" style="height:200px; width:100vw;" src="assets/images/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
										<div class="content">
											<p class="date"><?php echo date('d M, Y', strtotime($row['created_at'])); ?></p>
											<p class="title"><?php echo htmlspecialchars($row['title']); ?></p>
											<a href="blog_post_details.php?id=<?php echo $row['id']; ?>" class="more-btn" target="_blank">Continue Reading</a>
										</div>
									</div>
									<!-- End Single Blog  -->
								</div>
							<?php endwhile; ?>
						<?php else: ?>
							<p class="px-3">No posts found.</p>
						<?php endif; ?>
					</div>

				</div>

			</div>
			<div class="col-12">
				<!-- Pagination -->
				<div class="pagination center">
					<?php if ($total_pages > 1): ?>
						<nav aria-label="Page navigation">
							<ul class="pagination-list">
								<?php if ($page > 1): ?>
								<li class="page-item">
									<a class="page-link" href="blog.php?page=<?php echo $page-1; ?><?php echo $search? '&search='.urlencode($search): ''; ?><?php echo $category? '&category='.urlencode($category): ''; ?>">Previous</a>
								</li>
								<?php endif; ?>
								<?php for ($i=1; $i<=$total_pages; $i++): ?>
								<li class="page-item <?php echo $i==$page? 'active':''; ?>">
									<a class="page-link" href="blog.php?page=<?php echo $i; ?><?php echo $search? '&search='.urlencode($search): ''; ?><?php echo $category? '&category='.urlencode($category): ''; ?>"><?php echo $i; ?></a>
								</li>
								<?php endfor; ?>
								<?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="blog.php?page=<?php echo $page+1; ?><?php echo $search? '&search='.urlencode($search): ''; ?><?php echo $category? '&category='.urlencode($category): ''; ?>">Next</a>
                                    </li>
                                <?php endif; ?>
							</ul>
						<?php endif; ?>
				</div>
				<!--/ End Pagination -->
			</div>
		</div>
	</div>
	<!--/ End Blog Grid -->

	<!-- Start Footer Area -->
	<?php include('components/footer.php') ?>
	<!-- /End Footer Area -->
	<?php include('components/script.php') ?>
</body>

</html>
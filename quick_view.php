<?php 
require_once 'db_connect.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT p.*, pi.image_path FROM product p 
    INNER JOIN product_images pi ON p.productid = pi.product_id
    WHERE p.productid = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        ?>
        <div class="row no-gutters">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <!-- Product Slider -->
                <div class="product-gallery">
                    <div class="quickview-slider-active">
                        <div class="single-slider">
                            <img src="assets/images/<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="height:510px;">
                        </div>
                        <div class="single-slider">
                            <img src="assets/images/<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="height:510px;">
                        </div>
                        <div class="single-slider">
                            <img src="assets/images/<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="height:510px;">
                        </div>
                        <div class="single-slider">
                            <img src="assets/images/<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="height:510px;">
                        </div>
                    </div>
                </div>
                <!-- End Product slider -->
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="quickview-content">
                    <h3><?= htmlspecialchars($product['productname']) ?></h3>
                    <div class="quickview-ratting-review">
                        <div class="quickview-ratting-wrap">
                            <div class="quickview-ratting">
                                <i class="yellow fa fa-star"></i>
                                <i class="yellow fa fa-star"></i>
                                <i class="yellow fa fa-star"></i>
                                <i class="yellow fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <a href="#"> (1 customer review)</a>
                        </div>
                        <div class="quickview-stock">
                            <span><i class="fa fa-check-circle-o"></i> in stock</span>
                        </div>
                    </div>
                    <p>Price: $ <?= number_format($product['sellprice'], 2) ?></p>
                    <div class="quickview-peragraph">
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?> </p>
                    </div>
                    <div class="size">
                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <h5 class="title">Size</h5>
                                <select>
                                    <option selected="selected">s</option>
                                    <option>m</option>
                                    <option>l</option>
                                    <option>xl</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-12">
                                <h5 class="title">Color</h5>
                                <select>
                                    <option selected="selected">orange</option>
                                    <option>purple</option>
                                    <option>black</option>
                                    <option>pink</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="quantity">
                        <!-- Input Order -->
                        <div class="input-group">
                            <div class="button minus">
                                <button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
                                    <i class="ti-minus"></i>
                                </button>
                            </div>
                            <input type="text" name="quant[1]" class="input-number" data-min="1" data-max="1000" value="1">
                            <div class="button plus">
                                <button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[1]">
                                    <i class="ti-plus"></i>
                                </button>
                            </div>
                        </div>
                        <!--/ End Input Order -->
                    </div>
                    <div class="add-to-cart">
                        <a href="cart.php?add=<?= $product['productid'] ?>" class="btn">Add to Cart</a>
                        <a href="#" class="btn min"><i class="ti-heart"></i></a>
                        <a href="#" class="btn min"><i class="fa fa-compress"></i></a>
                    </div>
                    <div class="default-social">
                        <h4 class="share-now">Share:</h4>
                        <ul>
                            <li><a class="facebook" href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a class="twitter" href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a class="youtube" href="#"><i class="fa fa-pinterest-p"></i></a></li>
                            <li><a class="dribbble" href="#"><i class="fa fa-google-plus"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    $stmt->close();
}
?>

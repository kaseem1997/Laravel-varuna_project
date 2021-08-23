<!DOCTYPE html>
<html>
<head>

	@include('common.head')
	 <link rel="stylesheet" type="text/css" href="{{url('public/css/owl.carousel.min.css')}}" />

</head>
<body>

	@include('common.header')

	<?php
	$BackUrl = CustomHelper::BackUrl();

	$websiteSettingsNamesArr = ['FREE_DELIVERY_AMOUNT', 'SHIPPING_CHARGE', 'SHIPPING_TEXT'];

	$websiteSettingsArr = CustomHelper::websiteSettingsArray($websiteSettingsNamesArr);

	$FREE_DELIVERY_AMOUNT = (isset($websiteSettingsArr['FREE_DELIVERY_AMOUNT']))?$websiteSettingsArr['FREE_DELIVERY_AMOUNT']->value:0;
	$SHIPPING_CHARGE = (isset($websiteSettingsArr['SHIPPING_CHARGE']))?$websiteSettingsArr['SHIPPING_CHARGE']->value:0;
	$SHIPPING_TEXT = (isset($websiteSettingsArr['SHIPPING_TEXT']))?$websiteSettingsArr['SHIPPING_TEXT']->value:'';
	

	$authCheck = auth()->check();

	$userWishlist = [];

	if($authCheck){
		$userWishlist = auth()->user()->userWishlist->keyBy('product_id');
	}


	$amountForFreeDelivery = 0;

	$totalShipping = (is_numeric($SHIPPING_CHARGE))?$SHIPPING_CHARGE:0;

	$cartContent = Cart::getContent();
	$cartTotal = Cart::getTotal($cartContent);

	$totalTax = Cart::getTax($cartContent);

	$totalWithTax = $cartTotal + $totalTax;

	if(is_numeric($FREE_DELIVERY_AMOUNT) && $totalWithTax >= $FREE_DELIVERY_AMOUNT ){
		$totalShipping = 0;
	}
	else{
		$amountForFreeDelivery = $FREE_DELIVERY_AMOUNT - $totalWithTax;
	}


	$productsSizesArr = [];

	
	?>
<!-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#cart_popup">Open Modal</button> -->




	<section class="fullwidth tabcart">
		<div class="container">
			<ul>
				<li class="active"><span><i class="cartlisticon"></i></span><strong>Bag</strong></li>
				<li><span><i class="addressicon"></i></span><strong>Address</strong></li>
				<li><span><i class="checkouticon"></i></span><strong>Checkout</strong></li>
			</ul>
		</div>
	</section>  
	
	<section class="fullwidth innerpage"> 
		<div class="container">
			<div class="sectionleft">
				<div class="offersec">
					<strong>Offers</strong>
					<ul>
						<?php
						if(!empty($SHIPPING_TEXT)){
							?>
							<li>{{$SHIPPING_TEXT}}</li>
							<?php
						}
						?>
						
					</ul>
				</div>

				<div class="freedelivery">

				<?php
				if($amountForFreeDelivery > 0){
					?>
					<i class="detailicon1"></i> Shop for ₹{{number_format($amountForFreeDelivery)}} more to get <strong>Free Delivery.</strong>
					<?php

				}
				elseif($totalShipping == 0){
					?>
					<i class="detailicon1 yay_free_delivery"></i> Yay!  <strong>Free Delivery</strong> on this order. 
					<?php
				}
				?>

				</div>


				<div class="title3">My Shopping Bag ({{$cartCount}} Items) </div>
				<ul class="cartlist">
					<?php

					//pr($cartContent->toArray());

					if(!empty($cartContent) && $cartContent->count() > 0){

						$storage = Storage::disk('public');
						$img_path = 'products/';
						$thumb_path = $img_path.'thumb/';

						foreach($cartContent as $cart){

							$cartId = $cart->id;

							$cartIdArr = explode('_', $cartId);

							$product_id = $cart->product_id;

							$product = $productModel->find($product_id);

							//$attributes = $cart->attributes;

							$productInventorySize = $product->productInventorySize;

							if(!empty($productInventorySize) && count($productInventorySize) > 0){
								//pr($productInventorySize->toArray());

								$productInventorySizeArr = $productInventorySize->sortBy('sort_order');

								foreach($productInventorySizeArr as $inventorySize){
									if($inventorySize->pivot->stock > 0){
										$productsSizesArr[$product_id][] = array(
											'size_id' => $inventorySize->id,
											'size_name' => $inventorySize->name,
											'stock' => $inventorySize->pivot->stock,
										);
									}
								}
							}

							$qty = $cart->qty;

							$sizeId = $cart->size_id;
							$sizeName = $cart->size_name;
							$clrName = $cart->color_name;

							$price = $product->price;
							$salePrice = $product->sale_price;

							$productBrand = $product->productBrand;

							$defaultImage = $product->defaultImage;
							$productImages = $product->productImages;

							$imgUrl = '';

							if(!empty($defaultImage) && count($defaultImage) > 0){
								if(!empty($defaultImage->image) && $storage->exists($thumb_path.$defaultImage->image) ){
									$imgUrl = url('public/storage/'.$thumb_path.$defaultImage->image);
								}
							}

							if(empty($imgUrl)){
								if(!empty($productImages) && count($productImages) > 0){
									$productImg = $productImages[0];
									if(!empty($productImg->image) && $storage->exists($thumb_path.$productImg->image) ){
										$imgUrl = url('public/storage/'.$thumb_path.$productImg->image);
									}
								}
							}

							$brandName = '';

							if(!empty($productBrand) && count($productBrand) > 0){
								$brandName = $productBrand->name;
							}

							$itemPrice = $price*$qty;
							$itemSalePrice = $salePrice*$qty;

							?>
							<li class="listItem">
								<a href="{{url('products/details/'.$product->slug)}}" class="cartimg">
									<?php
									if(!empty($imgUrl)){
										?>
										<img src="{{$imgUrl}}" alt="{{$product->name}}">
										<?php
									}
									?>
									
								</a>
								<div class="procont">
									<div class="titles">
										<?php
										if(!empty($brandName)){
											?>
											<p><span>{{$brandName}}</span></p>
											<?php
										}
										?>
										<p><a href="{{url('products/details/'.$product->slug)}}" target="_blank">{{$cart->product_name}}</a></p>
									</div>
									<div class="cartprice">
										<?php
										if($salePrice > 0 && $salePrice < $price){
											$discount = CustomHelper::calculateProductDiscount($itemPrice, $itemSalePrice);
											?>
											<span>₹{{number_format($itemSalePrice)}} <del>₹{{number_format($itemPrice)}}</del></span>
											<small>Saving: <cite>{{number_format($discount)}}% off</cite></small>
											<?php
										}
										else{
											?>
											<span>₹{{$itemPrice}} </span>
											<?php
										}
										?>

									</div>

									<div class="sizeqty">
										<div class="size">SIZE : <span data-cid="{{$cart->id}}" data-pid="{{$product->id}}" data-sid="{{$sizeId}}" data-qty="{{$qty}}" class="sizeList sizeQtyList">{{$sizeName}}</span> </div>
										<div class="qty">QTY : <span data-cid="{{$cart->id}}" data-pid="{{$product->id}}" data-sid="{{$sizeId}}" data-qty="{{$qty}}" class="qtyList sizeQtyList">{{$qty}}</span></div>
									</div>

									<div class="removeandwish">
										<div class="remove">
											<a href="javascript:void(0)" data-cartid="{{$cart->id}}" class="removeCartItem">
											<i class="deleteicon"></i> <span>Remove</span>
											</a>
										</div>

										<div class="wishlistmove">

											
											<?php
											//prd($userWishlist->toArray());
											if($authCheck){

												if(isset($userWishlist[$product->id]) && $userWishlist[$product->id]->size_id == $sizeId ){
													?>
													<a href="javascript:void(0)"><i class="wishlistpink"></i> <span>Wishlisted</span></a>
													<?php
												}
												else{
													?>
													<a href="javascript:void(0)" data-cid="{{$cart->id}}" class="moveToWishlist"><i class="wishlistpink"></i> <span>Move To Wishlist</span></a>
													<?php
												}
											}
											else{
												?>
												<a href="javascript:void(0)" onclick="gotoLogin()"><i class="wishlistpink"></i> <span>Move To Wishlist</span></a>
												<?php
											}
											?>

										</div>

									</div>
								</li>
								<?php
							}
						}

						//pr($productsSizesArr);

						$productsSizesJson = json_encode($productsSizesArr, JSON_FORCE_OBJECT);
						?>

					</ul>

				</div>

				<div class="sectionright">

					<div class="secures"><i class="secureimg"></i> <span>100% <small>Secure</small></span></div>
					
					<?php

					$cartProcessButton = '';
			if($authCheck){
				$cartProcessButton = '<a href="'.url('cart/address').'">Place Order</a>';
				/*?>
				<a href="{{url('cart/address')}}">Place Order</a>
				<?php*/
			}
			else{
				//$cartProcessButton = '<a href="javascript:void(0)" onClick="gotoLogin()">Place Order</a>';
				$cartProcessButton = '<a href="javascript:void(0)" class="open_slide">Place Order</a>';
				/*?>
				<a href="javascript:void(0)" onClick="gotoLogin()">Place Order</a>
				<?php*/
			}

			$couponRemovable = true;
			$showCoupon = true;
			?>

					@include('cart._price_details')

				</div>
				

			</div>
		</section>

		@include('common.footer')
<script type="text/javascript" src="{{url('public')}}/js/owl.carousel.min.js"></script> 
		@include('cart._popups')

		<div class="addedto " style="display:none;" ></div>

		<script type="text/javascript" src="{{url('public')}}/bootstrap/js/bootstrap.min.js"></script>
		
		<script type="text/javascript">

			var productsSizesJson = JSON.parse('<?php echo $productsSizesJson; ?>');

			$(".sizeQtyList").click(function(){
				//var updateModal = $("#updateModal");
				var updateModal = $("#cart_popup");

				var productId = $(this).data("pid");
				var oldSizeId = $(this).data("sid");
				var cartId = $(this).data("cid");
				var oldQty = $(this).data("qty");

				productId = parseInt(productId);
				oldSizeId = parseInt(oldSizeId);

				/*console.log("productId="+productId);
				console.log("sizeId="+sizeId);*/

				var countProductsSizesJson = Object.keys(productsSizesJson).length;
				//console.log("countProductsSizesJson="+countProductsSizesJson);

				if(countProductsSizesJson > 0){

					if(productsSizesJson[productId]){

						var sizeQtyLen = Object.keys(productsSizesJson[productId]).length;

						if(sizeQtyLen > 6){
							updateModal.find(".slider_wrap").addClass("active_slider");
						}
						else{
							updateModal.find(".slider_wrap").removeClass("active_slider");
						}

						//console.log(Object.keys(productsSizesJson[productId]).length);

						var sizeListing = '';

						$.each(productsSizesJson[productId], function(i, val){

							var sizeId = productsSizesJson[productId][i]['size_id'];
							var stock = productsSizesJson[productId][i]['stock'];

							var boxBg = "";
							var sizeChecked = "";

							if(sizeId == oldSizeId){
								boxBg = "background:#e41881;color:#fff;";
							}

							if(sizeId == oldSizeId){
								sizeChecked = "checked";
							}

							//sizeListing += '<li><a href="javascript:void(0)" data-cid="'+cartId+'" data-pid="'+productId+'" data-sid="'+productsSizesJson[productId][i]['size_id']+'" data-osid="'+sizeId+'" data-type="size" class="changeSize" >'+productsSizesJson[productId][i]['size_name']+'</a></li>';

							sizeListing += '<div class="item">';

							sizeListing += '<div class="size_box" data-cid="'+cartId+'" data-pid="'+productId+'" data-sid="'+sizeId+'" data-osid="'+oldSizeId+'" data-oqty="'+oldQty+'" style="'+boxBg+'" >';

							//sizeListing += '<a href="javascript:void(0)" data-cid="'+cartId+'" data-pid="'+productId+'" data-sid="'+productsSizesJson[productId][i]['size_id']+'" data-osid="'+sizeId+'" data-type="size" class="changeSize" >'+productsSizesJson[productId][i]['size_name']+'</a>';

							sizeListing += '<input type="radio" name="sizeId" value="'+sizeId+'" data-stock="'+stock+'" '+sizeChecked+' >';
							sizeListing += '<label>'+productsSizesJson[productId][i]['size_name']+'</label>';

							sizeListing += '</div>';
							sizeListing += '</div>';

						});

						if(sizeListing && sizeListing != ''){

							updateModal.find("input[name=productId]").val(productId);
							updateModal.find("input[name=cartId]").val(cartId);
							updateModal.find("input[name=oldSizeId]").val(oldSizeId);
							updateModal.find("input[name=oldQty]").val(oldQty);
							updateModal.find("input[name=qty]").val(oldQty);

							updateModal.find(".slider_wrap .owl-theme").html(sizeListing);
							initOwlCarousel();
						}
					}

					//updateModal.find(".modalTitle").text("Select Size");

					updateModal.modal("show");
				}

			});

			$(document).on('click', '.size_box', function(){
				$("input[name=sizeId]").prop("checked", false);

				$(this).find("input[name=sizeId]").prop("checked", true);

				$(".size_box").css({"background":"","color":"#000"});
				$(this).css({"background":"#e41881","color":"#fff"});

				var sizeStock = $("input[name=sizeId]:checked").data("stock");

				sizeStock = parseInt(sizeStock);

				var currQty = $("input[name=qty]").val();

				if(currQty > sizeStock){
					$("input[name=qty]").val(sizeStock);
				}
			});



			$(document).on('click', '.updateSizeQty', function(){
				var sizeQtyForm = $("form[name=sizeQtyForm]");

				//console.log(sizeQtyForm.serialize());

				//return false;

				var _token = '{{ csrf_token() }}';
					$.ajax({
						url: "{{url('cart/update')}}",
						type: 'post',
						data: sizeQtyForm.serialize(),
						dataType: 'JSON',
						headers:{'X-CSRF-TOKEN': _token},
						cache: false,
						crossDomain: true,
						beforeSend: function(){

						},
						success: function(resp){
							if(resp.success){
								window.location.reload();
							}
						}
					});
			});


			$(document).on('click', '.couponCodeTxt', function(e){
				e.preventDefault();

				var couponCode = $(this).text();

				if(couponCode && couponCode != ""){
					$("form[name=couponForm]").find("input[name=coupon]").val(couponCode);
				}
			});

			$(document).on('click', '#applyCoupon', function(e){
				e.preventDefault();

				var couponForm = $("form[name=couponForm]");

				var coupon = couponForm.find("input[name=coupon]").val().trim(); 
				if(coupon==''){
					alert('Please enter coupon code');
					return false;
				}

				var _token = '{{ csrf_token() }}';
				$.ajax({
					url: "{{url('cart/apply_coupon')}}",
					type: 'post',
					data: {coupon:coupon },
					dataType: 'JSON',
					headers:{'X-CSRF-TOKEN': _token},
					cache: false,
					crossDomain: true,
					beforeSend: function(){
						$("#couponpopup").find(".error").html('');
					},
					success: function(resp){
						if(resp.success){
							alert("Coupon applied successfully.");

							window.location.reload();
						}
						else if(resp.message){
							$("#couponpopup").find(".error").html(resp.message);
							//alert(resp.message);
						}
					}
				}); 


			});


			$(document).on('click', '#removeCoupon', function(){

				var _token = '{{ csrf_token() }}';

				$.ajax({
					url: "{{url('cart/remove_coupon')}}",
					type: 'post',
					data: {},
					dataType: 'JSON',
					headers:{'X-CSRF-TOKEN': _token},
					cache: false,
					crossDomain: true,
					beforeSend: function(){

					},
					success: function(resp){
						if(resp.success){
							alert("Coupon removed successfully.");

							window.location.reload();
						}
					}
				}); 


			});

			$(".removeCartItem").on("click", function(){

				var conf = confirm("Are you sure you want to remove this item?");

				if(conf){

					var cartId = $(this).data("cartid");

					var _token = '{{ csrf_token() }}';

					$.ajax({
						url: "{{ url('cart/delete') }}",
						type: "POST",
						data: {cartId:cartId},
						dataType:"JSON",
						headers:{'X-CSRF-TOKEN': _token},
						cache: false,
						beforeSend:function(){

						},
						success: function(resp){
							if(resp.success){
								window.location.reload();
							}

						}
					});

				}
				else{
					$(".sizeErr").text("Please select a size");
				}
			});

			$(".moveToWishlist").on("click", function(){
				var currSel = $(this);

				var cartId = currSel.data("cid");

				var _token = '{{ csrf_token() }}';

				$.ajax({
					url: "{{ url('users/add_to_wishlist') }}",
					type: "POST",
					data: {cartId:cartId},
					dataType:"JSON",
					headers:{'X-CSRF-TOKEN': _token},
					cache: false,
					beforeSend:function(){

					},
					success: function(resp){
						if(resp.success){

							setTimeout(function(){ window.location.reload(); }, 1000);
								
							currSel.parents("li.listItem").remove();

							var countItem = $(".listItem").length;
							if(countItem > 0){
								showAlertScc("Item has been added to wishlist.");

								if(resp.cartCount){
									$("#cart_count").text(resp.cartCount);
								}
							}
							else{
								window.location.reload();
							}
						}

					}
				});

			});

			function gotoLogin(){
				window.location = "{{url('account/login?referer='.$BackUrl)}}";
			}

			function showAlert(msg, type){
				if(msg && msg != ""){

					var alertClass = 'alert';

					if(type && type!= ""){
						alertClass += ' alert-'+type;
					}

					var message = '<div class="'+alertClass+'"> '+msg+' </div>';

					$("#alertModal").find(".modal-content").html(message);
					$("#alertModal").modal("show");
				}
			}

			function showAlertScc($msg){

				if($msg && $msg != ""){
					$(".addedto").html($msg);
					$(".addedto").show();

					setTimeout(function(){ $(".addedto").hide(); }, 2000);
				}
			}



		</script>

		<script type="text/javascript">
			$('.add').click(function () {
				var qtyStock = $("input[name=sizeId]:checked").data("stock");

				qtyStock = parseInt(qtyStock);

				if ($(this).prev().val() > qtyStock) {
					$(this).prev().val(qtyStock);
				}

				if ($(this).prev().val() < qtyStock) {
					$(this).prev().val(+$(this).prev().val() + 1);
				}
			});
			$('.sub').click(function () {
				if ($(this).next().val() > 1) {
					if ($(this).next().val() > 1) $(this).next().val(+$(this).next().val() - 1);
				}
			});

			function initOwlCarousel(){

				$('.owl-carousel').owlCarousel('destroy');

				$('.owl-carousel').owlCarousel({
					//loop:false,
					margin:10,
					dots:false,
					mouseDrag: false,
					nav:true,
					responsive:{
						0:{
							items:3
						},
						600:{
							items:3
						},
						1000:{
							items:6
						}
					}
				});
			}

			//initOwlCaeousel();
		</script>
	</body>
	</html>
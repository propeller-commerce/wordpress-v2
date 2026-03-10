<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!DOCTYPE html>
<html lang="nl" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
	xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="x-ua-compatible" content="ie=edge" />
	<title><?php echo esc_html(__('Return request', 'propeller-ecommerce-v2')); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<!--[if mso]>
    <style type="text/css">
      body, table, td {
        font-family: Arial, sans-serif !important;
      }
    </style>
  <![endif]-->

	<style type="text/css">
		/* -------------------------------------
        GLOBAL RESETS
    ------------------------------------- */
		body,
		table,
		td,
		p {
			margin: 0;
			padding: 0;
		}

		body,
		td,
		th {
			font-family: Arial, sans-serif;
			font-size: 16px;
			color: #333333;
			line-height: 1.5;
		}

		body {
			background-color: #f5f5f5;
		}

		* {
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
		}

		/* -------------------------------------
        RESPONSIVE WRAPPER
    ------------------------------------- */
		.email-container {
			max-width: 600px;
			width: 100%;
			margin: 0 auto;
			background-color: #ffffff;
		}

		.content {
			padding: 20px;
		}

		.footer {
			font-size: 12px;
			color: #777777;
			text-align: center;
			padding: 10px 20px;
		}

		/* -------------------------------------
        MEDIA QUERIES
    ------------------------------------- */
		@media only screen and (max-width: 600px) {
			.content {
				padding: 10px;
			}
		}
	</style>
</head>

<body>
	<!-- Outer Wrapper Table -->
	<table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
		<tr>
			<td align="center">
				<!-- Inner Container -->
				<table class="email-container" border="0" cellpadding="0" cellspacing="0" role="presentation">
					<!-- Header Section -->
					<tr>
						<td style="background-color: #2f99dd; color: #ffffff; text-align: center; padding: 20px;">
							<h1 style="margin: 0; font-size: 24px;"><?php echo esc_html(__('Return request', 'propeller-ecommerce-v2')); ?></h1>
						</td>
					</tr>

					<!-- Body Content Section -->
					<tr>
						<td class="content">
							<!-- Salutation -->
							<p style="font-size: 16px;">
								<?php echo esc_html(__("Hello", 'propeller-ecommerce-v2')); ?> <strong><?php echo esc_html($args['return_contact']); ?></strong>,</p>

							<!-- Intro/Explanation -->
							<p style="font-size: 16px;"><?php echo esc_html(__('Thank you for your return request on order number', 'propeller-ecommerce-v2')); ?> <strong><?php echo esc_html($args['return_order']); ?></strong>.</p>
							<p style="margin-bottom: 20px;font-size: 16px;"><?php echo esc_html(__('Below you will find the information as it is known to us and an overview of your return.', 'propeller-ecommerce-v2')); ?></p>


							<?php
							$childrenMap = [];

							if (!empty($args['child_product_parent']) && !empty($args['child_product_name'])) {
								foreach ($args['child_product_parent'] as $childId => $parentId) {
									if (isset($args['child_product_name'][$childId])) {
										$childrenMap[$parentId][] = $args['child_product_name'][$childId];
									}
								}
							}
							?>

							<!-- Returned Products -->
							<div style="margin-top: 10px;">

								<?php foreach ($args['return-product'] as $productId) { ?>
									<div style="padding: 15px 0;">
										<!-- Parent Product -->
										<div style="margin-bottom: 5px;">
											<strong><?php echo esc_html(__('Returned product', 'propeller-ecommerce-v2')); ?></strong><br>
											<?php echo esc_html($args['return_quantity'][$productId]); ?> x <?php echo esc_html($args['product_name'][$productId]); ?>
										</div>

										<!-- Child Products -->
										<?php if (!empty($childrenMap[$productId])) { ?>
											<ul style="padding-left: 20px; margin: 5px 0;">
												<?php foreach ($childrenMap[$productId] as $childName) { ?>
													<li style="font-size: 13px; color: #555;"><?php echo esc_html($childName); ?></li>
												<?php } ?>
											</ul>
										<?php } ?>

										<!-- Return Details -->
										<div style="margin-top: 10px;">
											<div><strong><?php echo esc_html(__('Package opened', 'propeller-ecommerce-v2')); ?></strong><br> <?php if ($args['return_package'][$productId] === 'Y') echo esc_html(__('Yes', 'propeller-ecommerce-v2'));
																																				else if ($args['return_package'][$productId] === 'N') echo esc_html(__('No', 'propeller-ecommerce-v2'));
																																				else echo esc_html($args['return_package'][$productId]); ?></div>
											<div><strong><?php echo esc_html(__('Return reason', 'propeller-ecommerce-v2')); ?></strong><br> <?php echo esc_html($args['return_reason_text'][$productId]); ?></div>

											<?php if (!empty($args['return_other'][$productId])) { ?>
												<div><strong><?php echo esc_html(__('Return reason other', 'propeller-ecommerce-v2')); ?></strong><br> <?php echo esc_html($args['return_other'][$productId]); ?></div>
											<?php } ?>
										</div>
									</div>
								<?php } ?>
							</div>

							<!-- Other Comment -->
							<?php if (!empty($args['return_comment'])) { ?>
								<div style="margin-top: 10px; margin-bottom: 20px;">
									<strong><?php echo esc_html(__('Other comment', 'propeller-ecommerce-v2')); ?></strong><br />
									<div style="margin-top: 5px;"><?php echo esc_html($args['return_comment']); ?></div>
								</div>
							<?php } ?>


							<!-- Additional Info -->
							<p style="margin-bottom: 20px;">
								<?php echo esc_html(__('We will contact you as soon as possible about the further handling of your return.', 'propeller-ecommerce-v2')); ?><br><br>

								<?php echo esc_html(__('With kind regards', 'propeller-ecommerce-v2')); ?>,
								<br><?php echo esc_html(get_bloginfo('name')); ?><br>
							</p>


						</td>
					</tr>

					<!-- Footer Section -->
					<tr>
						<?php
						function get_custom_logo_url()
						{
							$custom_logo_id = get_theme_mod('custom_logo');
							$image = wp_get_attachment_image_src($custom_logo_id, 'full');
							return $image[0];
						}
						?>
						<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
							<tr>

								<!-- Coll 1 -->
								<td style="width: 75%; text-align: left; padding: 10px;">
									<p style="margin: 0;"><small>&copy; <?php echo esc_html(do_shortcode('[current_year]')); ?></small><small>&nbsp;<a href="http://propeller-commerce.com">propeller-commerce.com</a></small></p>
								</td>
								<!-- Coll 2 -->
								<td style="width: 25%; text-align: right; padding: 10px;">
									<img src="<?php echo esc_url(get_custom_logo_url()); ?>" border="0" width="100%" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
								</td>

							</tr>
						</table>
					</tr>
				</table>
				<!-- End Inner Container -->
			</td>
		</tr>
	</table>
</body>

</html>
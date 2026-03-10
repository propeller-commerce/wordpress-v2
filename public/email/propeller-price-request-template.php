<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!DOCTYPE html>
<html lang="nl" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
	xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="x-ua-compatible" content="ie=edge" />
	<title><?php echo esc_html(__('Your price request', 'propeller-ecommerce-v2')); ?></title>
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
							<h1 style="margin: 0; font-size: 24px;"><?php echo esc_html(__('Your price request', 'propeller-ecommerce-v2')); ?></h1>
						</td>
					</tr>

					<!-- Body Content Section -->
					<tr>
						<td class="content">
							<!-- Intro/Explanation -->
							<p style="font-size: 16px;"><?php echo esc_html(__('Thank you for your price request.', 'propeller-ecommerce-v2')); ?></p>
							<p style="margin-bottom: 20px;font-size: 16px;"><?php echo esc_html(__('We will contact you as soon as possible about the requested pricings.', 'propeller-ecommerce-v2')); ?></p>



							<!-- Returned Products -->
							<div style="margin-top: 10px;">

								<div style="margin-bottom: 26px;font-size: 14px;color: #333333;">
									<b><?php echo esc_html(__('E-mail', 'propeller-ecommerce-v2')); ?></b>: <?php echo esc_html($user_email); ?><br />
									<b><?php echo esc_html(__('Name', 'propeller-ecommerce-v2')); ?></b>: <?php
																											if (isset($user_data) && $user_data) {
																												$name_parts = array_filter([$user_data->firstName ?? '', $user_data->middleName ?? '', $user_data->lastName ?? '']);
																												echo esc_html(join(' ', $name_parts));
																											}
																											?><br />
									<b><?php echo esc_html(__('Company', 'propeller-ecommerce-v2')); ?></b>: <?php echo esc_html($user_data->company->name); ?><br />
									<?php if ($user_data->__typename === 'Contact' && !empty($user_data->company->debtorId)) { ?>
										<b><?php echo esc_html(__('Cust. Number', 'propeller-ecommerce-v2')); ?></b>: <?php echo esc_html($user_data->company->debtorId); ?>
									<?php } else if ($user_data->__typename === 'Customer' && !empty($user_data->debtorId)) { ?>
										<b><?php echo esc_html(__('Cust. Number', 'propeller-ecommerce-v2')); ?></b>: <?php echo esc_html($user_data->debtorId); ?>
									<?php } ?>
								</div>

								<?php
								foreach ($data['product-code-row'] as $index => $product) {
									if (empty($product))
										continue;
								?>
									<div style="margin-bottom: 10px;">
										<strong><?php echo esc_html(__('Requested product', 'propeller-ecommerce-v2')); ?></strong><br>
										<div style="margin-bottom: 0;font-size: 14px;color: #333333;"><?php echo esc_html($data['product-quantity-row'][$index]); ?> x <?php echo esc_html($data['product-code-row'][$index] . ' / ' . $data['product-name-row'][$index]); ?></div>

									</div>
								<?php } ?>
								<?php if (!empty($data['request_comment'])) { ?>
									<div style="font-size: 14px;"><b><?php echo esc_html(__('Comments', 'propeller-ecommerce-v2')); ?></b>: <?php echo esc_html($data['request_comment']); ?></div>
								<?php } ?>
							</div>




							<!-- Additional Info -->
							<p style="margin-bottom: 20px; margin-top: 20px;">


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
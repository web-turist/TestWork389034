<?php

add_action('wp_enqueue_scripts', 'my_scripts');

function my_scripts()
{
	wp_enqueue_style('main_style', get_template_directory_uri() . '/assets/css/style.min.css', null, null, 'all');
	wp_enqueue_style('chatbot_style', get_template_directory_uri() . '/chatbot/chatbot.css', null, null, 'all');
	// wp_enqueue_style('style_woocommerce', get_template_directory_uri() . '/chatbot/style_for_test_woo.css', null, null, 'all');

	wp_enqueue_script('fp2', get_template_directory_uri() . '/chatbot/fp2.js', array(), null, ['strategy' => 'defer']);
	wp_enqueue_script('chatbot', get_template_directory_uri() . '/chatbot/chatbot.js', array(), null, ['strategy' => 'defer']);
	wp_enqueue_script('config_script', get_template_directory_uri() . '/chatbot/config_script.js', array(), null, ['strategy' => 'defer']);
	wp_enqueue_script('cards_script', get_template_directory_uri() . '/assets/js/script.min.js', array(), null, ['strategy' => 'defer']);
}


add_theme_support('post-thumbnails');
add_theme_support('title-tag');
add_theme_support('custom-logo');

add_filter('upload_mimes', 'svg_upload_allow');

## Добавляет SVG в список разрешенных для загрузки файлов.
function svg_upload_allow($mimes)
{
	$mimes['svg']  = 'image/svg+xml';

	return $mimes;
}

add_filter('wp_check_filetype_and_ext', 'fix_svg_mime_type', 10, 5);

## Исправление MIME типа для SVG файлов.
function fix_svg_mime_type($data, $file, $filename, $mimes, $real_mime = '')
{

	// WP 5.1 +
	if (version_compare($GLOBALS['wp_version'], '5.1.0', '>=')) {
		$dosvg = in_array($real_mime, ['image/svg', 'image/svg+xml']);
	} else {
		$dosvg = ('.svg' === strtolower(substr($filename, -4)));
	}
	// mime тип был обнулен, поправим его
	// а также проверим право пользователя
	if ($dosvg) {
		// разрешим
		if (current_user_can('manage_options')) {

			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}
		// запретим
		else {
			$data['ext']  = false;
			$data['type'] = false;
		}
	}
	return $data;
}

## Создание нового типа записи "WEBprojects"
add_action('init', 'register_post_types');

function register_post_types()
{
	register_post_type('WEBprojects', [
		'label'  => null,
		'labels' => [
			'name'               => 'WEBprojects', // основное название для типа записи
			'singular_name'      => 'проект', // название для одной записи этого типа
			'add_new'            => 'Добавить проект', // для добавления новой записи
			'add_new_item'       => 'Добавление проект', // заголовка у вновь создаваемой записи в админ-панели.
			'edit_item'          => 'Редактирование проект', // для редактирования типа записи
			'new_item'           => 'Новый проект', // текст новой записи
			'view_item'          => 'Смотреть проект', // для просмотра записи этого типа.
			'search_items'       => 'Искать проект', // для поиска по этим типам записи
			'not_found'          => 'Не найдено', // если в результате поиска ничего не было найдено
			'not_found_in_trash' => 'Не найдено в корзине', // если не было найдено в корзине
			'parent_item_colon'  => '', // для родителей (у древовидных типов)
			'menu_name'          => 'Проекты WEB', // название меню
		],
		'description'            => '',
		'public'                 => true,
		'show_in_menu'           => null, // показывать ли в меню админки
		'show_in_rest'        => null, // добавить в REST API. C WP 4.7
		'rest_base'           => null, // $post_type. C WP 4.7
		'menu_position'       => null,
		'menu_icon'           => "dashicons-networking",
		'hierarchical'        => false,
		'supports'            => ['title', 'thumbnail', 'editor', 'custom-fields'], // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
		'taxonomies'          => [],
		'has_archive'         => false,
		'rewrite'             => true,
		'query_var'           => true,
	]);
}

// подключаем функцию активации мета блока (my_extra_fields)
add_action('add_meta_boxes', 'my_extra_fields', 1);

function my_extra_fields()
{
	add_meta_box(
		'extra_fields',
		'Дополнительные поля',
		'extra_fields_box_func',
		'WEBprojects',
		'normal',
		'high'
	);
}


// код блока
function extra_fields_box_func($post)
{

	$extra = [
		'stack',
		'preview_link'
	]
?>
	<p>
	<div>Укажите стек технологий</div>
	<label><input type="text" name="extra[stack]" value="<?php echo get_post_meta($post->ID, 'stack', 1); ?>" style="width:50%" /></label>
	</p>

	<p>
	<div>Укажите визуальный вид ссылки</div>
	<label><input type="text" name="extra[preview_link]" value="<?php echo get_post_meta($post->ID, 'preview_link', 1); ?>" style="width:50%" /></label>
	</p>

	<input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
<?php
}

// включаем обновление полей при сохранении
add_action('save_post', 'my_extra_fields_update', 0);

// Сохраняем данные, при сохранении поста
function my_extra_fields_update($post_id)
{
	// базовая проверка
	if (
		empty($_POST['extra'])
		|| !wp_verify_nonce($_POST['extra_fields_nonce'], __FILE__)
		|| wp_is_post_autosave($post_id)
		|| wp_is_post_revision($post_id)
	)
		return false;

	// Все ОК! Теперь, нужно сохранить/удалить данные
	$_POST['extra'] = array_map('sanitize_text_field', $_POST['extra']); // чистим все данные от пробелов по краям
	foreach ($_POST['extra'] as $key => $value) {
		if (empty($value)) {
			delete_post_meta($post_id, $key); // удаляем поле если значение пустое
			continue;
		}

		update_post_meta($post_id, $key, $value); // add_post_meta() работает автоматически
	}

	return $post_id;
}

## Реализация меню
add_action('after_setup_theme', function () {
	register_nav_menus([
		'top_menu' => 'Верхнее меню',
		'bottom_menu' => 'Нижнее меню'
	]);
});

## Подключение своего класса пунктов меню
add_filter('nav_menu_css_class', 'menu_items_class', 10, 2);
function menu_items_class($classes, $item)
{
	$classes[] = 'menu__item';
	return $classes;
}


############################----Ниже находится код, который имеет отношение к тестовому заданию Abelohost.com----#################

add_action('admin_enqueue_scripts', 'add_admin_scripts', 10, 1);
function add_admin_scripts($hook)
{
	global $post;

	if ($hook == 'post-new.php' || $hook == 'post.php') {
		if ('product' === $post->post_type) {
			wp_enqueue_script('cards_script', get_template_directory_uri() . '/assets/js/script_admin.js', array(), null, ['strategy' => 'defer']);
		}
	}
}

//Создание полей на странице продукта
add_action('woocommerce_product_options_general_product_data', 'woo_add_custom_fields');
function woo_add_custom_fields()
{
	// Группа полей
	echo '<div class="options_group">';

	//поле select
	woocommerce_wp_select(array(
		'id'       => '_select',
		'type'     => 'select',
		'label'    => 'Уникальность товара',
		'options'  => array(
		'Rare'     => __('Rare', 'woocommerce'),
		'Frequent' => __('Frequent', 'woocommerce'),
		'Unusuali' => __('Unusuali', 'woocommerce'),
		),
	));

	//поле даты
	woocommerce_wp_text_input([
		'id'    => '_adding_date',
		'label' => 'Adding date',
		'type'  => 'date'
	]);

	//дополнительная картинка
	$product = wc_get_product();
	$img = $product->get_image([500, 500], ['class' => 'image_for_delete']);
    ?>
	<div class="image_for_delete">
		<?php echo $img ?>
	</div>
    <?php

	echo '<button type="button" class="delete_img_button">Удалить картинку</button>';
	echo '<button type="button" class="add_img_button">Вернуть картинку</button>';

	echo '</div>';
}

//Сохранение данных созданных полей
add_action('woocommerce_process_product_meta', 'woo_save_custom_fields', 10);
function woo_save_custom_fields($post_id)
{
	$product = wc_get_product($post_id);

	$select_field = isset($_POST['_select']) ? sanitize_text_field($_POST['_select']) : '';
	$product->update_meta_data('_select', $select_field);

	$adding_date = isset($_POST['_adding_date']) ? sanitize_text_field($_POST['_adding_date']) : '';
	$product->update_meta_data('_adding_date', $adding_date);

	$product->save();
}

//Выводим вёрстку
function woo_custom_loop()
{
?>
	<div class="container">
		<button class="cards__button btn-modal">Add product</button>
		<div class="cards__wrapper">
			<?php
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => -1
			);
			$loop = new WP_Query($args);
			if ($loop->have_posts()) {
				while ($loop->have_posts()) : $loop->the_post();
					$product = wc_get_product();
					?>
						<div class="card">
							<div class="card__wrapper">
								<div class="card__img">
									<img src="<?php $img_id = $product->get_image_id();
												echo wp_get_attachment_url($img_id) ?>" alt="mouse">
								</div>

								<div class="details">
									<a class="card__title" href="#"><?php echo $product->get_title(); ?></a>
									<div class="card__prices">
										<div class="card__price"><?php echo $product->get_sale_price(); ?></div>
										<div class="card__discount">/<span> <?php echo $product->get_regular_price(); ?> </span></div>
									</div>
									<div class="card__uniqueness"><?php echo $product->get_meta('_select', true); ?></div>
									<div class="card__date"> <?php echo $product->get_meta('_adding_date'); ?> </div>
								</div>

							</div>
						</div>
					<?php
				endwhile;
			} else {
				echo __('There are no products in the catalogue.');
			}
				wp_reset_postdata();
			?>
		</div>
	</div>
<?php
}

add_action('wp', 'add_woo_custom_loop');
function add_woo_custom_loop()
{
	if (is_page( 'katalog' ) ) {
	add_action('woocommerce_after_shop_loop', 'woo_custom_loop');
	}
}


add_action('woocommerce_after_main_content', 'adding_form_in_katalog');
function adding_form_in_katalog()
{

?>
	<section class="contact-form">
		<div class="substrate">
			<form class="form" method="post">
				<div class="form__title">Adding Product</div>
				<div class="form__inputs">
					<input class="form__input input-name" type="text" name="product-name" placeholder="Product name">
					<input class="form__input input-price" type="text" name="product-price" placeholder="Product price">
					<input class="form__input input-discount" type="text" name="product-discount" placeholder="Product price with discount">
					<input class="form__input input-img" type="file" name="product-img" multiple>
					<input class="form__input input-date" type="date" name="adding-date">
					<select class="form__input" name="uniqueness" id="uniqueness-select">
						<option value="rare">Rare</option>
						<option value="frequent">Frequent</option>
						<option value="unusual">Unusual</option>
					</select>
					<input type="hidden" name="action" value="bots">
				</div>
				<input class="form__btn" type="submit" value="Submit">
				<div class="form__checkbox">
					<input class="form__checkbox-input" type="checkbox" name="checkbox" id="checkbox" checked required>
					<label class="form__checkbox-label" for="checkbox">I give my consent to the processing of personal data.</label>
				</div>
			</form>
		</div>
	</section>
<?php
}


//https://thisisbot.ru/wp-json/my_route/v1/add_product
add_action( 'rest_api_init', 'my_woo_routes' );
function my_woo_routes()
{
    register_rest_route( 'my_route/v1', '/add_product', array(
        'methods'  => 'POST',
        'callback' => 'woo_add_prodact',
    ) );
}

function woo_add_prodact()
{
	if (isset($_POST)) {
		$data = json_decode(file_get_contents('php://input'), true) ;
		print_r($data);



		$objProduct = new WC_Product();

		$objProduct->set_name($data['productName']);
		$objProduct->set_status('publish');
		$objProduct->set_catalog_visibility('visible');
		$objProduct->set_description('Product Description');
		$objProduct->set_sku('');
		$objProduct->set_price($data['productDiscount']);
		$objProduct->set_regular_price($data['productPrice']);
		$objProduct->set_manage_stock(true);
		$objProduct->set_stock_quantity(17);
		$objProduct->set_stock_status('instock');
		$objProduct->set_backorders('no');
		$objProduct->set_reviews_allowed(true);
		$objProduct->set_sold_individually(false);
		$objProduct->set_category_ids(array(3));


		function uploadMedia($image_url)
		{
			require_once('wp-admin/includes/image.php');
			require_once('wp-admin/includes/file.php');
			require_once('wp-admin/includes/media.php');
			$media = media_sideload_image($image_url, 0);
			$attachments = get_posts(array(
				'post_type' => 'attachment',
				'post_status' => null,
				'post_parent' => 0,
				'orderby' => 'post_date',
				'order' => 'DESC'
			));
			return $attachments[0]->ID;
			}

			$productImagesIDs = array();
			$images = array('');

			foreach($images as $image)
			{
				$mediaID = uploadMedia($image);
				if($mediaID) {
					$productImagesIDs[] = $mediaID;
				}
			}
			if($productImagesIDs) {
				$objProduct->set_image_id($productImagesIDs[0]);

			if(count($productImagesIDs) > 1) {
				$objProduct->set_gallery_image_ids($productImagesIDs);
			}
		}

		$product_id = $objProduct->save();





	}

    return rest_ensure_response( 'Hello World, this is the WordPress REST API' );
}

?>

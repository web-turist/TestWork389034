<?php

add_action('wp_enqueue_scripts', 'my_scripts');

function my_scripts()
{
	wp_enqueue_style('main_style', get_template_directory_uri() . '/assets/css/style.min.css', null, null, 'all');
	wp_enqueue_style('chatbot_style', get_template_directory_uri() . '/chatbot/chatbot.css', null, null, 'all');

	wp_enqueue_script('fp2', get_template_directory_uri() . '/chatbot/fp2.js', array(), null, ['strategy' => 'defer']);
    wp_enqueue_script('chatbot', get_template_directory_uri() . '/chatbot/chatbot.js', array(), null, ['strategy' => 'defer']);
    wp_enqueue_script('config_script', get_template_directory_uri() . '/chatbot/config_script.js', array(), null, ['strategy' => 'defer']);
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

## Сохраняем данные, при сохранении поста
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

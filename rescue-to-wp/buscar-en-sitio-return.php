<?php
include_once('../simple_html_dom.php');

// scraping_generic('https://www.fondounido.org.mx/blog5', 'div.card a' );

function scraping_generic( $url, $search ) {

	$return = false;
	$html   = file_get_html( $url );
	$matriz = array();

	// get article block
	foreach( $html->find( $search ) as $found ) {
		$return - true;
		// nueva url anidada.
		$nu_url = $found->href;
		$the_html = file_get_html($nu_url);

		// 1. Funcion, buscar titulo.
		// $the_title = $the_html->find( 'h1', 1 )->plaintext;
		// correccion para historias.
		$the_title = $the_html->find( 'h1', 0 )->plaintext;

		// 1.2 convertir titulo a nicename.
		$the_nicename = ekiline_cleanspchar($the_title);

		// 1.3 crear ID random.
		$the_id = rand(1000,10000);

		// 2. Funcion, buscar imagen principal.
		// $img_obj     = 'div.page-content h1 img[src]';
		// $img_obj_url = $the_html->find( $img_obj, 0 )->src;
		// corrección para links de BLOG, halar cualquier imagen dentro de post, particularmente la ultima que halle.
		// $img_obj_url = $the_html->find( 'div.page-content img', -1)->src;
		// correccion para historias.
		$img_obj_url = $the_html->find( 'section[style*=background-image]', 0);
		$img_obj_url = get_string_between($img_obj_url, 'url(\'', '\')');

		// 3. Function, buscar contenido: contenedor > primer div > ultimo div.
		// $filter_content = $the_html->find( 'div.page-content', 0 )->find( 'div', 1 )->find( 'div', -1 )->innertext;
		// corrección para links de BLOG, hacer más preciso el contenido.
		// $filter_content = $the_html->find( 'div.page-content div div', 0 )->find( 'div', 2 )->innertext;
		// correccion para historias.
		$filter_content = $the_html->find( '.body-min-height', 0 )->find( '.container', 1 )->find( 'div', 0 )->innertext;

		// $the_content = str_get_html( $filter_content );
		// // limpiar.
		// foreach ( $the_content->find( 'h1, img, .carousel-item, .mceEditable, .mceNonEditable' ) as $unwanted ) {
		// 	$unwanted->outertext = '';
		// }
		$the_content = str_get_html( $filter_content );
		// limpiar contenido no deseado, elementos de bloque.
		foreach ( $the_content->find( 'h1, img' ) as $unwanted ) {
			$unwanted->outertext = '';
		}
		// limpiar contenido no deseado, atributos.
		foreach ( $the_content->find('p, ul, li, h1, h2, h3, h4, h5, h6 div') as $clean_item ) {
			$clean_item->style = null;
			$clean_item->class = null;
		}
		$the_content->load( $the_content->save() );

		// resultado.
		// echo '<div style="border-bottom:red 1px solid;padding:10px;">';
		// echo '<code>' . $the_id . '</code>';
		// echo '<code>' . $the_nicename . '</code>';
		// echo '<h1>' . $the_title . '</h1>';
		// echo '<img src="' . $img_obj_url . '" width="100px" height="auto">';
		// echo $the_content;
		// echo '</div>';
		$matriz[] = [ 'id' => $the_id, 'nicename' => $the_nicename, 'title' => $the_title, 'content' => $the_content, 'img_url' => $img_obj_url ];
	}

	// print_r($matriz);
	return $matriz;

	// clean up memory
	$html->clear();
	unset($html);

	return $return;
}

function ekiline_cleanspchar($text) {

    setlocale(LC_ALL, 'en_US.UTF8');
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $alias = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $text);
    $alias = strtolower(trim($alias, '-'));
    $alias = preg_replace("/[\/_|+ -]+/", "-", $alias);

    while (substr($alias, -1, 1) == "-") {
        $alias = substr($alias, 0, -1);
    }
    while (substr($alias, 0, 1) == "-") {
        $alias = substr($alias, 1, 100);
    }

    return $alias;
}

function get_string_between($string, $start, $end){
	$string = ' ' . $string;
	$ini = strpos($string, $start);
	if ($ini == 0) return '';
	$ini += strlen($start);
	$len = strpos($string, $end, $ini) - $ini;
	return substr($string, $ini, $len);
}
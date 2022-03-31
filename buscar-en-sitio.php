<?php
include_once('simple_html_dom.php');

// //Es posible que cuando pruebes este proxy no este activo, en caso de ser asi, busca uno
// //que si lo este en cualquier pagina de proxies gratuitos en internet
// $proxyurl = '88.198.50.103:8080';

// $context = stream_context_create();
// stream_context_set_params($context, array(
// 		'proxy' => $proxyurl,
// 		'ignore_errors' => true,
// 		'max_redirects' => 3)
// 		);

// $html = file_get_html('http://localhost/wpdev/ekiline/blog/', 0, $context);

// $articles_titles = $html->find('h2');

// foreach($articles_titles as $article_title) {
// 	echo $article_title->plaintext . "\n\n";
// }

// $html->clear();
// unset($html);

// scraping_generic('http://localhost/wpdev/ekiline/blog/', 'h2 a' );

scraping_generic('https://www.fondounido.org.mx/blog5', 'div.card a' );

function scraping_generic( $url, $search ) {

	$return = false;
	$html = file_get_html( $url );

	// get article block
	foreach( $html->find( $search ) as $found ) {
		$return - true;
		// nueva url anidada.
		$nu_url = $found->href;
		$the_html = file_get_html($nu_url);

		// 1. Funcion, buscar titulo.
		$the_title = $the_html->find( 'h1', 1 )->plaintext;

		// 2. Funcion, buscar imagen principal.
		$img_obj     = 'div.page-content h1 img[src]';
		$img_obj_url = $the_html->find( $img_obj, 0 )->src;

		// 3. Function, buscar contenido: contenedor > primer div > ultimo div.
		$filter_content = $the_html->find( 'div.page-content', 0 )->find( 'div', 1 )->find( 'div', -1 )->innertext;

		$content = str_get_html( $filter_content );
		// limpiar.
		foreach ( $content->find( 'h1, img, .carousel-item, .mceEditable' ) as $unwanted ) {
			$unwanted->outertext = '';
		}
		$content->load( $content->save() );

		// resultado.
		echo '<div style="border-bottom:red 1px solid;padding:10px;">';
		echo '<h1>' . $the_title . '</h1>';
		echo '<img src="' . $img_obj_url . '" width="100px" height="auto">';
		echo $content;
		echo '</div>';

	}

	// clean up memory
	$html->clear();
	unset($html);

	return $return;
}


<?php
include_once('../simple_html_dom.php');

$urls = [
	'https://www.fondounido.org.mx/blog5',
	'https://www.fondounido.org.mx/blog4',
	'https://www.fondounido.org.mx/blog3',
	'https://www.fondounido.org.mx/blog2',
	'https://www.fondounido.org.mx/blog1',
	'https://www.fondounido.org.mx/blog',
];

foreach( $urls as $img_item ) {
	scraping_generic( $img_item, 'div.card a' );
}


function scraping_generic( $url, $search ) {

	$return = false;
	$html = file_get_html( $url );

	// get article block
	foreach( $html->find( $search ) as $found ) {
		$return - true;
		// nueva url anidada.
		$nu_url = $found->href;
		$the_html = file_get_html($nu_url);

		// 2. Funcion, buscar imagen principal.
		// $img_obj     = 'div.page-content h1 img[src]';
		// $img_obj_url = $the_html->find( $img_obj, 0 )->src;
		// corrección para links de BLOG, halar cualquier imagen dentro de post, particularmente la ultima que halle.
		$img_obj_url = $the_html->find( 'div.page-content img', -1)->src;

		// resultado.
		echo '<div style="border-bottom:red 1px solid;padding:10px;">';
		echo '<img src="' . $img_obj_url . '" width="100px" height="auto">';
		echo '</div>';
	}

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
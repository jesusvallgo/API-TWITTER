<?php
	/* Especifica si debe mostrar los errores o no */
	ini_set('display_errors', 1);

	/* Importa la clase que interactua con Twitter */
	require_once('TwitterAPIExchange.php');

	/* Clase para realizar acciones en Twitter */
	class Twitter{
		/* Clase para obtener los tweets del timeline de la cuenta asociada */
		function getTweets($user,$list,$numTweets){
			$peticion = null;

			/* Parametros de autenticacion de la aplicacion de Twetter */
			$settings = array(
				'oauth_access_token' => "4038782833-UZ8pLocc1mxAhASEi7oQa8e3tenCgGHK2feNh9F",
				'oauth_access_token_secret' => "hjG2zVHs59nKo4tISJJWF532VhrtK68zoQddNG8KAe0ZM",
				'consumer_key' => "Zf1DO4AOIaavguF9JRw0tQijo",
				'consumer_secret' => "4eWaOt4dDiHWWGVL0BUA9RuUN8wQB0jnccuzZtgdk8dVonQCEF"
			);

			if( empty($user) && empty($list) ){
				/* Se debe de obtener todos los tweets del timeline */
				$url = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
				/* Se restringe el numero de tweets a obtener */
				$getfield = '?count='.$numTweets.'&include_rts=false';
			}
			elseif( !empty($user) && !empty($list) ){
				/* Se debe de obtener los tweets de la lista especificada */
				$url = 'https://api.twitter.com/1.1/lists/statuses.json';
				/* Se restringe el numero de tweets a obtener */
				$getfield = '?slug='.$list.'&owner_screen_name='.$user.'&count='.$numTweets.'&include_rts=false';
			}
			elseif( !empty($user) && empty($list) ){
				/* Se debe de obtener solo tweets del usuario especificado */
				$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
				/* Se restringe el usuario y el numero de tweets a obtener */
				$getfield = '?screen_name='.$user.'&count='.$numTweets.'&include_rts=false';
			}

			/* Se debe de obtener los tweets de la lista especificada */
			#$url = 'https://api.twitter.com/1.1/search/tweets.json';
			/* Se restringe el numero de tweets a obtener */
			#$getfield = '?q=%23superbowl&result_type=recent';

			if( !empty($url) ){
				/* Se especifica el metodo para la peticion */
				$requestMethod = 'GET';

				/* Se crea la instancia de la clase con los parametros especificados */
				$twitter = new TwitterAPIExchange($settings);

				/* Se realiza la peticion */
				$peticion =  $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();
			}

			/* Se retorna el resultado de la peticion en formato json */
			return $peticion;
		}
	}

	/* Se crea la instancia para realizar acciones con Twitter */
	$twitterObject = new Twitter();

	/* Se realiza la peticion de los tweets */
	# Si se dejan los parametros vacios se obtienen los tweets del timeline
	# Si se especifican valores en ambos parametros se obtienen los tweets de la lista especificada
	# Si se especifica valor en el primer parametro se obtienen los tweets del usuario especificado
	#$peticionjson =  $twitterObject->getTweets("Mauricio_x_O","","30");
	$peticionjson =  $twitterObject->getTweets("sismos2015","","30");
	#print_r($peticionjson);

	if( empty($peticionjson) ){
		$imagenTwitter[0] = "<div><span>No se pudo</span></div>";
		$imagenTwitter[1] = "<div><span>leer el álbum</span></div>";
		$imagenTwitter[2] = "<div><span>de imágenes</span></div>";
	}
	else{
		/* Se pasa el resultado de formato json a arreglo */
		$json = json_decode($peticionjson);

		/* Inicializa el contador de imagenes */
		$arregloImagenes=0;
		for($i=0; $i<count($json); $i++){
			/* Recorre cada resultado obtenido */
			$tweet = $json[$i];

			#if( $tweet->extended_entities->media ){
			if( isset($tweet->extended_entities->media) ){
				/* Si existen elementos multimedia */

				for($contMedia=0; $contMedia<count($tweet->extended_entities->media); $contMedia++){
					/* Recorre */
					$imagenTwitter[$arregloImagenes] = "<img src='".$tweet->extended_entities->media[$contMedia]->media_url."' />";
					$arregloImagenes++;
				}
			}
		}

		if( $arregloImagenes==0 ){
			$imagenTwitter[0] = "<div><span>No se encontraron</span></div>";
			$imagenTwitter[1] = "<div><span>imágenes en</span></div>";
			$imagenTwitter[2] = "<div><span>el álbum indicado</span></div>";
		}
	}
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Carrusel Twitter</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<!-- jQuery, -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

		<!-- Fotorama -->
		<link  href="http://cdnjs.cloudflare.com/ajax/libs/fotorama/4.5.2/fotorama.css" rel="stylesheet">
		<script src="http://cdnjs.cloudflare.com/ajax/libs/fotorama/4.5.2/fotorama.js"></script>
	</head>
	<body>
		<section style="width: 700px; margin: 0 auto;">
			<div class="fotorama" data-autoplay="2000" data-width="100%" data-ratio="800/600" data-transition="crossfade" data-stopautoplayontouch="false" data-fit="contain" data-navposition="bottom" data-nav="thumbs" data-keyboard="true">
				<?php
					for($contImagen=0; $contImagen<count($imagenTwitter); $contImagen++){
						echo $imagenTwitter[$contImagen];
					}
				?>
			</div>
		</section>
	</body>
</html>
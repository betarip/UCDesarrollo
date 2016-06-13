<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

//disaplay errors

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);

$app = new \Slim\App($c);
//Configuracion de la base de datos

function getDatabase(){
	$database = new medoo([
	    'database_type' => 'mysql',
	    'database_name' => 'accesos',
	    'server' => 'localhost',
	    'username' => 'root',
	    'password' => 'telecom',
	    'charset' => 'utf8'
	]);	
	return $database;
}

function getConnection() {
    $dbhost="localhost";
    $dbuser="root";
    $dbpass="telecom";
    $dbname="accesos";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}



//declaraciones del API
$app->get('/time', 'getTime');

//Usuarios
$app->get('/getUsuario/{usuario}', 'getUsuario');
$app->post('/postUsuarioPuerta/{usuario},{puerta}', 'postUsuarioPuerta');
$app->post('/postUsuario/{usuario},{pin}', 'postUsuario');
//$app->put('/putUsuario/{usuario},{puerta}', 'putUsuario');
$app->put('/putUsuarioPin/{usuario},{pin}', 'putUsuarioPin');
$app->delete('/deleteUsuario/{usuario}', 'deleteUsuario');

//Puertas
$app->get('/getPuertaUsuarios/{puerta}', 'getPuertaUsuarios');

//Accesos
$app->get('/getAcceso/{usuario},{puerta}', 'getAcceso');
$app->post('/postAcceso/{usuario},{puerta}', 'postAcceso');
$app->delete('/deleteAcceso/{usuario},{puerta}', 'deleteAcceso');

//declaracion de las funciones



function getTime(Request $request, Response $response) {
    //$name = $request->getAttribute('name');
    $datetime = new DateTime();
    $datetime =$datetime->format("Y-m-d h:i:s");
    $response->getBody()->write($datetime);
    return $response;


}

function getUsuario($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT * from usuarios where uuid =:usuario";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("usuario",$args["usuario"]);
		$stmt->execute();
		$usuario = $stmt->fetchObject();
		$db=null;
		//COMPROBACION DEL QUERY
		if($usuario == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe usuario";
		}else{
			//$respuesta["resultado"]	=1;
			//$respuesta["usuario"]=$usuario;
			$respuesta=intval($usuario->pin);

		}
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$db->error()[2];
	}
	echo json_encode($respuesta);
}

function postUsuarioPuerta($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "INSERT INTO puertas (usuario, puerta) values (:usuario , :puerta)";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("usuario",$args["usuario"]);
		$stmt->bindParam("puerta",$args["puerta"]);
		$stmt->execute();
		$id=$db->lastInsertId();
		$db=null;
		//COMPROBACION DEL QUERY
		if($id == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe usuario";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["usuario"]=$id;
		}
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);
	
}

function postUsuario($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "INSERT INTO usuarios (uuid, pin) values (:usuario , :pin)";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("usuario",$args["usuario"]);
		$stmt->bindParam("pin",$args["pin"]);
		$stmt->execute();
		$id=$db->lastInsertId();
		$db=null;
		//COMPROBACION DEL QUERY
		if($id == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe usuario";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["usuario"]=$id;
		}
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);

}
/*
function putUsuarioPin($request, $response, $args){
	$db =getDatabase();
	//QUERY
	$datas = $db->update("usuarios", [
		"pin" => intval($args['pin'])
	], [
		"uuid"=> $args['usuario']
	]);
	if($db->error()==""){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$db->error()[2];
	}else{
		//COMPROBACION DEL QUERY
		if($datas==0){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe usuario";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["mensaje"]="pin cambiado para ".$args["usuario"];

		}
	}
	echo json_encode($respuesta);
}
*/
function deleteUsuario($request, $response, $args){
	$db =getDatabase();
	//QUERY
	$datas = $db->delete("usuarios", [
		"AND"=>[
			"uuid"=>$args['usuario']
		]
	]);
	if($db->error()==""){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$db->error()[2];
	}else{
		//COMPROBACION DEL QUERY
		if($datas==0){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe usuario";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["mensaje"]="pin cambiado para ".$args["usuario"];

		}
	}
	echo json_encode($respuesta);
}


function getAcceso($request, $response, $args){
	$db = getConnection();
	
	
	try{
		//llamada del proceso almacenado
		
		$sql = "call DarAcceso(:usuario,:puerta,@acceso,@pin);";
		$stmt = $db->prepare($sql);
		$stmt->bindParam("usuario",$args["usuario"]);
		$stmt->bindParam("puerta",intval($args["puerta"]));
		$stmt->execute();
		$stmt->closeCursor();
		//ejecutar el segundo query para obtener los valores
		$r = $db->query("Select @acceso as acceso, @pin as pin")->fetch(PDO::FETCH_ASSOC);
		$db=null;
		if($r["acceso"]!="1"){
			$respuesta["resultado"]=0;
			$respuesta["error"]="Acceso no autorizado";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["pin"]=$r["pin"];
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$db->error()[2];
	}
	echo json_encode($respuesta);
}

function putAcceso ($request, $response, $args){
    // Update book identified by $args['id']
    $db = getDatabase();
   }





$app->run();

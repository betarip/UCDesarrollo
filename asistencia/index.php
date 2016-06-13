<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



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

require 'comprobaciones.php';
require 'profesores.php';
require 'clases.php';



//declaraciones del API
$app->get('/time', 'getTime');
//Index
$app->get('/', 'descripcion');

//Profesores
$app->get('/getProfesoresUUID/{uuid}', 'getProfesoresUUID');
$app->get('/getProfesores/{usuario},{pass}', 'getProfesores');
$app->post('/postProfesor/{uuid},{nombre},{usuario},{pass}', 'postProfesor');
//$app->put('/putProfesor/{UUID},{nombre},{usuario},{pass},', 'putProfesor');
//$app->delete('/deleteUsuario/{usuario}', 'deleteUsuario');

//Clases
$app->get('/getClase/{id_clase}', 'getClase');
$app->get('/getClases/{id_profesor}', 'getClases');
$app->post('/postClase/{nombre},{id_profesor}','postClase');
//$app->put('/putClase/{nombre},{id_profesor}','putClase');

//Horario
$app->get('/getHorario/{idHorario}','getHorario');
$app->get('/getHorarios/{idClase}','getHorarios');
$app->post('/postHorario/{idClase},{dia},{horaI},{horaF},{aula}','postHorario');
//$app->put('/putHorario/{idClase},{dia},{horaI},{horaF},{aula}','putUsuario');

//Alumnos
$app->get('/getAlumnoUUID/{UUID}','getAlumnoUUID');
$app->get('/getAlumno/{id}','getAlumno');
$app->post('/postAlumno/{id},{UUID},{nombre}','postAlumno');
//$app->put('/putAlumno/{id},{UUID},{nombre}','putAlumno');

//Registro de clases
$app->get('/getRegistroClase/{id_clase}','getRegistroClase');
$app->get('/getRegistroClaseAlumno/{id_alumno}','getRegistroClaseAlumno');
$app->post('/postRegistroClaseAlumno/{idClase},{idAlumno}','postRegistroClaseAlumno');
//$app->put('/putRegistro/{idClase},{idAlumno}');

//Asistencia
$app->get('/getAsistenciaClaseAlumnoID/{idClase},{id}','getAsistenciaClaseAlumnoID');
$app->get('/getAsistenciaClaseAlumnoUUID/{idClase},{uuid}','getAsistenciaClaseAlumnoUUID');

$app->post('/postAsistenciaID/{idAlumno},{idClase},{idHorario}',"postAsistenciaID");
$app->post('/postAsistenciaUUID/{uuid},{idClase},{idHorario}',"postAsistenciaUUID");
//$app->get('/getAsistenciaClase/{idClase}','getAsistenciaClase');


function getTime(Request $request, Response $response) {
    //$name = $request->getAttribute('name');
    $datetime = new DateTime();
    $datetime =$datetime->format("Y-m-d h:i:s");
    $response->getBody()->write($datetime);
    return $response;
}

function getHorario($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT * FROM horario WHERE id_horario=:id_horario";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id_horario",$args["idHorario"]);
		$stmt->execute();
		$usuario = $stmt->fetchObject();
		//COMPROBACION DEL QUERY

		if($usuario == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe clase";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["usuario"]=$usuario;
			//$respuesta=intval($usuario->pin);
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);	
}

function getHorarios($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT * FROM horario WHERE id_clase=:idClase";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("idClase",$args["idClase"]);
		$stmt->execute();
		$usuario = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);

		//COMPROBACION DEL QUERY
		if($usuario == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="El profesor no tiene clases";
		}else{
			$respuesta["resultado"]	=1;
			$data[] = $usuario;
			while ($usuario = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		      $data[] = $usuario;
		    }

			$respuesta["clases"]=$data;
			//$respuesta=intval($usuario->pin);
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);	
}

function postHorario($request, $response, $args){
	$db = getConnection();
	//QUERY
	//{idClase},{dia},{horaI},{horaF},{aula}
	$sql = "INSERT INTO horario (id_clase, dia, hora_inicio, hora_fin, aula) values (:idClase ,:dia ,:horaI ,:horaF ,:aula)";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("idClase",$args["idClase"]);
		$stmt->bindParam("dia",$args["dia"]);
		$stmt->bindParam("horaI",$args["horaI"]);
		$stmt->bindParam("horaF",$args["horaF"]);
		$stmt->bindParam("aula",$args["aula"]);
		$stmt->execute();
		$id=$db->lastInsertId();
		$db=null;
		//COMPROBACION DEL QUERY
		if($id == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe usuario";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["clase"]=$id;
		}
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);
}

function getAlumnoUUID($request, $reponse, $args){
	$db = getConnection();
	//QUERY
	
	$sql = "SELECT * from alumno where uuid =:uuid";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("uuid",$args["UUID"]);
		
		$stmt->execute();
		$usuario = $stmt->fetchObject();
		//COMPROBACION DEL QUERY
		if($usuario == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe usuario";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["usuario"]=$usuario;
			//$respuesta=intval($usuario->pin);
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);		
}

function getAlumnoUUIDr($request, $reponse, $args){
	$db = getConnection();
	//QUERY
	
	$sql = "SELECT * from alumno where uuid =:uuid";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("uuid",$args["uuid"]);
		
		$stmt->execute();
		$usuario = $stmt->fetchObject();
		//COMPROBACION DEL QUERY
		if($usuario == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe usuario";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["usuario"]=$usuario;
			//$respuesta=intval($usuario->pin);
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	return json_encode($respuesta);		
}
function getAlumno($request, $reponse, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT * from alumno where id_alumno =:id_alumno";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id_alumno",$args["id"]);
		
		$stmt->execute();
		$usuario = $stmt->fetchObject();
		//COMPROBACION DEL QUERY
		if($usuario == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe usuario";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["usuario"]=$usuario;
			//$respuesta=intval($usuario->pin);
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);		
}

function postAlumno($request, $response, $args){
	$db = getConnection();
	//QUERY
	//{id},{UUID},{nombre}
	try{
		$sql = "call InsertarAlumno(:id_alumno,:i_uuid,:nombre,@id,@exito);";
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id_alumno",$args["id"]);
		$stmt->bindParam("i_uuid",$args["UUID"]);
		$stmt->bindParam("nombre",$args["nombre"]);
		$stmt->execute();
		$stmt->closeCursor();
		//ejecutar el segundo query para obtener los valores
		$r = $db->query("select @id as id,@exito as exito")->fetch(PDO::FETCH_ASSOC);
		$db=null;
		if($r["exito"]!="1"){
			$respuesta["resultado"]=0;
			$respuesta["error"]="Ya existe UUID";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["id"]=$r["id"];
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);
}

function getRegistroClase($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT id_alumno FROM registro_alumno_clase WHERE id_clase=:id_clase ";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id_clase",$args["id_clase"]);
		$stmt->execute();
		$usuario = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);

		//COMPROBACION DEL QUERY
		if($usuario == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="No existe la clase";
		}else{
			$respuesta["resultado"]	=1;
			$data[] = $usuario;
			while ($usuario = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		      $data[] = $usuario;
		    }

			$respuesta["alumnos"]=$data;
			//$respuesta=intval($usuario->pin);
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);
}

function getRegistroClaseAlumno($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT id_clase FROM registro_alumno_clase WHERE id_alumno=:id_alumno ";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id_alumno",$args["id_alumno"]);
		$stmt->execute();
		$usuario = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);

		//COMPROBACION DEL QUERY
		if($usuario == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="el alumno no tiene clases";
		}else{
			$respuesta["resultado"]	=1;
			$data[] = $usuario;
			while ($usuario = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		      $data[] = $usuario;
		    }

			$respuesta["alumnos"]=$data;
			//$respuesta=intval($usuario->pin);
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);
}

function postRegistroClaseAlumno($request, $response, $args){
	$db = getConnection();
	//QUERY
	//{idClase},{idAlumno}
	try{
		$sql = "call InsertarClaseAlumno(:id_alumno,:id_clase,@id,@exito);";
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id_alumno",$args["idClase"]);
		$stmt->bindParam("id_clase",$args["idAlumno"]);
		$stmt->execute();
		$stmt->closeCursor();
		//ejecutar el segundo query para obtener los valores
		$r = $db->query("select @id as id,@exito as exito")->fetch(PDO::FETCH_ASSOC);
		$db=null;
		if($r["exito"]!="1"){
			$respuesta["resultado"]=0;
			$respuesta["error"]="Ya existe UUID";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["id"]=$r["id"];
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);
}



function getAsistenciaClaseAlumnoID($request,$response, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT * FROM asistencia WHERE id_clase=:idClase and id_alumno=:id" ;
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("idClase",$args["idClase"]);
		$stmt->bindParam("id",$args["id"]);
		$stmt->execute();
		$usuario = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
		//COMPROBACION DEL QUERY
		if($usuario == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="El alumno no tiene asistencias";
		}else{
			$respuesta["resultado"]	=1;
			$data[] = $usuario;
			while ($usuario = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		      $data[] = $usuario;
		    }

			$respuesta["clases"]=$data;
			//$respuesta=intval($usuario->pin);
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);	
}

function getAsistenciaClaseAlumnoUUID($request,$response, $args){
	$args2["uuid"]=$args["uuid"];
	$alumno = json_decode(getAlumnoUUIDr(null,null,$args2),true);
	$alumno = $alumno["usuario"]["id_alumno"];
	
	$db = getConnection();
	//QUERY
	$sql = "SELECT id_clase, hora FROM asistencia WHERE id_clase=:idClase and id_alumno=:id_alumno" ;
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("idClase",$args["idClase"]);
		$stmt->bindParam("id_alumno",$alumno);
		$stmt->execute();
		$usuario = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
		//COMPROBACION DEL QUERY
		if($usuario == null){
			$respuesta["resultado"]=0;
			$respuesta["error"]="El alumno no tiene asistencias";
		}else{
			$respuesta["resultado"]	=1;
			$cadena = explode(" ",$usuario[1]);
			$alumno="";
			$alumno[]=$usuario[0];
			$alumno[]=$cadena[0];
			$alumno[]=$cadena[1];
			$data[] = $alumno;
			while ($usuario = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
		      	$cadena = explode(" ",$usuario[1]);
				$alumno="";
				$alumno[]=$usuario[0];
				$alumno[]=$cadena[0];
				$alumno[]=$cadena[1];
				$data[] = $alumno;
		    }

			$respuesta["clases"]=$data;
			//$respuesta=intval($usuario->pin);
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);	
	
}

function postAsistenciaID($request, $response, $args){
	$db = getConnection();
	//QUERY
	//{idAlumno},{idClase},{idHorario}
	try{
		$sql = "call RegistrarAsistencia(:idAlumno,:idClase,:idHorario,@id,@exito);";
		$stmt = $db->prepare($sql);
		$stmt->bindParam("idAlumno",$args["idAlumno"]);
		$stmt->bindParam("idClase",$args["idClase"]);
		$stmt->bindParam("idHorario",$args["idHorario"]);
		$stmt->execute();
		$stmt->closeCursor();
		//ejecutar el segundo query para obtener los valores
		$r = $db->query("select @id as id,@exito as exito")->fetch(PDO::FETCH_ASSOC);
		$db=null;
		if($r["exito"]!="1"){
			$respuesta["resultado"]=0;
			$respuesta["error"]="Ya existe UUID";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["id"]=$r["id"];
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);
}

function postAsistenciaUUID($request, $response, $args){
	$args2["uuid"]=$args["uuid"];
	$alumno = json_decode(getAlumnoUUIDr(null,null,$args2),true);
	$alumno = $alumno["usuario"]["id_alumno"];
	$db = getConnection();
	//QUERY
	//{idAlumno},{idClase},{idHorario}
	try{
		$sql = "call RegistrarAsistencia(:idAlumno,:idClase,:idHorario,@id,@exito);";
		$stmt = $db->prepare($sql);
		$stmt->bindParam("idAlumno",$alumno);
		$stmt->bindParam("idClase",$args["idClase"]);
		$stmt->bindParam("idHorario",$args["idHorario"]);
		$stmt->execute();
		$stmt->closeCursor();
		//ejecutar el segundo query para obtener los valores
		$r = $db->query("select @id as id,@exito as exito")->fetch(PDO::FETCH_ASSOC);
		$db=null;
		if($r["exito"]!="1"){
			$respuesta["resultado"]=0;
			$respuesta["error"]="Ya existe UUID";
		}else{
			$respuesta["resultado"]	=1;
			$respuesta["id"]=$r["id"];
		}
		
	}catch(PDOException $e){
		$respuesta["resultado"]=0;
		$respuesta["error"]="Error de la BD";
		$respuesta["errorEspecifico"]=$e;
	}
	echo json_encode($respuesta);
}


$app->run();

?>
<?php
	function descripcion($request, $response, $args){
		echo "Hola Asistencia";
	}

	function getProfesoresUUID($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT * from profesor where uuid =:uuid";
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
	echo json_encode($respuesta);	
}

function getProfesores($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT * FROM profesor WHERE usuario=:usuario and pass=:pass";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("usuario",$args["usuario"]);
		$stmt->bindParam("pass",$args["pass"]);
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

function postProfesor($request, $response, $args){
	$db = getConnection();
	//QUERY
	try{
		$sql = "call InsertarProfesor(:nombre,:uuid,:usuario,:pass,@id,@exito);";
		$stmt = $db->prepare($sql);
		$stmt->bindParam("nombre",$args["nombre"]);
		$stmt->bindParam("uuid",$args["uuid"]);
		$stmt->bindParam("usuario",$args["usuario"]);
		$stmt->bindParam("pass",$args["pass"]);
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
?>
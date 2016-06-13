<?php
function getClase($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT * FROM clase WHERE id_clase=:id_clase";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id_clase",$args["id_clase"]);
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

function getClases($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "SELECT * FROM clase WHERE id_profesor=:id_profesor";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id_profesor",$args["id_profesor"]);
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

function postClase($request, $response, $args){
	$db = getConnection();
	//QUERY
	$sql = "INSERT INTO clase (nombre, id_profesor) values (:nombre , :id_profesor)";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("nombre",$args["nombre"]);
		$stmt->bindParam("id_profesor",$args["id_profesor"]);
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

?>
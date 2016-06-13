<?php

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
    $dbname="asistencia";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}


function comprobarDia($dia,$hora,$idHorario){
	$db = getConnection();
	//QUERY
	$sql = "SELECT * FROM horario WHERE id_clase=:id_clase";
	try{
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id_clase",$idHorario);
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

?>
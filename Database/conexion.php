<?php

	$servidor="localhost";
	$usuario="root";
	$password="";
	$bd="sociod";

	function conectarBD(){
	
		global $servidor,$usuario,$password,$bd;
		$conexion = new mysqli($servidor,$usuario,$password,$bd);

		if ($conexion->connect_errno) {
			echo "Error: Fallo al conectarse a MySQL debido a: \n"; 
			echo "Errno: " . $conexion->connect_errno . "\n";
			echo "Error: " . $conexion->connect_error . "\n";
			exit;
		}
		$conexion->set_charset("utf8");
		
		return $conexion;
	}

	function desconectarBD($conexion){
		$conexion->close();
	}

	// function actualizarCuota($socioUsuario, $cuotaPagada, $fechaUltimoPago, $fechaProximoPago){
	// 	$conexion = conectarBD();
	
	// 	$socioUsuario = $conexion->real_escape_string($socioUsuario);
	// 	$cuotaPagada = (int)$cuotaPagada;
	// 	$fechaUltimoPago = $conexion->real_escape_string($fechaUltimoPago);
	// 	$fechaProximoPago = $conexion->real_escape_string($fechaProximoPago);
	
	// 	$modificacion = "UPDATE Socio 
	// 			SET `Cuota pagada` = '$cuotaPagada', `Último pago` = '$fechaUltimoPago', `Próximo pago` = '$fechaProximoPago' 
	// 			WHERE Usuario = '$socioUsuario'";
	
	// 	$modificacionHecha = $conexion->query($modificacion);
	
	// 	desconectarBD($conexion);
	// 	return $modificacionHecha;
	// }
?>
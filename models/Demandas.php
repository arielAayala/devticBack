<?php

include_once "Profesionales.php";
include_once "../conexion/Conexion.php";

class Demandas {

    public function crearDemanda($token,  $idTipo, $idOrganizacion, $tituloDemanda, $motivoDemanda, $almacenDemanda){
        $idTipo = intval($idTipo);
        $idOrganizacion = intval($idOrganizacion);

        if($datos=Profesionales::validarToken($token)) {
            $con = new Conexion();
            $query="INSERT INTO demandas(idEstado, idTipo, idOrganizacion, tituloDemanda, motivoDemanda, almacenDemanda, fechaIngresoDemanda) 
            VALUES(1,$idTipo, $idOrganizacion, '$tituloDemanda', '$motivoDemanda', '$almacenDemanda', CURDATE() )";
            if ($con ->query($query)) {
                $idDemanda = $con -> insert_id;
                $queryGrupos = "INSERT INTO grupos(idDemanda, idProfesional, creadorDemanda) VALUES ($idDemanda, $datos->idProfesional, 1)";
                if ($con-> query($queryGrupos)) {
                    return true;
                }else{
                    $queryDemandas = "DELETE  FROM demandas WHERE idDemanda = $idDemanda";
                    $con ->query($queryDemandas);
                }
            }
        }
        return false;
    }

    public function actualizarDemanda($token, $idDemanda,$idTipo, $idOrganizacion, $tituloDemanda, $motivoDemanda, $almacenDemanda){
        if($datos=Profesionales::validarToken($token)) {
            $con = new Conexion();
            $query="SELECT COUNT(*) as cantidad FROM grupos where $datos->idProfesional = idProfesional AND $idDemanda = idDemanda";
            $resultado = $con ->query($query);
            if ($resultado->num_rows == 1 || $datos->prioridadProfesional==1) {
                $queryActualizar = "UPDATE demandas SET
                idTipo = $idTipo,
                idOrganizacion = $idOrganizacion,
                tituloDemanda = '$tituloDemanda',
                motivoDemanda = '$motivoDemanda',
                almacenDemanda = '$almacenDemanda' WHERE idDemanda = $idDemanda";
                if ($con ->query($queryActualizar)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function eliminarDemanda($token, $idDemanda){
        if ($datos = Profesionales::validarToken($token)) {
            if($datos-> prioridadProfesional == 1){
                $con = new Conexion;
                $query = "DELETE FROM grupos WHERE idDemanda = $idDemanda" ;
                if ($con->query($query)) {
                    $queryDemanda = "DELETE FROM demandas where idDemanda = $idDemanda";
                    if ($con->query($queryDemanda)) {
                        return true;
                    }
                }
            }
        }
        echo $con ->error;
        return false;
    }

    public function obtenerTodasDemandas($token, $pagina){
        if ($datos = Profesionales::validarToken($token)) {
            $con = new Conexion();
            $query = "SELECT d.idDemanda, p.fotoProfesional, especialidades.nombreEspecialidad , personas.nombrePersona ,d.tituloDemanda, d.fechaIngresoDemanda, d.motivoDemanda, e.nombreEstado, t.nombreTipo, o.nombreOrganizacion   FROM demandas d INNER JOIN estados e ON e.idEstado= d.idEstado INNER JOIN tipos t ON d.idTipo = t.idTipo INNER JOIN organizaciones o ON o.idOrganizacion = d.idOrganizacion INNER JOIN grupos g ON g.idDemanda = d.idDemanda and g.creadorDemanda = 1 INNER JOIN profesionales p ON p.idProfesional = g.idProfesional INNER JOIN personas ON personas.idPersona = p.idPersona INNER JOIN especialidades ON especialidades.idEspecialidad = p.idEspecialidad ORDER BY d.fechaIngresoDemanda LIMIT 10 OFFSET ". (($pagina - 1)*10) ;
            $datos =[];
            $resultado = $con ->query($query);
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $datos[]=$row;
                }
            }
            $queryTotal = "SELECT COUNT(idDemanda) AS demandasTotales FROM demandas";
            if ($resultadoTotal = $con -> query($queryTotal)) {
                while ($row = $resultadoTotal->fetch_assoc()) {
                    $total = $row["demandasTotales"];
                }
            }
            return ["data"=>$datos, "demandasTotales"=>intval($total), "paginaNumero"=> $pagina];
        }
        return false;
    }

    public function obtenerDemanda($token, $id){
        if ($datos = Profesionales::validarToken($token)) {
            $con = new Conexion();
            $query = "SELECT d.idDemanda, d.almacenDemanda,p.fotoProfesional, especialidades.nombreEspecialidad , personas.nombrePersona ,d.tituloDemanda, d.fechaIngresoDemanda, d.motivoDemanda, e.nombreEstado,e.idEstado, t.nombreTipo, t.idTipo, o.nombreOrganizacion, o.idOrganizacion   FROM demandas d INNER JOIN estados e ON e.idEstado= d.idEstado INNER JOIN tipos t ON d.idTipo = t.idTipo INNER JOIN organizaciones o ON o.idOrganizacion = d.idOrganizacion INNER JOIN grupos g ON g.idDemanda = d.idDemanda and g.creadorDemanda = 1 INNER JOIN profesionales p ON p.idProfesional = g.idProfesional INNER JOIN personas ON personas.idPersona = p.idPersona INNER JOIN especialidades ON especialidades.idEspecialidad = p.idEspecialidad WHERE d.idDemanda = $id";
            $datos =[];
            $resultado = $con ->query($query);
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $datos=$row;
                }
            }
            
            return $datos;
        }
        return false;
    }
    
}

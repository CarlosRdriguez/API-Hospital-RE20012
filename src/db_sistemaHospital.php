<?php

class db_sistemaHospital {
    // Credenciales para la conexion a la nube en Filess.io
    private $dbHost = '692zpb.h.filess.io';
    private $dbUser = 'bd_hospital_re20012_biggermix';
    private $dbPass = 'RE20012par3';
    private $dbName = 'bd_hospital_re20012_biggermix';
    private $dbPort = '3306';

    // Metodo para que se pueda conectar a la base de datos
    public function conectDB() {
        $mysqlConnect = "mysql:host=$this->dbHost;port=$this->dbPort;dbname=$this->dbName;charset=utf8";
        $dbConnection = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
        
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $dbConnection;
    }
}
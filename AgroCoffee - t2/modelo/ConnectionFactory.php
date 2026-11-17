<?php
/* ==========================================================================
 *  ConnectionFactory.php
 *  Camada: MODELO
 *
 *  Fábrica de conexões com o banco. Todos os DAOs pedem a conexão aqui,
 *  e a mesma conexão é reaproveitada durante a requisição (Singleton).
 * ========================================================================== */

class ConnectionFactory {

    private static $host  = "localhost";
    private static $user  = "root";
    private static $senha = "";
    private static $db    = "agrocoffee";

    private static $con = null;

    public static function getConnection() {
        if (self::$con === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=utf8mb4";
                self::$con = new PDO($dsn, self::$user, self::$senha);
                self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $ex) {
                die("Erro ao conectar no banco: " . $ex->getMessage());
            }
        }
        return self::$con;
    }
}

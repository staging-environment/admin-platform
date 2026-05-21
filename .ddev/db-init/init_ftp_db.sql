-- 1. Creamos la base de datos independiente para el FTP si no existe
CREATE DATABASE IF NOT EXISTS utrecar_ftp_db;

-- 2. Nos cambiamos a ella para crear la estructura
USE utrecar_ftp_db;

-- 3. Creamos la tabla con las columnas exactas de tu modelo FtpUser.php
CREATE TABLE IF NOT EXISTS ftp_users (
    user VARCHAR(64) NOT NULL PRIMARY KEY,
    password VARCHAR(64) NOT NULL,
    dir VARCHAR(255) NOT NULL,
    uid INT(11) NOT NULL DEFAULT 33,
    gid INT(11) NOT NULL DEFAULT 33
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

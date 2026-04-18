-- PHP Login/Register System - Current Database Schema
-- Character set: utf8mb4 (emoji + full unicode support)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `loginregister`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `loginregister`;

CREATE TABLE `users` (
    `Kod`        INT(11)      NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(30)  NOT NULL,
    `password`   VARCHAR(255) NOT NULL,
    `status`     CHAR(1)      NOT NULL DEFAULT 'A' COMMENT 'A=Aktif, P=Pasif',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`Kod`),
    UNIQUE KEY `uq_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Test user (password: Test1234)
INSERT INTO `users` (`username`, `password`, `status`) VALUES
('testyonetici', '$2y$12$wpMZbTk3voIWZqj3/mjzLOuKyWn42QgrKcDd5AtmmqKOAR9/9RQLe', 'A');

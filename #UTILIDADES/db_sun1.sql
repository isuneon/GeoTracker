/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.1.33-MariaDB : Database - db_kpi_admin_isuneon
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_kpi_admin_isuneon` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci */;

USE `db_kpi_admin_isuneon`;

/*Table structure for table `admin_horarios_config` */

DROP TABLE IF EXISTS `admin_horarios_config`;

CREATE TABLE `admin_horarios_config` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `admin_horarios_config` */

insert  into `admin_horarios_config`(`id`,`name`,`descripcion`,`activo`,`created_at`,`updated_at`,`deleted_at`) values (1,'agendado','Sincronizacion Agendada.',1,'2016-11-18 01:42:33','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'resumen_gerencial','Horario de Envio de Resumen Gerencial',1,'2017-01-26 11:56:29','0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'alerta_admin','Horario de Envio de Alertas Administrativas',1,'2017-01-26 14:23:50','2017-01-26 14:23:50','0000-00-00 00:00:00');

/*Table structure for table `admin_horarios_dias` */

DROP TABLE IF EXISTS `admin_horarios_dias`;

CREATE TABLE `admin_horarios_dias` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `id_horarios` int(2) NOT NULL,
  `id_dia` int(2) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Data for the table `admin_horarios_dias` */

insert  into `admin_horarios_dias`(`id`,`id_horarios`,`id_dia`,`activo`) values (1,1,1,1),(2,1,2,1),(3,1,3,1),(4,1,4,1),(5,1,5,1),(6,1,6,1),(7,1,7,1),(8,2,1,1),(9,2,2,1),(10,2,3,1),(11,2,4,1),(12,2,5,1),(13,2,6,1),(14,2,7,0);

/*Table structure for table `admin_horarios_horas` */

DROP TABLE IF EXISTS `admin_horarios_horas`;

CREATE TABLE `admin_horarios_horas` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `id_horarios` int(2) NOT NULL,
  `hora_sync` time DEFAULT '00:00:00',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `admin_horarios_horas` */

insert  into `admin_horarios_horas`(`id`,`id_horarios`,`hora_sync`,`activo`) values (1,1,'08:00:00',1),(2,1,'12:00:00',1),(3,1,'15:00:00',1),(4,2,'06:00:00',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

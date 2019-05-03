/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.1.21-MariaDB : Database - db_kpi_1
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_kpi_1` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `db_kpi_1`;

/*Table structure for table `admin_config` */

DROP TABLE IF EXISTS `admin_config`;

CREATE TABLE `admin_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre_empresa` varchar(255) DEFAULT 'DEMO' COMMENT 'Nombre empresa',
  `sinc_fec_ult` datetime DEFAULT NULL COMMENT 'Fecha de ultima sincronizacion',
  `sinc_ini` datetime DEFAULT NULL COMMENT 'Fecha y Hora de inicio de sincronizacion',
  `sinc_fin` datetime DEFAULT NULL COMMENT 'Fecha y Hora de finalizacion de sincronizacion',
  `sinc_manual` int(1) DEFAULT '0' COMMENT 'switch para indicar sincronizacion manual',
  `sinc_password` varchar(255) DEFAULT NULL COMMENT 'Clave acceso de aplicacion',
  `num_lice` varchar(255) DEFAULT NULL COMMENT 'Numero de Licencia',
  `fec_install` datetime DEFAULT NULL COMMENT 'Fecha de instalacion',
  `sender_user` varchar(255) DEFAULT NULL COMMENT 'Cuenta para enviar email',
  `sender_pass` varchar(255) DEFAULT NULL COMMENT 'pass del remitente',
  `defa_cod_proveedor` varchar(60) DEFAULT '0000' COMMENT 'Codigo proveedor principal',
  `defa_cod_tip_banco` varchar(60) DEFAULT '0000' COMMENT 'Codigo para proveedor tipo bancos',
  `defa_costo` varchar(60) DEFAULT 'cos_pro_un' COMMENT 'Costo por defecto',
  `defa_precio` varchar(60) DEFAULT 'prec_vta1' COMMENT 'Precio por defecto',
  `defa_pmaxgan` decimal(5,2) DEFAULT '30.00' COMMENT 'Porcentaje max ganancia',
  `defa_p_est_cos` decimal(5,2) DEFAULT '12.50' COMMENT 'Porcentaje estructura de costos',
  `defa_language` varchar(3) NOT NULL DEFAULT 'ES',
  `defa_moneda` varchar(4) NOT NULL DEFAULT 'Bs',
  `alert_mont_odp` decimal(18,4) DEFAULT '10000.0000' COMMENT 'Monto para alerta de orden de pago',
  `alert_mont_odc` decimal(18,4) DEFAULT '10000.0000' COMMENT 'Monto para alerta orden de compra',
  `alert_mont_com` decimal(18,4) DEFAULT '10000.0000' COMMENT 'Monto para alerta de compras',
  `alert_mont_fact` decimal(18,4) DEFAULT '10000.0000' COMMENT 'Monto para alerta de factura',
  `alert_mont_cotz` decimal(18,4) DEFAULT '10000.0000' COMMENT 'Monto para alerta de cotizacion',
  `version` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `admin_config` */

insert  into `admin_config`(`id`,`nombre_empresa`,`sinc_fec_ult`,`sinc_ini`,`sinc_fin`,`sinc_manual`,`sinc_password`,`num_lice`,`fec_install`,`sender_user`,`sender_pass`,`defa_cod_proveedor`,`defa_cod_tip_banco`,`defa_costo`,`defa_precio`,`defa_pmaxgan`,`defa_p_est_cos`,`defa_language`,`defa_moneda`,`alert_mont_odp`,`alert_mont_odc`,`alert_mont_com`,`alert_mont_fact`,`alert_mont_cotz`,`version`) values (1,'DEMO','2017-01-25 14:18:06','2017-01-26 14:18:13','2017-01-26 14:18:18',0,'fe01ce2a7fbac8fafaed7c982a04e229','KPIADMIN-DEMO001-00000000-00000000-00000000-0000000A','2017-01-18 14:18:31','email@email.com','2378y3bkjebk/*/-','132323werewrw','0000','cos_pro_un','prec_vta1',51.20,12.50,'ES','Bs',100.0000,10000.0000,10000.0000,10000.0000,10000.0000,NULL);

/*Table structure for table `admin_correos` */

DROP TABLE IF EXISTS `admin_correos`;

CREATE TABLE `admin_correos` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `id_notifi` int(2) DEFAULT NULL,
  `to_email` text,
  `cc_email` text,
  `bcc_email` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `admin_correos` */

insert  into `admin_correos`(`id`,`id_notifi`,`to_email`,`cc_email`,`bcc_email`) values (1,1,'email@email.com','email@email.com','email@email.com');

/*Table structure for table `admin_dia` */

DROP TABLE IF EXISTS `admin_dia`;

CREATE TABLE `admin_dia` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `dia` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `admin_dia` */

insert  into `admin_dia`(`id`,`dia`) values (1,'Lunes'),(2,'Martes'),(3,'Miercoles'),(4,'Jueves'),(5,'Viernes'),(6,'Sabado'),(7,'Domingo');

/*Table structure for table `admin_horarios` */

DROP TABLE IF EXISTS `admin_horarios`;

CREATE TABLE `admin_horarios` (
  `id` int(3) NOT NULL,
  `id_notificacion` int(3) NOT NULL,
  `id_dia` int(3) NOT NULL,
  `id_notifi_horario` int(3) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*Data for the table `admin_horarios` */

insert  into `admin_horarios`(`id`,`id_notificacion`,`id_dia`,`id_notifi_horario`,`activo`) values (1,1,1,1,1),(2,1,1,2,0),(3,1,1,3,1),(4,1,2,1,0),(5,1,2,2,0),(6,1,2,3,0),(7,1,3,1,0),(8,1,3,2,0),(9,1,3,3,0),(10,1,4,1,0),(11,1,4,2,0),(12,1,4,3,0),(13,1,5,1,0),(14,1,5,2,0),(15,1,5,3,0),(16,1,6,1,0),(17,1,6,2,0),(18,1,6,3,0),(19,1,7,1,0),(20,1,7,2,0),(21,1,7,3,0),(22,2,1,4,0),(25,2,2,4,0),(28,2,3,4,0),(31,2,4,4,0),(34,2,5,4,0),(37,2,6,4,0),(42,2,7,4,0);

/*Table structure for table `admin_notifica_horario` */

DROP TABLE IF EXISTS `admin_notifica_horario`;

CREATE TABLE `admin_notifica_horario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_notificacion` int(11) NOT NULL,
  `descripcion` varchar(90) COLLATE utf8_spanish_ci NOT NULL,
  `hora` time NOT NULL,
  `activo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*Data for the table `admin_notifica_horario` */

insert  into `admin_notifica_horario`(`id`,`id_notificacion`,`descripcion`,`hora`,`activo`) values (1,1,'MAÑANA','08:00:00',1),(2,1,'TARDE','05:00:00',0),(3,1,'NOCHE','09:00:00',0),(4,2,'HORARIO1','07:00:00',1);

/*Table structure for table `admin_notificacion` */

DROP TABLE IF EXISTS `admin_notificacion`;

CREATE TABLE `admin_notificacion` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `sender_user` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sender_pass` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sender_asunto` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sender_cuerpo` text COLLATE utf8_spanish_ci,
  `sender_attach` text COLLATE utf8_spanish_ci,
  `notificacion_cantidad` int(2) DEFAULT NULL,
  `sp_nombre_consultar` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sp_parametros_enviar` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sms_send` tinyint(1) DEFAULT '0',
  `sms_numbers` text COLLATE utf8_spanish_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*Data for the table `admin_notificacion` */

insert  into `admin_notificacion`(`id`,`name`,`descripcion`,`activo`,`sender_user`,`sender_pass`,`sender_asunto`,`sender_cuerpo`,`sender_attach`,`notificacion_cantidad`,`sp_nombre_consultar`,`sp_parametros_enviar`,`sms_send`,`sms_numbers`,`created_at`,`updated_at`,`deleted_at`) values (1,'resumen_gerencial','Resumen Gerencial',1,'email@email.com','2378y3bkjebk/*/-','Resumen G',NULL,NULL,3,NULL,NULL,0,NULL,'2017-01-26 11:32:03','2017-01-26 11:32:03','0000-00-00 00:00:00'),(2,'alerta_admin','Alertas Administrativas',1,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,0,NULL,'2017-01-26 11:36:35','2017-01-26 11:36:35','0000-00-00 00:00:00');

/*Table structure for table `admin_notificacion_dias` */

DROP TABLE IF EXISTS `admin_notificacion_dias`;

CREATE TABLE `admin_notificacion_dias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_notificacion` int(11) DEFAULT NULL,
  `id_dia` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index2` (`id_notificacion`,`id_dia`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*Data for the table `admin_notificacion_dias` */

insert  into `admin_notificacion_dias`(`id`,`id_notificacion`,`id_dia`,`activo`) values (1,1,1,1),(2,1,2,1),(3,1,3,1),(4,1,4,1),(5,1,5,1),(6,1,6,0),(7,2,1,1),(8,2,2,1),(9,2,3,1),(10,2,4,1),(11,2,5,1),(12,2,6,0),(13,2,7,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

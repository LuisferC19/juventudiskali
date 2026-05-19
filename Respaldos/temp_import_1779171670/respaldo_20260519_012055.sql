-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: iskali
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actividades`
--

DROP TABLE IF EXISTS `actividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actividades` (
  `id_actividad` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `zona` enum('san_martin','tlaxcala','ambas') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ambas',
  `estado` enum('planeada','en_curso','completada','cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planeada',
  `id_responsable` int NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_actividad`),
  KEY `fk_act_responsable` (`id_responsable`),
  KEY `idx_actividades_zona` (`zona`),
  KEY `idx_actividades_estado` (`estado`),
  KEY `idx_actividades_fechas` (`fecha_inicio`,`fecha_fin`),
  CONSTRAINT `fk_act_responsable` FOREIGN KEY (`id_responsable`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `chk_act_fechas` CHECK ((`fecha_fin` >= `fecha_inicio`))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actividades`
--

LOCK TABLES `actividades` WRITE;
/*!40000 ALTER TABLE `actividades` DISABLE KEYS */;
INSERT INTO `actividades` VALUES (1,'Distribución de despensas ? San Martín','Reparto mensual de despensas básicas en colonias vulnerables','2026-06-05 09:00:00','2026-06-05 14:00:00','san_martin','planeada',2,'2026-05-12 20:17:03'),(2,'Jornada médica gratuita ? Tlaxcala','Consultas médicas y odontológicas gratuitas','2026-06-12 08:00:00','2026-06-12 17:00:00','tlaxcala','planeada',5,'2026-05-12 20:17:03'),(3,'Entrega de material escolar ? Ambas','Distribución de útiles para inicio de ciclo escolar','2026-07-15 09:00:00','2026-07-15 13:00:00','ambas','planeada',2,'2026-05-12 20:17:03'),(4,'Campaña de ropa de abrigo ? Tlaxcala','Recolección y entrega de cobijas y ropa de invierno','2026-08-01 08:00:00','2026-08-01 16:00:00','tlaxcala','planeada',8,'2026-05-12 20:17:03');
/*!40000 ALTER TABLE `actividades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asignaciones_voluntario`
--

DROP TABLE IF EXISTS `asignaciones_voluntario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignaciones_voluntario` (
  `id_asignacion` int NOT NULL AUTO_INCREMENT,
  `id_voluntario` int NOT NULL,
  `id_entrega` int NOT NULL,
  `id_usuario_asignador` int NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_compromiso` datetime NOT NULL,
  `estado` enum('asignada','aceptada','rechazada','completada','cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'asignada',
  `observaciones_voluntario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_asignacion`),
  KEY `fk_asig_entrega` (`id_entrega`),
  KEY `fk_asig_usuario` (`id_usuario_asignador`),
  KEY `idx_asig_voluntario` (`id_voluntario`,`estado`),
  CONSTRAINT `fk_asig_entrega` FOREIGN KEY (`id_entrega`) REFERENCES `entregas` (`id_entrega`),
  CONSTRAINT `fk_asig_usuario` FOREIGN KEY (`id_usuario_asignador`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_asig_voluntario` FOREIGN KEY (`id_voluntario`) REFERENCES `voluntarios` (`id_voluntario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignaciones_voluntario`
--

LOCK TABLES `asignaciones_voluntario` WRITE;
/*!40000 ALTER TABLE `asignaciones_voluntario` DISABLE KEYS */;
/*!40000 ALTER TABLE `asignaciones_voluntario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistencia_voluntarios`
--

DROP TABLE IF EXISTS `asistencia_voluntarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistencia_voluntarios` (
  `id_asistencia` int NOT NULL AUTO_INCREMENT,
  `id_voluntario` int NOT NULL,
  `id_actividad` int NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metodo` enum('manual','qr') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `presente` tinyint(1) NOT NULL DEFAULT '1',
  `token_qr` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_asistencia`),
  UNIQUE KEY `uq_vol_actividad` (`id_voluntario`,`id_actividad`),
  UNIQUE KEY `uq_token_qr` (`token_qr`),
  KEY `idx_asistencia_actividad` (`id_actividad`),
  KEY `idx_asistencia_voluntario` (`id_voluntario`),
  CONSTRAINT `fk_asist_actividad` FOREIGN KEY (`id_actividad`) REFERENCES `actividades` (`id_actividad`),
  CONSTRAINT `fk_asist_voluntario` FOREIGN KEY (`id_voluntario`) REFERENCES `voluntarios` (`id_voluntario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencia_voluntarios`
--

LOCK TABLES `asistencia_voluntarios` WRITE;
/*!40000 ALTER TABLE `asistencia_voluntarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `asistencia_voluntarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `avances_campana`
--

DROP TABLE IF EXISTS `avances_campana`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avances_campana` (
  `id_avance` int NOT NULL AUTO_INCREMENT,
  `id_campana` int NOT NULL,
  `id_usuario` int NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `porcentaje_avance` decimal(5,2) NOT NULL DEFAULT '0.00',
  `monto_recaudado` decimal(12,2) NOT NULL DEFAULT '0.00',
  `evidencia_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_avance`),
  KEY `fk_avances_campana` (`id_campana`),
  KEY `fk_avances_usuario` (`id_usuario`),
  CONSTRAINT `fk_avances_campana` FOREIGN KEY (`id_campana`) REFERENCES `campanas` (`id_campana`),
  CONSTRAINT `fk_avances_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `chk_porcentaje` CHECK ((`porcentaje_avance` between 0.00 and 100.00))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avances_campana`
--

LOCK TABLES `avances_campana` WRITE;
/*!40000 ALTER TABLE `avances_campana` DISABLE KEYS */;
/*!40000 ALTER TABLE `avances_campana` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficiario_tipos_apoyo`
--

DROP TABLE IF EXISTS `beneficiario_tipos_apoyo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beneficiario_tipos_apoyo` (
  `id_beneficiario` int NOT NULL,
  `id_tipo_apoyo` int NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `notas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_beneficiario`,`id_tipo_apoyo`),
  KEY `fk_bta_tipo_apoyo` (`id_tipo_apoyo`),
  CONSTRAINT `fk_bta_beneficiario` FOREIGN KEY (`id_beneficiario`) REFERENCES `beneficiarios` (`id_beneficiario`),
  CONSTRAINT `fk_bta_tipo_apoyo` FOREIGN KEY (`id_tipo_apoyo`) REFERENCES `tipos_apoyo` (`id_tipo_apoyo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficiario_tipos_apoyo`
--

LOCK TABLES `beneficiario_tipos_apoyo` WRITE;
/*!40000 ALTER TABLE `beneficiario_tipos_apoyo` DISABLE KEYS */;
INSERT INTO `beneficiario_tipos_apoyo` VALUES (2,2,'2026-05-12 14:17:03',1,NULL),(3,3,'2026-05-12 14:17:03',1,NULL),(4,4,'2026-05-12 14:17:03',1,NULL),(5,5,'2026-05-12 14:17:03',1,NULL),(6,6,'2026-05-12 14:17:03',1,NULL),(7,7,'2026-05-12 14:17:03',1,NULL),(8,1,'2026-05-12 14:17:03',1,NULL),(9,3,'2026-05-12 14:17:03',1,NULL),(10,8,'2026-05-12 14:17:03',1,NULL),(12,2,'2026-05-12 14:17:03',1,NULL),(13,9,'2026-05-12 14:17:03',1,NULL);
/*!40000 ALTER TABLE `beneficiario_tipos_apoyo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficiarios`
--

DROP TABLE IF EXISTS `beneficiarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beneficiarios` (
  `id_beneficiario` int NOT NULL AUTO_INCREMENT,
  `tipo_persona` enum('fisica','moral') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fisica',
  `id_comunidad` int DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activo','inactivo','en_espera') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_usuario_registrador` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_beneficiario`),
  KEY `fk_benef_usuario` (`id_usuario_registrador`),
  KEY `fk_benef_comunidad` (`id_comunidad`),
  KEY `idx_benef_estado` (`estado`),
  KEY `idx_benef_tipo` (`tipo_persona`),
  CONSTRAINT `fk_benef_comunidad` FOREIGN KEY (`id_comunidad`) REFERENCES `comunidades` (`id_comunidad`),
  CONSTRAINT `fk_benef_usuario` FOREIGN KEY (`id_usuario_registrador`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficiarios`
--

LOCK TABLES `beneficiarios` WRITE;
/*!40000 ALTER TABLE `beneficiarios` DISABLE KEYS */;
INSERT INTO `beneficiarios` VALUES (2,'fisica',2,'Av. Principal 45','2223001002','activo','5 integrantes',2,'2024-03-01 10:00:00','2026-05-12 14:17:03'),(3,'fisica',3,'Barrio el Calvario 3','2223001003','activo','Adulto mayor sin seguro',5,'2024-05-01 09:00:00','2026-05-12 14:17:03'),(4,'fisica',4,'Colonia Centro 22','2223001004','activo','Pensionada con ingresos bajos',5,'2024-09-01 10:00:00','2026-05-12 14:17:03'),(5,'moral',5,'Carretera Federal km 12','2223001005','activo','30 niños de 2 a 10 años',2,'2024-10-01 08:00:00','2026-05-12 14:17:03'),(6,'fisica',6,'Calle Magnolias 8','2223001006','activo','Desempleado busca capacitación',8,'2025-01-05 09:00:00','2026-05-12 14:17:03'),(7,'moral',7,'Delegación Municipal','2223001007','activo','120 familias sin red de agua',5,'2024-06-15 10:00:00','2026-05-12 14:17:03'),(8,'fisica',8,'Priv. Las Flores 5','2223001008','activo','Ingreso a secundaria',2,'2024-02-05 09:00:00','2026-05-12 14:17:03'),(9,'fisica',9,'Calle Sauce 17','2223001009','activo','Diabética requiere insulina',5,'2025-02-01 10:00:00','2026-05-12 14:17:03'),(10,'fisica',10,'Col. San Francisco 3','2223001010','activo','7 integrantes zona fría',5,'2024-09-10 11:00:00','2026-05-12 14:17:03'),(12,'fisica',12,'Calle Olivos 9','2223001012','en_espera','En proceso de verificación',8,'2024-03-10 09:00:00','2026-05-12 14:17:03'),(13,'moral',13,'Calle 5 de Mayo 88','2223001013','inactivo','Albergan 80 animales',5,'2025-03-01 10:00:00','2026-05-16 18:56:10');
/*!40000 ALTER TABLE `beneficiarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficiarios_fisicos`
--

DROP TABLE IF EXISTS `beneficiarios_fisicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beneficiarios_fisicos` (
  `id_beneficiario` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `edad` tinyint unsigned DEFAULT NULL,
  `curp` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  PRIMARY KEY (`id_beneficiario`),
  UNIQUE KEY `uq_benef_curp` (`curp`),
  KEY `idx_benef_fisicos_nombre` (`nombre`,`apellido`),
  CONSTRAINT `fk_bf_beneficiario` FOREIGN KEY (`id_beneficiario`) REFERENCES `beneficiarios` (`id_beneficiario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficiarios_fisicos`
--

LOCK TABLES `beneficiarios_fisicos` WRITE;
/*!40000 ALTER TABLE `beneficiarios_fisicos` DISABLE KEYS */;
INSERT INTO `beneficiarios_fisicos` VALUES (2,'José','Torres Herrera',45,NULL,NULL),(3,'Roberto','Jiménez Sosa',72,NULL,NULL),(4,'María del Carmen','Vega',60,NULL,NULL),(6,'José Ramón','Pérez Luna',35,NULL,NULL),(8,'Lucía','Estrada Bravo',9,NULL,NULL),(9,'Guadalupe','Morales Tapia',78,NULL,NULL),(10,'Carlos','Reyes Castillo',30,NULL,NULL),(12,'Carlos','Medina Olvera',55,NULL,NULL);
/*!40000 ALTER TABLE `beneficiarios_fisicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficiarios_morales`
--

DROP TABLE IF EXISTS `beneficiarios_morales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beneficiarios_morales` (
  `id_beneficiario` int NOT NULL,
  `razon_social` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfc` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_beneficiario`),
  UNIQUE KEY `uq_benef_rfc` (`rfc`),
  KEY `idx_benef_morales_razon` (`razon_social`),
  CONSTRAINT `fk_bm_beneficiario` FOREIGN KEY (`id_beneficiario`) REFERENCES `beneficiarios` (`id_beneficiario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficiarios_morales`
--

LOCK TABLES `beneficiarios_morales` WRITE;
/*!40000 ALTER TABLE `beneficiarios_morales` DISABLE KEYS */;
INSERT INTO `beneficiarios_morales` VALUES (5,'Albergue Esperanza',NULL),(7,'Comunidad Rancho Nuevo',NULL),(13,'Refugio Canino Ángeles',NULL);
/*!40000 ALTER TABLE `beneficiarios_morales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campanas`
--

DROP TABLE IF EXISTS `campanas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `campanas` (
  `id_campana` int NOT NULL AUTO_INCREMENT,
  `id_usuario_creador` int NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tipo_meta` enum('economica','especie','mixta','servicio') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_economica` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fecha_inicio` date NOT NULL,
  `fecha_cierre` date NOT NULL,
  `estado` enum('borrador','activa','pausada','cerrada','cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
  `imagen_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_campana`),
  KEY `fk_campanas_usuario` (`id_usuario_creador`),
  KEY `idx_campanas_estado` (`estado`),
  CONSTRAINT `fk_campanas_usuario` FOREIGN KEY (`id_usuario_creador`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `chk_campana_fechas` CHECK ((`fecha_cierre` >= `fecha_inicio`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campanas`
--

LOCK TABLES `campanas` WRITE;
/*!40000 ALTER TABLE `campanas` DISABLE KEYS */;
/*!40000 ALTER TABLE `campanas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comunidades`
--

DROP TABLE IF EXISTS `comunidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comunidades` (
  `id_comunidad` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `municipio` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Puebla',
  PRIMARY KEY (`id_comunidad`),
  UNIQUE KEY `uq_comunidad` (`nombre`,`municipio`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comunidades`
--

LOCK TABLES `comunidades` WRITE;
/*!40000 ALTER TABLE `comunidades` DISABLE KEYS */;
INSERT INTO `comunidades` VALUES (1,'San Andrés Cholula','San Andrés Cholula','Puebla'),(2,'San Martín Texmelucan','San Martín Texmelucan','Puebla'),(3,'Teziutlán','Teziutlán','Puebla'),(4,'Huauchinango','Huauchinango','Puebla'),(5,'Tehuacán','Tehuacán','Puebla'),(6,'Atlixco','Atlixco','Puebla'),(7,'Rancho Nuevo','Puebla','Puebla'),(8,'Ciudad Serdán','Ciudad Serdán','Puebla'),(9,'Izúcar de Matamoros','Izúcar de Matamoros','Puebla'),(10,'Chignahuapan','Chignahuapan','Puebla'),(11,'Zacatlán','Zacatlán','Puebla'),(12,'Tepexi de Rodríguez','Tepexi de Rodríguez','Puebla'),(13,'Puebla Centro','Puebla','Puebla'),(14,'Huejotzingo','Huejotzingo','Puebla'),(15,'Puebla','Puebla','Puebla');
/*!40000 ALTER TABLE `comunidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donaciones`
--

DROP TABLE IF EXISTS `donaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donaciones` (
  `id_donacion` int NOT NULL AUTO_INCREMENT,
  `id_donador` int NOT NULL,
  `id_campana` int NOT NULL,
  `id_usuario_registrador` int NOT NULL,
  `fecha_recepcion` date NOT NULL,
  `estado` enum('pendiente','recibida','verificada','rechazada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `evidencia_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_donacion`),
  KEY `fk_don_registra` (`id_usuario_registrador`),
  KEY `idx_donaciones_estado` (`estado`),
  KEY `idx_donaciones_fecha` (`fecha_recepcion`),
  KEY `idx_donaciones_donador` (`id_donador`),
  KEY `idx_donaciones_campana` (`id_campana`),
  CONSTRAINT `fk_don_campana` FOREIGN KEY (`id_campana`) REFERENCES `campanas` (`id_campana`),
  CONSTRAINT `fk_don_donador` FOREIGN KEY (`id_donador`) REFERENCES `donadores` (`id_donador`),
  CONSTRAINT `fk_don_registra` FOREIGN KEY (`id_usuario_registrador`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donaciones`
--

LOCK TABLES `donaciones` WRITE;
/*!40000 ALTER TABLE `donaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `donaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donaciones_economicas`
--

DROP TABLE IF EXISTS `donaciones_economicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donaciones_economicas` (
  `id_donacion` int NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `moneda` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MXN',
  `metodo_pago` enum('efectivo','transferencia','cheque','otro') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia_pago` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_donacion`),
  CONSTRAINT `fk_eco_donacion` FOREIGN KEY (`id_donacion`) REFERENCES `donaciones` (`id_donacion`) ON DELETE CASCADE,
  CONSTRAINT `chk_eco_monto` CHECK ((`monto` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donaciones_economicas`
--

LOCK TABLES `donaciones_economicas` WRITE;
/*!40000 ALTER TABLE `donaciones_economicas` DISABLE KEYS */;
/*!40000 ALTER TABLE `donaciones_economicas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donaciones_especie`
--

DROP TABLE IF EXISTS `donaciones_especie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donaciones_especie` (
  `id_donacion` int NOT NULL,
  `id_tipo_bien` int NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cantidad` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unidad_medida` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_donacion`),
  KEY `fk_esp_tipo_bien` (`id_tipo_bien`),
  CONSTRAINT `fk_esp_donacion` FOREIGN KEY (`id_donacion`) REFERENCES `donaciones` (`id_donacion`) ON DELETE CASCADE,
  CONSTRAINT `fk_esp_tipo_bien` FOREIGN KEY (`id_tipo_bien`) REFERENCES `tipos_bien` (`id_tipo_bien`),
  CONSTRAINT `chk_esp_cantidad` CHECK ((`cantidad` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donaciones_especie`
--

LOCK TABLES `donaciones_especie` WRITE;
/*!40000 ALTER TABLE `donaciones_especie` DISABLE KEYS */;
/*!40000 ALTER TABLE `donaciones_especie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donador_insignias`
--

DROP TABLE IF EXISTS `donador_insignias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donador_insignias` (
  `id_donador` int NOT NULL,
  `id_insignia` int NOT NULL,
  `fecha_obtencion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario_asignador` int NOT NULL,
  PRIMARY KEY (`id_donador`,`id_insignia`),
  KEY `fk_di_insignia` (`id_insignia`),
  KEY `fk_di_asignador` (`id_usuario_asignador`),
  CONSTRAINT `fk_di_asignador` FOREIGN KEY (`id_usuario_asignador`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_di_donador` FOREIGN KEY (`id_donador`) REFERENCES `donadores` (`id_donador`),
  CONSTRAINT `fk_di_insignia` FOREIGN KEY (`id_insignia`) REFERENCES `insignias` (`id_insignia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donador_insignias`
--

LOCK TABLES `donador_insignias` WRITE;
/*!40000 ALTER TABLE `donador_insignias` DISABLE KEYS */;
/*!40000 ALTER TABLE `donador_insignias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donadores`
--

DROP TABLE IF EXISTS `donadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donadores` (
  `id_donador` int NOT NULL AUTO_INCREMENT,
  `id_nivel` int DEFAULT NULL,
  `tipo_donante` enum('anonimo','persona','grupo','organizacion') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'persona',
  `puntos_acumulados` int NOT NULL DEFAULT '0',
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_donador`),
  UNIQUE KEY `uq_donador_email` (`email`),
  KEY `fk_donadores_nivel` (`id_nivel`),
  KEY `idx_donadores_tipo` (`tipo_donante`),
  CONSTRAINT `fk_donadores_nivel` FOREIGN KEY (`id_nivel`) REFERENCES `niveles_gamificacion` (`id_nivel`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donadores`
--

LOCK TABLES `donadores` WRITE;
/*!40000 ALTER TABLE `donadores` DISABLE KEYS */;
INSERT INTO `donadores` VALUES (2,2,'persona',180,'maria.lopez@email.com','2221100002',1,'2024-01-25 11:00:00'),(3,4,'organizacion',620,'contacto@empresaalfa.mx','2221100003',1,'2024-02-01 09:00:00'),(4,2,'persona',310,'pedro.mtz@email.com','2221100004',1,'2024-02-10 10:00:00'),(5,3,'persona',480,'elena.rojas@email.com','2221100005',1,'2024-02-15 12:00:00'),(6,5,'organizacion',1200,'info@fundacionluz.mx','2221100006',1,'2024-02-20 08:00:00'),(7,1,'persona',50,'luis.torres@email.com','2221100007',1,'2024-03-01 09:00:00'),(8,3,'persona',420,'carmen.vaz@email.com','2221100008',1,'2024-03-05 10:00:00'),(9,2,'persona',260,'arturo.diaz@email.com','2221100009',1,'2024-03-10 11:00:00'),(10,4,'grupo',750,'admin@coopunida.mx','2221100010',1,'2024-03-15 08:00:00'),(11,1,'persona',70,'fernanda.rios@email.com','2221100011',1,'2024-04-01 10:00:00'),(12,2,'persona',190,'oscar.campos@email.com','2221100012',1,'2024-04-05 11:00:00'),(13,3,'grupo',450,'hola@gruposemilla.mx','2221100013',1,'2024-04-10 09:00:00'),(22,NULL,'persona',0,'fernandoserranorosales9@gmail.com','',0,'2026-05-18 21:13:37');
/*!40000 ALTER TABLE `donadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donadores_fisicos`
--

DROP TABLE IF EXISTS `donadores_fisicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donadores_fisicos` (
  `id_donador` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `curp` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  PRIMARY KEY (`id_donador`),
  UNIQUE KEY `uq_curp` (`curp`),
  CONSTRAINT `fk_df_donador` FOREIGN KEY (`id_donador`) REFERENCES `donadores` (`id_donador`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donadores_fisicos`
--

LOCK TABLES `donadores_fisicos` WRITE;
/*!40000 ALTER TABLE `donadores_fisicos` DISABLE KEYS */;
INSERT INTO `donadores_fisicos` VALUES (2,'María','López Suárez',NULL,NULL),(4,'Pedro','Martínez Cruz',NULL,NULL),(5,'Elena','Rojas Ángeles',NULL,NULL),(7,'Luis','Torres Guerrero',NULL,NULL),(8,'Carmen','Vázquez Ibáñez',NULL,NULL),(9,'Arturo','Díaz Méndez',NULL,NULL),(11,'Fernanda','Ríos Estrada',NULL,NULL),(12,'Óscar','Campos Leiva',NULL,NULL),(22,'Fernando','Serrano R',NULL,NULL);
/*!40000 ALTER TABLE `donadores_fisicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donadores_grupos`
--

DROP TABLE IF EXISTS `donadores_grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donadores_grupos` (
  `id_donador` int NOT NULL,
  `nombre_grupo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `representante` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_donador`),
  CONSTRAINT `fk_dg_donador` FOREIGN KEY (`id_donador`) REFERENCES `donadores` (`id_donador`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donadores_grupos`
--

LOCK TABLES `donadores_grupos` WRITE;
/*!40000 ALTER TABLE `donadores_grupos` DISABLE KEYS */;
INSERT INTO `donadores_grupos` VALUES (10,'Cooperativa Unida','Lucía Peralta'),(13,'Grupo Semilla','Marcos Ibáñez');
/*!40000 ALTER TABLE `donadores_grupos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donadores_morales`
--

DROP TABLE IF EXISTS `donadores_morales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donadores_morales` (
  `id_donador` int NOT NULL,
  `razon_social` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfc` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `representante_legal` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `giro_comercial` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_donador`),
  UNIQUE KEY `uq_rfc_donador` (`rfc`),
  CONSTRAINT `fk_dm_donador` FOREIGN KEY (`id_donador`) REFERENCES `donadores` (`id_donador`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donadores_morales`
--

LOCK TABLES `donadores_morales` WRITE;
/*!40000 ALTER TABLE `donadores_morales` DISABLE KEYS */;
INSERT INTO `donadores_morales` VALUES (3,'Empresa Alfa',NULL,NULL,'Empresa privada'),(6,'Fundación Luz',NULL,NULL,'Fundación sin fines de lucro');
/*!40000 ALTER TABLE `donadores_morales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entregas`
--

DROP TABLE IF EXISTS `entregas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entregas` (
  `id_entrega` int NOT NULL AUTO_INCREMENT,
  `id_donacion` int NOT NULL,
  `id_beneficiario` int NOT NULL,
  `id_usuario_responsable` int NOT NULL,
  `cantidad_entregada` decimal(12,2) NOT NULL,
  `fecha_entrega` datetime NOT NULL,
  `evidencia_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  `estado` enum('programada','en_proceso','completada','cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'programada',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_entrega`),
  KEY `fk_entrega_donacion` (`id_donacion`),
  KEY `fk_entrega_benefic` (`id_beneficiario`),
  KEY `fk_entrega_responsable` (`id_usuario_responsable`),
  KEY `idx_entregas_estado` (`estado`),
  KEY `idx_entregas_fecha` (`fecha_entrega`),
  CONSTRAINT `fk_entrega_benefic` FOREIGN KEY (`id_beneficiario`) REFERENCES `beneficiarios` (`id_beneficiario`),
  CONSTRAINT `fk_entrega_donacion` FOREIGN KEY (`id_donacion`) REFERENCES `donaciones` (`id_donacion`),
  CONSTRAINT `fk_entrega_responsable` FOREIGN KEY (`id_usuario_responsable`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entregas`
--

LOCK TABLES `entregas` WRITE;
/*!40000 ALTER TABLE `entregas` DISABLE KEYS */;
/*!40000 ALTER TABLE `entregas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_accesos`
--

DROP TABLE IF EXISTS `historial_accesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_accesos` (
  `id_acceso` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `tipo_accion` enum('login','logout','fallo_login','recuperacion') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exitoso` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_acceso`),
  KEY `idx_historial_usuario` (`id_usuario`,`fecha_hora`),
  CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_accesos`
--

LOCK TABLES `historial_accesos` WRITE;
/*!40000 ALTER TABLE `historial_accesos` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_accesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `insignias`
--

DROP TABLE IF EXISTS `insignias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `insignias` (
  `id_insignia` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `puntos_otorgados` int NOT NULL DEFAULT '0',
  `imagen_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_insignia`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `insignias`
--

LOCK TABLES `insignias` WRITE;
/*!40000 ALTER TABLE `insignias` DISABLE KEYS */;
INSERT INTO `insignias` VALUES (1,'Primera Donación','Otorgada al realizar la primera donación',50,'img/i_primera.png',1),(2,'Donador del Mes','Mayor donación en el mes',200,'img/i_mes.png',1),(3,'Corazón Solidario','Tres donaciones consecutivas',150,'img/i_corazon.png',1),(4,'Voluntario Estrella','Completó 10 entregas exitosas',300,'img/i_estrella.png',1),(5,'Gran Benefactor','Donación mayor a $10,000 MXN',400,'img/i_benefactor.png',1);
/*!40000 ALTER TABLE `insignias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventario`
--

DROP TABLE IF EXISTS `inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventario` (
  `id_inventario` int NOT NULL AUTO_INCREMENT,
  `id_tipo_bien` int NOT NULL,
  `cantidad_total_recibida` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cantidad_total_entregada` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cantidad_disponible` decimal(12,2) GENERATED ALWAYS AS ((`cantidad_total_recibida` - `cantidad_total_entregada`)) STORED,
  `ultima_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_usuario_actualizador` int NOT NULL,
  PRIMARY KEY (`id_inventario`),
  UNIQUE KEY `uq_inv_tipo` (`id_tipo_bien`),
  KEY `fk_inv_usuario` (`id_usuario_actualizador`),
  CONSTRAINT `fk_inv_tipo` FOREIGN KEY (`id_tipo_bien`) REFERENCES `tipos_bien` (`id_tipo_bien`),
  CONSTRAINT `fk_inv_usuario` FOREIGN KEY (`id_usuario_actualizador`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `chk_inv_entregada` CHECK ((`cantidad_total_entregada` <= `cantidad_total_recibida`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventario`
--

LOCK TABLES `inventario` WRITE;
/*!40000 ALTER TABLE `inventario` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos_inventario`
--

DROP TABLE IF EXISTS `movimientos_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimientos_inventario` (
  `id_movimiento` int NOT NULL AUTO_INCREMENT,
  `id_inventario` int NOT NULL,
  `id_usuario` int NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `cantidad_anterior` decimal(12,2) NOT NULL,
  `cantidad_posterior` decimal(12,2) NOT NULL,
  `tipo_referencia` enum('donacion','entrega','ajuste_manual') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_referencia` int DEFAULT NULL,
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_movimiento`),
  KEY `fk_mov_inventario` (`id_inventario`),
  KEY `fk_mov_usuario` (`id_usuario`),
  CONSTRAINT `fk_mov_inventario` FOREIGN KEY (`id_inventario`) REFERENCES `inventario` (`id_inventario`),
  CONSTRAINT `fk_mov_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos_inventario`
--

LOCK TABLES `movimientos_inventario` WRITE;
/*!40000 ALTER TABLE `movimientos_inventario` DISABLE KEYS */;
/*!40000 ALTER TABLE `movimientos_inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `niveles_gamificacion`
--

DROP TABLE IF EXISTS `niveles_gamificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `niveles_gamificacion` (
  `id_nivel` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `puntos_minimos` int NOT NULL DEFAULT '0',
  `puntos_maximos` int NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `imagen_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_nivel`),
  CONSTRAINT `chk_nivel_rango` CHECK ((`puntos_maximos` > `puntos_minimos`))
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `niveles_gamificacion`
--

LOCK TABLES `niveles_gamificacion` WRITE;
/*!40000 ALTER TABLE `niveles_gamificacion` DISABLE KEYS */;
INSERT INTO `niveles_gamificacion` VALUES (1,'Semilla',0,99,'Primer nivel del donador','img/semilla.png'),(2,'Brote',100,249,'Empieza a contribuir regularmente','img/brote.png'),(3,'Árbol Joven',250,499,'Donador comprometido','img/arbol_joven.png'),(4,'Árbol Maduro',500,999,'Alto nivel de participación','img/arbol_maduro.png'),(5,'Selva',1000,1999,'Donador de gran impacto','img/selva.png'),(6,'Guardián',2000,3499,'Protector activo de la comunidad','img/guardian.png'),(7,'Héroe Local',3500,5999,'Reconocido por la comunidad','img/heroe.png'),(8,'Leyenda',6000,9999,'Impacto transformador','img/leyenda.png'),(9,'Embajador',10000,19999,'Representante oficial de iskalli','img/embajador.png'),(10,'Fundador',20000,49999,'Pilar del proyecto desde sus inicios','img/fundador.png');
/*!40000 ALTER TABLE `niveles_gamificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `id_notificacion` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int DEFAULT NULL,
  `destinatario_email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('sistema','campana','donacion','entrega','insignia','alerta','actividad','asistencia','reconocimiento') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `canal` set('web','app') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `asunto` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `enviado_email` tinyint(1) NOT NULL DEFAULT '0',
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  `id_referencia` int DEFAULT NULL,
  `id_tipo_referencia` int DEFAULT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notificacion`),
  KEY `fk_notif_tipo_ref` (`id_tipo_referencia`),
  KEY `idx_notif_leida` (`id_usuario`,`leida`),
  KEY `idx_notif_canal` (`canal`),
  CONSTRAINT `fk_notif_tipo_ref` FOREIGN KEY (`id_tipo_referencia`) REFERENCES `tipos_referencia_notif` (`id_tipo_ref`),
  CONSTRAINT `fk_notif_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reconocimientos`
--

DROP TABLE IF EXISTS `reconocimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reconocimientos` (
  `id_reconocimiento` int NOT NULL AUTO_INCREMENT,
  `id_donador` int DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  `id_campana` int DEFAULT NULL,
  `tipo` enum('diploma','carta','certificado','otro','voluntario_mes','mayor_asistencia','mayor_entregas','donador_destacado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `archivo_pdf_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_emision` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario_emisor` int NOT NULL,
  `otorgado_por` int DEFAULT NULL,
  PRIMARY KEY (`id_reconocimiento`),
  KEY `fk_reconoc_donador` (`id_donador`),
  KEY `fk_reconoc_usuario` (`id_usuario`),
  KEY `fk_reconoc_campana` (`id_campana`),
  KEY `fk_reconoc_emisor` (`id_usuario_emisor`),
  KEY `fk_reconoc_otorgador` (`otorgado_por`),
  CONSTRAINT `fk_reconoc_campana` FOREIGN KEY (`id_campana`) REFERENCES `campanas` (`id_campana`),
  CONSTRAINT `fk_reconoc_donador` FOREIGN KEY (`id_donador`) REFERENCES `donadores` (`id_donador`),
  CONSTRAINT `fk_reconoc_emisor` FOREIGN KEY (`id_usuario_emisor`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_reconoc_otorgador` FOREIGN KEY (`otorgado_por`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_reconoc_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `chk_reconoc_receptor` CHECK (((`id_donador` is not null) or (`id_usuario` is not null)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reconocimientos`
--

LOCK TABLES `reconocimientos` WRITE;
/*!40000 ALTER TABLE `reconocimientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `reconocimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recuperacion_contrasena`
--

DROP TABLE IF EXISTS `recuperacion_contrasena`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recuperacion_contrasena` (
  `id_recuperacion` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_recuperacion`),
  UNIQUE KEY `uq_token` (`token`),
  KEY `fk_recuperacion_usuario` (`id_usuario`),
  CONSTRAINT `fk_recuperacion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recuperacion_contrasena`
--

LOCK TABLES `recuperacion_contrasena` WRITE;
/*!40000 ALTER TABLE `recuperacion_contrasena` DISABLE KEYS */;
/*!40000 ALTER TABLE `recuperacion_contrasena` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respaldos`
--

DROP TABLE IF EXISTS `respaldos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `respaldos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo_operacion` enum('EXPORTACION','IMPORTACION') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_archivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `formato` enum('SQL','ZIP','JSON') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ZIP',
  `nombre_bd` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tamanio_bytes` bigint unsigned DEFAULT NULL,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `fechayhora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respaldos`
--

LOCK TABLES `respaldos` WRITE;
/*!40000 ALTER TABLE `respaldos` DISABLE KEYS */;
INSERT INTO `respaldos` VALUES (1,'IMPORTACION','respaldo_20260513_161250.zip','ZIP','iskali',12181,16,'2026-05-13 21:13:28','Restauración manual desde el panel de administración.'),(2,'EXPORTACION','respaldo_20260516_000238.zip','ZIP','iskali',12320,16,'2026-05-16 05:02:39','Respaldo manual generado desde el panel.'),(3,'IMPORTACION','respaldo_20260516_215139.zip','ZIP','iskali',12380,16,'2026-05-17 02:51:59','Restauración manual desde el panel de administración.'),(4,'EXPORTACION','respaldo_20260516_215229.zip','ZIP','iskali',12406,16,'2026-05-17 02:52:30','Respaldo manual generado desde el panel.'),(5,'EXPORTACION','respaldo_20260517_132059.zip','ZIP','iskali',12430,16,'2026-05-17 18:21:04','Respaldo manual generado desde el panel.'),(6,'EXPORTACION','respaldo_20260517_210219.zip','ZIP','iskali',12451,16,'2026-05-18 02:02:20','Respaldo manual generado desde el panel.'),(7,'EXPORTACION','respaldo_20260518_095924.zip','ZIP','iskali',12171,16,'2026-05-18 14:59:26','Respaldo manual generado desde el panel.'),(8,'IMPORTACION','respaldo_20260518_101216.zip','ZIP','iskali',12264,16,'2026-05-18 15:12:45','Restauración manual desde el panel de administración.'),(9,'EXPORTACION','respaldo_20260518_220709.zip','ZIP','iskali',12299,16,'2026-05-19 03:07:11','Respaldo manual generado desde el panel.'),(10,'EXPORTACION','respaldo_20260518_220851.zip','ZIP','iskali',12326,16,'2026-05-19 03:08:54','Respaldo manual generado desde el panel.'),(11,'EXPORTACION','respaldo_20260518_220854.zip','ZIP','iskali',12340,16,'2026-05-19 03:08:56','Respaldo manual generado desde el panel.'),(12,'EXPORTACION','respaldo_20260518_220856.zip','ZIP','iskali',12356,16,'2026-05-19 03:08:58','Respaldo manual generado desde el panel.'),(13,'EXPORTACION','respaldo_20260518_221225.zip','ZIP','iskali',12371,16,'2026-05-19 03:12:26','Respaldo manual generado desde el panel.');
/*!40000 ALTER TABLE `respaldos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `uq_rol_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','Acceso total al sistema',1),(2,'Coordinador','Gestiona campañas y donaciones',1),(3,'Voluntario','Apoya en entregas y seguimiento',1),(4,'Auditor','Solo lectura para revisión contable',1),(5,'Donador','Perfil de donante con acceso limitado',1),(6,'Comunicación','Gestiona noticias y reconocimientos',1),(7,'Inventarista','Administra entradas y salidas de inventario',1),(8,'Beneficiario','Rol informativo de beneficiarios registrados',0),(9,'Supervisor','Supervisa voluntarios y entregas en campo',1),(10,'Soporte','Atiende solicitudes del sistema',1),(11,'Logística','Planifica rutas y asignaciones de entregas',1),(12,'Analista','Genera reportes y estadísticas',1),(13,'Captador','Registra nuevas donaciones en campo',1),(14,'Legal','Valida contratos y documentación oficial',1),(15,'Externo','Rol temporal para colaboraciones externas',0);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_apoyo`
--

DROP TABLE IF EXISTS `tipos_apoyo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_apoyo` (
  `id_tipo_apoyo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_tipo_apoyo`),
  UNIQUE KEY `uq_tipo_apoyo` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_apoyo`
--

LOCK TABLES `tipos_apoyo` WRITE;
/*!40000 ALTER TABLE `tipos_apoyo` DISABLE KEYS */;
INSERT INTO `tipos_apoyo` VALUES (1,'Material escolar','Útiles y material para estudio',1),(2,'Despensa','Paquetes de alimentos básicos',1),(3,'Medicamentos','Medicamentos y atención médica',1),(4,'Cobijas','Ropa de cama y abrigo',1),(5,'Juguetes','Juguetes para menores de edad',1),(6,'Empleo joven','Capacitación y apoyo para jóvenes',1),(7,'Agua potable','Distribución de agua potable',1),(8,'Cobijas/ropa','Cobijas y ropa de abrigo combinados',1),(9,'Rescate animal','Atención veterinaria y adopción',1),(10,'Servicio médico','Consultas y atención médica gratuita',1);
/*!40000 ALTER TABLE `tipos_apoyo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_bien`
--

DROP TABLE IF EXISTS `tipos_bien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_bien` (
  `id_tipo_bien` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidad_medida` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_tipo_bien`),
  UNIQUE KEY `uq_bien_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_bien`
--

LOCK TABLES `tipos_bien` WRITE;
/*!40000 ALTER TABLE `tipos_bien` DISABLE KEYS */;
INSERT INTO `tipos_bien` VALUES (1,'Despensa Básica','paquete','Paquete de alimentos básicos',1),(2,'Ropa Usada','kg','Ropa en buen estado',1),(3,'Medicamento','caja','Medicamentos no caducados',1),(4,'Material Escolar','set','Útiles escolares completos',1),(5,'Juguetes','pieza','Juguetes en buen estado',1),(6,'Computadora','pieza','Equipo de cómputo funcional',1),(7,'Cobija o Colcha','pieza','Ropa de cama en buen estado',1),(8,'Agua Embotellada','garrafón','Agua potable para zonas de escasez',1),(9,'Donativo en Especie Misc.','pieza','Artículos varios en buen estado',1);
/*!40000 ALTER TABLE `tipos_bien` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_referencia_notif`
--

DROP TABLE IF EXISTS `tipos_referencia_notif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_referencia_notif` (
  `id_tipo_ref` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_tipo_ref`),
  UNIQUE KEY `uq_ref_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_referencia_notif`
--

LOCK TABLES `tipos_referencia_notif` WRITE;
/*!40000 ALTER TABLE `tipos_referencia_notif` DISABLE KEYS */;
INSERT INTO `tipos_referencia_notif` VALUES (1,'campana','Referencia a la tabla campanas'),(2,'donacion','Referencia a la tabla donaciones'),(3,'entrega','Referencia a la tabla entregas'),(4,'insignia','Referencia a la tabla insignias'),(5,'actividad','Referencia a la tabla actividades'),(6,'asistencia','Referencia a la tabla asistencia_voluntarios'),(7,'reconocimiento','Referencia a la tabla reconocimientos');
/*!40000 ALTER TABLE `tipos_referencia_notif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `id_rol` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contrasena_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `intentos_fallidos` int NOT NULL DEFAULT '0',
  `fecha_bloqueo_temporal` datetime DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uq_usuario_email` (`email`),
  KEY `fk_usuarios_rol` (`id_rol`),
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (2,2,'Carlos','Mendoza López','carlos.coord@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-01-15 09:00:00'),(3,3,'Sofía','Gómez Torres','sofia.vol@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-02-01 10:00:00'),(4,3,'Miguel','Ramírez Castillo','miguel.vol@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-02-05 10:30:00'),(5,2,'Andrea','Flores Jiménez','andrea.coord@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-02-10 11:00:00'),(6,6,'Roberto','Sánchez Vega','roberto.com@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-02-20 08:30:00'),(7,7,'Diana','Cruz Morales','diana.inv@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-03-01 09:15:00'),(8,9,'Héctor','Vargas Ríos','hector.sup@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-03-05 07:45:00'),(9,10,'Valeria','Luna Espinoza','valeria.sop@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-03-10 08:00:00'),(10,4,'Jorge','Ortiz Salinas','jorge.aud@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-03-15 09:00:00'),(11,13,'Patricia','Núñez Cabrera','patricia.cap@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-04-01 10:00:00'),(12,11,'Ernesto','Medina Villanueva','ernesto.log@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-04-05 11:00:00'),(13,12,'Isabel','Peña Durán','isabel.ana@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-04-10 08:30:00'),(14,5,'Martín','Aguilar Ochoa','martin.don@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-04-15 09:30:00'),(15,15,'Claudia','Reyes Montes','claudia.ext@iskalli.mx','$2y$10$q9xr8MiezW4JpL9K6KtbXu18BUHs043/EQRpxsFfaU0zFEGgf8vem',1,0,NULL,NULL,'2024-05-01 10:00:00'),(16,1,'Admin','Sistema','admin@iskali.mx','$2b$10$cGx0.CrtxGlnL3IwfZCPYuadZomiu4KRXDNAVHpbiFfIW5yIdbtm6',1,0,NULL,'2026-05-19 00:20:39','2026-05-12 14:17:55'),(20,1,'Luis Fernando','Rosales Serrano','l23240044@smartin.tecnm.mx','$2y$10$IIPWNY6jK8RPPc5xFZnhF.n61ZVLgs45zMNT0dFTR/DIAVyVQGMQy',1,1,NULL,NULL,'2026-05-18 21:06:30');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `v_actividades`
--

DROP TABLE IF EXISTS `v_actividades`;
/*!50001 DROP VIEW IF EXISTS `v_actividades`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_actividades` AS SELECT 
 1 AS `id_actividad`,
 1 AS `titulo`,
 1 AS `descripcion`,
 1 AS `fecha_inicio`,
 1 AS `fecha_fin`,
 1 AS `zona`,
 1 AS `estado`,
 1 AS `responsable`,
 1 AS `creado_en`,
 1 AS `total_asistentes`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_beneficiarios`
--

DROP TABLE IF EXISTS `v_beneficiarios`;
/*!50001 DROP VIEW IF EXISTS `v_beneficiarios`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_beneficiarios` AS SELECT 
 1 AS `id_beneficiario`,
 1 AS `tipo_persona`,
 1 AS `nombre_completo`,
 1 AS `edad`,
 1 AS `curp`,
 1 AS `rfc`,
 1 AS `comunidad`,
 1 AS `municipio`,
 1 AS `direccion`,
 1 AS `telefono`,
 1 AS `estado`,
 1 AS `notas`,
 1 AS `tipos_apoyo`,
 1 AS `registrado_por`,
 1 AS `created_at`,
 1 AS `updated_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_donaciones`
--

DROP TABLE IF EXISTS `v_donaciones`;
/*!50001 DROP VIEW IF EXISTS `v_donaciones`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_donaciones` AS SELECT 
 1 AS `id_donacion`,
 1 AS `fecha_recepcion`,
 1 AS `estado`,
 1 AS `tipo_donacion`,
 1 AS `descripcion`,
 1 AS `cantidad`,
 1 AS `unidad_medida`,
 1 AS `tipo_bien`,
 1 AS `monto`,
 1 AS `moneda`,
 1 AS `metodo_pago`,
 1 AS `referencia_pago`,
 1 AS `donador`,
 1 AS `tipo_donante`,
 1 AS `campana`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_donadores`
--

DROP TABLE IF EXISTS `v_donadores`;
/*!50001 DROP VIEW IF EXISTS `v_donadores`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_donadores` AS SELECT 
 1 AS `id_donador`,
 1 AS `tipo_donante`,
 1 AS `nombre_completo`,
 1 AS `curp`,
 1 AS `rfc`,
 1 AS `representante_legal`,
 1 AS `representante_grupo`,
 1 AS `email`,
 1 AS `telefono`,
 1 AS `puntos_acumulados`,
 1 AS `nivel`,
 1 AS `activo`,
 1 AS `created_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_inventario`
--

DROP TABLE IF EXISTS `v_inventario`;
/*!50001 DROP VIEW IF EXISTS `v_inventario`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_inventario` AS SELECT 
 1 AS `id_inventario`,
 1 AS `tipo_bien`,
 1 AS `unidad_medida`,
 1 AS `cantidad_total_recibida`,
 1 AS `cantidad_total_entregada`,
 1 AS `cantidad_disponible`,
 1 AS `ultima_actualizacion`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_reconocimientos`
--

DROP TABLE IF EXISTS `v_reconocimientos`;
/*!50001 DROP VIEW IF EXISTS `v_reconocimientos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_reconocimientos` AS SELECT 
 1 AS `id_reconocimiento`,
 1 AS `tipo`,
 1 AS `descripcion`,
 1 AS `fecha_emision`,
 1 AS `receptor`,
 1 AS `emitido_por`,
 1 AS `campana`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `voluntarios`
--

DROP TABLE IF EXISTS `voluntarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voluntarios` (
  `id_voluntario` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `telefono_contacto` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zona_asignada` enum('san_martin','tlaxcala','ambas','otra') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ambas',
  `disponibilidad` enum('tiempo_completo','medio_tiempo','fines_semana','bajo_demanda') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_ingreso` date NOT NULL,
  `notas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_voluntario`),
  UNIQUE KEY `uq_vol_usuario` (`id_usuario`),
  CONSTRAINT `fk_voluntarios_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voluntarios`
--

LOCK TABLES `voluntarios` WRITE;
/*!40000 ALTER TABLE `voluntarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `voluntarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'iskali'
--

--
-- Final view structure for view `v_actividades`
--

/*!50001 DROP VIEW IF EXISTS `v_actividades`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_actividades` AS select `a`.`id_actividad` AS `id_actividad`,`a`.`titulo` AS `titulo`,`a`.`descripcion` AS `descripcion`,`a`.`fecha_inicio` AS `fecha_inicio`,`a`.`fecha_fin` AS `fecha_fin`,`a`.`zona` AS `zona`,`a`.`estado` AS `estado`,concat(`u`.`nombre`,' ',`u`.`apellido`) AS `responsable`,`a`.`creado_en` AS `creado_en`,count(`av`.`id_asistencia`) AS `total_asistentes` from ((`actividades` `a` left join `usuarios` `u` on((`a`.`id_responsable` = `u`.`id_usuario`))) left join `asistencia_voluntarios` `av` on(((`a`.`id_actividad` = `av`.`id_actividad`) and (`av`.`presente` = 1)))) group by `a`.`id_actividad`,`a`.`titulo`,`a`.`descripcion`,`a`.`fecha_inicio`,`a`.`fecha_fin`,`a`.`zona`,`a`.`estado`,`u`.`nombre`,`u`.`apellido`,`a`.`creado_en` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_beneficiarios`
--

/*!50001 DROP VIEW IF EXISTS `v_beneficiarios`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_beneficiarios` AS select `b`.`id_beneficiario` AS `id_beneficiario`,`b`.`tipo_persona` AS `tipo_persona`,(case when (`bf`.`id_beneficiario` is not null) then concat(`bf`.`nombre`,' ',`bf`.`apellido`) when (`bm`.`id_beneficiario` is not null) then `bm`.`razon_social` else 'Sin datos' end) AS `nombre_completo`,`bf`.`edad` AS `edad`,`bf`.`curp` AS `curp`,`bm`.`rfc` AS `rfc`,`com`.`nombre` AS `comunidad`,`com`.`municipio` AS `municipio`,`b`.`direccion` AS `direccion`,`b`.`telefono` AS `telefono`,`b`.`estado` AS `estado`,`b`.`notas` AS `notas`,group_concat(`ta`.`nombre` order by `ta`.`nombre` ASC separator ', ') AS `tipos_apoyo`,concat(`u`.`nombre`,' ',`u`.`apellido`) AS `registrado_por`,`b`.`created_at` AS `created_at`,`b`.`updated_at` AS `updated_at` from ((((((`beneficiarios` `b` left join `beneficiarios_fisicos` `bf` on((`b`.`id_beneficiario` = `bf`.`id_beneficiario`))) left join `beneficiarios_morales` `bm` on((`b`.`id_beneficiario` = `bm`.`id_beneficiario`))) left join `comunidades` `com` on((`b`.`id_comunidad` = `com`.`id_comunidad`))) left join `beneficiario_tipos_apoyo` `bta` on((`b`.`id_beneficiario` = `bta`.`id_beneficiario`))) left join `tipos_apoyo` `ta` on((`bta`.`id_tipo_apoyo` = `ta`.`id_tipo_apoyo`))) left join `usuarios` `u` on((`b`.`id_usuario_registrador` = `u`.`id_usuario`))) group by `b`.`id_beneficiario`,`b`.`tipo_persona`,`bf`.`id_beneficiario`,`bm`.`id_beneficiario`,`bf`.`edad`,`bf`.`curp`,`bm`.`rfc`,`com`.`nombre`,`com`.`municipio`,`b`.`direccion`,`b`.`telefono`,`b`.`estado`,`b`.`notas`,`u`.`nombre`,`u`.`apellido`,`b`.`created_at`,`b`.`updated_at` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_donaciones`
--

/*!50001 DROP VIEW IF EXISTS `v_donaciones`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_donaciones` AS select `don`.`id_donacion` AS `id_donacion`,`don`.`fecha_recepcion` AS `fecha_recepcion`,`don`.`estado` AS `estado`,(case when (`de`.`id_donacion` is not null) then 'especie' when (`deco`.`id_donacion` is not null) then 'economica' end) AS `tipo_donacion`,`de`.`descripcion` AS `descripcion`,`de`.`cantidad` AS `cantidad`,`de`.`unidad_medida` AS `unidad_medida`,`tb`.`nombre` AS `tipo_bien`,`deco`.`monto` AS `monto`,`deco`.`moneda` AS `moneda`,`deco`.`metodo_pago` AS `metodo_pago`,`deco`.`referencia_pago` AS `referencia_pago`,coalesce(`dm`.`razon_social`,concat(`df`.`nombre`,' ',`df`.`apellido`),`dg`.`nombre_grupo`,'An�nimo') AS `donador`,`d`.`tipo_donante` AS `tipo_donante`,`c`.`nombre` AS `campana` from ((((((((`donaciones` `don` left join `donaciones_especie` `de` on((`don`.`id_donacion` = `de`.`id_donacion`))) left join `donaciones_economicas` `deco` on((`don`.`id_donacion` = `deco`.`id_donacion`))) left join `tipos_bien` `tb` on((`de`.`id_tipo_bien` = `tb`.`id_tipo_bien`))) left join `donadores` `d` on((`don`.`id_donador` = `d`.`id_donador`))) left join `donadores_fisicos` `df` on((`d`.`id_donador` = `df`.`id_donador`))) left join `donadores_morales` `dm` on((`d`.`id_donador` = `dm`.`id_donador`))) left join `donadores_grupos` `dg` on((`d`.`id_donador` = `dg`.`id_donador`))) left join `campanas` `c` on((`don`.`id_campana` = `c`.`id_campana`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_donadores`
--

/*!50001 DROP VIEW IF EXISTS `v_donadores`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_donadores` AS select `d`.`id_donador` AS `id_donador`,`d`.`tipo_donante` AS `tipo_donante`,(case when (`d`.`tipo_donante` = 'anonimo') then 'An�nimo' when (`d`.`tipo_donante` = 'grupo') then `dg`.`nombre_grupo` when (`df`.`id_donador` is not null) then concat(`df`.`nombre`,' ',`df`.`apellido`) when (`dm`.`id_donador` is not null) then `dm`.`razon_social` else 'Sin datos' end) AS `nombre_completo`,`df`.`curp` AS `curp`,`dm`.`rfc` AS `rfc`,`dm`.`representante_legal` AS `representante_legal`,`dg`.`representante` AS `representante_grupo`,`d`.`email` AS `email`,`d`.`telefono` AS `telefono`,`d`.`puntos_acumulados` AS `puntos_acumulados`,`ng`.`nombre` AS `nivel`,`d`.`activo` AS `activo`,`d`.`created_at` AS `created_at` from ((((`donadores` `d` left join `donadores_fisicos` `df` on((`d`.`id_donador` = `df`.`id_donador`))) left join `donadores_morales` `dm` on((`d`.`id_donador` = `dm`.`id_donador`))) left join `donadores_grupos` `dg` on((`d`.`id_donador` = `dg`.`id_donador`))) left join `niveles_gamificacion` `ng` on((`d`.`id_nivel` = `ng`.`id_nivel`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_inventario`
--

/*!50001 DROP VIEW IF EXISTS `v_inventario`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_inventario` AS select `i`.`id_inventario` AS `id_inventario`,`tb`.`nombre` AS `tipo_bien`,`tb`.`unidad_medida` AS `unidad_medida`,`i`.`cantidad_total_recibida` AS `cantidad_total_recibida`,`i`.`cantidad_total_entregada` AS `cantidad_total_entregada`,`i`.`cantidad_disponible` AS `cantidad_disponible`,`i`.`ultima_actualizacion` AS `ultima_actualizacion` from (`inventario` `i` join `tipos_bien` `tb` on((`i`.`id_tipo_bien` = `tb`.`id_tipo_bien`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_reconocimientos`
--

/*!50001 DROP VIEW IF EXISTS `v_reconocimientos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_reconocimientos` AS select `r`.`id_reconocimiento` AS `id_reconocimiento`,`r`.`tipo` AS `tipo`,`r`.`descripcion` AS `descripcion`,`r`.`fecha_emision` AS `fecha_emision`,(case when (`r`.`id_usuario` is not null) then concat(`u`.`nombre`,' ',`u`.`apellido`) when (`r`.`id_donador` is not null) then coalesce(`dm`.`razon_social`,concat(`df`.`nombre`,' ',`df`.`apellido`)) else 'Sin datos' end) AS `receptor`,concat(`emisor`.`nombre`,' ',`emisor`.`apellido`) AS `emitido_por`,`c`.`nombre` AS `campana` from ((((((`reconocimientos` `r` left join `usuarios` `u` on((`r`.`id_usuario` = `u`.`id_usuario`))) left join `usuarios` `emisor` on((`r`.`id_usuario_emisor` = `emisor`.`id_usuario`))) left join `donadores` `d` on((`r`.`id_donador` = `d`.`id_donador`))) left join `donadores_fisicos` `df` on((`d`.`id_donador` = `df`.`id_donador`))) left join `donadores_morales` `dm` on((`d`.`id_donador` = `dm`.`id_donador`))) left join `campanas` `c` on((`r`.`id_campana` = `c`.`id_campana`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-19  0:20:56

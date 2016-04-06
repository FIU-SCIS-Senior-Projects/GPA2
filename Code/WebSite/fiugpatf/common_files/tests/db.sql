-- MySQL dump 10.13  Distrib 5.6.28, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: GPA_Tracker
-- ------------------------------------------------------
-- Server version	5.6.28-0ubuntu0.15.10.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Assessment`
--

DROP TABLE IF EXISTS `Assessment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Assessment` (
  `assessmentTypeID` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `assessmentID` int(11) NOT NULL AUTO_INCREMENT,
  `studentCourseID` int(11) NOT NULL,
  `dateEntered` date NOT NULL,
  PRIMARY KEY (`assessmentID`),
  KEY `assessmentTypeID` (`assessmentTypeID`),
  KEY `studentCourseID` (`studentCourseID`),
  CONSTRAINT `Assessment_ibfk_1` FOREIGN KEY (`assessmentTypeID`) REFERENCES `AssessmentType` (`assessmentTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Assessment_ibfk_2` FOREIGN KEY (`studentCourseID`) REFERENCES `StudentCourse` (`studentCourseID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Assessment`
--

LOCK TABLES `Assessment` WRITE;
/*!40000 ALTER TABLE `Assessment` DISABLE KEYS */;
INSERT INTO `Assessment` VALUES (16,90,19,2270,'2015-08-31'),(16,60,20,2270,'2015-09-07'),(16,75,21,2270,'2015-09-14'),(16,50,22,2270,'2015-09-21'),(16,89,23,2270,'2015-09-28'),(17,90,24,2270,'2015-10-05'),(19,100,25,2269,'2015-09-01'),(20,95,26,2269,'2015-09-08'),(21,50,27,2269,'2015-09-15'),(22,75,28,2269,'2015-09-22'),(18,90,29,2270,'2015-12-10'),(12,85,31,1340,'2015-12-11'),(12,90,32,1340,'2015-12-11'),(12,79,33,1340,'2015-12-11'),(27,70,34,2643,'2015-12-11'),(27,56,35,2643,'2015-12-11'),(27,90,36,2643,'2015-12-11'),(27,100,37,2643,'2015-12-11'),(28,90,38,2643,'2015-12-11'),(28,50,39,2643,'2015-12-11'),(29,76,40,2643,'2015-12-11');
/*!40000 ALTER TABLE `Assessment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AssessmentType`
--

DROP TABLE IF EXISTS `AssessmentType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AssessmentType` (
  `assessmentName` varchar(35) NOT NULL,
  `percentage` float NOT NULL,
  `studentCourseID` int(11) NOT NULL,
  `assessmentTypeID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`studentCourseID`,`assessmentTypeID`),
  UNIQUE KEY `assessmentTypeID` (`assessmentTypeID`),
  UNIQUE KEY `assessmentName` (`assessmentName`,`studentCourseID`),
  KEY `studentCourseID` (`studentCourseID`),
  CONSTRAINT `AssessmentType_ibfk_1` FOREIGN KEY (`studentCourseID`) REFERENCES `StudentCourse` (`studentCourseID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AssessmentType`
--

LOCK TABLES `AssessmentType` WRITE;
/*!40000 ALTER TABLE `AssessmentType` DISABLE KEYS */;
INSERT INTO `AssessmentType` VALUES ('Homework',30,1340,12),('Quizes',40,1340,13),('Final',30,1340,26),('Lab 1',10,2269,19),('Lab 2',12,2269,20),('Lab 3',15,2269,21),('Lab 4',15,2269,22),('Exam 2',24,2269,24),('Exam 1',24,2269,25),('Homework',30,2270,16),('Midterm',30,2270,17),('Final',40,2270,18),('Homework',30,2643,27),('Quizes',20,2643,28),('Final',50,2643,29),('homework',30,2754,32);
/*!40000 ALTER TABLE `AssessmentType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CourseInfo`
--

DROP TABLE IF EXISTS `CourseInfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CourseInfo` (
  `courseID` varchar(8) NOT NULL,
  `courseName` varchar(45) NOT NULL,
  `credits` int(11) NOT NULL,
  `courseInfoID` int(11) NOT NULL AUTO_INCREMENT,
  `courseDescription` varchar(200) NOT NULL,
  PRIMARY KEY (`courseInfoID`),
  UNIQUE KEY `course_name` (`courseName`),
  UNIQUE KEY `courseInfoID` (`courseInfoID`),
  UNIQUE KEY `courseID` (`courseID`)
) ENGINE=InnoDB AUTO_INCREMENT=2144 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CourseInfo`
--

LOCK TABLES `CourseInfo` WRITE;
/*!40000 ALTER TABLE `CourseInfo` DISABLE KEYS */;
INSERT INTO `CourseInfo` VALUES ('COP3530','Data Structures',3,3,''),('MAC2311','Calculus I',4,4,''),('MAC2312','Calculus II',4,5,''),('MAP2302','Differential Equations',3,6,''),('ENC1101','Writing and Rhetoric I',3,10,''),('EML2032','Programming for ME',3,11,''),('THE2000','Theatre Appreciation',3,13,''),('COP4555','Principles of Programming Languages',3,14,''),('PHY2054','Physics without Calculus II ',4,15,''),('COT3541','Logic for Computer Science',3,16,''),('CDA3103','Fundamentals of Computer Systems',3,17,''),('EGN3365','Materials Eng',3,18,''),('CAP4710','Computer Graphics',3,19,''),('CEN4010','Software Engineering 1',3,20,''),('CHM1045L','General Chemistry Lab I',1,21,''),('PHY2053','Physics without Calculus',4,22,''),('MAC2313','Multivariable Calculus',4,23,''),('AMH2041','Origins of American Civilization',3,24,''),('SLS1501','First Year Experience',1,25,''),('MAD2104','Discrete Mathematics',3,26,''),('EMA3702','Mech & Mat Science',3,28,''),('EGN3321','Dynamics',3,29,''),('EGN3311','Statics',3,31,''),('CEN4072','Software Testing',3,32,''),('EGN1033','Technology,Human and Society',3,33,''),('PHY2048L','Physics with Calculus Lab',1,34,''),('PHY2049L','Physics with Calculus II Lab',1,35,''),('MAD3512','Introduction to Theory of Algorithm',3,36,''),('ENC1102','Writing and Rhetoric II',3,37,''),('NSE3992','Natl Student Exch-',12,38,''),('CGS3095','Technology in the Global Arena',3,39,''),('TRF3000','Transfer Credit-Upper',3,40,''),('MAD3305','Graph Theory',3,41,''),('ARC2701','History of Architecture',3,42,''),('PHY2049','Physics with Calculus II',4,44,''),('CGS1920','Introduction to Computing',1,46,''),('MAS3105','Linear Algebra',3,47,''),('EGN3343','Thermodynamics I',3,48,''),('EGN1002','Engineering Orient',2,49,''),('MAD4203','Introduction to Combinatorics',3,50,''),('ASN3410','Intro to East Asia',3,52,''),('IDH1001','Honors Seminar I',3,53,''),('COP4338','Programming III',3,54,''),('CHM1045','General Chemistry I',3,55,''),('ENC3249','Professional and Technical Writing ',3,56,''),('PHY2048','Physics with Calculus',4,57,''),('ECO2013','Principles of Macroeconomics',3,58,''),('CDA4101','Structured Computer Organization',3,59,''),('CNT4713','Net-centric Computing',3,60,''),('BSC1010','General Biology I',3,62,''),('EIN3354','Engineering Economy',3,64,''),('STA2122','Statistics for Behavioral and Socia',3,65,''),('EML1533','Intro to CAD for ME',3,66,''),('BSC1010L','General Biology I Lab',3,68,''),('ARH2000','Exploring Art',3,193,''),('HUM1020','Introduction to Humanities',3,194,''),('LIT1000','Introduction to Literature',3,195,''),('MUL1010','Music Literature/Music Appreciation',3,196,''),('PHI2010','Introduction to Philosophy',3,197,''),('AFH2000','African Civilizations',3,199,''),('AMH2042','Modern American Civilization',3,201,''),('EUH2011','Western Civ.: Early European Civili',3,203,''),('EUH2021','Western Civ.: Medieval to Modern Eu',3,204,''),('EUH2030','Western Civ.: Europe in the Modern ',3,205,''),('ENG2012','Approaches to Literature',3,206,''),('HUM3214','Ancient Classical Culture and Civil',3,207,''),('HUM3306','History of Ideas',3,208,''),('IDS3309','How We Know What We Know',3,209,''),('LAH2020','Latin American Civilization ',3,210,''),('PHH2063','Classics in Phil.: Intro.to the His',3,211,''),('PHI2600','Introduction to Ethics',3,212,''),('POT3013','Ancient and Medieval Political Theo',3,213,''),('REL2011','Religion: Analysis and Interpretati',3,214,''),('SPC3230','Rhetorical Comm.: A Theory Civil Di',3,215,''),('SPC3271','Rhetoric and Public Address',3,216,''),('WOH2001','World Civilization',3,217,''),('MAC1105','College Algebra',3,218,''),('MGF1106','Finite Math',3,220,''),('MGF1107','Math of Social Choice and Decision ',3,221,''),('STA2023','Statistics for Business and Economics',3,222,''),('CGS2518','Data Analysis',3,223,''),('COP2250','Programming in Java',3,225,''),('MAC1140','Pre-Calculus Algebra',3,226,''),('MAC1114','Trigonometry',3,227,''),('MAC1147','Pre-Calculus Algebra and Trigonomet',4,228,''),('MAC2233','Calculus for Business',3,229,''),('MTG1204','Geometry for Education',3,232,''),('PHI2100','Introduction to Logic',3,233,''),('STA3111','Statistics I',3,235,''),('STA3145','Statistics for the Health Professio',3,236,''),('AMH2020','American History 1850 to Present',3,237,''),('ANT2000','Introduction to Anthropology',3,238,''),('POS2041','American Government',3,240,''),('PSY2012','Introductory Psychology',3,241,''),('AFA2004','Black Popular Cultures: Global Dime',3,243,''),('AMH3560','The History of Women in the U.S.',3,244,''),('ANT3212','World Ethnographies',3,245,''),('ANT3241','Myth, Ritual and Mysticism',3,246,''),('ANT3451','Anthropology of Race and Ethnicity',3,247,''),('COM3461','Intercultural/Interracial Communica',3,249,''),('CPO2002','Introduction to Comparative Politic',3,250,''),('CPO3103','Politics of Western Europe',3,251,''),('CPO3304','Politics of Latin America',3,252,''),('DEP2000','Human Growth and Development',3,253,''),('ECO2023','Principles of Microeconomics',3,254,''),('ECS3003','Comparative Economic Systems',3,255,''),('ECS3021','Women, Culture, and Economic Develo',3,256,''),('EDF3521','Education in History',3,257,''),('EVR1017','The Global Environment and Society',3,259,''),('GEA2000','Introduction to Geography',3,260,''),('IDS3163','Global Supply Chains and Logistics',3,261,''),('IDS3183','Health Without Borders',3,262,''),('IDS3189','Intâ€™l Nutr., Pub. Health and Eco.',3,263,''),('IDS3301','The Culture of Capitalism and Globa',3,264,''),('IDS3315','Gaining Global Perspectives',3,265,''),('IDS3333','Div. of Meaning: Language, Culture,',3,266,''),('INP2002','Intro. Industrial/Organizational Ps',3,267,''),('INR2001','Introduction to International Relat',3,268,''),('INR3081','Contemporary International Problems',3,269,''),('LBS3001','Introduction to Labor Studies',3,270,''),('POT3302','Political Ideologies',3,271,''),('REL3306','Studies in World Religions',3,272,''),('SOP3004','Introductory Social Psychology',3,273,''),('SOP3015','Social and Personality Development',3,274,''),('SPC3210','Communication Theory',3,275,''),('SYD3804','Sociology of Gender',3,276,''),('SYG2010','Social Problems',3,277,''),('SYG3002','Basic Ideas of Sociology',3,278,''),('SYP3000','The Individual in Society',3,279,''),('WST3015','Intro to Global Gender and Womenâ€™',3,280,''),('WST3641','LGBT and Beyond: Sexualities in Gl.',3,281,''),('AST1002','Descriptive Astronomy',3,282,''),('AST1002L','Descriptive Astronomy Lab',3,283,''),('CHM1020','Chemistry for Liberal Studies',3,286,''),('CHM1020L','Chemistry for Liberal Studies Lab',3,287,''),('EVR1001','Intro. To Environ. Sciences',3,290,''),('EVR1001L','Intro. To Environ. Sciences Lab',1,291,''),('ESC1000','Intro. To Earth Science',3,292,''),('ESC1000L','Intro. To Earth Science Lab',1,293,''),('PHY1020','Understanding the Physical World',3,294,''),('PHY1020L','Understanding the Physical World La',1,295,''),('PHY2053L','Physics without Calculus Lab',1,299,''),('BSC1085','Anatomy and Physiology',3,300,''),('BSC1085L','Anatomy and Physiology Lab',1,301,''),('AST2003','Solar System Astronomy',3,302,''),('AST2003L','Solar System Astronomy Lab',1,303,''),('BOT1010','Introductory Botany',3,304,''),('BOT1010L','Introductory Botany Lab',1,305,''),('BSC1011','General Biology II',3,306,''),('BSC1011L','General Biology II Lab',1,307,''),('BSC2023','Human Biology',3,308,''),('BSC2023L','Human Biology Lab',1,309,''),('CHM1033','Survey of Chemistry',4,310,''),('CHM1033L','Survey of Chemistry Lab',1,311,''),('CHS3501','Survey of Forensic Science',3,312,''),('CHS3501L','Survey of Forensic Science Lab',1,313,''),('EVR3011','Environ. Resources and Poll.',3,314,''),('EVR3011L','Environ. Resources and Poll. Lab',1,315,''),('EVR3013','Ecology of South Florida',3,316,''),('EVR3013L','Ecology of South Florida Lab',1,317,''),('GEO3510','Earth Resources',3,318,''),('GEO3510L','Earth Resources Lab',1,319,''),('GLY1010','Physical Geology',3,320,''),('GLY1010L','Physical Geology Lab',1,321,''),('GLY3039','Environmental Geology',3,322,''),('GLY3039L','Environmental Geology Lab',1,323,''),('HUN2000','Found. of Nutritional Sci.',3,324,''),('HUN2000L','Found. of Nutritional Sci. Lab',1,325,''),('IDS3211','Global Climate Change',3,326,''),('IDS3211L','Global Climate Change Lab',1,327,''),('IDS3212','The Global Scientific Revolution an',1,328,''),('IDS3214','Coastal Environment from the Bay to',3,330,''),('MET2010','Meteor. Atmos. Physics',3,331,''),('MET2010L','Meteor. Atmos. Physics Lab',1,332,''),('MCB2000','Introductory Microbiology',3,333,''),('MCB2000L','Introductory Micro Lab',1,334,''),('OCB2003','Introductory Marine Biology',3,335,''),('OCB2003L','Introductory Marine Biology Lab',1,336,''),('OCE3014','Oceanography',3,337,''),('OCE3014L','Oceanography Lab',1,338,''),('PCB2099','Foundations of Human Physiology',3,339,''),('PCB2099L','Foundations of Human Physiology Lab',1,340,''),('PHY2054L','Physics without Calculus II Lab',1,344,''),('ARH2050','Art History Survey I',3,345,''),('ARH2051','Art History Survey II',3,346,''),('COM3417','Communication in Film',3,347,''),('CRW2001','ntroduction to Creative Writing',3,349,''),('DAA1100','Modern Dance Techniques I',3,350,''),('DAA1200','Ballet Techniques I',3,351,''),('ENL3504','British Literature to 1660',3,352,''),('ENL3506','British Literature Since 1660',3,353,''),('IDS3336','Artistic Expression in a Global Soc',3,354,''),('MUH2116','Evolution of Jazz',3,355,''),('SPC2608','Public Speaking',3,356,''),('TPP2100','Introduction to Acting',3,357,''),('CIS4911','Senior Project',3,370,''),('COP4610','Operating Syst Princ',3,377,''),('CAP4453','Robot Vision',3,384,''),('CAP4770','Data Mining',3,386,''),('CEN4021','Software Engineering 2',3,387,''),('CEN4083','Cloud Computing',3,389,''),('COP4226','Advanced Windows Programming',3,390,''),('COP4520','Introduction to Parallel Computing',3,391,''),('COP4534','Algorithm Techniques',3,392,''),('COP4604','Advanced Unix Prog',3,393,''),('COP4722','Survey of Database Systems',3,394,''),('MAD3401','Numerical Analysis',3,396,''),('MHF4302','Mathematical Logic',3,398,''),('EGN1110C','Engineering Drawing',3,567,''),('EGN1100','Intro To Engineering',2,570,''),('EIN3235','Eval Engr Data I',3,571,''),('EEL2880','Engr Software Tech',3,574,''),('EEL3110','Circuit Analysis',3,575,''),('EEL3110L','Circuits Lab',1,576,''),('EEL3120','Intro to Linear Systems',3,577,''),('EEL3135','Signals And Systems',3,578,''),('EEL3712','Logic Design I',3,579,''),('EEL3712L','Logic Design I Lab',1,580,''),('EEL4920','Senior Design I',2,581,''),('EEL4921C','Senior Design II',2,582,''),('EEE3303','Electronics I',3,583,''),('EEE3303L','Electronics I Lab',1,584,''),('EEL4213','Power Systems I',3,585,''),('EEL4213L','Energy Convrg Lab',1,586,''),('EEL4410','Fields and Waves',3,587,''),('EEL4214','Power Systems II',3,590,''),('EEL4215','Power Systems III',3,591,''),('EEL4241','Power Electronics',3,592,''),('EEL3657','Control Systems I',3,593,''),('EEL4611','Control Systems II',3,594,''),('EEL4611L','Systems Lab',1,595,''),('EEE3396','Solid State Devices',3,600,''),('EEE4304','Electronics II',3,601,''),('EEE4304L','Electronics II Lab',1,602,''),('EEE4314','Integrated Circ Syst',3,603,''),('EEE4314L','Integrated Ckt. Lab',1,604,''),('EEE4421C','Intro to Nanofab',3,605,''),('EEE4463','MEMS I',3,606,''),('EEE4464','MEMS II',3,607,''),('EEL3514','Comm Systems',3,608,''),('EEL3514L','Comm Sys Lab',1,609,''),('EEL4461C','Antennas',3,610,''),('EEL4510','Intro To Dsp',3,611,''),('EEL4515','Adv Comm Systems',3,612,''),('EEE4202C','Med Instrum Design',4,615,''),('EEL4140','Filter Design',3,617,''),('EEL3160','Comp Appl Elect Engr',3,619,''),('EEL4730','Programming Embedded Systms',3,620,''),('EEL4734','Embedded Operating Systems',3,621,''),('EEL4749','Embedded System',3,622,''),('EEL4831','Embedded GUI Programming',3,623,''),('EEE4343','Intro Digital Elect',3,624,''),('EEL4709C','Computer Design',3,625,''),('EEL4746','Microcomputers I',3,626,''),('EEL4746L','Microcomputers I Lab',1,627,''),('EEL4747','RISC',3,628,''),('EEL4747L','Microcomputers II Lab',1,629,''),('AST2004','Stellar Astronomy',3,761,''),('ENC3213','Prof and Tech Writing',3,763,''),('COM3110','Bus And Prof Commun',3,766,''),('ISC1000','Great Ideas in Science',3,795,''),('ISC1000L','Great Ideas in Science Lab',1,796,''),('CGS2100','Comp Appls Business',3,798,''),('ACG2021','Acc For Decisions',3,801,''),('REL3308','Studies In World Rel',3,802,''),('COP3402','Fund Computer System',3,843,''),('CHM1046','Gen Chemistry II',3,1501,''),('STA3033','Prob & Stat For Cs',3,1596,''),('SYG2000','Intro Sociology',3,1701,''),('COP2210','Programming I',4,1703,''),('COT3420','Logic For Comp Sci',3,1704,''),('ITA1130','Italian I',5,1705,''),('PCB3063','Genetics',3,1706,''),('PCB3043','Ecology',3,1707,''),('PCB3063L','Genetics Lab',1,1708,''),('PCB4674','Evolution',3,1709,''),('COP3337','Programming II',3,1710,''),('HSA3111','Intro to Hlth Serv Sys',3,2095,''),('HSA3412','CCultural Competency in HSC',3,2096,''),('ACG3301','Acc Pln & Cont',3,2097,''),('HSC3661','Comm Theory H Pro',3,2098,''),('COP4710','Database Management',3,2099,''),('CGS2060','Intro To Micro Comp',4,2100,''),('CHM1046L','Gen Chem Lab II',2,2101,''),('CLP2001','Personal Adjustment',3,2102,''),('PHI2011','Philos Analysis',3,2103,''),('TRF1000','Transfer Credit-Lower',4,2104,''),('TRF1001','Transfer Credit Lower Division',4,2105,'');
/*!40000 ALTER TABLE `CourseInfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GraduatePrograms`
--

DROP TABLE IF EXISTS `GraduatePrograms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GraduatePrograms` (
  `graduateProgram` varchar(50) NOT NULL,
  `requiredGPA` double(3,2) NOT NULL,
  `graduateProgramID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`graduateProgramID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GraduatePrograms`
--

LOCK TABLES `GraduatePrograms` WRITE;
/*!40000 ALTER TABLE `GraduatePrograms` DISABLE KEYS */;
INSERT INTO `GraduatePrograms` VALUES ('Masters in IT',3.20,1),('Masters in Mathematics',2.80,2),('Masters in Computer Science',3.00,3);
/*!40000 ALTER TABLE `GraduatePrograms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LoginAttempts`
--

DROP TABLE IF EXISTS `LoginAttempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LoginAttempts` (
  `userName` varchar(35) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LoginAttempts`
--

LOCK TABLES `LoginAttempts` WRITE;
/*!40000 ALTER TABLE `LoginAttempts` DISABLE KEYS */;
INSERT INTO `LoginAttempts` VALUES ('pdoe','0000-00-00 00:00:00'),('pdoe','0000-00-00 00:00:00'),('pdoe','0000-00-00 00:00:00'),('pdoe','0000-00-00 00:00:00'),('jdoe','0000-00-00 00:00:00'),('pdoe','0000-00-00 00:00:00'),('pdoe','0000-00-00 00:00:00'),('mdoe','0000-00-00 00:00:00'),('mdoe','0000-00-00 00:00:00'),('mdoe','0000-00-00 00:00:00'),('mdoe','0000-00-00 00:00:00'),('admin','0000-00-00 00:00:00'),('admin','0000-00-00 00:00:00'),('mdoe','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `LoginAttempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Major`
--

DROP TABLE IF EXISTS `Major`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Major` (
  `majorName` varchar(35) NOT NULL,
  `majorID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`majorID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Major`
--

LOCK TABLES `Major` WRITE;
/*!40000 ALTER TABLE `Major` DISABLE KEYS */;
INSERT INTO `Major` VALUES ('Computer Science',1),('Math',2),('Electrical Engineering',3),('Nursing BSN',4);
/*!40000 ALTER TABLE `Major` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MajorBucket`
--

DROP TABLE IF EXISTS `MajorBucket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MajorBucket` (
  `bucketID` int(11) NOT NULL AUTO_INCREMENT,
  `majorID` int(11) NOT NULL,
  `dateStart` date NOT NULL,
  `dateEnd` date NOT NULL,
  `description` varchar(35) NOT NULL,
  `allRequired` tinyint(1) NOT NULL,
  `quantityNeeded` int(11) NOT NULL,
  `quantification` varchar(35) NOT NULL,
  `parentID` int(11) DEFAULT NULL,
  PRIMARY KEY (`bucketID`),
  UNIQUE KEY `majorID_2` (`majorID`,`description`),
  KEY `majorID` (`majorID`),
  KEY `parentID` (`parentID`),
  CONSTRAINT `MajorBucket_ibfk_1` FOREIGN KEY (`majorID`) REFERENCES `Major` (`majorID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `MajorBucket_ibfk_2` FOREIGN KEY (`parentID`) REFERENCES `MajorBucket` (`bucketID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=346 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MajorBucket`
--

LOCK TABLES `MajorBucket` WRITE;
/*!40000 ALTER TABLE `MajorBucket` DISABLE KEYS */;
INSERT INTO `MajorBucket` VALUES (279,1,'0000-00-00','9999-12-31','First Year Experience',1,1,'credit',329),(280,1,'0000-00-00','9999-12-31','Communication',0,6,'credits',329),(281,1,'0000-00-00','9999-12-31','Humanities',0,6,'credits',329),(282,1,'0000-00-00','9999-12-31','Humanities - Group 1',0,3,'credits',281),(283,1,'0000-00-00','9999-12-31','Humanities - Group 2',0,0,'credits',281),(284,1,'0000-00-00','9999-12-31','Mathematics',0,6,'credits',329),(285,1,'0000-00-00','9999-12-31','Mathematics - Group 1',0,3,'credits',284),(286,1,'0000-00-00','9999-12-31','Mathematics - Group 2',0,3,'credits',284),(287,1,'0000-00-00','9999-12-31','Social Sciences',0,3,'credits',329),(288,1,'0000-00-00','9999-12-31','Social Sciences - Group 1',0,3,'credits',287),(289,1,'0000-00-00','9999-12-31','Social Sciences - Group 2',0,3,'credits',287),(290,1,'0000-00-00','9999-12-31','Natural Sciences',0,6,'credits',329),(291,1,'0000-00-00','9999-12-31','Natural Sciences - Group 1',0,3,'credits',290),(292,1,'0000-00-00','9999-12-31','Natural Sciences - Group 2',0,3,'credits',290),(293,1,'0000-00-00','9999-12-31','Arts',0,3,'credits',329),(294,1,'0000-00-00','9999-12-31','CS Prerequisites',1,7,'courses',NULL),(295,1,'0000-00-00','9999-12-31','CS Core Courses',1,18,'courses',NULL),(296,1,'0000-00-00','9999-12-31','CS Elective Courses',1,3,'courses',NULL),(297,1,'0000-00-00','9999-12-31','CS Science Elective Courses',1,3,'courses',NULL),(298,3,'0000-00-00','9999-12-31','First Year Experience',1,1,'credit',NULL),(299,3,'0000-00-00','9999-12-31','Communication',0,6,'credits',NULL),(300,3,'0000-00-00','9999-12-31','Humanities',0,6,'credits',NULL),(301,3,'0000-00-00','9999-12-31','Humanities - Group 1',0,3,'credits',300),(302,3,'0000-00-00','9999-12-31','Humanities - Group 2',0,0,'credits',300),(303,3,'0000-00-00','9999-12-31','Mathematics',0,6,'credits',NULL),(304,3,'0000-00-00','9999-12-31','Mathematics - Group 1',0,3,'credits',303),(305,3,'0000-00-00','9999-12-31','Mathematics - Group 2',0,3,'credits',303),(306,3,'0000-00-00','9999-12-31','Social Sciences',0,3,'credits',NULL),(307,3,'0000-00-00','9999-12-31','Social Sciences - Group 1',0,3,'credits',306),(308,3,'0000-00-00','9999-12-31','Social Sciences - Group 2',0,3,'credits',306),(309,3,'0000-00-00','9999-12-31','Natural Sciences',0,6,'credits',NULL),(310,3,'0000-00-00','9999-12-31','Natural Sciences - Group 1',0,3,'credits',309),(311,3,'0000-00-00','9999-12-31','Natural Sciences - Group 2',0,3,'credits',309),(312,3,'0000-00-00','9999-12-31','Arts',0,3,'credits',NULL),(313,3,'0000-00-00','9999-12-31','Mathematics and Sciences',1,10,'courses',NULL),(314,3,'0000-00-00','9999-12-31','CAD requirement',1,3,'credits',NULL),(315,3,'0000-00-00','9999-12-31','Engineering Orientation',1,2,'credits',NULL),(316,3,'0000-00-00','9999-12-31','Additional Required Engineering Bre',1,9,'credits',NULL),(317,3,'0000-00-00','9999-12-31','ECE Courses',1,7,'courses',NULL),(318,3,'0000-00-00','9999-12-31','Senior Design',1,4,'credits',NULL),(319,3,'0000-00-00','9999-12-31','Electrical Engineering (EE) Program',1,5,'courses',NULL),(320,3,'0000-00-00','9999-12-31','CONCENTRATION REQUIREMENTS',0,2,'buckets',NULL),(321,3,'0000-00-00','9999-12-31','Power / Energy',0,9,'credits',320),(322,3,'0000-00-00','9999-12-31','Integrated Nano-technology',0,9,'credits',320),(323,3,'0000-00-00','9999-12-31','Control Systems',1,9,'credits',320),(324,3,'0000-00-00','9999-12-31','Bio-Engineering',1,9,'credits',320),(325,3,'0000-00-00','9999-12-31','Communications',1,9,'credits',320),(326,3,'0000-00-00','9999-12-31','Embedded Systems',1,9,'credits',320),(327,3,'0000-00-00','9999-12-31','Computer Architecture and Microproc',1,9,'credits',320),(328,3,'0000-00-00','9999-12-31','Data System Software',1,9,'credits',320),(329,1,'0000-00-00','9999-12-31','UCC',1,7,'buckets',NULL);
/*!40000 ALTER TABLE `MajorBucket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MajorBucketRequiredCourses`
--

DROP TABLE IF EXISTS `MajorBucketRequiredCourses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MajorBucketRequiredCourses` (
  `bucketID` int(11) NOT NULL,
  `courseInfoID` int(11) NOT NULL,
  `minimumGrade` varchar(2) NOT NULL,
  PRIMARY KEY (`bucketID`,`courseInfoID`),
  KEY `courseInfoID` (`courseInfoID`),
  KEY `bucketID` (`bucketID`),
  CONSTRAINT `MajorBucketRequiredCourses_ibfk_1` FOREIGN KEY (`courseInfoID`) REFERENCES `CourseInfo` (`courseInfoID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `MajorBucketRequiredCourses_ibfk_2` FOREIGN KEY (`bucketID`) REFERENCES `MajorBucket` (`bucketID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MajorBucketRequiredCourses`
--

LOCK TABLES `MajorBucketRequiredCourses` WRITE;
/*!40000 ALTER TABLE `MajorBucketRequiredCourses` DISABLE KEYS */;
INSERT INTO `MajorBucketRequiredCourses` VALUES (279,25,'C'),(280,10,'C'),(280,37,'C'),(282,13,'D-'),(282,193,'D-'),(282,194,'D-'),(282,195,'D-'),(282,196,'D-'),(282,197,'D-'),(283,24,'D-'),(283,42,'D-'),(283,199,'D-'),(283,201,'D-'),(283,203,'D-'),(283,204,'D-'),(283,205,'D-'),(283,206,'D-'),(283,207,'D-'),(283,208,'D-'),(283,209,'D-'),(283,210,'D-'),(283,211,'D-'),(283,212,'D-'),(283,213,'D-'),(283,214,'D-'),(283,215,'D-'),(283,216,'D-'),(283,217,'D-'),(285,4,'C'),(285,218,'C'),(285,220,'C'),(285,221,'C'),(285,222,'C'),(286,4,'C'),(286,5,'C'),(286,23,'C'),(286,65,'C'),(286,223,'C'),(286,225,'C'),(286,226,'C'),(286,227,'C'),(286,228,'C'),(286,229,'C'),(286,232,'C'),(286,233,'C'),(286,235,'C'),(286,236,'C'),(288,58,'D-'),(288,237,'D-'),(288,238,'D-'),(288,240,'D-'),(288,241,'D-'),(289,33,'D-'),(289,52,'D-'),(289,243,'D-'),(289,244,'D-'),(289,245,'D-'),(289,246,'D-'),(289,247,'D-'),(289,249,'D-'),(289,250,'D-'),(289,251,'D-'),(289,252,'D-'),(289,253,'D-'),(289,254,'D-'),(289,255,'D-'),(289,256,'D-'),(289,257,'D-'),(289,259,'D-'),(289,260,'D-'),(289,261,'D-'),(289,262,'D-'),(289,263,'D-'),(289,264,'D-'),(289,265,'D-'),(289,266,'D-'),(289,267,'D-'),(289,268,'D-'),(289,269,'D-'),(289,270,'D-'),(289,271,'D-'),(289,272,'D-'),(289,273,'D-'),(289,274,'D-'),(289,275,'D-'),(289,276,'D-'),(289,277,'D-'),(289,278,'D-'),(289,279,'D-'),(289,280,'D-'),(289,281,'D-'),(291,21,'D-'),(291,22,'D-'),(291,34,'D-'),(291,55,'D-'),(291,57,'D-'),(291,62,'D-'),(291,68,'D-'),(291,282,'D-'),(291,283,'D-'),(291,286,'D-'),(291,287,'D-'),(291,290,'D-'),(291,291,'D-'),(291,292,'D-'),(291,293,'D-'),(291,294,'D-'),(291,295,'D-'),(291,299,'D-'),(291,300,'D-'),(291,301,'D-'),(292,15,'D-'),(292,35,'D-'),(292,44,'D-'),(292,302,'D-'),(292,303,'D-'),(292,304,'D-'),(292,305,'D-'),(292,306,'D-'),(292,307,'D-'),(292,308,'D-'),(292,309,'D-'),(292,310,'D-'),(292,311,'D-'),(292,312,'D-'),(292,313,'D-'),(292,314,'D-'),(292,315,'D-'),(292,316,'D-'),(292,317,'D-'),(292,318,'D-'),(292,319,'D-'),(292,320,'D-'),(292,321,'D-'),(292,322,'D-'),(292,323,'D-'),(292,324,'D-'),(292,325,'D-'),(292,326,'D-'),(292,327,'D-'),(292,328,'D-'),(292,330,'D-'),(292,331,'D-'),(292,332,'D-'),(292,333,'D-'),(292,334,'D-'),(292,335,'D-'),(292,336,'D-'),(292,337,'D-'),(292,338,'D-'),(292,339,'D-'),(292,340,'D-'),(292,344,'D-'),(293,53,'C'),(293,345,'D-'),(293,346,'D-'),(293,347,'D-'),(293,349,'D-'),(293,350,'D-'),(293,351,'D-'),(293,352,'D-'),(293,353,'D-'),(293,354,'D-'),(293,355,'D-'),(293,356,'D-'),(293,357,'D-'),(294,4,'C'),(294,5,'C'),(294,34,'C'),(294,35,'C'),(294,44,'C'),(294,57,'C'),(294,1703,'C'),(295,3,'C'),(295,14,'C'),(295,16,'C'),(295,17,'C'),(295,20,'C'),(295,26,'C'),(295,36,'C'),(295,39,'C'),(295,46,'C'),(295,54,'C'),(295,56,'C'),(295,59,'C'),(295,60,'C'),(295,370,'C'),(295,377,'C'),(295,763,'C'),(295,766,'C'),(295,843,'C'),(295,1710,'C'),(296,19,'C'),(296,32,'C'),(296,41,'C'),(296,50,'C'),(296,384,'C'),(296,386,'C'),(296,387,'C'),(296,389,'C'),(296,390,'C'),(296,391,'C'),(296,392,'C'),(296,393,'C'),(296,394,'C'),(296,396,'C'),(296,398,'C'),(297,4,'C'),(297,41,'C'),(297,50,'C'),(297,55,'C'),(297,60,'C'),(297,62,'C'),(297,304,'C'),(297,306,'C'),(297,320,'C'),(297,390,'C'),(297,391,'C'),(297,393,'C'),(297,394,'C'),(297,761,'C'),(297,1501,'C'),(298,25,'C'),(299,10,'C'),(299,37,'C'),(301,13,'D-'),(301,193,'D-'),(301,194,'D-'),(301,195,'D-'),(301,196,'D-'),(301,197,'D-'),(302,24,'D-'),(302,42,'D-'),(302,199,'D-'),(302,201,'D-'),(302,203,'D-'),(302,204,'D-'),(302,205,'D-'),(302,206,'D-'),(302,207,'D-'),(302,208,'D-'),(302,209,'D-'),(302,210,'D-'),(302,211,'D-'),(302,212,'D-'),(302,213,'D-'),(302,214,'D-'),(302,215,'D-'),(302,216,'D-'),(302,217,'D-'),(304,4,'C'),(304,218,'C'),(304,220,'C'),(304,221,'C'),(304,222,'C'),(305,5,'C'),(305,23,'C'),(305,65,'C'),(305,223,'C'),(305,225,'C'),(305,226,'C'),(305,227,'C'),(305,228,'C'),(305,229,'C'),(305,232,'C'),(305,233,'C'),(305,235,'C'),(305,236,'C'),(307,58,'D-'),(307,237,'D-'),(307,238,'D-'),(307,240,'D-'),(307,241,'D-'),(308,33,'D-'),(308,52,'D-'),(308,243,'D-'),(308,244,'D-'),(308,245,'D-'),(308,246,'D-'),(308,247,'D-'),(308,249,'D-'),(308,250,'D-'),(308,251,'D-'),(308,252,'D-'),(308,253,'D-'),(308,254,'D-'),(308,255,'D-'),(308,256,'D-'),(308,257,'D-'),(308,259,'D-'),(308,260,'D-'),(308,261,'D-'),(308,262,'D-'),(308,263,'D-'),(308,264,'D-'),(308,265,'D-'),(308,266,'D-'),(308,267,'D-'),(308,268,'D-'),(308,269,'D-'),(308,270,'D-'),(308,271,'D-'),(308,272,'D-'),(308,273,'D-'),(308,274,'D-'),(308,275,'D-'),(308,276,'D-'),(308,277,'D-'),(308,278,'D-'),(308,279,'D-'),(308,280,'D-'),(308,281,'D-'),(310,21,'D-'),(310,22,'D-'),(310,34,'D-'),(310,55,'D-'),(310,57,'D-'),(310,62,'D-'),(310,68,'D-'),(310,282,'D-'),(310,283,'D-'),(310,286,'D-'),(310,287,'D-'),(310,290,'D-'),(310,291,'D-'),(310,292,'D-'),(310,293,'D-'),(310,294,'D-'),(310,295,'D-'),(310,299,'D-'),(310,300,'D-'),(310,301,'D-'),(311,15,'D-'),(311,35,'D-'),(311,44,'D-'),(311,302,'D-'),(311,303,'D-'),(311,304,'D-'),(311,305,'D-'),(311,306,'D-'),(311,307,'D-'),(311,308,'D-'),(311,309,'D-'),(311,310,'D-'),(311,311,'D-'),(311,312,'D-'),(311,313,'D-'),(311,314,'D-'),(311,315,'D-'),(311,316,'D-'),(311,317,'D-'),(311,318,'D-'),(311,319,'D-'),(311,320,'D-'),(311,321,'D-'),(311,322,'D-'),(311,323,'D-'),(311,324,'D-'),(311,325,'D-'),(311,326,'D-'),(311,327,'D-'),(311,328,'D-'),(311,330,'D-'),(311,331,'D-'),(311,332,'D-'),(311,333,'D-'),(311,334,'D-'),(311,335,'D-'),(311,336,'D-'),(311,337,'D-'),(311,338,'D-'),(311,339,'D-'),(311,340,'D-'),(311,344,'D-'),(312,53,'C'),(312,345,'D-'),(312,346,'D-'),(312,347,'D-'),(312,349,'D-'),(312,350,'D-'),(312,351,'D-'),(312,352,'D-'),(312,353,'D-'),(312,354,'D-'),(312,355,'D-'),(312,356,'D-'),(312,357,'D-'),(313,4,'C'),(313,5,'C'),(313,6,'C'),(313,21,'C'),(313,23,'C'),(313,35,'C'),(313,44,'C'),(313,55,'C'),(313,57,'C'),(314,567,'D-'),(315,49,'D-'),(315,570,'D-'),(316,64,'C'),(317,574,'C-'),(317,575,'C-'),(317,576,'C-'),(317,577,'C-'),(317,578,'C-'),(317,579,'C-'),(317,580,'C-'),(318,581,'C-'),(318,582,'C-'),(321,585,'C-'),(321,586,'C-'),(321,590,'C-'),(321,591,'C-'),(321,592,'C-'),(322,583,'C-'),(322,584,'C-'),(322,600,'C-'),(322,601,'C-'),(322,602,'C-'),(322,603,'C-'),(322,604,'C-'),(322,605,'C-'),(322,606,'C-'),(322,607,'C-'),(323,29,'C-'),(323,31,'C-'),(323,593,'C-'),(323,594,'C-'),(323,595,'C-'),(324,583,'C-'),(324,584,'C-'),(324,605,'C-'),(324,611,'C-'),(324,615,'C-'),(324,617,'C-'),(325,608,'C-'),(325,609,'C-'),(325,610,'C-'),(325,611,'C-'),(325,612,'C-'),(326,619,'C-'),(326,620,'C-'),(326,621,'C-'),(326,622,'C-'),(326,623,'C-'),(328,3,'C-'),(328,26,'C-'),(328,54,'C-'),(328,377,'C-'),(328,393,'C-');
/*!40000 ALTER TABLE `MajorBucketRequiredCourses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PDFToBucket`
--

DROP TABLE IF EXISTS `PDFToBucket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PDFToBucket` (
  `pdfName` varchar(100) NOT NULL,
  `bucketName` varchar(35) NOT NULL,
  PRIMARY KEY (`pdfName`,`bucketName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PDFToBucket`
--

LOCK TABLES `PDFToBucket` WRITE;
/*!40000 ALTER TABLE `PDFToBucket` DISABLE KEYS */;
INSERT INTO `PDFToBucket` VALUES ('Additional Required Engineering Breadth','Additional Required Engineering Bre'),('Arts','Arts'),('Bio-Engineering','Bio-Engineering'),('CAD requirement','CAD requirement'),('Communications','Communications'),('Computer Architecture & Microprocessor Design','Computer Architecture and Microproc'),('Control Systems','Control Systems'),('Core Courses','CS Core Courses'),('Data System Software','Data System Software'),('ECE Courses','ECE Courses'),('Electives','CS Science Elective Courses'),('Electrical Engineering (EE) Program Core','Electrical Engineering (EE) Program'),('Embedded Systems','Embedded Systems'),('Engineering Orientation','Engineering Orientation'),('English Composition','Communication'),('First Year Experience','First Year Experience'),('Humanities - Group One','Humanities - Group 1'),('Humanities - Group Two','Humanities - Group 2'),('Integrated Nano-technology','Integrated Nano-technology'),('Mathematics and Sciences','Mathematics and Sciences'),('Mathematics Group One','Mathematics - Group 1'),('Mathematics Group Two','Mathematics - Group 2'),('NATURAL SCIENCE - GROUP TWO','Natural Sciences - Group 2'),('Natural Science Additional','CS Science Elective Courses'),('NATURAL SCIENCES - GROUP ONE','Natural Sciences - Group 1'),('Power / Energy','Power / Energy'),('Prerequisites','CS Prerequisites'),('Senior Design','Senior Design'),('Social Science Group One','Social Sciences - Group 1'),('Social Science Group Two','Social Sciences - Group 2');
/*!40000 ALTER TABLE `PDFToBucket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `StudentCourse`
--

DROP TABLE IF EXISTS `StudentCourse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `StudentCourse` (
  `grade` varchar(2) NOT NULL,
  `weight` int(11) NOT NULL,
  `relevance` int(11) NOT NULL,
  `studentCourseID` int(11) NOT NULL AUTO_INCREMENT,
  `semester` varchar(10) NOT NULL,
  `year` int(11) NOT NULL,
  `courseInfoID` int(11) NOT NULL,
  `selected` tinyint(1) NOT NULL,
  `userID` int(11) NOT NULL,
  `referenceBucket` int(11) DEFAULT NULL,
  PRIMARY KEY (`studentCourseID`),
  UNIQUE KEY `grade` (`grade`,`weight`,`relevance`,`semester`,`year`,`courseInfoID`,`selected`,`userID`,`referenceBucket`),
  UNIQUE KEY `grade_2` (`grade`,`weight`,`relevance`,`semester`,`year`,`courseInfoID`,`selected`,`userID`,`referenceBucket`),
  KEY `userID` (`userID`),
  KEY `courseInfoID` (`courseInfoID`),
  CONSTRAINT `StudentCourse_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `StudentCourse_ibfk_2` FOREIGN KEY (`courseInfoID`) REFERENCES `CourseInfo` (`courseInfoID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3874 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `StudentCourse`
--

LOCK TABLES `StudentCourse` WRITE;
/*!40000 ALTER TABLE `StudentCourse` DISABLE KEYS */;
INSERT INTO `StudentCourse` VALUES ('A',0,0,1296,'Fall',2010,23,0,1,NULL),('A',0,0,2226,'Fall',2010,23,0,2,NULL),('A',0,0,2288,'Fall',2010,23,0,9,NULL),('A',0,0,2537,'Fall',2010,23,0,13,NULL),('A',0,0,2599,'Fall',2010,23,0,14,NULL),('A',0,0,2661,'Fall',2010,23,0,15,NULL),('A',0,0,2789,'Fall',2010,23,0,17,NULL),('A',0,0,2851,'Fall',2010,23,0,19,NULL),('A',0,0,2979,'Fall',2010,23,0,23,NULL),('A',0,0,2361,'Fall',2010,25,0,8,NULL),('A',0,0,2706,'Fall',2010,25,0,16,NULL),('A',0,0,2896,'Fall',2010,25,0,22,NULL),('A',0,0,3024,'Fall',2010,25,0,24,NULL),('A',0,0,3090,'Fall',2010,25,0,25,NULL),('A',0,0,1292,'Fall',2010,53,0,1,NULL),('A',0,0,2222,'Fall',2010,53,0,2,NULL),('A',0,0,2284,'Fall',2010,53,0,9,NULL),('A',0,0,2533,'Fall',2010,53,0,13,NULL),('A',0,0,2595,'Fall',2010,53,0,14,NULL),('A',0,0,2657,'Fall',2010,53,0,15,NULL),('A',0,0,2785,'Fall',2010,53,0,17,NULL),('A',0,0,2847,'Fall',2010,53,0,19,NULL),('A',0,0,2975,'Fall',2010,53,0,23,NULL),('A',0,0,2375,'Fall',2010,796,0,8,NULL),('A',0,0,2720,'Fall',2010,796,0,16,NULL),('A',0,0,2910,'Fall',2010,796,0,22,NULL),('A',0,0,3038,'Fall',2010,796,0,24,NULL),('A',0,0,3104,'Fall',2010,796,0,25,NULL),('A',0,0,1302,'Fall',2011,11,0,1,NULL),('A',0,0,2232,'Fall',2011,11,0,2,NULL),('A',0,0,2294,'Fall',2011,11,0,9,NULL),('A',0,0,2543,'Fall',2011,11,0,13,NULL),('A',0,0,2605,'Fall',2011,11,0,14,NULL),('A',0,0,2667,'Fall',2011,11,0,15,NULL),('A',0,0,2795,'Fall',2011,11,0,17,NULL),('A',0,0,2857,'Fall',2011,11,0,19,NULL),('A',0,0,2985,'Fall',2011,11,0,23,NULL),('A',0,0,2346,'Fall',2012,46,0,10,NULL),('A',0,0,2383,'Fall',2012,766,0,8,NULL),('A',0,0,2728,'Fall',2012,766,0,16,NULL),('A',0,0,2918,'Fall',2012,766,0,22,NULL),('A',0,0,3046,'Fall',2012,766,0,24,NULL),('A',0,0,3112,'Fall',2012,766,0,25,NULL),('A',0,0,1321,'Fall',2013,26,0,1,NULL),('A',0,0,2251,'Fall',2013,26,0,2,NULL),('A',0,0,2313,'Fall',2013,26,0,9,NULL),('A',0,0,2562,'Fall',2013,26,0,13,NULL),('A',0,0,2624,'Fall',2013,26,0,14,NULL),('A',0,0,2686,'Fall',2013,26,0,15,NULL),('A',0,0,2814,'Fall',2013,26,0,17,NULL),('A',0,0,2876,'Fall',2013,26,0,19,NULL),('A',0,0,3004,'Fall',2013,26,0,23,NULL),('A',0,0,2385,'Fall',2013,35,0,8,NULL),('A',0,0,2730,'Fall',2013,35,0,16,NULL),('A',0,0,2920,'Fall',2013,35,0,22,NULL),('A',0,0,3048,'Fall',2013,35,0,24,NULL),('A',0,0,3114,'Fall',2013,35,0,25,NULL),('A',0,0,2434,'Fall',2013,306,0,11,NULL),('A',0,0,2359,'Fall',2014,393,0,10,NULL),('A',0,0,3836,'Fall',2015,20,0,12,NULL),('A',0,0,3864,'Fall',2015,20,0,28,NULL),('A',0,0,1338,'Fall',2015,370,0,1,NULL),('A',0,0,2268,'Fall',2015,370,0,2,NULL),('A',0,0,2409,'Fall',2015,370,0,8,NULL),('A',0,0,2337,'Spring',2011,34,0,10,NULL),('A',0,0,2339,'Spring',2011,35,0,10,NULL),('A',0,0,1297,'Spring',2011,49,0,1,NULL),('A',0,0,2227,'Spring',2011,49,0,2,NULL),('A',0,0,2289,'Spring',2011,49,0,9,NULL),('A',0,0,2538,'Spring',2011,49,0,13,NULL),('A',0,0,2600,'Spring',2011,49,0,14,NULL),('A',0,0,2662,'Spring',2011,49,0,15,NULL),('A',0,0,2790,'Spring',2011,49,0,17,NULL),('A',0,0,2852,'Spring',2011,49,0,19,NULL),('A',0,0,2980,'Spring',2011,49,0,23,NULL),('A',0,0,2336,'Spring',2011,57,0,10,NULL),('A',0,0,1303,'Spring',2012,29,0,1,NULL),('A',0,0,2233,'Spring',2012,29,0,2,NULL),('A',0,0,2295,'Spring',2012,29,0,9,NULL),('A',0,0,2544,'Spring',2012,29,0,13,NULL),('A',0,0,2606,'Spring',2012,29,0,14,NULL),('A',0,0,2668,'Spring',2012,29,0,15,NULL),('A',0,0,2796,'Spring',2012,29,0,17,NULL),('A',0,0,2858,'Spring',2012,29,0,19,NULL),('A',0,0,2986,'Spring',2012,29,0,23,NULL),('A',0,0,1304,'Spring',2012,48,0,1,NULL),('A',0,0,2234,'Spring',2012,48,0,2,NULL),('A',0,0,2296,'Spring',2012,48,0,9,NULL),('A',0,0,2545,'Spring',2012,48,0,13,NULL),('A',0,0,2607,'Spring',2012,48,0,14,NULL),('A',0,0,2669,'Spring',2012,48,0,15,NULL),('A',0,0,2797,'Spring',2012,48,0,17,NULL),('A',0,0,2859,'Spring',2012,48,0,19,NULL),('A',0,0,2987,'Spring',2012,48,0,23,NULL),('A',0,0,2373,'Spring',2012,305,0,8,NULL),('A',0,0,2718,'Spring',2012,305,0,16,NULL),('A',0,0,2908,'Spring',2012,305,0,22,NULL),('A',0,0,3036,'Spring',2012,305,0,24,NULL),('A',0,0,3102,'Spring',2012,305,0,25,NULL),('A',0,0,2395,'Spring',2013,39,0,8,NULL),('A',0,0,2740,'Spring',2013,39,0,16,NULL),('A',0,0,2930,'Spring',2013,39,0,22,NULL),('A',0,0,3058,'Spring',2013,39,0,24,NULL),('A',0,0,3124,'Spring',2013,39,0,25,NULL),('A',0,0,1309,'Spring',2013,42,0,1,NULL),('A',0,0,2239,'Spring',2013,42,0,2,NULL),('A',0,0,2301,'Spring',2013,42,0,9,NULL),('A',0,0,2550,'Spring',2013,42,0,13,NULL),('A',0,0,2612,'Spring',2013,42,0,14,NULL),('A',0,0,2674,'Spring',2013,42,0,15,NULL),('A',0,0,2802,'Spring',2013,42,0,17,NULL),('A',0,0,2864,'Spring',2013,42,0,19,NULL),('A',0,0,2992,'Spring',2013,42,0,23,NULL),('A',0,0,2438,'Spring',2013,46,0,11,NULL),('A',0,0,1331,'Spring',2015,14,0,1,NULL),('A',0,0,2261,'Spring',2015,14,0,2,NULL),('A',0,0,2323,'Spring',2015,14,0,9,NULL),('A',0,0,2572,'Spring',2015,14,0,13,NULL),('A',0,0,2634,'Spring',2015,14,0,14,NULL),('A',0,0,2696,'Spring',2015,14,0,15,NULL),('A',0,0,2824,'Spring',2015,14,0,17,NULL),('A',0,0,2886,'Spring',2015,14,0,19,NULL),('A',0,0,3014,'Spring',2015,14,0,23,NULL),('A',0,0,1329,'Spring',2015,39,0,1,NULL),('A',0,0,2259,'Spring',2015,39,0,2,NULL),('A',0,0,2321,'Spring',2015,39,0,9,NULL),('A',0,0,2570,'Spring',2015,39,0,13,NULL),('A',0,0,2632,'Spring',2015,39,0,14,NULL),('A',0,0,2694,'Spring',2015,39,0,15,NULL),('A',0,0,2822,'Spring',2015,39,0,17,NULL),('A',0,0,2884,'Spring',2015,39,0,19,NULL),('A',0,0,3012,'Spring',2015,39,0,23,NULL),('A',0,0,3831,'Spring',2015,54,0,12,NULL),('A',0,0,3859,'Spring',2015,54,0,28,NULL),('A',0,0,2450,'Spring',2015,389,0,11,NULL),('A',0,0,2437,'Summer',2004,26,0,11,NULL),('A',0,0,1282,'Summer',2011,13,0,1,NULL),('A',0,0,2212,'Summer',2011,13,0,2,NULL),('A',0,0,2274,'Summer',2011,13,0,9,NULL),('A',0,0,2523,'Summer',2011,13,0,13,NULL),('A',0,0,2585,'Summer',2011,13,0,14,NULL),('A',0,0,2647,'Summer',2011,13,0,15,NULL),('A',0,0,2775,'Summer',2011,13,0,17,NULL),('A',0,0,2837,'Summer',2011,13,0,19,NULL),('A',0,0,2965,'Summer',2011,13,0,23,NULL),('A',0,0,1281,'Summer',2011,37,0,1,NULL),('A',0,0,2342,'Summer',2011,763,0,10,NULL),('A',0,0,2371,'Summer',2012,34,0,8,NULL),('A',0,0,2390,'Summer',2012,34,0,8,NULL),('A',0,0,2716,'Summer',2012,34,0,16,NULL),('A',0,0,2735,'Summer',2012,34,0,16,NULL),('A',0,0,2906,'Summer',2012,34,0,22,NULL),('A',0,0,2925,'Summer',2012,34,0,22,NULL),('A',0,0,3034,'Summer',2012,34,0,24,NULL),('A',0,0,3053,'Summer',2012,34,0,24,NULL),('A',0,0,3100,'Summer',2012,34,0,25,NULL),('A',0,0,3119,'Summer',2012,34,0,25,NULL),('A',0,0,1335,'Summer',2015,20,0,1,NULL),('A',0,0,2265,'Summer',2015,20,0,2,NULL),('A',0,0,2327,'Summer',2015,20,0,9,NULL),('A',0,0,2576,'Summer',2015,20,0,13,NULL),('A',0,0,2638,'Summer',2015,20,0,14,NULL),('A',0,0,2700,'Summer',2015,20,0,15,NULL),('A',0,0,2828,'Summer',2015,20,0,17,NULL),('A',0,0,2890,'Summer',2015,20,0,19,NULL),('A',0,0,3018,'Summer',2015,20,0,23,NULL),('A',0,0,2433,'Summer',2015,35,0,11,NULL),('A',0,0,2357,'Summer',2015,370,0,10,NULL),('A-',0,0,2364,'Fall',2011,13,0,8,NULL),('A-',0,0,2709,'Fall',2011,13,0,16,NULL),('A-',0,0,2899,'Fall',2011,13,0,22,NULL),('A-',0,0,3027,'Fall',2011,13,0,24,NULL),('A-',0,0,3093,'Fall',2011,13,0,25,NULL),('A-',0,0,1301,'Fall',2011,66,0,1,NULL),('A-',0,0,2231,'Fall',2011,66,0,2,NULL),('A-',0,0,2293,'Fall',2011,66,0,9,NULL),('A-',0,0,2542,'Fall',2011,66,0,13,NULL),('A-',0,0,2604,'Fall',2011,66,0,14,NULL),('A-',0,0,2666,'Fall',2011,66,0,15,NULL),('A-',0,0,2794,'Fall',2011,66,0,17,NULL),('A-',0,0,2856,'Fall',2011,66,0,19,NULL),('A-',0,0,2984,'Fall',2011,66,0,23,NULL),('A-',0,0,2365,'Fall',2011,206,0,8,NULL),('A-',0,0,2710,'Fall',2011,206,0,16,NULL),('A-',0,0,2900,'Fall',2011,206,0,22,NULL),('A-',0,0,3028,'Fall',2011,206,0,24,NULL),('A-',0,0,3094,'Fall',2011,206,0,25,NULL),('A-',0,0,2439,'Fall',2013,39,0,11,NULL),('A-',0,0,3814,'Fall',2013,1703,0,12,NULL),('A-',0,0,3842,'Fall',2013,1703,0,28,NULL),('A-',0,0,1326,'Fall',2014,19,0,1,NULL),('A-',0,0,2256,'Fall',2014,19,0,2,NULL),('A-',0,0,2318,'Fall',2014,19,0,9,NULL),('A-',0,0,2567,'Fall',2014,19,0,13,NULL),('A-',0,0,2629,'Fall',2014,19,0,14,NULL),('A-',0,0,2691,'Fall',2014,19,0,15,NULL),('A-',0,0,2819,'Fall',2014,19,0,17,NULL),('A-',0,0,2881,'Fall',2014,19,0,19,NULL),('A-',0,0,3009,'Fall',2014,19,0,23,NULL),('A-',0,0,2449,'Fall',2015,377,0,11,NULL),('A-',0,0,1306,'Spring',2012,47,0,1,NULL),('A-',0,0,2236,'Spring',2012,47,0,2,NULL),('A-',0,0,2298,'Spring',2012,47,0,9,NULL),('A-',0,0,2547,'Spring',2012,47,0,13,NULL),('A-',0,0,2609,'Spring',2012,47,0,14,NULL),('A-',0,0,2671,'Spring',2012,47,0,15,NULL),('A-',0,0,2799,'Spring',2012,47,0,17,NULL),('A-',0,0,2861,'Spring',2012,47,0,19,NULL),('A-',0,0,2989,'Spring',2012,47,0,23,NULL),('A-',0,0,1305,'Spring',2012,64,0,1,NULL),('A-',0,0,2235,'Spring',2012,64,0,2,NULL),('A-',0,0,2297,'Spring',2012,64,0,9,NULL),('A-',0,0,2546,'Spring',2012,64,0,13,NULL),('A-',0,0,2608,'Spring',2012,64,0,14,NULL),('A-',0,0,2670,'Spring',2012,64,0,15,NULL),('A-',0,0,2798,'Spring',2012,64,0,17,NULL),('A-',0,0,2860,'Spring',2012,64,0,19,NULL),('A-',0,0,2988,'Spring',2012,64,0,23,NULL),('A-',0,0,2431,'Spring',2013,34,0,11,NULL),('A-',0,0,1310,'Spring',2013,52,0,1,NULL),('A-',0,0,2240,'Spring',2013,52,0,2,NULL),('A-',0,0,2302,'Spring',2013,52,0,9,NULL),('A-',0,0,2551,'Spring',2013,52,0,13,NULL),('A-',0,0,2613,'Spring',2013,52,0,14,NULL),('A-',0,0,2675,'Spring',2013,52,0,15,NULL),('A-',0,0,2803,'Spring',2013,52,0,17,NULL),('A-',0,0,2865,'Spring',2013,52,0,19,NULL),('A-',0,0,2993,'Spring',2013,52,0,23,NULL),('A-',0,0,2441,'Spring',2014,59,0,11,NULL),('A-',0,0,1332,'Spring',2015,36,0,1,NULL),('A-',0,0,2262,'Spring',2015,36,0,2,NULL),('A-',0,0,2324,'Spring',2015,36,0,9,NULL),('A-',0,0,2573,'Spring',2015,36,0,13,NULL),('A-',0,0,2635,'Spring',2015,36,0,14,NULL),('A-',0,0,2697,'Spring',2015,36,0,15,NULL),('A-',0,0,2825,'Spring',2015,36,0,17,NULL),('A-',0,0,2887,'Spring',2015,36,0,19,NULL),('A-',0,0,3015,'Spring',2015,36,0,23,NULL),('A-',0,0,2211,'Summer',2011,37,0,2,NULL),('A-',0,0,2273,'Summer',2011,37,0,9,NULL),('A-',0,0,2522,'Summer',2011,37,0,13,NULL),('A-',0,0,2584,'Summer',2011,37,0,14,NULL),('A-',0,0,2646,'Summer',2011,37,0,15,NULL),('A-',0,0,2774,'Summer',2011,37,0,17,NULL),('A-',0,0,2836,'Summer',2011,37,0,19,NULL),('A-',0,0,2964,'Summer',2011,37,0,23,NULL),('A-',0,0,2350,'Summer',2013,3,0,10,NULL),('A-',0,0,2442,'Summer',2014,3,0,11,NULL),('A-',0,0,2443,'Summer',2014,54,0,11,NULL),('B',0,0,2362,'Fall',2010,10,0,8,NULL),('B',0,0,2707,'Fall',2010,10,0,16,NULL),('B',0,0,2897,'Fall',2010,10,0,22,NULL),('B',0,0,3025,'Fall',2010,10,0,24,NULL),('B',0,0,3091,'Fall',2010,10,0,25,NULL),('B',0,0,2376,'Fall',2010,222,0,8,NULL),('B',0,0,2721,'Fall',2010,222,0,16,NULL),('B',0,0,2911,'Fall',2010,222,0,22,NULL),('B',0,0,3039,'Fall',2010,222,0,24,NULL),('B',0,0,3105,'Fall',2010,222,0,25,NULL),('B',0,0,2374,'Fall',2010,795,0,8,NULL),('B',0,0,2719,'Fall',2010,795,0,16,NULL),('B',0,0,2909,'Fall',2010,795,0,22,NULL),('B',0,0,3037,'Fall',2010,795,0,24,NULL),('B',0,0,3103,'Fall',2010,795,0,25,NULL),('B',0,0,1300,'Fall',2011,18,0,1,NULL),('B',0,0,2230,'Fall',2011,18,0,2,NULL),('B',0,0,2292,'Fall',2011,18,0,9,NULL),('B',0,0,2541,'Fall',2011,18,0,13,NULL),('B',0,0,2603,'Fall',2011,18,0,14,NULL),('B',0,0,2665,'Fall',2011,18,0,15,NULL),('B',0,0,2793,'Fall',2011,18,0,17,NULL),('B',0,0,2855,'Fall',2011,18,0,19,NULL),('B',0,0,2983,'Fall',2011,18,0,23,NULL),('B',0,0,2381,'Fall',2011,802,0,8,NULL),('B',0,0,2726,'Fall',2011,802,0,16,NULL),('B',0,0,2916,'Fall',2011,802,0,22,NULL),('B',0,0,3044,'Fall',2011,802,0,24,NULL),('B',0,0,3110,'Fall',2011,802,0,25,NULL),('B',0,0,1325,'Fall',2014,3,0,1,NULL),('B',0,0,2255,'Fall',2014,3,0,2,NULL),('B',0,0,2317,'Fall',2014,3,0,9,NULL),('B',0,0,2566,'Fall',2014,3,0,13,NULL),('B',0,0,2628,'Fall',2014,3,0,14,NULL),('B',0,0,2690,'Fall',2014,3,0,15,NULL),('B',0,0,2818,'Fall',2014,3,0,17,NULL),('B',0,0,2880,'Fall',2014,3,0,19,NULL),('B',0,0,3008,'Fall',2014,3,0,23,NULL),('B',0,0,3826,'Fall',2014,59,0,12,NULL),('B',0,0,3854,'Fall',2014,59,0,28,NULL),('B',0,0,2334,'Spring',2011,4,0,10,NULL),('B',0,0,2335,'Spring',2011,5,0,10,NULL),('B',0,0,1298,'Spring',2011,6,0,1,NULL),('B',0,0,2228,'Spring',2011,6,0,2,NULL),('B',0,0,2290,'Spring',2011,6,0,9,NULL),('B',0,0,2539,'Spring',2011,6,0,13,NULL),('B',0,0,2601,'Spring',2011,6,0,14,NULL),('B',0,0,2663,'Spring',2011,6,0,15,NULL),('B',0,0,2791,'Spring',2011,6,0,17,NULL),('B',0,0,2853,'Spring',2011,6,0,19,NULL),('B',0,0,2981,'Spring',2011,6,0,23,NULL),('B',0,0,2363,'Spring',2011,37,0,8,NULL),('B',0,0,2708,'Spring',2011,37,0,16,NULL),('B',0,0,2898,'Spring',2011,37,0,22,NULL),('B',0,0,3026,'Spring',2011,37,0,24,NULL),('B',0,0,3092,'Spring',2011,37,0,25,NULL),('B',0,0,2345,'Spring',2012,766,0,10,NULL),('B',0,0,2353,'Spring',2014,14,0,10,NULL),('B',0,0,1322,'Spring',2014,17,0,1,NULL),('B',0,0,2252,'Spring',2014,17,0,2,NULL),('B',0,0,2314,'Spring',2014,17,0,9,NULL),('B',0,0,2563,'Spring',2014,17,0,13,NULL),('B',0,0,2625,'Spring',2014,17,0,14,NULL),('B',0,0,2687,'Spring',2014,17,0,15,NULL),('B',0,0,2815,'Spring',2014,17,0,17,NULL),('B',0,0,2877,'Spring',2014,17,0,19,NULL),('B',0,0,3005,'Spring',2014,17,0,23,NULL),('B',0,0,1337,'Spring',2014,41,0,1,NULL),('B',0,0,2267,'Spring',2014,41,0,2,NULL),('B',0,0,2329,'Spring',2014,41,0,9,NULL),('B',0,0,2578,'Spring',2014,41,0,13,NULL),('B',0,0,2640,'Spring',2014,41,0,14,NULL),('B',0,0,2702,'Spring',2014,41,0,15,NULL),('B',0,0,2830,'Spring',2014,41,0,17,NULL),('B',0,0,2892,'Spring',2014,41,0,19,NULL),('B',0,0,3020,'Spring',2014,41,0,23,NULL),('B',0,0,1330,'Spring',2015,54,0,1,NULL),('B',0,0,2260,'Spring',2015,54,0,2,NULL),('B',0,0,2322,'Spring',2015,54,0,9,NULL),('B',0,0,2571,'Spring',2015,54,0,13,NULL),('B',0,0,2633,'Spring',2015,54,0,14,NULL),('B',0,0,2695,'Spring',2015,54,0,15,NULL),('B',0,0,2823,'Spring',2015,54,0,17,NULL),('B',0,0,2885,'Spring',2015,54,0,19,NULL),('B',0,0,3013,'Spring',2015,54,0,23,NULL),('B',0,0,3840,'Spring',2016,391,0,12,NULL),('B',0,0,2428,'Summer',2004,4,0,11,NULL),('B',0,0,2370,'Summer',2012,57,0,8,NULL),('B',0,0,2389,'Summer',2012,57,0,8,NULL),('B',0,0,2715,'Summer',2012,57,0,16,NULL),('B',0,0,2734,'Summer',2012,57,0,16,NULL),('B',0,0,2905,'Summer',2012,57,0,22,NULL),('B',0,0,2924,'Summer',2012,57,0,22,NULL),('B',0,0,3033,'Summer',2012,57,0,24,NULL),('B',0,0,3052,'Summer',2012,57,0,24,NULL),('B',0,0,3099,'Summer',2012,57,0,25,NULL),('B',0,0,3118,'Summer',2012,57,0,25,NULL),('B',0,0,2341,'Summer',2013,62,0,10,NULL),('B',0,0,2358,'Summer',2014,60,0,10,NULL),('B',0,0,3835,'Summer',2015,36,0,12,NULL),('B',0,0,3863,'Summer',2015,36,0,28,NULL),('B',0,0,1334,'Summer',2015,60,0,1,NULL),('B',0,0,2264,'Summer',2015,60,0,2,NULL),('B',0,0,2326,'Summer',2015,60,0,9,NULL),('B',0,0,2575,'Summer',2015,60,0,13,NULL),('B',0,0,2637,'Summer',2015,60,0,14,NULL),('B',0,0,2699,'Summer',2015,60,0,15,NULL),('B',0,0,2827,'Summer',2015,60,0,17,NULL),('B',0,0,2889,'Summer',2015,60,0,19,NULL),('B',0,0,3017,'Summer',2015,60,0,23,NULL),('B+',0,0,2210,'Fall',2010,10,0,2,NULL),('B+',0,0,2272,'Fall',2010,10,0,9,NULL),('B+',0,0,2521,'Fall',2010,10,0,13,NULL),('B+',0,0,2583,'Fall',2010,10,0,14,NULL),('B+',0,0,2645,'Fall',2010,10,0,15,NULL),('B+',0,0,2773,'Fall',2010,10,0,17,NULL),('B+',0,0,2835,'Fall',2010,10,0,19,NULL),('B+',0,0,2963,'Fall',2010,10,0,23,NULL),('B+',0,0,2384,'Fall',2012,763,0,8,NULL),('B+',0,0,2729,'Fall',2012,763,0,16,NULL),('B+',0,0,2919,'Fall',2012,763,0,22,NULL),('B+',0,0,3047,'Fall',2012,763,0,24,NULL),('B+',0,0,3113,'Fall',2012,763,0,25,NULL),('B+',0,0,2435,'Fall',2013,320,0,11,NULL),('B+',0,0,1327,'Fall',2014,16,0,1,NULL),('B+',0,0,2257,'Fall',2014,16,0,2,NULL),('B+',0,0,2319,'Fall',2014,16,0,9,NULL),('B+',0,0,2568,'Fall',2014,16,0,13,NULL),('B+',0,0,2630,'Fall',2014,16,0,14,NULL),('B+',0,0,2692,'Fall',2014,16,0,15,NULL),('B+',0,0,2820,'Fall',2014,16,0,17,NULL),('B+',0,0,2882,'Fall',2014,16,0,19,NULL),('B+',0,0,3010,'Fall',2014,16,0,23,NULL),('B+',0,0,3828,'Fall',2014,19,0,12,NULL),('B+',0,0,1328,'Fall',2014,56,0,1,NULL),('B+',0,0,2258,'Fall',2014,56,0,2,NULL),('B+',0,0,2320,'Fall',2014,56,0,9,NULL),('B+',0,0,2569,'Fall',2014,56,0,13,NULL),('B+',0,0,2631,'Fall',2014,56,0,14,NULL),('B+',0,0,2693,'Fall',2014,56,0,15,NULL),('B+',0,0,2821,'Fall',2014,56,0,17,NULL),('B+',0,0,2883,'Fall',2014,56,0,19,NULL),('B+',0,0,3011,'Fall',2014,56,0,23,NULL),('B+',0,0,3856,'Fall',2014,2099,0,28,NULL),('B+',0,0,3838,'Fall',2015,377,0,12,NULL),('B+',0,0,3866,'Fall',2015,377,0,28,NULL),('B+',0,0,1287,'Spring',2011,33,0,1,NULL),('B+',0,0,2217,'Spring',2011,33,0,2,NULL),('B+',0,0,2279,'Spring',2011,33,0,9,NULL),('B+',0,0,2528,'Spring',2011,33,0,13,NULL),('B+',0,0,2590,'Spring',2011,33,0,14,NULL),('B+',0,0,2652,'Spring',2011,33,0,15,NULL),('B+',0,0,2780,'Spring',2011,33,0,17,NULL),('B+',0,0,2842,'Spring',2011,33,0,19,NULL),('B+',0,0,2970,'Spring',2011,33,0,23,NULL),('B+',0,0,2340,'Spring',2011,761,0,10,NULL),('B+',0,0,1283,'Spring',2012,24,0,1,NULL),('B+',0,0,2213,'Spring',2012,24,0,2,NULL),('B+',0,0,2275,'Spring',2012,24,0,9,NULL),('B+',0,0,2524,'Spring',2012,24,0,13,NULL),('B+',0,0,2586,'Spring',2012,24,0,14,NULL),('B+',0,0,2648,'Spring',2012,24,0,15,NULL),('B+',0,0,2776,'Spring',2012,24,0,17,NULL),('B+',0,0,2838,'Spring',2012,24,0,19,NULL),('B+',0,0,2966,'Spring',2012,24,0,23,NULL),('B+',0,0,2382,'Spring',2012,217,0,8,NULL),('B+',0,0,2727,'Spring',2012,217,0,16,NULL),('B+',0,0,2917,'Spring',2012,217,0,22,NULL),('B+',0,0,3045,'Spring',2012,217,0,24,NULL),('B+',0,0,3111,'Spring',2012,217,0,25,NULL),('B+',0,0,2372,'Spring',2012,304,0,8,NULL),('B+',0,0,2393,'Spring',2012,304,0,8,NULL),('B+',0,0,2717,'Spring',2012,304,0,16,NULL),('B+',0,0,2738,'Spring',2012,304,0,16,NULL),('B+',0,0,2907,'Spring',2012,304,0,22,NULL),('B+',0,0,2928,'Spring',2012,304,0,22,NULL),('B+',0,0,3035,'Spring',2012,304,0,24,NULL),('B+',0,0,3056,'Spring',2012,304,0,24,NULL),('B+',0,0,3101,'Spring',2012,304,0,25,NULL),('B+',0,0,3122,'Spring',2012,304,0,25,NULL),('B+',0,0,2394,'Spring',2013,320,0,8,NULL),('B+',0,0,2739,'Spring',2013,320,0,16,NULL),('B+',0,0,2929,'Spring',2013,320,0,22,NULL),('B+',0,0,3057,'Spring',2013,320,0,24,NULL),('B+',0,0,3123,'Spring',2013,320,0,25,NULL),('B+',0,0,3819,'Spring',2014,17,0,12,NULL),('B+',0,0,3847,'Spring',2014,17,0,28,NULL),('B+',0,0,3830,'Spring',2015,3,0,12,NULL),('B+',0,0,3858,'Spring',2015,3,0,28,NULL),('B+',0,0,3833,'Spring',2015,16,0,12,NULL),('B+',0,0,3861,'Spring',2015,16,0,28,NULL),('B+',0,0,2447,'Spring',2015,60,0,11,NULL),('B+',0,0,3832,'Spring',2015,394,0,12,NULL),('B+',0,0,3860,'Spring',2015,394,0,28,NULL),('B+',0,0,2349,'Summer',2013,19,0,10,NULL),('B+',0,0,2432,'Summer',2015,44,0,11,NULL),('B-',0,0,1280,'Fall',2010,10,0,1,NULL),('B-',0,0,1295,'Fall',2010,21,0,1,NULL),('B-',0,0,2225,'Fall',2010,21,0,2,NULL),('B-',0,0,2287,'Fall',2010,21,0,9,NULL),('B-',0,0,2536,'Fall',2010,21,0,13,NULL),('B-',0,0,2598,'Fall',2010,21,0,14,NULL),('B-',0,0,2660,'Fall',2010,21,0,15,NULL),('B-',0,0,2788,'Fall',2010,21,0,17,NULL),('B-',0,0,2850,'Fall',2010,21,0,19,NULL),('B-',0,0,2978,'Fall',2010,21,0,23,NULL),('B-',0,0,1319,'Fall',2010,55,0,1,NULL),('B-',0,0,2249,'Fall',2010,55,0,2,NULL),('B-',0,0,2311,'Fall',2010,55,0,9,NULL),('B-',0,0,2560,'Fall',2010,55,0,13,NULL),('B-',0,0,2622,'Fall',2010,55,0,14,NULL),('B-',0,0,2684,'Fall',2010,55,0,15,NULL),('B-',0,0,2812,'Fall',2010,55,0,17,NULL),('B-',0,0,2874,'Fall',2010,55,0,19,NULL),('B-',0,0,3002,'Fall',2010,55,0,23,NULL),('B-',0,0,2369,'Fall',2010,254,0,8,NULL),('B-',0,0,2714,'Fall',2010,254,0,16,NULL),('B-',0,0,2904,'Fall',2010,254,0,22,NULL),('B-',0,0,3032,'Fall',2010,254,0,24,NULL),('B-',0,0,3098,'Fall',2010,254,0,25,NULL),('B-',0,0,1317,'Fall',2011,44,0,1,NULL),('B-',0,0,2247,'Fall',2011,44,0,2,NULL),('B-',0,0,2309,'Fall',2011,44,0,9,NULL),('B-',0,0,2558,'Fall',2011,44,0,13,NULL),('B-',0,0,2620,'Fall',2011,44,0,14,NULL),('B-',0,0,2682,'Fall',2011,44,0,15,NULL),('B-',0,0,2810,'Fall',2011,44,0,17,NULL),('B-',0,0,2872,'Fall',2011,44,0,19,NULL),('B-',0,0,3000,'Fall',2011,44,0,23,NULL),('B-',0,0,1307,'Fall',2012,40,0,1,NULL),('B-',0,0,2237,'Fall',2012,40,0,2,NULL),('B-',0,0,2299,'Fall',2012,40,0,9,NULL),('B-',0,0,2548,'Fall',2012,40,0,13,NULL),('B-',0,0,2610,'Fall',2012,40,0,14,NULL),('B-',0,0,2672,'Fall',2012,40,0,15,NULL),('B-',0,0,2800,'Fall',2012,40,0,17,NULL),('B-',0,0,2862,'Fall',2012,40,0,19,NULL),('B-',0,0,2990,'Fall',2012,40,0,23,NULL),('B-',0,0,2360,'Fall',2013,41,0,10,NULL),('B-',0,0,1336,'Fall',2013,50,0,1,NULL),('B-',0,0,2266,'Fall',2013,50,0,2,NULL),('B-',0,0,2328,'Fall',2013,50,0,9,NULL),('B-',0,0,2577,'Fall',2013,50,0,13,NULL),('B-',0,0,2639,'Fall',2013,50,0,14,NULL),('B-',0,0,2701,'Fall',2013,50,0,15,NULL),('B-',0,0,2829,'Fall',2013,50,0,17,NULL),('B-',0,0,2891,'Fall',2013,50,0,19,NULL),('B-',0,0,3019,'Fall',2013,50,0,23,NULL),('B-',0,0,3815,'Fall',2013,56,0,12,NULL),('B-',0,0,3843,'Fall',2013,56,0,28,NULL),('B-',0,0,2445,'Fall',2014,16,0,11,NULL),('B-',0,0,2402,'Fall',2014,19,0,8,NULL),('B-',0,0,2444,'Fall',2014,19,0,11,NULL),('B-',0,0,2747,'Fall',2014,19,0,16,NULL),('B-',0,0,2937,'Fall',2014,19,0,22,NULL),('B-',0,0,3065,'Fall',2014,19,0,24,NULL),('B-',0,0,3131,'Fall',2014,19,0,25,NULL),('B-',0,0,2427,'Spring',2004,766,0,11,NULL),('B-',0,0,1316,'Spring',2011,57,0,1,NULL),('B-',0,0,2246,'Spring',2011,57,0,2,NULL),('B-',0,0,2308,'Spring',2011,57,0,9,NULL),('B-',0,0,2557,'Spring',2011,57,0,13,NULL),('B-',0,0,2619,'Spring',2011,57,0,14,NULL),('B-',0,0,2681,'Spring',2011,57,0,15,NULL),('B-',0,0,2809,'Spring',2011,57,0,17,NULL),('B-',0,0,2871,'Spring',2011,57,0,19,NULL),('B-',0,0,2999,'Spring',2011,57,0,23,NULL),('B-',0,0,2378,'Spring',2011,312,0,8,NULL),('B-',0,0,2723,'Spring',2011,312,0,16,NULL),('B-',0,0,2913,'Spring',2011,312,0,22,NULL),('B-',0,0,3041,'Spring',2011,312,0,24,NULL),('B-',0,0,3107,'Spring',2011,312,0,25,NULL),('B-',0,0,2348,'Spring',2013,59,0,10,NULL),('B-',0,0,2355,'Spring',2014,16,0,10,NULL),('C',0,0,2343,'Fall',2011,17,0,10,NULL),('C',0,0,1299,'Fall',2011,31,0,1,NULL),('C',0,0,2229,'Fall',2011,31,0,2,NULL),('C',0,0,2291,'Fall',2011,31,0,9,NULL),('C',0,0,2540,'Fall',2011,31,0,13,NULL),('C',0,0,2602,'Fall',2011,31,0,14,NULL),('C',0,0,2664,'Fall',2011,31,0,15,NULL),('C',0,0,2792,'Fall',2011,31,0,17,NULL),('C',0,0,2854,'Fall',2011,31,0,19,NULL),('C',0,0,2982,'Fall',2011,31,0,23,NULL),('C',0,0,2380,'Fall',2011,801,0,8,NULL),('C',0,0,2725,'Fall',2011,801,0,16,NULL),('C',0,0,2915,'Fall',2011,801,0,22,NULL),('C',0,0,3043,'Fall',2011,801,0,24,NULL),('C',0,0,3109,'Fall',2011,801,0,25,NULL),('C',0,0,2392,'Fall',2013,44,0,8,NULL),('C',0,0,2737,'Fall',2013,44,0,16,NULL),('C',0,0,2927,'Fall',2013,44,0,22,NULL),('C',0,0,3055,'Fall',2013,44,0,24,NULL),('C',0,0,3121,'Fall',2013,44,0,25,NULL),('C',0,0,3817,'Fall',2013,1596,0,12,NULL),('C',0,0,3845,'Fall',2013,1596,0,28,NULL),('C',0,0,3818,'Fall',2013,1701,0,12,NULL),('C',0,0,3846,'Fall',2013,1701,0,28,NULL),('C',0,0,2401,'Fall',2014,3,0,8,NULL),('C',0,0,2746,'Fall',2014,3,0,16,NULL),('C',0,0,2936,'Fall',2014,3,0,22,NULL),('C',0,0,3064,'Fall',2014,3,0,24,NULL),('C',0,0,3130,'Fall',2014,3,0,25,NULL),('C',0,0,2338,'Spring',2011,44,0,10,NULL),('C',0,0,1286,'Spring',2011,58,0,1,NULL),('C',0,0,2216,'Spring',2011,58,0,2,NULL),('C',0,0,2368,'Spring',2011,58,0,8,NULL),('C',0,0,2278,'Spring',2011,58,0,9,NULL),('C',0,0,2527,'Spring',2011,58,0,13,NULL),('C',0,0,2589,'Spring',2011,58,0,14,NULL),('C',0,0,2651,'Spring',2011,58,0,15,NULL),('C',0,0,2713,'Spring',2011,58,0,16,NULL),('C',0,0,2779,'Spring',2011,58,0,17,NULL),('C',0,0,2841,'Spring',2011,58,0,19,NULL),('C',0,0,2903,'Spring',2011,58,0,22,NULL),('C',0,0,2969,'Spring',2011,58,0,23,NULL),('C',0,0,3031,'Spring',2011,58,0,24,NULL),('C',0,0,3097,'Spring',2011,58,0,25,NULL),('C',0,0,2379,'Spring',2011,313,0,8,NULL),('C',0,0,2724,'Spring',2011,313,0,16,NULL),('C',0,0,2914,'Spring',2011,313,0,22,NULL),('C',0,0,3042,'Spring',2011,313,0,24,NULL),('C',0,0,3108,'Spring',2011,313,0,25,NULL),('C',0,0,2391,'Spring',2013,5,0,8,NULL),('C',0,0,2736,'Spring',2013,5,0,16,NULL),('C',0,0,2926,'Spring',2013,5,0,22,NULL),('C',0,0,3054,'Spring',2013,5,0,24,NULL),('C',0,0,3120,'Spring',2013,5,0,25,NULL),('C',0,0,1311,'Spring',2013,28,0,1,NULL),('C',0,0,2241,'Spring',2013,28,0,2,NULL),('C',0,0,2303,'Spring',2013,28,0,9,NULL),('C',0,0,2552,'Spring',2013,28,0,13,NULL),('C',0,0,2614,'Spring',2013,28,0,14,NULL),('C',0,0,2676,'Spring',2013,28,0,15,NULL),('C',0,0,2804,'Spring',2013,28,0,17,NULL),('C',0,0,2866,'Spring',2013,28,0,19,NULL),('C',0,0,2994,'Spring',2013,28,0,23,NULL),('C',0,0,2398,'Spring',2014,17,0,8,NULL),('C',0,0,2743,'Spring',2014,17,0,16,NULL),('C',0,0,2933,'Spring',2014,17,0,22,NULL),('C',0,0,3061,'Spring',2014,17,0,24,NULL),('C',0,0,3127,'Spring',2014,17,0,25,NULL),('C',0,0,2399,'Spring',2014,26,0,8,NULL),('C',0,0,3822,'Spring',2014,26,0,12,NULL),('C',0,0,2744,'Spring',2014,26,0,16,NULL),('C',0,0,2934,'Spring',2014,26,0,22,NULL),('C',0,0,3062,'Spring',2014,26,0,24,NULL),('C',0,0,3128,'Spring',2014,26,0,25,NULL),('C',0,0,3850,'Spring',2014,26,0,28,NULL),('C',0,0,3820,'Spring',2014,39,0,12,NULL),('C',0,0,3848,'Spring',2014,39,0,28,NULL),('C',0,0,2448,'Spring',2015,14,0,11,NULL),('C',0,0,2404,'Spring',2015,54,0,8,NULL),('C',0,0,2749,'Spring',2015,54,0,16,NULL),('C',0,0,2939,'Spring',2015,54,0,22,NULL),('C',0,0,3067,'Spring',2015,54,0,24,NULL),('C',0,0,3133,'Spring',2015,54,0,25,NULL),('C',0,0,2429,'Summer',2004,5,0,11,NULL),('C',0,0,2430,'Summer',2004,57,0,11,NULL),('C',0,0,2356,'Summer',2014,36,0,10,NULL),('C',0,0,2400,'Summer',2014,59,0,8,NULL),('C',0,0,2745,'Summer',2014,59,0,16,NULL),('C',0,0,2935,'Summer',2014,59,0,22,NULL),('C',0,0,3063,'Summer',2014,59,0,24,NULL),('C',0,0,3129,'Summer',2014,59,0,25,NULL),('C',0,0,1288,'Summer',2015,62,0,1,NULL),('C',0,0,2218,'Summer',2015,62,0,2,NULL),('C',0,0,2250,'Summer',2015,62,0,2,NULL),('C',0,0,2280,'Summer',2015,62,0,9,NULL),('C',0,0,2312,'Summer',2015,62,0,9,NULL),('C',0,0,2529,'Summer',2015,62,0,13,NULL),('C',0,0,2561,'Summer',2015,62,0,13,NULL),('C',0,0,2591,'Summer',2015,62,0,14,NULL),('C',0,0,2623,'Summer',2015,62,0,14,NULL),('C',0,0,2653,'Summer',2015,62,0,15,NULL),('C',0,0,2685,'Summer',2015,62,0,15,NULL),('C',0,0,2781,'Summer',2015,62,0,17,NULL),('C',0,0,2813,'Summer',2015,62,0,17,NULL),('C',0,0,2843,'Summer',2015,62,0,19,NULL),('C',0,0,2875,'Summer',2015,62,0,19,NULL),('C',0,0,2971,'Summer',2015,62,0,23,NULL),('C',0,0,3003,'Summer',2015,62,0,23,NULL),('C',0,0,1289,'Summer',2015,68,0,1,NULL),('C',0,0,2219,'Summer',2015,68,0,2,NULL),('C',0,0,2281,'Summer',2015,68,0,9,NULL),('C',0,0,2530,'Summer',2015,68,0,13,NULL),('C',0,0,2592,'Summer',2015,68,0,14,NULL),('C',0,0,2654,'Summer',2015,68,0,15,NULL),('C',0,0,2782,'Summer',2015,68,0,17,NULL),('C',0,0,2844,'Summer',2015,68,0,19,NULL),('C',0,0,2972,'Summer',2015,68,0,23,NULL),('C+',0,0,1279,'Fall',2010,25,0,1,NULL),('C+',0,0,2209,'Fall',2010,25,0,2,NULL),('C+',0,0,2271,'Fall',2010,25,0,9,NULL),('C+',0,0,2520,'Fall',2010,25,0,13,NULL),('C+',0,0,2582,'Fall',2010,25,0,14,NULL),('C+',0,0,2644,'Fall',2010,25,0,15,NULL),('C+',0,0,2772,'Fall',2010,25,0,17,NULL),('C+',0,0,2834,'Fall',2010,25,0,19,NULL),('C+',0,0,2962,'Fall',2010,25,0,23,NULL),('C+',0,0,2347,'Fall',2012,26,0,10,NULL),('C+',0,0,2238,'Fall',2012,40,0,2,NULL),('C+',0,0,2300,'Fall',2012,40,0,9,NULL),('C+',0,0,2549,'Fall',2012,40,0,13,NULL),('C+',0,0,2611,'Fall',2012,40,0,14,NULL),('C+',0,0,2673,'Fall',2012,40,0,15,NULL),('C+',0,0,2801,'Fall',2012,40,0,17,NULL),('C+',0,0,2863,'Fall',2012,40,0,19,NULL),('C+',0,0,2991,'Fall',2012,40,0,23,NULL),('C+',0,0,2351,'Fall',2013,54,0,10,NULL),('C+',0,0,1324,'Fall',2014,59,0,1,NULL),('C+',0,0,2254,'Fall',2014,59,0,2,NULL),('C+',0,0,2316,'Fall',2014,59,0,9,NULL),('C+',0,0,2565,'Fall',2014,59,0,13,NULL),('C+',0,0,2627,'Fall',2014,59,0,14,NULL),('C+',0,0,2689,'Fall',2014,59,0,15,NULL),('C+',0,0,2817,'Fall',2014,59,0,17,NULL),('C+',0,0,2879,'Fall',2014,59,0,19,NULL),('C+',0,0,3007,'Fall',2014,59,0,23,NULL),('C+',0,0,3837,'Fall',2015,60,0,12,NULL),('C+',0,0,3865,'Fall',2015,60,0,28,NULL),('C+',0,0,2436,'Spring',2004,843,0,11,NULL),('C+',0,0,2352,'Spring',2014,20,0,10,NULL),('C+',0,0,2354,'Spring',2014,377,0,10,NULL),('C+',0,0,3821,'Spring',2014,1710,0,12,NULL),('C+',0,0,3849,'Spring',2014,1710,0,28,NULL),('C+',0,0,2405,'Spring',2015,16,0,8,NULL),('C+',0,0,2750,'Spring',2015,16,0,16,NULL),('C+',0,0,2940,'Spring',2015,16,0,22,NULL),('C+',0,0,3068,'Spring',2015,16,0,24,NULL),('C+',0,0,3134,'Spring',2015,16,0,25,NULL),('C+',0,0,2403,'Spring',2015,20,0,8,NULL),('C+',0,0,2446,'Spring',2015,20,0,11,NULL),('C+',0,0,2748,'Spring',2015,20,0,16,NULL),('C+',0,0,2938,'Spring',2015,20,0,22,NULL),('C+',0,0,3066,'Spring',2015,20,0,24,NULL),('C+',0,0,3132,'Spring',2015,20,0,25,NULL),('C+',0,0,3841,'Spring',2016,41,0,12,NULL),('C+',0,0,3834,'Summer',2015,14,0,12,NULL),('C+',0,0,3862,'Summer',2015,14,0,28,NULL),('D',0,0,3827,'Fall',2014,3,0,12,NULL),('D',0,0,3855,'Fall',2014,3,0,28,NULL),('D',0,0,2377,'Spring',2011,798,0,8,NULL),('D',0,0,2722,'Spring',2011,798,0,16,NULL),('D',0,0,2912,'Spring',2011,798,0,22,NULL),('D',0,0,3040,'Spring',2011,798,0,24,NULL),('D',0,0,3106,'Spring',2011,798,0,25,NULL),('D-',0,0,3816,'Fall',2013,26,0,12,NULL),('D-',0,0,3844,'Fall',2013,26,0,28,NULL),('DR',0,0,3829,'Fall',2014,16,0,12,NULL),('DR',0,0,3857,'Fall',2014,16,0,28,NULL),('DR',0,0,3823,'Summer',2014,59,0,12,NULL),('DR',0,0,3851,'Summer',2014,59,0,28,NULL),('F',0,0,3825,'Summer',2014,3,0,12,NULL),('F',0,0,3853,'Summer',2014,3,0,28,NULL),('F',0,0,3824,'Summer',2014,19,0,12,NULL),('F',0,0,3852,'Summer',2014,2099,0,28,NULL),('IP',0,0,2410,'Fall',2015,14,0,8,NULL),('IP',0,0,2755,'Fall',2015,14,0,16,NULL),('IP',0,0,2945,'Fall',2015,14,0,22,NULL),('IP',0,0,3073,'Fall',2015,14,0,24,NULL),('IP',0,0,3139,'Fall',2015,14,0,25,NULL),('IP',0,0,2412,'Fall',2015,36,0,8,NULL),('IP',0,0,2757,'Fall',2015,36,0,16,NULL),('IP',0,0,2947,'Fall',2015,36,0,22,NULL),('IP',0,0,3075,'Fall',2015,36,0,24,NULL),('IP',0,0,3141,'Fall',2015,36,0,25,NULL),('IP',0,0,2330,'Fall',2015,370,0,9,NULL),('IP',0,0,2579,'Fall',2015,370,0,13,NULL),('IP',0,0,2641,'Fall',2015,370,0,14,NULL),('IP',0,0,2703,'Fall',2015,370,0,15,NULL),('IP',0,0,2754,'Fall',2015,370,0,16,NULL),('IP',0,0,2831,'Fall',2015,370,0,17,NULL),('IP',0,0,2893,'Fall',2015,370,0,19,NULL),('IP',0,0,2944,'Fall',2015,370,0,22,NULL),('IP',0,0,3021,'Fall',2015,370,0,23,NULL),('IP',0,0,3072,'Fall',2015,370,0,24,NULL),('IP',0,0,3138,'Fall',2015,370,0,25,NULL),('IP',0,0,1339,'Fall',2015,377,0,1,NULL),('IP',0,0,2269,'Fall',2015,377,0,2,NULL),('IP',0,0,2411,'Fall',2015,377,0,8,NULL),('IP',0,0,2331,'Fall',2015,377,0,9,NULL),('IP',0,0,2451,'Fall',2015,377,0,11,NULL),('IP',0,0,2580,'Fall',2015,377,0,13,NULL),('IP',0,0,2642,'Fall',2015,377,0,14,NULL),('IP',0,0,2704,'Fall',2015,377,0,15,NULL),('IP',0,0,2756,'Fall',2015,377,0,16,NULL),('IP',0,0,2832,'Fall',2015,377,0,17,NULL),('IP',0,0,2894,'Fall',2015,377,0,19,NULL),('IP',0,0,2946,'Fall',2015,377,0,22,NULL),('IP',0,0,3022,'Fall',2015,377,0,23,NULL),('IP',0,0,3074,'Fall',2015,377,0,24,NULL),('IP',0,0,3140,'Fall',2015,377,0,25,NULL),('IP',0,0,1340,'Fall',2015,390,0,1,NULL),('IP',0,0,2270,'Fall',2015,390,0,2,NULL),('IP',0,0,2332,'Fall',2015,390,0,9,NULL),('IP',0,0,2581,'Fall',2015,390,0,13,NULL),('IP',0,0,2643,'Fall',2015,390,0,14,NULL),('IP',0,0,2705,'Fall',2015,390,0,15,NULL),('IP',0,0,2833,'Fall',2015,390,0,17,NULL),('IP',0,0,2895,'Fall',2015,390,0,19,NULL),('IP',0,0,3023,'Fall',2015,390,0,23,NULL),('IP',0,0,2453,'Spring',2016,36,0,11,NULL),('IP',0,0,3869,'Spring',2016,41,0,28,NULL),('IP',0,0,2452,'Spring',2016,370,0,11,NULL),('IP',0,0,3839,'Spring',2016,370,0,12,NULL),('IP',0,0,3867,'Spring',2016,370,0,28,NULL),('IP',0,0,3868,'Spring',2016,391,0,28,NULL),('ND',0,0,3870,'',0,46,0,28,NULL),('ND',0,0,2426,'',0,56,0,8,NULL),('ND',0,0,2771,'',0,56,0,16,NULL),('ND',0,0,2961,'',0,56,0,22,NULL),('ND',0,0,3089,'',0,56,0,24,NULL),('ND',0,0,3155,'',0,56,0,25,NULL),('ND',0,0,2425,'',0,60,0,8,NULL),('ND',0,0,2770,'',0,60,0,16,NULL),('ND',0,0,2960,'',0,60,0,22,NULL),('ND',0,0,3088,'',0,60,0,24,NULL),('ND',0,0,3154,'',0,60,0,25,NULL),('ND',0,0,3076,'',0,345,0,24,NULL),('ND',0,0,3142,'',0,345,0,25,NULL),('ND',0,0,2413,'',0,345,1,8,NULL),('ND',0,0,2758,'',0,345,1,16,NULL),('ND',0,0,2948,'',0,345,1,22,NULL),('ND',0,0,2759,'',0,346,0,16,NULL),('ND',0,0,2949,'',0,346,0,22,NULL),('ND',0,0,3143,'',0,346,0,25,NULL),('ND',0,0,2414,'',0,346,1,8,NULL),('ND',0,0,3077,'',0,346,1,24,NULL),('ND',0,0,2760,'',0,347,0,16,NULL),('ND',0,0,2950,'',0,347,0,22,NULL),('ND',0,0,3078,'',0,347,0,24,NULL),('ND',0,0,3144,'',0,347,0,25,NULL),('ND',0,0,2415,'',0,347,1,8,NULL),('ND',0,0,2761,'',0,349,0,16,NULL),('ND',0,0,2951,'',0,349,0,22,NULL),('ND',0,0,3079,'',0,349,0,24,NULL),('ND',0,0,3145,'',0,349,0,25,NULL),('ND',0,0,2416,'',0,349,1,8,NULL),('ND',0,0,2417,'',0,350,0,8,NULL),('ND',0,0,2762,'',0,350,0,16,NULL),('ND',0,0,2952,'',0,350,0,22,NULL),('ND',0,0,3080,'',0,350,0,24,NULL),('ND',0,0,3146,'',0,350,0,25,NULL),('ND',0,0,2418,'',0,351,0,8,NULL),('ND',0,0,2763,'',0,351,0,16,NULL),('ND',0,0,2953,'',0,351,0,22,NULL),('ND',0,0,3081,'',0,351,0,24,NULL),('ND',0,0,3147,'',0,351,0,25,NULL),('ND',0,0,2419,'',0,352,0,8,NULL),('ND',0,0,2764,'',0,352,0,16,NULL),('ND',0,0,2954,'',0,352,0,22,NULL),('ND',0,0,3082,'',0,352,0,24,NULL),('ND',0,0,3148,'',0,352,0,25,NULL),('ND',0,0,2420,'',0,353,0,8,NULL),('ND',0,0,2765,'',0,353,0,16,NULL),('ND',0,0,2955,'',0,353,0,22,NULL),('ND',0,0,3083,'',0,353,0,24,NULL),('ND',0,0,3149,'',0,353,0,25,NULL),('ND',0,0,2421,'',0,354,0,8,NULL),('ND',0,0,2766,'',0,354,0,16,NULL),('ND',0,0,2956,'',0,354,0,22,NULL),('ND',0,0,3084,'',0,354,0,24,NULL),('ND',0,0,3150,'',0,354,0,25,NULL),('ND',0,0,2422,'',0,355,0,8,NULL),('ND',0,0,2767,'',0,355,0,16,NULL),('ND',0,0,2957,'',0,355,0,22,NULL),('ND',0,0,3085,'',0,355,0,24,NULL),('ND',0,0,3151,'',0,355,0,25,NULL),('ND',0,0,2423,'',0,356,0,8,NULL),('ND',0,0,2768,'',0,356,0,16,NULL),('ND',0,0,2958,'',0,356,0,22,NULL),('ND',0,0,3086,'',0,356,0,24,NULL),('ND',0,0,3152,'',0,356,0,25,NULL),('ND',0,0,2424,'',0,357,0,8,NULL),('ND',0,0,2769,'',0,357,0,16,NULL),('ND',0,0,2959,'',0,357,0,22,NULL),('ND',0,0,3087,'',0,357,0,24,NULL),('ND',0,0,3153,'',0,357,0,25,NULL),('ND',0,0,3871,'',0,763,0,28,NULL),('ND',0,0,3872,'',0,766,0,28,NULL),('ND',0,0,3873,'',0,843,0,28,NULL),('P',0,0,2396,'Fall',2013,46,0,8,NULL),('P',0,0,2741,'Fall',2013,46,0,16,NULL),('P',0,0,2931,'Fall',2013,46,0,22,NULL),('P',0,0,3059,'Fall',2013,46,0,24,NULL),('P',0,0,3125,'Fall',2013,46,0,25,NULL),('P',0,0,1333,'Summer',2015,46,0,1,NULL),('P',0,0,2263,'Summer',2015,46,0,2,NULL),('P',0,0,2325,'Summer',2015,46,0,9,NULL),('P',0,0,2574,'Summer',2015,46,0,13,NULL),('P',0,0,2636,'Summer',2015,46,0,14,NULL),('P',0,0,2698,'Summer',2015,46,0,15,NULL),('P',0,0,2826,'Summer',2015,46,0,17,NULL),('P',0,0,2888,'Summer',2015,46,0,19,NULL),('P',0,0,3016,'Summer',2015,46,0,23,NULL),('TR',0,0,1284,'Fall',2010,4,0,1,NULL),('TR',0,0,2214,'Fall',2010,4,0,2,NULL),('TR',0,0,2242,'Fall',2010,4,0,2,NULL),('TR',0,0,2366,'Fall',2010,4,0,8,NULL),('TR',0,0,2367,'Fall',2010,4,0,8,NULL),('TR',0,0,2386,'Fall',2010,4,0,8,NULL),('TR',0,0,2387,'Fall',2010,4,0,8,NULL),('TR',0,0,2407,'Fall',2010,4,0,8,NULL),('TR',0,0,2408,'Fall',2010,4,0,8,NULL),('TR',0,0,2276,'Fall',2010,4,0,9,NULL),('TR',0,0,2304,'Fall',2010,4,0,9,NULL),('TR',0,0,2525,'Fall',2010,4,0,13,NULL),('TR',0,0,2553,'Fall',2010,4,0,13,NULL),('TR',0,0,2587,'Fall',2010,4,0,14,NULL),('TR',0,0,2615,'Fall',2010,4,0,14,NULL),('TR',0,0,2649,'Fall',2010,4,0,15,NULL),('TR',0,0,2677,'Fall',2010,4,0,15,NULL),('TR',0,0,2711,'Fall',2010,4,0,16,NULL),('TR',0,0,2712,'Fall',2010,4,0,16,NULL),('TR',0,0,2731,'Fall',2010,4,0,16,NULL),('TR',0,0,2732,'Fall',2010,4,0,16,NULL),('TR',0,0,2752,'Fall',2010,4,0,16,NULL),('TR',0,0,2753,'Fall',2010,4,0,16,NULL),('TR',0,0,2777,'Fall',2010,4,0,17,NULL),('TR',0,0,2805,'Fall',2010,4,0,17,NULL),('TR',0,0,2839,'Fall',2010,4,0,19,NULL),('TR',0,0,2867,'Fall',2010,4,0,19,NULL),('TR',0,0,2901,'Fall',2010,4,0,22,NULL),('TR',0,0,2902,'Fall',2010,4,0,22,NULL),('TR',0,0,2921,'Fall',2010,4,0,22,NULL),('TR',0,0,2922,'Fall',2010,4,0,22,NULL),('TR',0,0,2942,'Fall',2010,4,0,22,NULL),('TR',0,0,2943,'Fall',2010,4,0,22,NULL),('TR',0,0,2967,'Fall',2010,4,0,23,NULL),('TR',0,0,2995,'Fall',2010,4,0,23,NULL),('TR',0,0,3029,'Fall',2010,4,0,24,NULL),('TR',0,0,3030,'Fall',2010,4,0,24,NULL),('TR',0,0,3049,'Fall',2010,4,0,24,NULL),('TR',0,0,3050,'Fall',2010,4,0,24,NULL),('TR',0,0,3070,'Fall',2010,4,0,24,NULL),('TR',0,0,3071,'Fall',2010,4,0,24,NULL),('TR',0,0,3095,'Fall',2010,4,0,25,NULL),('TR',0,0,3096,'Fall',2010,4,0,25,NULL),('TR',0,0,3115,'Fall',2010,4,0,25,NULL),('TR',0,0,3116,'Fall',2010,4,0,25,NULL),('TR',0,0,3136,'Fall',2010,4,0,25,NULL),('TR',0,0,3137,'Fall',2010,4,0,25,NULL),('TR',0,0,1285,'Fall',2010,5,0,1,NULL),('TR',0,0,2215,'Fall',2010,5,0,2,NULL),('TR',0,0,2243,'Fall',2010,5,0,2,NULL),('TR',0,0,2277,'Fall',2010,5,0,9,NULL),('TR',0,0,2305,'Fall',2010,5,0,9,NULL),('TR',0,0,2526,'Fall',2010,5,0,13,NULL),('TR',0,0,2554,'Fall',2010,5,0,13,NULL),('TR',0,0,2588,'Fall',2010,5,0,14,NULL),('TR',0,0,2616,'Fall',2010,5,0,14,NULL),('TR',0,0,2650,'Fall',2010,5,0,15,NULL),('TR',0,0,2678,'Fall',2010,5,0,15,NULL),('TR',0,0,2778,'Fall',2010,5,0,17,NULL),('TR',0,0,2806,'Fall',2010,5,0,17,NULL),('TR',0,0,2840,'Fall',2010,5,0,19,NULL),('TR',0,0,2868,'Fall',2010,5,0,19,NULL),('TR',0,0,2968,'Fall',2010,5,0,23,NULL),('TR',0,0,2996,'Fall',2010,5,0,23,NULL),('TR',0,0,1291,'Fall',2010,15,0,1,NULL),('TR',0,0,2221,'Fall',2010,15,0,2,NULL),('TR',0,0,2283,'Fall',2010,15,0,9,NULL),('TR',0,0,2532,'Fall',2010,15,0,13,NULL),('TR',0,0,2594,'Fall',2010,15,0,14,NULL),('TR',0,0,2656,'Fall',2010,15,0,15,NULL),('TR',0,0,2784,'Fall',2010,15,0,17,NULL),('TR',0,0,2846,'Fall',2010,15,0,19,NULL),('TR',0,0,2974,'Fall',2010,15,0,23,NULL),('TR',0,0,1293,'Fall',2010,22,0,1,NULL),('TR',0,0,2223,'Fall',2010,22,0,2,NULL),('TR',0,0,2285,'Fall',2010,22,0,9,NULL),('TR',0,0,2534,'Fall',2010,22,0,13,NULL),('TR',0,0,2596,'Fall',2010,22,0,14,NULL),('TR',0,0,2658,'Fall',2010,22,0,15,NULL),('TR',0,0,2786,'Fall',2010,22,0,17,NULL),('TR',0,0,2848,'Fall',2010,22,0,19,NULL),('TR',0,0,2976,'Fall',2010,22,0,23,NULL),('TR',0,0,1314,'Fall',2010,34,0,1,NULL),('TR',0,0,2244,'Fall',2010,34,0,2,NULL),('TR',0,0,2306,'Fall',2010,34,0,9,NULL),('TR',0,0,2555,'Fall',2010,34,0,13,NULL),('TR',0,0,2617,'Fall',2010,34,0,14,NULL),('TR',0,0,2679,'Fall',2010,34,0,15,NULL),('TR',0,0,2807,'Fall',2010,34,0,17,NULL),('TR',0,0,2869,'Fall',2010,34,0,19,NULL),('TR',0,0,2997,'Fall',2010,34,0,23,NULL),('TR',0,0,1290,'Fall',2010,35,0,1,NULL),('TR',0,0,2220,'Fall',2010,35,0,2,NULL),('TR',0,0,2245,'Fall',2010,35,0,2,NULL),('TR',0,0,2282,'Fall',2010,35,0,9,NULL),('TR',0,0,2307,'Fall',2010,35,0,9,NULL),('TR',0,0,2531,'Fall',2010,35,0,13,NULL),('TR',0,0,2556,'Fall',2010,35,0,13,NULL),('TR',0,0,2593,'Fall',2010,35,0,14,NULL),('TR',0,0,2618,'Fall',2010,35,0,14,NULL),('TR',0,0,2655,'Fall',2010,35,0,15,NULL),('TR',0,0,2680,'Fall',2010,35,0,15,NULL),('TR',0,0,2783,'Fall',2010,35,0,17,NULL),('TR',0,0,2808,'Fall',2010,35,0,17,NULL),('TR',0,0,2845,'Fall',2010,35,0,19,NULL),('TR',0,0,2870,'Fall',2010,35,0,19,NULL),('TR',0,0,2973,'Fall',2010,35,0,23,NULL),('TR',0,0,2998,'Fall',2010,35,0,23,NULL),('TR',0,0,1294,'Fall',2010,65,0,1,NULL),('TR',0,0,2224,'Fall',2010,65,0,2,NULL),('TR',0,0,2286,'Fall',2010,65,0,9,NULL),('TR',0,0,2535,'Fall',2010,65,0,13,NULL),('TR',0,0,2597,'Fall',2010,65,0,14,NULL),('TR',0,0,2659,'Fall',2010,65,0,15,NULL),('TR',0,0,2787,'Fall',2010,65,0,17,NULL),('TR',0,0,2849,'Fall',2010,65,0,19,NULL),('TR',0,0,2977,'Fall',2010,65,0,23,NULL);
/*!40000 ALTER TABLE `StudentCourse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `StudentMajor`
--

DROP TABLE IF EXISTS `StudentMajor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `StudentMajor` (
  `userID` int(11) NOT NULL,
  `majorID` int(11) NOT NULL,
  `declaredDate` date NOT NULL,
  PRIMARY KEY (`userID`,`majorID`),
  KEY `userID` (`userID`),
  KEY `majorID` (`majorID`),
  CONSTRAINT `StudentMajor_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `StudentMajor_ibfk_2` FOREIGN KEY (`majorID`) REFERENCES `Major` (`majorID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `StudentMajor`
--

LOCK TABLES `StudentMajor` WRITE;
/*!40000 ALTER TABLE `StudentMajor` DISABLE KEYS */;
INSERT INTO `StudentMajor` VALUES (1,1,'0000-00-00'),(2,1,'0000-00-00'),(3,2,'0000-00-00'),(4,1,'0000-00-00'),(8,1,'0000-00-00'),(9,1,'0000-00-00'),(10,1,'0000-00-00'),(11,1,'0000-00-00'),(12,1,'0000-00-00'),(13,1,'0000-00-00'),(14,1,'0000-00-00'),(15,1,'0000-00-00'),(16,1,'0000-00-00'),(17,1,'0000-00-00'),(19,1,'0000-00-00'),(22,1,'0000-00-00'),(23,1,'0000-00-00'),(24,1,'0000-00-00'),(25,1,'0000-00-00'),(28,1,'0000-00-00'),(29,1,'0000-00-00');
/*!40000 ALTER TABLE `StudentMajor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `StudentMajorBucket`
--

DROP TABLE IF EXISTS `StudentMajorBucket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `StudentMajorBucket` (
  `userID` int(11) NOT NULL,
  `bucketID` int(11) NOT NULL,
  `selected` tinyint(1) NOT NULL,
  PRIMARY KEY (`userID`,`bucketID`),
  KEY `userID` (`userID`),
  KEY `bucketID` (`bucketID`),
  CONSTRAINT `StudentMajorBucket_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `StudentMajorBucket_ibfk_2` FOREIGN KEY (`bucketID`) REFERENCES `MajorBucket` (`bucketID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `StudentMajorBucket`
--

LOCK TABLES `StudentMajorBucket` WRITE;
/*!40000 ALTER TABLE `StudentMajorBucket` DISABLE KEYS */;
/*!40000 ALTER TABLE `StudentMajorBucket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `email` varchar(255) NOT NULL,
  `userName` varchar(35) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstName` varchar(35) NOT NULL,
  `lastName` varchar(35) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `Email` (`email`),
  UNIQUE KEY `Username` (`userName`),
  UNIQUE KEY `Password_2` (`password`,`firstName`,`lastName`,`type`),
  UNIQUE KEY `Password_3` (`password`,`firstName`,`lastName`,`type`),
  UNIQUE KEY `Password_4` (`password`,`firstName`,`lastName`,`type`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES ('jdoe001@fiu.edu','jdoe','$2y$10$qCX9DyNzi81oMj8FyIvFluQ3jF4mdG/n75BkhbZHu5s7X8PlMZGni','John','Doe2',0,1),('mdoe001@fiu.edu','mdoe','$2y$10$7VgYcS4uxTtutewqVtcjY.6bXlmWfmRROj0TybVVSken3oQ3pwDZq','Mary','Doe1',0,2),('pdoe001@fiu.edu','pdoe','$2y$10$ULy3u.tmleqVeKGcqn2G5uIZTRCIvz2ETS40e8DJUW9Zq1qbTe.Cm','Peter','Doe3',0,3),('admin@fiu.edu','admin','$2y$10$ho7bY27CG0l2.7b4LWn/s.6Vk25FpBF6UOlWyHdmeXM3WVtq9KUau','admin','fiu',1,4),('iukac001@fiu.edu','iukac001','$2y$10$GHant8JoN7ab7SwtgleQn.UNCEIE4MpozB8fyBLYz3.qFdpq52Wzq','Ike','Ukaci',0,8),('Hello@fiu.edu','hello','$2y$10$yOmp/wQPwbsenbKS2HvVU.5Q.6zz/9aKGrj.UPVLvJIRuWR7YgJpS','hello','hello',0,9),('jhena005@fiu.edu','jhena005','$2y$10$qOm1fJ231OmBT/sWEWLXpuruUzN3CnBXPAzIzgGHiZSwVcrXHjZPq','Johann','Henao',0,10),('rarci003@fiu.edu','rarci003','$2y$10$Zn9TQbDOiE.ainXgfOYGvueTm0Ls2GHCiVuGTUZxIf7UEju/zwDTa','Roberto','Arciniegas',0,11),('newuser21@fiu.edu','newuser20','$2y$10$IGgcMPTagkWZYHxZv9C.DOjLzgxoVQUEOkczZrUhmpidh8LsZZdhq','newuser','twenty',0,12),('newUser5@fiu.edu','newUser5','$2y$10$komLL8tBKS8McNUPWSMFVu5aFDKqZ59efZ4v05MskyWDAAhE/R/CO','New','User',0,13),('newuser50@fiu.edu','newUser50','$2y$10$9QUtHuYH6D1XcoRfVjrJxOay23b2qH.ZpqpvEYaNfA.9e6VNjb5N.','new','user',0,14),('new60@fiu.edu','newuser60','$2y$10$WDAkzfwIsIPVaCDTM1pdl.dwf2DArmE97ZZnmBHuTv.KrFEvxtTfm','new','user',0,15),('ikeU@fiu.edu','ikeufiu','$2y$10$26Rs056ZMyweEYlgN4Qy5eDXhkB1Tes5zx5EQ.3UQQKDcRcyhV1SG','ikeu','fiu',0,16),('newuser70@fiu.edu','newuser70','$2y$10$HMHn3KH.NNnF2S2CjusV4ewSCYDdn0mgJk9hWOpzV7CThhNVuPaFS','new','user',0,17),('ltorr121@fiu.edu','ltorr121','$2y$10$mVVCnFNUefcOvS78M7DBluKC6kBvBx3f6TgHZUIf5VrKw0/SKoTn6','Leonard','Torres',0,18),('newuser80@fiu.edu','newuser80','$2y$10$smxq1BdkF4gSOW16DginIO91ajUmWqL9ftKLv7Qtus.S057e7rAua','new','user',0,19),('newuser50@aol.com','newuser51','$2y$10$je5NgvJRC6Wu.E2Ko1DysubD78yRtq3FhL03ASnPeBbbckFtrGr0a','newuser','fifty',0,22),('newuser90@fiu.edu','newuser90','$2y$10$YE1pslb.lyv.29E5DdWKceW9RuDu90I.JOd6L7xkqpfIVvWEH417O','new','user',0,23),('jimmy@fiu.edu','jimmyfiu','$2y$10$sn5uwp/CrA5pcZl7h3PLoevrEMdgEF6UbcTmsSTLQNRar0jQ4y5H6','jimmy','fiu',0,24),('newuser52@aol.com','newuser52','$2y$10$kIYRPQx0oBQeUurEU8dkruhKPbIFju87Xh0HK5QHaBf3RRewnJmxO','neuser','fiftry2',0,25),('user1@gmail.com','user1','$2y$10$CXo.GD4EmUIZL6c/wS3yq.0/7aDYktwn9l10Q4p6pvurQnPzy1diK','User','One',0,26),('lmend066@fiu.edu','lmend066','$2y$10$Jo8gtYHD2OI4DksPi5472uIPEMGTjKdjnVrJOgiovyCwGC.Wzxgn2','Lizette','Mendoza',0,27),('newuser45@fiu.edu','newuser45','$2y$10$L9DlQxCSRoS/r9yuwnxCA.z58masRzpKK8SI4ot17OHgAWlseOyCi','new','user',0,28),('newuser30@gmail.com','newuser30','$2y$10$7GiXqEQhxCAJmJn5snV4MOrR8e8eLRiN0MOPKxZ53DnCfu7RJ6QaW','kid','kid',0,29),('admin99@fiu.edu','admin99','$2y$10$jRQRKBBR/hbOUnuf5qOs7OqooKLBOBjxqt7Jt4wFdsDzY63nwhOgK','admin','99',1,30);
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `Username` varchar(35) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
INSERT INTO `login_attempts` VALUES ('pdoe','0000-00-00 00:00:00'),('pdoe','0000-00-00 00:00:00'),('pdoe','0000-00-00 00:00:00'),('pdoe','0000-00-00 00:00:00'),('jdoe','0000-00-00 00:00:00'),('pdoe','0000-00-00 00:00:00'),('pdoe','0000-00-00 00:00:00'),('mdoe','0000-00-00 00:00:00'),('mdoe','0000-00-00 00:00:00'),('mdoe','0000-00-00 00:00:00'),('mdoe','0000-00-00 00:00:00'),('admin','0000-00-00 00:00:00'),('admin','0000-00-00 00:00:00'),('mdoe','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-04-01 19:17:06

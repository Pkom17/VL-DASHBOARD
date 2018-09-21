-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Lun 12 Mars 2018 à 17:24
-- Version du serveur :  5.7.14
-- Version de PHP :  5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `apidb`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_active_sites` ()  BEGIN
  SET @QUERY =    "SELECT 
                    `vf`.`ID`,
                    `vf`.`name` 
                  FROM `vl_site_summary` `vss` 
                  JOIN `view_facilitys` `vf` 
                    ON `vss`.`facility` = `vf`.`ID`";

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_all_sites_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `vf`.`name`, 
                    SUM((`vss`.`Undetected`+`vss`.`less1000`)) AS `suppressed`, 
                    SUM(`vss`.`sustxfail`) AS `nonsuppressed` 
                    FROM `vl_site_summary` `vss` 
                    LEFT JOIN `view_facilitys` `vf` 
                    ON `vss`.`facility` = `vf`.`ID`
    WHERE 1";

   

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
      END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vss`.`facility` ORDER BY `suppressed` DESC LIMIT 0, 50 ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_avg_labs_testing_trends` (IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    AVG(`vns`.`alltests`) AS `alltests`, 
                    AVG(`vns`.`rejected`) AS `rejected`, 
                    AVG(`vns`.`received`) AS `received`, 
                    `vns`.`month`, 
                    `vns`.`year` 
                FROM `vl_lab_summary` `vns` 
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `vns`.`year` = '",filter_year,"' ");
    SET @QUERY = CONCAT(@QUERY, " GROUP BY `month` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_counties_sustxfail` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `c`.`ID`, 
                    `c`.`name`, 
                    ((SUM(`less5000`+`above5000`)/(SUM(`Undetected`+`less1000`)+SUM(`less5000`+`above5000`)))*100) AS `sustxfail` 
                    FROM `vl_county_summary` `vcs` 
                    JOIN `countys` `c` 
                    ON `vcs`.`county` = `c`.`ID`
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `c`.`name` ORDER BY `sustxfail` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_counties_sustxfail_stats` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `c`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`,
                    (SUM(`Undetected`+`less1000`)/(SUM(`Undetected`+`less1000`)+SUM(`less5000`+`above5000`))) AS `pecentage`
                FROM vl_county_summary `vcs`
                LEFT JOIN countys `c`
                    ON c.ID = vcs.county 
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_lactating_women` (IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vnj`.`county` AS `id`, 
                    SUM(`vnj`.`tests`) AS `tests`
                FROM `vl_county_justification` `vnj`
                WHERE 1 AND `vnj`.`justification` = 9 ";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`year` = '",filter_year,"' AND `vnj`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`year` = '",filter_year,"' ");
    END IF;
  
  SET @QUERY = CONCAT(@QUERY, " GROUP BY `vnj`.`county` ");
  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_non_suppression` (IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vnj`.`county` AS `id`, 
                    SUM((`vnj`.`tests`)) AS `tests`, 
                    SUM((`vnj`.`above5000`)+(`vnj`.`less5000`)) AS `non_suppressed`
                FROM `vl_county_justification` `vnj`
                WHERE 1 ";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`year` = '",filter_year,"' AND `vnj`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`year` = '",filter_year,"' ");
    END IF;
  
  SET @QUERY = CONCAT(@QUERY, " GROUP BY `vnj`.`county` ");
  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `c`.`name`,
                    SUM(`vcs`.`undetected`+`vcs`.`less1000`) AS `suppressed`,
                    SUM(`vcs`.`less5000`+`vcs`.`above5000`) AS `nonsuppressed`,
                    SUM(`vcs`.`undetected`+`vcs`.`less1000`+`vcs`.`less5000`+`vcs`.`above5000`) AS `total`
                FROM `vl_county_summary` `vcs`
                    JOIN `countys` `c` ON `vcs`.`county` = `c`.`ID`
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vcs`.`county` ORDER BY `total` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_partner_details` (IN `county` INT(11), IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT `p`.`name` AS `partner`, `vf`.`name` AS `facility`, SUM(`vss`.`alltests`) AS `tests`, 
                  SUM(`vss`.`less1000` + `vss`.`undetected`) AS `suppressed`, 
                  SUM(`vss`.`less5000` + `vss`.`above5000`) AS `non_suppressed`,
                  SUM(`vss`.`rejected`) AS `rejected`, SUM(`vss`.`adults`) AS `adults`, SUM(`vss`.`paeds`) AS `children`
                FROM `vl_site_summary` `vss`
                JOIN (`view_facilitys` `vf` CROSS JOIN `partners` `p`)
                ON (`vss`.`facility`=`vf`.`ID` AND `p`.`ID`=`vf`.`partner`)
                WHERE 1 ";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",county,"' AND `vss`.`year` = '",filter_year,"' AND `vss`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",county,"' AND `vss`.`year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vf`.`ID` ");

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `p`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_positivity_notification` (IN `C_ID` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                        SUM(`actualinfantsPOS`) AS `positive`, 
                        ((SUM(`actualinfantsPOS`)/SUM(`actualinfants`))*100) AS `positivity_rate` 
                    FROM `county_summary`
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_ID,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_positivity_yearly_notification` (IN `C_Id` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                        SUM(`actualinfantsPOS`) AS `positive`, 
                        ((SUM(`actualinfantsPOS`)/SUM(`actualinfants`))*100) AS `positivity_rate` 
                    FROM `county_summary_yearly`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"'  ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_pregnant_women` (IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vnj`.`county` AS `id`, 
                    SUM(`vnj`.`tests`) AS `tests`
                FROM `vl_county_justification` `vnj`
                WHERE 1 AND `vnj`.`justification` = 6 ";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`year` = '",filter_year,"' AND `vnj`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`year` = '",filter_year,"' ");
    END IF;
  
  SET @QUERY = CONCAT(@QUERY, " GROUP BY `vnj`.`county` ");
  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_rejected` (IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vnj`.`county` AS `id`, 
                    SUM((`vnj`.`tests`)) AS `tests`, 
                    SUM(`vnj`.`rejected`) AS `rejected`
                FROM `vl_county_justification` `vnj`
                WHERE 1 ";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`year` = '",filter_year,"' AND `vnj`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`year` = '",filter_year,"' ");
    END IF;
  
  SET @QUERY = CONCAT(@QUERY, " GROUP BY `vnj`.`county` ");
  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_sites_listing` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						SUM(`vss`.`sustxfail`) AS `sustxfail`, 
                        SUM(`vss`.`alltests`) AS `alltests`, 
                        ((SUM(`vss`.`sustxfail`)/SUM(`vss`.`alltests`))*100) AS `non supp`, 
                        `vf`.`ID`, 
                        `vf`.`name` 
					FROM `vl_site_summary` `vss` LEFT JOIN `view_facilitys` `vf` 
                    ON `vss`.`facility`=`vf`.`ID` 
                    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",C_id,"' GROUP BY `vf`.`ID` ORDER BY `non supp` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_sites_outcomes` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `vf`.`name`,
                    SUM(`vss`.`undetected`+`vss`.`less1000`) AS `suppressed`,
                    SUM(`vss`.`less5000`+`vss`.`above5000`) AS `nonsuppressed`,
                    SUM(`vss`.`undetected`+`vss`.`less1000`+`vss`.`less5000`+`vss`.`above5000`) AS `total`  
                  FROM `vl_site_summary` `vss` 
                  LEFT JOIN `view_facilitys` `vf` 
                    ON `vss`.`facility` = `vf`.`ID` 
                  WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;





    SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",C_id,"' GROUP BY `vss`.`facility` ORDER BY `total` DESC LIMIT 0, 50 ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_summary` (IN `county` INT(11), IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vnj`.`county` AS `id`, 
                    SUM(`vnj`.`tests`) AS `tests`,
                    SUM(`vnj`.`less1000` + `vnj`.`undetected`) AS `suppressed`, 
                    SUM(`vnj`.`less5000` + `vnj`.`above5000`) AS `non_suppressed`,
                    SUM(`vnj`.`rejected`) AS `rejected`
                FROM `vl_county_justification` `vnj`
                WHERE 1  ";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`county` = '",county,"' AND `vnj`.`year` = '",filter_year,"' AND `vnj`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`county` = '",county,"' AND `vnj`.`year` = '",filter_year,"' ");
    END IF;
  
  SET @QUERY = CONCAT(@QUERY, " GROUP BY `vnj`.`county` ");
  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_suppression` (IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vnj`.`county` AS `id`, 
                    SUM((`vnj`.`tests`)) AS `tests`, 
                    SUM((`vnj`.`undetected`)+(`vnj`.`less1000`)) AS `suppressed`
                FROM `vl_county_justification` `vnj`
                WHERE 1 ";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`year` = '",filter_year,"' AND `vnj`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`year` = '",filter_year,"' ");
    END IF;
  
  SET @QUERY = CONCAT(@QUERY, " GROUP BY `vnj`.`county` ");
  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_county_sustxfail_subcounty` (IN `C_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                      `d`.`name`, 
                      ((SUM(`vss`.`sustxfail`)/SUM(`vss`.`alltests`))*100) AS `percentages`, 
                      SUM(`vss`.`sustxfail`) AS `sustxfail` 
                  FROM `vl_subcounty_summary` `vss` 
                  JOIN `districts` `d` 
                  ON `vss`.`subcounty` = `d`.`ID`
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `d`.`county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `d`.`county` = '",C_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `d`.`county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `d`.`county` = '",C_Id,"' AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `d`.`name` ORDER BY `percentages` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_age_breakdown` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11), IN `county` INT(11), IN `subcounty` INT(11), IN `partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `com`.`name`,
                    SUM(`nodatapos`) AS `nodatapos`,       
                    SUM(`nodataneg`) AS `nodataneg`,     
                    SUM(`less2wpos`) AS `less2wpos`,       
                    SUM(`less2wneg`) AS `less2wneg`,    
                    SUM(`twoto6wpos`) AS `twoto6wpos`,        
                    SUM(`twoto6wneg`) AS `twoto6wneg`,     
                    SUM(`sixto8wpos`) AS `sixto8wpos`,      
                    SUM(`sixto8wneg`) AS `sixto8wneg`,   
                    SUM(`sixmonthpos`) AS `sixmonthpos`,              
                    SUM(`sixmonthneg`) AS `sixmonthneg`,
                    SUM(`ninemonthpos`) AS `ninemonthpos`,      
                    SUM(`ninemonthneg`) AS `ninemonthneg`,   
                    SUM(`twelvemonthpos`) AS `twelvemonthpos`,              
                    SUM(`twelvemonthneg`) AS `twelvemonthneg` ";

    IF (county != 0 || county != '') THEN
        SET @QUERY = CONCAT(@QUERY, "FROM `county_agebreakdown` LEFT JOIN `countys` `com` ON `county_agebreakdown`.`county` = `com`.`ID` WHERE 1");
    END IF;           
        IF (subcounty != 0 || subcounty != '') THEN
        SET @QUERY = CONCAT(@QUERY, "FROM `subcounty_agebreakdown` LEFT JOIN `districts` `com` ON `subcounty_agebreakdown`.`subcounty` = `com`.`ID` WHERE 1");
    END IF;
    IF (partner != 0 || partner != '') THEN
        SET @QUERY = CONCAT(@QUERY, "FROM `ip_agebreakdown` LEFT JOIN `partners` `com` ON `ip_agebreakdown`.`partner` = `com`.`ID` WHERE 1");
    END IF;


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `com`.`ID` ");
     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_age_breakdown_trends` (IN `age` INT(11), IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
            `year`, 
            `month`, 
            `pos`, 
            `neg`

            FROM `national_age_breakdown`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND (`year` = '",from_year,"' OR `year` = '",to_year,"') 
        and `age_band_id` = '",age,"'
     ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_age_data` (IN `type` INT(11), IN `param` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  
  SET @column = '';

  SET @QUERY =    "SELECT
                    `a`.`name`,
                    SUM(`att`.`pos`) AS `positive`,
                    SUM(`att`.`neg`) AS `negative` ";
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `county_age_breakdown` `att` ");
      SET @column = " `att`.`county` ";
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_age_breakdown` `att`  ");
      SET @column = " `att`.`subcounty` ";
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `ip_age_breakdown` `att`  ");
      SET @column = " `att`.`partner` ";
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, "  FROM `site_age_breakdown` `att` ");
      SET @column = " `att`.`site` ";
    END IF;
    IF(type = 0) THEN
      SET @QUERY = CONCAT(@QUERY, "  FROM `national_age_breakdown` `att` ");
    END IF;

    
    SET @QUERY = CONCAT(@QUERY, " JOIN `age_bands` `a` ON `att`.`age_band_id` = `a`.`ID` WHERE 1 ");


    
    IF(type != 0) THEN
      SET @QUERY = CONCAT(@QUERY, " AND ", @column , " = '",param,"' ");
    END IF;


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;



    SET @QUERY = CONCAT(@QUERY, " GROUP BY `a`.`name` ORDER BY `age_band_id` ASC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_age_data_listing` (IN `type` INT(11), IN `age` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  
  SET @column = '';

  SET @QUERY =    "SELECT
                    `a`.`name`,
                    SUM(`att`.`pos`) AS `pos`,
                    SUM(`att`.`neg`) AS `neg` ";
    
  IF(type = 1) THEN
    SET @column = " `att`.`county` ";
    SET @QUERY = CONCAT(@QUERY, " FROM `county_age_breakdown` `att` JOIN `countys` `a`   ");
  END IF;
  IF(type = 2) THEN
    SET @column = " `att`.`subcounty` ";
    SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_age_breakdown` `att` JOIN `districts` `a` ");
  END IF;
  IF(type = 3) THEN
    SET @column = " `att`.`partner` ";
    SET @QUERY = CONCAT(@QUERY, " FROM `ip_age_breakdown` `att` JOIN `partners` `a` ");
  END IF;
  IF(type = 4) THEN
    SET @column = " `att`.`site` ";
    SET @QUERY = CONCAT(@QUERY, " FROM `site_age_breakdown` `att` JOIN `facilitys` `a` ");
  END IF;

    
  SET @QUERY = CONCAT(@QUERY, "  ON ", @column ," = `a`.`ID` WHERE 1 ");    

    

    IF(age != 0) THEN
      SET @QUERY = CONCAT(@QUERY, " AND `age_band_id` = '",age,"' ");
    END IF;


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;



    SET @QUERY = CONCAT(@QUERY, " GROUP BY ", @column , " ORDER BY `pos` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_age_testing_trends` (IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
            `year`, 
            `month`, 
            `nodatapos`,       
            `nodataneg`,     
            `less2wpos`,       
            `less2wneg`,    
            `twoto6wpos`,        
            `twoto6wneg`,     
            `sixto8wpos`,      
            `sixto8wneg`,   
            `sixmonthpos`,              
            `sixmonthneg`,
            `ninemonthpos`,      
            `ninemonthneg`,   
            `twelvemonthpos`,              
            `twelvemonthneg` 
            FROM `national_agebreakdown`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `year` BETWEEN '",from_year,"' AND '",to_year,"' ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_all_sites_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `vf`.`name`, 
                    SUM((`ss`.`actualinfantsPOS`)) AS `pos`, 
                    SUM((`ss`.`actualinfants`-`ss`.`actualinfantsPOS`)) AS `neg` 
                  FROM `site_summary` `ss` 
                  LEFT JOIN `view_facilitys` `vf` 
                    ON `ss`.`facility` = `vf`.`ID`
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ss`.`facility` ORDER BY `neg` DESC, `pos` DESC LIMIT 0, 50 ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_average_rejection` (IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `year`, `month`,
                    AVG(`tests`) AS `tests`, 
                    AVG(`rejected`) AS `rejected`
                  FROM `national_summary` 
                  WHERE 1";
				  
	SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    SET @QUERY = CONCAT(@QUERY, " GROUP BY `month` ");
    SET @QUERY = CONCAT(@QUERY, " ORDER BY `month` ASC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_counties_positivity_mixed` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `c`.`name`,
                    SUM(`actualinfantsPOS`) AS `pos`,
                    SUM(`actualinfants`-`actualinfantsPOS`) AS `neg`,
                    SUM(`actualinfants`) AS `tests`,
                    ((SUM(`actualinfantsPOS`)/(SUM(`actualinfants`)))*100) AS `pecentage`
                ";

     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `county_summary` `cs` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `county_summary_yearly` `cs` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN countys `c`
                    ON c.ID = cs.county WHERE 1 ");


   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `c`.`name` ORDER BY `tests` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_counties_positivity_stats` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `c`.`name`,
                    SUM(`actualinfantsPOS`) AS `pos`,
                    SUM(`actualinfants`-`actualinfantsPOS`) AS `neg`,
                    ((SUM(`actualinfantsPOS`)/(SUM(`actualinfants`)))*100) AS `pecentage`";

     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `county_summary` `cs` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `county_summary_yearly` `cs` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN countys `c`
                    ON c.ID = cs.county WHERE 1 ");

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `c`.`name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_countys_details` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                  `countys`.`name` AS `county`, 
                  AVG(`sitessending`) AS `sitessending`,
                  SUM(`alltests`) AS `alltests`, 
                  `countys`.`pmtctneed1617` AS `pmtctneed`,
                  SUM(`actualinfants`) AS `actualinfants`, 
                   SUM(`pos`) AS `positive`, 
                  SUM(`neg`) AS `negative`, 
                  SUM(`repeatspos`) AS `repeatspos`,
                  SUM(`repeatposPOS`) AS `repeatsposPOS`,
                  SUM(`confirmdna`) AS `confirmdna`,
                  SUM(`confirmedPOS`) AS `confirmedPOS`,
                  SUM(`infantsless2w`) AS `infantsless2w`, 
                  SUM(`infantsless2wPOS`) AS `infantsless2wpos`, 
                  SUM(`infantsless2m`) AS `infantsless2m`, 
                  SUM(`infantsless2mPOS`) AS `infantsless2mpos`, 
                  SUM(`infantsabove2m`) AS `infantsabove2m`, 
                  SUM(`infantsabove2mPOS`) AS `infantsabove2mpos`,  
                  AVG(`medage`) AS `medage`,
                  SUM(`rejected`) AS `rejected`
                  FROM `county_summary` 
                  LEFT JOIN `countys` ON `county_summary`.`county` = `countys`.`ID`  WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `county_summary`.`county` ORDER BY `tests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_age` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
            SUM(`sixweekspos`) AS `sixweekspos`, 
            SUM(`sixweeksneg`) AS `sixweeksneg`, 
            SUM(`sevento3mpos`) AS `sevento3mpos`, 
            SUM(`sevento3mneg`) AS `sevento3mneg`,
            SUM(`threemto9mpos`) AS `threemto9mpos`, 
            SUM(`threemto9mneg`) AS `threemto9mneg`,
            SUM(`ninemto18mpos`) AS `ninemto18mpos`, 
            SUM(`ninemto18mneg`) AS `ninemto18mneg`,
            SUM(`above18mpos`) AS `above18mpos`, 
            SUM(`above18mneg`) AS `above18mneg`,
            SUM(`nodatapos`) AS `nodatapos`, 
            SUM(`nodataneg`) AS `nodataneg`
          FROM `county_agebreakdown` WHERE 1";
  

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_age_breakdown` (IN `C_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM(`less2wpos`) AS `less2wpos`,
                    SUM(`less2wneg`) AS `less2wneg`,
                    SUM(`twoto6wpos`) AS `twoto6wpos`,
                    SUM(`twoto6wneg`) AS `twoto6wneg`,
                    SUM(`sixto8wpos`) AS `sixto8wpos`,
                    SUM(`sixto8wneg`) AS `sixto8wneg`,
                    SUM(`sixmonthpos`) AS `sixmonthpos`,
                    SUM(`sixmonthneg`) AS `sixmonthneg`,
                    SUM(`ninemonthpos`) AS `ninemonthpos`,
                    SUM(`ninemonthneg`) AS `ninemonthneg`,
                    SUM(`twelvemonthpos`) AS `twelvemonthpos`,
                    SUM(`twelvemonthneg`) AS `twelvemonthneg`
                    FROM county_agebreakdown
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_age_positivity` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        SUM(`nodatapos`) AS `nodatapos`,
                        SUM(`nodataneg`) AS `nodataneg`,
                        SUM(`less2wpos`) AS `less2wpos`,
                        SUM(`less2wneg`) AS `less2wneg`,
                        SUM(`twoto6wpos`) AS `twoto6wpos`,
                        SUM(`twoto6wneg`) AS `twoto6wneg`,
                        SUM(`sixto8wpos`) AS `sixto8wpos`,
                        SUM(`sixto8wneg`) AS `sixto8wneg`,
                        SUM(`sixmonthpos`) AS `sixmonthpos`,
                        SUM(`sixmonthneg`) AS `sixmonthneg`,
                        SUM(`ninemonthpos`) AS `ninemonthpos`,
                        SUM(`ninemonthneg`) AS `ninemonthneg`,
                        SUM(`twelvemonthpos`) AS `twelvemonthpos`,
                        SUM(`twelvemonthneg`) AS `twelvemonthneg`
                    FROM `county_agebreakdown` 
                WHERE 1 ";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_age_range` (IN `band_type` INT(11), IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
            `a`.`name` AS `age_band`,
            `a`.`age_range`,
            SUM(`pos`) AS `pos`, 
            SUM(`neg`) AS `neg`
          FROM `county_age_breakdown` `n`
          LEFT JOIN `age_bands` `a` ON `a`.`ID` = `n`.`age_band_id`
          WHERE 1";
  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

    IF (band_type = 1) THEN
        SET @QUERY = CONCAT(@QUERY, " GROUP BY `a`.`ID`  ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " GROUP BY `a`.`age_range_id`  ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_age_summary` (IN `C_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
  					SUM(`infantsless2m`) AS `infantsless2m`,       
					SUM(`infantsless2mPOS`) AS `infantsless2mPOS`,     
					SUM(`infantsless2w`) AS `infantsless2w`,       
					SUM(`infantsless2wPOS`) AS `infantsless2wPOS`,    
					SUM(`infants4to6w`) AS `infants4to6w`,        
					SUM(`infants4to6wPOS`) AS `infants4to6wPOS`,     
					SUM(`infantsabove2m`) AS `infantsabove2m`,      
					SUM(`infantsabove2mPOS`) AS `infantsabove2mPOS`,   
					SUM(`adults`) AS `adults`,              
					SUM(`adultsPOS`) AS `adultsPOS`            
					FROM `county_summary`
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_eid_outcomes` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`pos`) AS `pos`,
        SUM(`neg`) AS `neg`,
        AVG(`medage`) AS `medage`,
        SUM(`alltests`) AS `alltests`,
        SUM(`eqatests`) AS `eqatests`,
        SUM(`firstdna`) AS `firstdna`,
        SUM(`confirmdna`) AS `confirmdna`,
        SUM(`confirmedPOS`) AS `confirmpos`,
        SUM(`repeatspos`) AS `repeatspos`,
        SUM(`pos`) AS `pos`,
        SUM(`neg`) AS `neg`,
        SUM(`repeatposPOS`) AS `repeatsposPOS`,
        SUM(`actualinfants`) AS `actualinfants`,
        SUM(`actualinfantsPOS`) AS `actualinfantspos`,
        SUM(`infantsless2m`) AS `infantsless2m`,
        SUM(`infantsless2mPOS`) AS `infantless2mpos`,
        SUM(`adults`) AS `adults`,
        SUM(`adultsPOS`) AS `adultsPOS`,
        SUM(`redraw`) AS `redraw`,
        SUM(`tests`) AS `tests`,
        SUM(`rejected`) AS `rejected`, 
        AVG(`sitessending`) AS `sitessending`";


    IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `county_summary` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `county_summary_yearly` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " WHERE 1 ");


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_entryP_positivity` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
  						`p`.`name`,
                        SUM(`pos`) AS `pos`,
                        SUM(`neg`) AS `neg`
                    FROM `county_entrypoint` `ci` 
                    LEFT JOIN `entry_points` `p` ON `ci`.`entrypoint` = `p`.`ID`
                WHERE 1 ";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' GROUP BY `p`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_entry_points` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `ep`.`name`, 
                        SUM(`pos`) AS `positive`, 
                        SUM(`neg`) AS `negative`  
                    FROM `county_entrypoint` `nep` 
                    JOIN `entry_points` `ep` 
                    ON `nep`.`entrypoint` = `ep`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ep`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_hei` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`enrolled`) AS `enrolled`,
        SUM(`dead`) AS `dead`,
        SUM(`ltfu`) AS `ltfu`,
        SUM(`adult`) AS `adult`,
        SUM(`transout`) AS `transout`,
        SUM(`other`) AS `other`
    FROM `county_summary`
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_hei_validation` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`validation_confirmedpos`) AS `Confirmed Positive`,
        SUM(`validation_repeattest`) AS `Repeat Test`,
        AVG(`validation_viralload`) AS `Viral Load`,
        SUM(`validation_adult`) AS `Adult`,
        SUM(`validation_unknownsite`) AS `Unknown Facility`,
        SUM(`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`) AS `followup_hei`, 
        sum(`actualinfantsPOS`) AS `positives`, 
        SUM(`actualinfants`-((`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`)-(`validation_repeattest`+`validation_unknownsite`+`validation_adult`+`validation_viralload`))) AS `true_tests` 
    FROM `county_summary`
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_infantsless2m` (IN `C_id` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `month`, 
                    `year`, 
                    `infantsless2m` 
                FROM `county_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ORDER BY `year`, `month` ASC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_iprophylaxis` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `p`.`name`, 
                        SUM(`pos`) AS `positive`, 
                        SUM(`neg`) AS `negative` 
                    FROM `county_iprophylaxis` `nip` 
                    JOIN `prophylaxis` `p` ON `nip`.`prophylaxis` = `p`.`ID`
                WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_iproph_positivity` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
  								`p`.`name`,
                        SUM(`pos`) AS `pos`,
                        SUM(`neg`) AS `neg`
                    FROM `county_iprophylaxis` `ci` 
                    LEFT JOIN `prophylaxis` `p` ON `ci`.`prophylaxis` = `p`.`ID`
                WHERE 1 ";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' GROUP BY `p`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_mprophylaxis` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `p`.`name`, 
                        SUM(`pos`) AS `positive`, 
                        SUM(`neg`) AS `negative` 
                    FROM `county_mprophylaxis` `nmp` 
                    JOIN `prophylaxis` `p` ON `nmp`.`prophylaxis` = `p`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_mproph_positivity` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
  						`p`.`name`,
                        SUM(`pos`) AS `pos`,
                        SUM(`neg`) AS `neg`
                    FROM `county_mprophylaxis` `ci` 
                    LEFT JOIN `prophylaxis` `p` ON `ci`.`prophylaxis` = `p`.`ID`
                WHERE 1 ";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' GROUP BY `p`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `c`.`name`,
                    `cs`.`county`,
                    SUM(`cs`.`pos`) AS `positive`,
                    SUM(`cs`.`neg`) AS `negative` 
                FROM `county_summary` `cs`
                    JOIN `countys` `c` ON `cs`.`county` = `c`.`ID`
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `cs`.`county` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_partners_details` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                  `p`.name AS `partner`, 
                  `c`.name AS `county`,
                  SUM(`alltests`) AS `alltests`, 
                  SUM(`actualinfants`) AS `actualinfants`,
                  SUM(`pos`) AS `positive`, 
                  SUM(`neg`) AS `negative`, 
                  SUM(`repeatspos`) AS `repeatspos`,
                  SUM(`repeatposPOS`) AS `repeatsposPOS`,
                  SUM(`confirmdna`) AS `confirmdna`,
                  SUM(`confirmedPOS`) AS `confirmedPOS`,
                  SUM(`infantsless2w`) AS `infantsless2w`, 
                  SUM(`infantsless2wPOS`) AS `infantsless2wpos`, 
                  SUM(`infantsless2m`) AS `infantsless2m`, 
                  SUM(`infantsless2mPOS`) AS `infantsless2mpos`, 
                  SUM(`infantsabove2m`) AS `infantsabove2m`, 
                  SUM(`infantsabove2mPOS`) AS `infantsabove2mpos`, 
                  AVG(`medage`) AS `medage`,
                  SUM(`rejected`) AS `rejected`     ";


     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `site_summary` `is` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `site_summary_yearly` `is` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN `view_facilitys` `vf` ON `vf`.`ID` = `is`.`facility`
                    LEFT JOIN `partners` `p` ON `p`.`ID` = `vf`.`partner`
                    LEFT JOIN `countys` `c` ON `c`.`ID` = `vf`.`county`
                  WHERE 1 ");


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vf`.county = '",C_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`name` ORDER BY `tests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_partner_positivity` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    DISTINCT(`p`.`name`) AS `name`,
                    SUM(`actualinfantsPOS`) AS `pos`,
                    SUM(`actualinfants`-`actualinfantsPOS`) AS `neg`,
                    ((SUM(`actualinfantsPOS`)/(SUM(`actualinfants`)))*100) AS `pecentage` ";


     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `ip_summary` `is` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `ip_summary_yearly` `is` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN `partners` `p` 
                  ON `is`.`partner` = `p`.`ID` 
                LEFT JOIN `view_facilitys` `vf`
                    ON `p`.`ID` = `vf`.`partner`
                WHERE 1 ");



   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",C_id,"' GROUP BY `name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_sites_details` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `to_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                  `view_facilitys`.`facilitycode` AS `MFLCode`, 
                  `view_facilitys`.`name`, 
                  `countys`.`name` AS `county`, 
                  `districts`.`name` AS `subcounty`, 
                  SUM(`tests`) AS `tests`, 
                  SUM(`firstdna`) AS `firstdna`, 
                  SUM(`confirmdna` + `repeatspos`) AS `confirmdna`,
                  SUM(`pos`) AS `positive`, 
                  SUM(`neg`) AS `negative`, 
                  SUM(`redraw`) AS `redraw`, 
                  SUM(`adults`) AS `adults`, 
                  SUM(`adultsPOS`) AS `adultspos`, 
                  AVG(`medage`) AS `medage`, 
                  SUM(`rejected`) AS `rejected`, 
                  SUM(`infantsless2m`) AS `infantsless2m`, 
                  SUM(`infantsless2mPOS`) AS `infantsless2mpos` 

                  FROM `site_summary` 
                  LEFT JOIN `view_facilitys` ON `site_summary`.`facility` = `view_facilitys`.`ID` 
                  LEFT JOIN `countys` ON `view_facilitys`.`county` = `countys`.`ID` 
                  LEFT JOIN `districts` ON `view_facilitys`.`district` = `districts`.`ID`  WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`county` = '",C_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`ID` ORDER BY `tests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_sites_outcomes` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                      `vf`.`name`,
                      SUM(`actualinfantsPOS`) AS `positive`,
                      SUM(`actualinfants`-`actualinfantsPOS`) AS `negative` 
                      FROM `site_summary` `ss` 
                      LEFT JOIN `view_facilitys` `vf` ON `ss`.`facility` = `vf`.`ID`  
                  WHERE 1";

 

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",C_id,"' ");


    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vf`.`name` ORDER BY `negative` DESC, `positive` DESC LIMIT 0, 50 ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_sites_positivity` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						            SUM(`vss`.`actualinfantsPOS`) AS `pos`, 
                        SUM(`actualinfants`-`actualinfantsPOS`) AS `neg`,
                        SUM(`vss`.`actualinfants`) AS `alltests`,
                        ((SUM(`vss`.`actualinfantsPOS`)/SUM(`vss`.`actualinfants`))*100) AS `positivity`, 
                        `vf`.`ID`, 
                        `vf`.`name` ";

     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `site_summary` `vss` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `site_summary_yearly` `vss` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN `view_facilitys` `vf` 
                    ON `vss`.`facility`=`vf`.`ID` 
                    WHERE 1 ");



  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",C_id,"' GROUP BY `vf`.`ID` ORDER BY `positivity` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_subcounties_details` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                  `d`.`name` AS `subcounty`,
                   `c`.`name` AS `county`,
                  SUM(`alltests`) AS `alltests`, 
                  AVG(`sitessending`) AS `sitessending`,
                  SUM(`actualinfants`) AS `actualinfants`, 
                   SUM(`pos`) AS `positive`, 
                  SUM(`neg`) AS `negative`, 
                  SUM(`repeatspos`) AS `repeatspos`,
                  SUM(`repeatposPOS`) AS `repeatsposPOS`,
                  SUM(`confirmdna`) AS `confirmdna`,
                  SUM(`confirmedPOS`) AS `confirmedPOS`,
                  SUM(`infantsless2w`) AS `infantsless2w`, 
                  SUM(`infantsless2wPOS`) AS `infantsless2wpos`, 
                  SUM(`infantsless2m`) AS `infantsless2m`, 
                  SUM(`infantsless2mPOS`) AS `infantsless2mpos`, 
                  SUM(`infantsabove2m`) AS `infantsabove2m`, 
                  SUM(`infantsabove2mPOS`) AS `infantsabove2mpos`, 
                  AVG(`medage`) AS `medage`,
                  SUM(`rejected`) AS `rejected`   ";


     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary` `scs` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary_yearly` `scs` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN `districts` `d` ON `scs`.`subcounty` = `d`.`ID`
            LEFT JOIN `countys` `c` ON `d`.`county` = `c`.`ID`  WHERE 1 ");


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `c`.`ID` = '",C_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `subcounty` ORDER BY `tests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_subcounties_positivity` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `c`.`name`,
                    SUM(`actualinfantsPOS`) AS `pos`,
                    SUM(`actualinfants`-`actualinfantsPOS`) AS `neg`,
                    ((SUM(`actualinfantsPOS`)/(SUM(`actualinfants`)))*100) AS `pecentage`
                ";


     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary` `cs` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary_yearly` `cs` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN districts `c`
                    ON c.ID = cs.subcounty 
                WHERE 1 ");


   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `c`.`county` = '",C_id,"' GROUP BY `name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_testing_trends` (IN `C_id` INT(11), IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
            `year`, 
            `month`, 
            `pos`, 
            `neg`,
            `rpos`, 
            `rneg`,
            `allpos`, 
            `allneg` 
            FROM `county_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' AND `year` BETWEEN '",from_year,"' AND '",to_year,"' ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_yearly_hei` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`enrolled`) AS `enrolled`,
        SUM(`dead`) AS `dead`,
        SUM(`ltfu`) AS `ltfu`,
        SUM(`adult`) AS `adult`,
        SUM(`transout`) AS `transout`,
        SUM(`other`) AS `other`
    FROM `county_summary_yearly`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' AND `year` = '",filter_year,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_county_yearly_hei_validation` (IN `C_id` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`validation_confirmedpos`) AS `Confirmed Positive`,
        SUM(`validation_repeattest`) AS `Repeat Test`,
        AVG(`validation_viralload`) AS `Viral Load`,
        SUM(`validation_adult`) AS `Adult`,
        SUM(`validation_unknownsite`) AS `Unknown Facility`,
        SUM(`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`) AS `followup_hei`, 
        sum(`actualinfantsPOS`) AS `positives`, 
        SUM(`actualinfants`-((`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`)-(`validation_repeattest`+`validation_unknownsite`+`validation_adult`+`validation_viralload`))) AS `true_tests` 
    FROM `county_summary_yearly`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' AND `year` = '",filter_year,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_iprophylaxis` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `p`.`name`,
                    SUM(`tests`) AS `tests`,       
                    SUM(`pos`) AS `pos`,     
                    SUM(`neg`) AS `neg`,       
                    SUM(`redraw`) AS `redraw` 
                FROM `national_iprophylaxis` `ci` 
                LEFT JOIN `prophylaxis` `p` ON `ci`.`prophylaxis` = `p`.`ID` 
                WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `tests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_iproph_breakdown` (IN `Pr_ID` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11), IN `county` INT(11), IN `subcounty` INT(11), IN `partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                `com`.`name`,
                SUM(`tests`) AS `tests`,       
                SUM(`pos`) AS `pos`,     
                SUM(`neg`) AS `neg`,       
                SUM(`redraw`) AS `redraw` ";

    IF (county != 0 || county != '') THEN
        SET @QUERY = CONCAT(@QUERY, "FROM `county_iprophylaxis` LEFT JOIN `countys` `com` ON `county_iprophylaxis`.`county` = `com`.`ID` WHERE 1 ");
    END IF;           
        IF (subcounty != 0 || subcounty != '') THEN
        SET @QUERY = CONCAT(@QUERY, "FROM `subcounty_iprophylaxis` LEFT JOIN `districts` `com` ON `subcounty_iprophylaxis`.`subcounty` = `com`.`ID` WHERE 1 ");
    END IF;
    IF (partner != 0 || partner != '') THEN
        SET @QUERY = CONCAT(@QUERY, "FROM `ip_iprophylaxis` LEFT JOIN `partners` `com` ON `ip_iprophylaxis`.`partner` = `com`.`ID` WHERE 1 ");
    END IF;


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `prophylaxis` = '",Pr_ID,"' GROUP BY `com`.`ID` ORDER BY `tests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_iproph_testing_trends` (IN `Pr_ID` INT(11), IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
            `year`, 
            `month`, 
            `tests`,       
            `pos`,     
            `neg`,       
            `redraw` 
            FROM `national_iprophylaxis`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `prophylaxis` = '",Pr_ID,"' AND `year` BETWEEN '",from_year,"' AND '",to_year,"' ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_lab_live_data` ()  BEGIN
  SET @QUERY =    "SELECT
                    `labs`.`ID` AS `ID`,
                    `labs`.`name` AS `name`,
                    SUM((`lablogs`.`enteredsamplesatsite`)) AS `enteredsamplesatsite`,
                    SUM((`lablogs`.`enteredsamplesatlab`)) AS `enteredsamplesatlab`,
                    SUM((`lablogs`.`receivedsamples`)) AS `receivedsamples`,
                    SUM((`lablogs`.`inqueuesamples`)) AS `inqueuesamples`,
                    SUM((`lablogs`.`inprocesssamples`)) AS `inprocesssamples`,
                    SUM((`lablogs`.`processedsamples`)) AS `processedsamples`,
                    SUM((`lablogs`.`pendingapproval`)) AS `pendingapproval`,
                    SUM((`lablogs`.`dispatchedresults`)) AS `dispatchedresults`,
                    SUM((`lablogs`.`oldestinqueuesample`)) AS `oldestinqueuesample`,
                    `dateupdated`
                FROM `lablogs`

                JOIN `labs` 
                    ON `lablogs`.`lab` = `labs`.`ID`
                WHERE 1 AND DATE(logdate) = (SELECT MAX(logdate) from lablogs WHERE testtype=1) AND testtype=1
                GROUP BY `labs`.`ID`";
  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_lab_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `l`.`ID`, `l`.`labname` AS `name`, 
                    SUM(`ls`.`pos`) AS `pos`,
                    SUM(`ls`.`neg`) AS `neg`,
                    SUM(`redraw`) AS `redraw`
                FROM `lab_summary` `ls`
                JOIN `labs` `l`
                ON `l`.`ID` = `ls`.`lab` 
                WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;
      

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `l`.`ID` ");    
    SET @QUERY = CONCAT(@QUERY, " ORDER BY `l`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_lab_performance` (IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `l`.`ID`, `l`.`labname` AS `name`, (`ls`.`alltests`) AS `tests`, (`ls`.`received`) AS `received`, `ls`.`rejected`, `ls`.`pos`, `ls`.neg,
                    `ls`.`month` 
                FROM `lab_summary` `ls`
                JOIN `labs` `l`
                ON `l`.`ID` = `ls`.`lab` 
                WHERE 1 ";

    
        SET @QUERY = CONCAT(@QUERY, " AND `ls`.`year` = '",filter_year,"' ");
  
  SET @QUERY = CONCAT(@QUERY, " ORDER BY `ls`.`month`, `l`.`ID` ");
  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_lab_performance_stats` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `l`.`labname` AS `name`, 
                    AVG(`ls`.`sitessending`) AS `sitesending`, 
                    SUM(`ls`.`batches`) AS `batches`, 
                    SUM(`ls`.`received`) AS `received`, 
                    SUM(`ls`.`tests`) AS `tests`, 
                    SUM(`ls`.`alltests`) AS `alltests`,  
                    SUM(`ls`.`rejected`) AS `rejected`,  
                    SUM(`ls`.`confirmdna`) AS `confirmdna`,  
                    SUM(`ls`.`confirmedPOs`) AS `confirmedpos`,
                    SUM(`ls`.`repeatspos`) AS `repeatspos`,  
                    SUM(`ls`.`repeatposPOS`) AS `repeatspospos`,
                    SUM(`ls`.`eqatests`) AS `eqa`,  
                    SUM(`ls`.`pos`) AS `pos`, 
                    SUM(`ls`.`neg`) AS `neg`, 
                    SUM(`ls`.`redraw`) AS `redraw` 
                  FROM `lab_summary` `ls` JOIN `labs` `l` ON `ls`.`lab` = `l`.`ID` 
                WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

      SET @QUERY = CONCAT(@QUERY, " GROUP BY `l`.`ID` ORDER BY `alltests` DESC ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_lab_rejections` (IN `lab` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`total`) AS `total`,
        `rr`.`name`,
        `rr`.`alias` ";


    IF (lab != 0 && lab != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `lab_rejections` `v`");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `national_rejections` `v`");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN `rejectedreasons` `rr` ON `v`.`rejected_reason` = `rr`.`ID`");

    SET @QUERY = CONCAT(@QUERY, " WHERE 1 ");


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    IF (lab != 0 && lab != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `lab` = '",lab,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `rr`.`ID` ORDER BY `total` DESC ");
    

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_lab_tat` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `l`.`ID`, `l`.`labname` AS `name`, AVG(`ls`.`tat1`) AS `tat1`,
                    AVG(`ls`.`tat2`) AS `tat2`, AVG(`ls`.`tat3`) AS `tat3`,
                    AVG(`ls`.`tat4`) AS `tat4`
                FROM `lab_summary` `ls`
                JOIN `labs` `l`
                ON `l`.`ID` = `ls`.`lab` 
                WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;
      

  SET @QUERY = CONCAT(@QUERY, " GROUP BY `l`.`ID` ");
    SET @QUERY = CONCAT(@QUERY, " ORDER BY `l`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_live_data_totals` ()  BEGIN
  SET @QUERY =    "SELECT
                    SUM((`lablogs`.`receivedsamples`)) AS `receivedsamples`,
                    SUM((`lablogs`.`inqueuesamples`)) AS `inqueuesamples`,
                    SUM((`lablogs`.`inprocesssamples`)) AS `inprocesssamples`,
                    SUM((`lablogs`.`processedsamples`)) AS `processedsamples`,
                    SUM((`lablogs`.`pendingapproval`)) AS `pendingapproval`,
                    SUM((`lablogs`.`dispatchedresults`)) AS `dispatchedresults`,
                    SUM((`lablogs`.`enteredsamplesatlab`)) AS `enteredsamplesatlab`,
                    SUM((`lablogs`.`enteredsamplesatsite`)) AS `enteredsamplesatsite`,
                    SUM((`lablogs`.`enteredreceivedsameday`)) AS `enteredreceivedsameday`,
                    SUM((`lablogs`.`enterednotreceivedsameday`)) AS `enterednotreceivedsameday`,
                    SUM((`lablogs`.`abbottinprocess`)) AS `abbottinprocess`,
                    SUM((`lablogs`.`panthainprocess`)) AS `panthainprocess`,
                    SUM((`lablogs`.`rocheinprocess`)) AS `rocheinprocess`,
                    SUM((`lablogs`.`abbottprocessed`)) AS `abbottprocessed`,
                    SUM((`lablogs`.`panthaprocessed`)) AS `panthaprocessed`,
                    SUM((`lablogs`.`rocheprocessed`)) AS `rocheprocessed`
                FROM `lablogs`

                WHERE 1 AND DATE(logdate) = (SELECT MAX(logdate) from lablogs WHERE testtype=1) AND testtype=1";
  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_age` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
            SUM(`sixweekspos`) AS `sixweekspos`, 
            SUM(`sixweeksneg`) AS `sixweeksneg`, 
            SUM(`sevento3mpos`) AS `sevento3mpos`, 
            SUM(`sevento3mneg`) AS `sevento3mneg`,
            SUM(`threemto9mpos`) AS `threemto9mpos`, 
            SUM(`threemto9mneg`) AS `threemto9mneg`,
            SUM(`ninemto18mpos`) AS `ninemto18mpos`, 
            SUM(`ninemto18mneg`) AS `ninemto18mneg`,
            SUM(`above18mpos`) AS `above18mpos`, 
            SUM(`above18mneg`) AS `above18mneg`,
            SUM(`nodatapos`) AS `nodatapos`, 
            SUM(`nodataneg`) AS `nodataneg`
          FROM `national_agebreakdown` WHERE 1";
  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_age_breakdown` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM(`less2wpos`) AS `less2wpos`,
                    SUM(`less2wneg`) AS `less2wneg`,
                    SUM(`twoto6wpos`) AS `twoto6wpos`,
                    SUM(`twoto6wneg`) AS `twoto6wneg`,
                    SUM(`sixto8wpos`) AS `sixto8wpos`,
                    SUM(`sixto8wneg`) AS `sixto8wneg`,
                    SUM(`sixmonthpos`) AS `sixmonthpos`,
                    SUM(`sixmonthneg`) AS `sixmonthneg`,
                    SUM(`ninemonthpos`) AS `ninemonthpos`,
                    SUM(`ninemonthneg`) AS `ninemonthneg`,
                    SUM(`twelvemonthpos`) AS `twelvemonthpos`,
                    SUM(`twelvemonthneg`) AS `twelvemonthneg`
                    FROM national_agebreakdown
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_age_positivity` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        SUM(`nodatapos`) AS `nodatapos`,
                        SUM(`nodataneg`) AS `nodataneg`,
                        SUM(`less2wpos`) AS `less2wpos`,
                        SUM(`less2wneg`) AS `less2wneg`,
                        SUM(`twoto6wpos`) AS `twoto6wpos`,
                        SUM(`twoto6wneg`) AS `twoto6wneg`,
                        SUM(`sixto8wpos`) AS `sixto8wpos`,
                        SUM(`sixto8wneg`) AS `sixto8wneg`,
                        SUM(`sixmonthpos`) AS `sixmonthpos`,
                        SUM(`sixmonthneg`) AS `sixmonthneg`,
                        SUM(`ninemonthpos`) AS `ninemonthpos`,
                        SUM(`ninemonthneg`) AS `ninemonthneg`,
                        SUM(`twelvemonthpos`) AS `twelvemonthpos`,
                        SUM(`twelvemonthneg`) AS `twelvemonthneg`
                    FROM `national_agebreakdown` 
                WHERE 1 ";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_age_range` (IN `band_type` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
            `a`.`name` AS `age_band`,
            `a`.`age_range`,
            SUM(`pos`) AS `pos`, 
            SUM(`neg`) AS `neg`
          FROM `national_age_breakdown` `n`
          LEFT JOIN `age_bands` `a` ON `a`.`ID` = `n`.`age_band_id`
          WHERE 1";
  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    IF (band_type = 1) THEN
        SET @QUERY = CONCAT(@QUERY, " GROUP BY `a`.`ID`  ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " GROUP BY `a`.`age_range_id`  ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_age_summary` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
  					SUM(`infantsless2m`) AS `infantsless2m`,       
					SUM(`infantsless2mPOS`) AS `infantsless2mPOS`,     
					SUM(`infantsless2w`) AS `infantsless2w`,       
					SUM(`infantsless2wPOS`) AS `infantsless2wPOS`,    
					SUM(`infants4to6w`) AS `infants4to6w`,        
					SUM(`infants4to6wPOS`) AS `infants4to6wPOS`,     
					SUM(`infantsabove2m`) AS `infantsabove2m`,      
					SUM(`infantsabove2mPOS`) AS `infantsabove2mPOS`,   
					SUM(`adults`) AS `adults`,              
					SUM(`adultsPOS`) AS `adultsPOS`            
					FROM `national_summary`
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_eid_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`pos`) AS `pos`,
        SUM(`neg`) AS `neg`,
        AVG(`medage`) AS `medage`,
        SUM(`alltests`) AS `alltests`,
        SUM(`eqatests`) AS `eqatests`,
        SUM(`firstdna`) AS `firstdna`,
        SUM(`confirmdna`) AS `confirmdna`,
        SUM(`confirmedPOS`) AS `confirmpos`,
        SUM(`repeatspos`) AS `repeatspos`,
        SUM(`pos`) AS `pos`,
        SUM(`neg`) AS `neg`,
        SUM(`repeatposPOS`) AS `repeatsposPOS`,
        SUM(`actualinfants`) AS `actualinfants`,
        SUM(`actualinfantsPOS`) AS `actualinfantspos`,
        SUM(`infantsless2m`) AS `infantsless2m`,
        SUM(`infantsless2mPOS`) AS `infantless2mpos`,
        SUM(`adults`) AS `adults`,
        SUM(`adultsPOS`) AS `adultsPOS`,
        SUM(`redraw`) AS `redraw`,
        SUM(`tests`) AS `tests`,
        SUM(`rejected`) AS `rejected`,  
        AVG(`sitessending`) AS `sitessending`";


    IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `national_summary` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `national_summary_yearly` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " WHERE 1 ");


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;
    

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_entryP_positivity` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
  						`p`.`name`,
                        SUM(`pos`) AS `pos`,
                        SUM(`neg`) AS `neg`
                    FROM `national_entrypoint` `ci` 
                    LEFT JOIN `entry_points` `p` ON `ci`.`entrypoint` = `p`.`ID`
                WHERE 1 ";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_entry_points` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `ep`.`name`, 
                        SUM(`pos`) AS `positive`, 
                        SUM(`neg`) AS `negative`  
                    FROM `national_entrypoint` `nep` 
                    JOIN `entry_points` `ep` 
                    ON `nep`.`entrypoint` = `ep`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ep`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_hei` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`enrolled`) AS `enrolled`,
        SUM(`dead`) AS `dead`,
        SUM(`ltfu`) AS `ltfu`,
        SUM(`adult`) AS `adult`,
        SUM(`transout`) AS `transout`,
        SUM(`other`) AS `other`
    FROM `national_summary`
    WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_hei_validation` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`validation_confirmedpos`) AS `Confirmed Positive`,
        SUM(`validation_repeattest`) AS `Repeat Test`,
        AVG(`validation_viralload`) AS `Viral Load`,
        SUM(`validation_adult`) AS `Adult`,
        SUM(`validation_unknownsite`) AS `Unknown Facility`,
        SUM(`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`) AS `followup_hei`, 
        sum(`actualinfantsPOS`) AS `positives`, 
        SUM(`actualinfants`-((`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`)-(`validation_repeattest`+`validation_unknownsite`+`validation_adult`+`validation_viralload`))) AS `true_tests` 
        FROM `national_summary`
    WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_infantsless2m` (IN `filter_year` INT(11), IN `filter_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `month`, 
                    `year`, 
                    `infantsless2m` 
                FROM `national_summary`
                WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_iprophylaxis` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `p`.`name`, 
                        SUM(`pos`) AS `positive`, 
                        SUM(`neg`) AS `negative` 
                    FROM `national_iprophylaxis` `nip` 
                    JOIN `prophylaxis` `p` ON `nip`.`prophylaxis` = `p`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_iproph_positivity` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
  						`p`.`name`,
                        SUM(`pos`) AS `pos`,
                        SUM(`neg`) AS `neg`
                    FROM `national_iprophylaxis` `ci` 
                    LEFT JOIN `prophylaxis` `p` ON `ci`.`prophylaxis` = `p`.`ID`
                WHERE 1 ";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_mprophylaxis` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `p`.`name`, 
                        SUM(`pos`) AS `positive`, 
                        SUM(`neg`) AS `negative` 
                    FROM `national_mprophylaxis` `nmp` 
                    JOIN `prophylaxis` `p` ON `nmp`.`prophylaxis` = `p`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_mproph_positivity` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
  						`p`.`name`,
                        SUM(`pos`) AS `pos`,
                        SUM(`neg`) AS `neg`
                    FROM `national_mprophylaxis` `ci` 
                    LEFT JOIN `prophylaxis` `p` ON `ci`.`prophylaxis` = `p`.`ID`
                WHERE 1 ";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_tat` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `vls`.`tat1`, 
                        `vls`.`tat2`, 
                        `vls`.`tat3`, 
                        `vls`.`tat4` 
                    FROM `national_summary` `vls` 
                    WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;
    

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_testing_trends` (IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
            `year`, 
            `month`, 
            `pos`, 
            `neg`,
            `rpos`, 
            `rneg`,
            `allpos`, 
            `allneg`

            FROM `national_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `year` = '",from_year,"' OR `year` = '",to_year,"' ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_yearly_hei` (IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`enrolled`) AS `enrolled`,
        SUM(`dead`) AS `dead`,
        SUM(`ltfu`) AS `ltfu`,
        SUM(`adult`) AS `adult`,
        SUM(`transout`) AS `transout`,
        SUM(`other`) AS `other`
    FROM `national_summary_yearly`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_yearly_hei_validation` (IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`validation_confirmedpos`) AS `Confirmed Positive`,
        SUM(`validation_repeattest`) AS `Repeat Test`,
        AVG(`validation_viralload`) AS `Viral Load`,
        SUM(`validation_adult`) AS `Adult`,
        SUM(`validation_unknownsite`) AS `Unknown Facility`,
        SUM(`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`) AS `followup_hei`, 
        sum(`actualinfantsPOS`) AS `positives`, 
        SUM(`actualinfants`-((`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`)-(`validation_repeattest`+`validation_unknownsite`+`validation_adult`+`validation_viralload`))) AS `true_tests` 
        FROM `national_summary_yearly`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_yearly_summary` ()  BEGIN
  SET @QUERY =    "SELECT 
                    `year`, 
                    SUM(`tests`) AS `tests`, 
                    SUM(`pos`) AS `positive`, 
                    SUM(`neg`) AS `negative`, 
                    SUM(`redraw`) AS `redraws` 
                  FROM `national_summary`  
                  WHERE `year` > '2007'
                  GROUP BY `year` ORDER BY `year` ASC";

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_national_yearly_tests` ()  BEGIN
  SET @QUERY =    "SELECT
                    `ns`.`year`, `ns`.`month`, SUM(`ns`.`firstdna`) AS `tests`, 
                    SUM(`ns`.`pos`) AS `positive`,
                    SUM(`ns`.`neg`) AS `negative`,
                    SUM(`ns`.`rejected`) AS `rejected`,
                    SUM(`ns`.`infantsless2m`) AS `infants`,
                    SUM(`ns`.`redraw`) AS `redraw`,
                    SUM(`ns`.`tat4`) AS `tat4`
                FROM `national_summary` `ns`
                WHERE 1 ";

      SET @QUERY = CONCAT(@QUERY, " GROUP BY `ns`.`month`, `ns`.`year` ");

     
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `ns`.`year` DESC, `ns`.`month` ASC ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_nat_partner_positivity` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `p`.`name`,
                    SUM(`actualinfantsPOS`) AS `pos`,
                    SUM(`actualinfants`-`actualinfantsPOS`) AS `neg`,
                    ((SUM(`actualinfantsPOS`)/(SUM(`actualinfants`)))*100) AS `pecentage` ";


     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `ip_summary` `is` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `ip_summary_yearly` `is` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN `partners` `p` 
                  ON `is`.`partner` = `p`.`ID` 
                WHERE 1 ");



   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_nat_subcounties_positivity` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `c`.`name`,
                    SUM(`actualinfantsPOS`) AS `pos`,
                    SUM(`actualinfants`-`actualinfantsPOS`) AS `neg`,
                    ((SUM(`actualinfantsPOS`)/(SUM(`actualinfants`)))*100) AS `pecentage` ";


     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary` `cs` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary_yearly` `cs` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN districts `c`
                    ON c.ID = cs.subcounty 
                WHERE 1 ");

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_age` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
            SUM(`sixweekspos`) AS `sixweekspos`, 
            SUM(`sixweeksneg`) AS `sixweeksneg`, 
            SUM(`sevento3mpos`) AS `sevento3mpos`, 
            SUM(`sevento3mneg`) AS `sevento3mneg`,
            SUM(`threemto9mpos`) AS `threemto9mpos`, 
            SUM(`threemto9mneg`) AS `threemto9mneg`,
            SUM(`ninemto18mpos`) AS `ninemto18mpos`, 
            SUM(`ninemto18mneg`) AS `ninemto18mneg`,
            SUM(`above18mpos`) AS `above18mpos`, 
            SUM(`above18mneg`) AS `above18mneg`,
            SUM(`nodatapos`) AS `nodatapos`, 
            SUM(`nodataneg`) AS `nodataneg`
          FROM `ip_agebreakdown` WHERE 1";
  

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_age_range` (IN `band_type` INT(11), IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
            `a`.`name` AS `age_band`,
            `a`.`age_range`,
            SUM(`pos`) AS `pos`, 
            SUM(`neg`) AS `neg`
          FROM `ip_age_breakdown` `n`
          LEFT JOIN `age_bands` `a` ON `a`.`ID` = `n`.`age_band_id`
          WHERE 1";
  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' ");

    IF (band_type = 1) THEN
        SET @QUERY = CONCAT(@QUERY, " GROUP BY `a`.`ID`  ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " GROUP BY `a`.`age_range_id`  ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_counties_details` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                      `c`.`name` AS `county`, 
                      `p`.`name` AS `partner`, 
                      SUM(`tests`) AS `tests`, 
                      SUM(`firstdna`) AS `firstdna`, 
                      SUM(`confirmdna` + `repeatspos`) AS `confirmdna`,
                      SUM(`pos`) AS `positive`, 
                      SUM(`neg`) AS `negative`, 
                      SUM(`redraw`) AS `redraw`, 
                      SUM(`adults`) AS `adults`, 
                      SUM(`adultsPOS`) AS `adultspos`, 
                      AVG(`medage`) AS `medage`, 
                      SUM(`rejected`) AS `rejected`, 
                      SUM(`infantsless2m`) AS `infantsless2m`, 
                      SUM(`infantsless2mPOS`) AS `infantsless2mpos`
                  FROM `county_summary` `cs`
                  JOIN `view_facilitys` `vf` ON `vf`.county = `cs`.county 
                  JOIN `countys` `c` ON `c`.ID = `cs`.county 
                  JOIN `partners` `p` ON `p`.ID = `vf`.partner  WHERE 1";



    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vf`.`partner` = '",P_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `c`.ID ORDER BY `tests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_county_details` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
                  `countys`.`name` AS `county`, 
                  COUNT(DISTINCT `view_facilitys`.`ID`) AS `facilities`, 
                  SUM(`alltests`) AS `alltests`, 
                  SUM(`actualinfants`) AS `actualinfants`, 
                   SUM(`pos`) AS `positive`, 
                  SUM(`neg`) AS `negative`, 
                  SUM(`repeatspos`) AS `repeatspos`,
                  SUM(`repeatposPOS`) AS `repeatsposPOS`,
                  SUM(`confirmdna`) AS `confirmdna`,
                  SUM(`confirmedPOS`) AS `confirmedPOS`,
                  SUM(`infantsless2w`) AS `infantsless2w`, 
                  SUM(`infantsless2wPOS`) AS `infantsless2wpos`, 
                  SUM(`infantsless2m`) AS `infantsless2m`, 
                  SUM(`infantsless2mPOS`) AS `infantsless2mpos`, 
                  SUM(`infantsabove2m`) AS `infantsabove2m`, 
                  SUM(`infantsabove2mPOS`) AS `infantsabove2mpos`,  
                  AVG(`medage`) AS `medage`,
                  SUM(`rejected`) AS `rejected` ";


    IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `site_summary` `ss` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `site_summary_yearly` `ss` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, "  
                  LEFT JOIN `view_facilitys` ON `ss`.`facility` = `view_facilitys`.`ID` 
                  LEFT JOIN `countys` ON `view_facilitys`.`county` = `countys`.`ID`  WHERE 1 ");


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",P_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`county` ORDER BY `tests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_eid_outcomes` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`pos`) AS `pos`,
        SUM(`neg`) AS `neg`,
        AVG(`medage`) AS `medage`,
        SUM(`alltests`) AS `alltests`,
        SUM(`eqatests`) AS `eqatests`,
        SUM(`firstdna`) AS `firstdna`,
        SUM(`confirmdna`) AS `confirmdna`,
        SUM(`confirmedPOS`) AS `confirmpos`,
        SUM(`repeatspos`) AS `repeatspos`,
        SUM(`pos`) AS `pos`,
        SUM(`neg`) AS `neg`,
        SUM(`repeatposPOS`) AS `repeatsposPOS`,
        SUM(`actualinfants`) AS `actualinfants`,
        SUM(`actualinfantsPOS`) AS `actualinfantspos`,
        SUM(`infantsless2m`) AS `infantsless2m`,
        SUM(`infantsless2mPOS`) AS `infantless2mpos`,
        SUM(`adults`) AS `adults`,
        SUM(`adultsPOS`) AS `adultsPOS`,
        SUM(`redraw`) AS `redraw`,
        SUM(`tests`) AS `tests`,
        SUM(`rejected`) AS `rejected`, 
        AVG(`sitessending`) AS `sitessending`";

    IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `ip_summary` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `ip_summary_yearly` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " WHERE 1 ");


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_entry_points` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `ep`.`name`, 
                        SUM(`pos`) AS `positive`, 
                        SUM(`neg`) AS `negative`  
                    FROM `ip_entrypoint` `nep` 
                    JOIN `entry_points` `ep` 
                    ON `nep`.`entrypoint` = `ep`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ep`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_hei` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`enrolled`) AS `enrolled`,
        SUM(`dead`) AS `dead`,
        SUM(`ltfu`) AS `ltfu`,
        SUM(`adult`) AS `adult`,
        SUM(`transout`) AS `transout`,
        SUM(`other`) AS `other`
    FROM `ip_summary`
    WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_hei_validation` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`validation_confirmedpos`) AS `Confirmed Positive`,
        SUM(`validation_repeattest`) AS `Repeat Test`,
        AVG(`validation_viralload`) AS `Viral Load`,
        SUM(`validation_adult`) AS `Adult`,
        SUM(`validation_unknownsite`) AS `Unknown Facility`,
        SUM(`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`) AS `followup_hei`, 
        sum(`actualinfantsPOS`) AS `positives`, 
        SUM(`actualinfants`-((`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`)-(`validation_repeattest`+`validation_unknownsite`+`validation_adult`+`validation_viralload`))) AS `true_tests`
    FROM `ip_summary`
    WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_iprophylaxis` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `p`.`name`, 
                        SUM(`pos`) AS `positive`, 
                        SUM(`neg`) AS `negative` 
                    FROM `ip_iprophylaxis` `nip` 
                    JOIN `prophylaxis` `p` ON `nip`.`prophylaxis` = `p`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_mprophylaxis` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `p`.`name`, 
                        SUM(`pos`) AS `positive`, 
                        SUM(`neg`) AS `negative` 
                    FROM `ip_mprophylaxis` `nmp` 
                    JOIN `prophylaxis` `p` ON `nmp`.`prophylaxis` = `p`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `p`.`name`,
                    SUM(`ps`.`pos`) AS `positive`,
                    SUM(`ps`.`neg`) AS `negative`";

    IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `ip_summary` `ps` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `ip_summary_yearly` `ps` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " JOIN `partners` `p` ON `ps`.`partner` = `p`.`ID`
                WHERE 1 ");

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ps`.`partner` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_performance` (IN `filter_partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `p`.`name`, `ip`.`month`, `ip`.`year`, SUM(`ip`.`tests`) AS `tests`, 
                    SUM(`ip`.`infantsless2m`) AS `infants`, 
                    SUM(`ip`.`pos`) AS `pos`,
                    SUM(`ip`.`neg`) AS `neg`, SUM(`ip`.`rejected`) AS `rej`
                FROM `ip_summary` `ip`
                JOIN `partners` `p`
                ON `p`.`ID` = `ip`.`partner` 
                WHERE 1 ";

    
        

         IF (filter_partner != 0 && filter_partner != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `p`.`ID` = '",filter_partner,"' ");
        END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ip`.`month`, `ip`.`year` ");
  
    SET @QUERY = CONCAT(@QUERY, " ORDER BY `ip`.`year` DESC, `ip`.`month` ASC ");


  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_sites_details` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                  `view_facilitys`.`facilitycode` AS `MFLCode`, 
                  `view_facilitys`.`name`, 
                  `view_facilitys`.`countyname` AS `county`, 
                  `view_facilitys`.`subcounty` AS `subcounty`, 
                  SUM(`alltests`) AS `alltests`, 
                  SUM(`actualinfants`) AS `actualinfants`, 
                   SUM(`pos`) AS `positive`, 
                  SUM(`neg`) AS `negative`, 
                  SUM(`repeatspos`) AS `repeatspos`,
                  SUM(`repeatposPOS`) AS `repeatsposPOS`,
                  SUM(`confirmdna`) AS `confirmdna`,
                  SUM(`confirmedPOS`) AS `confirmedPOS`,
                  SUM(`infantsless2w`) AS `infantsless2w`, 
                  SUM(`infantsless2wPOS`) AS `infantsless2wpos`, 
                  SUM(`infantsless2m`) AS `infantsless2m`, 
                  SUM(`infantsless2mPOS`) AS `infantsless2mpos`, 
                  SUM(`infantsabove2m`) AS `infantsabove2m`, 
                  SUM(`infantsabove2mPOS`) AS `infantsabove2mpos`,
                  SUM(`noage`) AS `noage`,
                  SUM(`adults`) AS `adults`,
                  AVG(`medage`) AS `medage`,
                  SUM(`rejected`) AS `rejected` ";
    IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `site_summary` ss ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `site_summary_yearly` ss ");
    END IF;
    SET @QUERY = CONCAT(@QUERY, " 
      LEFT JOIN `view_facilitys` ON `ss`.`facility` = `view_facilitys`.`ID` 
      WHERE 1 ");
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;
    SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",P_id,"' ");
    SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`ID` ORDER BY `tests` DESC ");
     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_sites_outcomes` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
					`vf`.`name`,
					SUM(`ss`.`pos`) AS `positive`,
          SUM(`ss`.`neg`) AS `negative`";


    IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `site_summary` ss ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `site_summary_yearly` ss ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " 
      LEFT JOIN `view_facilitys` `vf` ON `ss`.`facility` = `vf`.`ID` 
      LEFT JOIN `countys` ON `vf`.`county` = `countys`.`ID`  WHERE 1 ");


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vf`.`partner` = '",P_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vf`.`name` ORDER BY `negative` DESC, `positive` DESC LIMIT 0, 50 ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_supported_sites` (IN `P_id` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                      `view_facilitys`.`DHIScode` AS `DHIS Code`, 
                      `view_facilitys`.`facilitycode` AS `MFL Code`, 
                      `view_facilitys`.`name` AS `Facility`, 
                      `countys`.`name` AS `County` 
                  FROM `view_facilitys` 
                  LEFT JOIN `countys` 
                    ON `view_facilitys`.`county` = `countys`.`ID` 
                  WHERE 1 ";

    SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",P_id,"' ORDER BY `Facility` ASC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_testing_trends` (IN `P_id` INT(11), IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
            `year`, 
            `month`, 
            `pos`, 
            `neg`,
            `rpos`, 
            `rneg`,
            `allpos`, 
            `allneg` 
            FROM `ip_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `year` BETWEEN '",from_year,"' AND '",to_year,"' ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_yearly_hei` (IN `P_id` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`enrolled`) AS `enrolled`,
        SUM(`dead`) AS `dead`,
        SUM(`ltfu`) AS `ltfu`,
        SUM(`adult`) AS `adult`,
        SUM(`transout`) AS `transout`,
        SUM(`other`) AS `other`
    FROM `ip_summary_yearly`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `year` = '",filter_year,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_yearly_hei_validation` (IN `P_id` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`validation_confirmedpos`) AS `Confirmed Positive`,
        SUM(`validation_repeattest`) AS `Repeat Test`,
        AVG(`validation_viralload`) AS `Viral Load`,
        SUM(`validation_adult`) AS `Adult`,
        SUM(`validation_unknownsite`) AS `Unknown Facility`,
        SUM(`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`) AS `followup_hei`, 
        sum(`actualinfantsPOS`) AS `positives`, 
        SUM(`actualinfants`-((`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`)-(`validation_repeattest`+`validation_unknownsite`+`validation_adult`+`validation_viralload`))) AS `true_tests`
    FROM `ip_summary_yearly`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `year` = '",filter_year,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_partner_year_summary` (IN `filter_partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `ip`.`year`,  SUM(`ip`.`pos`) AS `positive`,
                    SUM(`ip`.`neg`) AS `negative`,
                    SUM(`ip`.`redraw`) AS `redraws`
                FROM `ip_summary` `ip`
                JOIN `partners` `p`
                ON `p`.`ID` = `ip`.`partner` 
                WHERE 1 ";

         IF (filter_partner != 0 && filter_partner != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `p`.`ID` = '",filter_partner,"' ");
        END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ip`.`year` ");
  
    SET @QUERY = CONCAT(@QUERY, " ORDER BY `ip`.`year` ASC ");


  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_rht_facility_outcomes` (IN `filter_county` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11), IN `filter_result` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    DISTINCT(`rs`.`facility`) AS `facility`,
                    `vf`.`name` AS `name`,
                    COUNT(*) AS `tests` 
                  FROM `rht_samples` `rs` 
                  LEFT JOIN `view_facilitys` `vf` 
                    ON `rs`.`facility` = `vf`.`ID`
    WHERE 1";

    IF (filter_county != 0 && filter_county != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",filter_county,"' ");
    END IF;


    IF (filter_result != 0 && filter_result != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `rs`.`result` = '",filter_result,"' ");
    END IF;

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
        SET @QUERY = CONCAT(@QUERY, " AND YEAR(`datetested`) = '",filter_year,"' AND MONTH(`datetested`) BETWEEN '",from_month,"' AND '",to_month,"' ");
      ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
        SET @QUERY = CONCAT(@QUERY, " AND ((YEAR(`datetested`) = '",filter_year,"' AND MONTH(`datetested`) >= '",from_month,"')  OR (YEAR(`datetested`) = '",to_year,"' AND MONTH(`datetested`) <= '",to_month,"') OR (YEAR(`datetested`) > '",filter_year,"' AND YEAR(`datetested`) < '",to_year,"')) ");
      ELSE
        SET @QUERY = CONCAT(@QUERY, " AND YEAR(`datetested`) = '",filter_year,"' AND MONTH(`datetested`)='",from_month,"' ");
      END IF;
    END IF;
    ELSE
      SET @QUERY = CONCAT(@QUERY, " AND YEAR(`datetested`) = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `rs`.`facility` ORDER BY `tests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_rht_outcomes` (IN `filter_county` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11), IN `filter_result` INT(11), IN `filter_gender` VARCHAR(11))  BEGIN
  SET @QUERY =    "SELECT 
                    COUNT(*) AS `tests` 
                  FROM `rht_samples` `rs` 
                  LEFT JOIN `view_facilitys` `vf` 
                    ON `rs`.`facility` = `vf`.`ID`
    WHERE 1";

    IF (filter_county != 0 && filter_county != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",filter_county,"' ");
    END IF;

    IF (filter_gender != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `rs`.`gender` = '",filter_gender,"' ");
    END IF;

    IF (filter_result != 0 && filter_result != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `rs`.`result` = '",filter_result,"' ");
    END IF;

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
        SET @QUERY = CONCAT(@QUERY, " AND YEAR(`datetested`) = '",filter_year,"' AND MONTH(`datetested`) BETWEEN '",from_month,"' AND '",to_month,"' ");
      ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
        SET @QUERY = CONCAT(@QUERY, " AND ((YEAR(`datetested`) = '",filter_year,"' AND MONTH(`datetested`) >= '",from_month,"')  OR (YEAR(`datetested`) = '",to_year,"' AND MONTH(`datetested`) <= '",to_month,"') OR (YEAR(`datetested`) > '",filter_year,"' AND YEAR(`datetested`) < '",to_year,"')) ");
      ELSE
        SET @QUERY = CONCAT(@QUERY, " AND YEAR(`datetested`) = '",filter_year,"' AND MONTH(`datetested`)='",from_month,"' ");
      END IF;
    END IF;
    ELSE
      SET @QUERY = CONCAT(@QUERY, " AND YEAR(`datetested`) = '",filter_year,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_rht_pos_trend` (IN `filter_county` INT(11), IN `filter_year` INT(11), IN `filter_result` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    MONTH(`rs`.`datetested`) AS `month`, 
                    COUNT(*) AS `tests`
                  FROM `rht_samples` `rs` 
                  LEFT JOIN `view_facilitys` `vf` 
                    ON `rs`.`facility` = `vf`.`ID`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND YEAR(`datetested`) = '",filter_year,"' ");

    
    IF (filter_result != 0 && filter_result != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `rs`.`result` = '",filter_result,"' ");
    END IF;

    IF (filter_county != 0 && filter_county != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",filter_county,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `month` ORDER BY `month` ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_rht_yearly_trend` (IN `filter_county` INT(11), IN `filter_result` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
                    YEAR(`rs`.`datetested`) AS `year`, 
                    MONTH(`rs`.`datetested`) AS `month`,
                    COUNT(*) AS `tests`
                  FROM `rht_samples` `rs` 
                  LEFT JOIN `view_facilitys` `vf` 
                    ON `rs`.`facility` = `vf`.`ID`
    WHERE 1";

    
    IF (filter_result != 0 && filter_result != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `rs`.`result` = '",filter_result,"' ");
    END IF;

    IF (filter_county != 0 && filter_county != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",filter_county,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `month`, `year` ORDER BY `year` DESC, `month` ASC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_sites_eid` (IN `filter_site` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM(`pos`) AS `pos`,
                    SUM(`neg`) AS `neg`,
                    AVG(`medage`) AS `medage`,
                    SUM(`alltests`) AS `alltests`,
                    SUM(`eqatests`) AS `eqatests`,
                    SUM(`firstdna`) AS `firstdna`,
                    SUM(`confirmdna`) AS `confirmdna`,
                    SUM(`confirmedPOS`) AS `confirmpos`,
                    SUM(`repeatspos`) AS `repeatspos`,
                    SUM(`repeatposPOS`) AS `repeatsposPOS`,
                    SUM(`actualinfants`) AS `actualinfants`,
                    SUM(`actualinfantsPOS`) AS `actualinfantspos`,
                    SUM(`infantsless2m`) AS `infantsless2m`,
                    SUM(`infantsless2mPOS`) AS `infantless2mpos`,
                    SUM(`adults`) AS `adults`,
                    SUM(`adultsPOS`) AS `adultsPOS`,
                    SUM(`redraw`) AS `redraw`,
                    SUM(`tests`) AS `tests`,
                    SUM(`rejected`) AS `rejected`, 
                    AVG(`sitessending`) AS `sitessending`";


    IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `site_summary` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `site_summary_yearly` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " WHERE 1 ");


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",filter_site,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `facility`");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_sites_hei_follow_up` (IN `filter_site` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM((`ss`.`enrolled`)) AS `enrolled`, 
                    SUM(`ss`.`dead`) AS `dead`, 
                    SUM(`ss`.`ltfu`) AS `ltfu`, 
                    SUM(`ss`.`transout`) AS `transout`, 
                    SUM(`ss`.`adult`) AS `adult`, 
                    SUM(`ss`.`other`) AS `other` 
                  FROM `site_summary` `ss` 
    WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",filter_site,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ss`.`facility` ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_sites_positivity` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						            SUM(`vss`.`actualinfantsPOS`) AS `pos`, 
                        SUM(`actualinfants`-`actualinfantsPOS`) AS `neg`,
                        SUM(`vss`.`actualinfants`) AS `alltests`,
                        ((SUM(`vss`.`actualinfantsPOS`)/SUM(`vss`.`actualinfants`))*100) AS `positivity`, 
                        `vf`.`ID`, 
                        `vf`.`name` ";



     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `site_summary` `vss` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `site_summary_yearly` `vss` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN `view_facilitys` `vf` 
                    ON `vss`.`facility`=`vf`.`ID` 
                    WHERE 1 ");

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vf`.`ID` ORDER BY `positivity` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_sites_trends` (IN `filter_site` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `ss`.`month`, 
                    SUM((`ss`.`actualinfantsPOS`)) AS `pos`, 
                    SUM(`ss`.`actualinfants`-`ss`.`actualinfantsPOS`) AS `neg`,
                    SUM(`ss`.`pos`+`ss`.`neg`) AS `tests`,
                    SUM(`ss`.`rejected`) AS `rejected`
                  FROM `site_summary` `ss` 
                  LEFT JOIN `view_facilitys` `vf` 
                    ON `ss`.`facility` = `vf`.`ID`
    WHERE 1";

    IF (filter_site != 0 && filter_site != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `facility`='",filter_site,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ss`.`month` ORDER BY `ss`.`month` ASC  ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_sites_yearly_hei_follow_up` (IN `filter_site` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM((`ss`.`enrolled`)) AS `enrolled`, 
                    SUM(`ss`.`dead`) AS `dead`, 
                    SUM(`ss`.`ltfu`) AS `ltfu`, 
                    SUM(`ss`.`transout`) AS `transout`, 
                    SUM(`ss`.`adult`) AS `adult`, 
                    SUM(`ss`.`other`) AS `other` 
                  FROM `site_summary_yearly` `ss` 
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",filter_site,"' AND `year` = '",filter_year,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ss`.`facility` ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_site_hei_validation` (IN `filter_site` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
       SUM(`validation_confirmedpos`) AS `Confirmed Positive`,
        SUM(`validation_repeattest`) AS `Repeat Test`,
        AVG(`validation_viralload`) AS `Viral Load`,
        SUM(`validation_adult`) AS `Adult`,
        SUM(`validation_unknownsite`) AS `Unknown Facility`,
        SUM(`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`) AS `followup_hei`, 
        sum(`actualinfantsPOS`) AS `positives`, 
        SUM(`actualinfants`-((`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`)-(`validation_repeattest`+`validation_unknownsite`+`validation_adult`+`validation_viralload`))) AS `true_tests`  
                  FROM `site_summary` 
            WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",filter_site,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_site_yearly_hei_validation` (IN `filter_site` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    SUM(`validation_confirmedpos`) AS `Confirmed Positive`,
                    SUM(`validation_repeattest`) AS `Repeat Test`,
                    AVG(`validation_viralload`) AS `Viral Load`,
                    SUM(`validation_adult`) AS `Adult`,
                    SUM(`validation_unknownsite`) AS `Unknown Facility`,
                    SUM(`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`) AS `followup_hei`, 
                    sum(`actualinfantsPOS`) AS `positives`, 
                    SUM(`actualinfants`-((`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`)-(`validation_repeattest`+`validation_unknownsite`+`validation_adult`+`validation_viralload`))) AS `true_tests`  
                  FROM `site_summary_yearly` 
            WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",filter_site,"' AND `year` = '",filter_year,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_age` (IN `SC_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
            SUM(`sixweekspos`) AS `sixweekspos`, 
            SUM(`sixweeksneg`) AS `sixweeksneg`, 
            SUM(`sevento3mpos`) AS `sevento3mpos`, 
            SUM(`sevento3mneg`) AS `sevento3mneg`,
            SUM(`threemto9mpos`) AS `threemto9mpos`, 
            SUM(`threemto9mneg`) AS `threemto9mneg`,
            SUM(`ninemto18mpos`) AS `ninemto18mpos`, 
            SUM(`ninemto18mneg`) AS `ninemto18mneg`,
            SUM(`above18mpos`) AS `above18mpos`, 
            SUM(`above18mneg`) AS `above18mneg`,
            SUM(`nodatapos`) AS `nodatapos`, 
            SUM(`nodataneg`) AS `nodataneg`
          FROM `subcounty_agebreakdown` WHERE 1";
 

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_id,"' ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_age_breakdown` (IN `SC_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM(`less2wpos`) AS `less2wpos`,
                    SUM(`less2wneg`) AS `less2wneg`,
                    SUM(`twoto6wpos`) AS `twoto6wpos`,
                    SUM(`twoto6wneg`) AS `twoto6wneg`,
                    SUM(`sixto8wpos`) AS `sixto8wpos`,
                    SUM(`sixto8wneg`) AS `sixto8wneg`,
                    SUM(`sixmonthpos`) AS `sixmonthpos`,
                    SUM(`sixmonthneg`) AS `sixmonthneg`,
                    SUM(`ninemonthpos`) AS `ninemonthpos`,
                    SUM(`ninemonthneg`) AS `ninemonthneg`,
                    SUM(`twelvemonthpos`) AS `twelvemonthpos`,
                    SUM(`twelvemonthneg`) AS `twelvemonthneg`
                    FROM subcounty_agebreakdown
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_age_range` (IN `band_type` INT(11), IN `Subcounty_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
            `a`.`name` AS `age_band`,
            `a`.`age_range`,
            SUM(`pos`) AS `pos`, 
            SUM(`neg`) AS `neg`
          FROM `subcounty_age_breakdown` `n`
          LEFT JOIN `age_bands` `a` ON `a`.`ID` = `n`.`age_band_id`
          WHERE 1";
  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",Subcounty_id,"' ");

    IF (band_type = 1) THEN
        SET @QUERY = CONCAT(@QUERY, " GROUP BY `a`.`ID`  ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " GROUP BY `a`.`age_range_id`  ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;

END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_age_summary` (IN `SC_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
  					SUM(`infantsless2m`) AS `infantsless2m`,       
					SUM(`infantsless2mPOS`) AS `infantsless2mPOS`,     
					SUM(`infantsless2w`) AS `infantsless2w`,       
					SUM(`infantsless2wPOS`) AS `infantsless2wPOS`,    
					SUM(`infants4to6w`) AS `infants4to6w`,        
					SUM(`infants4to6wPOS`) AS `infants4to6wPOS`,     
					SUM(`infantsabove2m`) AS `infantsabove2m`,      
					SUM(`infantsabove2mPOS`) AS `infantsabove2mPOS`,   
					SUM(`adults`) AS `adults`,              
					SUM(`adultsPOS`) AS `adultsPOS`            
					FROM `subcounty_summary`
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_eid` (IN `filter_subcounty` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM(`pos`) AS `pos`,
                    SUM(`neg`) AS `neg`,
                    AVG(`medage`) AS `medage`,
                    SUM(`alltests`) AS `alltests`,
                    SUM(`eqatests`) AS `eqatests`,
                    SUM(`firstdna`) AS `firstdna`,
                    SUM(`confirmdna`) AS `confirmdna`,
                    SUM(`confirmedPOS`) AS `confirmpos`,
                    SUM(`repeatspos`) AS `repeatspos`,
                    SUM(`repeatposPOS`) AS `repeatsposPOS`,
                    SUM(`actualinfants`) AS `actualinfants`,
                    SUM(`actualinfantsPOS`) AS `actualinfantspos`,
                    SUM(`infantsless2m`) AS `infantsless2m`,
                    SUM(`infantsless2mPOS`) AS `infantless2mpos`,
                    SUM(`adults`) AS `adults`,
                    SUM(`adultsPOS`) AS `adultsPOS`,
                    SUM(`redraw`) AS `redraw`,
                    SUM(`tests`) AS `tests`,
                    SUM(`rejected`) AS `rejected`,
                    AVG(`sitessending`) AS `sitessending` ";


     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary` `scs` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary_yearly` `scs` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " WHERE 1 ");



    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",filter_subcounty,"' ");


     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_hei_follow_up` (IN `filter_subcounty` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM((`ss`.`enrolled`)) AS `enrolled`, 
                    SUM(`ss`.`dead`) AS `dead`, 
                    SUM(`ss`.`ltfu`) AS `ltfu`, 
                    SUM(`ss`.`transout`) AS `transout`, 
                    SUM(`ss`.`adult`) AS `adult`, 
                    SUM(`ss`.`other`) AS `other` 
                  FROM `subcounty_summary` `ss` 
    WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",filter_subcounty,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_hei_validation` (IN `filter_subcounty` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`validation_confirmedpos`) AS `Confirmed Positive`,
        SUM(`validation_repeattest`) AS `Repeat Test`,
        AVG(`validation_viralload`) AS `Viral Load`,
        SUM(`validation_adult`) AS `Adult`,
        SUM(`validation_unknownsite`) AS `Unknown Facility`,
        SUM(`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`) AS `followup_hei`, 
        sum(`actualinfantsPOS`) AS `positives`, 
        SUM(`actualinfants`-((`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`)-(`validation_repeattest`+`validation_unknownsite`+`validation_adult`+`validation_viralload`))) AS `true_tests` 
                  FROM `subcounty_summary` 
    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",filter_subcounty,"' ");


     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_outcomes` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `d`.`name`,
                    `scs`.`subcounty`,
                    SUM(`scs`.`actualinfantsPOS`) AS `positive`,
                    SUM(`scs`.`actualinfants`-`scs`.`actualinfantsPOS`) AS `negative` 
                ";


     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary` `scs` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary_yearly` `scs` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " JOIN `districts` `d` ON `scs`.`subcounty` = `d`.`ID` 
                JOIN `countys` `c` ON `d`.`county` = `c`.`ID`
    WHERE 1 ");

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `c`.`ID` = '",C_id,"' ");



    SET @QUERY = CONCAT(@QUERY, " GROUP BY `scs`.`subcounty` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_sites_details` (IN `SC_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                  `view_facilitys`.`facilitycode` AS `MFLCode`, 
                  `view_facilitys`.`name`, 
                  `countys`.`name` AS `county`, 
                  `districts`.`name` AS `subcounty`, 
                  SUM(`alltests`) AS `alltests`, 
                  SUM(`actualinfants`) AS `actualinfants`, 
                  SUM(`pos`) AS `positive`, 
                  SUM(`neg`) AS `negative`, 
                  SUM(`repeatspos`) AS `repeatspos`,
                  SUM(`repeatposPOS`) AS `repeatsposPOS`,
                  SUM(`confirmdna`) AS `confirmdna`,
                  SUM(`confirmedPOS`) AS `confirmedPOS`,
                  SUM(`infantsless2w`) AS `infantsless2w`, 
                  SUM(`infantsless2wPOS`) AS `infantsless2wpos`, 
                  SUM(`infantsless2m`) AS `infantsless2m`, 
                  SUM(`infantsless2mPOS`) AS `infantsless2mpos`, 
                  SUM(`infantsabove2m`) AS `infantsabove2m`, 
                  SUM(`infantsabove2mPOS`) AS `infantsabove2mpos`,  
                  AVG(`medage`) AS `medage`,
                  SUM(`rejected`) AS `rejected`
                  FROM `site_summary`
                  LEFT JOIN `view_facilitys` ON `site_summary`.`facility` = `view_facilitys`.`ID`
                  JOIN `districts` ON  `view_facilitys`.`district` = `districts`.`ID` 
                  LEFT JOIN `countys` ON `view_facilitys`.`county` = `countys`.`ID`  WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`district` = '",SC_id,"' ");



    SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`ID` ORDER BY `tests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_yearly_hei_follow_up` (IN `filter_subcounty` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM((`ss`.`enrolled`)) AS `enrolled`, 
                    SUM(`ss`.`dead`) AS `dead`, 
                    SUM(`ss`.`ltfu`) AS `ltfu`, 
                    SUM(`ss`.`transout`) AS `transout`, 
                    SUM(`ss`.`adult`) AS `adult`, 
                    SUM(`ss`.`other`) AS `other` 
                  FROM `subcounty_summary_yearly` `ss` 
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",filter_subcounty,"' AND `year` = '",filter_year,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_subcounty_yearly_hei_validation` (IN `filter_subcounty` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    SUM(`validation_confirmedpos`) AS `Confirmed Positive`,
                    SUM(`validation_repeattest`) AS `Repeat Test`,
                    AVG(`validation_viralload`) AS `Viral Load`,
                    SUM(`validation_adult`) AS `Adult`,
                    SUM(`validation_unknownsite`) AS `Unknown Facility`,
                    SUM(`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`) AS `followup_hei`, 
                    sum(`actualinfantsPOS`) AS `positives`, 
                    SUM(`actualinfants`-((`enrolled`+`ltfu`+`adult`+`transout`+`dead`+`other`)-(`validation_repeattest`+`validation_unknownsite`+`validation_adult`+`validation_viralload`))) AS `true_tests` 
                  FROM `subcounty_summary_yearly` 
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",filter_subcounty,"' AND `year` = '",filter_year,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_top_subcounty_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `d`.`name`,
                    `scs`.`subcounty`,
                    SUM(`scs`.`actualinfantsPOS`) AS `positive`,
                    SUM(`scs`.`actualinfants`-`scs`.`actualinfantsPOS`) AS `negative` ";


     IF (from_month != 0 && from_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary` `scs` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `subcounty_summary_yearly` `scs` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " JOIN `districts` `d` ON `scs`.`subcounty` = `d`.`ID`
    WHERE 1 ");



    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;



    SET @QUERY = CONCAT(@QUERY, " GROUP BY `scs`.`subcounty` ORDER BY `negative` DESC, `positive` DESC ");
    SET @QUERY = CONCAT(@QUERY, " LIMIT 50 ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_unsupported_facilities` ()  BEGIN
  SET @QUERY =    "SELECT  
            `vf`.`facilitycode`, `vf`.`name`, `vf`.`DHIScode`,
            `d`.`name` AS `subcounty`, `c`.`name` AS `county`
          FROM `view_facilitys` `vf` 
          JOIN `districts` `d` ON `vf`.`district` = `d`.`ID`
          JOIN `countys` `c` ON `vf`.`county` = `c`.`ID`
          WHERE 1 AND `partner`=0 ";
  
    
    SET @QUERY = CONCAT(@QUERY, " ORDER BY `vf`.`name` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_yearly_lab_summary` (IN `lab` INT(11), IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `ls`.`year`,  
                    `ls`.`month`,  
                    `ls`.`neg`, 
                    `ls`.`pos`, 
                    `ls`.`redraw`
                FROM `lab_summary` `ls`
                WHERE 1 ";

      IF (lab != 0 && lab != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `ls`.`lab` = '",lab,"' ");
      END IF; 

      SET @QUERY = CONCAT(@QUERY, "  AND `year` BETWEEN '",from_year,"' AND '",to_year,"' ORDER BY `year` ASC, `month`  ");
    

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_yearly_lab_tests` (IN `lab` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `ls`.`year`, `ls`.`month`, SUM(`ls`.`tests`) AS `tests`, 
                    SUM(`ls`.`pos`) AS `positive`,
                    SUM(`ls`.`neg`) AS `negative`,
                    SUM(`ls`.`rejected`) AS `rejected`,
                    SUM(`ls`.`tat4`) AS `tat4`
                FROM `lab_summary` `ls`
                WHERE 1 ";

      IF (lab != 0 && lab != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `ls`.`lab` = '",lab,"' ");
      END IF;  

    
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `ls`.`month`, `ls`.`year` ");

     
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `ls`.`year` DESC, `ls`.`month` ASC ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_yearly_summary` (IN `county` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `cs`.`year`,  
                    SUM(`cs`.`neg`) AS `negative`, 
                    SUM(`cs`.`pos`) AS `positive`, 
                    SUM(`cs`.`redraw`) AS `redraws`
                FROM `county_summary` `cs`
                WHERE 1 ";

      IF (county != 0 && county != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `cs`.`county` = '",county,"' ");
      END IF; 
    
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `cs`.`year` ");
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `cs`.`year` ASC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_eid_yearly_tests` (IN `county` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `cs`.`year`, `cs`.`month`, SUM(`cs`.`firstdna`) AS `tests`, 
                    SUM(`cs`.`pos`) AS `positive`,
                    SUM(`cs`.`neg`) AS `negative`,
                    SUM(`cs`.`rejected`) AS `rejected`,
                    SUM(`cs`.`infantsless2m`) AS `infants`,
                    SUM(`cs`.`redraw`) AS `redraw`,
                    SUM(`cs`.`tat4`) AS `tat4`
                FROM `county_summary` `cs`
                WHERE 1 ";

      IF (county != 0 && county != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `cs`.`county` = '",county,"' ");
      END IF;  

    
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `cs`.`month`, `cs`.`year` ");

     
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `cs`.`year` DESC, `cs`.`month` ASC ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_labs_sampletypes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `lb`.`labname`, 
                    SUM(`vls`.`dbs`) AS `dbs`, 
                    SUM(`vls`.`plasma`) AS `plasma`, 
                    SUM(`vls`.`edta`) AS `edta`, 
                    `vls`.`year` 
                FROM `vl_lab_summary` `vls` 
                JOIN `labs` `lb` 
                    ON `vls`.`lab` = `lb`.`ID`
                WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;



    SET @QUERY = CONCAT(@QUERY, " GROUP BY `lb`.`labname` ORDER BY SUM(`dbs`+`plasma`+`edta`) DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_labs_tat` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `lb`.`labname`, 
                        `vls`.`tat1`, 
                        `vls`.`tat2`, 
                        `vls`.`tat3`, 
                        `vls`.`tat4` 
                    FROM `vl_lab_summary` `vls` 
                    JOIN `labs` `lb` 
                        ON `vls`.`lab` = `lb`.`ID` WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `lb`.`labname`, `vls`.`month` ASC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_labs_testing_trends` (IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `lb`.`labname`, 
                    `vls`.`alltests`,
                    `vls`.`eqa`,
                    `vls`.`confirmtx`, 
                    `vls`.`rejected`, 
                    `vls`.`received`, 
                    `vls`.`month`, 
                    `vls`.`year` 
                FROM `vl_lab_summary` `vls` 
                JOIN `labs` `lb` 
                    ON `vls`.`lab` = `lb`.`ID`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `vls`.`year` = '",filter_year,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_lab_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `l`.`labname`,
                    SUM((`vcs`.`Undetected`+`vcs`.`less1000`)) AS `detectableNless1000`,
                    SUM(`vcs`.`sustxfail`) AS `sustxfl`
                FROM `vl_lab_summary` `vcs` JOIN `labs` `l` ON `vcs`.`lab` = `l`.`ID` WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vcs`.`lab` ORDER BY `detectableNless1000` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_lab_tat` (IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `l`.`ID`, `l`.`labname` AS `name`, AVG(`ls`.`tat1`) AS `tat1`,
                    AVG(`ls`.`tat2`) AS `tat2`, AVG(`ls`.`tat3`) AS `tat3`,
                    AVG(`ls`.`tat4`) AS `tat4`
                FROM `lab_summary` `ls`
                JOIN `labs` `l`
                ON `l`.`ID` = `ls`.`lab` 
                WHERE 1 ";

       

        IF (filter_month != 0 && filter_month != '') THEN
           SET @QUERY = CONCAT(@QUERY, "  AND `ls`.`year` = '",filter_year,"' AND `ls`.`month`='",filter_month,"' ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `ls`.`year` = '",filter_year,"' ");
        END IF;
      

  SET @QUERY = CONCAT(@QUERY, " GROUP BY `l`.`ID` ");
    SET @QUERY = CONCAT(@QUERY, " ORDER BY `l`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_age` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `ac`.`name`, 
                    SUM(`vna`.`tests`) AS `agegroups`, 
                    SUM(`vna`.`undetected`+`vna`.`less1000`) AS `suppressed`,
                    SUM(`vna`.`less5000`+`vna`.`above5000`) AS `nonsuppressed`
                FROM `vl_national_age` `vna`
                JOIN `agecategory` `ac`
                    ON `vna`.`age` = `ac`.`ID`
                WHERE 1";

    
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ac`.`ID` ORDER BY `ac`.`ID` ASC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_entry_points` (IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`ep`.`name`, 
						SUM(`pos`) AS `positive`, 
						SUM(`neg`) AS `negative`  
					FROM `national_entrypoint` `nep` 
					JOIN `entry_points` `ep` 
					ON `nep`.`entrypoint` = `ep`.`ID`
                WHERE 1";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `nep`.`year` = '",filter_year,"' AND `nep`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `nep`.`year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ep`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_gender` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `g`.`name`,
                    SUM(`vng`.`undetected`+`vng`.`less1000`) AS `suppressed`,
                    SUM(`vng`.`less5000`+`vng`.`above5000`) AS `nonsuppressed`
                FROM `vl_national_gender` `vng`
                JOIN `gender` `g`
                    ON `vng`.`gender` = `g`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `g`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_iprophylaxis` (IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`p`.`name`, 
						SUM(`pos`) AS `positive`, 
						SUM(`neg`) AS `negative` 
					FROM `national_iprophylaxis` `nip` 
					JOIN `prophylaxis` `p` ON `nip`.`prophylaxis` = `p`.`ID`
                WHERE 1";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `nip`.`year` = '",filter_year,"' AND `nip`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `nip`.`year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_justification` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vj`.`name`,
                    SUM((`vnj`.`tests`)) AS `justifications`
                FROM `vl_national_justification` `vnj`
                JOIN `viraljustifications` `vj` 
                    ON `vnj`.`justification` = `vj`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vj`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_justification_breakdown` (IN `justification` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        SUM(`Undetected`) AS `Undetected`,
                        SUM(`less1000`) AS `less1000`, 
                        SUM(`less5000`) AS `less5000`,
                        SUM(`above5000`) AS `above5000`
                    FROM `vl_national_justification` 
                    WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `justification` = '",justification,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_mprophylaxis` (IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`p`.`name`, 
						SUM(`pos`) AS `positive`, 
						SUM(`neg`) AS `negative` 
					FROM `national_mprophylaxis` `nmp` 
					JOIN `prophylaxis` `p` ON `nmp`.`prophylaxis` = `p`.`ID`
                WHERE 1";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `nmp`.`year` = '",filter_year,"' AND `nmp`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `nmp`.`year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_positivity_notification` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                        SUM(`actualinfantsPOS`) AS `positive`, 
                        ((SUM(`actualinfantsPOS`)/SUM(`actualinfants`))*100) AS `positivity_rate` 
                    FROM `national_summary`
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_positivity_yearly_notification` (IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                        SUM(`actualinfantsPOS`) AS `positive`, 
                        ((SUM(`actualinfantsPOS`)/SUM(`actualinfants`))*100) AS `positivity_rate` 
                    FROM `national_summary_yearly`
                WHERE 1 ";

    SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sample_types` (IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
					`month`,
					`year`,
					sum(`edta`) as edta,
					sum(`dbs`) as dbs,
					sum(`plasma`) as plasma,
					sum(`alledta`) as alledta,
					sum(`alldbs`) as alldbs,
					sum(`allplasma`) as allplasma,
					sum((`Undetected`+`less1000`)) AS `suppressed`,
					sum((`Undetected`+`less1000`+`less5000`+`above5000`)) AS `tests`,
					sum(((`Undetected`+`less1000`)*100/(`Undetected`+`less1000`+`less5000`+`above5000`))) AS `suppression`
				FROM `vl_national_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `year` = '",from_year,"' OR `year`='",to_year,"' ");
    SET @QUERY = CONCAT(@QUERY, " GROUP BY  `month`, `year` ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sitessending` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
        `sitessending`
    FROM `vl_national_summary`
    WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sustxfail_age` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `ac`.`name`, 
                        SUM(`vna`.`sustxfail`) AS `sustxfail` 
                    FROM `vl_national_age` `vna` 
                    JOIN `agecategory` `ac` 
                        ON `vna`.`age` = `ac`.`subID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ac`.`name` ORDER BY `ac`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sustxfail_gender` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `gn`.`name`, 
                        SUM(`vng`.`sustxfail`) AS `sustxfail` 
                    FROM `vl_national_gender` `vng` 
                    JOIN `gender` `gn` 
                        ON `vng`.`gender` = `gn`.`ID`
                WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " GROUP BY `gn`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sustxfail_justification` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `vj`.`name`,
                        SUM(`vnj`.`sustxfail`) AS `sustxfail`
                    FROM `vl_national_justification` `vnj`
                    JOIN `viraljustifications` `vj`
                        ON `vnj`.`justification` = `vj`.`ID`
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vj`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sustxfail_notification` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                        SUM(`sustxfail`) AS `sustxfail`, 
                        ((SUM(`sustxfail`)/SUM(`alltests`))*100) AS `sustxfail_rate` 
                    FROM `vl_national_summary`
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sustxfail_partner` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                      `p`.`name`, 
                      ((SUM(`vps`.`sustxfail`)/SUM(`vps`.`alltests`))*100) AS `percentages`, 
                      SUM(`vps`.`sustxfail`) AS `sustxfail` 
                  FROM `vl_partner_summary` `vps` 
                  JOIN `partners` `p` 
                  ON `vps`.`partner` = `p`.`ID`
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`name` ORDER BY `percentages` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sustxfail_rank_regimen` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT `vp`.`name`, SUM(`vnr`.`sustxfail`) AS `sustxfail`
                    FROM `vl_national_regimen` `vnr`
                    JOIN `viralprophylaxis` `vp`
                        ON `vnr`.`regimen` = `vp`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, "  GROUP BY `vp`.`name` ORDER BY `sustxfail` DESC LIMIT 0, 5");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sustxfail_regimen` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT `vp`.`name`, SUM(`vnr`.`sustxfail`) AS `sustxfail`
                    FROM `vl_national_regimen` `vnr`
                    JOIN `viralprophylaxis` `vp`
                        ON `vnr`.`regimen` = `vp`.`ID`
                WHERE 1";

    

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, "  GROUP BY `vp`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sustxfail_sampletypes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `vsd`.`name`, 
                        SUM(`vns`.`sustxfail`) AS `sustxfail` 
                    FROM `vl_national_sampletype` `vns` 
                    JOIN `viralsampletypedetails` `vsd` 
                        ON `vns`.`sampletype` = `vsd`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vsd`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_sustxfail_subcounty` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                      `d`.`name`, 
                      ((SUM(`vss`.`sustxfail`)/SUM(`vss`.`alltests`))*100) AS `percentages`, 
                      SUM(`vss`.`sustxfail`) AS `sustxfail` 
                  FROM `vl_subcounty_summary` `vss` 
                  JOIN `districts` `d` 
                  ON `vss`.`subcounty` = `d`.`ID`
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `d`.`name` ORDER BY `percentages` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_tat` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `vns`.`tat1`, 
                        `vns`.`tat2`, 
                        `vns`.`tat3`, 
                        `vns`.`tat4` 
                    FROM `vl_national_summary` `vns` 
                    WHERE 1";

     IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_testing_trends` (IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
            `year`, 
            `month`, 
            `pos`, 
            `neg` 
            FROM `national_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `year` = '",from_year,"' OR `year` = '",to_year,"' ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_national_vl_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`baseline`) AS `baseline`, 
        SUM(`baselinesustxfail`) AS `baselinesustxfail`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`alltests`) AS `alltests`,
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`, 
        SUM(`repeattests`) AS `repeats`, 
        SUM(`invalids`) AS `invalids`,
        SUM(`received`) AS `received`,
        AVG(`sitessending`) AS `sitessending`
    FROM `vl_national_summary`
    WHERE 1";

   

     IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_age` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `ac`.`name`, 
                    SUM(`vca`.`tests`) AS `agegroups`,
                    SUM(`vca`.`undetected`+`vca`.`less1000`) AS `suppressed`,
                    SUM(`vca`.`less5000`+`vca`.`above5000`) AS `nonsuppressed`
                FROM `vl_partner_age` `vca`
                JOIN `agecategory` `ac`
                    ON `vca`.`age` = `ac`.`ID`
                WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vca`.`partner` = '",P_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ac`.`ID` ORDER BY `ac`.`ID` ASC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_entry_points` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`ep`.`name`, 
						SUM(`pos`) AS `positive`, 
						SUM(`neg`) AS `negative`  
					FROM `ip_entrypoint` `nep` 
					JOIN `entry_points` `ep` 
					ON `nep`.`entrypoint` = `ep`.`ID`
                WHERE 1";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `nep`.`year` = '",filter_year,"' AND `nep`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `nep`.`year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ep`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_gender` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `g`.`name`,
                    SUM(`vng`.`undetected`+`vng`.`less1000`) AS `suppressed`,
                    SUM(`vng`.`less5000`+`vng`.`above5000`) AS `nonsuppressed`
                FROM `vl_partner_gender` `vng`
                JOIN `gender` `g`
                    ON `vng`.`gender` = `g`.`ID`
                WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, "  AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, "  AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, "  AND `year` = '",filter_year,"' ");
    END IF;

     SET @QUERY = CONCAT(@QUERY, " AND `vng`.`partner` = '",P_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `g`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_iprophylaxis` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`p`.`name`, 
						SUM(`pos`) AS `positive`, 
						SUM(`neg`) AS `negative` 
					FROM `ip_iprophylaxis` `nip` 
					JOIN `prophylaxis` `p` ON `nip`.`prophylaxis` = `p`.`ID`
                WHERE 1";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `nip`.`year` = '",filter_year,"' AND `nip`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `nip`.`year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_justification` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vj`.`name`,
                    SUM((`vnj`.`tests`)) AS `justifications`
                FROM `vl_partner_justification` `vnj`
                JOIN `viraljustifications` `vj` 
                    ON `vnj`.`justification` = `vj`.`ID`
                WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vnj`.`partner` = '",P_id,"' ");


    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vj`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_justification_breakdown` (IN `justification` INT(11), IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        SUM(`Undetected`) AS `Undetected`,
                        SUM(`less1000`) AS `less1000`, 
                        SUM(`less5000`) AS `less5000`,
                        SUM(`above5000`) AS `above5000`
                    FROM `vl_partner_justification` 
                    WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;
    
    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `justification` = '",justification,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_mprophylaxis` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `filter_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`p`.`name`, 
						SUM(`pos`) AS `positive`, 
						SUM(`neg`) AS `negative` 
					FROM `ip_mprophylaxis` `nmp` 
					JOIN `prophylaxis` `p` ON `nmp`.`prophylaxis` = `p`.`ID`
                WHERE 1";

    IF (filter_month != 0 && filter_month != '') THEN
       SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `nmp`.`year` = '",filter_year,"' AND `nmp`.`month`='",filter_month,"' ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `nmp`.`year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `p`.`ID` ORDER BY `negative` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `p`.`name`,
                    SUM((`vps`.`Undetected`+`vps`.`less1000`)) AS `suppressed`,
                    SUM((`vps`.`less5000`+`vps`.`above5000`)) AS `nonsuppressed`
                FROM `vl_partner_summary` `vps`
                    JOIN `partners` `p` ON `vps`.`partner` = `p`.`ID`
                WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vps`.`partner` ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_performance` (IN `filter_partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `p`.`name`, `ip`.`month`, `ip`.`year`, SUM(`ip`.`tests`) AS `tests`, 
                    SUM(`ip`.`infantsless2m`) AS `infants`, 
                    SUM(`ip`.`pos`) AS `pos`,
                    SUM(`ip`.`neg`) AS `neg`, SUM(`ip`.`rejected`) AS `rej`
                FROM `ip_summary` `ip`
                JOIN `partners` `p`
                ON `p`.`ID` = `ip`.`partner` 
                WHERE 1 ";

    
        

         IF (filter_partner != 0 && filter_partner != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `p`.`ID` = '",filter_partner,"' ");
        END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ip`.`month`, `ip`.`year` ");
  
    SET @QUERY = CONCAT(@QUERY, " ORDER BY `ip`.`year` DESC, `ip`.`month` ASC ");


  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_sample_types` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
					`month`,
					`year`,
					SUM(`edta`) AS `edta`,
 					SUM(`dbs`) AS `dbs`,
 					SUM(`plasma`) AS `plasma`,
					SUM(`alledta`) AS `alledta`,
 					SUM(`alldbs`) AS `alldbs`,
 					SUM(`allplasma`) AS `allplasma`,
					SUM(`Undetected`+`less1000`) AS `suppressed`,
					SUM(`Undetected`+`less1000`+`less5000`+`above5000`) AS `tests`,
					SUM((`Undetected`+`less1000`)*100/(`Undetected`+`less1000`+`less5000`+`above5000`)) AS `suppression`
				FROM `vl_partner_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND (`year` = '",filter_year,"' OR  `year` = '",to_year,"') GROUP BY `year`, `month` ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_sitessending` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
       `sitessending`
    FROM `vl_partner_summary`
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' ");
    


     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_sites_details` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `view_facilitys`.`facilitycode` AS `MFLCode`, 
                    `view_facilitys`.`name`, 
                    `countys`.`name` AS `county`,
                    SUM(`vl_site_summary`.`received`) AS `received`, 
                    SUM(`vl_site_summary`.`rejected`) AS `rejected`,  
                    SUM(`vl_site_summary`.`invalids`) AS `invalids`,
                    SUM(`vl_site_summary`.`alltests`) AS `alltests`,  
                    SUM(`vl_site_summary`.`Undetected`) AS `undetected`,  
                    SUM(`vl_site_summary`.`less1000`) AS `less1000`,  
                    SUM(`vl_site_summary`.`less5000`) AS `less5000`,  
                    SUM(`vl_site_summary`.`above5000`) AS `above5000`,
                    SUM(`vl_site_summary`.`baseline`) AS `baseline`,
                    SUM(`vl_site_summary`.`baselinesustxfail`) AS `baselinesustxfail`,
                    SUM(`vl_site_summary`.`confirmtx`) AS `confirmtx`,
                    SUM(`vl_site_summary`.`confirm2vl`) AS `confirm2vl` FROM `vl_site_summary` 
                  LEFT JOIN `view_facilitys` ON `vl_site_summary`.`facility` = `view_facilitys`.`ID` 
                  LEFT JOIN `countys` ON `view_facilitys`.`county` = `countys`.`ID`  WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",P_id,"' GROUP BY `view_facilitys`.`ID` ORDER BY `alltests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_sites_outcomes` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `view_facilitys`.`name`, 
                    SUM(`vl_site_summary`.`undetected`+`vl_site_summary`.`less1000`) AS `suppressed`,
                    SUM(`vl_site_summary`.`less5000`+`vl_site_summary`.`above5000`) AS `nonsuppressed` FROM `vl_site_summary` LEFT JOIN `view_facilitys` ON `vl_site_summary`.`facility` = `view_facilitys`.`ID` WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",P_id,"' GROUP BY `view_facilitys`.`ID` ORDER BY `suppressed` DESC LIMIT 0, 50 ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_sustxfail_age` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `ac`.`name`, 
                        SUM(`vca`.`sustxfail`) AS `sustxfail` 
                    FROM `vl_partner_age` `vca` 
                    JOIN `agecategory` `ac` 
                        ON `vca`.`age` = `ac`.`subID`
                WHERE 1 ";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vca`.`partner` = '",P_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ac`.`name` ORDER BY `ac`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_sustxfail_gender` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `gn`.`name`, 
                        SUM(`vrg`.`sustxfail`) AS `sustxfail` 
                    FROM `vl_partner_gender` `vrg` 
                    JOIN `gender` `gn` 
                        ON `vrg`.`gender` = `gn`.`ID`
                WHERE 1  ";

 
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vrg`.`partner` = '",P_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `gn`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_sustxfail_justification` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `vj`.`name`,
                        SUM(`vcj`.`sustxfail`) AS `sustxfail`
                    FROM `vl_partner_justification` `vcj`
                    JOIN `viraljustifications` `vj`
                        ON `vcj`.`justification` = `vj`.`ID`
                WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;



    SET @QUERY = CONCAT(@QUERY, " AND `vcj`.`partner` = '",C_id,"' GROUP BY `vj`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_sustxfail_regimen` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT `vp`.`name`, SUM(`vnr`.`sustxfail`) AS `sustxfail`
                    FROM `vl_partner_regimen` `vnr`
                    JOIN `viralprophylaxis` `vp`
                        ON `vnr`.`regimen` = `vp`.`ID`
                WHERE 1";

  

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, "  AND `vnr`.`partner` = '",P_id,"' GROUP BY `vp`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_sustxfail_sampletypes` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `vsd`.`name`, 
                        SUM(`vcs`.`sustxfail`) AS `sustxfail` 
                    FROM `vl_partner_sampletype` `vcs` 
                    JOIN `viralsampletypedetails` `vsd` 
                        ON `vcs`.`sampletype` = `vsd`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;
    
    SET @QUERY = CONCAT(@QUERY, " AND `vcs`.`partner` = '",P_id,"' GROUP BY `vsd`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_testing_trends` (IN `P_id` INT(11), IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
            `year`, 
            `month`, 
            `pos`, 
            `neg` 
            FROM `ip_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `year` BETWEEN '",from_year,"' AND '",to_year,"' ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_vl_outcomes` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`baseline`) AS `baseline`, 
        SUM(`baselinesustxfail`) AS `baselinesustxfail`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`alltests`) AS `alltests`,
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`, 
        SUM(`repeattests`) AS `repeats`, 
        SUM(`invalids`) AS `invalids`,
        SUM(`received`) AS `received`,
        AVG(`sitessending`) AS `sitessending`
    FROM `vl_partner_summary`
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' ");


     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_partner_year_summary` (IN `filter_partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `ip`.`year`,  SUM(`ip`.`pos`) AS `positive`,
                    SUM(`ip`.`neg`) AS `negative`
                FROM `ip_summary` `ip`
                JOIN `partners` `p`
                ON `p`.`ID` = `ip`.`partner` 
                WHERE 1 ";

         IF (filter_partner != 0 && filter_partner != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `p`.`ID` = '",filter_partner,"' ");
        END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ip`.`year` ");
  
    SET @QUERY = CONCAT(@QUERY, " ORDER BY `ip`.`year` DESC ");


  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_age` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `ac`.`name`, 
                    SUM(`vca`.`tests`) AS `agegroups`, 
                    SUM(`vca`.`undetected`+`vca`.`less1000`) AS `suppressed`,
                    SUM(`vca`.`less5000`+`vca`.`above5000`) AS `nonsuppressed`
                FROM `vl_county_age` `vca`
                JOIN `agecategory` `ac`
                    ON `vca`.`age` = `ac`.`ID`
                WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " AND `vca`.`county` = '",C_id,"' GROUP BY `ac`.`ID` ORDER BY `ac`.`ID` ASC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_gender` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `g`.`name`,
                    SUM(`vng`.`undetected`+`vng`.`less1000`) AS `suppressed`,
                    SUM(`vng`.`less5000`+`vng`.`above5000`) AS `nonsuppressed`
                FROM `vl_county_gender` `vng`
                JOIN `gender` `g`
                    ON `vng`.`gender` = `g`.`ID`
                WHERE 1 ";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    

    SET @QUERY = CONCAT(@QUERY, " AND `vng`.`county` = '",C_id,"' GROUP BY `g`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_justification` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vj`.`name`,
                    SUM((`vnj`.`tests`)) AS `justifications`
                FROM `vl_county_justification` `vnj`
                JOIN `viraljustifications` `vj` 
                    ON `vnj`.`justification` = `vj`.`ID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, "  AND `vnj`.`county` = '",C_id,"' GROUP BY `vj`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_justification_breakdown` (IN `justification` INT(11), IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        SUM(`Undetected`) AS `Undetected`,
                        SUM(`less1000`) AS `less1000`, 
                        SUM(`less5000`) AS `less5000`,
                        SUM(`above5000`) AS `above5000`
                    FROM `vl_county_justification` 
                    WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;



    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' AND `justification` = '",justification,"' ");
    
     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_sample_types` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
					`month`,
					`year`,
					SUM(`edta`) AS `edta`,
 					SUM(`dbs`) AS `dbs`,
 					SUM(`plasma`) AS `plasma`,
					SUM(`alledta`) AS `alledta`,
 					SUM(`alldbs`) AS `alldbs`,
 					SUM(`allplasma`) AS `allplasma`,
					SUM(`Undetected`+`less1000`) AS `suppressed`,
					SUM(`Undetected`+`less1000`+`less5000`+`above5000`) AS `tests`,
					SUM((`Undetected`+`less1000`)*100/(`Undetected`+`less1000`+`less5000`+`above5000`)) AS `suppression`
				FROM `vl_county_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' AND (`year` = '",filter_year,"' OR  `year` = '",to_year,"') GROUP BY `year`, `month` ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_sitessending` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
       `sitessending`
    FROM `vl_county_summary`
    WHERE 1";



    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_sustxfail_age` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `ac`.`name`, 
                        SUM(`vca`.`sustxfail`) AS `sustxfail` 
                    FROM `vl_county_age` `vca` 
                    JOIN `agecategory` `ac` 
                        ON `vca`.`age` = `ac`.`subID`
                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;



    SET @QUERY = CONCAT(@QUERY, " AND `vca`.`county` = '",C_id,"'  GROUP BY `ac`.`name` ORDER BY `ac`.`ID` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_sustxfail_gender` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `gn`.`name`, 
                        SUM(`vrg`.`sustxfail`) AS `sustxfail` 
                    FROM `vl_county_gender` `vrg` 
                    JOIN `gender` `gn` 
                        ON `vrg`.`gender` = `gn`.`ID`
                WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vrg`.`county` = '",C_id,"' GROUP BY `gn`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_sustxfail_justification` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `vj`.`name`,
                        SUM(`vcj`.`sustxfail`) AS `sustxfail`
                    FROM `vl_county_justification` `vcj`
                    JOIN `viraljustifications` `vj`
                        ON `vcj`.`justification` = `vj`.`ID`
                WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vcj`.`county` = '",C_id,"' GROUP BY `vj`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_sustxfail_notification` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        SUM(`sustxfail`) AS `sustxfail`, 
                        ((SUM(`sustxfail`)/SUM(`alltests`))*100) AS `sustxfail_rate` 
                    FROM `vl_county_summary`
                WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_sustxfail_partner` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                      DISTINCT(`p`.`name`) AS `name`,
                      ((SUM(`vps`.`sustxfail`)/SUM(`vps`.`alltests`))*100) AS `percentages`, 
                      SUM(`vps`.`sustxfail`) AS `sustxfail` 
                  FROM `vl_partner_summary` `vps` 
                  JOIN `partners` `p` 
                    ON `vps`.`partner` = `p`.`ID`
                  JOIN `view_facilitys` `vf`
                    ON `p`.`ID` = `vf`.`partner`
                WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",C_id,"' GROUP BY `p`.`name` ORDER BY `percentages` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_sustxfail_rank_regimen` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT `vp`.`name`, SUM(`vnr`.`sustxfail`) AS `sustxfail`
                    FROM `vl_county_regimen` `vnr`
                    JOIN `viralprophylaxis` `vp`
                        ON `vnr`.`regimen` = `vp`.`ID`
                WHERE 1 ";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vnr`.`county` = '",C_id,"'  GROUP BY `vp`.`name` ORDER BY `sustxfail` DESC LIMIT 0, 5 ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_sustxfail_regimen` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT `vp`.`name`, SUM(`vnr`.`sustxfail`) AS `sustxfail`
                    FROM `vl_county_regimen` `vnr`
                    JOIN `viralprophylaxis` `vp`
                        ON `vnr`.`regimen` = `vp`.`ID`
                WHERE 1 ";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vnr`.`county` = '",C_id,"'  GROUP BY `vp`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_sustxfail_sampletypes` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `vsd`.`name`, 
                        SUM(`vcs`.`sustxfail`) AS `sustxfail` 
                    FROM `vl_county_sampletype` `vcs` 
                    JOIN `viralsampletypedetails` `vsd` 
                        ON `vcs`.`sampletype` = `vsd`.`ID`
                WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vcs`.`county` = '",C_id,"' GROUP BY `vsd`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_regional_vl_outcomes` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`baseline`) AS `baseline`, 
        SUM(`baselinesustxfail`) AS `baselinesustxfail`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`alltests`) AS `alltests`,
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`, 
        SUM(`repeattests`) AS `repeats`, 
        SUM(`invalids`) AS `invalids`,
        SUM(`received`) AS `received`,
        AVG(`sitessending`) AS `sitessending`
    FROM `vl_county_summary`
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_sites_age` (IN `S_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
                    `ac`.`name`, 
                    SUM(`vsa`.`tests`) AS `agegroups`, 
                    SUM(`vsa`.`undetected`+`vsa`.`less1000`) AS `suppressed`,
                    SUM(`vsa`.`less5000`+`vsa`.`above5000`) AS `nonsuppressed`
                FROM `vl_site_age` `vsa`
                JOIN `agecategory` `ac`
                    ON `vsa`.`age` = `ac`.`ID`
                WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",S_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ac`.`ID` ORDER BY `ac`.`ID` ASC ");



    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_sites_gender` (IN `S_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
                    `g`.`name`,
                      SUM(`vsg`.`Undetected` + `vsg`.`less1000`) AS `suppressed`, 
                      SUM(`vsg`.`less5000` + `vsg`.`above5000`) AS `nonsuppressed` 
                FROM `vl_site_gender` `vsg`
                JOIN `gender` `g`
                    ON `vsg`.`gender` = `g`.`ID`
                WHERE 1 ";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",S_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `g`.`name` ");

    

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_sites_listing` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						SUM(`vss`.`sustxfail`) AS `sustxfail`, 
                        SUM(`vss`.`alltests`) AS `alltests`,
                        SUM(`vss`.`sustxfail`), 
                        ((SUM(`vss`.`sustxfail`)/SUM(`vss`.`alltests`))*100) AS `non supp`, 
                        `vf`.`ID`, 
                        `vf`.`name` 
					FROM `vl_site_summary` `vss` LEFT JOIN `view_facilitys` `vf` 
                    ON `vss`.`facility`=`vf`.`ID` 
                    WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vf`.`ID` ORDER BY `non supp` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_sites_sample_types` (IN `S_id` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
					`month`,
					`year`,
					SUM(`edta`) AS `edta`,
 					SUM(`dbs`) AS `dbs`,
 					SUM(`plasma`) AS `plasma`
				FROM `vl_site_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",S_id,"' AND `year` = '",filter_year,"' GROUP BY `year`, `month` ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_sites_trends` (IN `S_id` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        `month`, 
        `year`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`alltests`) AS `alltests`,
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`,
        AVG(`sitessending`) AS `sitessending`
    FROM `vl_site_summary`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",S_id,"' AND `year` = '",filter_year,"' GROUP BY `month`");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_sites_vl_outcomes` (IN `S_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`baseline`) AS `baseline`, 
        SUM(`baselinesustxfail`) AS `baselinesustxfail`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`alltests`) AS `alltests`,
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`, 
        SUM(`repeattests`) AS `repeats`, 
        SUM(`invalids`) AS `invalids`,
        SUM(`received`) AS `received`,
        AVG(`sitessending`) AS `sitessending`
    FROM `vl_site_summary`
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",S_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_subcounty_sustxfail_notification` (IN `SC_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                        SUM(`sustxfail`) AS `sustxfail`, 
                        ((SUM(`sustxfail`)/SUM(`alltests`))*100) AS `sustxfail_rate` 
                    FROM `vl_subcounty_summary`
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' ");
    END IF;

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_age_breakdowns_outcomes` (IN `filter_age` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11), IN `county` INT(11), IN `partner` INT(11), IN `subcounty` INT(11), IN `site` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `c`.`name`,
                    SUM(`vca`.`undetected`+`vca`.`less1000`) AS `suppressed`,
                    SUM(`vca`.`less5000`+`vca`.`above5000`) AS `nonsuppressed`, 
                    SUM(`vca`.`undetected`+`vca`.`less1000`+`vca`.`less5000`+`vca`.`above5000`) AS `total`,
                    ((SUM(`vca`.`undetected`+`vca`.`less1000`)/SUM(`vca`.`undetected`+`vca`.`less1000`+`vca`.`less5000`+`vca`.`above5000`))*100) AS `percentage` ";

    IF (county != 0 && county != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_county_age` `vca` JOIN `countys` `c` ON `vca`.`county` = `c`.`ID` WHERE 1 ");
    END IF;
    IF (partner != 0 && partner != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_partner_age` `vca` JOIN `partners` `c` ON `vca`.`partner` = `c`.`ID` WHERE 1 ");
    END IF;
    IF (subcounty != 0 && subcounty != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_subcounty_age` `vca` JOIN `districts` `c` ON `vca`.`subcounty` = `c`.`ID` WHERE 1 ");
    END IF;
    IF (site != 0 && site != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_site_age` `vca` JOIN `facilitys` `c` ON `vca`.`facility` = `c`.`ID` WHERE 1 ");
    END IF;

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " AND `age` = '",filter_age,"' GROUP BY `c`.`ID` ORDER BY `percentage` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_age_gender` (IN `A_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`maletest`-`malenonsuppressed`) AS `malesuppressed`,
        SUM(`malenonsuppressed`) AS `malenonsuppressed`, 
        SUM(`femaletest`-`femalenonsuppressed`) AS `femalesuppressed`,
        SUM(`femalenonsuppressed`) AS `femalenonsuppressed`, 
        SUM(`nogendertest`-`nogendernonsuppressed`) AS `nodatasuppressed`,
        SUM(`nogendernonsuppressed`) AS `nodatanonsuppressed`
    FROM `vl_national_age`
    WHERE 1 ";
  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `age` = '",A_id,"' ");    


    

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_age_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`ac`.`name`, 
						SUM(`vna`.`less5000`+`vna`.`above5000`) AS `nonsuppressed`, 
						SUM(`vna`.`Undetected`+`vna`.`less1000`) AS `suppressed` 
						FROM `vl_national_age` `vna`
						LEFT JOIN `agecategory` `ac` 
						ON `vna`.`age` = `ac`.`ID`
					WHERE `ac`.`ID` NOT BETWEEN '1' AND '5'";

 
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " GROUP BY `name` ORDER BY `ac`.`ID` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_age_sample_types` (IN `A_id` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
          `month`,
          `year`,
          SUM(`edta`) AS `edta`,
          SUM(`dbs`) AS `dbs`,
          SUM(`plasma`) AS `plasma`,
          SUM(`Undetected`+`less1000`) AS `suppressed`,
          SUM(`Undetected`+`less1000`+`less5000`+`above5000`) AS `tests`,
          SUM((`Undetected`+`less1000`)*100/(`Undetected`+`less1000`+`less5000`+`above5000`)) AS `suppression` 
    FROM `vl_national_age`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `age` = '",A_id,"' AND `year` = '",filter_year,"' GROUP BY `month` ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_age_vl_outcomes` (IN `A_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`baseline`) AS `baseline`, 
        SUM(`baselinesustxfail`) AS `baselinesustxfail`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`tests`) AS `alltests`, 
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`, 
        SUM(`repeattests`) AS `repeats`, 
        SUM(`invalids`) AS `invalids`
    FROM `vl_national_age`
    WHERE 1 ";

  

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `age` = '",A_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_baseline` (IN `param_type` INT(11), IN `param` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
                    SUM(`ba`.`tests`) AS `tests`,  
                    SUM(`ba`.`sustxfail`) AS `sustxfail`,  
                    SUM(`ba`.`rejected`) AS `rejected`,  
                    SUM(`ba`.`invalids`) AS `invalids`,  
                    SUM(`ba`.`dbs`) AS `dbs`,  
                    SUM(`ba`.`plasma`) AS `plasma`,  
                    SUM(`ba`.`edta`) AS `edta`,
                    SUM(`ba`.`maletest`) AS `maletest`,
                    SUM(`ba`.`femaletest`) AS `femaletest`,
                    SUM(`ba`.`nogendertest`) AS `nogendertest`,
                    SUM(`ba`.`noage`) AS `noage`,
                    SUM(`ba`.`less2`) AS `less2`,
                    SUM(`ba`.`less9`) AS `less9`,
                    SUM(`ba`.`less14`) AS `less14`,
                    SUM(`ba`.`less19`) AS `less19`,
                    SUM(`ba`.`less24`) AS `less24`,
                    SUM(`ba`.`over25`) AS `over25`,
                    SUM(`ba`.`undetected`) AS `undetected`,
                    SUM(`ba`.`less1000`) AS `less1000`,
                    SUM(`ba`.`less5000`) AS `less5000`,
                    SUM(`ba`.`above5000`) AS `above5000`
                  ";

    IF (param_type = 0) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_national_justification` `ba`  WHERE 1 ");
    END IF;

    IF (param_type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_county_justification` `ba`  WHERE 1 and `county` = '",param,"' ");
    END IF;

    IF (param_type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_subcounty_justification` `ba`  WHERE 1 and `subcounty` = '",param,"' ");
    END IF;

    IF (param_type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_partner_justification` `ba`  WHERE 1 and `partner` = '",param,"' ");
    END IF;

    IF (param_type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_site_justification` `ba`  WHERE 1 and `facility` = '",param,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `justification` = 10 ");

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_baseline_list` (IN `param_type` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
                    `ac`.`name`,
                    SUM(`ba`.`tests`) AS `tests`,  
                    SUM(`ba`.`sustxfail`) AS `sustxfail`,  
                    SUM(`ba`.`rejected`) AS `rejected`,  
                    SUM(`ba`.`invalids`) AS `invalids`,  
                    SUM(`ba`.`dbs`) AS `dbs`,  
                    SUM(`ba`.`plasma`) AS `plasma`,  
                    SUM(`ba`.`edta`) AS `edta`,
                    SUM(`ba`.`maletest`) AS `maletest`,
                    SUM(`ba`.`femaletest`) AS `femaletest`,
                    SUM(`ba`.`nogendertest`) AS `nogendertest`,
                    SUM(`ba`.`noage`) AS `noage`,
                    SUM(`ba`.`less2`) AS `less2`,
                    SUM(`ba`.`less9`) AS `less9`,
                    SUM(`ba`.`less14`) AS `less14`,
                    SUM(`ba`.`less19`) AS `less19`,
                    SUM(`ba`.`less24`) AS `less24`,
                    SUM(`ba`.`over25`) AS `over25`,
                    SUM(`ba`.`undetected`) AS `undetected`,
                    SUM(`ba`.`less1000`) AS `less1000`,
                    SUM(`ba`.`less5000`) AS `less5000`,
                    SUM(`ba`.`above5000`) AS `above5000`
                  ";

    IF (param_type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_county_justification` `ba` JOIN `countys` `ac` ON `ac`.`ID` = `ba`.`county`  ");
    END IF;

    IF (param_type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_subcounty_justification` `ba` JOIN `districts` `ac` ON `ac`.`ID` = `ba`.`subcounty` ");
    END IF;

    IF (param_type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_partner_justification` `ba` JOIN `partners` `ac` ON `ac`.`ID` = `ba`.`partner` ");
    END IF;

    IF (param_type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_site_justification` `ba` JOIN `view_facilitys` `ac` ON `ac`.`ID` = `ba`.`facility` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " WHERE 1 AND `justification` = 10 AND `tests` > 0 ");

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ac`.`name` ORDER BY `tests` DESC ");


     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_age_outcomes` (IN `filter_age` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `c`.`name`,
                    SUM(`vca`.`undetected`+`vca`.`less1000`) AS `suppressed`,
                    SUM(`vca`.`less5000`+`vca`.`above5000`) AS `nonsuppressed` 
                FROM `vl_county_age` `vca`
                    JOIN `countys` `c` ON `vca`.`county` = `c`.`ID`
    WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " AND `age` = '",filter_age,"' GROUP BY `vca`.`county` ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_details` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
                    `countys`.`name` AS `county`,
                    AVG(`vcs`.`sitessending`) AS `sitesending`, 
                    SUM(`vcs`.`received`) AS `received`, 
                    SUM(`vcs`.`rejected`) AS `rejected`,  
                    SUM(`vcs`.`invalids`) AS `invalids`,
                    SUM(`vcs`.`alltests`) AS `alltests`,  
                    SUM(`vcs`.`Undetected`) AS `undetected`,  
                    SUM(`vcs`.`less1000`) AS `less1000`,  
                    SUM(`vcs`.`less5000`) AS `less5000`,  
                    SUM(`vcs`.`above5000`) AS `above5000`,
                    SUM(`vcs`.`confirmtx`) AS `confirmtx`,
                    SUM(`vcs`.`confirm2vl`) AS `confirm2vl`,
                    SUM(`vcs`.`baseline`) AS `baseline`,
                    SUM(`vcs`.`baselinesustxfail`) AS `baselinesustxfail`
                  FROM `vl_county_summary` `vcs`
                  JOIN `countys` ON `vcs`.`county` = `countys`.`ID`  WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `countys`.`name` ORDER BY `alltests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_partners` (IN `filter_county` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `c`.`name` AS `county`,
                    `p`.`name` AS `partner`,
                    AVG(`vss`.`sitessending`) AS `sitesending`, 
                    SUM(`vss`.`received`) AS `received`, 
                    SUM(`vss`.`rejected`) AS `rejected`,  
                    SUM(`vss`.`invalids`) AS `invalids`,
                    SUM(`vss`.`alltests`) AS `alltests`,  
                    SUM(`vss`.`Undetected`) AS `undetected`,  
                    SUM(`vss`.`less1000`) AS `less1000`,  
                    SUM(`vss`.`less5000`) AS `less5000`,  
                    SUM(`vss`.`above5000`) AS `above5000`,
                    SUM(`vss`.`confirmtx`) AS `confirmtx`,
                    SUM(`vss`.`confirm2vl`) AS `confirm2vl`,
                    SUM(`vss`.`baseline`) AS `baseline`,
                    SUM(`vss`.`baselinesustxfail`) AS `baselinesustxfail` 
                    FROM `vl_site_summary` `vss`
      
            LEFT JOIN `view_facilitys` `vf` ON `vf`.`ID` = `vss`.`facility`
                  LEFT JOIN `partners` `p` ON `p`.`ID` = `vf`.`partner`
                  LEFT JOIN `countys` `c` ON `c`.`ID` = `vf`.`county`
                     WHERE 1 ";

    
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",filter_county,"' GROUP BY `p`.`name` ORDER BY `alltests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_regimen_outcomes` (IN `filter_regimen` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `c`.`name`,
                    SUM(`vcr`.`undetected`+`vcr`.`less1000`) AS `suppressed`,
                    SUM(`vcr`.`less5000`+`vcr`.`above5000`) AS `nonsuppressed` 
                FROM `vl_county_regimen` `vcr`
                    JOIN `countys` `c` ON `vcr`.`county` = `c`.`ID`
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `regimen` = '",filter_regimen,"' GROUP BY `vcr`.`county` ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_samples_outcomes` (IN `filter_sampletype` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `c`.`name`,
                    SUM(`vcs`.`undetected`+`vcs`.`less1000`) AS `suppressed`,
                    SUM(`vcs`.`less5000`+`vcs`.`above5000`) AS `nonsuppressed`
		    FROM `vl_county_sampletype` `vcs`
                    JOIN `countys` `c` ON `vcs`.`county` = `c`.`ID`
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `sampletype` = '",filter_sampletype,"' GROUP BY `vcs`.`county` ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_shortcodes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    COUNT(shortcodequeries.ID) AS `counts`, 
                    view_facilitys.county, 
                    countys.name
                  FROM shortcodequeries 
                  LEFT JOIN view_facilitys 
                    ON shortcodequeries.mflcode = view_facilitys.facilitycode 
                  LEFT JOIN countys 
                    ON countys.ID = view_facilitys.county 
                  WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived) BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived) >= '",from_month,"')  OR (YEAR(shortcodequeries.datereceived) = '",to_year,"' AND MONTH(shortcodequeries.datereceived) <= '",to_month,"') OR (YEAR(shortcodequeries.datereceived) > '",filter_year,"' AND YEAR(shortcodequeries.datereceived) < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived)='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY view_facilitys.county ORDER BY COUNT(shortcodequeries.ID) DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_subcounty_outcomes` (IN `filter_county` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `vf`.`countyname`, 
						`vf`.`subcounty` AS `name`, 
						SUM(`vss`.`less5000`+`vss`.`above5000`) AS `nonsuppressed`, 
						SUM(`vss`.`Undetected`+`vss`.`less1000`) AS `suppressed` 
						FROM `vl_subcounty_summary` `vss`
						JOIN `view_facilitys` `vf` 
						ON `vss`.`subcounty` = `vf`.`district`
					WHERE 1 ";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

     IF (filter_county != 0 && filter_county != '') THEN
        SET @QUERY = CONCAT(@QUERY, " AND `vf`.`county` = '",filter_county,"' ");
     END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vf`.`district` ORDER BY `suppressed` DESC, `nonsuppressed` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_sustxfail` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `c`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`
                FROM vl_county_summary `vcs`
                LEFT JOIN countys `c`
                    ON c.ID = vcs.county
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `name` ORDER BY suppressed DESC, nonsuppressed DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_sustxfail_age` (IN `C_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `ag`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`,
                    (SUM(`Undetected`+`less1000`)/(SUM(`Undetected`+`less1000`)+SUM(`less5000`+`above5000`))) AS `pecentage`
                FROM vl_county_age `vca`
                LEFT JOIN agecategory `ag`
                    ON ag.ID = vca.age

                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " AND ag.ID NOT BETWEEN '1' AND '5' GROUP BY `name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_sustxfail_gender` (IN `C_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `g`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`
                FROM vl_county_gender `vcg`
                LEFT JOIN gender `g`
                    ON g.ID = vcg.gender 
                WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " GROUP BY `g`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_county_sustxfail_justification` (IN `C_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `vj`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`,
                    (SUM(`Undetected`+`less1000`)/(SUM(`Undetected`+`less1000`)+SUM(`less5000`+`above5000`))) AS `pecentage`
                FROM vl_county_justification `vcj`
                LEFT JOIN viraljustifications `vj`
                    ON vj.ID = vcj.justification
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `county` = '",C_Id,"' AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vj`.`name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_age_suppression_listing` (IN `type` INT(11), IN `county` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `view_facilitys`.`countyname`, `view_facilitys`.`subcounty`,
                     `view_facilitys`.`partnername`, `view_facilitys`.`name`,

                    SUM(`noage_suppressed`) AS `noage_suppressed`, SUM(`noage_nonsuppressed`) AS `noage_nonsuppressed`,
                    SUM(`less2_suppressed`) AS `less2_suppressed`, SUM(`less2_nonsuppressed`) AS `less2_nonsuppressed`,
                    SUM(`less9_suppressed`) AS `less9_suppressed`, SUM(`less9_nonsuppressed`) AS `less9_nonsuppressed`,
                    SUM(`less14_suppressed`) AS `less14_suppressed`, SUM(`less14_nonsuppressed`) AS `less14_nonsuppressed`,
                    SUM(`less19_suppressed`) AS `less19_suppressed`, SUM(`less19_nonsuppressed`) AS `less19_nonsuppressed`,
                    SUM(`less24_suppressed`) AS `less24_suppressed`, SUM(`less24_nonsuppressed`) AS `less24_nonsuppressed`,
                    SUM(`over25_suppressed`) AS `over25_suppressed`, SUM(`over25_nonsuppressed`) AS `over25_nonsuppressed`
                    
                     FROM `vl_site_suppression` 
                  JOIN `view_facilitys` ON `vl_site_suppression`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    IF(county != 0) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`county` = '",county,"' ");
    END IF; 
    
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`county` ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`district`  ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`partner`  ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `vl_site_suppression`.`facility` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_age_suppression_listing_partner` (IN `type` INT(11), IN `partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `view_facilitys`.`countyname`, `view_facilitys`.`subcounty`,
                     `view_facilitys`.`partnername`, `view_facilitys`.`name`,

                    SUM(`noage_suppressed`) AS `noage_suppressed`, SUM(`noage_nonsuppressed`) AS `noage_nonsuppressed`,
                    SUM(`less2_suppressed`) AS `less2_suppressed`, SUM(`less2_nonsuppressed`) AS `less2_nonsuppressed`,
                    SUM(`less9_suppressed`) AS `less9_suppressed`, SUM(`less9_nonsuppressed`) AS `less9_nonsuppressed`,
                    SUM(`less14_suppressed`) AS `less14_suppressed`, SUM(`less14_nonsuppressed`) AS `less14_nonsuppressed`,
                    SUM(`less19_suppressed`) AS `less19_suppressed`, SUM(`less19_nonsuppressed`) AS `less19_nonsuppressed`,
                    SUM(`less24_suppressed`) AS `less24_suppressed`, SUM(`less24_nonsuppressed`) AS `less24_nonsuppressed`,
                    SUM(`over25_suppressed`) AS `over25_suppressed`, SUM(`over25_nonsuppressed`) AS `over25_nonsuppressed`
                    
                     FROM `vl_site_suppression` 
                  JOIN `view_facilitys` ON `vl_site_suppression`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    IF(partner != 1000) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",partner,"' ");
    END IF; 
    
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`county` ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`district`  ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`partner`  ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `vl_site_suppression`.`facility` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_age_suppression_listing_partner_year` (IN `type` INT(11), IN `partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `view_facilitys`.`countyname`, `view_facilitys`.`subcounty`,
                     `view_facilitys`.`partnername`, `view_facilitys`.`name`,

                    SUM(`noage_suppressed`) AS `noage_suppressed`, SUM(`noage_nonsuppressed`) AS `noage_nonsuppressed`,
                    SUM(`less2_suppressed`) AS `less2_suppressed`, SUM(`less2_nonsuppressed`) AS `less2_nonsuppressed`,
                    SUM(`less9_suppressed`) AS `less9_suppressed`, SUM(`less9_nonsuppressed`) AS `less9_nonsuppressed`,
                    SUM(`less14_suppressed`) AS `less14_suppressed`, SUM(`less14_nonsuppressed`) AS `less14_nonsuppressed`,
                    SUM(`less19_suppressed`) AS `less19_suppressed`, SUM(`less19_nonsuppressed`) AS `less19_nonsuppressed`,
                    SUM(`less24_suppressed`) AS `less24_suppressed`, SUM(`less24_nonsuppressed`) AS `less24_nonsuppressed`,
                    SUM(`over25_suppressed`) AS `over25_suppressed`, SUM(`over25_nonsuppressed`) AS `over25_nonsuppressed`
                    
                     FROM `vl_site_suppression_year` 
                  JOIN `view_facilitys` ON `vl_site_suppression_year`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    IF(partner != 1000) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",partner,"' ");
    END IF; 
    
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`county` ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`district`  ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`partner`  ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `vl_site_suppression_year`.`facility` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_gender_suppression_listing` (IN `type` INT(11), IN `county` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `view_facilitys`.`countyname`, `view_facilitys`.`subcounty`,
                     `view_facilitys`.`partnername`, `view_facilitys`.`name`,

                    SUM(`male_suppressed`) AS `male_suppressed`, SUM(`male_nonsuppressed`) AS `male_nonsuppressed`,
                    SUM(`female_suppressed`) AS `female_suppressed`, SUM(`female_nonsuppressed`) AS `female_nonsuppressed`,
                    SUM(`nogender_suppressed`) AS `nogender_suppressed`, SUM(`nogender_nonsuppressed`) AS `nogender_nonsuppressed`
                    
                     FROM `vl_site_suppression` 
                  JOIN `view_facilitys` ON `vl_site_suppression`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    IF(county != 0) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`county` = '",county,"' ");
    END IF; 
    
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`county` ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`district`  ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`partner`  ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `vl_site_suppression`.`facility` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_gender_suppression_listing_partner` (IN `type` INT(11), IN `partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `view_facilitys`.`countyname`, `view_facilitys`.`subcounty`,
                     `view_facilitys`.`partnername`, `view_facilitys`.`name`,

                    SUM(`male_suppressed`) AS `male_suppressed`, SUM(`male_nonsuppressed`) AS `male_nonsuppressed`,
                    SUM(`female_suppressed`) AS `female_suppressed`, SUM(`female_nonsuppressed`) AS `female_nonsuppressed`,
                    SUM(`nogender_suppressed`) AS `nogender_suppressed`, SUM(`nogender_nonsuppressed`) AS `nogender_nonsuppressed`
                    
                     FROM `vl_site_suppression` 
                  JOIN `view_facilitys` ON `vl_site_suppression`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    IF(partner != 1000) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",partner,"' ");
    END IF; 
    
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`county` ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`district`  ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`partner`  ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `vl_site_suppression`.`facility` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_gender_suppression_listing_partner_year` (IN `type` INT(11), IN `partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `view_facilitys`.`countyname`, `view_facilitys`.`subcounty`,
                     `view_facilitys`.`partnername`, `view_facilitys`.`name`,

                    SUM(`male_suppressed`) AS `male_suppressed`, SUM(`male_nonsuppressed`) AS `male_nonsuppressed`,
                    SUM(`female_suppressed`) AS `female_suppressed`, SUM(`female_nonsuppressed`) AS `female_nonsuppressed`,
                    SUM(`nogender_suppressed`) AS `nogender_suppressed`, SUM(`nogender_nonsuppressed`) AS `nogender_nonsuppressed`
                    
                     FROM `vl_site_suppression_year` 
                  JOIN `view_facilitys` ON `vl_site_suppression_year`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    IF(partner != 1000) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",partner,"' ");
    END IF; 
    
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`county` ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`district`  ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`partner`  ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `vl_site_suppression_year`.`facility` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_suppression` (IN `type` INT(11), IN `id` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM(`suppressed`) AS `suppressed`, 
                    SUM(`nonsuppressed`) AS `nonsuppressed`, 
                    AVG(`suppression`) AS `suppression`, 
                    AVG(`coverage`) AS `coverage`, 
                    SUM(`totalartmar`) AS `totallstrpt` 
                     FROM `vl_site_suppression` 
                  JOIN `view_facilitys` ON `vl_site_suppression`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    

    IF(type != 4) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND (`vl_site_suppression`.`suppressed` > 0 || `vl_site_suppression`.`nonsuppressed` > 0) ");
    END IF; 
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`county` = '",id,"' ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`district` = '",id,"' ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",id,"' ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " AND `vl_site_suppression`.`facility` = '",id,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_suppression_listing` (IN `type` INT(11), IN `county` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `view_facilitys`.`countyname`, `view_facilitys`.`subcounty`,
                     `view_facilitys`.`partnername`, `view_facilitys`.`name`,
                    SUM(`suppressed`) AS `suppressed`, 
                    SUM(`nonsuppressed`) AS `nonsuppressed`, 
                    AVG(`suppression`) AS `suppression`, 
                    SUM(`totalartmar`) AS `totallstrpt`
                     FROM `vl_site_suppression` 
                  JOIN `view_facilitys` ON `vl_site_suppression`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    IF(county != 0) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`county` = '",county,"' ");
    END IF; 
    

    IF(type != 4) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND (`vl_site_suppression`.`suppressed` > 0 || `vl_site_suppression`.`nonsuppressed` > 0) ");
    END IF; 
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`county` ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`district`  ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`partner`  ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `vl_site_suppression`.`facility` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `suppression` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_suppression_listing_partners` (IN `type` INT(11), IN `partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `view_facilitys`.`countyname`, `view_facilitys`.`subcounty`,
                     `view_facilitys`.`partnername`, `view_facilitys`.`name`,
                    SUM(`suppressed`) AS `suppressed`, 
                    SUM(`nonsuppressed`) AS `nonsuppressed`, 
                    AVG(`suppression`) AS `suppression`, 
                    SUM(`totalartmar`) AS `totallstrpt`
                     FROM `vl_site_suppression` 
                  JOIN `view_facilitys` ON `vl_site_suppression`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    IF(partner != 1000) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",partner,"' ");
    END IF; 
    

    IF(type != 4) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND (`vl_site_suppression`.`suppressed` > 0 || `vl_site_suppression`.`nonsuppressed` > 0) ");
    END IF; 
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`county` ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`district`  ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`partner`  ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `vl_site_suppression`.`facility` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `suppression` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_suppression_listing_partners_year` (IN `type` INT(11), IN `partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `view_facilitys`.`countyname`, `view_facilitys`.`subcounty`,
                     `view_facilitys`.`partnername`, `view_facilitys`.`name`,
                    SUM(`suppressed`) AS `suppressed`, 
                    SUM(`nonsuppressed`) AS `nonsuppressed`, 
                    AVG(`suppression`) AS `suppression`, 
                    SUM(`totalartmar`) AS `totallstrpt`
                     FROM `vl_site_suppression_year` 
                  JOIN `view_facilitys` ON `vl_site_suppression_year`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    IF(partner != 1000) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",partner,"' ");
    END IF; 
    

    IF(type != 4) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND (`vl_site_suppression_year`.`suppressed` > 0 || `vl_site_suppression_year`.`nonsuppressed` > 0) ");
    END IF; 
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`county` ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`district`  ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `view_facilitys`.`partner`  ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `vl_site_suppression_year`.`facility` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " ORDER BY `suppression` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_current_suppression_year` (IN `type` INT(11), IN `id` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    SUM(`suppressed`) AS `suppressed`, 
                    SUM(`nonsuppressed`) AS `nonsuppressed`, 
                    AVG(`suppression`) AS `suppression`, 
                    AVG(`coverage`) AS `coverage`, 
                    SUM(`totalartmar`) AS `totallstrpt` 
                     FROM `vl_site_suppression_year` 
                  JOIN `view_facilitys` ON `vl_site_suppression_year`.`facility` = `view_facilitys`.`ID` 
                  WHERE 1";
    
    

    IF(type != 4) THEN 
      SET @QUERY = CONCAT(@QUERY, " AND (`vl_site_suppression_year`.`suppressed` > 0 || `vl_site_suppression_year`.`nonsuppressed` > 0) ");
    END IF; 
    
    IF(type = 1) THEN
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`county` = '",id,"' ");
    END IF;
    IF(type = 2) THEN
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`district` = '",id,"' ");
    END IF;
    IF(type = 3) THEN
      SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",id,"' ");
    END IF;
    IF(type = 4) THEN
      SET @QUERY = CONCAT(@QUERY, " AND `vl_site_suppression_year`.`facility` = '",id,"' ");
    END IF;

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_lab_live_data` (IN `filter_type` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `labs`.`ID` AS `ID`,
                    `labs`.`name` AS `name`,
                    SUM((`lablogs`.`enteredsamplesatsite`)) AS `enteredsamplesatsite`,
                    SUM((`lablogs`.`enteredsamplesatlab`)) AS `enteredsamplesatlab`,
                    SUM((`lablogs`.`receivedsamples`)) AS `receivedsamples`,
                    SUM((`lablogs`.`inqueuesamples`)) AS `inqueuesamples`,
                    SUM((`lablogs`.`inprocesssamples`)) AS `inprocesssamples`,
                    SUM((`lablogs`.`processedsamples`)) AS `processedsamples`,
                    SUM((`lablogs`.`pendingapproval`)) AS `pendingapproval`,
                    SUM((`lablogs`.`dispatchedresults`)) AS `dispatchedresults`,
                    SUM((`lablogs`.`oldestinqueuesample`)) AS `oldestinqueuesample`,
                    `dateupdated`
                FROM `lablogs`

                JOIN `labs` 
                    ON `lablogs`.`lab` = `labs`.`ID`
                WHERE 1 ";

                SET @QUERY = CONCAT(@QUERY, " AND DATE(logdate) = (SELECT MAX(logdate) from lablogs WHERE testtype = '",filter_type,"') AND testtype = '",filter_type,"'");

                SET @QUERY = CONCAT(@QUERY, " GROUP BY `labs`.`ID` ");
  
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_lab_performance_stats` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `l`.`labname` AS `name`, 
                    AVG(`ls`.`sitessending`) AS `sitesending`, 
                    SUM(`ls`.`received`) AS `received`, 
                    SUM(`ls`.`rejected`) AS `rejected`,  
                    SUM(`ls`.`invalids`) AS `invalids`,
                    SUM(`ls`.`alltests`) AS `alltests`,  
                    SUM(`ls`.`Undetected`) AS `undetected`,  
                    SUM(`ls`.`less1000`) AS `less1000`,  
                    SUM(`ls`.`less5000`) AS `less5000`,  
                    SUM(`ls`.`above5000`) AS `above5000`,  
                    SUM(`ls`.`eqa`) AS `eqa`,   
                    SUM(`ls`.`confirmtx`) AS `confirmtx`,
                    SUM(`ls`.`confirm2vl`) AS `confirm2vl`,
                    SUM(`ls`.`baseline`) AS `baseline`,
                    SUM(`ls`.`baselinesustxfail`) AS `baselinesustxfail`
                  FROM `vl_lab_summary` `ls` JOIN `labs` `l` ON `ls`.`lab` = `l`.`ID` 
                WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `l`.`ID` ORDER BY `alltests` DESC ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_lab_rejections` (IN `lab` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`total`) AS `total`,
        `vr`.`name`,
        `vr`.`alias` ";


    IF (lab != 0 && lab != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_lab_rejections` `v` ");
    ELSE
        SET @QUERY = CONCAT(@QUERY, " FROM `vl_national_rejections` `v` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " LEFT JOIN `viralrejectedreasons` `vr` ON `v`.`rejected_reason` = `vr`.`ID`");

    SET @QUERY = CONCAT(@QUERY, " WHERE 1 ");


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    IF (lab != 0 && lab != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `lab` = '",lab,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vr`.`ID` ORDER BY `total` DESC ");
    

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
     
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_live_data_totals` (IN `filter_type` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    SUM((`lablogs`.`receivedsamples`)) AS `receivedsamples`,
                    SUM((`lablogs`.`inqueuesamples`)) AS `inqueuesamples`,
                    SUM((`lablogs`.`inprocesssamples`)) AS `inprocesssamples`,
                    SUM((`lablogs`.`processedsamples`)) AS `processedsamples`,
                    SUM((`lablogs`.`pendingapproval`)) AS `pendingapproval`,
                    SUM((`lablogs`.`dispatchedresults`)) AS `dispatchedresults`,
                    SUM((`lablogs`.`enteredsamplesatlab`)) AS `enteredsamplesatlab`,
                    SUM((`lablogs`.`enteredsamplesatsite`)) AS `enteredsamplesatsite`,
                    SUM((`lablogs`.`enteredreceivedsameday`)) AS `enteredreceivedsameday`,
                    SUM((`lablogs`.`enterednotreceivedsameday`)) AS `enterednotreceivedsameday`,
                    SUM((`lablogs`.`abbottinprocess`)) AS `abbottinprocess`,
                    SUM((`lablogs`.`panthainprocess`)) AS `panthainprocess`,
                    SUM((`lablogs`.`rocheinprocess`)) AS `rocheinprocess`,
                    SUM((`lablogs`.`abbottprocessed`)) AS `abbottprocessed`,
                    SUM((`lablogs`.`panthaprocessed`)) AS `panthaprocessed`,
                    SUM((`lablogs`.`rocheprocessed`)) AS `rocheprocessed`,
                    SUM((`lablogs`.`yeartodate`)) AS `yeartodate`,
                    SUM((`lablogs`.`monthtodate`)) AS `monthtodate`
                FROM `lablogs`

                WHERE 1 ";
  
                SET @QUERY = CONCAT(@QUERY, " AND DATE(logdate) = (SELECT MAX(logdate) from lablogs WHERE testtype = '",filter_type,"') AND testtype = '",filter_type,"'");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_live_lab_samples` (IN `filter_type` INT(11), IN `filter_lab` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    SUM((`lablogs`.`oneweek`)) AS `oneweek`,
                    SUM((`lablogs`.`twoweeks`)) AS `twoweeks`,
                    SUM((`lablogs`.`threeweeks`)) AS `threeweeks`,
                    SUM((`lablogs`.`aboveamonth`)) AS `aboveamonth`
                FROM `lablogs`

                WHERE 1 ";

                IF (filter_lab != 0 && filter_lab != '') THEN
                  SET @QUERY = CONCAT(@QUERY, " AND `lab` = '",filter_lab,"' ");
                END IF;
  
                SET @QUERY = CONCAT(@QUERY, " AND DATE(logdate) = (SELECT MAX(logdate) from lablogs WHERE testtype = '",filter_type,"') AND testtype = '",filter_type,"'");
               
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_national_sustxfail_age` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `ag`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`,
                    (SUM(`Undetected`+`less1000`)/(SUM(`Undetected`+`less1000`)+SUM(`less5000`+`above5000`))) AS `pecentage`
                FROM vl_national_age `vna`
                LEFT JOIN agecategory `ag`
                    ON ag.ID = vna.age

                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " AND ag.ID NOT BETWEEN '1' AND '5' GROUP BY `name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_national_sustxfail_gender` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `g`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`
                FROM vl_national_gender `vng`
                LEFT JOIN gender `g`
                    ON g.ID = vng.gender 
                WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " GROUP BY `g`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_national_sustxfail_justification` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `vj`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`,
                    (SUM(`Undetected`+`less1000`)/(SUM(`Undetected`+`less1000`)+SUM(`less5000`+`above5000`))) AS `pecentage`
                FROM vl_national_justification `vnj`
                LEFT JOIN viraljustifications `vj`
                    ON vj.ID = vnj.justification
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vj`.`name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_national_yearly_summary` ()  BEGIN
  SET @QUERY =    "SELECT
                    `cs`.`year`, `cs`.`month`, 
                    SUM(`cs`.`Undetected` + `cs`.`less1000`) AS `suppressed`, 
                    SUM(`cs`.`above5000` + `cs`.`less5000`) AS `nonsuppressed`
                FROM `vl_national_summary` `cs`
                WHERE 1  ";
    
      SET @QUERY = CONCAT(@QUERY, "  GROUP BY `cs`.`year`,`cs`.`month` ");

     
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `cs`.`year` ASC ,`cs`.`month`");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_national_yearly_trends` ()  BEGIN
  SET @QUERY =    "SELECT
                    `cs`.`year`, `cs`.`month`, 
                    SUM(`cs`.`Undetected` + `cs`.`less1000`) AS `suppressed`, 
                    SUM(`cs`.`above5000` + `cs`.`less5000`) AS `nonsuppressed`, 
                    SUM(`cs`.`received`) AS `received`, 
                    SUM(`cs`.`rejected`) AS `rejected`,
                    AVG(`cs`.`tat4`) AS `tat4`
                FROM `vl_national_summary` `cs`
                WHERE 1  ";
    
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `cs`.`month`, `cs`.`year` ");

     
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `cs`.`year` DESC, `cs`.`month` ASC ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_age_gender` (IN `P_Id` INT(11), IN `A_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`maletest`) AS `maletest`,
        SUM(`femaletest`) AS `femaletest`,
        SUM(`nogendertest`) AS `nodata`
    FROM `vl_partner_age`
    WHERE 1 ";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `age` = '",A_id,"' AND `partner` = '",P_Id,"' ");


     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_age_outcomes` (IN `P_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        `ac`.`name`, 
                        SUM(`vna`.`less5000`+`vna`.`above5000`) AS `nonsuppressed`, 
                        SUM(`vna`.`Undetected`+`vna`.`less1000`) AS `suppressed` 
                        FROM `vl_partner_age` `vna`
                        LEFT JOIN `agecategory` `ac` 
                        ON `vna`.`age` = `ac`.`ID`
                    WHERE `ac`.`ID` NOT BETWEEN '1' AND '5'";

 
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " AND `vna`.`partner` = '",P_Id,"' GROUP BY `name` ORDER BY `ac`.`ID` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_age_sample_types` (IN `P_Id` INT(11), IN `A_id` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
          `month`,
          `year`,
          SUM(`edta`) AS `edta`,
          SUM(`dbs`) AS `dbs`,
          SUM(`plasma`) AS `plasma`,
          SUM(`Undetected`+`less1000`) AS `suppressed`,
          SUM(`Undetected`+`less1000`+`less5000`+`above5000`) AS `tests`,
          SUM((`Undetected`+`less1000`)*100/(`Undetected`+`less1000`+`less5000`+`above5000`)) AS `suppression` 

    FROM `vl_partner_age`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `age` = '",A_id,"' AND `partner` = '",P_Id,"' AND `year` = '",filter_year,"' GROUP BY `month` ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_age_suppression` (IN `A_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        partners.name,
        SUM(`Undetected` + `less1000`) AS `suppressed`, 
        SUM(`above5000` + `less5000`) AS `nonsuppressed`
    FROM `vl_partner_age`
    LEFT JOIN partners 
      ON partners.ID = vl_partner_age.partner
    WHERE 1 ";
  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `age` = '",A_id,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `name` ORDER BY `suppressed` desc ");
    

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_age_vl_outcomes` (IN `P_id` INT(11), IN `A_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`baseline`) AS `baseline`, 
        SUM(`baselinesustxfail`) AS `baselinesustxfail`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`tests`) AS `alltests`, 
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`, 
        SUM(`repeattests`) AS `repeats`, 
        SUM(`invalids`) AS `invalids`
    FROM `vl_partner_age`
    WHERE 1 ";
 

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `age` = '",A_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_county_age_outcomes` (IN `P_Id` INT(11), IN `filter_age` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `c`.`name`,
                    SUM(`vpa`.`undetected`+`vpa`.`less1000`) AS `suppressed`,
                    SUM(`vpa`.`less5000`+`vpa`.`above5000`) AS `nonsuppressed`
                  FROM vl_partner_age vpa
                  LEFT JOIN view_facilitys vf
                    ON vf.partner = vpa.partner
                  LEFT JOIN countys c
                    ON c.ID = vf.county
                  WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " AND `age` = '",filter_age,"' AND `vpa`.`partner` = '",P_Id,"' GROUP BY `c`.`name` ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_county_details` (IN `P_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `countys`.`name` AS `county`, 
                    COUNT(DISTINCT `view_facilitys`.`ID`) AS `facilities`,
                    SUM(`vl_site_summary`.`received`) AS `received`, 
                    SUM(`vl_site_summary`.`rejected`) AS `rejected`,  
                    SUM(`vl_site_summary`.`invalids`) AS `invalids`,
                    SUM(`vl_site_summary`.`alltests`) AS `alltests`,  
                    SUM(`vl_site_summary`.`Undetected`) AS `undetected`,  
                    SUM(`vl_site_summary`.`less1000`) AS `less1000`,  
                    SUM(`vl_site_summary`.`less5000`) AS `less5000`,  
                    SUM(`vl_site_summary`.`above5000`) AS `above5000`,
                    SUM(`vl_site_summary`.`baseline`) AS `baseline`,
                    SUM(`vl_site_summary`.`baselinesustxfail`) AS `baselinesustxfail`,
                    SUM(`vl_site_summary`.`confirmtx`) AS `confirmtx`,
                    SUM(`vl_site_summary`.`confirm2vl`) AS `confirm2vl`
                     FROM `vl_site_summary` 
                  LEFT JOIN `view_facilitys` ON `vl_site_summary`.`facility` = `view_facilitys`.`ID` 
                  LEFT JOIN `countys` ON `view_facilitys`.`county` = `countys`.`ID`  WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`partner` = '",P_id,"' GROUP BY `view_facilitys`.`county` ORDER BY `alltests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_county_regimen_outcomes` (IN `P_Id` INT(11), IN `filter_regimen` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `c`.`name`,
                    SUM(`vcr`.`undetected`+`vcr`.`less1000`) AS `suppressed`,
                    SUM(`vcr`.`less5000`+`vcr`.`above5000`) AS `nonsuppressed` 
                FROM `vl_partner_regimen` `vcr`
                    LEFT JOIN view_facilitys vf
                    ON vf.partner = vcr.partner
                  LEFT JOIN countys c
                    ON c.ID = vf.county
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `regimen` = '",filter_regimen,"' AND `vcr`.`partner` = '",P_Id,"' GROUP BY `c`.`name` ORDER BY `suppressed` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_regimen_age` (IN `P_id` INT(11), IN `R_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`noage`) AS `noage`,
        SUM(`less2`) AS `less2`,
        SUM(`less9`) AS `less9`,
        SUM(`less14`) AS `less14`,
        SUM(`less19`) AS `less19`,
        SUM(`less24`) AS `less24`,
        SUM(`over25`) AS `over25`
    FROM `vl_partner_regimen`
    WHERE 1 ";



    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `regimen` = '",R_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_regimen_gender` (IN `P_id` INT(11), IN `R_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`maletest`) AS `maletest`,
        SUM(`femaletest`) AS `femaletest`,
        SUM(`nogendertest`) AS `nodata`
    FROM `vl_partner_regimen`
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `regimen` = '",R_id,"' ");

   
     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_regimen_outcomes` (IN `P_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`vp`.`name`, 
						SUM(`vnr`.`less5000`+`vnr`.`above5000`) AS `nonsuppressed`, 
						SUM(`vnr`.`Undetected`+`vnr`.`less1000`) AS `suppressed` 
						FROM `vl_partner_regimen` `vnr`
						LEFT JOIN `viralprophylaxis` `vp` 
						ON `vnr`.`regimen` = `vp`.`ID`
					WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_Id,"' GROUP BY `name` ORDER BY `suppressed` DESC, `nonsuppressed` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_regimen_sample_types` (IN `P_id` INT(11), IN `R_id` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
          `month`,
          `year`,
          SUM(`edta`) AS `edta`,
          SUM(`dbs`) AS `dbs`,
          SUM(`plasma`) AS `plasma`,
          SUM(`Undetected`+`less1000`) AS `suppressed`,
          SUM(`Undetected`+`less1000`+`less5000`+`above5000`) AS `tests`,
          SUM((`Undetected`+`less1000`)*100/(`Undetected`+`less1000`+`less5000`+`above5000`)) AS `suppression`  
    FROM `vl_partner_regimen`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `regimen` = '",R_id,"' AND `year` = '",filter_year,"' GROUP BY `month` ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_regimen_vl_outcomes` (IN `P_id` INT(11), IN `R_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
       SUM(`baseline`) AS `baseline`, 
        SUM(`baselinesustxfail`) AS `baselinesustxfail`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`tests`) AS `alltests`, 
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`, 
        SUM(`repeattests`) AS `repeats`, 
        SUM(`invalids`) AS `invalids`
    FROM `vl_partner_regimen`
    WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `partner` = '",P_id,"' AND `regimen` = '",R_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_shortcodes` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    COUNT(shortcodequeries.ID) AS `counts`, 
                    view_facilitys.partner, 
                    partners.name
                  FROM shortcodequeries 
                  LEFT JOIN view_facilitys 
                    ON shortcodequeries.mflcode = view_facilitys.facilitycode 
                  LEFT JOIN partners 
                    ON partners.ID = view_facilitys.partner
                  WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived) BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived) >= '",from_month,"')  OR (YEAR(shortcodequeries.datereceived) = '",to_year,"' AND MONTH(shortcodequeries.datereceived) <= '",to_month,"') OR (YEAR(shortcodequeries.datereceived) > '",filter_year,"' AND YEAR(shortcodequeries.datereceived) < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived)='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' ");
    END IF;

    IF (C_id != 0 && C_id != '') THEN
        SET @QUERY = CONCAT(@QUERY, "  AND view_facilitys.county = '",C_id,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY view_facilitys.partner ORDER BY COUNT(shortcodequeries.ID) DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_yearly_summary` (IN `P_id` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `cs`.`year`, `cs`.`month`, 
                    SUM(`cs`.`Undetected` + `cs`.`less1000`) AS `suppressed`, 
                    SUM(`cs`.`above5000` + `cs`.`less5000`) AS `nonsuppressed`
                FROM `vl_partner_summary` `cs`
                WHERE 1  ";

      IF (P_id != 0 && P_id != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `cs`.`partner` = '",P_id,"' ");
      END IF;  

    
      SET @QUERY = CONCAT(@QUERY, "  GROUP BY `cs`.`year`,`cs`.`month` ");

     
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `cs`.`year` ASC,`cs`.`month`  ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_partner_yearly_trends` (IN `P_id` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `cs`.`year`, `cs`.`month`, 
                    SUM(`cs`.`Undetected` + `cs`.`less1000`) AS `suppressed`, 
                    SUM(`cs`.`above5000` + `cs`.`less5000`) AS `nonsuppressed`, 
                    SUM(`cs`.`received`) AS `received`, 
                    SUM(`cs`.`rejected`) AS `rejected`,
                    SUM(`cs`.`tat4`) AS `tat4`
                FROM `vl_partner_summary` `cs`
                WHERE 1  ";

      IF (P_id != 0 && P_id != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `cs`.`partner` = '",P_id,"' ");
      END IF;  

    
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `cs`.`month`, `cs`.`year` ");

     
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `cs`.`year` DESC, `cs`.`month` ASC ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_pmtct` (IN `Pm_ID` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11), IN `national` INT(11), IN `county` INT(11), IN `partner` INT(11), IN `subcounty` INT(11), IN `site` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
                    `pt`.`name` AS `pmtcttype`,
                    SUM(`vcs`.`rejected`) AS `rejected`,  
                    SUM(`vcs`.`invalids`) AS `invalids`,  
                    SUM(`vcs`.`Undetected`) AS `undetected`,  
                    SUM(`vcs`.`less1000`) AS `less1000`,  
                    SUM(`vcs`.`less5000`) AS `less5000`,  
                    SUM(`vcs`.`above5000`) AS `above5000`,
                    SUM(`vcs`.`confirmtx`) AS `confirmtx`,
                    SUM(`vcs`.`confirm2vl`) AS `confirm2vl`,
                    SUM(`vcs`.`baseline`) AS `baseline`,
                    SUM(`vcs`.`baselinesustxfail`) AS `baselinesustxfail`
                  ";

    IF (national != 0 && national != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_national_pmtct` `vcs`
                  JOIN `viralpmtcttype` `pt` ON `vcs`.`pmtcttype` = `pt`.`ID`  WHERE 1 ");
    END IF;
    IF (county != 0 && county != '') THEN
      SET @QUERY = CONCAT(@QUERY, " ,
                  `ac`.`name` FROM `vl_county_pmtct` `vcs`
                  JOIN `viralpmtcttype` `pt` ON `vcs`.`pmtcttype` = `pt`.`ID` JOIN `countys` `ac` ON `ac`.`ID` = `vcs`.`county`  WHERE `vcs`.`county` = '",county,"' ");
    END IF;
    IF (partner != 0 && partner != '') THEN
      SET @QUERY = CONCAT(@QUERY, " ,
                  `ac`.`name` FROM `vl_partner_pmtct` `vcs`
                  JOIN `viralpmtcttype` `pt` ON `vcs`.`pmtcttype` = `pt`.`ID` JOIN `partners` `ac` ON `ac`.`ID` = `vcs`.`partner`  WHERE `vcs`.`partner` = '",partner,"' ");
    END IF;
    IF (subcounty != 0 && subcounty != '') THEN
      SET @QUERY = CONCAT(@QUERY, " ,
                  `ac`.`name` FROM `vl_subcounty_pmtct` `vcs`
                  JOIN `viralpmtcttype` `pt` ON `vcs`.`pmtcttype` = `pt`.`ID` JOIN `districts` `ac` ON `ac`.`ID` = `vcs`.`subcounty`  WHERE `vcs`.`subcounty` = '",subcounty,"' ");
    END IF;
    IF (site != 0 && site != '') THEN
      SET @QUERY = CONCAT(@QUERY, " ,
                  `ac`.`name` FROM `vl_site_pmtct` `vcs`
                  JOIN `viralpmtcttype` `pt` ON `vcs`.`pmtcttype` = `pt`.`ID` JOIN `view_facilitys` `ac` ON `ac`.`ID` = `vcs`.`facility`  WHERE `vcs`.`facility` = '",site,"' ");
    END IF;

    IF (Pm_ID != 0 && Pm_ID != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `pmtcttype` = '",Pm_ID,"' ");
    END IF;

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `pt`.`name` ORDER BY `pt`.`name` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_pmtct_breakdown` (IN `Pm_ID` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11), IN `county` INT(11), IN `subcounty` INT(11), IN `partner` INT(11), IN `site` INT(11))  BEGIN
  SET @QUERY =    "SELECT
          `com`.`name`,
  				SUM(`vcs`.`rejected`) AS `rejected`,  
          SUM(`vcs`.`invalids`) AS `invalids`,  
          SUM(`vcs`.`Undetected`) AS `undetected`,  
          SUM(`vcs`.`less1000`) AS `less1000`,  
          SUM(`vcs`.`less5000`) AS `less5000`,  
          SUM(`vcs`.`above5000`) AS `above5000`,
          SUM(`vcs`.`confirmtx`) AS `confirmtx`,
          SUM(`vcs`.`confirm2vl`) AS `confirm2vl`,
          SUM(`vcs`.`baseline`) AS `baseline`,
          SUM(`vcs`.`baselinesustxfail`) AS `baselinesustxfail`,
          SUM(`vcs`.`undetected`+`vcs`.`less1000`+`vcs`.`less5000`+`vcs`.`above5000`) AS `routine` ";

    IF (county != 0 || county != '') THEN
        SET @QUERY = CONCAT(@QUERY, "FROM `vl_county_pmtct` `vcs` LEFT JOIN `countys` `com` ON `vcs`.`county` = `com`.`ID` ");
    END IF;           
		IF (subcounty != 0 || subcounty != '') THEN
        SET @QUERY = CONCAT(@QUERY, "FROM `vl_subcounty_pmtct` `vcs` LEFT JOIN `districts` `com` ON `vcs`.`subcounty` = `com`.`ID` ");
    END IF;
    IF (partner != 0 || partner != '') THEN
        SET @QUERY = CONCAT(@QUERY, "FROM `vl_partner_pmtct` `vcs` LEFT JOIN `partners` `com` ON `vcs`.`partner` = `com`.`ID` ");
    END IF;
    IF (site != 0 || site != '') THEN
        SET @QUERY = CONCAT(@QUERY, "FROM `vl_site_pmtct` `vcs` LEFT JOIN `view_facilitys` `com` ON `vcs`.`facility` = `com`.`ID` ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " WHERE `vcs`.`pmtcttype` = '",Pm_ID,"' ");

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `com`.`ID` ORDER BY `routine` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_pmtct_grouped` (IN `Pm_ID` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11), IN `county` INT(11), IN `subcounty` INT(11), IN `partner` INT(11))  BEGIN
  SET @QUERY =    "SELECT
          `com`.`name`,
  				SUM(`vcs`.`rejected`) AS `rejected`,  
          SUM(`vcs`.`invalids`) AS `invalids`,  
          SUM(`vcs`.`Undetected`) AS `undetected`,  
          SUM(`vcs`.`less1000`) AS `less1000`,  
          SUM(`vcs`.`less5000`) AS `less5000`,  
          SUM(`vcs`.`above5000`) AS `above5000`,
          SUM(`vcs`.`confirmtx`) AS `confirmtx`,
          SUM(`vcs`.`confirm2vl`) AS `confirm2vl`,
          SUM(`vcs`.`baseline`) AS `baseline`,
          SUM(`vcs`.`baselinesustxfail`) AS `baselinesustxfail`,
          SUM(`vcs`.`undetected`+`vcs`.`less1000`+`vcs`.`less5000`+`vcs`.`above5000`) AS `routine` 
          FROM `vl_site_pmtct` `vcs` LEFT JOIN `view_facilitys` `com` ON `vcs`.`facility` = `com`.`ID` WHERE 1 ";

    IF (county != 0 || county != '') THEN
        SET @QUERY = CONCAT(@QUERY, " AND `com`.`county` = '",county,"' ");
    END IF;           
		IF (subcounty != 0 || subcounty != '') THEN
        SET @QUERY = CONCAT(@QUERY, " AND `com`.`district` = '",subcounty,"' ");
    END IF;
    IF (partner != 0 || partner != '') THEN
        SET @QUERY = CONCAT(@QUERY, " AND `com`.`partner` = '",partner,"' ");
    END IF;
    IF (Pm_ID != 0 || Pm_ID != '') THEN
        SET @QUERY = CONCAT(@QUERY, " AND `vcs`.`pmtcttype` = '",Pm_ID,"' ");
    END IF;

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `com`.`ID` ORDER BY `routine` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_pmtct_suppression` (IN `Pm_id` INT(11), IN `filter_year` INT(11), IN `filter_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11), IN `national` INT(11), IN `county` INT(11), IN `partner` INT(11), IN `subcounty` INT(11), IN `site` INT(11))  BEGIN
  SET @QUERY =    "SELECT
          `vcs`.`month`,
          `vcs`.`year`,
          SUM(`vcs`.`undetected`+`vcs`.`less1000`) AS `suppressed`,
          SUM(`vcs`.`less5000`+`vcs`.`above5000`) AS `nonsuppressed`,
          SUM(`vcs`.`undetected`+`vcs`.`less1000`+`vcs`.`less5000`+`vcs`.`above5000`) AS `tests`,
          SUM(`vcs`.`undetected`+`vcs`.`less1000`)*100/SUM(`vcs`.`undetected`+`vcs`.`less1000`+`vcs`.`less5000`+`vcs`.`above5000`) AS `suppression`
                  ";

    IF (national != 0 && national != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_national_pmtct` `vcs`
                  JOIN `viralpmtcttype` `pt` ON `vcs`.`pmtcttype` = `pt`.`ID`  WHERE 1 ");
    END IF;
    IF (county != 0 && county != '') THEN
      SET @QUERY = CONCAT(@QUERY, " ,
                  `ac`.`name` FROM `vl_county_pmtct` `vcs`
                  JOIN `viralpmtcttype` `pt` ON `vcs`.`pmtcttype` = `pt`.`ID` JOIN `countys` `ac` ON `ac`.`ID` = `vcs`.`county`  WHERE `vcs`.`county` = '",county,"' ");
    END IF;
    IF (partner != 0 && partner != '') THEN
      SET @QUERY = CONCAT(@QUERY, " ,
                  `ac`.`name` FROM `vl_partner_pmtct` `vcs`
                  JOIN `viralpmtcttype` `pt` ON `vcs`.`pmtcttype` = `pt`.`ID` JOIN `partners` `ac` ON `ac`.`ID` = `vcs`.`partner`  WHERE `vcs`.`partner` = '",partner,"' ");
    END IF;
    IF (subcounty != 0 && subcounty != '') THEN
      SET @QUERY = CONCAT(@QUERY, " ,
                  `ac`.`name` FROM `vl_subcounty_pmtct` `vcs`
                  JOIN `viralpmtcttype` `pt` ON `vcs`.`pmtcttype` = `pt`.`ID` JOIN `districts` `ac` ON `ac`.`ID` = `vcs`.`subcounty`  WHERE `vcs`.`subcounty` = '",subcounty,"' ");
    END IF;
    IF (site != 0 && site != '') THEN
      SET @QUERY = CONCAT(@QUERY, " ,
                  `ac`.`name` FROM `vl_site_pmtct` `vcs`
                  JOIN `viralpmtcttype` `pt` ON `vcs`.`pmtcttype` = `pt`.`ID` JOIN `view_facilitys` `ac` ON `ac`.`ID` = `vcs`.`facility`  WHERE `vcs`.`facility` = '",site,"' ");
    END IF;

    IF (Pm_id != 0 && Pm_id != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `pmtcttype` = '",Pm_id,"' ");
    END IF;

    IF (filter_month !=0 && filter_month != '') THEN
      SET @QUERY = CONCAT(@QUERY, " AND `year` BETWEEN '",filter_year,"' AND '",to_year,"' AND `month` BETWEEN '",filter_month,"' AND '",to_month,"' ");
    ELSE 
      SET @QUERY = CONCAT(@QUERY, " AND `year` BETWEEN '",filter_year,"' AND '",to_year,"' ");
    END IF;
    
    SET @QUERY = CONCAT(@QUERY, " GROUP BY `year`, `month` ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_regimens_breakdowns_outcomes` (IN `filter_regimen` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11), IN `county` INT(11), IN `partner` INT(11), IN `subcounty` INT(11), IN `site` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `c`.`name`,
                    SUM(`vca`.`undetected`+`vca`.`less1000`) AS `suppressed`,
                    SUM(`vca`.`less5000`+`vca`.`above5000`) AS `nonsuppressed`, 
                    SUM(`vca`.`undetected`+`vca`.`less1000`+`vca`.`less5000`+`vca`.`above5000`) AS `total`,
                    ((SUM(`vca`.`undetected`+`vca`.`less1000`)/SUM(`vca`.`undetected`+`vca`.`less1000`+`vca`.`less5000`+`vca`.`above5000`))*100) AS `percentage` ";

    IF (county != 0 && county != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_county_regimen` `vca` JOIN `countys` `c` ON `vca`.`county` = `c`.`ID` WHERE 1 ");
    END IF;
    IF (partner != 0 && partner != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_partner_regimen` `vca` JOIN `partners` `c` ON `vca`.`partner` = `c`.`ID` WHERE 1 ");
    END IF;
    IF (subcounty != 0 && subcounty != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_subcounty_regimen` `vca` JOIN `districts` `c` ON `vca`.`subcounty` = `c`.`ID` WHERE 1 ");
    END IF;
    IF (site != 0 && site != '') THEN
      SET @QUERY = CONCAT(@QUERY, " FROM `vl_site_regimen` `vca` JOIN `facilitys` `c` ON `vca`.`facility` = `c`.`ID` WHERE 1 ");
    END IF;

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " AND `regimen` = '",filter_regimen,"' GROUP BY `c`.`ID` ORDER BY `percentage` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_regimen_age` (IN `R_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`noage`) AS `noage`,
        SUM(`less2`) AS `less2`,
        SUM(`less9`) AS `less9`,
        SUM(`less14`) AS `less14`,
        SUM(`less19`) AS `less19`,
        SUM(`less24`) AS `less24`,
        SUM(`over25`) AS `over25`
    FROM `vl_national_regimen`
    WHERE 1 ";



    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `regimen` = '",R_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_regimen_gender` (IN `R_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`maletest`) AS `maletest`,
        SUM(`femaletest`) AS `femaletest`,
        SUM(`nogendertest`) AS `nodata`
    FROM `vl_national_regimen`
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `regimen` = '",R_id,"' ");

   
     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_regimen_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`vp`.`name`, 
						SUM(`vnr`.`less5000`+`vnr`.`above5000`) AS `nonsuppressed`, 
						SUM(`vnr`.`Undetected`+`vnr`.`less1000`) AS `suppressed` 
						FROM `vl_national_regimen` `vnr`
						LEFT JOIN `viralprophylaxis` `vp` 
						ON `vnr`.`regimen` = `vp`.`ID`
					WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `name` ORDER BY `suppressed` DESC, `nonsuppressed` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_regimen_vl_outcomes` (IN `R_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`baseline`) AS `baseline`, 
        SUM(`baselinesustxfail`) AS `baselinesustxfail`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`tests`) AS `alltests`, 
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`, 
        SUM(`repeattests`) AS `repeats`, 
        SUM(`invalids`) AS `invalids`
    FROM `vl_national_regimen`
    WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `regimen` = '",R_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_samples_age` (IN `S_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`noage`) AS `noage`,
        SUM(`less2`) AS `less2`,
        SUM(`less9`) AS `less9`,
        SUM(`less14`) AS `less14`,
        SUM(`less19`) AS `less19`,
        SUM(`less24`) AS `less24`,
        SUM(`over25`) AS `over25`
    FROM `vl_national_sampletype`
    WHERE 1 ";



    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `sampletype` = '",S_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_samples_gender` (IN `S_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`maletest`) AS `maletest`,
        SUM(`femaletest`) AS `femaletest`
    FROM `vl_national_sampletype`
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `sampletype` = '",S_id,"' ");

   
     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_samples_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`vs`.`name`, 
						SUM(`vns`.`less5000`+`vns`.`above5000`) AS `nonsuppressed`, 
						SUM(`vns`.`Undetected`+`vns`.`less1000`) AS `suppressed` 
						FROM `vl_national_sampletype` `vns`
						LEFT JOIN `viralsampletypedetails` `vs` 
						ON `vns`.`sampletype` = `vs`.`ID`
					WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `name` ORDER BY `suppressed` DESC, `nonsuppressed` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_samples_vl_outcomes` (IN `S_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`baseline`) AS `baseline`, 
        SUM(`baselinesustxfail`) AS `baselinesustxfail`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`tests`) AS `alltests`, 
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`, 
        SUM(`repeattests`) AS `repeats`, 
        SUM(`invalids`) AS `invalids`
    FROM `vl_national_sampletype`
    WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `sampletype` = '",S_id,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_sample_summary` (IN `S_id` INT(11), IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
            `year`, 
            `month`, 
            sum((`undetected`+`less1000`)) AS `suppressed`,
            sum((`less5000`+`above5000`)) AS `nonsuppressed`, 
            sum((`undetected`+`less1000`)*100/(`less5000`+`above5000`+`undetected`+`less1000`)) AS `percentage`  
            FROM `vl_national_sampletype`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `sampletype` = '",S_id,"' AND `year` BETWEEN '",from_year,"' AND '",to_year,"' GROUP BY `year`,`month`  ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_sample_types` (IN `R_id` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
          `month`,
          `year`,
          SUM(`edta`) AS `edta`,
          SUM(`dbs`) AS `dbs`,
          SUM(`plasma`) AS `plasma`,
          SUM(`Undetected`+`less1000`) AS `suppressed`,
          SUM(`Undetected`+`less1000`+`less5000`+`above5000`) AS `tests`,
          SUM((`Undetected`+`less1000`)*100/(`Undetected`+`less1000`+`less5000`+`above5000`)) AS `suppression` 
    FROM `vl_national_regimen`
    WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `regimen` = '",R_id,"' AND `year` = '",filter_year,"' GROUP BY `month` ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_shortcodes_requests` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
					COUNT(DISTINCT shortcodequeries.`ID`) AS `count`,
					MONTH(shortcodequeries.`datereceived`) AS `month`,
					YEAR(shortcodequeries.`datereceived`) AS `year`,
					MONTHNAME(shortcodequeries.`datereceived`) AS `monthname`
				FROM shortcodequeries
					LEFT JOIN view_facilitys
				ON view_facilitys.facilitycode = shortcodequeries.mflcode 
                 WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived) BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived) >= '",from_month,"')  OR (YEAR(shortcodequeries.datereceived) = '",to_year,"' AND MONTH(shortcodequeries.datereceived) <= '",to_month,"') OR (YEAR(shortcodequeries.datereceived) > '",filter_year,"' AND YEAR(shortcodequeries.datereceived) < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived)='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' ");
    END IF;

    IF (C_id != 0 && C_id != '') THEN
        SET @QUERY = CONCAT(@QUERY, "  AND view_facilitys.county = '",C_id,"' ");
    END IF;



    SET @QUERY = CONCAT(@QUERY, " GROUP BY `year` ASC,`month` ASC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_sites_shortcodes` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    COUNT(DISTINCT shortcodequeries.ID) AS `count`, 
                    view_facilitys.facilitycode, 
                    view_facilitys.name 
                  FROM shortcodequeries 
                  LEFT JOIN view_facilitys 
                    ON shortcodequeries.mflcode = view_facilitys.facilitycode 
                  WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived) BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived) >= '",from_month,"')  OR (YEAR(shortcodequeries.datereceived) = '",to_year,"' AND MONTH(shortcodequeries.datereceived) <= '",to_month,"') OR (YEAR(shortcodequeries.datereceived) > '",filter_year,"' AND YEAR(shortcodequeries.datereceived) < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived)='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' ");
    END IF;

    IF (C_id != 0 && C_id != '') THEN
        SET @QUERY = CONCAT(@QUERY, "  AND view_facilitys.county = '",C_id,"' ");
    END IF;



    SET @QUERY = CONCAT(@QUERY, " GROUP BY view_facilitys.facilitycode ORDER BY COUNT(shortcodequeries.ID) DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_site_justification` (IN `Site` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vj`.`name`,
                    SUM((`vsj`.`tests`)) AS `justifications`
                FROM `vl_site_justification` `vsj`
                JOIN `viraljustifications` `vj` 
                    ON `vsj`.`justification` = `vj`.`ID`
                WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `vsj`.`facility` = '",Site,"' ");


    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vj`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_site_justification_breakdown` (IN `justification` INT(11), IN `Site` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                        SUM(`Undetected`) AS `Undetected`,
                        SUM(`less1000`) AS `less1000`, 
                        SUM(`less5000`) AS `less5000`,
                        SUM(`above5000`) AS `above5000`
                    FROM `vl_site_justification` 
                    WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;
    
    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",Site,"' AND `justification` = '",justification,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_site_patients` (IN `filter_site` INT(11), IN `filter_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
                    SUM((`alltests`)) AS `alltests`,
                    SUM((`onevl`)) AS `onevl`,
                    SUM((`twovl`)) AS `twovl`,
                    SUM((`threevl`)) AS `threevl`,
                    SUM((`above3vl`)) AS `above3vl`,
                    SUM((`vf`.`totalartmar`)) AS `totalartmar`
                    FROM `vl_site_patient_tracking` `vspt`
                    JOIN `view_facilitys` `vf` 
                    ON `vspt`.`facility` = `vf`.`ID`
                WHERE 1";

   
    SET @QUERY = CONCAT(@QUERY, " AND `facility` = '",filter_site,"' ");

    SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_age` (IN `filter_subcounty` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        `ac`.`name`, 
        SUM(`vsa`.`tests`) AS `tests`, 
        SUM(`vsa`.`undetected`+`vsa`.`less1000`) AS `suppressed`,
        SUM(`vsa`.`less5000`+`vsa`.`above5000`) AS `nonsuppressed`
    FROM `vl_subcounty_age` `vsa`
    JOIN `agecategory` `ac`
        ON `vsa`.`age` = `ac`.`ID`
    WHERE 1 ";



    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",filter_subcounty,"' ");

    SET @QUERY = CONCAT(@QUERY, " AND `ac`.`subID` = 2 ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `ac`.`name` ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_details` (IN `filter_county` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT  
                    `countys`.`name` AS `county`,
                    `districts`.`name` AS `subcounty`,
                    AVG(`vcs`.`sitessending`) AS `sitesending`, 
                    SUM(`vcs`.`received`) AS `received`, 
                    SUM(`vcs`.`rejected`) AS `rejected`,  
                    SUM(`vcs`.`invalids`) AS `invalids`,
                    SUM(`vcs`.`alltests`) AS `alltests`,  
                    SUM(`vcs`.`Undetected`) AS `undetected`,  
                    SUM(`vcs`.`less1000`) AS `less1000`,  
                    SUM(`vcs`.`less5000`) AS `less5000`,  
                    SUM(`vcs`.`above5000`) AS `above5000`,
                    SUM(`vcs`.`baseline`) AS `baseline`,
                    SUM(`vcs`.`baselinesustxfail`) AS `baselinesustxfail`,
                    SUM(`vcs`.`confirmtx`) AS `confirmtx`,
                    SUM(`vcs`.`confirm2vl`) AS `confirm2vl` FROM `vl_subcounty_summary` `vcs`
                   JOIN `districts` ON `vcs`.`subcounty` = `districts`.`ID`
                  JOIN `countys` ON `countys`.`ID` = `districts`.`county`
                     WHERE 1 ";

    
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

     IF (filter_county != 0 && filter_county != '') THEN
        SET @QUERY = CONCAT(@QUERY, " AND `districts`.`county` = '",filter_county,"' ");
     END IF;

    SET @QUERY = CONCAT(@QUERY, "  GROUP BY `districts`.`ID` ORDER BY `alltests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_gender` (IN `filter_subcounty` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        `name`,
        SUM(`tests`) AS `tests`, 
        SUM(`Undetected` + `less1000`) AS `suppressed`, 
        SUM(`less5000` + `above5000`) AS `nonsuppressed`

    FROM `vl_subcounty_gender`
    JOIN `gender` 
                    ON `vl_subcounty_gender`.`gender` = `gender`.`ID`
    WHERE 1 ";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",filter_subcounty,"' ");

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `name` ");



     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_outcomes` (IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
						`d`.`name`, 
						SUM(`vss`.`less5000`+`vss`.`above5000`) AS `nonsuppressed`, 
						SUM(`vss`.`Undetected`+`vss`.`less1000`) AS `suppressed` 
						FROM `vl_subcounty_summary` `vss`
						JOIN `districts` `d` 
						ON `vss`.`subcounty` = `d`.`ID`
					WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `name` ORDER BY `suppressed` DESC, `nonsuppressed` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_sample_types` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
					`month`,
					`year`,
					SUM(`edta`) AS `edta`,
 					SUM(`dbs`) AS `dbs`,
 					SUM(`plasma`) AS `plasma`,
					SUM(`alledta`) AS `alledta`,
 					SUM(`alldbs`) AS `alldbs`,
 					SUM(`allplasma`) AS `allplasma`,
					SUM(`Undetected`+`less1000`) AS `suppressed`,
					SUM(`Undetected`+`less1000`+`less5000`+`above5000`) AS `tests`,
					SUM((`Undetected`+`less1000`)*100/(`Undetected`+`less1000`+`less5000`+`above5000`)) AS `suppression`
				FROM `vl_subcounty_summary`
                WHERE 1";

    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",C_id,"' AND (`year` = '",filter_year,"'  OR  `year` = '",to_year,"') GROUP BY `year`, `month` ORDER BY `year` ASC, `month` ");
    
    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_shortcodes` (IN `C_id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    COUNT(shortcodequeries.ID) AS `counts`, 
                    view_facilitys.district, 
                    districts.name
                  FROM shortcodequeries 
                  LEFT JOIN view_facilitys 
                    ON shortcodequeries.mflcode = view_facilitys.facilitycode 
                  LEFT JOIN districts 
                    ON districts.ID = view_facilitys.district
                  WHERE 1";


    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived) BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived) >= '",from_month,"')  OR (YEAR(shortcodequeries.datereceived) = '",to_year,"' AND MONTH(shortcodequeries.datereceived) <= '",to_month,"') OR (YEAR(shortcodequeries.datereceived) > '",filter_year,"' AND YEAR(shortcodequeries.datereceived) < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' AND MONTH(shortcodequeries.datereceived)='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND YEAR(shortcodequeries.datereceived) = '",filter_year,"' ");
    END IF;

    IF (C_id != 0 && C_id != '') THEN
        SET @QUERY = CONCAT(@QUERY, "  AND view_facilitys.county = '",C_id,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY view_facilitys.district ORDER BY COUNT(shortcodequeries.ID) DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_sites_details` (IN `filter_subcounty` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `view_facilitys`.`facilitycode` AS `MFLCode`, 
                    `view_facilitys`.`name`, 
                    `countys`.`name` AS `county`, 
                    `districts`.`name` AS `subcounty`,
                    SUM(`vl_site_summary`.`received`) AS `received`, 
                    SUM(`vl_site_summary`.`rejected`) AS `rejected`,  
                    SUM(`vl_site_summary`.`invalids`) AS `invalids`,
                    SUM(`vl_site_summary`.`alltests`) AS `alltests`,  
                    SUM(`vl_site_summary`.`Undetected`) AS `undetected`,  
                    SUM(`vl_site_summary`.`less1000`) AS `less1000`,  
                    SUM(`vl_site_summary`.`less5000`) AS `less5000`,  
                    SUM(`vl_site_summary`.`above5000`) AS `above5000`,
                    SUM(`vl_site_summary`.`baseline`) AS `baseline`,
                    SUM(`vl_site_summary`.`baselinesustxfail`) AS `baselinesustxfail`,
                    SUM(`vl_site_summary`.`confirmtx`) AS `confirmtx`,
                    SUM(`vl_site_summary`.`confirm2vl`) AS `confirm2vl` FROM `vl_site_summary` 
                  LEFT JOIN `view_facilitys` ON `vl_site_summary`.`facility` = `view_facilitys`.`ID` 
                  LEFT JOIN `districts` ON `view_facilitys`.`district` = `districts`.`ID` 
                  LEFT JOIN `countys` ON `view_facilitys`.`county` = `countys`.`ID`  
                  WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `view_facilitys`.`district` = '",filter_subcounty,"' GROUP BY `view_facilitys`.`ID` ORDER BY `alltests` DESC ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_sustxfail_age` (IN `SC_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `ag`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`,
                    (SUM(`Undetected`+`less1000`)/(SUM(`Undetected`+`less1000`)+SUM(`less5000`+`above5000`))) AS `pecentage`
                FROM vl_subcounty_age `vsa`
                LEFT JOIN agecategory `ag`
                    ON ag.ID = vsa.age

                WHERE 1";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " AND ag.ID NOT BETWEEN '1' AND '5' GROUP BY `name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_sustxfail_gender` (IN `SC_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =  "SELECT 
                    `g`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`
                FROM vl_subcounty_gender `vcg`
                LEFT JOIN gender `g`
                    ON g.ID = vcg.gender 
                WHERE 1";

  
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' ");
    END IF;


    SET @QUERY = CONCAT(@QUERY, " GROUP BY `g`.`name` ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_sustxfail_justification` (IN `SC_Id` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT 
                    `vj`.`name`,
                    SUM(`Undetected`+`less1000`) AS `suppressed`,
                    SUM(`less5000`+`above5000`) AS `nonsuppressed`,
                    (SUM(`Undetected`+`less1000`)/(SUM(`Undetected`+`less1000`)+SUM(`less5000`+`above5000`))) AS `pecentage`
                FROM vl_subcounty_justification `vsj`
                LEFT JOIN viraljustifications `vj`
                    ON vj.ID = vsj.justification
                WHERE 1";

   
    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",SC_Id,"' AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " GROUP BY `vj`.`name` ORDER BY `pecentage` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
    
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_subcounty_vl_outcomes` (IN `filter_subcounty` INT(11), IN `filter_year` INT(11), IN `from_month` INT(11), IN `to_year` INT(11), IN `to_month` INT(11))  BEGIN
  SET @QUERY =    "SELECT
        SUM(`baseline`) AS `baseline`, 
        SUM(`baselinesustxfail`) AS `baselinesustxfail`, 
        SUM(`confirmtx`) AS `confirmtx`,
        SUM(`confirm2vl`) AS `confirm2vl`,
        SUM(`Undetected`) AS `undetected`,
        SUM(`less1000`) AS `less1000`,
        SUM(`less5000`) AS `less5000`,
        SUM(`above5000`) AS `above5000`,
        SUM(`alltests`) AS `alltests`,
        SUM(`sustxfail`) AS `sustxfail`,
        SUM(`rejected`) AS `rejected`, 
        SUM(`repeattests`) AS `repeats`, 
        SUM(`invalids`) AS `invalids`
    FROM `vl_subcounty_summary`
    WHERE 1 ";

    IF (from_month != 0 && from_month != '') THEN
      IF (to_month != 0 && to_month != '' && filter_year = to_year) THEN
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month` BETWEEN '",from_month,"' AND '",to_month,"' ");
        ELSE IF(to_month != 0 && to_month != '' && filter_year != to_year) THEN
          SET @QUERY = CONCAT(@QUERY, " AND ((`year` = '",filter_year,"' AND `month` >= '",from_month,"')  OR (`year` = '",to_year,"' AND `month` <= '",to_month,"') OR (`year` > '",filter_year,"' AND `year` < '",to_year,"')) ");
        ELSE
            SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' AND `month`='",from_month,"' ");
        END IF;
    END IF;
    ELSE
        SET @QUERY = CONCAT(@QUERY, " AND `year` = '",filter_year,"' ");
    END IF;

    SET @QUERY = CONCAT(@QUERY, " AND `subcounty` = '",filter_subcounty,"' ");

     PREPARE stmt FROM @QUERY;
     EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_yearly_lab_summary` (IN `lab` INT(11), IN `from_year` INT(11), IN `to_year` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vls`.`year` as `year`,
                     `vls`.`month` as `month`, 
                    sum((`vls`.`Undetected` + `vls`.`less1000`)) AS `suppressed`, 
                    sum((`vls`.`above5000` + `vls`.`less5000`)) AS `nonsuppressed`
                FROM `vl_lab_summary` `vls`
                WHERE 1  ";

      IF (lab != 0 && lab != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `vls`.`lab` = '",lab,"' ");
      END IF;  

    
      SET @QUERY = CONCAT(@QUERY, "  AND `year` BETWEEN '",from_year,"' AND '",to_year,"' group by `vls`.`year`,`vls`.`month`   ORDER BY `year` ASC, `month` ");

     

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_yearly_lab_trends` (IN `lab` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `vls`.`year`, `vls`.`month`,  
                    SUM(`vls`.`Undetected` + `vls`.`less1000`) AS `suppressed`, 
                    SUM(`vls`.`above5000` + `vls`.`less5000`) AS `nonsuppressed`,
                    SUM(`vls`.`alltests`) AS `alltests`, 
                    SUM(`vls`.`received`) AS `received`, 
                    SUM(`vls`.`rejected`) AS `rejected`,
                    AVG(`vls`.`tat4`) AS `tat4`
                FROM `vl_lab_summary` `vls`
                WHERE 1  ";

      IF (lab != 0 && lab != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `vls`.`lab` = '",lab,"' ");
      END IF;  

    
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `vls`.`month`, `vls`.`year` ");

     
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `vls`.`year` DESC, `vls`.`month` ASC ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_yearly_summary` (IN `county` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `cs`.`year`, `cs`.`month`, 
                    SUM(`cs`.`Undetected` + `cs`.`less1000`) AS `suppressed`, 
                    SUM(`cs`.`above5000` + `cs`.`less5000`) AS `nonsuppressed`
                FROM `vl_county_summary` `cs`
                WHERE 1  ";

      IF (county != 0 && county != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `cs`.`county` = '",county,"' ");
      END IF;  

    
      SET @QUERY = CONCAT(@QUERY, "  GROUP BY `cs`.`year` , `cs`.`month`");

     
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `cs`.`year` ASC, `cs`.`month` ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_vl_yearly_trends` (IN `county` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `cs`.`year`, `cs`.`month`, 
                    SUM(`cs`.`Undetected` + `cs`.`less1000`) AS `suppressed`, 
                    SUM(`cs`.`above5000` + `cs`.`less5000`) AS `nonsuppressed`, 
                    SUM(`cs`.`received`) AS `received`, 
                    SUM(`cs`.`rejected`) AS `rejected`,
                    AVG(`cs`.`tat4`) AS `tat4`
                FROM `vl_county_summary` `cs`
                WHERE 1  ";

      IF (county != 0 && county != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `cs`.`county` = '",county,"' ");
      END IF;  

    
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `cs`.`month`, `cs`.`year` ");

     
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `cs`.`year` DESC, `cs`.`month` ASC ");
      

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `proc_get_yearly_summary` (IN `county` INT(11))  BEGIN
  SET @QUERY =    "SELECT
                    `cs`.`year`,  SUM(`cs`.`neg`) AS `neg`, 
                    SUM(`cs`.`pos`) AS `positive`
                FROM `county_summary` `cs`
                WHERE 1 ";

      IF (county != 0 && county != '') THEN
           SET @QUERY = CONCAT(@QUERY, " AND `cs`.`county` = '",county,"' ");
      END IF; 
    
      SET @QUERY = CONCAT(@QUERY, " GROUP BY `cs`.`year` ");
      SET @QUERY = CONCAT(@QUERY, " ORDER BY `cs`.`year` DESC ");

    PREPARE stmt FROM @QUERY;
    EXECUTE stmt;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `administrators`
--

CREATE TABLE `administrators` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_type_id` int(10) UNSIGNED NOT NULL DEFAULT '6',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `agecategory`
--

CREATE TABLE `agecategory` (
  `ID` int(10) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `subID` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `age_bands`
--

CREATE TABLE `age_bands` (
  `ID` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lower` double(10,5) UNSIGNED DEFAULT NULL,
  `upper` double(10,5) UNSIGNED DEFAULT NULL,
  `age_range_id` int(11) NOT NULL,
  `age_range` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lower_range` double(10,5) UNSIGNED DEFAULT NULL,
  `upper_range` double(10,5) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `api_usage`
--

CREATE TABLE `api_usage` (
  `id` int(11) NOT NULL,
  `userKey` varchar(100) NOT NULL,
  `apiName` varchar(100) NOT NULL,
  `noOfHits` int(11) NOT NULL DEFAULT '0',
  `hitDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `countys`
--

CREATE TABLE `countys` (
  `ID` int(32) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `CountyDHISCode` varchar(150) DEFAULT NULL,
  `CountyMFLCode` varchar(150) DEFAULT NULL,
  `CountyCoordinates` varchar(3070) DEFAULT NULL,
  `pmtctneed1617` int(50) DEFAULT NULL,
  `letter` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `countys`
--

INSERT INTO `countys` (`ID`, `name`, `CountyDHISCode`, `CountyMFLCode`, `CountyCoordinates`, `pmtctneed1617`, `letter`) VALUES
(1, 'AGNEBY-TIASSA-ME', '1', '1', NULL, NULL, NULL),
(2, 'GBOKLE-NAWA-SAN PEDRO', '2', '2', NULL, NULL, NULL),
(3, 'KABADOUGOU-BAFIN-FOLON', '3', '3', NULL, NULL, NULL),
(4, 'HAUT-SASSANDRA', '4', '4', NULL, NULL, NULL),
(5, 'GOH', '5', '5', NULL, NULL, NULL),
(6, 'BELIER', '6', '6', NULL, NULL, NULL),
(7, 'ABIDJAN 1-GRANDS PONTS', '7', '7', NULL, NULL, NULL),
(8, 'ABIDJAN 2', '8', '8', NULL, NULL, NULL),
(9, 'MARAHOUE', '9', '9', NULL, NULL, NULL),
(10, 'TONKPI', '10', '10', NULL, NULL, NULL),
(11, 'CAVALLY-GUEMON', '11', '11', NULL, NULL, NULL),
(12, 'N\'ZI-IFOU', '12', '12', NULL, NULL, NULL),
(13, 'INDENIE-DJUABLIN', '13', '13', NULL, NULL, NULL),
(14, 'PORO-TCHOLOGO-BAGOUE', '14', '14', NULL, NULL, NULL),
(15, 'LOH-DJIBOUA', '15', '15', NULL, NULL, NULL),
(16, 'SUD-COMOE', '16', '16', NULL, NULL, NULL),
(17, 'GBEKE', '17', '17', NULL, NULL, NULL),
(18, 'HAMBOL', '18', '18', NULL, NULL, NULL),
(19, 'WORODOUGOU-BERE', '19', '19', NULL, NULL, NULL),
(20, 'BOUNKANI-GONTOUGO', '20', '20', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `county_agebreakdown`
--

CREATE TABLE `county_agebreakdown` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `county` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `sixweekspos` int(10) DEFAULT NULL,
  `sixweeksneg` int(10) DEFAULT NULL,
  `sevento3mpos` int(10) DEFAULT NULL,
  `sevento3mneg` int(10) DEFAULT NULL,
  `threemto9mpos` int(10) DEFAULT NULL,
  `threemto9mneg` int(10) DEFAULT NULL,
  `ninemto18mpos` int(10) DEFAULT NULL,
  `ninemto18mneg` int(10) DEFAULT NULL,
  `above18mpos` int(10) DEFAULT NULL,
  `above18mneg` int(10) DEFAULT NULL,
  `nodatapos` int(10) DEFAULT NULL,
  `nodataneg` int(10) DEFAULT NULL,
  `less2wpos` int(10) DEFAULT NULL,
  `less2wneg` int(10) DEFAULT NULL,
  `twoto6wpos` int(10) DEFAULT NULL,
  `twoto6wneg` int(10) DEFAULT NULL,
  `sixto8wpos` int(10) DEFAULT NULL,
  `sixto8wneg` int(10) DEFAULT NULL,
  `sixmonthpos` int(10) DEFAULT NULL,
  `sixmonthneg` int(10) DEFAULT NULL,
  `ninemonthpos` int(10) DEFAULT NULL,
  `ninemonthneg` int(10) DEFAULT NULL,
  `twelvemonthpos` int(10) DEFAULT NULL,
  `twelvemonthneg` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `county_age_breakdown`
--

CREATE TABLE `county_age_breakdown` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `county` int(10) UNSIGNED NOT NULL,
  `age_band_id` int(10) UNSIGNED NOT NULL,
  `pos` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `neg` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `county_entrypoint`
--

CREATE TABLE `county_entrypoint` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `county` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `entrypoint` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `county_iprophylaxis`
--

CREATE TABLE `county_iprophylaxis` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `county` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `prophylaxis` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `county_mprophylaxis`
--

CREATE TABLE `county_mprophylaxis` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `county` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `prophylaxis` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `county_rejections`
--

CREATE TABLE `county_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `county` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `county_summary`
--

CREATE TABLE `county_summary` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `county` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `avgage` int(10) DEFAULT NULL,
  `medage` int(10) DEFAULT NULL,
  `received` int(10) DEFAULT NULL,
  `alltests` int(10) DEFAULT NULL COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `firstdna` int(10) DEFAULT NULL,
  `confirmdna` int(10) DEFAULT NULL,
  `repeatspos` int(10) DEFAULT NULL,
  `repeatposPOS` int(10) DEFAULT NULL,
  `confirmedPOS` int(10) DEFAULT NULL,
  `actualinfants` int(10) DEFAULT NULL,
  `actualinfantsPOS` int(10) DEFAULT NULL,
  `infantsless2m` int(10) DEFAULT NULL,
  `infantsless2mPOS` int(10) DEFAULT NULL,
  `infantsless2mNEG` int(10) DEFAULT NULL,
  `infantsless2mREJ` int(10) DEFAULT NULL,
  `infantsless2w` int(10) DEFAULT NULL,
  `infantsless2wPOS` int(10) DEFAULT NULL,
  `infants4to6w` int(10) DEFAULT NULL,
  `infants4to6wPOS` int(10) DEFAULT NULL,
  `infantsabove2m` int(10) DEFAULT NULL,
  `infantsabove2mPOS` int(10) DEFAULT NULL,
  `adults` int(10) DEFAULT NULL,
  `adultsPOS` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `rpos` int(10) DEFAULT '0',
  `rneg` int(10) DEFAULT '0',
  `allpos` int(10) DEFAULT '0',
  `allneg` int(10) DEFAULT '0',
  `redraw` int(10) DEFAULT NULL,
  `rejected` int(10) DEFAULT NULL,
  `validation_confirmedpos` int(10) DEFAULT NULL,
  `validation_repeattest` int(10) DEFAULT NULL,
  `validation_viralload` int(10) DEFAULT NULL,
  `validation_adult` int(10) DEFAULT NULL,
  `validation_unknownsite` int(10) DEFAULT NULL,
  `enrolled` int(10) DEFAULT NULL,
  `dead` int(10) DEFAULT NULL,
  `ltfu` int(10) DEFAULT NULL,
  `adult` int(10) DEFAULT NULL,
  `transout` int(10) DEFAULT NULL,
  `other` int(10) DEFAULT NULL,
  `sitessending` int(10) DEFAULT NULL,
  `tat1` int(10) DEFAULT NULL,
  `tat2` int(10) DEFAULT NULL,
  `tat3` int(10) DEFAULT NULL,
  `tat4` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `county_summary_yearly`
--

CREATE TABLE `county_summary_yearly` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `county` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `avgage` int(10) DEFAULT NULL,
  `medage` int(10) DEFAULT NULL,
  `received` int(10) DEFAULT NULL,
  `alltests` int(10) DEFAULT NULL COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `firstdna` int(10) DEFAULT NULL,
  `confirmdna` int(10) DEFAULT NULL,
  `repeatspos` int(10) DEFAULT NULL,
  `repeatposPOS` int(10) DEFAULT NULL,
  `confirmedPOS` int(10) DEFAULT NULL,
  `actualinfants` int(10) DEFAULT NULL,
  `actualinfantsPOS` int(10) DEFAULT NULL,
  `infantsless2m` int(10) DEFAULT NULL,
  `infantsless2mPOS` int(10) DEFAULT NULL,
  `infantsless2mNEG` int(10) DEFAULT NULL,
  `infantsless2mREJ` int(10) DEFAULT NULL,
  `infantsless2w` int(10) DEFAULT NULL,
  `infantsless2wPOS` int(10) DEFAULT NULL,
  `infants4to6w` int(10) DEFAULT NULL,
  `infants4to6wPOS` int(10) DEFAULT NULL,
  `infantsabove2m` int(10) DEFAULT NULL,
  `infantsabove2mPOS` int(10) DEFAULT NULL,
  `adults` int(10) DEFAULT NULL,
  `adultsPOS` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `rpos` int(10) DEFAULT '0',
  `rneg` int(10) DEFAULT '0',
  `allpos` int(10) DEFAULT '0',
  `allneg` int(10) DEFAULT '0',
  `redraw` int(10) DEFAULT NULL,
  `rejected` int(10) DEFAULT NULL,
  `validation_confirmedpos` int(10) DEFAULT NULL,
  `validation_repeattest` int(10) DEFAULT NULL,
  `validation_viralload` int(10) DEFAULT NULL,
  `validation_adult` int(10) DEFAULT NULL,
  `validation_unknownsite` int(10) DEFAULT NULL,
  `enrolled` int(10) DEFAULT NULL,
  `dead` int(10) DEFAULT NULL,
  `ltfu` int(10) DEFAULT NULL,
  `adult` int(10) DEFAULT NULL,
  `transout` int(10) DEFAULT NULL,
  `other` int(10) DEFAULT NULL,
  `sitessending` int(10) DEFAULT NULL,
  `tat1` int(10) DEFAULT NULL,
  `tat2` int(10) DEFAULT NULL,
  `tat3` int(10) DEFAULT NULL,
  `tat4` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `districts`
--

CREATE TABLE `districts` (
  `ID` int(14) NOT NULL,
  `name` varchar(100) NOT NULL,
  `SubCountyDHISCode` varchar(50) DEFAULT NULL,
  `SubCountyMFLCode` varchar(50) DEFAULT NULL,
  `SubCountyCoordinates` varchar(3070) DEFAULT NULL,
  `county` int(32) NOT NULL,
  `province` int(14) NOT NULL,
  `comment` varchar(32) DEFAULT NULL,
  `flag` int(32) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `districts`
--

INSERT INTO `districts` (`ID`, `name`, `SubCountyDHISCode`, `SubCountyMFLCode`, `SubCountyCoordinates`, `county`, `province`, `comment`, `flag`) VALUES
(1, 'DS ADZOPE ', '1', '1', NULL, 1, 0, NULL, 1),
(2, 'DS AGBOVILLE ', '2', '2', NULL, 1, 0, NULL, 1),
(3, 'DS AKOUPE', '3', '3', NULL, 1, 0, NULL, 1),
(4, 'DS ALEPE', '4', '4', NULL, 1, 0, NULL, 1),
(5, 'DS SIKENSI', '5', '5', NULL, 1, 0, NULL, 1),
(6, 'DS TIASSALE ', '6', '6', NULL, 1, 0, NULL, 1),
(7, 'DS SAN PEDRO', '7', '7', NULL, 2, 0, NULL, 1),
(8, 'DS TABOU', '8', '8', NULL, 2, 0, NULL, 1),
(9, 'DS SOUBRE', '9', '9', NULL, 2, 0, NULL, 1),
(10, 'DS SASSANDRA ', '10', '10', NULL, 2, 0, NULL, 1),
(11, 'DS GUEYO', '11', '11', NULL, 2, 0, NULL, 1),
(12, 'DS ODIENNE ', '12', '12', NULL, 3, 0, NULL, 1),
(13, 'DS TOUBA', '13', '13', NULL, 3, 0, NULL, 1),
(14, 'DS DALOA ', '14', '14', NULL, 4, 0, NULL, 1),
(15, 'DS VAVOUA ', '15', '15', NULL, 4, 0, NULL, 1),
(16, 'DS ISSIA', '16', '16', NULL, 4, 0, NULL, 1),
(17, 'DS GAGNOA', '17', '17', NULL, 5, 0, NULL, 1),
(18, 'DS OUME', '18', '18', NULL, 5, 0, NULL, 1),
(19, 'DS YAMOUSSOUKRO', '19', '19', NULL, 6, 0, NULL, 1),
(20, 'DS TIEBISSOU', '20', '20', NULL, 6, 0, NULL, 1),
(21, 'DS TOUMODI', '21', '21', NULL, 6, 0, NULL, 1),
(22, 'DS DIDIEVI', '22', '22', NULL, 6, 0, NULL, 1),
(23, 'DS ADJAME-PLATEAU-ATTECOUBE', '23', '23', NULL, 7, 0, NULL, 1),
(24, 'DS YOPOUGON-EST', '24', '24', NULL, 7, 0, NULL, 1),
(25, 'DS YOPOUGON-OUEST-SONGNON', '25', '25', NULL, 7, 0, NULL, 1),
(26, 'DS DABOU', '26', '26', NULL, 7, 0, NULL, 1),
(27, 'DS JACQUEVILLE', '27', '27', NULL, 7, 0, NULL, 1),
(28, 'DS GRAND-LAHOU', '28', '28', NULL, 7, 0, NULL, 1),
(29, 'DS TREICHVILLE-MARCORY', '29', '29', NULL, 8, 0, NULL, 1),
(30, 'DS KOUMASSI-PORT BOUET-VRIDI ', '30', '30', NULL, 8, 0, NULL, 1),
(31, 'DS COCODY-BINGERVILLE ', '31', '31', NULL, 8, 0, NULL, 1),
(32, 'DS ABOBO-EST', '32', '32', NULL, 8, 0, NULL, 1),
(33, 'DS ABOBO-OUEST ', '33', '33', NULL, 8, 0, NULL, 1),
(34, 'DS ANYAMA', '34', '34', NULL, 8, 0, NULL, 1),
(35, 'DS BOUAFLE ', '35', '35', NULL, 9, 0, NULL, 1),
(36, 'DS SINFRA ', '36', '36', NULL, 9, 0, NULL, 1),
(37, 'DS ZUENOULA', '37', '37', NULL, 9, 0, NULL, 1),
(38, 'DS MAN', '38', '38', NULL, 10, 0, NULL, 1),
(39, 'DS BIANKOUMAN', '39', '39', NULL, 10, 0, NULL, 1),
(40, 'DS DANANE', '40', '40', NULL, 10, 0, NULL, 1),
(41, 'DS ZOUAN-HOUNIEN', '41', '41', NULL, 10, 0, NULL, 1),
(42, 'DS GUIGLO', '42', '42', NULL, 11, 0, NULL, 1),
(43, 'DS TOULEPLEU', '43', '43', NULL, 11, 0, NULL, 1),
(44, 'DS DUEKOUE', '44', '44', NULL, 11, 0, NULL, 1),
(45, 'DS BLOLEQUIN', '45', '45', NULL, 11, 0, NULL, 1),
(46, 'DS BANGOLO', '46', '46', NULL, 11, 0, NULL, 1),
(47, 'DS KOUIBLY', '47', '47', NULL, 11, 0, NULL, 1),
(48, 'DS DIMBOKRO', '48', '48', NULL, 12, 0, NULL, 1),
(49, 'DS BOCANDA ', '49', '49', NULL, 12, 0, NULL, 1),
(50, 'DS BONGOUANOU', '50', '50', NULL, 12, 0, NULL, 1),
(51, 'DS DAOUKRO', '51', '51', NULL, 12, 0, NULL, 1),
(52, 'DS M\'BAHIAKRO', '52', '52', NULL, 12, 0, NULL, 1),
(53, 'DS PRIKRO', '53', '53', NULL, 12, 0, NULL, 1),
(54, 'DS ABENGOUROU', '54', '54', NULL, 13, 0, NULL, 1),
(55, 'DS AGNIBILEKRO', '55', '55', NULL, 13, 0, NULL, 1),
(56, 'DS BETTIE', '56', '56', NULL, 13, 0, NULL, 1),
(57, 'DS KORHOGO', '57', '57', NULL, 14, 0, NULL, 1),
(58, 'DS BOUNDIALI', '58', '58', NULL, 14, 0, NULL, 1),
(59, 'DS FERKESSEDOUGOU', '59', '59', NULL, 14, 0, NULL, 1),
(60, 'DS TENGRELA', '60', '60', NULL, 14, 0, NULL, 1),
(61, 'DS DIVO', '61', '61', NULL, 15, 0, NULL, 1),
(62, 'DS LAKOTA', '62', '62', NULL, 15, 0, NULL, 1),
(63, 'DS ABOISSO', '63', '63', NULL, 16, 0, NULL, 1),
(64, 'DS ADIAKE ', '64', '64', NULL, 16, 0, NULL, 1),
(65, 'DS GRAND-BASSAM', '65', '65', NULL, 16, 0, NULL, 1),
(66, 'DS BOUAKE NORD-OUEST', '66', '66', NULL, 17, 0, NULL, 1),
(67, 'DS BOUAKE NORD-EST', '67', '67', NULL, 17, 0, NULL, 1),
(68, 'DS BOUAKE SUD', '68', '68', NULL, 17, 0, NULL, 1),
(69, 'DS SAKASSOU', '69', '69', NULL, 17, 0, NULL, 1),
(70, 'DS BEOUMI', '70', '70', NULL, 17, 0, NULL, 1),
(71, 'DS DABAKALA', '71', '71', NULL, 18, 0, NULL, 1),
(72, 'DS KATIOLA', '72', '72', NULL, 18, 0, NULL, 1),
(73, 'DS NIAKARAMADOUGOU', '73', '73', NULL, 18, 0, NULL, 1),
(74, 'DS SEGUELA', '74', '74', NULL, 19, 0, NULL, 1),
(75, 'DS MANKONO', '75', '75', NULL, 19, 0, NULL, 1),
(76, 'DS BONDOUKOU', '76', '76', '', 20, 0, NULL, 1),
(77, 'DS BOUNA', '77', '77', NULL, 20, 0, NULL, 1),
(78, 'DS TANDA', '78', '78', NULL, 20, 0, NULL, 1),
(79, 'DS NASSIAN', '79', '79', NULL, 20, 0, NULL, 1),
(80, 'DS FRESCO', '80', '80', NULL, 15, 0, NULL, 1),
(81, 'DS OUANGOLODOUGOU', '81', '81', NULL, 14, 0, NULL, 1),
(82, 'DS MINIGNAN', '82', '82', NULL, 3, 0, NULL, 1),
(83, 'DS GUITRY\r\n', '83', '83', NULL, 15, 0, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `entry_points`
--

CREATE TABLE `entry_points` (
  `ID` int(14) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `facilitys`
--

CREATE TABLE `facilitys` (
  `ID` smallint(5) UNSIGNED NOT NULL,
  `facilitycode` int(10) NOT NULL DEFAULT '0' COMMENT 'Facility Name',
  `district` int(30) DEFAULT '0' COMMENT 'Facility District Name',
  `name` varchar(100) NOT NULL COMMENT 'Facility Name',
  `lab` int(14) NOT NULL,
  `partner` int(14) NOT NULL DEFAULT '0',
  `ftype` varchar(200) DEFAULT NULL,
  `DHIScode` varchar(50) NOT NULL DEFAULT '0' COMMENT 'Facility Name',
  `districtname` varchar(30) DEFAULT NULL COMMENT 'Facility District Name',
  `longitude` varchar(200) DEFAULT NULL,
  `latitude` varchar(200) DEFAULT NULL,
  `burden` varchar(200) DEFAULT NULL,
  `artpatients` int(200) DEFAULT NULL,
  `pmtctnos` int(200) DEFAULT NULL,
  `Mless15` int(200) DEFAULT NULL,
  `Mmore15` int(200) DEFAULT NULL,
  `Fless15` int(200) DEFAULT NULL,
  `Fmore15` int(200) DEFAULT NULL,
  `totalartmar` int(200) DEFAULT NULL,
  `totalartsep15` int(200) DEFAULT NULL,
  `asofdate` date DEFAULT NULL,
  `partnerold` int(14) DEFAULT '0' COMMENT 'before Aug 2016 Update',
  `partner2` int(14) DEFAULT '0',
  `telephone` varchar(20) DEFAULT NULL COMMENT 'Facility Telephone 1',
  `telephone2` varchar(20) DEFAULT NULL COMMENT 'Facility Telephone 2',
  `fax` varchar(30) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL COMMENT 'Facility email Address',
  `PostalAddress` varchar(40) DEFAULT NULL COMMENT 'Facility Contact Address',
  `contactperson` varchar(30) DEFAULT NULL COMMENT 'Facility Contact Name',
  `contacttelephone` varchar(20) DEFAULT NULL COMMENT 'Contact Person''s Telephone 1',
  `contacttelephone2` varchar(20) DEFAULT NULL COMMENT 'Contact Person''s Telephone 2',
  `physicaladdress` varchar(40) DEFAULT NULL COMMENT 'Facility Physical Address',
  `ContactEmail` varchar(40) DEFAULT NULL COMMENT 'Facility Physical Address',
  `subcountyemail` varchar(40) DEFAULT NULL COMMENT 'Facility Physical Address',
  `countyemail` varchar(40) DEFAULT NULL COMMENT 'Facility Physical Address',
  `partneremail` varchar(40) DEFAULT NULL COMMENT 'Facility Physical Address',
  `Flag` int(11) NOT NULL DEFAULT '1',
  `partnerregion` int(14) DEFAULT NULL,
  `pasco` int(14) DEFAULT NULL,
  `ART` varchar(5) DEFAULT NULL,
  `PMTCT` varchar(5) DEFAULT NULL,
  `originalID` int(14) DEFAULT NULL,
  `Service_Provider` varchar(25) DEFAULT NULL,
  `SMS_printer_phoneNo` varchar(25) DEFAULT NULL,
  `smssecondarycontact` varchar(100) DEFAULT NULL,
  `smsprimarycontact` varchar(100) DEFAULT NULL,
  `smscontactperson` varchar(100) DEFAULT NULL,
  `smsprinter` int(14) DEFAULT '0',
  `G4Sbranchname` varchar(100) DEFAULT NULL,
  `G4Slocation` varchar(100) DEFAULT NULL,
  `G4Sphone1` varchar(100) DEFAULT NULL,
  `G4Sphone2` varchar(100) DEFAULT NULL,
  `G4Sphone3` varchar(100) DEFAULT NULL,
  `G4Sfax` varchar(100) DEFAULT NULL,
  `datemodified` date DEFAULT NULL,
  `active` int(11) DEFAULT '0',
  `negpilot` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `facilitys`
--

INSERT INTO `facilitys` (`ID`, `facilitycode`, `district`, `name`, `lab`, `partner`, `ftype`, `DHIScode`, `districtname`, `longitude`, `latitude`, `burden`, `artpatients`, `pmtctnos`, `Mless15`, `Mmore15`, `Fless15`, `Fmore15`, `totalartmar`, `totalartsep15`, `asofdate`, `partnerold`, `partner2`, `telephone`, `telephone2`, `fax`, `email`, `PostalAddress`, `contactperson`, `contacttelephone`, `contacttelephone2`, `physicaladdress`, `ContactEmail`, `subcountyemail`, `countyemail`, `partneremail`, `Flag`, `partnerregion`, `pasco`, `ART`, `PMTCT`, `originalID`, `Service_Provider`, `SMS_printer_phoneNo`, `smssecondarycontact`, `smsprimarycontact`, `smscontactperson`, `smsprinter`, `G4Sbranchname`, `G4Slocation`, `G4Sphone1`, `G4Sphone2`, `G4Sphone3`, `G4Sfax`, `datemodified`, `active`, `negpilot`) VALUES
(1, 1068, 23, 'CAT de Adjame\r\n', 49, 0, NULL, 'CA', 'DS ADJAME-PLATEAU-ATTECOUBE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(2, 1073, 23, 'FSU de Attecoube\r\n', 29, 0, NULL, '1', 'DS ADJAME-PLATEAU-ATTECOUBE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(3, 2131, 23, 'Centre de Sante Urbain de Williamsville\r\n', 0, 0, NULL, '53', 'DS ADJAME-PLATEAU-ATTECOUBE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(4, 1069, 0, 'CMM-GSPM Indenie\r\n', 0, 0, NULL, '556', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(5, 1077, 23, 'HMA', 54, 0, NULL, '494', 'DS ADJAME-PLATEAU-ATTECOUBE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(6, 2094, 23, 'INSP\r\n', 44, 0, NULL, '54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(7, 1383, 26, '\r\nHG de Dabou\r\n', 3, 0, NULL, '120', 'DS DABOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(8, 1384, 26, 'Hopital Methodiste de Dabou\r\n', 4, 0, NULL, '120', 'DS DABOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(9, 1387, 26, 'Centre de Sante SAPH de Toupah\r\n', 5, 0, NULL, '120', 'DS DABOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(10, 1507, 28, 'HG de Grand Lahou\r\n', 50, 0, NULL, '47', 'DS GRAND-LAHOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(11, 1554, 27, 'HG de Jacqueville\r\n', 51, 0, NULL, '48', 'DS JACQUEVILLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(12, 1936, 24, 'Centre Nazareen\r\n', 12, 0, NULL, 'YZ', 'DS YOPOUGON-EST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(13, 1937, 24, 'Centre Plus yopougon\r\n\r\n', 7, 0, NULL, '298', 'DS YOPOUGON-EST', '\r\n', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(14, 1940, 24, 'CSU Com de Andokoi\r\n', 0, 0, NULL, '', 'DS YOPOUGON-EST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(15, 1941, 24, 'FSU Com de Koute\r\n', 10, 0, NULL, '', 'DS YOPOUGON-EST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(16, 1919, 25, 'CEPREF)\r\n', 2, 0, NULL, '16', 'DS YOPOUGON-OUEST-SONGON', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(17, 1329, 25, 'CHU Yopougon\r\n', 1, 0, NULL, '3', 'DS YOPOUGON-OUEST-SONGON', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(18, 1927, 25, 'Formation Sanitaire Urbaine Communautaire de Niangon Sud (AGEFOSYN)\r\n', 8, 0, NULL, '0', 'DS YOPOUGON-OUEST-SONGON', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(19, 1933, 25, 'Formation Sanitaire Urbaine Communautaire de Port Bouet II\r\n', 6, 0, NULL, '108', 'DS YOPOUGON-OUEST-SONGNON', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(20, 1042, 32, 'Formation Sanitaire Urbaine Communautaire de Abobo-Avocatier\r\n', 161, 0, NULL, '', 'DS ABOBO-EST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(21, 1035, 32, 'Centre AntiTuberculeux de Abobo\r\n', 155, 0, NULL, '0', 'DS ABOBO-EST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(22, 1037, 32, 'Centre d\'Education Specialise de Abobote\r\n', 0, 0, NULL, '0', 'DS ABOBO-EST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(23, 1047, 32, 'Centre Medical SOS Village Abobo\r\n', 0, 0, NULL, '0', 'DS ABOBO-EST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(24, 1049, 32, 'Centre de Sante Urbain Communautaire de Abobo-BC\r\n', 138, 0, NULL, '0', 'DS ABOBO-EST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(25, 1051, 32, 'Formation Sanitaire Urbaine Communautaire de Abobo-Baoule\r\n', 164, 0, NULL, '0', 'DS ABOBO-EST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(26, 1044, 33, 'Hopital General de Abobo-Nord\r\n', 141, 0, NULL, '', 'DS ABOBO-OUEST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(27, 1055, 33, 'Centre de Sante El Rapha\r\n', 117, 0, NULL, '0', 'DS ABOBO-OUEST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(28, 1062, 33, 'Formation Sanitaire Urbaine Communautaire de Anonkoua-Koute\r\n', 10, 0, NULL, '0', 'DS ABOBO-OUEST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(29, 1063, 33, 'Hopital General de Abobo-Sud\r\n', 118, 0, NULL, '0', 'DS ABOBO-OUEST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(30, 1163, 34, 'Hopital General de Anyama\r\n', 115, 0, NULL, '0', 'DS ANYAMA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(31, 2458, 31, 'CMM du C. d\'Akouedo\r\n', 0, 0, NULL, '0', 'DS COCODY-BINGERVILLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(32, 1351, 31, 'Centre de Sante Urbain Communautaire de Nimatoulaye\n', 0, 0, NULL, 'DS COCODY-BINGERVILLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(33, 2086, 31, 'Service Laboratoire du Centre Hospitalier Universitaire de Cocody\r\n', 144, 0, NULL, 'DS COCODY-BINGERVILLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(35, 1357, 31, 'Hopital General de Bingerville\r\n', 142, 0, NULL, '0', 'DS COCODY-BINGERVILLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(36, 1358, 31, 'Institut Pasteur de Cote d\'Ivoire\r\n', 44, 0, NULL, '0', 'DS COCODY-BINGERVILLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(37, 1359, 31, 'Formation Sanitaire Urbaine de Cocody/Protection Maternelle Infantile  Cocody\r\n', 0, 0, NULL, '0', 'DS COCODY-BINGERVILLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(38, 1595, 30, 'Centre AntiTuberculeux de Koumassi\r\n', 119, 0, NULL, '0', 'DS KOUMASSI-PORT BOUET-VRIDI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(39, 1609, 30, 'Hopital General de Koumassi\r\n', 120, 0, NULL, '0', 'DS KOUMASSI-PORT BOUET-VRIDI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(40, 1610, 30, 'Hopital General de Port Bouet\r\n', 111, 0, NULL, '0', 'DS KOUMASSI-PORT BOUET-VRIDI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(41, 1448, 61, 'Hopital General de Guitry\r\n', 158, 0, NULL, '0', 'DS DIVO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(42, 1870, 29, 'Centre Integre de Recherches Biocliniques d\'\r\nAbidjan (CIRBA)\r\n', 0, 0, NULL, '0', 'DS TREICHVILLE-MARCORY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(43, 1868, 29, 'Centre de Sante Espace Confiance\r\n', 0, 0, NULL, '0', 'DS TREICHVILLE-MARCORY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(44, 1874, 29, 'Hopital General de Marcory\r\n', 47, 0, NULL, '0', 'DS TREICHVILLE-MARCORY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(45, 1875, 29, 'Hopital General de Treichville\r\n', 31, 0, NULL, '0', 'DS TREICHVILLE-MARCORY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(46, 1876, 29, 'Hope Worldwide Cote d\'Ivoire de Treichville\r\n', 56, 0, NULL, 'DS TREICHVILLE-MARCORY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(47, 1872, 29, 'Dispensaire Anti Venerien INHP\r\n', 45, 0, NULL, '0', 'DS TREICHVILLE-MARCORY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(48, 1877, 29, 'KO\'KHOUA Centre National de Transfusion Sanguine\r\n', 0, 0, NULL, '0', 'DS TREICHVILLE-MARCORY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(49, 1869, 29, 'Centre Medical La Pierre Angulaire\r\n', 0, 0, NULL, '0', 'DS TREICHVILLE-MARCORY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(50, 1101, 1, 'Hopital General de Adzope\r\n', 147, 0, NULL, '0', 'DS ADZOPE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(51, 1102, 1, 'Institut Raoul Follerau\r\n', 0, 0, NULL, '0', 'DS ADZOPE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(52, 1105, 2, 'Centre Hospitalier Regional de Agboville\r\n', 148, 0, NULL, '0', 'DS AGBOVILLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(53, 1154, 3, 'Hopital General de Akoupe\r\n', 149, 0, NULL, '0', 'DS AKOUPE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(54, 1155, 3, 'Hopital General de Yakasse-Attobrou\r\n', 166, 0, NULL, '0', 'DS AKOUPE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(55, 1162, 4, 'Hopital General de Alepe\r\n', 153, 0, NULL, '0', 'DS ALEPE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(56, 1758, 5, 'Hopital General de Sikensi\r\n', 150, 0, NULL, '0', 'DS SIKENSI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(57, 1832, 6, 'Hopital General de Taabo\r\n', 152, 0, NULL, '0', 'DS TIASSALE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(58, 1833, 6, 'Hopital General de Tiassale\r\n', 151, 0, NULL, '0', 'DS TIASSALE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(59, 1857, 20, 'Hopital General de Tiebissou\r\n', 125, 0, NULL, '0', 'DS TIEBISSOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(60, 1865, 21, 'Hopital General de Djekanou\r\n', 124, 0, NULL, '0', 'DS TOUMODI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(61, 1866, 21, 'Hopital General de Toumodi\r\n', 123, 0, NULL, '0', 'DS TOUMODI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(62, 1899, 19, 'Centre Hospitalier Regional de Yamoussoukro\r\n', 121, 0, NULL, '0', 'DS YAMOUSSOUKRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(63, 1898, 19, 'Centre Medico-Social Walle\r\n', 122, 0, NULL, '0', 'DS YAMOUSSOUKRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(64, 2267, 19, 'Renaissance SanÃ© BouakÃ© de Yamoussoukro\r\n', 137, 0, NULL, '0', 'DS YAMOUSSOUKRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(65, 1196, 76, 'Centre AntiTuberculeux de Bondoukou\r\n', 84, 0, NULL, '0', 'DS BONDOUKOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(66, 1197, 76, 'Centre Hospitalier Regional de Bondoukou\r\n', 83, 0, NULL, '0', 'DS BONDOUKOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(67, 1313, 77, 'Hopital General de Bouna\r\n', 82, 0, NULL, '0', 'DS BOUNA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(68, 1649, 79, 'Hopital General de Nassian\r\n', 78, 0, NULL, '0', 'DS NASSIAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(69, 1823, 78, 'Hopital General de Tanda\r\n', 80, 0, NULL, '0', 'DS TANDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(70, 1822, 78, 'Hopital General de Kounfao\r\n', 0, 0, NULL, '0', 'DS TANDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(71, 1167, 46, 'Hopital General de Bangolo\r\n', 22, 0, NULL, '0', 'DS BANGOLO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(72, 1188, 45, 'Hopital General de Blolequin\r\n', 21, 0, NULL, '0', 'DS BLOLEQUIN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(73, 1453, 44, 'Hopital General de Duekoue\r\n', 20, 0, NULL, '0', 'DS DUEKOUE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(74, 1516, 42, 'Centre Hospitalier Regional de Guiglo\r\n', 23, 0, NULL, '0', 'DS GUIGLO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(75, 1519, 42, 'Hopital General de Tai\r\n', 25, 0, NULL, '0', 'DS GUIGLO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(76, 1594, 47, 'Hopital General de Kouibly\r\n', 26, 0, NULL, '0', 'DS KOUIBLY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(77, 1861, 43, 'Hopital General de Toulepleu\r\n', 24, 0, NULL, '0', 'DS TOULEPLEU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(78, 1178, 70, 'Hopital General de Beoumi\r\n', 61, 0, NULL, '0', 'DS BEOUMI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(79, 1279, 66, 'Centre AntiTuberculeux de Bouake\r\n', 57, 0, NULL, '0', 'DS BOUAKE-NORD-OUEST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(80, 1280, 66, 'Centre Hospitalier Universitaire de Bouake\r\n', 58, 0, NULL, '0', 'DS BOUAKE-NORD-OUEST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(81, 1281, 66, 'Centre Solidarite Action Sociale de Bouake\r\n', 59, 0, NULL, '0', 'DS BOUAKE-NORD-OUEST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(82, 1299, 68, 'Hopital Saint Camille\r\n', 0, 0, NULL, '0', 'DS BOUAKE-SUD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(83, 1692, 69, 'Hopital General de Sakassou', 62, 0, NULL, '0', 'DS SAKASSOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(84, 1513, 11, 'Hopital General de Gueyo\r\n', 0, 0, NULL, '0', 'DS GUEYO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(85, 1695, 7, 'Centre Hospitalier Regional de San-Pedro\r\n', 32, 0, NULL, '0', 'DS SAN PEDRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(86, 1705, 7, 'Centre de Sante Urbain de Grand Bereby\r\n', 0, 0, NULL, '0', 'DS SAN PEDRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(87, 1721, 7, 'Societe Africaine de Plantation d\'Hevea (SAPH) de San Pedro', 0, 0, NULL, '0', 'DS SAN PEDRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(88, 1716, 7, 'Maternite municipale Henriette Konan Bedie de Bardot\r\n', 0, 0, NULL, '0', 'DS SAN PEDRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(89, 1740, 10, 'Hopital General de Sassandra\r\n', 33, 0, NULL, '0', 'DS SASSANDRA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(90, 1783, 9, 'Centre de Sante Urbain de Meagui\r\n', 42, 0, NULL, '0', 'DS SOUBRE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(91, 1790, 9, 'Hopital General de Buyo\r\n', 38, 0, NULL, '0', 'DS SOUBRE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(92, 1791, 9, 'Hopital General de Soubre\r\n', 35, 0, NULL, '0', 'DS SOUBRE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(93, 1799, 8, 'Centre de Sante Urbain de Grabo\r\n', 41, 0, NULL, '0', 'DS TABOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(94, 1806, 8, 'Hopital General de Tabou\r\n', 37, 0, NULL, '0', 'DS TABOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(95, 1469, 17, 'Centre AntiTuberculeux de Gagnoa\r\n', 106, 0, NULL, '0', 'DS GAGNOA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(96, 1470, 17, 'Centre Hospitalier Regional de Gagnoa\r\n', 107, 0, NULL, '0', 'DS GAGNOA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(97, 1484, 17, 'Centre de Sante Urbain de Guiberoua\r\n', 108, 0, NULL, '0', 'DS GAGNOA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(98, 1488, 17, 'Hopital General de Gagnoa\r\n', 105, 0, NULL, '0', 'DS GAGNOA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(99, 1676, 18, 'Centre de Sante Urbain de Diegonefla\r\n', 110, 0, NULL, '0', 'DS OUME', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(100, 1680, 18, 'Hopital General de Oume\r\n', 109, 0, NULL, '0', 'DS OUME', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(101, 1371, 71, 'Hopital General de Dabakala\r\n', 64, 0, NULL, '0', 'DS DABAKALA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(102, 1555, 72, 'Centre Hospitalier Regional de Katiola\r\n', 63, 0, NULL, '0', 'DS KATIOLA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(103, 1653, 73, 'Hopital General de Niakaramadougou\r\n', 65, 0, NULL, '0', 'DS NIAKARAMADOUGOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(104, 1389, 14, 'Centre AntiTuberculeux de Daloa\r\n', 89, 0, NULL, '0', 'DS DALOA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(105, 1391, 14, 'Centre Hospitalier Regional de Daloa\r\n', 88, 0, NULL, '0', 'DS DALOA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(106, 1388, 14, 'AIBEF Daloa\r\n', 90, 0, NULL, '0', 'DS DALOA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(107, 1408, 14, 'Hopital General de Zoukougbeu\r\n', 91, 0, NULL, '0', 'DS DALOA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(108, 1390, 14, 'CMM Daloa\r\n', 0, 0, NULL, '0', 'DS DALOA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(109, 1536, 16, 'Centre de Sante Urbain de Saioua\r\n', 87, 0, NULL, '0', 'DS ISSIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(110, 1540, 16, 'Hopital General de Issia\r\n', 86, 0, NULL, '0', 'DS ISSIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(111, 1896, 15, 'Hopital General de Vavoua\r\n', 85, 0, NULL, '0', 'DS VAVOUA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(112, 1004, 54, 'Centre Medical PIM\r\n\r\n', 0, 0, NULL, '0', 'DS ABENGOUROU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(113, 1005, 54, 'Centre Hospitalier Regional de Abengourou\r\n', 112, 0, NULL, '0', 'DS ABENGOUROU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(115, 1128, 55, 'Hopital General de Agnibilekrou\r\n', 113, 0, NULL, '0', 'DS AGNIBILEKROU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(116, 1181, 56, 'Centre de Sante Urbain de Bettie/ Hopital General de Bettie\n', 114, 0, NULL, '0', 'DS BETTIE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(117, 1655, 12, 'Centre Hospitalier Regional de Odienne\r\n', 17, 0, NULL, '0', 'DS ODIENNE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(118, 1656, 12, 'Centre de Sante Pietro Bonilli\r\n', 0, 0, NULL, '0', 'DS ODIENNE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(119, 1660, 12, 'Hopital General de Madinani\r\n', 19, 0, NULL, '0', 'DS ODIENNE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(120, 1858, 8, 'Centre Hospitalier Regional de Touba\r\n', 16, 0, NULL, '0', 'DS TOUBA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(121, 1436, 61, 'Centre AntiTuberculeux de Divo\r\n', 156, 0, NULL, '0', 'DS DIVO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(122, 1437, 61, 'Centre Hospitalier Regional de Divo\r\n', 157, 0, NULL, '0', 'DS DIVO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(123, 1445, 61, 'Centre de Sante Urbain de Hire\r\n', 160, 0, NULL, '0', 'DS DIVO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(124, 1446, 61, 'Centre de Sante Urbain de Yocoboue\r\n', 154, 0, NULL, '0', 'DS DIVO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(125, 1463, 0, 'Centre Medico-Social Franzisca de Gbagbam\r\n', 0, 0, NULL, '0', 'DS FRESCO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(126, 1467, 0, 'Hopital General de Fresco\r\n', 146, 0, NULL, '0', 'DS FRESCO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(127, 1619, 62, 'Hopital General de Lakota\r\n', 159, 0, NULL, '0', 'DS LAKOTA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(128, 1250, 35, 'Centre Hospitalier Regional de Bouafle\r\n', 97, 0, NULL, '0', 'DS BOUAFLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(129, 1261, 35, 'Centre de Sante Urbain de Bonon\r\n', 98, 0, NULL, '0', 'DS BOUAFLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(130, 1765, 36, 'Centre de Sante Urbain de Kononfla\r\n', 104, 0, NULL, '0', 'DS SINFRA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(131, 1766, 36, 'Dispensaire Urbain Mission Christ Roi de Sinfra\r\n', 0, 0, NULL, '0', 'DS SINFRA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(132, 1767, 36, 'Hopital General de Sinfra\r\n', 102, 0, NULL, '0', 'DS SINFRA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(133, 1958, 37, 'Dispensaire Medico-Social Sucrivoire\r\n', 0, 0, NULL, '0', 'DS ZUENOULA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(134, 1957, 37, 'Centre de Sante Urbain de Gohitafla\r\n', 101, 0, NULL, '0', 'DS ZUENOULA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(135, 1959, 37, 'Hopital General de Zuenoula\r\n', 99, 0, NULL, '0', 'DS ZUENOULA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(136, 1641, 52, 'Hopital General de M\'bahiakro\r\n', 0, 0, NULL, '0', 'DS M\'BAHIAKRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(137, 1195, 49, 'Hopital General de Bocanda\r\n', 0, 0, NULL, '0', 'DS BOCANDA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(138, 1246, 50, 'Hopital General de Bongouanou\r\n', 0, 0, NULL, '0', 'DS BONGOUANOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(139, 1245, 50, 'Hopital General de Arrah\r\n', 0, 0, NULL, '0', 'DS BONGOUANOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);
INSERT INTO `facilitys` (`ID`, `facilitycode`, `district`, `name`, `lab`, `partner`, `ftype`, `DHIScode`, `districtname`, `longitude`, `latitude`, `burden`, `artpatients`, `pmtctnos`, `Mless15`, `Mmore15`, `Fless15`, `Fmore15`, `totalartmar`, `totalartsep15`, `asofdate`, `partnerold`, `partner2`, `telephone`, `telephone2`, `fax`, `email`, `PostalAddress`, `contactperson`, `contacttelephone`, `contacttelephone2`, `physicaladdress`, `ContactEmail`, `subcountyemail`, `countyemail`, `partneremail`, `Flag`, `partnerregion`, `pasco`, `ART`, `PMTCT`, `originalID`, `Service_Provider`, `SMS_printer_phoneNo`, `smssecondarycontact`, `smsprimarycontact`, `smscontactperson`, `smsprinter`, `G4Sbranchname`, `G4Slocation`, `G4Sphone1`, `G4Sphone2`, `G4Sphone3`, `G4Sfax`, `datemodified`, `active`, `negpilot`) VALUES
(140, 1247, 50, 'Hopital General de M\'batto\r\n', 0, 0, NULL, '0', 'DS BONGOUANOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(141, 1422, 51, 'Centre de Sante Urbain de Ouelle\r\n', 0, 0, NULL, '0', 'DS DAOUKRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(142, 1423, 51, 'Hopital General de Daoukro\r\n', 0, 0, NULL, '0', 'DS DAOUKRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(143, 1427, 22, 'Hopital General de Didievi\r\n', 132, 0, NULL, '0', 'DS DIDIEVI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(144, 1429, 48, 'Centre Hospitalier Regional de Dimbokro\r\n', 126, 0, NULL, '0', 'DS DIMBOKRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(145, 1424, 53, 'Hopital General de Prikro\r\n', 131, 0, NULL, '0', 'DS PRIKRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(146, 1326, 58, 'Hopital General de Boundiali\r\n', 77, 0, NULL, '0', 'DS BOUNDIALI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(147, 1457, 59, 'Centre de Sante Urbain de Kong\r\n', 74, 0, NULL, '0', 'DS FERKESSEDOUGOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(148, 1459, 59, 'Hopital Baptiste de Ferkessedougou\r\n', 0, 0, NULL, '0', 'DS FERKESSEDOUGOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(149, 1460, 59, 'Hopital General de Ferkessedougou\r\n', 73, 0, NULL, '0', 'DS FERKESSEDOUDOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(150, 1560, 0, 'Centre AntiTuberculeux de Korhogo\r\n', 71, 0, NULL, '0', 'DS KORHOGO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(151, 1561, 0, 'Centre Hospitalier Regional de Korhogo\r\n', 66, 0, NULL, '0', 'DS KORHOGO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(152, 1573, 0, 'Centre de Sante Urbain de Dikodougou\r\n', 69, 0, NULL, '0', 'DS KORHOGO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(153, 0, 0, 'Centre de Sante Urbain de M\'bengue\r\n', 0, 0, NULL, '0', 'DS KORHOGO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(154, 1582, 0, 'Centre de Sante Urbain de Petit Paris\r\n', 0, 0, NULL, '0', 'DS KORHOGO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(155, 1586, 0, 'Dispensaire Rural de Torgokaha\r\n', 0, 0, NULL, '0', 'DS KORHOGO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(156, 1670, 0, 'Hopital General de Ouangolodougou\r\n', 75, 0, NULL, '0', 'DS OUANGOLODOUGOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(157, 1662, 0, 'Centre de Sante Urbain Cesaco Piansola de Ouangolodougou\r\n', 0, 0, NULL, '0', 'DS OUANGOLODOUGOU', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(158, 1830, 60, 'Hopital General de Tengrela\r\n', 79, 0, NULL, '0', 'DS TENGRELA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(159, 1187, 39, 'Hopital General de Biankouma\r\n', 13, 0, NULL, '0', 'DS BIANKOUMA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(160, 1415, 40, 'Hopital General de Danane\r\n', 14, 0, NULL, '0', 'DS DANANE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(161, 1621, 38, 'Centre AntiTuberculeux de Man\r\n', 9, 0, NULL, '0', 'DS MAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(162, 1622, 38, 'Centre Hospitalier Regional de Man\r\n', 27, 0, NULL, '0', 'DS MAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(163, 1624, 38, 'Centre de Sante Urbain de Libreville\r\n', 28, 0, NULL, '0', 'DS MAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(164, 1950, 41, 'Hopital General de Zouhan Hounien\r\n', 0, 0, NULL, '0', 'DS ZOUHAN-HOUNIEN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(165, 1627, 75, 'Centre de Sante Notre Dame Marandalah\r\n', 75, 0, NULL, '0', 'DS MANKONO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(166, 1634, 75, 'Centre de Sante Urbain de Dianra\r\n', 0, 0, NULL, '0', 'DS MANKONO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(167, 1639, 75, 'Hopital General de Mankono\r\n', 94, 0, NULL, '0', 'DS MANKONO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(168, 1750, 74, 'Hopital General de Kani\r\n', 93, 0, NULL, '0', 'DS SEGUELA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(169, 1743, 74, 'Centre Hospitalier Regional de Seguela\r\n', 0, 0, NULL, '0', 'DS SEGUELA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `feedings`
--

CREATE TABLE `feedings` (
  `ID` int(14) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `Flag` int(14) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `gender`
--

CREATE TABLE `gender` (
  `ID` int(10) NOT NULL,
  `name` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `hei_categories`
--

CREATE TABLE `hei_categories` (
  `ID` int(10) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `active` int(10) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `hei_validation`
--

CREATE TABLE `hei_validation` (
  `ID` int(14) NOT NULL,
  `name` varchar(30) NOT NULL,
  `desc` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ip_agebreakdown`
--

CREATE TABLE `ip_agebreakdown` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(14) DEFAULT NULL,
  `partner` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `sixweekspos` int(10) DEFAULT NULL,
  `sixweeksneg` int(10) DEFAULT NULL,
  `sevento3mpos` int(10) DEFAULT NULL,
  `sevento3mneg` int(10) DEFAULT NULL,
  `threemto9mpos` int(10) DEFAULT NULL,
  `threemto9mneg` int(10) DEFAULT NULL,
  `ninemto18mpos` int(10) DEFAULT NULL,
  `ninemto18mneg` int(10) DEFAULT NULL,
  `above18mpos` int(10) DEFAULT NULL,
  `above18mneg` int(10) DEFAULT NULL,
  `nodatapos` int(10) DEFAULT NULL,
  `nodataneg` int(10) DEFAULT NULL,
  `less2wpos` int(10) DEFAULT NULL,
  `less2wneg` int(10) DEFAULT NULL,
  `twoto6wpos` int(10) DEFAULT NULL,
  `twoto6wneg` int(10) DEFAULT NULL,
  `sixto8wpos` int(10) DEFAULT NULL,
  `sixto8wneg` int(10) DEFAULT NULL,
  `sixmonthpos` int(10) DEFAULT NULL,
  `sixmonthneg` int(10) DEFAULT NULL,
  `ninemonthpos` int(10) DEFAULT NULL,
  `ninemonthneg` int(10) DEFAULT NULL,
  `twelvemonthpos` int(10) DEFAULT NULL,
  `twelvemonthneg` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ip_age_breakdown`
--

CREATE TABLE `ip_age_breakdown` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `partner` int(10) UNSIGNED NOT NULL,
  `age_band_id` int(10) UNSIGNED NOT NULL,
  `pos` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `neg` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ip_entrypoint`
--

CREATE TABLE `ip_entrypoint` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(14) DEFAULT NULL,
  `partner` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `entrypoint` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ip_iprophylaxis`
--

CREATE TABLE `ip_iprophylaxis` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(14) DEFAULT NULL,
  `partner` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `prophylaxis` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ip_mprophylaxis`
--

CREATE TABLE `ip_mprophylaxis` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(14) DEFAULT NULL,
  `partner` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `prophylaxis` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ip_rejections`
--

CREATE TABLE `ip_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `partner` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ip_summary`
--

CREATE TABLE `ip_summary` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(14) DEFAULT NULL,
  `partner` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `avgage` int(10) DEFAULT NULL,
  `medage` int(10) DEFAULT NULL,
  `received` int(10) DEFAULT NULL,
  `alltests` int(10) DEFAULT NULL COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `firstdna` int(10) DEFAULT NULL,
  `confirmdna` int(10) DEFAULT NULL,
  `repeatspos` int(10) DEFAULT NULL,
  `repeatposPOS` int(10) DEFAULT NULL,
  `confirmedPOS` int(10) DEFAULT NULL,
  `actualinfants` int(10) DEFAULT NULL,
  `actualinfantsPOS` int(10) DEFAULT NULL,
  `infantsless2m` int(10) DEFAULT NULL,
  `infantsless2mPOS` int(10) DEFAULT NULL,
  `infantsless2mNEG` int(10) DEFAULT NULL,
  `infantsless2mREJ` int(10) DEFAULT NULL,
  `infantsless2w` int(10) DEFAULT NULL,
  `infantsless2wPOS` int(10) DEFAULT NULL,
  `infants4to6w` int(10) DEFAULT NULL,
  `infants4to6wPOS` int(10) DEFAULT NULL,
  `infantsabove2m` int(10) DEFAULT NULL,
  `infantsabove2mPOS` int(10) DEFAULT NULL,
  `adults` int(10) DEFAULT NULL,
  `adultsPOS` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `rpos` int(10) DEFAULT '0',
  `rneg` int(10) DEFAULT '0',
  `allpos` int(10) DEFAULT '0',
  `allneg` int(10) DEFAULT '0',
  `redraw` int(10) DEFAULT NULL,
  `rejected` int(10) DEFAULT NULL,
  `validation_confirmedpos` int(10) DEFAULT NULL,
  `validation_repeattest` int(10) DEFAULT NULL,
  `validation_viralload` int(10) DEFAULT NULL,
  `validation_adult` int(10) DEFAULT NULL,
  `validation_unknownsite` int(10) DEFAULT NULL,
  `enrolled` int(10) DEFAULT NULL,
  `dead` int(10) DEFAULT NULL,
  `ltfu` int(10) DEFAULT NULL,
  `adult` int(10) DEFAULT NULL,
  `transout` int(10) DEFAULT NULL,
  `other` int(10) DEFAULT NULL,
  `sitessending` int(10) DEFAULT NULL,
  `tat1` int(10) DEFAULT '0',
  `tat2` int(10) DEFAULT '0',
  `tat3` int(10) DEFAULT '0',
  `tat4` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ip_summary_yearly`
--

CREATE TABLE `ip_summary_yearly` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(14) DEFAULT NULL,
  `partner` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `avgage` int(10) DEFAULT NULL,
  `medage` int(10) DEFAULT NULL,
  `received` int(10) DEFAULT NULL,
  `alltests` int(10) DEFAULT NULL COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `firstdna` int(10) DEFAULT NULL,
  `confirmdna` int(10) DEFAULT NULL,
  `repeatspos` int(10) DEFAULT NULL,
  `repeatposPOS` int(10) DEFAULT NULL,
  `confirmedPOS` int(10) DEFAULT NULL,
  `actualinfants` int(10) DEFAULT NULL,
  `actualinfantsPOS` int(10) DEFAULT NULL,
  `infantsless2m` int(10) DEFAULT NULL,
  `infantsless2mPOS` int(10) DEFAULT NULL,
  `infantsless2mNEG` int(10) DEFAULT NULL,
  `infantsless2mREJ` int(10) DEFAULT NULL,
  `infantsless2w` int(10) DEFAULT NULL,
  `infantsless2wPOS` int(10) DEFAULT NULL,
  `infants4to6w` int(10) DEFAULT NULL,
  `infants4to6wPOS` int(10) DEFAULT NULL,
  `infantsabove2m` int(10) DEFAULT NULL,
  `infantsabove2mPOS` int(10) DEFAULT NULL,
  `adults` int(10) DEFAULT NULL,
  `adultsPOS` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `rpos` int(10) DEFAULT '0',
  `rneg` int(10) DEFAULT '0',
  `allpos` int(10) DEFAULT '0',
  `allneg` int(10) DEFAULT '0',
  `redraw` int(10) DEFAULT NULL,
  `rejected` int(10) DEFAULT NULL,
  `validation_confirmedpos` int(10) DEFAULT NULL,
  `validation_repeattest` int(10) DEFAULT NULL,
  `validation_viralload` int(10) DEFAULT NULL,
  `validation_adult` int(10) DEFAULT NULL,
  `validation_unknownsite` int(10) DEFAULT NULL,
  `enrolled` int(10) DEFAULT NULL,
  `dead` int(10) DEFAULT NULL,
  `ltfu` int(10) DEFAULT NULL,
  `adult` int(10) DEFAULT NULL,
  `transout` int(10) DEFAULT NULL,
  `other` int(10) DEFAULT NULL,
  `sitessending` int(10) DEFAULT NULL,
  `tat1` int(10) DEFAULT '0',
  `tat2` int(10) DEFAULT '0',
  `tat3` int(10) DEFAULT '0',
  `tat4` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `lablogs`
--

CREATE TABLE `lablogs` (
  `ID` int(10) NOT NULL,
  `lab` varchar(10) NOT NULL DEFAULT '0',
  `logdate` date DEFAULT NULL,
  `testtype` int(10) NOT NULL DEFAULT '0' COMMENT 'EID=1, VL=2',
  `yeartodate` int(10) NOT NULL DEFAULT '0' COMMENT 'all tests current  year to now',
  `monthtodate` int(10) NOT NULL DEFAULT '0' COMMENT 'all tests current month to now',
  `receivedsamples` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples received at lab on the date specified',
  `enteredsamplesatlab` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples entered/logged onto system  on the date specified at lab',
  `enteredsamplesatsite` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples entered/logged onto system  on the date specified from site',
  `enteredreceivedsameday` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples entered/logged onto system  on the date specified received on log date',
  `enterednotreceivedsameday` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples entered/logged onto system  on the date specified received not on log date',
  `inqueuesamples` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples not in worksheet/waiting to be picked for processing  as of the date specified',
  `oldestinqueuesample` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples not in worksheet/waiting to be picked for processing  as of the date specified',
  `inprocesssamples` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples in worksheets being processing  as of the date specified',
  `abbottinprocess` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples in worksheets being processing  as of the date specified',
  `rocheinprocess` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples in worksheets being processing  as of the date specified',
  `panthainprocess` int(14) NOT NULL DEFAULT '0',
  `processedsamples` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples  processed on the date specified',
  `abbottprocessed` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples  processed on the date specified',
  `rocheprocessed` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples  processed on the date specified',
  `panthaprocessed` int(14) NOT NULL DEFAULT '0',
  `updatedresults` int(14) NOT NULL DEFAULT '0' COMMENT 'all samples  updated with results on the date specified',
  `approvedresults` int(14) NOT NULL DEFAULT '0' COMMENT 'all sample results approved for dispatch/rerun on the date specified',
  `pendingapproval` int(14) NOT NULL DEFAULT '0' COMMENT 'all sample results pending apprval for dispatch/rerun on the date specified',
  `dispatchedresults` int(14) NOT NULL DEFAULT '0' COMMENT 'all sample results released for printing/email/sms on the date specified',
  `oneweek` int(14) NOT NULL DEFAULT '0' COMMENT 'all sample that stayed in lab for atleasts a week from time drawn',
  `twoweeks` int(14) NOT NULL DEFAULT '0' COMMENT 'all sample that stayed in lab for 2 weeks from time drawn',
  `threeweeks` int(14) NOT NULL DEFAULT '0' COMMENT 'all sample that stayed in lab for 3 weeks from time drawn',
  `aboveamonth` int(14) NOT NULL DEFAULT '0' COMMENT 'all sample that stayed in lab for over a month from time drawn',
  `pendingsynch` int(14) NOT NULL DEFAULT '0' COMMENT 'all results pending synchronization to NASCOP as of the date specified.',
  `dateupdated` datetime DEFAULT NULL COMMENT 'all results pending synchronization to NASCOP as of the date specified.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `labs`
--

CREATE TABLE `labs` (
  `ID` int(14) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(32) DEFAULT NULL,
  `labname` varchar(50) DEFAULT NULL,
  `labdesc` varchar(50) DEFAULT NULL,
  `lablocation` varchar(50) DEFAULT NULL,
  `labtel1` varchar(32) DEFAULT NULL,
  `labtel2` varchar(32) DEFAULT NULL,
  `taqman` int(1) DEFAULT '1',
  `abbott` int(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `labs`
--

INSERT INTO `labs` (`ID`, `name`, `email`, `labname`, `labdesc`, `lablocation`, `labtel1`, `labtel2`, `taqman`, `abbott`) VALUES
(1, '', NULL, 'CHU Yopougon', NULL, '', NULL, NULL, 1, 1),
(2, '', NULL, 'CePReF', NULL, NULL, NULL, NULL, 1, 1),
(3, '', NULL, 'HG Dabou', NULL, NULL, NULL, NULL, 1, 1),
(4, '', NULL, 'Hopital Methodiste Dabou', NULL, NULL, NULL, NULL, 1, 1),
(5, '', NULL, 'SAPH Toupah', NULL, NULL, NULL, NULL, 1, 1),
(6, '', NULL, 'FSUCom de  Port Bouët II', NULL, NULL, NULL, NULL, 1, 1),
(7, '', NULL, 'Centre plus', NULL, NULL, NULL, NULL, 1, 1),
(8, '', NULL, 'FSU Com Niangon sud', NULL, NULL, NULL, NULL, 1, 1),
(9, '', NULL, 'CAT Man', NULL, NULL, NULL, NULL, 1, 1),
(10, '', NULL, 'FSUCom Kouté', NULL, NULL, NULL, NULL, 1, 1),
(11, '', NULL, 'FSUCom andokoi', NULL, NULL, NULL, NULL, 1, 1),
(12, '', NULL, 'Centre nazareen', NULL, NULL, NULL, NULL, 1, 1),
(13, '', NULL, 'HG Biankouma', NULL, NULL, NULL, NULL, 1, 1),
(14, '', NULL, 'HG Danané', NULL, NULL, NULL, NULL, 1, 1),
(15, '', NULL, 'HG Zouan hounien', NULL, NULL, NULL, NULL, 1, 1),
(16, '', NULL, 'CHR Touba', NULL, NULL, NULL, NULL, 1, 1),
(17, '', NULL, 'CHR Odienné', NULL, NULL, NULL, NULL, 1, 1),
(18, '', NULL, 'CSU Pietro bonelli', NULL, NULL, NULL, NULL, 1, 1),
(19, '', NULL, 'HG Madinani', NULL, NULL, NULL, NULL, 1, 1),
(20, '', NULL, 'HG Duekoue', NULL, NULL, NULL, NULL, 1, 1),
(21, '', NULL, 'HG Blolequin', NULL, NULL, NULL, NULL, 1, 1),
(22, '', NULL, 'HG Bangolo', NULL, NULL, NULL, NULL, 1, 1),
(23, '', NULL, 'CHR guiglo', NULL, NULL, NULL, NULL, 1, 1),
(24, '', NULL, 'HG Toulepleu', NULL, NULL, NULL, NULL, 1, 1),
(25, '', NULL, 'HG Tai', NULL, NULL, NULL, NULL, 1, 1),
(26, '', NULL, 'HG Kouibly', NULL, NULL, NULL, NULL, 1, 1),
(27, '', NULL, 'CHR Man', NULL, NULL, NULL, NULL, 1, 1),
(28, '', NULL, 'CSU LIBREVILLE Man', NULL, NULL, NULL, NULL, 1, 1),
(29, '', NULL, 'FSU Attécoubé', NULL, NULL, NULL, NULL, 1, 1),
(30, '', NULL, 'FSU Willi', NULL, NULL, NULL, NULL, 1, 1),
(31, '', NULL, 'HG Treichville', NULL, NULL, NULL, NULL, 1, 1),
(32, '', NULL, 'CHR San Pédro', NULL, NULL, NULL, NULL, 1, 1),
(33, '', NULL, 'HG Sassandra', NULL, NULL, NULL, NULL, 1, 1),
(34, '', NULL, 'Mté HKB San Pedro', NULL, NULL, NULL, NULL, 1, 1),
(35, '', NULL, 'HG Soubré', NULL, NULL, NULL, NULL, 1, 1),
(36, '', NULL, 'HG Guéyo', NULL, NULL, NULL, NULL, 1, 1),
(37, '', NULL, 'HG Tabou', NULL, NULL, NULL, NULL, 1, 1),
(38, '', NULL, 'HG Buyo', NULL, NULL, NULL, NULL, 1, 1),
(39, '', NULL, 'CM SOGB Grand Berebi', NULL, NULL, NULL, NULL, 1, 1),
(40, '', NULL, 'CMS SAPH San Pedro', NULL, NULL, NULL, NULL, 1, 1),
(41, '', NULL, 'CSU Grabo', NULL, NULL, NULL, NULL, 1, 1),
(42, '', NULL, 'CSU Méagui', NULL, NULL, NULL, NULL, 1, 1),
(43, '', NULL, 'CeDReS', NULL, NULL, NULL, NULL, 1, 1),
(44, '', NULL, 'INSP', NULL, NULL, NULL, NULL, 1, 1),
(45, '', NULL, 'INHP', NULL, NULL, NULL, NULL, 1, 1),
(46, '', NULL, 'CIRBA', NULL, NULL, NULL, NULL, 1, 1),
(47, '', NULL, 'HG Marcory', NULL, NULL, NULL, NULL, 1, 1),
(48, '', NULL, 'Ko\'Khoua', NULL, NULL, NULL, NULL, 1, 1),
(49, '', NULL, 'CAT Adjamé', NULL, NULL, NULL, NULL, 1, 1),
(50, '', NULL, 'HG Grand Lahou', NULL, NULL, NULL, NULL, 1, 1),
(51, '', NULL, 'HG Jacqueville', NULL, NULL, NULL, NULL, 1, 1),
(52, '', NULL, 'Espace confiance', NULL, NULL, NULL, NULL, 1, 1),
(53, '', NULL, 'Pierre angulaire', NULL, NULL, NULL, NULL, 1, 1),
(54, '', NULL, 'HMA', NULL, NULL, NULL, NULL, 1, 1),
(55, '', NULL, 'GSPM', NULL, NULL, NULL, NULL, 1, 1),
(56, '', NULL, 'Hope', NULL, NULL, NULL, NULL, 1, 1),
(57, '', NULL, 'CAT Bouake ', NULL, NULL, NULL, NULL, 1, 1),
(58, '', NULL, 'CHU Bouake', NULL, NULL, NULL, NULL, 1, 1),
(59, '', NULL, 'CSAS Bouake', NULL, NULL, NULL, NULL, 1, 1),
(60, '', NULL, 'H Saint Camille  Bouake', NULL, NULL, NULL, NULL, 1, 1),
(61, '', NULL, 'HG Beoumi', NULL, NULL, NULL, NULL, 1, 1),
(62, '', NULL, 'HG Sakassou', NULL, NULL, NULL, NULL, 1, 1),
(63, '', NULL, 'CHR Katiola', NULL, NULL, NULL, NULL, 1, 1),
(64, '', NULL, 'HG Dabakala', NULL, NULL, NULL, NULL, 1, 1),
(65, '', NULL, 'HG Niakara', NULL, NULL, NULL, NULL, 1, 1),
(66, '', NULL, 'CHR Korhogo', NULL, NULL, NULL, NULL, 1, 1),
(67, '', NULL, 'CSU Petit Paris Korhogo', NULL, NULL, NULL, NULL, 1, 1),
(68, '', NULL, 'Dispensaire Baptiste  Torgokaha Korhogo', NULL, NULL, NULL, NULL, 1, 1),
(69, '', NULL, 'CSU Dikodougou Korhogo', NULL, NULL, NULL, NULL, 1, 1),
(70, '', NULL, 'CSU M\'Bengué Korhogo', NULL, NULL, NULL, NULL, 1, 1),
(71, '', NULL, 'CAT  Korhogo', NULL, NULL, NULL, NULL, 1, 1),
(72, '', NULL, 'H Baptiste Ferkessegougou', NULL, NULL, NULL, NULL, 1, 1),
(73, '', NULL, 'HG Ferkessedougou', NULL, NULL, NULL, NULL, 1, 1),
(74, '', NULL, 'CSU Kong Ferkessedougou', NULL, NULL, NULL, NULL, 1, 1),
(75, '', NULL, 'HG Ouangolodougou', NULL, NULL, NULL, NULL, 1, 1),
(76, '', NULL, 'Mission Catholique Oungolodougou', NULL, NULL, NULL, NULL, 1, 1),
(77, '', NULL, 'HG Boundiali', NULL, NULL, NULL, NULL, 1, 1),
(78, '', NULL, 'HG Nassian', NULL, NULL, NULL, NULL, 1, 1),
(79, '', NULL, 'HG Tengrela', NULL, NULL, NULL, NULL, 1, 1),
(80, '', NULL, 'HG Tanda', NULL, NULL, NULL, NULL, 1, 1),
(81, '', NULL, 'CSU Koun fao', NULL, NULL, NULL, NULL, 1, 1),
(82, '', NULL, 'HG Bouna', NULL, NULL, NULL, NULL, 1, 1),
(83, '', NULL, 'CHR Bondoukou', NULL, NULL, NULL, NULL, 1, 1),
(84, '', NULL, 'CAT Bondoukou', NULL, NULL, NULL, NULL, 1, 1),
(85, '', NULL, 'HG Vavoua', NULL, NULL, NULL, NULL, 1, 1),
(86, '', NULL, 'HG Issia', NULL, NULL, NULL, NULL, 1, 1),
(87, '', NULL, 'CSU Saïoua', NULL, NULL, NULL, NULL, 1, 1),
(88, '', NULL, 'CHR Daloa', NULL, NULL, NULL, NULL, 1, 1),
(89, '', NULL, 'CAT Daloa', NULL, NULL, NULL, NULL, 1, 1),
(90, '', NULL, 'Clinique AIBEF de  Daloa', NULL, NULL, NULL, NULL, 1, 1),
(91, '', NULL, 'HG Zoukougbeu', NULL, NULL, NULL, NULL, 1, 1),
(92, '', NULL, 'CHR Séguéla', NULL, NULL, NULL, NULL, 1, 1),
(93, '', NULL, 'HG Kani', NULL, NULL, NULL, NULL, 1, 1),
(94, '', NULL, 'HG Mankono', NULL, NULL, NULL, NULL, 1, 1),
(95, '', NULL, 'CSCNDC Marrandallah', NULL, NULL, NULL, NULL, 1, 1),
(96, '', NULL, 'HG Dianra', NULL, NULL, NULL, NULL, 1, 1),
(97, '', NULL, 'CHR Bouafle', NULL, NULL, NULL, NULL, 1, 1),
(98, '', NULL, 'CSU Bonon', NULL, NULL, NULL, NULL, 1, 1),
(99, '', NULL, 'HG Zuenoula', NULL, NULL, NULL, NULL, 1, 1),
(100, '', NULL, 'CMS Sucrivoire', NULL, NULL, NULL, NULL, 1, 1),
(101, '', NULL, 'CSU Gohitafla', NULL, NULL, NULL, NULL, 1, 1),
(102, '', NULL, 'HG Sinfra', NULL, NULL, NULL, NULL, 1, 1),
(103, '', NULL, 'Dispensaire Christ Roi', NULL, NULL, NULL, NULL, 1, 1),
(104, '', NULL, 'CSU Kononfla', NULL, NULL, NULL, NULL, 1, 1),
(105, '', NULL, 'HG Gagnoa', NULL, NULL, NULL, NULL, 1, 1),
(106, '', NULL, 'CAT Gagnoa', NULL, NULL, NULL, NULL, 1, 1),
(107, '', NULL, 'CHR Gagnoa', NULL, NULL, NULL, NULL, 1, 1),
(108, '', NULL, 'CSU Guiberoua', NULL, NULL, NULL, NULL, 1, 1),
(109, '', NULL, 'HG Oumé', NULL, NULL, NULL, NULL, 1, 1),
(110, '', NULL, 'CSU Diégonéfla', NULL, NULL, NULL, NULL, 1, 1),
(111, '', NULL, 'HG Port Bouet', NULL, NULL, NULL, NULL, 1, 1),
(112, '', NULL, 'CHR Abengourou', NULL, NULL, NULL, NULL, 1, 1),
(113, '', NULL, 'HG Agnibilekro', NULL, NULL, NULL, NULL, 1, 1),
(114, '', NULL, 'HG Bettie', NULL, NULL, NULL, NULL, 1, 1),
(115, '', NULL, 'HG Anyama', NULL, NULL, NULL, NULL, 1, 1),
(116, '', NULL, 'CSU COM Anonkoua Koute', NULL, NULL, NULL, NULL, 1, 1),
(117, '', NULL, 'Centre EL Rapha', NULL, NULL, NULL, NULL, 1, 1),
(118, '', NULL, 'HG Abobo Sud', NULL, NULL, NULL, NULL, 1, 1),
(119, '', NULL, 'CAT Koumassi', NULL, NULL, NULL, NULL, 1, 1),
(120, '', NULL, 'HG Koumassi', NULL, NULL, NULL, NULL, 1, 1),
(121, '', NULL, 'CHR Yamoussoukro', NULL, NULL, NULL, NULL, 1, 1),
(122, '', NULL, 'CMS Walle', NULL, NULL, NULL, NULL, 1, 1),
(123, '', NULL, 'HG Toumodi', NULL, NULL, NULL, NULL, 1, 1),
(124, '', NULL, 'HG Djekanou', NULL, NULL, NULL, NULL, 1, 1),
(125, '', NULL, 'HG Tiebissou', NULL, NULL, NULL, NULL, 1, 1),
(126, '', NULL, 'CHR Dimbokro', NULL, NULL, NULL, NULL, 1, 1),
(127, '', NULL, 'Bocanda', NULL, NULL, NULL, NULL, 1, 1),
(128, '', NULL, 'Bongouanou', NULL, NULL, NULL, NULL, 1, 1),
(129, '', NULL, 'Daoukro', NULL, NULL, NULL, NULL, 1, 1),
(130, '', NULL, 'M\'Bahiakro', NULL, NULL, NULL, NULL, 1, 1),
(131, '', NULL, 'HG Prikro', NULL, NULL, NULL, NULL, 1, 1),
(132, '', NULL, 'HG Didievi', NULL, NULL, NULL, NULL, 1, 1),
(133, '', NULL, 'CMS Ouelle', NULL, NULL, NULL, NULL, 1, 1),
(134, '', NULL, 'Centre PIM', NULL, NULL, NULL, NULL, 1, 1),
(135, '', NULL, 'Hopital General de Arrah', NULL, NULL, NULL, NULL, 1, 1),
(136, '', NULL, 'Hopital General de M\'batto', NULL, NULL, NULL, NULL, 1, 1),
(137, '', NULL, 'RSB de Yamoussoukro', NULL, NULL, NULL, NULL, 1, 1),
(138, '', NULL, 'CSU Com Abobo BC', NULL, NULL, NULL, NULL, 1, 1),
(139, '', NULL, 'CES Aboboté ', NULL, NULL, NULL, NULL, 1, 1),
(140, '', NULL, 'CM SOS village enfants ', NULL, NULL, NULL, NULL, 1, 1),
(141, '', NULL, 'HG Abobo Nord', NULL, NULL, NULL, NULL, 1, 1),
(142, '', NULL, 'HG Bingerville ', NULL, NULL, NULL, NULL, 1, 1),
(143, '', NULL, 'PMI Cocody', NULL, NULL, NULL, NULL, 1, 1),
(144, '', NULL, 'CHU Cocody', NULL, NULL, NULL, NULL, 1, 1),
(145, '', NULL, 'IPCI', NULL, NULL, NULL, NULL, 1, 1),
(146, '', NULL, 'HG Fresco', NULL, NULL, NULL, NULL, 1, 1),
(147, '', NULL, 'HG Adzope', NULL, NULL, NULL, NULL, 1, 1),
(148, '', NULL, 'CHR Agboville', NULL, NULL, NULL, NULL, 1, 1),
(149, '', NULL, 'HG Akoupe', NULL, NULL, NULL, NULL, 1, 1),
(150, '', NULL, 'HG Sikensi', NULL, NULL, NULL, NULL, 1, 1),
(151, '', NULL, 'HG Tiassale', NULL, NULL, NULL, NULL, 1, 1),
(152, '', NULL, 'HG Taabo', NULL, NULL, NULL, NULL, 1, 1),
(153, '', NULL, 'HG Alepe', NULL, NULL, NULL, NULL, 1, 1),
(154, '', NULL, 'CSU Yocoboué', NULL, NULL, NULL, NULL, 1, 1),
(155, '', NULL, 'CAT Abobo', NULL, NULL, NULL, NULL, 1, 1),
(156, '', NULL, 'CAT Divo', NULL, NULL, NULL, NULL, 1, 1),
(157, '', NULL, 'CHR Divo', NULL, NULL, NULL, NULL, 1, 1),
(158, '', NULL, 'HG Guitry', NULL, NULL, NULL, NULL, 1, 1),
(159, '', NULL, 'HG Lakota', NULL, NULL, NULL, NULL, 1, 1),
(160, '', NULL, 'CSU Hiré', NULL, NULL, NULL, NULL, 1, 1),
(161, '', NULL, ' FSU Avocatier', NULL, NULL, NULL, NULL, 1, 1),
(162, '', NULL, 'Camp médico militaire Akouédo', NULL, NULL, NULL, NULL, 1, 1),
(163, '', NULL, 'CMS Gbagbam', NULL, NULL, NULL, NULL, 1, 1),
(164, '', NULL, 'FSU Com Abobo Baoulé', NULL, NULL, NULL, NULL, 1, 1),
(165, '', NULL, 'IRF', NULL, NULL, NULL, NULL, 1, 1),
(166, '', NULL, 'HG Yakasse Attobrou', NULL, NULL, NULL, NULL, 1, 1),
(167, '', NULL, 'Centre médical  Nimatoullah', NULL, NULL, NULL, NULL, 1, 1),
(168, '', NULL, 'HG de Bassam', NULL, NULL, NULL, NULL, 1, 1),
(169, '', NULL, 'HG de Bonoua', NULL, NULL, NULL, NULL, 1, 1),
(170, '', NULL, 'HG d\'Adiaké', NULL, NULL, NULL, NULL, 1, 1),
(171, '', NULL, 'HG Ayamé/ mission Catholique', NULL, NULL, NULL, NULL, 1, 1),
(172, '', NULL, 'HG Maféré', NULL, NULL, NULL, NULL, 1, 1),
(173, '', NULL, 'CHR Aboisso', NULL, NULL, NULL, NULL, 1, 1),
(174, '', NULL, 'HG de Tiapoum', NULL, NULL, NULL, NULL, 1, 1),
(175, '', NULL, 'CNTS Abidjan', NULL, NULL, NULL, NULL, 1, 1),
(176, '', NULL, 'CTS Yamoussoukro', NULL, NULL, NULL, NULL, 1, 1),
(177, '', NULL, 'CTS Daloa', NULL, NULL, NULL, NULL, 1, 1),
(178, '', NULL, 'CTS Abengourou', NULL, NULL, NULL, NULL, 1, 1),
(179, '', NULL, 'Laboratoire du 2eme Bataillon de Daloa', NULL, NULL, NULL, NULL, 1, 1),
(180, '', NULL, 'Laboratoire de la BASA ', NULL, NULL, NULL, NULL, 1, 1),
(181, '', NULL, 'Laboratoire du CPS AGBAN ', NULL, NULL, NULL, NULL, 1, 1),
(182, '', NULL, 'Laboratoire de la BASE NAVALE', NULL, NULL, NULL, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `lab_rejections`
--

CREATE TABLE `lab_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `lab` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lab_summary`
--

CREATE TABLE `lab_summary` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `lab` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `batches` int(10) DEFAULT NULL COMMENT 'all tests=infant + eqa',
  `received` int(10) DEFAULT NULL COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `alltests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL,
  `confirmdna` int(10) DEFAULT NULL,
  `confirmedPOs` int(10) DEFAULT NULL,
  `repeatspos` int(10) DEFAULT NULL,
  `repeatposPOS` int(10) DEFAULT NULL,
  `rejected` int(10) DEFAULT NULL,
  `sitessending` int(10) DEFAULT NULL,
  `tat1` int(10) DEFAULT NULL,
  `tat2` int(10) DEFAULT NULL,
  `tat3` int(10) DEFAULT NULL,
  `tat4` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `national_agebreakdown`
--

CREATE TABLE `national_agebreakdown` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `sixweekspos` int(10) DEFAULT NULL,
  `sixweeksneg` int(10) DEFAULT NULL,
  `sevento3mpos` int(10) DEFAULT NULL,
  `sevento3mneg` int(10) DEFAULT NULL,
  `threemto9mpos` int(10) DEFAULT NULL,
  `threemto9mneg` int(10) DEFAULT NULL,
  `ninemto18mpos` int(10) DEFAULT NULL,
  `ninemto18mneg` int(10) DEFAULT NULL,
  `above18mpos` int(10) DEFAULT NULL,
  `above18mneg` int(10) DEFAULT NULL,
  `nodatapos` int(10) DEFAULT NULL,
  `nodataneg` int(10) DEFAULT NULL,
  `less2wpos` int(10) DEFAULT NULL,
  `less2wneg` int(10) DEFAULT NULL,
  `twoto6wpos` int(10) DEFAULT NULL,
  `twoto6wneg` int(10) DEFAULT NULL,
  `sixto8wpos` int(10) DEFAULT NULL,
  `sixto8wneg` int(10) DEFAULT NULL,
  `sixmonthpos` int(10) DEFAULT NULL,
  `sixmonthneg` int(10) DEFAULT NULL,
  `ninemonthpos` int(10) DEFAULT NULL,
  `ninemonthneg` int(10) DEFAULT NULL,
  `twelvemonthpos` int(10) DEFAULT NULL,
  `twelvemonthneg` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `national_age_breakdown`
--

CREATE TABLE `national_age_breakdown` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `age_band_id` int(10) UNSIGNED NOT NULL,
  `pos` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `neg` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `national_entrypoint`
--

CREATE TABLE `national_entrypoint` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `entrypoint` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `national_iprophylaxis`
--

CREATE TABLE `national_iprophylaxis` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `prophylaxis` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `national_mprophylaxis`
--

CREATE TABLE `national_mprophylaxis` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `prophylaxis` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `national_rejections`
--

CREATE TABLE `national_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `national_summary`
--

CREATE TABLE `national_summary` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `avgage` int(10) DEFAULT '0',
  `medage` int(10) DEFAULT '0',
  `received` int(10) DEFAULT '0',
  `alltests` int(10) DEFAULT '0' COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `firstdna` int(10) DEFAULT '0',
  `confirmdna` int(10) DEFAULT '0',
  `repeatspos` int(10) DEFAULT '0',
  `repeatposPOS` int(10) DEFAULT '0',
  `confirmedPOS` int(10) DEFAULT '0',
  `actualinfants` int(10) DEFAULT '0',
  `actualinfantsPOS` int(10) DEFAULT '0',
  `infantsless2m` int(10) DEFAULT '0',
  `infantsless2mPOS` int(10) DEFAULT '0',
  `infantsless2mNEG` int(10) DEFAULT '0',
  `infantsless2mREJ` int(10) DEFAULT '0' COMMENT 'rejected less 2 months',
  `infantsless2w` int(10) DEFAULT '0' COMMENT 'less 2 weeks',
  `infantsless2wPOS` int(10) DEFAULT '0' COMMENT 'less 2 weeks',
  `infants4to6w` int(10) DEFAULT '0' COMMENT '4-6 weeks',
  `infants4to6wPOS` int(10) DEFAULT '0' COMMENT '4-6 weeks Positive',
  `infantsabove2m` int(10) DEFAULT '0' COMMENT 'above 2 months',
  `infantsabove2mPOS` int(10) DEFAULT '0' COMMENT 'above 2 months positive',
  `adults` int(10) DEFAULT '0',
  `adultsPOS` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `noagePOS` int(10) DEFAULT '0',
  `pos` int(10) DEFAULT '0',
  `neg` int(10) DEFAULT '0',
  `rpos` int(10) DEFAULT '0',
  `rneg` int(10) DEFAULT '0',
  `allpos` int(10) DEFAULT '0',
  `allneg` int(10) DEFAULT '0',
  `redraw` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `validation_confirmedpos` int(10) DEFAULT '0' COMMENT 'hei validation',
  `validation_repeattest` int(10) DEFAULT '0' COMMENT 'hei validation',
  `validation_viralload` int(10) DEFAULT '0' COMMENT 'hei validation',
  `validation_adult` int(10) DEFAULT '0' COMMENT 'hei validation',
  `validation_unknownsite` int(10) DEFAULT '0' COMMENT 'hei validation',
  `enrolled` int(10) DEFAULT '0' COMMENT 'hei enrolled to treatment',
  `dead` int(10) DEFAULT '0' COMMENT 'hei dead',
  `ltfu` int(10) DEFAULT '0' COMMENT 'hei lost to follow up',
  `adult` int(10) DEFAULT '0' COMMENT 'hei adult',
  `transout` int(10) DEFAULT '0' COMMENT 'hei transfer out',
  `other` int(10) DEFAULT '0' COMMENT 'hei other reasons',
  `sitessending` int(10) DEFAULT '0',
  `tat1` int(10) DEFAULT '0',
  `tat2` int(10) DEFAULT '0',
  `tat3` int(10) DEFAULT '0',
  `tat4` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `national_summary_yearly`
--

CREATE TABLE `national_summary_yearly` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `avgage` int(10) DEFAULT '0',
  `medage` int(10) DEFAULT '0',
  `received` int(10) DEFAULT '0',
  `alltests` int(10) DEFAULT '0' COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `firstdna` int(10) DEFAULT '0',
  `confirmdna` int(10) DEFAULT '0',
  `repeatspos` int(10) DEFAULT '0',
  `repeatposPOS` int(10) DEFAULT '0',
  `confirmedPOS` int(10) DEFAULT '0',
  `actualinfants` int(10) DEFAULT '0',
  `actualinfantsPOS` int(10) DEFAULT '0',
  `infantsless2m` int(10) DEFAULT '0',
  `infantsless2mPOS` int(10) DEFAULT '0',
  `infantsless2mNEG` int(10) DEFAULT '0',
  `infantsless2mREJ` int(10) DEFAULT '0' COMMENT 'rejected less 2 months',
  `infantsless2w` int(10) DEFAULT '0' COMMENT 'less 2 weeks',
  `infantsless2wPOS` int(10) DEFAULT '0' COMMENT 'less 2 weeks',
  `infants4to6w` int(10) DEFAULT '0' COMMENT '4-6 weeks',
  `infants4to6wPOS` int(10) DEFAULT '0' COMMENT '4-6 weeks Positive',
  `infantsabove2m` int(10) DEFAULT '0' COMMENT 'above 2 months',
  `infantsabove2mPOS` int(10) DEFAULT '0' COMMENT 'above 2 months positive',
  `adults` int(10) DEFAULT '0',
  `adultsPOS` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `noagePOS` int(10) DEFAULT '0',
  `pos` int(10) DEFAULT '0',
  `neg` int(10) DEFAULT '0',
  `rpos` int(10) DEFAULT '0',
  `rneg` int(10) DEFAULT '0',
  `allpos` int(10) DEFAULT '0',
  `allneg` int(10) DEFAULT '0',
  `redraw` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `validation_confirmedpos` int(10) DEFAULT '0' COMMENT 'hei validation',
  `validation_repeattest` int(10) DEFAULT '0' COMMENT 'hei validation',
  `validation_viralload` int(10) DEFAULT '0' COMMENT 'hei validation',
  `validation_adult` int(10) DEFAULT '0' COMMENT 'hei validation',
  `validation_unknownsite` int(10) DEFAULT '0' COMMENT 'hei validation',
  `enrolled` int(10) DEFAULT '0' COMMENT 'hei enrolled to treatment',
  `dead` int(10) DEFAULT '0' COMMENT 'hei dead',
  `ltfu` int(10) DEFAULT '0' COMMENT 'hei lost to follow up',
  `adult` int(10) DEFAULT '0' COMMENT 'hei adult',
  `transout` int(10) DEFAULT '0' COMMENT 'hei transfer out',
  `other` int(10) DEFAULT '0' COMMENT 'hei other reasons',
  `sitessending` int(10) DEFAULT '0',
  `tat1` int(10) DEFAULT '0',
  `tat2` int(10) DEFAULT '0',
  `tat3` int(10) DEFAULT '0',
  `tat4` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `partners`
--

CREATE TABLE `partners` (
  `ID` int(32) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `partnerDHISCode` varchar(100) DEFAULT NULL,
  `partnertype` varchar(100) DEFAULT NULL,
  `logo` varchar(45) DEFAULT NULL,
  `flag` int(45) DEFAULT NULL,
  `orderno` int(45) DEFAULT NULL,
  `unknown2013` double DEFAULT NULL,
  `unknown2014` double DEFAULT NULL,
  `unknown2015` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `patients`
--

CREATE TABLE `patients` (
  `ID` int(14) NOT NULL,
  `labid` int(14) NOT NULL DEFAULT '0',
  `FacilityMFLcode` int(10) DEFAULT NULL,
  `FacilityDHISCode` varchar(50) DEFAULT NULL,
  `batchno` varchar(50) DEFAULT NULL,
  `patientID` varchar(45) DEFAULT NULL,
  `PatientName` varchar(45) DEFAULT NULL,
  `Age` int(3) DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `PatientPhoneNo` varchar(50) DEFAULT NULL,
  `SampleType` varchar(15) DEFAULT NULL,
  `Justification` varchar(50) DEFAULT NULL,
  `Regimen` varchar(50) DEFAULT NULL,
  `datecollected` varchar(50) DEFAULT NULL,
  `receivedstatus` varchar(50) DEFAULT NULL,
  `rejectedreason` varchar(50) DEFAULT NULL,
  `reason_for_repeat` varchar(50) DEFAULT NULL,
  `datereceived` varchar(50) DEFAULT NULL,
  `datetested` varchar(50) DEFAULT NULL,
  `result` varchar(45) DEFAULT NULL,
  `datedispatched` varchar(50) DEFAULT NULL,
  `labtestedin` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `patients_eid`
--

CREATE TABLE `patients_eid` (
  `ID` int(14) NOT NULL,
  `labid` int(14) NOT NULL DEFAULT '0',
  `FacilityMFLcode` int(10) DEFAULT NULL,
  `FacilityDHISCode` varchar(50) DEFAULT NULL,
  `batchno` varchar(50) DEFAULT NULL,
  `patientID` varchar(45) DEFAULT NULL,
  `PatientName` varchar(45) DEFAULT NULL,
  `Age` int(3) DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `PatientPhoneNo` varchar(50) DEFAULT NULL,
  `motherregimen` varchar(100) DEFAULT NULL,
  `infantregimen` varchar(100) DEFAULT NULL,
  `entrypoint` varchar(100) DEFAULT NULL,
  `feedingtype` varchar(100) DEFAULT NULL,
  `datecollected` varchar(50) DEFAULT NULL,
  `receivedstatus` varchar(50) DEFAULT NULL,
  `pcrtype` varchar(50) DEFAULT NULL,
  `rejectedreason` varchar(50) DEFAULT NULL,
  `reason_for_repeat` varchar(50) DEFAULT NULL,
  `datereceived` varchar(50) DEFAULT NULL,
  `datetested` varchar(50) DEFAULT NULL,
  `result` int(45) DEFAULT NULL,
  `datedispatched` varchar(50) DEFAULT NULL,
  `labtestedin` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `pcrtype`
--

CREATE TABLE `pcrtype` (
  `ID` int(14) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `platforms`
--

CREATE TABLE `platforms` (
  `ID` int(10) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `prophylaxis`
--

CREATE TABLE `prophylaxis` (
  `ID` int(14) NOT NULL,
  `name` varchar(100) NOT NULL,
  `ptype` int(14) NOT NULL,
  `flag` int(14) NOT NULL DEFAULT '1',
  `rank` int(14) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `prophylaxistypes`
--

CREATE TABLE `prophylaxistypes` (
  `ID` int(14) NOT NULL,
  `Name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `provinces`
--

CREATE TABLE `provinces` (
  `ID` int(14) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `receivedstatus`
--

CREATE TABLE `receivedstatus` (
  `ID` int(14) NOT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `rejectedreasons`
--

CREATE TABLE `rejectedreasons` (
  `ID` int(10) NOT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `alias` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='eid rejected reasons';

-- --------------------------------------------------------

--
-- Structure de la table `results`
--

CREATE TABLE `results` (
  `ID` int(14) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `rht_samples`
--

CREATE TABLE `rht_samples` (
  `ID` int(14) NOT NULL,
  `batchno` varchar(50) DEFAULT NULL,
  `age` varchar(50) DEFAULT NULL,
  `dob` varchar(50) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `prophylaxis` varchar(50) DEFAULT NULL,
  `patient` varchar(100) DEFAULT NULL,
  `facility` int(14) DEFAULT NULL,
  `fcode` int(14) DEFAULT NULL,
  `receivedstatus` int(14) DEFAULT NULL,
  `pcrtype` int(14) DEFAULT NULL,
  `spots` int(14) DEFAULT NULL,
  `datecollected` date DEFAULT NULL,
  `datedispatchedfromfacility` varchar(50) DEFAULT NULL,
  `datereceived` date DEFAULT NULL,
  `comments` varchar(5000) DEFAULT NULL,
  `labcomment` varchar(1000) DEFAULT NULL,
  `parentid` bigint(32) DEFAULT '0',
  `rejectedreason` bigint(32) DEFAULT NULL,
  `datetested` date DEFAULT NULL,
  `worksheet` int(14) DEFAULT NULL,
  `interpretation` varchar(100) DEFAULT NULL,
  `result` int(14) DEFAULT NULL,
  `datemodified` date DEFAULT NULL,
  `datedispatched` date DEFAULT NULL,
  `Inworksheet` int(10) DEFAULT '0',
  `BatchComplete` int(10) DEFAULT '0',
  `DispatchComments` varchar(1000) DEFAULT NULL,
  `Flag` int(1) NOT NULL DEFAULT '1',
  `run` int(14) DEFAULT NULL,
  `repeatt` int(1) DEFAULT '0',
  `inputcomplete` int(10) DEFAULT NULL,
  `labtestedin` int(32) NOT NULL DEFAULT '0',
  `dateuploaded` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `shortcodequeries`
--

CREATE TABLE `shortcodequeries` (
  `ID` int(10) NOT NULL,
  `phoneno` varchar(30) DEFAULT NULL,
  `message` varchar(50) DEFAULT NULL,
  `datereceived` datetime DEFAULT NULL,
  `mflcode` varchar(50) DEFAULT NULL,
  `samplecode` varchar(50) DEFAULT NULL,
  `status` int(30) DEFAULT '0',
  `dateresponded` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `sites`
--

CREATE TABLE `sites` (
  `fid` int(10) DEFAULT NULL,
  `partner` int(10) DEFAULT NULL,
  `county` int(10) DEFAULT NULL,
  `synched` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `site_rejections`
--

CREATE TABLE `site_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `facility` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `site_summary`
--

CREATE TABLE `site_summary` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) DEFAULT '0',
  `dateupdated` date DEFAULT NULL,
  `facility` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `avgage` int(10) DEFAULT NULL,
  `medage` int(10) DEFAULT NULL,
  `received` int(10) DEFAULT NULL,
  `alltests` int(10) DEFAULT NULL COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `firstdna` int(10) DEFAULT NULL,
  `confirmdna` int(10) DEFAULT NULL,
  `repeatspos` int(10) DEFAULT NULL,
  `repeatposPOS` int(10) DEFAULT NULL,
  `confirmedPOS` int(10) DEFAULT NULL,
  `actualinfants` int(10) DEFAULT NULL,
  `actualinfantsPOS` int(10) DEFAULT NULL,
  `infantsless2m` int(10) DEFAULT NULL,
  `infantsless2mPOS` int(10) DEFAULT NULL,
  `infantsless2mNEG` int(10) DEFAULT NULL,
  `infantsless2mREJ` int(10) DEFAULT NULL,
  `infantsless2w` int(10) DEFAULT NULL,
  `infantsless2wPOS` int(10) DEFAULT NULL,
  `infants4to6w` int(10) DEFAULT NULL,
  `infants4to6wPOS` int(10) DEFAULT NULL,
  `infantsabove2m` int(10) DEFAULT NULL,
  `infantsabove2mPOS` int(10) DEFAULT NULL,
  `adults` int(10) DEFAULT '0',
  `adultsPOS` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `noagePOS` int(10) DEFAULT '0',
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `rpos` int(10) DEFAULT '0',
  `rneg` int(10) DEFAULT '0',
  `allpos` int(10) DEFAULT '0',
  `allneg` int(10) DEFAULT '0',
  `redraw` int(10) DEFAULT NULL,
  `rejected` int(10) DEFAULT NULL,
  `validation_confirmedpos` int(10) DEFAULT NULL,
  `validation_repeattest` int(10) DEFAULT NULL,
  `validation_viralload` int(10) DEFAULT NULL,
  `validation_adult` int(10) DEFAULT NULL,
  `validation_unknownsite` int(10) DEFAULT NULL,
  `enrolled` int(10) DEFAULT NULL,
  `dead` int(10) DEFAULT NULL,
  `ltfu` int(10) DEFAULT NULL,
  `adult` int(10) DEFAULT NULL,
  `transout` int(10) DEFAULT NULL,
  `other` int(10) DEFAULT NULL,
  `tat1` int(10) DEFAULT NULL,
  `tat2` int(10) DEFAULT NULL,
  `tat3` int(10) DEFAULT NULL,
  `tat4` int(10) DEFAULT NULL,
  `sitessending` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `site_summary_yearly`
--

CREATE TABLE `site_summary_yearly` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) DEFAULT '0',
  `dateupdated` date DEFAULT NULL,
  `facility` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `avgage` int(10) DEFAULT NULL,
  `medage` int(10) DEFAULT NULL,
  `received` int(10) DEFAULT NULL,
  `alltests` int(10) DEFAULT NULL COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `firstdna` int(10) DEFAULT NULL,
  `confirmdna` int(10) DEFAULT NULL,
  `repeatspos` int(10) DEFAULT NULL,
  `repeatposPOS` int(10) DEFAULT NULL,
  `confirmedPOS` int(10) DEFAULT NULL,
  `actualinfants` int(10) DEFAULT NULL,
  `actualinfantsPOS` int(10) DEFAULT NULL,
  `infantsless2m` int(10) DEFAULT NULL,
  `infantsless2mPOS` int(10) DEFAULT NULL,
  `infantsless2mNEG` int(10) DEFAULT NULL,
  `infantsless2mREJ` int(10) DEFAULT NULL,
  `infantsless2w` int(10) DEFAULT NULL,
  `infantsless2wPOS` int(10) DEFAULT NULL,
  `infants4to6w` int(10) DEFAULT NULL,
  `infants4to6wPOS` int(10) DEFAULT NULL,
  `infantsabove2m` int(10) DEFAULT NULL,
  `infantsabove2mPOS` int(10) DEFAULT NULL,
  `adults` int(10) DEFAULT NULL,
  `adultsPOS` int(10) DEFAULT NULL,
  `noage` int(10) DEFAULT '0',
  `noagePOS` int(10) DEFAULT '0',
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `rpos` int(10) DEFAULT '0',
  `rneg` int(10) DEFAULT '0',
  `allpos` int(10) DEFAULT '0',
  `allneg` int(10) DEFAULT '0',
  `redraw` int(10) DEFAULT NULL,
  `rejected` int(10) DEFAULT NULL,
  `validation_confirmedpos` int(10) DEFAULT NULL,
  `validation_repeattest` int(10) DEFAULT NULL,
  `validation_viralload` int(10) DEFAULT NULL,
  `validation_adult` int(10) DEFAULT NULL,
  `validation_unknownsite` int(10) DEFAULT NULL,
  `enrolled` int(10) DEFAULT NULL,
  `dead` int(10) DEFAULT NULL,
  `ltfu` int(10) DEFAULT NULL,
  `adult` int(10) DEFAULT NULL,
  `transout` int(10) DEFAULT NULL,
  `other` int(10) DEFAULT NULL,
  `tat1` int(10) DEFAULT NULL,
  `tat2` int(10) DEFAULT NULL,
  `tat3` int(10) DEFAULT NULL,
  `tat4` int(10) DEFAULT NULL,
  `sitessending` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `subcounty_agebreakdown`
--

CREATE TABLE `subcounty_agebreakdown` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `subcounty` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `sixweekspos` int(10) DEFAULT NULL,
  `sixweeksneg` int(10) DEFAULT NULL,
  `sevento3mpos` int(10) DEFAULT NULL,
  `sevento3mneg` int(10) DEFAULT NULL,
  `threemto9mpos` int(10) DEFAULT NULL,
  `threemto9mneg` int(10) DEFAULT NULL,
  `ninemto18mpos` int(10) DEFAULT NULL,
  `ninemto18mneg` int(10) DEFAULT NULL,
  `above18mpos` int(10) DEFAULT NULL,
  `above18mneg` int(10) DEFAULT NULL,
  `nodatapos` int(10) DEFAULT NULL,
  `nodataneg` int(10) DEFAULT NULL,
  `less2wpos` int(10) DEFAULT NULL,
  `less2wneg` int(10) DEFAULT NULL,
  `twoto6wpos` int(10) DEFAULT NULL,
  `twoto6wneg` int(10) DEFAULT NULL,
  `sixto8wpos` int(10) DEFAULT NULL,
  `sixto8wneg` int(10) DEFAULT NULL,
  `sixmonthpos` int(10) DEFAULT NULL,
  `sixmonthneg` int(10) DEFAULT NULL,
  `ninemonthpos` int(10) DEFAULT NULL,
  `ninemonthneg` int(10) DEFAULT NULL,
  `twelvemonthpos` int(10) DEFAULT NULL,
  `twelvemonthneg` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `subcounty_age_breakdown`
--

CREATE TABLE `subcounty_age_breakdown` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `subcounty` int(10) UNSIGNED NOT NULL,
  `age_band_id` int(10) UNSIGNED NOT NULL,
  `pos` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `neg` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `subcounty_entrypoint`
--

CREATE TABLE `subcounty_entrypoint` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `subcounty` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `entrypoint` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `subcounty_iprophylaxis`
--

CREATE TABLE `subcounty_iprophylaxis` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `subcounty` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `prophylaxis` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `subcounty_mprophylaxis`
--

CREATE TABLE `subcounty_mprophylaxis` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `subcounty` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `prophylaxis` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `redraw` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `subcounty_rejections`
--

CREATE TABLE `subcounty_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `subcounty` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `subcounty_summary`
--

CREATE TABLE `subcounty_summary` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `subcounty` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `avgage` int(10) DEFAULT NULL,
  `medage` int(10) DEFAULT NULL,
  `received` int(10) DEFAULT NULL,
  `alltests` int(10) DEFAULT NULL COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `firstdna` int(10) DEFAULT NULL,
  `confirmdna` int(10) DEFAULT NULL,
  `repeatspos` int(10) DEFAULT NULL,
  `repeatposPOS` int(10) DEFAULT NULL,
  `confirmedPOS` int(10) DEFAULT NULL,
  `actualinfants` int(10) DEFAULT NULL,
  `actualinfantsPOS` int(10) DEFAULT NULL,
  `infantsless2m` int(10) DEFAULT NULL,
  `infantsless2mPOS` int(10) DEFAULT NULL,
  `infantsless2mNEG` int(10) DEFAULT NULL,
  `infantsless2mREJ` int(10) DEFAULT NULL,
  `infantsless2w` int(10) DEFAULT NULL,
  `infantsless2wPOS` int(10) DEFAULT NULL,
  `infants4to6w` int(10) DEFAULT NULL,
  `infants4to6wPOS` int(10) DEFAULT NULL,
  `infantsabove2m` int(10) DEFAULT NULL,
  `infantsabove2mPOS` int(10) DEFAULT NULL,
  `adults` int(10) DEFAULT NULL,
  `adultsPOS` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `rpos` int(10) DEFAULT '0',
  `rneg` int(10) DEFAULT '0',
  `allpos` int(10) DEFAULT '0',
  `allneg` int(10) DEFAULT '0',
  `redraw` int(10) DEFAULT NULL,
  `rejected` int(10) DEFAULT NULL,
  `validation_confirmedpos` int(10) DEFAULT NULL,
  `validation_repeattest` int(10) DEFAULT NULL,
  `validation_viralload` int(10) DEFAULT NULL,
  `validation_adult` int(10) DEFAULT NULL,
  `validation_unknownsite` int(10) DEFAULT NULL,
  `enrolled` int(10) DEFAULT NULL,
  `dead` int(10) DEFAULT NULL,
  `ltfu` int(10) DEFAULT NULL,
  `adult` int(10) DEFAULT NULL,
  `transout` int(10) DEFAULT NULL,
  `other` int(10) DEFAULT NULL,
  `sitessending` int(10) DEFAULT NULL,
  `tat1` int(10) DEFAULT NULL,
  `tat2` int(10) DEFAULT NULL,
  `tat3` int(10) DEFAULT NULL,
  `tat4` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `subcounty_summary_yearly`
--

CREATE TABLE `subcounty_summary_yearly` (
  `ID` int(10) NOT NULL,
  `dateupdated` varchar(10) DEFAULT NULL,
  `subcounty` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `avgage` int(10) DEFAULT NULL,
  `medage` int(10) DEFAULT NULL,
  `received` int(10) DEFAULT NULL,
  `alltests` int(10) DEFAULT NULL COMMENT 'all tests=infant + eqa',
  `eqatests` int(10) DEFAULT NULL,
  `tests` int(10) DEFAULT NULL,
  `firstdna` int(10) DEFAULT NULL,
  `confirmdna` int(10) DEFAULT NULL,
  `repeatspos` int(10) DEFAULT NULL,
  `repeatposPOS` int(10) DEFAULT NULL,
  `confirmedPOS` int(10) DEFAULT NULL,
  `actualinfants` int(10) DEFAULT NULL,
  `actualinfantsPOS` int(10) DEFAULT NULL,
  `infantsless2m` int(10) DEFAULT NULL,
  `infantsless2mPOS` int(10) DEFAULT NULL,
  `infantsless2mNEG` int(10) DEFAULT NULL,
  `infantsless2mREJ` int(10) DEFAULT NULL,
  `infantsless2w` int(10) DEFAULT NULL,
  `infantsless2wPOS` int(10) DEFAULT NULL,
  `infants4to6w` int(10) DEFAULT NULL,
  `infants4to6wPOS` int(10) DEFAULT NULL,
  `infantsabove2m` int(10) DEFAULT NULL,
  `infantsabove2mPOS` int(10) DEFAULT NULL,
  `adults` int(10) DEFAULT NULL,
  `adultsPOS` int(10) DEFAULT NULL,
  `pos` int(10) DEFAULT NULL,
  `neg` int(10) DEFAULT NULL,
  `rpos` int(10) DEFAULT '0',
  `rneg` int(10) DEFAULT '0',
  `allpos` int(10) DEFAULT '0',
  `allneg` int(10) DEFAULT '0',
  `redraw` int(10) DEFAULT NULL,
  `rejected` int(10) DEFAULT NULL,
  `validation_confirmedpos` int(10) DEFAULT NULL,
  `validation_repeattest` int(10) DEFAULT NULL,
  `validation_viralload` int(10) DEFAULT NULL,
  `validation_adult` int(10) DEFAULT NULL,
  `validation_unknownsite` int(10) DEFAULT NULL,
  `enrolled` int(10) DEFAULT NULL,
  `dead` int(10) DEFAULT NULL,
  `ltfu` int(10) DEFAULT NULL,
  `adult` int(10) DEFAULT NULL,
  `transout` int(10) DEFAULT NULL,
  `other` int(10) DEFAULT NULL,
  `sitessending` int(10) DEFAULT NULL,
  `tat1` int(10) DEFAULT NULL,
  `tat2` int(10) DEFAULT NULL,
  `tat3` int(10) DEFAULT NULL,
  `tat4` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `survey`
--

CREATE TABLE `survey` (
  `id` int(10) UNSIGNED NOT NULL,
  `facility` int(10) UNSIGNED DEFAULT '0',
  `county` int(10) UNSIGNED DEFAULT '0',
  `poc` tinyint(1) UNSIGNED DEFAULT '0',
  `name` varchar(50) DEFAULT '0',
  `survey_date` date DEFAULT NULL,
  `surveyor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `surveyors`
--

CREATE TABLE `surveyors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `admin` tinyint(4) DEFAULT '0',
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `survey_details`
--

CREATE TABLE `survey_details` (
  `id` int(11) NOT NULL,
  `survey_id` int(11) DEFAULT '0',
  `date-of-birth` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `entrypoint` int(11) DEFAULT NULL,
  `date-of-visit` date DEFAULT NULL,
  `date-collected` date DEFAULT NULL,
  `date-tested` date DEFAULT NULL,
  `date-dispatch` date DEFAULT NULL,
  `result` int(11) DEFAULT NULL,
  `art-initiated` tinyint(1) DEFAULT NULL,
  `date-art` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `testtype`
--

CREATE TABLE `testtype` (
  `ID` int(10) NOT NULL,
  `name` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `userKey` varchar(100) NOT NULL,
  `userToken` varchar(256) NOT NULL,
  `accessLevel` varchar(256) NOT NULL,
  `createdBy` varchar(50) DEFAULT NULL,
  `updatedBy` varchar(50) DEFAULT NULL,
  `createdDate` date DEFAULT NULL,
  `updatedDate` date DEFAULT NULL,
  `allowedHits` int(11) NOT NULL DEFAULT '1000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `user_types`
--

CREATE TABLE `user_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `view_facilitys`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `view_facilitys` (
`ID` smallint(5) unsigned
,`originalID` int(14)
,`longitude` varchar(200)
,`latitude` varchar(200)
,`DHIScode` varchar(50)
,`facilitycode` int(10)
,`name` varchar(100)
,`burden` varchar(200)
,`totalartmar` int(200)
,`asofdate` date
,`totalartsep15` int(200)
,`smsprinter` int(14)
,`flag` int(11)
,`ART` varchar(5)
,`district` int(30)
,`subcounty` varchar(100)
,`partner` int(14)
,`partnername` varchar(100)
,`partner2` int(14)
,`county` int(32)
,`countyname` varchar(150)
,`province` int(14)
,`active` int(11)
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vipin_test_view`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vipin_test_view` (
`ID` int(10)
,`sorted` int(10)
,`month` int(10)
,`year` int(10)
,`facility` int(10)
,`received` int(14)
,`alltests` int(10)
,`sustxfail` int(10)
,`confirmtx` int(10)
,`confirm2vl` int(10)
,`repeattests` int(10)
,`rejected` int(10)
,`dbs` int(10)
,`plasma` int(10)
,`edta` int(10)
,`maletest` int(10)
,`femaletest` int(10)
,`adults` int(10)
,`paeds` int(10)
,`noage` int(10)
,`Undetected` int(10)
,`less1000` int(10)
,`less5000` int(10)
,`above5000` int(10)
,`invalids` int(10)
,`less5` int(10)
,`less10` int(10)
,`less15` int(10)
,`less18` int(10)
,`over18` int(10)
,`sitessending` int(10)
,`less2` int(10)
,`less9` int(10)
,`less14` int(10)
,`less19` int(10)
,`less24` int(10)
,`over25` int(10)
,`tat1` int(10)
,`tat2` int(10)
,`tat3` int(10)
,`tat4` int(10)
,`acname` varchar(50)
,`vsaage` int(10)
,`vsasustxfail` int(10)
,`vsaundetected` int(10)
,`vsaless1000` int(10)
);

-- --------------------------------------------------------

--
-- Structure de la table `viraljustifications`
--

CREATE TABLE `viraljustifications` (
  `id` int(10) NOT NULL,
  `displaylabel` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `flag` int(50) DEFAULT '1',
  `rank` int(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `viralpmtcttype`
--

CREATE TABLE `viralpmtcttype` (
  `ID` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `subID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `viralprophylaxis`
--

CREATE TABLE `viralprophylaxis` (
  `ID` int(50) NOT NULL,
  `displaylabel` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(100) NOT NULL,
  `line` int(100) NOT NULL,
  `ptype` int(14) NOT NULL DEFAULT '2',
  `category` int(14) NOT NULL DEFAULT '3'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `viralrejectedreasons`
--

CREATE TABLE `viralrejectedreasons` (
  `ID` int(10) NOT NULL,
  `Name` varchar(200) DEFAULT NULL,
  `alias` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `viralsampletype`
--

CREATE TABLE `viralsampletype` (
  `ID` int(10) NOT NULL,
  `sampletype` int(14) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `typecode` int(100) DEFAULT NULL,
  `flag` int(10) DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `viralsampletypedetails`
--

CREATE TABLE `viralsampletypedetails` (
  `ID` int(10) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `flag` int(10) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_county_age`
--

CREATE TABLE `vl_county_age` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `county` int(10) DEFAULT '0',
  `age` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_county_gender`
--

CREATE TABLE `vl_county_gender` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `county` int(10) DEFAULT '0',
  `gender` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_county_justification`
--

CREATE TABLE `vl_county_justification` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `county` int(10) DEFAULT '0',
  `justification` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(11) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_county_pmtct`
--

CREATE TABLE `vl_county_pmtct` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `county` int(10) UNSIGNED NOT NULL,
  `pmtcttype` int(10) UNSIGNED NOT NULL,
  `sustxfail` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `confirmtx` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `confirm2vl` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `baseline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `baselinesustxfail` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `rejected` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `invalids` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `undetected` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `less1000` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `less5000` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `above5000` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_county_regimen`
--

CREATE TABLE `vl_county_regimen` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `county` int(10) DEFAULT '0',
  `regimen` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_county_rejections`
--

CREATE TABLE `vl_county_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `county` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_county_sampletype`
--

CREATE TABLE `vl_county_sampletype` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `county` int(10) DEFAULT '0',
  `sampletype` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_county_summary`
--

CREATE TABLE `vl_county_summary` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `county` int(10) DEFAULT '0',
  `received` int(14) DEFAULT '0',
  `alltests` int(10) DEFAULT '0' COMMENT 'all tests=infant + eqa',
  `sustxfail` int(10) DEFAULT '0' COMMENT '>1000cpml',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) NOT NULL DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `alldbs` int(10) DEFAULT '0',
  `allplasma` int(10) DEFAULT '0',
  `alledta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0' COMMENT '>15yrs',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0' COMMENT 'invalid, failed, collect new sample',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `sitessending` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0',
  `tat1` int(10) DEFAULT '0',
  `tat2` int(10) DEFAULT '0',
  `tat3` int(10) DEFAULT '0',
  `tat4` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vl_county_summary_view`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vl_county_summary_view` (
`ID` int(10)
,`sorted` int(10)
,`month` int(10)
,`year` int(10)
,`county` int(10)
,`received` int(14)
,`alltests` int(10)
,`sustxfail` int(10)
,`confirmtx` int(10)
,`confirm2vl` int(10)
,`repeattests` int(10)
,`rejected` int(10)
,`dbs` int(10)
,`plasma` int(10)
,`edta` int(10)
,`maletest` int(10)
,`femaletest` int(10)
,`nogendertest` int(10)
,`adults` int(10)
,`paeds` int(10)
,`noage` int(10)
,`Undetected` int(10)
,`less1000` int(10)
,`less5000` int(10)
,`above5000` int(10)
,`invalids` int(10)
,`less5` int(10)
,`less10` int(10)
,`less15` int(10)
,`less18` int(10)
,`over18` int(10)
,`sitessending` int(10)
,`less2` int(10)
,`less9` int(10)
,`less14` int(10)
,`less19` int(10)
,`less24` int(10)
,`over25` int(10)
,`tat1` int(10)
,`tat2` int(10)
,`tat3` int(10)
,`tat4` int(10)
,`justificationName` varchar(100)
,`justificationId` int(10)
,`jtests` int(10)
,`jsustxfail` int(10)
,`jundetected` int(10)
,`jless1000` int(10)
,`ageCategoryName` varchar(50)
,`ageCategoryId` int(10)
,`dsustxfail` int(10)
,`dundetected` int(10)
,`dless1000` int(10)
,`sampleName` varchar(100)
,`sampletype` int(14)
,`stdsustxfail` int(10)
,`stdundetected` int(10)
,`stdless1000` int(10)
);

-- --------------------------------------------------------

--
-- Structure de la table `vl_lab_platform`
--

CREATE TABLE `vl_lab_platform` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `platform` int(10) DEFAULT '0',
  `lab` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_lab_rejections`
--

CREATE TABLE `vl_lab_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `lab` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_lab_sampletype`
--

CREATE TABLE `vl_lab_sampletype` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `lab` int(10) DEFAULT '0',
  `sampletype` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_lab_summary`
--

CREATE TABLE `vl_lab_summary` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) DEFAULT '0',
  `dateupdated` date DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `lab` int(10) DEFAULT '0',
  `received` int(14) DEFAULT '0',
  `alltests` int(10) DEFAULT '0' COMMENT 'all tests=infant + eqa',
  `sustxfail` int(10) DEFAULT '0' COMMENT '>1000cpml',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `eqa` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `alldbs` int(10) DEFAULT '0',
  `allplasma` int(10) DEFAULT '0',
  `alledta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0' COMMENT '>15yrs',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0' COMMENT 'invalid, failed, collect new sample',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `sitessending` int(10) DEFAULT '0',
  `tat1` int(10) DEFAULT '0',
  `tat2` int(10) DEFAULT '0',
  `tat3` int(10) DEFAULT '0',
  `tat4` int(10) DEFAULT '0',
  `batches` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vl_lab_summary_view`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vl_lab_summary_view` (
`ID` int(10)
,`sorted` int(10)
,`month` int(10)
,`year` int(10)
,`lab` int(10)
,`received` int(14)
,`alltests` int(10)
,`sustxfail` int(10)
,`confirmtx` int(10)
,`confirm2vl` int(10)
,`repeattests` int(10)
,`rejected` int(10)
,`dbs` int(10)
,`plasma` int(10)
,`edta` int(10)
,`maletest` int(10)
,`femaletest` int(10)
,`adults` int(10)
,`paeds` int(10)
,`noage` int(10)
,`Undetected` int(10)
,`less1000` int(10)
,`less5000` int(10)
,`above5000` int(10)
,`invalids` int(10)
,`less5` int(10)
,`less10` int(10)
,`less15` int(10)
,`less18` int(10)
,`over18` int(10)
,`sitessending` int(10)
,`tat1` int(10)
,`tat2` int(10)
,`tat3` int(10)
,`tat4` int(10)
,`batches` int(10)
,`less2` int(10)
,`less9` int(10)
,`less14` int(10)
,`less19` int(10)
,`less24` int(10)
,`over25` int(10)
,`dSampleType` int(10)
,`dUndetected` int(10)
,`dless1000` int(10)
,`dless5000` int(10)
,`dabove5000` int(10)
,`dsampleTypeName` varchar(100)
,`dPlatform` int(10)
,`dTests` int(10)
,`dPlatformName` varchar(50)
,`dlabName` varchar(50)
);

-- --------------------------------------------------------

--
-- Structure de la table `vl_national_age`
--

CREATE TABLE `vl_national_age` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `age` int(10) DEFAULT '0' COMMENT 'get from age category',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `malenonsuppressed` int(10) DEFAULT '0',
  `femalenonsuppressed` int(10) DEFAULT '0',
  `nogendernonsuppressed` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_national_gender`
--

CREATE TABLE `vl_national_gender` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `gender` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_national_justification`
--

CREATE TABLE `vl_national_justification` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `justification` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_national_pmtct`
--

CREATE TABLE `vl_national_pmtct` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `pmtcttype` int(10) UNSIGNED NOT NULL,
  `sustxfail` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `confirmtx` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `confirm2vl` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `baseline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `baselinesustxfail` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `rejected` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `invalids` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `undetected` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `less1000` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `less5000` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `above5000` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_national_regimen`
--

CREATE TABLE `vl_national_regimen` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `regimen` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_national_rejections`
--

CREATE TABLE `vl_national_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_national_sampletype`
--

CREATE TABLE `vl_national_sampletype` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `sampletype` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_national_summary`
--

CREATE TABLE `vl_national_summary` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `actualpatients` int(10) DEFAULT '0',
  `received` int(14) DEFAULT '0',
  `alltests` int(10) DEFAULT '0' COMMENT 'all tests=infant + eqa',
  `sustxfail` int(10) DEFAULT '0' COMMENT '>1000cpml',
  `confirmtx` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `alldbs` int(10) DEFAULT '0',
  `allplasma` int(10) DEFAULT '0',
  `alledta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0' COMMENT '>15yrs',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0' COMMENT 'invalid, failed, collect new sample',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `sitessending` int(10) DEFAULT '0',
  `tat1` int(10) DEFAULT '0',
  `tat2` int(10) DEFAULT '0',
  `tat3` int(10) DEFAULT '0',
  `tat4` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0' COMMENT '0.1-2',
  `less9` int(10) DEFAULT '0' COMMENT '2-9',
  `less14` int(10) DEFAULT '0' COMMENT '10-14',
  `less19` int(10) DEFAULT '0' COMMENT '15-19',
  `less24` int(10) DEFAULT '0' COMMENT '20-24',
  `over25` int(10) DEFAULT '0' COMMENT '25+'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vl_national_summary_view`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vl_national_summary_view` (
`ID` int(10)
,`sorted` int(10)
,`month` int(10)
,`year` int(10)
,`actualpatients` int(10)
,`received` int(14)
,`alltests` int(10)
,`sustxfail` int(10)
,`confirmtx` int(10)
,`repeattests` int(10)
,`confirm2vl` int(10)
,`rejected` int(10)
,`dbs` int(10)
,`plasma` int(10)
,`edta` int(10)
,`maletest` int(10)
,`femaletest` int(10)
,`nogendertest` int(10)
,`adults` int(10)
,`paeds` int(10)
,`noage` int(10)
,`Undetected` int(10)
,`less1000` int(10)
,`less5000` int(10)
,`above5000` int(10)
,`invalids` int(10)
,`less5` int(10)
,`less10` int(10)
,`less15` int(10)
,`less18` int(10)
,`over18` int(10)
,`sitessending` int(10)
,`tat1` int(10)
,`tat2` int(10)
,`tat3` int(10)
,`tat4` int(10)
,`less2` int(10)
,`less9` int(10)
,`less14` int(10)
,`less19` int(10)
,`less24` int(10)
,`over25` int(10)
,`justificationName` varchar(100)
,`justificationId` int(10)
,`jtests` int(10)
,`jsustxfail` int(10)
,`jundetected` int(10)
,`jless1000` int(10)
,`ageCategoryName` varchar(50)
,`ageCategoryId` int(10)
,`dsustxfail` int(10)
,`dundetected` int(10)
,`dless1000` int(10)
,`sampleName` varchar(100)
,`sampletype` int(14)
,`stdsustxfail` int(10)
,`stdundetected` int(10)
,`stdless1000` int(10)
);

-- --------------------------------------------------------

--
-- Structure de la table `vl_partner_age`
--

CREATE TABLE `vl_partner_age` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `partner` int(10) DEFAULT '0',
  `age` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_partner_gender`
--

CREATE TABLE `vl_partner_gender` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `year` int(10) DEFAULT '0',
  `partner` int(10) DEFAULT '0',
  `gender` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_partner_justification`
--

CREATE TABLE `vl_partner_justification` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `partner` int(10) DEFAULT '0',
  `justification` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(11) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_partner_pmtct`
--

CREATE TABLE `vl_partner_pmtct` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `partner` int(10) UNSIGNED NOT NULL,
  `pmtcttype` int(10) UNSIGNED NOT NULL,
  `sustxfail` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `confirmtx` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `confirm2vl` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `baseline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `baselinesustxfail` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `rejected` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `invalids` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `undetected` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `less1000` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `less5000` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `above5000` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_partner_regimen`
--

CREATE TABLE `vl_partner_regimen` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `partner` int(10) DEFAULT '0',
  `regimen` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_partner_rejections`
--

CREATE TABLE `vl_partner_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `partner` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_partner_sampletype`
--

CREATE TABLE `vl_partner_sampletype` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `partner` int(10) DEFAULT '0',
  `sampletype` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_partner_summary`
--

CREATE TABLE `vl_partner_summary` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `partner` int(10) DEFAULT '0',
  `received` int(14) DEFAULT '0',
  `alltests` int(10) DEFAULT '0' COMMENT 'all tests=infant + eqa',
  `sustxfail` int(10) DEFAULT '0' COMMENT '>1000cpml',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `alldbs` int(10) DEFAULT '0',
  `allplasma` int(10) DEFAULT '0',
  `alledta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0' COMMENT '>15yrs',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0' COMMENT 'invalid, failed, collect new sample',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `sitessending` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0',
  `tat1` int(10) DEFAULT '0',
  `tat2` int(10) DEFAULT '0',
  `tat3` int(10) DEFAULT '0',
  `tat4` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vl_partner_summary_view`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vl_partner_summary_view` (
`ID` int(10)
,`sorted` int(10)
,`month` int(10)
,`year` int(10)
,`partner` int(10)
,`received` int(14)
,`alltests` int(10)
,`sustxfail` int(10)
,`confirmtx` int(10)
,`confirm2vl` int(10)
,`repeattests` int(10)
,`rejected` int(10)
,`dbs` int(10)
,`plasma` int(10)
,`edta` int(10)
,`maletest` int(10)
,`femaletest` int(10)
,`nogendertest` int(10)
,`adults` int(10)
,`paeds` int(10)
,`noage` int(10)
,`Undetected` int(10)
,`less1000` int(10)
,`less5000` int(10)
,`above5000` int(10)
,`invalids` int(10)
,`less5` int(10)
,`less10` int(10)
,`less15` int(10)
,`less18` int(10)
,`over18` int(10)
,`sitessending` int(10)
,`less2` int(10)
,`less9` int(10)
,`less14` int(10)
,`less19` int(10)
,`less24` int(10)
,`over25` int(10)
,`tat1` int(10)
,`tat2` int(10)
,`tat3` int(10)
,`tat4` int(10)
,`justificationName` varchar(100)
,`justificationId` int(10)
,`jtests` int(10)
,`jsustxfail` int(10)
,`jundetected` int(10)
,`jless1000` int(10)
,`ageCategoryName` varchar(50)
,`ageCategoryId` int(10)
,`dsustxfail` int(10)
,`dundetected` int(10)
,`dless1000` int(10)
,`sampleName` varchar(100)
,`sampletype` int(14)
,`stdsustxfail` int(10)
,`stdundetected` int(10)
,`stdless1000` int(10)
);

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_age`
--

CREATE TABLE `vl_site_age` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` date DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `facility` int(10) DEFAULT '0',
  `age` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_gender`
--

CREATE TABLE `vl_site_gender` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` date DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `facility` int(10) DEFAULT '0',
  `gender` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_justification`
--

CREATE TABLE `vl_site_justification` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `facility` int(10) DEFAULT '0',
  `justification` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_patient_tracking`
--

CREATE TABLE `vl_site_patient_tracking` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` date DEFAULT NULL,
  `year` int(10) NOT NULL DEFAULT '0',
  `facility` int(10) NOT NULL DEFAULT '0',
  `alltests` int(10) NOT NULL DEFAULT '0' COMMENT 'all tests=infant + eqa',
  `onevl` int(10) NOT NULL DEFAULT '0' COMMENT '>1000cpml',
  `twovl` int(10) NOT NULL DEFAULT '0',
  `threevl` int(10) NOT NULL DEFAULT '0',
  `above3vl` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_pmtct`
--

CREATE TABLE `vl_site_pmtct` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `facility` int(10) UNSIGNED NOT NULL,
  `pmtcttype` int(10) UNSIGNED NOT NULL,
  `sustxfail` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `confirmtx` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `confirm2vl` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `baseline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `baselinesustxfail` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `rejected` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `invalids` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `undetected` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `less1000` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `less5000` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `above5000` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_regimen`
--

CREATE TABLE `vl_site_regimen` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` date DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `facility` int(10) DEFAULT '0',
  `regimen` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_rejections`
--

CREATE TABLE `vl_site_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `facility` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_sampletype`
--

CREATE TABLE `vl_site_sampletype` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` date DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `facility` int(10) DEFAULT '0',
  `sampletype` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_summary`
--

CREATE TABLE `vl_site_summary` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` date DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `facility` int(10) DEFAULT '0',
  `received` int(14) DEFAULT '0',
  `alltests` int(10) DEFAULT '0' COMMENT 'all tests=infant + eqa',
  `sustxfail` int(10) DEFAULT '0' COMMENT '>1000cpml',
  `confirm2vl` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0' COMMENT 'confirmtx',
  `repeattests` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `alldbs` int(10) DEFAULT '0',
  `allplasma` int(10) DEFAULT '0',
  `alledta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0' COMMENT '>15yrs',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0' COMMENT 'invalid, failed, collect new sample',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `sitessending` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0',
  `tat1` int(10) DEFAULT '0',
  `tat2` int(10) DEFAULT '0',
  `tat3` int(10) DEFAULT '0',
  `tat4` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vl_site_summary_view`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vl_site_summary_view` (
`ID` int(10)
,`sorted` int(10)
,`month` int(10)
,`year` int(10)
,`facility` int(10)
,`received` int(14)
,`alltests` int(10)
,`sustxfail` int(10)
,`confirmtx` int(10)
,`confirm2vl` int(10)
,`repeattests` int(10)
,`rejected` int(10)
,`dbs` int(10)
,`plasma` int(10)
,`edta` int(10)
,`maletest` int(10)
,`femaletest` int(10)
,`nogendertest` int(10)
,`adults` int(10)
,`paeds` int(10)
,`noage` int(10)
,`Undetected` int(10)
,`less1000` int(10)
,`less5000` int(10)
,`above5000` int(10)
,`invalids` int(10)
,`less5` int(10)
,`less10` int(10)
,`less15` int(10)
,`less18` int(10)
,`over18` int(10)
,`sitessending` int(10)
,`less2` int(10)
,`less9` int(10)
,`less14` int(10)
,`less19` int(10)
,`less24` int(10)
,`over25` int(10)
,`tat1` int(10)
,`tat2` int(10)
,`tat3` int(10)
,`tat4` int(10)
,`justificationName` varchar(100)
,`justificationId` int(10)
,`jtests` int(10)
,`jsustxfail` int(10)
,`jundetected` int(10)
,`jless1000` int(10)
,`ageCategoryName` varchar(50)
,`ageCategoryId` int(10)
,`dsustxfail` int(10)
,`dundetected` int(10)
,`dless1000` int(10)
,`sampleName` varchar(100)
,`sampletype` int(14)
,`stdsustxfail` int(10)
,`stdundetected` int(10)
,`stdless1000` int(10)
);

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_suppression`
--

CREATE TABLE `vl_site_suppression` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `facility` int(10) UNSIGNED NOT NULL,
  `suppressed` int(10) UNSIGNED DEFAULT '0',
  `nonsuppressed` int(10) UNSIGNED DEFAULT '0',
  `suppression` double(10,4) UNSIGNED DEFAULT '0.0000',
  `coverage` double(10,4) UNSIGNED DEFAULT '0.0000',
  `male_suppressed` int(11) DEFAULT '0',
  `male_nonsuppressed` int(11) DEFAULT '0',
  `female_suppressed` int(11) DEFAULT '0',
  `female_nonsuppressed` int(11) DEFAULT '0',
  `nogender_suppressed` int(11) DEFAULT '0',
  `nogender_nonsuppressed` int(11) DEFAULT '0',
  `noage_suppressed` int(11) DEFAULT '0',
  `noage_nonsuppressed` int(11) DEFAULT '0',
  `less2_suppressed` int(11) DEFAULT '0',
  `less2_nonsuppressed` int(11) DEFAULT '0',
  `less9_suppressed` int(11) DEFAULT '0',
  `less9_nonsuppressed` int(11) DEFAULT '0',
  `less14_suppressed` int(11) DEFAULT '0',
  `less14_nonsuppressed` int(11) DEFAULT '0',
  `less19_suppressed` int(11) DEFAULT '0',
  `less19_nonsuppressed` int(11) DEFAULT '0',
  `less24_suppressed` int(11) DEFAULT '0',
  `less24_nonsuppressed` int(11) DEFAULT '0',
  `over25_suppressed` int(11) DEFAULT '0',
  `over25_nonsuppressed` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_site_suppression_year`
--

CREATE TABLE `vl_site_suppression_year` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `facility` int(10) UNSIGNED NOT NULL,
  `suppressed` int(10) UNSIGNED DEFAULT '0',
  `nonsuppressed` int(10) UNSIGNED DEFAULT '0',
  `suppression` double(10,4) UNSIGNED DEFAULT '0.0000',
  `coverage` double(10,4) UNSIGNED DEFAULT '0.0000',
  `male_suppressed` int(11) DEFAULT '0',
  `male_nonsuppressed` int(11) DEFAULT '0',
  `female_suppressed` int(11) DEFAULT '0',
  `female_nonsuppressed` int(11) DEFAULT '0',
  `nogender_suppressed` int(11) DEFAULT '0',
  `nogender_nonsuppressed` int(11) DEFAULT '0',
  `noage_suppressed` int(11) DEFAULT '0',
  `noage_nonsuppressed` int(11) DEFAULT '0',
  `less2_suppressed` int(11) DEFAULT '0',
  `less2_nonsuppressed` int(11) DEFAULT '0',
  `less9_suppressed` int(11) DEFAULT '0',
  `less9_nonsuppressed` int(11) DEFAULT '0',
  `less14_suppressed` int(11) DEFAULT '0',
  `less14_nonsuppressed` int(11) DEFAULT '0',
  `less19_suppressed` int(11) DEFAULT '0',
  `less19_nonsuppressed` int(11) DEFAULT '0',
  `less24_suppressed` int(11) DEFAULT '0',
  `less24_nonsuppressed` int(11) DEFAULT '0',
  `over25_suppressed` int(11) DEFAULT '0',
  `over25_nonsuppressed` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `vl_subcounty_age`
--

CREATE TABLE `vl_subcounty_age` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `subcounty` int(10) DEFAULT '0',
  `age` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_subcounty_gender`
--

CREATE TABLE `vl_subcounty_gender` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `subcounty` int(10) DEFAULT '0',
  `gender` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_subcounty_justification`
--

CREATE TABLE `vl_subcounty_justification` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `subcounty` int(10) DEFAULT '0',
  `justification` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(11) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_subcounty_pmtct`
--

CREATE TABLE `vl_subcounty_pmtct` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `subcounty` int(10) UNSIGNED NOT NULL,
  `pmtcttype` int(10) UNSIGNED NOT NULL,
  `sustxfail` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `confirmtx` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `confirm2vl` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `baseline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `baselinesustxfail` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `rejected` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `invalids` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `undetected` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `less1000` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `less5000` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `above5000` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_subcounty_regimen`
--

CREATE TABLE `vl_subcounty_regimen` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `subcounty` int(10) DEFAULT '0',
  `regimen` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_subcounty_rejections`
--

CREATE TABLE `vl_subcounty_rejections` (
  `ID` int(10) UNSIGNED NOT NULL,
  `dateupdated` date DEFAULT NULL,
  `subcounty` int(10) UNSIGNED NOT NULL,
  `month` int(10) UNSIGNED NOT NULL,
  `year` int(10) UNSIGNED NOT NULL,
  `rejected_reason` int(10) UNSIGNED NOT NULL,
  `total` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vl_subcounty_sampletype`
--

CREATE TABLE `vl_subcounty_sampletype` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `subcounty` int(10) DEFAULT '0',
  `sampletype` int(10) DEFAULT '0',
  `tests` int(10) DEFAULT '0',
  `sustxfail` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0',
  `confirm2vl` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `repeattests` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vl_subcounty_summary`
--

CREATE TABLE `vl_subcounty_summary` (
  `ID` int(10) NOT NULL,
  `sorted` int(10) NOT NULL DEFAULT '0',
  `dateupdated` varchar(10) DEFAULT NULL,
  `month` int(10) DEFAULT '0',
  `year` int(10) DEFAULT '0',
  `subcounty` int(10) DEFAULT '0',
  `received` int(14) DEFAULT '0',
  `alltests` int(10) DEFAULT '0' COMMENT 'all tests=infant + eqa',
  `sustxfail` int(10) DEFAULT '0' COMMENT '>1000cpml',
  `confirm2vl` int(10) DEFAULT '0',
  `confirmtx` int(10) DEFAULT '0' COMMENT 'confirmtx',
  `repeattests` int(10) DEFAULT '0',
  `baseline` int(10) DEFAULT '0',
  `baselinesustxfail` int(10) DEFAULT '0',
  `rejected` int(10) DEFAULT '0',
  `dbs` int(10) DEFAULT '0',
  `plasma` int(10) DEFAULT '0',
  `edta` int(10) DEFAULT '0',
  `alldbs` int(10) DEFAULT '0',
  `allplasma` int(10) DEFAULT '0',
  `alledta` int(10) DEFAULT '0',
  `maletest` int(10) DEFAULT '0',
  `femaletest` int(10) DEFAULT '0',
  `nogendertest` int(10) DEFAULT '0',
  `adults` int(10) DEFAULT '0',
  `paeds` int(10) DEFAULT '0' COMMENT '>15yrs',
  `noage` int(10) DEFAULT '0',
  `Undetected` int(10) DEFAULT '0',
  `less1000` int(10) DEFAULT '0',
  `less5000` int(10) DEFAULT '0',
  `above5000` int(10) DEFAULT '0',
  `invalids` int(10) DEFAULT '0' COMMENT 'invalid, failed, collect new sample',
  `less5` int(10) DEFAULT '0',
  `less10` int(10) DEFAULT '0',
  `less15` int(10) DEFAULT '0',
  `less18` int(10) DEFAULT '0',
  `over18` int(10) DEFAULT '0',
  `sitessending` int(10) DEFAULT '0',
  `less2` int(10) DEFAULT '0',
  `less9` int(10) DEFAULT '0',
  `less14` int(10) DEFAULT '0',
  `less19` int(10) DEFAULT '0',
  `less24` int(10) DEFAULT '0',
  `over25` int(10) DEFAULT '0',
  `tat1` int(10) DEFAULT '0',
  `tat2` int(10) DEFAULT '0',
  `tat3` int(10) DEFAULT '0',
  `tat4` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vl_subcounty_summary_view`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `vl_subcounty_summary_view` (
`ID` int(10)
,`sorted` int(10)
,`month` int(10)
,`year` int(10)
,`subcounty` int(10)
,`received` int(14)
,`alltests` int(10)
,`sustxfail` int(10)
,`confirmtx` int(10)
,`confirm2vl` int(10)
,`repeattests` int(10)
,`rejected` int(10)
,`dbs` int(10)
,`plasma` int(10)
,`edta` int(10)
,`maletest` int(10)
,`femaletest` int(10)
,`nogendertest` int(10)
,`adults` int(10)
,`paeds` int(10)
,`noage` int(10)
,`Undetected` int(10)
,`less1000` int(10)
,`less5000` int(10)
,`above5000` int(10)
,`invalids` int(10)
,`less5` int(10)
,`less10` int(10)
,`less15` int(10)
,`less18` int(10)
,`over18` int(10)
,`sitessending` int(10)
,`less2` int(10)
,`less9` int(10)
,`less14` int(10)
,`less19` int(10)
,`less24` int(10)
,`over25` int(10)
,`tat1` int(10)
,`tat2` int(10)
,`tat3` int(10)
,`tat4` int(10)
,`justificationName` varchar(100)
,`justificationId` int(10)
,`jtests` int(10)
,`jsustxfail` int(10)
,`jundetected` int(10)
,`jless1000` int(10)
,`ageCategoryName` varchar(50)
,`ageCategoryId` int(10)
,`dsustxfail` int(10)
,`dundetected` int(10)
,`dless1000` int(10)
,`sampleName` varchar(100)
,`sampletype` int(14)
,`stdsustxfail` int(10)
,`stdundetected` int(10)
,`stdless1000` int(10)
);

-- --------------------------------------------------------

--
-- Structure de la vue `view_facilitys`
--
DROP TABLE IF EXISTS `view_facilitys`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `view_facilitys`  AS  select `facilitys`.`ID` AS `ID`,`facilitys`.`originalID` AS `originalID`,`facilitys`.`longitude` AS `longitude`,`facilitys`.`latitude` AS `latitude`,`facilitys`.`DHIScode` AS `DHIScode`,`facilitys`.`facilitycode` AS `facilitycode`,`facilitys`.`name` AS `name`,`facilitys`.`burden` AS `burden`,`facilitys`.`totalartmar` AS `totalartmar`,`facilitys`.`asofdate` AS `asofdate`,`facilitys`.`totalartsep15` AS `totalartsep15`,`facilitys`.`smsprinter` AS `smsprinter`,`facilitys`.`Flag` AS `flag`,`facilitys`.`ART` AS `ART`,`facilitys`.`district` AS `district`,`districts`.`name` AS `subcounty`,`facilitys`.`partner` AS `partner`,`partners`.`name` AS `partnername`,`facilitys`.`partner2` AS `partner2`,`districts`.`county` AS `county`,`countys`.`name` AS `countyname`,`districts`.`province` AS `province`,`facilitys`.`active` AS `active` from (((`facilitys` join `districts`) join `countys`) join `partners`) where ((`facilitys`.`district` = `districts`.`ID`) and (`districts`.`county` = `countys`.`ID`) and (`facilitys`.`partner` = `partners`.`ID`) and (`facilitys`.`Flag` = 1)) ;

-- --------------------------------------------------------

--
-- Structure de la vue `vipin_test_view`
--
DROP TABLE IF EXISTS `vipin_test_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vipin_test_view`  AS  select `vss`.`ID` AS `ID`,`vss`.`sorted` AS `sorted`,`vss`.`month` AS `month`,`vss`.`year` AS `year`,`vss`.`facility` AS `facility`,`vss`.`received` AS `received`,`vss`.`alltests` AS `alltests`,`vss`.`sustxfail` AS `sustxfail`,`vss`.`confirmtx` AS `confirmtx`,`vss`.`confirm2vl` AS `confirm2vl`,`vss`.`repeattests` AS `repeattests`,`vss`.`rejected` AS `rejected`,`vss`.`dbs` AS `dbs`,`vss`.`plasma` AS `plasma`,`vss`.`edta` AS `edta`,`vss`.`maletest` AS `maletest`,`vss`.`femaletest` AS `femaletest`,`vss`.`adults` AS `adults`,`vss`.`paeds` AS `paeds`,`vss`.`noage` AS `noage`,`vss`.`Undetected` AS `Undetected`,`vss`.`less1000` AS `less1000`,`vss`.`less5000` AS `less5000`,`vss`.`above5000` AS `above5000`,`vss`.`invalids` AS `invalids`,`vss`.`less5` AS `less5`,`vss`.`less10` AS `less10`,`vss`.`less15` AS `less15`,`vss`.`less18` AS `less18`,`vss`.`over18` AS `over18`,`vss`.`sitessending` AS `sitessending`,`vss`.`less2` AS `less2`,`vss`.`less9` AS `less9`,`vss`.`less14` AS `less14`,`vss`.`less19` AS `less19`,`vss`.`less24` AS `less24`,`vss`.`over25` AS `over25`,`vss`.`tat1` AS `tat1`,`vss`.`tat2` AS `tat2`,`vss`.`tat3` AS `tat3`,`vss`.`tat4` AS `tat4`,`ac`.`name` AS `acname`,`vsa`.`age` AS `vsaage`,`vsa`.`sustxfail` AS `vsasustxfail`,`vsa`.`Undetected` AS `vsaundetected`,`vsa`.`less1000` AS `vsaless1000` from ((`vl_site_age` `vsa` join `agecategory` `ac`) join `vl_site_summary` `vss`) where ((`ac`.`ID` = `vsa`.`age`) and (`vss`.`facility` = `vsa`.`facility`)) ;

-- --------------------------------------------------------

--
-- Structure de la vue `vl_county_summary_view`
--
DROP TABLE IF EXISTS `vl_county_summary_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vl_county_summary_view`  AS  select `s`.`ID` AS `ID`,`s`.`sorted` AS `sorted`,`s`.`month` AS `month`,`s`.`year` AS `year`,`s`.`county` AS `county`,`s`.`received` AS `received`,`s`.`alltests` AS `alltests`,`s`.`sustxfail` AS `sustxfail`,`s`.`confirmtx` AS `confirmtx`,`s`.`confirm2vl` AS `confirm2vl`,`s`.`repeattests` AS `repeattests`,`s`.`rejected` AS `rejected`,`s`.`dbs` AS `dbs`,`s`.`plasma` AS `plasma`,`s`.`edta` AS `edta`,`s`.`maletest` AS `maletest`,`s`.`femaletest` AS `femaletest`,`s`.`nogendertest` AS `nogendertest`,`s`.`adults` AS `adults`,`s`.`paeds` AS `paeds`,`s`.`noage` AS `noage`,`s`.`Undetected` AS `Undetected`,`s`.`less1000` AS `less1000`,`s`.`less5000` AS `less5000`,`s`.`above5000` AS `above5000`,`s`.`invalids` AS `invalids`,`s`.`less5` AS `less5`,`s`.`less10` AS `less10`,`s`.`less15` AS `less15`,`s`.`less18` AS `less18`,`s`.`over18` AS `over18`,`s`.`sitessending` AS `sitessending`,`s`.`less2` AS `less2`,`s`.`less9` AS `less9`,`s`.`less14` AS `less14`,`s`.`less19` AS `less19`,`s`.`less24` AS `less24`,`s`.`over25` AS `over25`,`s`.`tat1` AS `tat1`,`s`.`tat2` AS `tat2`,`s`.`tat3` AS `tat3`,`s`.`tat4` AS `tat4`,`vj`.`name` AS `justificationName`,`jd`.`justification` AS `justificationId`,`jd`.`tests` AS `jtests`,`jd`.`sustxfail` AS `jsustxfail`,`jd`.`Undetected` AS `jundetected`,`jd`.`less1000` AS `jless1000`,`ac`.`name` AS `ageCategoryName`,`ad`.`age` AS `ageCategoryId`,`ad`.`sustxfail` AS `dsustxfail`,`ad`.`Undetected` AS `dundetected`,`ad`.`less1000` AS `dless1000`,`vst`.`name` AS `sampleName`,`vst`.`sampletype` AS `sampletype`,`std`.`sustxfail` AS `stdsustxfail`,`std`.`Undetected` AS `stdundetected`,`std`.`less1000` AS `stdless1000` from ((((((`vl_county_summary` `s` left join `vl_county_age` `ad` on(((`s`.`county` = `ad`.`county`) and (`s`.`year` = `ad`.`year`) and (`s`.`month` = `ad`.`month`)))) left join `agecategory` `ac` on((`ad`.`age` = `ac`.`ID`))) left join `vl_county_justification` `jd` on(((`s`.`county` = `jd`.`county`) and (`s`.`year` = `jd`.`year`) and (`s`.`month` = `jd`.`month`)))) left join `viraljustifications` `vj` on((`jd`.`justification` = `vj`.`id`))) left join `vl_county_sampletype` `std` on(((`s`.`county` = `std`.`county`) and (`s`.`year` = `std`.`year`) and (`s`.`month` = `std`.`month`)))) left join `viralsampletype` `vst` on((`std`.`sampletype` = `vst`.`sampletype`))) ;

-- --------------------------------------------------------

--
-- Structure de la vue `vl_lab_summary_view`
--
DROP TABLE IF EXISTS `vl_lab_summary_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vl_lab_summary_view`  AS  select `vls`.`ID` AS `ID`,`vls`.`sorted` AS `sorted`,`vls`.`month` AS `month`,`vls`.`year` AS `year`,`vls`.`lab` AS `lab`,`vls`.`received` AS `received`,`vls`.`alltests` AS `alltests`,`vls`.`sustxfail` AS `sustxfail`,`vls`.`confirmtx` AS `confirmtx`,`vls`.`confirm2vl` AS `confirm2vl`,`vls`.`repeattests` AS `repeattests`,`vls`.`rejected` AS `rejected`,`vls`.`dbs` AS `dbs`,`vls`.`plasma` AS `plasma`,`vls`.`edta` AS `edta`,`vls`.`maletest` AS `maletest`,`vls`.`femaletest` AS `femaletest`,`vls`.`adults` AS `adults`,`vls`.`paeds` AS `paeds`,`vls`.`noage` AS `noage`,`vls`.`Undetected` AS `Undetected`,`vls`.`less1000` AS `less1000`,`vls`.`less5000` AS `less5000`,`vls`.`above5000` AS `above5000`,`vls`.`invalids` AS `invalids`,`vls`.`less5` AS `less5`,`vls`.`less10` AS `less10`,`vls`.`less15` AS `less15`,`vls`.`less18` AS `less18`,`vls`.`over18` AS `over18`,`vls`.`sitessending` AS `sitessending`,`vls`.`tat1` AS `tat1`,`vls`.`tat2` AS `tat2`,`vls`.`tat3` AS `tat3`,`vls`.`tat4` AS `tat4`,`vls`.`batches` AS `batches`,`vls`.`less2` AS `less2`,`vls`.`less9` AS `less9`,`vls`.`less14` AS `less14`,`vls`.`less19` AS `less19`,`vls`.`less24` AS `less24`,`vls`.`over25` AS `over25`,`vlst`.`sampletype` AS `dSampleType`,`vlst`.`Undetected` AS `dUndetected`,`vlst`.`less1000` AS `dless1000`,`vlst`.`less5000` AS `dless5000`,`vlst`.`above5000` AS `dabove5000`,`vst`.`name` AS `dsampleTypeName`,`vlp`.`platform` AS `dPlatform`,`vlp`.`tests` AS `dTests`,`p`.`name` AS `dPlatformName`,`l`.`labname` AS `dlabName` from (((((`vl_lab_summary` `vls` left join `labs` `l` on((`vls`.`lab` = `l`.`ID`))) left join `vl_lab_sampletype` `vlst` on(((`vls`.`lab` = `vlst`.`lab`) and (`vls`.`year` = `vlst`.`year`) and (`vls`.`month` = `vlst`.`month`)))) left join `viralsampletype` `vst` on((`vlst`.`sampletype` = `vst`.`sampletype`))) left join `vl_lab_platform` `vlp` on(((`vlst`.`lab` = `vlp`.`lab`) and (`vlst`.`year` = `vlp`.`year`) and (`vlst`.`month` = `vlp`.`month`)))) left join `platforms` `p` on((`vlp`.`platform` = `p`.`ID`))) ;

-- --------------------------------------------------------

--
-- Structure de la vue `vl_national_summary_view`
--
DROP TABLE IF EXISTS `vl_national_summary_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vl_national_summary_view`  AS  select `s`.`ID` AS `ID`,`s`.`sorted` AS `sorted`,`s`.`month` AS `month`,`s`.`year` AS `year`,`s`.`actualpatients` AS `actualpatients`,`s`.`received` AS `received`,`s`.`alltests` AS `alltests`,`s`.`sustxfail` AS `sustxfail`,`s`.`confirmtx` AS `confirmtx`,`s`.`repeattests` AS `repeattests`,`s`.`confirm2vl` AS `confirm2vl`,`s`.`rejected` AS `rejected`,`s`.`dbs` AS `dbs`,`s`.`plasma` AS `plasma`,`s`.`edta` AS `edta`,`s`.`maletest` AS `maletest`,`s`.`femaletest` AS `femaletest`,`s`.`nogendertest` AS `nogendertest`,`s`.`adults` AS `adults`,`s`.`paeds` AS `paeds`,`s`.`noage` AS `noage`,`s`.`Undetected` AS `Undetected`,`s`.`less1000` AS `less1000`,`s`.`less5000` AS `less5000`,`s`.`above5000` AS `above5000`,`s`.`invalids` AS `invalids`,`s`.`less5` AS `less5`,`s`.`less10` AS `less10`,`s`.`less15` AS `less15`,`s`.`less18` AS `less18`,`s`.`over18` AS `over18`,`s`.`sitessending` AS `sitessending`,`s`.`tat1` AS `tat1`,`s`.`tat2` AS `tat2`,`s`.`tat3` AS `tat3`,`s`.`tat4` AS `tat4`,`s`.`less2` AS `less2`,`s`.`less9` AS `less9`,`s`.`less14` AS `less14`,`s`.`less19` AS `less19`,`s`.`less24` AS `less24`,`s`.`over25` AS `over25`,`vj`.`name` AS `justificationName`,`jd`.`justification` AS `justificationId`,`jd`.`tests` AS `jtests`,`jd`.`sustxfail` AS `jsustxfail`,`jd`.`Undetected` AS `jundetected`,`jd`.`less1000` AS `jless1000`,`ac`.`name` AS `ageCategoryName`,`ad`.`age` AS `ageCategoryId`,`ad`.`sustxfail` AS `dsustxfail`,`ad`.`Undetected` AS `dundetected`,`ad`.`less1000` AS `dless1000`,`vst`.`name` AS `sampleName`,`vst`.`sampletype` AS `sampletype`,`std`.`sustxfail` AS `stdsustxfail`,`std`.`Undetected` AS `stdundetected`,`std`.`less1000` AS `stdless1000` from ((((((`vl_national_summary` `s` left join `vl_national_age` `ad` on(((`s`.`year` = `ad`.`year`) and (`s`.`month` = `ad`.`month`)))) left join `agecategory` `ac` on((`ad`.`age` = `ac`.`ID`))) left join `vl_national_justification` `jd` on(((`s`.`year` = `jd`.`year`) and (`s`.`month` = `jd`.`month`)))) left join `viraljustifications` `vj` on((`jd`.`justification` = `vj`.`id`))) left join `vl_national_sampletype` `std` on(((`s`.`year` = `std`.`year`) and (`s`.`month` = `std`.`month`)))) left join `viralsampletype` `vst` on((`std`.`sampletype` = `vst`.`sampletype`))) ;

-- --------------------------------------------------------

--
-- Structure de la vue `vl_partner_summary_view`
--
DROP TABLE IF EXISTS `vl_partner_summary_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vl_partner_summary_view`  AS  select `s`.`ID` AS `ID`,`s`.`sorted` AS `sorted`,`s`.`month` AS `month`,`s`.`year` AS `year`,`s`.`partner` AS `partner`,`s`.`received` AS `received`,`s`.`alltests` AS `alltests`,`s`.`sustxfail` AS `sustxfail`,`s`.`confirmtx` AS `confirmtx`,`s`.`confirm2vl` AS `confirm2vl`,`s`.`repeattests` AS `repeattests`,`s`.`rejected` AS `rejected`,`s`.`dbs` AS `dbs`,`s`.`plasma` AS `plasma`,`s`.`edta` AS `edta`,`s`.`maletest` AS `maletest`,`s`.`femaletest` AS `femaletest`,`s`.`nogendertest` AS `nogendertest`,`s`.`adults` AS `adults`,`s`.`paeds` AS `paeds`,`s`.`noage` AS `noage`,`s`.`Undetected` AS `Undetected`,`s`.`less1000` AS `less1000`,`s`.`less5000` AS `less5000`,`s`.`above5000` AS `above5000`,`s`.`invalids` AS `invalids`,`s`.`less5` AS `less5`,`s`.`less10` AS `less10`,`s`.`less15` AS `less15`,`s`.`less18` AS `less18`,`s`.`over18` AS `over18`,`s`.`sitessending` AS `sitessending`,`s`.`less2` AS `less2`,`s`.`less9` AS `less9`,`s`.`less14` AS `less14`,`s`.`less19` AS `less19`,`s`.`less24` AS `less24`,`s`.`over25` AS `over25`,`s`.`tat1` AS `tat1`,`s`.`tat2` AS `tat2`,`s`.`tat3` AS `tat3`,`s`.`tat4` AS `tat4`,`vj`.`name` AS `justificationName`,`jd`.`justification` AS `justificationId`,`jd`.`tests` AS `jtests`,`jd`.`sustxfail` AS `jsustxfail`,`jd`.`Undetected` AS `jundetected`,`jd`.`less1000` AS `jless1000`,`ac`.`name` AS `ageCategoryName`,`ad`.`age` AS `ageCategoryId`,`ad`.`sustxfail` AS `dsustxfail`,`ad`.`Undetected` AS `dundetected`,`ad`.`less1000` AS `dless1000`,`vst`.`name` AS `sampleName`,`vst`.`sampletype` AS `sampletype`,`std`.`sustxfail` AS `stdsustxfail`,`std`.`Undetected` AS `stdundetected`,`std`.`less1000` AS `stdless1000` from ((((((`vl_partner_summary` `s` left join `vl_partner_age` `ad` on(((`s`.`partner` = `ad`.`partner`) and (`s`.`year` = `ad`.`year`) and (`s`.`month` = `ad`.`month`)))) left join `agecategory` `ac` on((`ad`.`age` = `ac`.`ID`))) left join `vl_partner_justification` `jd` on(((`s`.`partner` = `jd`.`partner`) and (`s`.`year` = `jd`.`year`) and (`s`.`month` = `jd`.`month`)))) left join `viraljustifications` `vj` on((`jd`.`justification` = `vj`.`id`))) left join `vl_partner_sampletype` `std` on(((`s`.`partner` = `std`.`partner`) and (`s`.`year` = `std`.`year`) and (`s`.`month` = `std`.`month`)))) left join `viralsampletype` `vst` on((`std`.`sampletype` = `vst`.`sampletype`))) ;

-- --------------------------------------------------------

--
-- Structure de la vue `vl_site_summary_view`
--
DROP TABLE IF EXISTS `vl_site_summary_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vl_site_summary_view`  AS  select `s`.`ID` AS `ID`,`s`.`sorted` AS `sorted`,`s`.`month` AS `month`,`s`.`year` AS `year`,`s`.`facility` AS `facility`,`s`.`received` AS `received`,`s`.`alltests` AS `alltests`,`s`.`sustxfail` AS `sustxfail`,`s`.`confirmtx` AS `confirmtx`,`s`.`confirm2vl` AS `confirm2vl`,`s`.`repeattests` AS `repeattests`,`s`.`rejected` AS `rejected`,`s`.`dbs` AS `dbs`,`s`.`plasma` AS `plasma`,`s`.`edta` AS `edta`,`s`.`maletest` AS `maletest`,`s`.`femaletest` AS `femaletest`,`s`.`nogendertest` AS `nogendertest`,`s`.`adults` AS `adults`,`s`.`paeds` AS `paeds`,`s`.`noage` AS `noage`,`s`.`Undetected` AS `Undetected`,`s`.`less1000` AS `less1000`,`s`.`less5000` AS `less5000`,`s`.`above5000` AS `above5000`,`s`.`invalids` AS `invalids`,`s`.`less5` AS `less5`,`s`.`less10` AS `less10`,`s`.`less15` AS `less15`,`s`.`less18` AS `less18`,`s`.`over18` AS `over18`,`s`.`sitessending` AS `sitessending`,`s`.`less2` AS `less2`,`s`.`less9` AS `less9`,`s`.`less14` AS `less14`,`s`.`less19` AS `less19`,`s`.`less24` AS `less24`,`s`.`over25` AS `over25`,`s`.`tat1` AS `tat1`,`s`.`tat2` AS `tat2`,`s`.`tat3` AS `tat3`,`s`.`tat4` AS `tat4`,`vj`.`name` AS `justificationName`,`jd`.`justification` AS `justificationId`,`jd`.`tests` AS `jtests`,`jd`.`sustxfail` AS `jsustxfail`,`jd`.`Undetected` AS `jundetected`,`jd`.`less1000` AS `jless1000`,`ac`.`name` AS `ageCategoryName`,`ad`.`age` AS `ageCategoryId`,`ad`.`sustxfail` AS `dsustxfail`,`ad`.`Undetected` AS `dundetected`,`ad`.`less1000` AS `dless1000`,`vst`.`name` AS `sampleName`,`vst`.`sampletype` AS `sampletype`,`std`.`sustxfail` AS `stdsustxfail`,`std`.`Undetected` AS `stdundetected`,`std`.`less1000` AS `stdless1000` from ((((((`vl_site_summary` `s` left join `vl_site_age` `ad` on(((`s`.`facility` = `ad`.`facility`) and (`s`.`year` = `ad`.`year`) and (`s`.`month` = `ad`.`month`)))) left join `agecategory` `ac` on((`ad`.`age` = `ac`.`ID`))) left join `vl_site_justification` `jd` on(((`s`.`facility` = `jd`.`facility`) and (`s`.`year` = `jd`.`year`) and (`s`.`month` = `jd`.`month`)))) left join `viraljustifications` `vj` on((`jd`.`justification` = `vj`.`id`))) left join `vl_site_sampletype` `std` on(((`s`.`facility` = `std`.`facility`) and (`s`.`year` = `std`.`year`) and (`s`.`month` = `std`.`month`)))) left join `viralsampletype` `vst` on((`std`.`sampletype` = `vst`.`sampletype`))) ;

-- --------------------------------------------------------

--
-- Structure de la vue `vl_subcounty_summary_view`
--
DROP TABLE IF EXISTS `vl_subcounty_summary_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vl_subcounty_summary_view`  AS  select `s`.`ID` AS `ID`,`s`.`sorted` AS `sorted`,`s`.`month` AS `month`,`s`.`year` AS `year`,`s`.`subcounty` AS `subcounty`,`s`.`received` AS `received`,`s`.`alltests` AS `alltests`,`s`.`sustxfail` AS `sustxfail`,`s`.`confirmtx` AS `confirmtx`,`s`.`confirm2vl` AS `confirm2vl`,`s`.`repeattests` AS `repeattests`,`s`.`rejected` AS `rejected`,`s`.`dbs` AS `dbs`,`s`.`plasma` AS `plasma`,`s`.`edta` AS `edta`,`s`.`maletest` AS `maletest`,`s`.`femaletest` AS `femaletest`,`s`.`nogendertest` AS `nogendertest`,`s`.`adults` AS `adults`,`s`.`paeds` AS `paeds`,`s`.`noage` AS `noage`,`s`.`Undetected` AS `Undetected`,`s`.`less1000` AS `less1000`,`s`.`less5000` AS `less5000`,`s`.`above5000` AS `above5000`,`s`.`invalids` AS `invalids`,`s`.`less5` AS `less5`,`s`.`less10` AS `less10`,`s`.`less15` AS `less15`,`s`.`less18` AS `less18`,`s`.`over18` AS `over18`,`s`.`sitessending` AS `sitessending`,`s`.`less2` AS `less2`,`s`.`less9` AS `less9`,`s`.`less14` AS `less14`,`s`.`less19` AS `less19`,`s`.`less24` AS `less24`,`s`.`over25` AS `over25`,`s`.`tat1` AS `tat1`,`s`.`tat2` AS `tat2`,`s`.`tat3` AS `tat3`,`s`.`tat4` AS `tat4`,`vj`.`name` AS `justificationName`,`jd`.`justification` AS `justificationId`,`jd`.`tests` AS `jtests`,`jd`.`sustxfail` AS `jsustxfail`,`jd`.`Undetected` AS `jundetected`,`jd`.`less1000` AS `jless1000`,`ac`.`name` AS `ageCategoryName`,`ad`.`age` AS `ageCategoryId`,`ad`.`sustxfail` AS `dsustxfail`,`ad`.`Undetected` AS `dundetected`,`ad`.`less1000` AS `dless1000`,`vst`.`name` AS `sampleName`,`vst`.`sampletype` AS `sampletype`,`std`.`sustxfail` AS `stdsustxfail`,`std`.`Undetected` AS `stdundetected`,`std`.`less1000` AS `stdless1000` from ((((((`vl_subcounty_summary` `s` left join `vl_subcounty_age` `ad` on(((`s`.`subcounty` = `ad`.`subcounty`) and (`s`.`year` = `ad`.`year`) and (`s`.`month` = `ad`.`month`)))) left join `agecategory` `ac` on((`ad`.`age` = `ac`.`ID`))) left join `vl_subcounty_justification` `jd` on(((`s`.`subcounty` = `jd`.`subcounty`) and (`s`.`year` = `jd`.`year`) and (`s`.`month` = `jd`.`month`)))) left join `viraljustifications` `vj` on((`jd`.`justification` = `vj`.`id`))) left join `vl_subcounty_sampletype` `std` on(((`s`.`subcounty` = `std`.`subcounty`) and (`s`.`year` = `std`.`year`) and (`s`.`month` = `std`.`month`)))) left join `viralsampletype` `vst` on((`std`.`sampletype` = `vst`.`sampletype`))) ;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `administrators_email_unique` (`email`),
  ADD KEY `administrators_user_type_id_foreign` (`user_type_id`);

--
-- Index pour la table `agecategory`
--
ALTER TABLE `agecategory`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `name` (`name`),
  ADD KEY `subID` (`subID`);

--
-- Index pour la table `age_bands`
--
ALTER TABLE `age_bands`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `api_usage`
--
ALTER TABLE `api_usage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_index_api_usage_userKey_apiName_hitDate` (`userKey`,`apiName`,`hitDate`),
  ADD KEY `idx_api_usage_userKey` (`userKey`),
  ADD KEY `idx_api_usage_apiName` (`apiName`),
  ADD KEY `idx_api_usage_hitDate` (`hitDate`),
  ADD KEY `uk_api_usage_userKey_apiName_hitDate` (`userKey`,`apiName`,`hitDate`);

--
-- Index pour la table `countys`
--
ALTER TABLE `countys`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `name` (`name`);

--
-- Index pour la table `county_agebreakdown`
--
ALTER TABLE `county_agebreakdown`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`county`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `sorted` (`dateupdated`),
  ADD KEY `sixweekspos` (`sixweekspos`),
  ADD KEY `sixweeksneg` (`sixweeksneg`),
  ADD KEY `sevento3mpos` (`sevento3mpos`),
  ADD KEY `sevento3mneg` (`sevento3mneg`),
  ADD KEY `threemto9mpos` (`threemto9mpos`),
  ADD KEY `threemto9mneg` (`threemto9mneg`),
  ADD KEY `ninemto18mpos` (`ninemto18mpos`),
  ADD KEY `ninemto18mneg` (`ninemto18mneg`),
  ADD KEY `nodatapos` (`nodatapos`),
  ADD KEY `nodataneg` (`nodataneg`),
  ADD KEY `less2wpos` (`less2wpos`),
  ADD KEY `less2wneg` (`less2wneg`),
  ADD KEY `twoto6wpos` (`twoto6wpos`),
  ADD KEY `twoto6wneg` (`twoto6wneg`),
  ADD KEY `sixto8wpos` (`sixto8wpos`),
  ADD KEY `sixto8wneg` (`sixto8wneg`),
  ADD KEY `sixmonthpos` (`sixmonthpos`),
  ADD KEY `sixmonthneg` (`sixmonthneg`),
  ADD KEY `ninemonthpos` (`ninemonthpos`),
  ADD KEY `ninemonthneg` (`ninemonthneg`),
  ADD KEY `twelvemonthpos` (`twelvemonthpos`),
  ADD KEY `twelvemonthneg` (`twelvemonthneg`);

--
-- Index pour la table `county_age_breakdown`
--
ALTER TABLE `county_age_breakdown`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county_age_breakdown_year_index` (`year`),
  ADD KEY `county_age_breakdown_month_index` (`month`),
  ADD KEY `county_age_breakdown_county_index` (`county`),
  ADD KEY `county_age_breakdown_age_band_id_index` (`age_band_id`);

--
-- Index pour la table `county_entrypoint`
--
ALTER TABLE `county_entrypoint`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`county`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`entrypoint`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `sorted` (`dateupdated`);

--
-- Index pour la table `county_iprophylaxis`
--
ALTER TABLE `county_iprophylaxis`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`county`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`prophylaxis`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `sorted` (`dateupdated`);

--
-- Index pour la table `county_mprophylaxis`
--
ALTER TABLE `county_mprophylaxis`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`county`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`prophylaxis`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `sorted` (`dateupdated`);

--
-- Index pour la table `county_rejections`
--
ALTER TABLE `county_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `county_summary`
--
ALTER TABLE `county_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`county`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`avgage`),
  ADD KEY `medage` (`medage`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`firstdna`),
  ADD KEY `confirmdna` (`confirmdna`),
  ADD KEY `infantsless2m` (`infantsless2m`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`enrolled`),
  ADD KEY `dead` (`dead`),
  ADD KEY `lftu` (`ltfu`),
  ADD KEY `adult` (`adult`),
  ADD KEY `transout` (`transout`),
  ADD KEY `other` (`other`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `sorted` (`dateupdated`),
  ADD KEY `infantsless2m_infantsless2mPOS` (`infantsless2m`,`infantsless2mPOS`),
  ADD KEY `actualinfants` (`actualinfants`),
  ADD KEY `actualinfantsPOS` (`actualinfantsPOS`),
  ADD KEY `repeatspos` (`repeatspos`),
  ADD KEY `adultsPOS` (`adultsPOS`),
  ADD KEY `adults` (`adults`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `validation_confirmedpos` (`validation_confirmedpos`),
  ADD KEY `validation_repeattest` (`validation_repeattest`),
  ADD KEY `validation_viralload` (`validation_viralload`),
  ADD KEY `validation_adult` (`validation_adult`),
  ADD KEY `validation_unknownsite` (`validation_unknownsite`),
  ADD KEY `infantsless2mREJ` (`infantsless2mREJ`),
  ADD KEY `infantsless2mNEG` (`infantsless2mNEG`),
  ADD KEY `infantsless2w` (`infantsless2w`),
  ADD KEY `infantsless2wPOS` (`infantsless2wPOS`),
  ADD KEY `infants4to6w` (`infants4to6w`),
  ADD KEY `infants4to6wPOS` (`infants4to6wPOS`),
  ADD KEY `infantsabove2m` (`infantsabove2m`),
  ADD KEY `infantsabove2mPOS` (`infantsabove2mPOS`);

--
-- Index pour la table `county_summary_yearly`
--
ALTER TABLE `county_summary_yearly`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`county`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`avgage`),
  ADD KEY `medage` (`medage`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`firstdna`),
  ADD KEY `confirmdna` (`confirmdna`),
  ADD KEY `infantsless2m` (`infantsless2m`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`enrolled`),
  ADD KEY `dead` (`dead`),
  ADD KEY `lftu` (`ltfu`),
  ADD KEY `adult` (`adult`),
  ADD KEY `transout` (`transout`),
  ADD KEY `other` (`other`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `sorted` (`dateupdated`),
  ADD KEY `infantsless2m_infantsless2mPOS` (`infantsless2m`,`infantsless2mPOS`),
  ADD KEY `actualinfants` (`actualinfants`),
  ADD KEY `actualinfantsPOS` (`actualinfantsPOS`),
  ADD KEY `repeatspos` (`repeatspos`),
  ADD KEY `adultsPOS` (`adultsPOS`),
  ADD KEY `adults` (`adults`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `validation_confirmedpos` (`validation_confirmedpos`),
  ADD KEY `validation_repeattest` (`validation_repeattest`),
  ADD KEY `validation_viralload` (`validation_viralload`),
  ADD KEY `validation_adult` (`validation_adult`),
  ADD KEY `validation_unknownsite` (`validation_unknownsite`),
  ADD KEY `infantsless2mREJ` (`infantsless2mREJ`),
  ADD KEY `infantsless2mNEG` (`infantsless2mNEG`),
  ADD KEY `infantsless2w` (`infantsless2w`),
  ADD KEY `infantsless2wPOS` (`infantsless2wPOS`),
  ADD KEY `infants4to6w` (`infants4to6w`),
  ADD KEY `infants4to6wPOS` (`infants4to6wPOS`),
  ADD KEY `infantsabove2m` (`infantsabove2m`),
  ADD KEY `infantsabove2mPOS` (`infantsabove2mPOS`);

--
-- Index pour la table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `entry_points`
--
ALTER TABLE `entry_points`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `name` (`name`);

--
-- Index pour la table `facilitys`
--
ALTER TABLE `facilitys`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `facilitycode` (`facilitycode`),
  ADD KEY `DHIScode` (`DHIScode`),
  ADD KEY `name` (`name`),
  ADD KEY `district` (`district`),
  ADD KEY `lab` (`lab`);

--
-- Index pour la table `feedings`
--
ALTER TABLE `feedings`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`),
  ADD KEY `name` (`name`),
  ADD KEY `description` (`description`);

--
-- Index pour la table `gender`
--
ALTER TABLE `gender`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `hei_categories`
--
ALTER TABLE `hei_categories`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `hei_validation`
--
ALTER TABLE `hei_validation`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `ip_agebreakdown`
--
ALTER TABLE `ip_agebreakdown`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`partner`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `sixweekspos` (`sixweekspos`),
  ADD KEY `sixweeksneg` (`sixweeksneg`),
  ADD KEY `sevento3mpos` (`sevento3mpos`),
  ADD KEY `sevento3mneg` (`sevento3mneg`),
  ADD KEY `threemto9mpos` (`threemto9mpos`),
  ADD KEY `threemto9mneg` (`threemto9mneg`),
  ADD KEY `ninemto18mpos` (`ninemto18mpos`),
  ADD KEY `ninemto18mneg` (`ninemto18mneg`),
  ADD KEY `nodatapos` (`nodatapos`),
  ADD KEY `nodataneg` (`nodataneg`),
  ADD KEY `less2wpos` (`less2wpos`),
  ADD KEY `less2wneg` (`less2wneg`),
  ADD KEY `twoto6wpos` (`twoto6wpos`),
  ADD KEY `twoto6wneg` (`twoto6wneg`),
  ADD KEY `sixto8wpos` (`sixto8wpos`),
  ADD KEY `sixto8wneg` (`sixto8wneg`),
  ADD KEY `sixmonthpos` (`sixmonthpos`),
  ADD KEY `sixmonthneg` (`sixmonthneg`),
  ADD KEY `ninemonthpos` (`ninemonthpos`),
  ADD KEY `ninemonthneg` (`ninemonthneg`),
  ADD KEY `twelvemonthpos` (`twelvemonthpos`),
  ADD KEY `twelvemonthneg` (`twelvemonthneg`);

--
-- Index pour la table `ip_age_breakdown`
--
ALTER TABLE `ip_age_breakdown`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ip_age_breakdown_year_index` (`year`),
  ADD KEY `ip_age_breakdown_month_index` (`month`),
  ADD KEY `ip_age_breakdown_partner_index` (`partner`),
  ADD KEY `ip_age_breakdown_age_band_id_index` (`age_band_id`);

--
-- Index pour la table `ip_entrypoint`
--
ALTER TABLE `ip_entrypoint`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`partner`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`entrypoint`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`);

--
-- Index pour la table `ip_iprophylaxis`
--
ALTER TABLE `ip_iprophylaxis`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`partner`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`prophylaxis`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `partner` (`partner`);

--
-- Index pour la table `ip_mprophylaxis`
--
ALTER TABLE `ip_mprophylaxis`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`partner`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`prophylaxis`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`);

--
-- Index pour la table `ip_rejections`
--
ALTER TABLE `ip_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `ip_summary`
--
ALTER TABLE `ip_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`partner`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`avgage`),
  ADD KEY `medage` (`medage`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`firstdna`),
  ADD KEY `confirmdna` (`confirmdna`),
  ADD KEY `infantsless2m` (`infantsless2m`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`enrolled`),
  ADD KEY `dead` (`dead`),
  ADD KEY `lftu` (`ltfu`),
  ADD KEY `adult` (`adult`),
  ADD KEY `transout` (`transout`),
  ADD KEY `other` (`other`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `infantsless2mPOS` (`infantsless2mPOS`),
  ADD KEY `actualinfants` (`actualinfants`),
  ADD KEY `actualinfantsPOS` (`actualinfantsPOS`),
  ADD KEY `adults` (`adults`),
  ADD KEY `adultsPOS` (`adultsPOS`),
  ADD KEY `repeatspos` (`repeatspos`),
  ADD KEY `infantsless2mNEG` (`infantsless2mNEG`),
  ADD KEY `infantsless2mREJ` (`infantsless2mREJ`),
  ADD KEY `infantsless2w` (`infantsless2w`),
  ADD KEY `infants4to6w` (`infants4to6w`),
  ADD KEY `infants4to6wPOS` (`infants4to6wPOS`),
  ADD KEY `infantsabove2m` (`infantsabove2m`),
  ADD KEY `infantsabove2mPOS` (`infantsabove2mPOS`),
  ADD KEY `infantsless2wPOS` (`infantsless2wPOS`),
  ADD KEY `validation_confirmedpos` (`validation_confirmedpos`),
  ADD KEY `validation_repeattest` (`validation_repeattest`),
  ADD KEY `validation_viralload` (`validation_viralload`),
  ADD KEY `validation_adult` (`validation_adult`),
  ADD KEY `validation_unknownsite` (`validation_unknownsite`);

--
-- Index pour la table `ip_summary_yearly`
--
ALTER TABLE `ip_summary_yearly`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`partner`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`avgage`),
  ADD KEY `medage` (`medage`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`firstdna`),
  ADD KEY `confirmdna` (`confirmdna`),
  ADD KEY `infantsless2m` (`infantsless2m`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`enrolled`),
  ADD KEY `dead` (`dead`),
  ADD KEY `lftu` (`ltfu`),
  ADD KEY `adult` (`adult`),
  ADD KEY `transout` (`transout`),
  ADD KEY `other` (`other`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `infantsless2mPOS` (`infantsless2mPOS`),
  ADD KEY `actualinfants` (`actualinfants`),
  ADD KEY `actualinfantsPOS` (`actualinfantsPOS`),
  ADD KEY `adults` (`adults`),
  ADD KEY `adultsPOS` (`adultsPOS`),
  ADD KEY `repeatspos` (`repeatspos`),
  ADD KEY `infantsless2mNEG` (`infantsless2mNEG`),
  ADD KEY `infantsless2mREJ` (`infantsless2mREJ`),
  ADD KEY `infantsless2w` (`infantsless2w`),
  ADD KEY `infants4to6w` (`infants4to6w`),
  ADD KEY `infants4to6wPOS` (`infants4to6wPOS`),
  ADD KEY `infantsabove2m` (`infantsabove2m`),
  ADD KEY `infantsabove2mPOS` (`infantsabove2mPOS`),
  ADD KEY `infantsless2wPOS` (`infantsless2wPOS`),
  ADD KEY `validation_confirmedpos` (`validation_confirmedpos`),
  ADD KEY `validation_repeattest` (`validation_repeattest`),
  ADD KEY `validation_viralload` (`validation_viralload`),
  ADD KEY `validation_adult` (`validation_adult`),
  ADD KEY `validation_unknownsite` (`validation_unknownsite`);

--
-- Index pour la table `lablogs`
--
ALTER TABLE `lablogs`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `labid` (`lab`),
  ADD KEY `logdate` (`logdate`),
  ADD KEY `receivedsamples` (`receivedsamples`),
  ADD KEY `enteredsamples` (`enteredsamplesatlab`),
  ADD KEY `inqueuesamples` (`inqueuesamples`),
  ADD KEY `inprocesssamples` (`inprocesssamples`),
  ADD KEY `processedsamples` (`processedsamples`),
  ADD KEY `updatedresults` (`updatedresults`),
  ADD KEY `approvedresults` (`approvedresults`),
  ADD KEY `pendingapproval` (`pendingapproval`),
  ADD KEY `dispatchedresults` (`dispatchedresults`),
  ADD KEY `pendingsynch` (`pendingsynch`),
  ADD KEY `enteredsamplesatlab` (`enteredsamplesatlab`),
  ADD KEY `enteredsamplesatsite` (`enteredsamplesatsite`),
  ADD KEY `enteredreceivedsameday` (`enteredreceivedsameday`),
  ADD KEY `enterednotreceivedsameday` (`enterednotreceivedsameday`),
  ADD KEY `oldestinqueuesample` (`oldestinqueuesample`),
  ADD KEY `abbottinprocess` (`abbottinprocess`),
  ADD KEY `rocheinprocess` (`rocheinprocess`),
  ADD KEY `abbottprocessed` (`abbottprocessed`),
  ADD KEY `rocheprocessed` (`rocheprocessed`),
  ADD KEY `testtype` (`testtype`),
  ADD KEY `panthaprocessed` (`panthaprocessed`),
  ADD KEY `panthainprocess` (`panthainprocess`),
  ADD KEY `yeartodate` (`yeartodate`),
  ADD KEY `monthtodate` (`monthtodate`),
  ADD KEY `oneweek` (`oneweek`),
  ADD KEY `twoweeks` (`twoweeks`),
  ADD KEY `threeweeks` (`threeweeks`),
  ADD KEY `aboveamonth` (`aboveamonth`);

--
-- Index pour la table `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `name` (`name`),
  ADD KEY `labname` (`labname`),
  ADD KEY `labdesc` (`labdesc`);

--
-- Index pour la table `lab_rejections`
--
ALTER TABLE `lab_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `lab_summary`
--
ALTER TABLE `lab_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`lab`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `alltests` (`batches`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`tat1`),
  ADD KEY `confirmdna` (`tat2`),
  ADD KEY `infantsless2m` (`tat3`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `sorted` (`dateupdated`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `national_agebreakdown`
--
ALTER TABLE `national_agebreakdown`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `sorted` (`dateupdated`),
  ADD KEY `sixweekspos` (`sixweekspos`),
  ADD KEY `sixweeksneg` (`sixweeksneg`),
  ADD KEY `sevento3mpos` (`sevento3mpos`),
  ADD KEY `sevento3mneg` (`sevento3mneg`),
  ADD KEY `threemto9mpos` (`threemto9mpos`),
  ADD KEY `threemto9mneg` (`threemto9mneg`),
  ADD KEY `ninemto18mpos` (`ninemto18mpos`),
  ADD KEY `ninemto18mneg` (`ninemto18mneg`),
  ADD KEY `nodatapos` (`nodatapos`),
  ADD KEY `nodataneg` (`nodataneg`),
  ADD KEY `less2wpos` (`less2wpos`),
  ADD KEY `less2wneg` (`less2wneg`),
  ADD KEY `twoto6wpos` (`twoto6wpos`),
  ADD KEY `twoto6wneg` (`twoto6wneg`),
  ADD KEY `sixto8wpos` (`sixto8wpos`),
  ADD KEY `sixto8wneg` (`sixto8wneg`),
  ADD KEY `sixmonthpos` (`sixmonthpos`),
  ADD KEY `sixmonthneg` (`sixmonthneg`),
  ADD KEY `ninemonthpos` (`ninemonthpos`),
  ADD KEY `ninemonthneg` (`ninemonthneg`),
  ADD KEY `twelvemonthpos` (`twelvemonthpos`),
  ADD KEY `twelvemonthneg` (`twelvemonthneg`);

--
-- Index pour la table `national_age_breakdown`
--
ALTER TABLE `national_age_breakdown`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `national_age_breakdown_year_index` (`year`),
  ADD KEY `national_age_breakdown_month_index` (`month`),
  ADD KEY `national_age_breakdown_age_band_id_index` (`age_band_id`);

--
-- Index pour la table `national_entrypoint`
--
ALTER TABLE `national_entrypoint`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`entrypoint`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `sorted` (`dateupdated`);

--
-- Index pour la table `national_iprophylaxis`
--
ALTER TABLE `national_iprophylaxis`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`prophylaxis`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `sorted` (`dateupdated`);

--
-- Index pour la table `national_mprophylaxis`
--
ALTER TABLE `national_mprophylaxis`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`prophylaxis`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `sorted` (`dateupdated`);

--
-- Index pour la table `national_rejections`
--
ALTER TABLE `national_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `national_summary`
--
ALTER TABLE `national_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`avgage`),
  ADD KEY `medage` (`medage`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`firstdna`),
  ADD KEY `confirmdna` (`confirmdna`),
  ADD KEY `infantsless2m` (`infantsless2m`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`enrolled`),
  ADD KEY `dead` (`dead`),
  ADD KEY `lftu` (`ltfu`),
  ADD KEY `adult` (`adult`),
  ADD KEY `transout` (`transout`),
  ADD KEY `other` (`other`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `sorted` (`dateupdated`),
  ADD KEY `actualinfants` (`actualinfants`),
  ADD KEY `infantsless2mPOS` (`infantsless2mPOS`),
  ADD KEY `actualinfantsPOS` (`actualinfantsPOS`),
  ADD KEY `repeatspos` (`repeatspos`),
  ADD KEY `adults` (`adults`),
  ADD KEY `adultsPOS` (`adultsPOS`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `infantsless2mREJ` (`infantsless2mREJ`),
  ADD KEY `infantsless2w` (`infantsless2w`),
  ADD KEY `infantsless2wPOS` (`infantsless2wPOS`),
  ADD KEY `infants4to6w` (`infants4to6w`),
  ADD KEY `infants4to6wPOS` (`infants4to6wPOS`),
  ADD KEY `infantsabove2m` (`infantsabove2m`),
  ADD KEY `infantsabove2mPOS` (`infantsabove2mPOS`),
  ADD KEY `validation_confirmedpos` (`validation_confirmedpos`),
  ADD KEY `validation_repeattest` (`validation_repeattest`),
  ADD KEY `validation_viralload` (`validation_viralload`),
  ADD KEY `validation_adult` (`validation_adult`),
  ADD KEY `validation_unknownsite` (`validation_unknownsite`);

--
-- Index pour la table `national_summary_yearly`
--
ALTER TABLE `national_summary_yearly`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`avgage`),
  ADD KEY `medage` (`medage`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`firstdna`),
  ADD KEY `confirmdna` (`confirmdna`),
  ADD KEY `infantsless2m` (`infantsless2m`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`enrolled`),
  ADD KEY `dead` (`dead`),
  ADD KEY `lftu` (`ltfu`),
  ADD KEY `adult` (`adult`),
  ADD KEY `transout` (`transout`),
  ADD KEY `other` (`other`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `sorted` (`dateupdated`),
  ADD KEY `actualinfants` (`actualinfants`),
  ADD KEY `infantsless2mPOS` (`infantsless2mPOS`),
  ADD KEY `actualinfantsPOS` (`actualinfantsPOS`),
  ADD KEY `repeatspos` (`repeatspos`),
  ADD KEY `adults` (`adults`),
  ADD KEY `adultsPOS` (`adultsPOS`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `infantsless2mREJ` (`infantsless2mREJ`),
  ADD KEY `infantsless2w` (`infantsless2w`),
  ADD KEY `infantsless2wPOS` (`infantsless2wPOS`),
  ADD KEY `infants4to6w` (`infants4to6w`),
  ADD KEY `infants4to6wPOS` (`infants4to6wPOS`),
  ADD KEY `infantsabove2m` (`infantsabove2m`),
  ADD KEY `infantsabove2mPOS` (`infantsabove2mPOS`),
  ADD KEY `validation_confirmedpos` (`validation_confirmedpos`),
  ADD KEY `validation_repeattest` (`validation_repeattest`),
  ADD KEY `validation_viralload` (`validation_viralload`),
  ADD KEY `validation_adult` (`validation_adult`),
  ADD KEY `validation_unknownsite` (`validation_unknownsite`);

--
-- Index pour la table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`),
  ADD KEY `password_resets_token_index` (`token`);

--
-- Index pour la table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FacilityMFLcode` (`FacilityMFLcode`),
  ADD KEY `FacilityDHISCode` (`FacilityDHISCode`),
  ADD KEY `patientID` (`patientID`),
  ADD KEY `PatientName` (`PatientName`),
  ADD KEY `Age` (`Age`),
  ADD KEY `Gender` (`Gender`),
  ADD KEY `PatientPhoneNo` (`PatientPhoneNo`),
  ADD KEY `SampleType` (`SampleType`),
  ADD KEY `Justification` (`Justification`),
  ADD KEY `Regimen` (`Regimen`),
  ADD KEY `datecollected` (`datecollected`),
  ADD KEY `datereceived` (`datereceived`),
  ADD KEY `datetested` (`datetested`),
  ADD KEY `result` (`result`),
  ADD KEY `datedispatched` (`datedispatched`),
  ADD KEY `labtestedin` (`labtestedin`),
  ADD KEY `labid` (`labid`);

--
-- Index pour la table `patients_eid`
--
ALTER TABLE `patients_eid`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FacilityMFLcode` (`FacilityMFLcode`),
  ADD KEY `FacilityDHISCode` (`FacilityDHISCode`),
  ADD KEY `patientID` (`patientID`),
  ADD KEY `PatientName` (`PatientName`),
  ADD KEY `Age` (`Age`),
  ADD KEY `Gender` (`Gender`),
  ADD KEY `PatientPhoneNo` (`PatientPhoneNo`),
  ADD KEY `SampleType` (`motherregimen`),
  ADD KEY `datecollected` (`datecollected`),
  ADD KEY `datereceived` (`datereceived`),
  ADD KEY `datetested` (`datetested`),
  ADD KEY `result` (`result`),
  ADD KEY `datedispatched` (`datedispatched`),
  ADD KEY `labtestedin` (`labtestedin`),
  ADD KEY `receivedstatus` (`receivedstatus`),
  ADD KEY `labid` (`labid`),
  ADD KEY `batchno` (`batchno`);

--
-- Index pour la table `pcrtype`
--
ALTER TABLE `pcrtype`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `platforms`
--
ALTER TABLE `platforms`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `prophylaxis`
--
ALTER TABLE `prophylaxis`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- Index pour la table `prophylaxistypes`
--
ALTER TABLE `prophylaxistypes`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`,`name`);

--
-- Index pour la table `receivedstatus`
--
ALTER TABLE `receivedstatus`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`,`Name`);

--
-- Index pour la table `rejectedreasons`
--
ALTER TABLE `rejectedreasons`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`),
  ADD KEY `ID_2` (`ID`,`Name`);

--
-- Index pour la table `rht_samples`
--
ALTER TABLE `rht_samples`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `age` (`age`),
  ADD KEY `gender` (`gender`),
  ADD KEY `facility` (`facility`),
  ADD KEY `fcode` (`fcode`),
  ADD KEY `datecollected` (`datecollected`),
  ADD KEY `datereceived` (`datereceived`),
  ADD KEY `datedispatched` (`datedispatched`),
  ADD KEY `labtestedin` (`labtestedin`),
  ADD KEY `dateuploaded` (`dateuploaded`),
  ADD KEY `datetested` (`datetested`);

--
-- Index pour la table `shortcodequeries`
--
ALTER TABLE `shortcodequeries`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `phoneno` (`phoneno`),
  ADD KEY `message` (`message`),
  ADD KEY `sorted` (`status`),
  ADD KEY `datesorted` (`dateresponded`),
  ADD KEY `datereceived` (`datereceived`),
  ADD KEY `mflcode` (`mflcode`),
  ADD KEY `samplecode` (`samplecode`),
  ADD KEY `status` (`status`),
  ADD KEY `dateresponded` (`dateresponded`);

--
-- Index pour la table `sites`
--
ALTER TABLE `sites`
  ADD KEY `partner` (`partner`),
  ADD KEY `county` (`county`),
  ADD KEY `fid` (`fid`),
  ADD KEY `new` (`synched`);

--
-- Index pour la table `site_rejections`
--
ALTER TABLE `site_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `site_summary`
--
ALTER TABLE `site_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`facility`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`avgage`),
  ADD KEY `medage` (`medage`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`firstdna`),
  ADD KEY `confirmdna` (`confirmdna`),
  ADD KEY `infantsless2m` (`infantsless2m`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`enrolled`),
  ADD KEY `dead` (`dead`),
  ADD KEY `lftu` (`ltfu`),
  ADD KEY `adult` (`adult`),
  ADD KEY `transout` (`transout`),
  ADD KEY `other` (`other`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `infantsless2m_infantsless2mPOS` (`infantsless2m`,`infantsless2mPOS`),
  ADD KEY `facility` (`facility`),
  ADD KEY `actualinfants` (`actualinfants`),
  ADD KEY `actualinfantsPOS` (`actualinfantsPOS`),
  ADD KEY `repeatspos` (`repeatspos`),
  ADD KEY `adultsPOS` (`adultsPOS`),
  ADD KEY `adults` (`adults`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `validation_confirmedpos` (`validation_confirmedpos`),
  ADD KEY `validation_repeattest` (`validation_repeattest`),
  ADD KEY `validation_viralload` (`validation_viralload`),
  ADD KEY `validation_adult` (`validation_adult`),
  ADD KEY `validation_unknownsite` (`validation_unknownsite`),
  ADD KEY `infantsless2mNEG` (`infantsless2mNEG`),
  ADD KEY `infantsless2mREJ` (`infantsless2mREJ`),
  ADD KEY `infantsless2w` (`infantsless2w`),
  ADD KEY `infantsless2wPOS` (`infantsless2wPOS`),
  ADD KEY `infants4to6w` (`infants4to6w`),
  ADD KEY `infants4to6wPOS` (`infants4to6wPOS`),
  ADD KEY `infantsabove2m` (`infantsabove2m`),
  ADD KEY `infantsabove2mPOS` (`infantsabove2mPOS`),
  ADD KEY `dateupdated` (`dateupdated`);

--
-- Index pour la table `site_summary_yearly`
--
ALTER TABLE `site_summary_yearly`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`facility`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`avgage`),
  ADD KEY `medage` (`medage`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`firstdna`),
  ADD KEY `confirmdna` (`confirmdna`),
  ADD KEY `infantsless2m` (`infantsless2m`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`enrolled`),
  ADD KEY `dead` (`dead`),
  ADD KEY `lftu` (`ltfu`),
  ADD KEY `adult` (`adult`),
  ADD KEY `transout` (`transout`),
  ADD KEY `other` (`other`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `infantsless2m_infantsless2mPOS` (`infantsless2m`,`infantsless2mPOS`),
  ADD KEY `facility` (`facility`),
  ADD KEY `actualinfants` (`actualinfants`),
  ADD KEY `actualinfantsPOS` (`actualinfantsPOS`),
  ADD KEY `repeatspos` (`repeatspos`),
  ADD KEY `adultsPOS` (`adultsPOS`),
  ADD KEY `adults` (`adults`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `validation_confirmedpos` (`validation_confirmedpos`),
  ADD KEY `validation_repeattest` (`validation_repeattest`),
  ADD KEY `validation_viralload` (`validation_viralload`),
  ADD KEY `validation_adult` (`validation_adult`),
  ADD KEY `validation_unknownsite` (`validation_unknownsite`),
  ADD KEY `infantsless2mNEG` (`infantsless2mNEG`),
  ADD KEY `infantsless2mREJ` (`infantsless2mREJ`),
  ADD KEY `infantsless2w` (`infantsless2w`),
  ADD KEY `infantsless2wPOS` (`infantsless2wPOS`),
  ADD KEY `infants4to6w` (`infants4to6w`),
  ADD KEY `infants4to6wPOS` (`infants4to6wPOS`),
  ADD KEY `infantsabove2m` (`infantsabove2m`),
  ADD KEY `infantsabove2mPOS` (`infantsabove2mPOS`),
  ADD KEY `dateupdated` (`dateupdated`);

--
-- Index pour la table `subcounty_agebreakdown`
--
ALTER TABLE `subcounty_agebreakdown`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `sorted` (`dateupdated`),
  ADD KEY `sixweekspos` (`sixweekspos`),
  ADD KEY `sixweeksneg` (`sixweeksneg`),
  ADD KEY `sevento3mpos` (`sevento3mpos`),
  ADD KEY `sevento3mneg` (`sevento3mneg`),
  ADD KEY `threemto9mpos` (`threemto9mpos`),
  ADD KEY `threemto9mneg` (`threemto9mneg`),
  ADD KEY `ninemto18mpos` (`ninemto18mpos`),
  ADD KEY `ninemto18mneg` (`ninemto18mneg`),
  ADD KEY `nodatapos` (`nodatapos`),
  ADD KEY `nodataneg` (`nodataneg`),
  ADD KEY `less2wpos` (`less2wpos`),
  ADD KEY `less2wneg` (`less2wneg`),
  ADD KEY `twoto6wpos` (`twoto6wpos`),
  ADD KEY `twoto6wneg` (`twoto6wneg`),
  ADD KEY `sixto8wpos` (`sixto8wpos`),
  ADD KEY `sixto8wneg` (`sixto8wneg`),
  ADD KEY `sixmonthpos` (`sixmonthpos`),
  ADD KEY `sixmonthneg` (`sixmonthneg`),
  ADD KEY `ninemonthpos` (`ninemonthpos`),
  ADD KEY `ninemonthneg` (`ninemonthneg`),
  ADD KEY `twelvemonthpos` (`twelvemonthpos`),
  ADD KEY `twelvemonthneg` (`twelvemonthneg`);

--
-- Index pour la table `subcounty_age_breakdown`
--
ALTER TABLE `subcounty_age_breakdown`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `subcounty_age_breakdown_year_index` (`year`),
  ADD KEY `subcounty_age_breakdown_month_index` (`month`),
  ADD KEY `subcounty_age_breakdown_subcounty_index` (`subcounty`),
  ADD KEY `subcounty_age_breakdown_age_band_id_index` (`age_band_id`);

--
-- Index pour la table `subcounty_entrypoint`
--
ALTER TABLE `subcounty_entrypoint`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`entrypoint`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `sorted` (`dateupdated`);

--
-- Index pour la table `subcounty_iprophylaxis`
--
ALTER TABLE `subcounty_iprophylaxis`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`prophylaxis`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `sorted` (`dateupdated`);

--
-- Index pour la table `subcounty_mprophylaxis`
--
ALTER TABLE `subcounty_mprophylaxis`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`prophylaxis`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `sorted` (`dateupdated`);

--
-- Index pour la table `subcounty_rejections`
--
ALTER TABLE `subcounty_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `subcounty_summary`
--
ALTER TABLE `subcounty_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`avgage`),
  ADD KEY `medage` (`medage`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`firstdna`),
  ADD KEY `confirmdna` (`confirmdna`),
  ADD KEY `infantsless2m` (`infantsless2m`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`enrolled`),
  ADD KEY `dead` (`dead`),
  ADD KEY `lftu` (`ltfu`),
  ADD KEY `adult` (`adult`),
  ADD KEY `transout` (`transout`),
  ADD KEY `other` (`other`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `sorted` (`dateupdated`),
  ADD KEY `infantsless2m_infantsless2mPOS` (`infantsless2m`,`infantsless2mPOS`),
  ADD KEY `actualinfants` (`actualinfants`),
  ADD KEY `actualinfantsPOS` (`actualinfantsPOS`),
  ADD KEY `repeatspos` (`repeatspos`),
  ADD KEY `adultsPOS` (`adultsPOS`),
  ADD KEY `adults` (`adults`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `subcounty` (`subcounty`),
  ADD KEY `validation_confirmedpos` (`validation_confirmedpos`),
  ADD KEY `validation_repeattest` (`validation_repeattest`),
  ADD KEY `validation_viralload` (`validation_viralload`),
  ADD KEY `validation_adult` (`validation_adult`),
  ADD KEY `validation_unknownsite` (`validation_unknownsite`),
  ADD KEY `infantsless2mNEG` (`infantsless2mNEG`),
  ADD KEY `infantsless2mREJ` (`infantsless2mREJ`),
  ADD KEY `infantsless2w` (`infantsless2w`),
  ADD KEY `infantsless2wPOS` (`infantsless2wPOS`),
  ADD KEY `infants4to6w` (`infants4to6w`),
  ADD KEY `infants4to6wPOS` (`infants4to6wPOS`),
  ADD KEY `infantsabove2m` (`infantsabove2m`),
  ADD KEY `infantsabove2mPOS` (`infantsabove2mPOS`);

--
-- Index pour la table `subcounty_summary_yearly`
--
ALTER TABLE `subcounty_summary_yearly`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`avgage`),
  ADD KEY `medage` (`medage`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`eqatests`),
  ADD KEY `tests` (`tests`),
  ADD KEY `firstdna` (`firstdna`),
  ADD KEY `confirmdna` (`confirmdna`),
  ADD KEY `infantsless2m` (`infantsless2m`),
  ADD KEY `pos` (`pos`),
  ADD KEY `neg` (`neg`),
  ADD KEY `redraw` (`redraw`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`enrolled`),
  ADD KEY `dead` (`dead`),
  ADD KEY `lftu` (`ltfu`),
  ADD KEY `adult` (`adult`),
  ADD KEY `transout` (`transout`),
  ADD KEY `other` (`other`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `sorted` (`dateupdated`),
  ADD KEY `infantsless2m_infantsless2mPOS` (`infantsless2m`,`infantsless2mPOS`),
  ADD KEY `actualinfants` (`actualinfants`),
  ADD KEY `actualinfantsPOS` (`actualinfantsPOS`),
  ADD KEY `repeatspos` (`repeatspos`),
  ADD KEY `adultsPOS` (`adultsPOS`),
  ADD KEY `adults` (`adults`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `subcounty` (`subcounty`),
  ADD KEY `validation_confirmedpos` (`validation_confirmedpos`),
  ADD KEY `validation_repeattest` (`validation_repeattest`),
  ADD KEY `validation_viralload` (`validation_viralload`),
  ADD KEY `validation_adult` (`validation_adult`),
  ADD KEY `validation_unknownsite` (`validation_unknownsite`),
  ADD KEY `infantsless2mNEG` (`infantsless2mNEG`),
  ADD KEY `infantsless2mREJ` (`infantsless2mREJ`),
  ADD KEY `infantsless2w` (`infantsless2w`),
  ADD KEY `infantsless2wPOS` (`infantsless2wPOS`),
  ADD KEY `infants4to6w` (`infants4to6w`),
  ADD KEY `infants4to6wPOS` (`infants4to6wPOS`),
  ADD KEY `infantsabove2m` (`infantsabove2m`),
  ADD KEY `infantsabove2mPOS` (`infantsabove2mPOS`);

--
-- Index pour la table `survey`
--
ALTER TABLE `survey`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `surveyors`
--
ALTER TABLE `surveyors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_surveyor` (`username`);

--
-- Index pour la table `survey_details`
--
ALTER TABLE `survey_details`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `testtype`
--
ALTER TABLE `testtype`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_users_userKey` (`userKey`);

--
-- Index pour la table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `viraljustifications`
--
ALTER TABLE `viraljustifications`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `viralpmtcttype`
--
ALTER TABLE `viralpmtcttype`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `viralprophylaxis`
--
ALTER TABLE `viralprophylaxis`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `viralsampletype`
--
ALTER TABLE `viralsampletype`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `viralsampletypedetails`
--
ALTER TABLE `viralsampletypedetails`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `vl_county_age`
--
ALTER TABLE `vl_county_age`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `<1000` (`less1000`),
  ADD KEY `<5000` (`less5000`),
  ADD KEY `>5000` (`above5000`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`county`),
  ADD KEY `sorted` (`sorted`);

--
-- Index pour la table `vl_county_gender`
--
ALTER TABLE `vl_county_gender`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `regimen` (`gender`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `<1000` (`less1000`),
  ADD KEY `<5000` (`less5000`),
  ADD KEY `>5000` (`above5000`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `0to4.9` (`less5`),
  ADD KEY `5to9.9` (`less10`),
  ADD KEY `10to14.9` (`less15`),
  ADD KEY `15to17.9` (`less18`),
  ADD KEY `18+` (`over18`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`county`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_county_justification`
--
ALTER TABLE `vl_county_justification`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `<1000` (`less1000`),
  ADD KEY `<5000` (`less5000`),
  ADD KEY `>5000` (`above5000`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `0to4.9` (`less5`),
  ADD KEY `5to9.9` (`less10`),
  ADD KEY `10to14.9` (`less15`),
  ADD KEY `15to17.9` (`less18`),
  ADD KEY `18+` (`over18`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`county`),
  ADD KEY `justification` (`justification`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_county_pmtct`
--
ALTER TABLE `vl_county_pmtct`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `vl_county_pmtct_year_index` (`year`),
  ADD KEY `vl_county_pmtct_month_index` (`month`),
  ADD KEY `vl_county_pmtct_county_index` (`county`),
  ADD KEY `vl_county_pmtct_pmtcttype_index` (`pmtcttype`);

--
-- Index pour la table `vl_county_regimen`
--
ALTER TABLE `vl_county_regimen`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `regimen` (`regimen`),
  ADD KEY `sustxfail` (`sustxfail`),
  ADD KEY `confirmtx` (`confirmtx`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `<1000` (`less1000`),
  ADD KEY `<5000` (`less5000`),
  ADD KEY `>5000` (`above5000`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `0to4.9` (`less5`),
  ADD KEY `5to9.9` (`less10`),
  ADD KEY `10to14.9` (`less15`),
  ADD KEY `15to17.9` (`less18`),
  ADD KEY `18+` (`over18`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`county`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_county_rejections`
--
ALTER TABLE `vl_county_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `vl_county_sampletype`
--
ALTER TABLE `vl_county_sampletype`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `sustxfail` (`sustxfail`),
  ADD KEY `repeattests` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `<1000` (`less1000`),
  ADD KEY `<5000` (`less5000`),
  ADD KEY `>5000` (`above5000`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `0to4.9` (`less5`),
  ADD KEY `5to9.9` (`less10`),
  ADD KEY `10to14.9` (`less15`),
  ADD KEY `15to17.9` (`less18`),
  ADD KEY `18+` (`over18`),
  ADD KEY `dbs` (`maletest`),
  ADD KEY `plasma` (`femaletest`),
  ADD KEY `county` (`county`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_county_summary`
--
ALTER TABLE `vl_county_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`sustxfail`),
  ADD KEY `medage` (`confirmtx`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`repeattests`),
  ADD KEY `tests` (`maletest`),
  ADD KEY `confirmdna` (`adults`),
  ADD KEY `infantsless2m` (`paeds`),
  ADD KEY `redraw` (`less1000`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`less5000`),
  ADD KEY `dead` (`above5000`),
  ADD KEY `lftu` (`invalids`),
  ADD KEY `adult` (`less5`),
  ADD KEY `transout` (`less10`),
  ADD KEY `other` (`less15`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`county`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `baseline` (`baseline`),
  ADD KEY `baselinesustxfail` (`baselinesustxfail`);

--
-- Index pour la table `vl_lab_platform`
--
ALTER TABLE `vl_lab_platform`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`lab`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `<1000` (`less1000`),
  ADD KEY `<5000` (`less5000`),
  ADD KEY `>5000` (`above5000`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`maletest`),
  ADD KEY `plasma` (`femaletest`),
  ADD KEY `county` (`platform`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_lab_rejections`
--
ALTER TABLE `vl_lab_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `vl_lab_sampletype`
--
ALTER TABLE `vl_lab_sampletype`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`sampletype`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `<1000` (`less1000`),
  ADD KEY `<5000` (`less5000`),
  ADD KEY `>5000` (`above5000`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`maletest`),
  ADD KEY `plasma` (`femaletest`),
  ADD KEY `county` (`lab`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_lab_summary`
--
ALTER TABLE `vl_lab_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`sustxfail`),
  ADD KEY `medage` (`confirmtx`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`repeattests`),
  ADD KEY `tests` (`maletest`),
  ADD KEY `firstdna` (`femaletest`),
  ADD KEY `confirmdna` (`adults`),
  ADD KEY `infantsless2m` (`paeds`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`less5000`),
  ADD KEY `dead` (`above5000`),
  ADD KEY `lftu` (`invalids`),
  ADD KEY `adult` (`less5`),
  ADD KEY `transout` (`less10`),
  ADD KEY `other` (`less15`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`lab`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `batches` (`batches`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `lesss14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_national_age`
--
ALTER TABLE `vl_national_age`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`age`),
  ADD KEY `tests` (`tests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `less1000` (`less1000`),
  ADD KEY `less5000` (`less5000`),
  ADD KEY `above5000` (`above5000`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`);

--
-- Index pour la table `vl_national_gender`
--
ALTER TABLE `vl_national_gender`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`gender`),
  ADD KEY `tests` (`tests`),
  ADD KEY `repeattests` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_national_justification`
--
ALTER TABLE `vl_national_justification`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`justification`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `over25` (`over25`),
  ADD KEY `less24` (`less24`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less2` (`less2`);

--
-- Index pour la table `vl_national_pmtct`
--
ALTER TABLE `vl_national_pmtct`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `vl_national_pmtct_year_index` (`year`),
  ADD KEY `vl_national_pmtct_month_index` (`month`),
  ADD KEY `vl_national_pmtct_pmtcttype_index` (`pmtcttype`);

--
-- Index pour la table `vl_national_regimen`
--
ALTER TABLE `vl_national_regimen`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `regimen` (`regimen`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_national_rejections`
--
ALTER TABLE `vl_national_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `vl_national_sampletype`
--
ALTER TABLE `vl_national_sampletype`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`maletest`),
  ADD KEY `plasma` (`femaletest`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_national_summary`
--
ALTER TABLE `vl_national_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`sustxfail`),
  ADD KEY `medage` (`confirmtx`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`repeattests`),
  ADD KEY `tests` (`maletest`),
  ADD KEY `firstdna` (`femaletest`),
  ADD KEY `confirmdna` (`adults`),
  ADD KEY `infantsless2m` (`paeds`),
  ADD KEY `pos` (`noage`),
  ADD KEY `neg` (`Undetected`),
  ADD KEY `redraw` (`less1000`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`less5000`),
  ADD KEY `dead` (`above5000`),
  ADD KEY `lftu` (`invalids`),
  ADD KEY `adult` (`less5`),
  ADD KEY `transout` (`less10`),
  ADD KEY `other` (`less15`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `18above` (`over18`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`),
  ADD KEY `nogendertest` (`nogendertest`),
  ADD KEY `actualpatients` (`actualpatients`),
  ADD KEY `baseline` (`baseline`),
  ADD KEY `baselinesustxfail` (`baselinesustxfail`),
  ADD KEY `dateupdated` (`dateupdated`);

--
-- Index pour la table `vl_partner_age`
--
ALTER TABLE `vl_partner_age`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`partner`);

--
-- Index pour la table `vl_partner_gender`
--
ALTER TABLE `vl_partner_gender`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`partner`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_partner_justification`
--
ALTER TABLE `vl_partner_justification`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`partner`),
  ADD KEY `justification` (`justification`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_partner_pmtct`
--
ALTER TABLE `vl_partner_pmtct`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `vl_partner_pmtct_year_index` (`year`),
  ADD KEY `vl_partner_pmtct_month_index` (`month`),
  ADD KEY `vl_partner_pmtct_partner_index` (`partner`),
  ADD KEY `vl_partner_pmtct_pmtcttype_index` (`pmtcttype`);

--
-- Index pour la table `vl_partner_regimen`
--
ALTER TABLE `vl_partner_regimen`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `regimen` (`regimen`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`partner`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_partner_rejections`
--
ALTER TABLE `vl_partner_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `vl_partner_sampletype`
--
ALTER TABLE `vl_partner_sampletype`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`maletest`),
  ADD KEY `plasma` (`femaletest`),
  ADD KEY `county` (`partner`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_partner_summary`
--
ALTER TABLE `vl_partner_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`sustxfail`),
  ADD KEY `medage` (`confirmtx`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`repeattests`),
  ADD KEY `tests` (`maletest`),
  ADD KEY `firstdna` (`femaletest`),
  ADD KEY `confirmdna` (`adults`),
  ADD KEY `infantsless2m` (`paeds`),
  ADD KEY `pos` (`noage`),
  ADD KEY `neg` (`Undetected`),
  ADD KEY `redraw` (`less1000`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`less5000`),
  ADD KEY `dead` (`above5000`),
  ADD KEY `lftu` (`invalids`),
  ADD KEY `adult` (`less5`),
  ADD KEY `transout` (`less10`),
  ADD KEY `other` (`less15`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `18+` (`over18`),
  ADD KEY `received` (`received`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`partner`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `baseline` (`baseline`),
  ADD KEY `baselinesustxfail` (`baselinesustxfail`);

--
-- Index pour la table `vl_site_age`
--
ALTER TABLE `vl_site_age`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `comp_idx_1` (`month`,`year`,`facility`),
  ADD KEY `dateupdated` (`dateupdated`);

--
-- Index pour la table `vl_site_gender`
--
ALTER TABLE `vl_site_gender`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `over25` (`over25`),
  ADD KEY `less24` (`less24`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less2` (`less2`),
  ADD KEY `facility` (`facility`);

--
-- Index pour la table `vl_site_justification`
--
ALTER TABLE `vl_site_justification`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `justification` (`justification`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `over25` (`over25`),
  ADD KEY `less24` (`less24`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less2` (`less2`),
  ADD KEY `facility` (`facility`);

--
-- Index pour la table `vl_site_patient_tracking`
--
ALTER TABLE `vl_site_patient_tracking`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`onevl`),
  ADD KEY `medage` (`twovl`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`above3vl`),
  ADD KEY `county` (`facility`),
  ADD KEY `confirm2vl` (`threevl`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `onevl` (`onevl`),
  ADD KEY `twovl` (`twovl`),
  ADD KEY `threevl` (`threevl`),
  ADD KEY `above3vl` (`above3vl`);

--
-- Index pour la table `vl_site_pmtct`
--
ALTER TABLE `vl_site_pmtct`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `vl_site_pmtct_year_index` (`year`),
  ADD KEY `vl_site_pmtct_month_index` (`month`),
  ADD KEY `vl_site_pmtct_facility_index` (`facility`),
  ADD KEY `vl_site_pmtct_pmtcttype_index` (`pmtcttype`);

--
-- Index pour la table `vl_site_regimen`
--
ALTER TABLE `vl_site_regimen`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `over25` (`over25`),
  ADD KEY `less24` (`less24`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less2` (`less2`),
  ADD KEY `facility` (`facility`);

--
-- Index pour la table `vl_site_rejections`
--
ALTER TABLE `vl_site_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `vl_site_sampletype`
--
ALTER TABLE `vl_site_sampletype`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirm2vl`),
  ADD KEY `over25` (`over25`),
  ADD KEY `less24` (`less24`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less2` (`less2`),
  ADD KEY `facility` (`facility`);

--
-- Index pour la table `vl_site_summary`
--
ALTER TABLE `vl_site_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`sustxfail`),
  ADD KEY `medage` (`confirm2vl`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`repeattests`),
  ADD KEY `tests` (`maletest`),
  ADD KEY `firstdna` (`femaletest`),
  ADD KEY `confirmdna` (`adults`),
  ADD KEY `infantsless2m` (`paeds`),
  ADD KEY `pos` (`noage`),
  ADD KEY `neg` (`Undetected`),
  ADD KEY `redraw` (`less1000`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`less5000`),
  ADD KEY `dead` (`above5000`),
  ADD KEY `lftu` (`invalids`),
  ADD KEY `adult` (`less5`),
  ADD KEY `transout` (`less10`),
  ADD KEY `other` (`less15`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`facility`),
  ADD KEY `confirm2vl` (`confirmtx`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `dateupdated` (`dateupdated`),
  ADD KEY `baseline` (`baseline`),
  ADD KEY `baselinesustxfail` (`baselinesustxfail`);

--
-- Index pour la table `vl_site_suppression`
--
ALTER TABLE `vl_site_suppression`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `vl_site_suppression_year`
--
ALTER TABLE `vl_site_suppression_year`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `vl_subcounty_age`
--
ALTER TABLE `vl_subcounty_age`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `sorted` (`sorted`);

--
-- Index pour la table `vl_subcounty_gender`
--
ALTER TABLE `vl_subcounty_gender`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `prophylaxis` (`gender`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_subcounty_justification`
--
ALTER TABLE `vl_subcounty_justification`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `Undetected` (`Undetected`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_subcounty_pmtct`
--
ALTER TABLE `vl_subcounty_pmtct`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `vl_subcounty_pmtct_year_index` (`year`),
  ADD KEY `vl_subcounty_pmtct_month_index` (`month`),
  ADD KEY `vl_subcounty_pmtct_subcounty_index` (`subcounty`),
  ADD KEY `vl_subcounty_pmtct_pmtcttype_index` (`pmtcttype`);

--
-- Index pour la table `vl_subcounty_regimen`
--
ALTER TABLE `vl_subcounty_regimen`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `pos` (`sustxfail`),
  ADD KEY `neg` (`confirmtx`),
  ADD KEY `redraw` (`repeattests`),
  ADD KEY `regimen` (`regimen`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `maletest` (`maletest`),
  ADD KEY `femaletest` (`femaletest`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `invalids` (`invalids`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`);

--
-- Index pour la table `vl_subcounty_rejections`
--
ALTER TABLE `vl_subcounty_rejections`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `vl_subcounty_sampletype`
--
ALTER TABLE `vl_subcounty_sampletype`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `tests` (`tests`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `adults` (`adults`),
  ADD KEY `paeds` (`paeds`),
  ADD KEY `noage` (`noage`),
  ADD KEY `dbs` (`maletest`),
  ADD KEY `plasma` (`femaletest`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`),
  ADD KEY `Undetected` (`Undetected`);

--
-- Index pour la table `vl_subcounty_summary`
--
ALTER TABLE `vl_subcounty_summary`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `month` (`month`),
  ADD KEY `year` (`year`),
  ADD KEY `avgage` (`sustxfail`),
  ADD KEY `medage` (`confirm2vl`),
  ADD KEY `alltests` (`alltests`),
  ADD KEY `eqatests` (`repeattests`),
  ADD KEY `tests` (`maletest`),
  ADD KEY `firstdna` (`femaletest`),
  ADD KEY `confirmdna` (`adults`),
  ADD KEY `infantsless2m` (`paeds`),
  ADD KEY `pos` (`noage`),
  ADD KEY `neg` (`Undetected`),
  ADD KEY `redraw` (`less1000`),
  ADD KEY `rejected` (`rejected`),
  ADD KEY `enrolled` (`less5000`),
  ADD KEY `dead` (`above5000`),
  ADD KEY `lftu` (`invalids`),
  ADD KEY `adult` (`less5`),
  ADD KEY `transout` (`less10`),
  ADD KEY `other` (`less15`),
  ADD KEY `sitessending` (`sitessending`),
  ADD KEY `received` (`received`),
  ADD KEY `dbs` (`dbs`),
  ADD KEY `plasma` (`plasma`),
  ADD KEY `edta` (`edta`),
  ADD KEY `county` (`subcounty`),
  ADD KEY `sorted` (`sorted`),
  ADD KEY `confirm2vl` (`confirmtx`),
  ADD KEY `less2` (`less2`),
  ADD KEY `less9` (`less9`),
  ADD KEY `less14` (`less14`),
  ADD KEY `less19` (`less19`),
  ADD KEY `less24` (`less24`),
  ADD KEY `over25` (`over25`),
  ADD KEY `tat1` (`tat1`),
  ADD KEY `tat2` (`tat2`),
  ADD KEY `tat3` (`tat3`),
  ADD KEY `tat4` (`tat4`),
  ADD KEY `baseline` (`baseline`),
  ADD KEY `baselinesustxfail` (`baselinesustxfail`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `administrators`
--
ALTER TABLE `administrators`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `agecategory`
--
ALTER TABLE `agecategory`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `age_bands`
--
ALTER TABLE `age_bands`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `api_usage`
--
ALTER TABLE `api_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `county_agebreakdown`
--
ALTER TABLE `county_agebreakdown`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `county_age_breakdown`
--
ALTER TABLE `county_age_breakdown`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `county_entrypoint`
--
ALTER TABLE `county_entrypoint`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `county_iprophylaxis`
--
ALTER TABLE `county_iprophylaxis`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `county_mprophylaxis`
--
ALTER TABLE `county_mprophylaxis`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `county_rejections`
--
ALTER TABLE `county_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `county_summary`
--
ALTER TABLE `county_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `county_summary_yearly`
--
ALTER TABLE `county_summary_yearly`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `districts`
--
ALTER TABLE `districts`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;
--
-- AUTO_INCREMENT pour la table `entry_points`
--
ALTER TABLE `entry_points`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `facilitys`
--
ALTER TABLE `facilitys`
  MODIFY `ID` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1624;
--
-- AUTO_INCREMENT pour la table `feedings`
--
ALTER TABLE `feedings`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `gender`
--
ALTER TABLE `gender`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `hei_categories`
--
ALTER TABLE `hei_categories`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `hei_validation`
--
ALTER TABLE `hei_validation`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `ip_agebreakdown`
--
ALTER TABLE `ip_agebreakdown`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ip_age_breakdown`
--
ALTER TABLE `ip_age_breakdown`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ip_entrypoint`
--
ALTER TABLE `ip_entrypoint`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ip_iprophylaxis`
--
ALTER TABLE `ip_iprophylaxis`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ip_mprophylaxis`
--
ALTER TABLE `ip_mprophylaxis`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ip_rejections`
--
ALTER TABLE `ip_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ip_summary`
--
ALTER TABLE `ip_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ip_summary_yearly`
--
ALTER TABLE `ip_summary_yearly`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `lablogs`
--
ALTER TABLE `lablogs`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `labs`
--
ALTER TABLE `labs`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;
--
-- AUTO_INCREMENT pour la table `lab_rejections`
--
ALTER TABLE `lab_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `lab_summary`
--
ALTER TABLE `lab_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `national_agebreakdown`
--
ALTER TABLE `national_agebreakdown`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `national_age_breakdown`
--
ALTER TABLE `national_age_breakdown`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `national_entrypoint`
--
ALTER TABLE `national_entrypoint`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `national_iprophylaxis`
--
ALTER TABLE `national_iprophylaxis`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `national_mprophylaxis`
--
ALTER TABLE `national_mprophylaxis`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `national_rejections`
--
ALTER TABLE `national_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `national_summary`
--
ALTER TABLE `national_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `national_summary_yearly`
--
ALTER TABLE `national_summary_yearly`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `partners`
--
ALTER TABLE `partners`
  MODIFY `ID` int(32) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `patients`
--
ALTER TABLE `patients`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `patients_eid`
--
ALTER TABLE `patients_eid`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `platforms`
--
ALTER TABLE `platforms`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `prophylaxis`
--
ALTER TABLE `prophylaxis`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `prophylaxistypes`
--
ALTER TABLE `prophylaxistypes`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `receivedstatus`
--
ALTER TABLE `receivedstatus`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `rejectedreasons`
--
ALTER TABLE `rejectedreasons`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `results`
--
ALTER TABLE `results`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `rht_samples`
--
ALTER TABLE `rht_samples`
  MODIFY `ID` int(14) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `shortcodequeries`
--
ALTER TABLE `shortcodequeries`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `site_rejections`
--
ALTER TABLE `site_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `site_summary`
--
ALTER TABLE `site_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `site_summary_yearly`
--
ALTER TABLE `site_summary_yearly`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `subcounty_agebreakdown`
--
ALTER TABLE `subcounty_agebreakdown`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `subcounty_age_breakdown`
--
ALTER TABLE `subcounty_age_breakdown`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `subcounty_entrypoint`
--
ALTER TABLE `subcounty_entrypoint`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `subcounty_iprophylaxis`
--
ALTER TABLE `subcounty_iprophylaxis`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `subcounty_mprophylaxis`
--
ALTER TABLE `subcounty_mprophylaxis`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `subcounty_rejections`
--
ALTER TABLE `subcounty_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `subcounty_summary`
--
ALTER TABLE `subcounty_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `subcounty_summary_yearly`
--
ALTER TABLE `subcounty_summary_yearly`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `survey`
--
ALTER TABLE `survey`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `surveyors`
--
ALTER TABLE `surveyors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `survey_details`
--
ALTER TABLE `survey_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `testtype`
--
ALTER TABLE `testtype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `viraljustifications`
--
ALTER TABLE `viraljustifications`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pour la table `viralpmtcttype`
--
ALTER TABLE `viralpmtcttype`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `viralprophylaxis`
--
ALTER TABLE `viralprophylaxis`
  MODIFY `ID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT pour la table `viralsampletype`
--
ALTER TABLE `viralsampletype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `viralsampletypedetails`
--
ALTER TABLE `viralsampletypedetails`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_county_age`
--
ALTER TABLE `vl_county_age`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_county_gender`
--
ALTER TABLE `vl_county_gender`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_county_justification`
--
ALTER TABLE `vl_county_justification`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_county_pmtct`
--
ALTER TABLE `vl_county_pmtct`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_county_regimen`
--
ALTER TABLE `vl_county_regimen`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_county_rejections`
--
ALTER TABLE `vl_county_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_county_sampletype`
--
ALTER TABLE `vl_county_sampletype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_county_summary`
--
ALTER TABLE `vl_county_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_lab_platform`
--
ALTER TABLE `vl_lab_platform`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_lab_rejections`
--
ALTER TABLE `vl_lab_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_lab_sampletype`
--
ALTER TABLE `vl_lab_sampletype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_lab_summary`
--
ALTER TABLE `vl_lab_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_national_age`
--
ALTER TABLE `vl_national_age`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_national_gender`
--
ALTER TABLE `vl_national_gender`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_national_justification`
--
ALTER TABLE `vl_national_justification`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_national_pmtct`
--
ALTER TABLE `vl_national_pmtct`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_national_regimen`
--
ALTER TABLE `vl_national_regimen`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_national_rejections`
--
ALTER TABLE `vl_national_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_national_sampletype`
--
ALTER TABLE `vl_national_sampletype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_national_summary`
--
ALTER TABLE `vl_national_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_partner_age`
--
ALTER TABLE `vl_partner_age`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_partner_gender`
--
ALTER TABLE `vl_partner_gender`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_partner_justification`
--
ALTER TABLE `vl_partner_justification`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_partner_pmtct`
--
ALTER TABLE `vl_partner_pmtct`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_partner_regimen`
--
ALTER TABLE `vl_partner_regimen`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_partner_rejections`
--
ALTER TABLE `vl_partner_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_partner_sampletype`
--
ALTER TABLE `vl_partner_sampletype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_partner_summary`
--
ALTER TABLE `vl_partner_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_age`
--
ALTER TABLE `vl_site_age`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_gender`
--
ALTER TABLE `vl_site_gender`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_justification`
--
ALTER TABLE `vl_site_justification`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_patient_tracking`
--
ALTER TABLE `vl_site_patient_tracking`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_pmtct`
--
ALTER TABLE `vl_site_pmtct`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_regimen`
--
ALTER TABLE `vl_site_regimen`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_rejections`
--
ALTER TABLE `vl_site_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_sampletype`
--
ALTER TABLE `vl_site_sampletype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_summary`
--
ALTER TABLE `vl_site_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_suppression`
--
ALTER TABLE `vl_site_suppression`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_site_suppression_year`
--
ALTER TABLE `vl_site_suppression_year`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_subcounty_age`
--
ALTER TABLE `vl_subcounty_age`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_subcounty_gender`
--
ALTER TABLE `vl_subcounty_gender`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_subcounty_justification`
--
ALTER TABLE `vl_subcounty_justification`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_subcounty_pmtct`
--
ALTER TABLE `vl_subcounty_pmtct`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_subcounty_regimen`
--
ALTER TABLE `vl_subcounty_regimen`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_subcounty_rejections`
--
ALTER TABLE `vl_subcounty_rejections`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_subcounty_sampletype`
--
ALTER TABLE `vl_subcounty_sampletype`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `vl_subcounty_summary`
--
ALTER TABLE `vl_subcounty_summary`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `administrators`
--
ALTER TABLE `administrators`
  ADD CONSTRAINT `administrators_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

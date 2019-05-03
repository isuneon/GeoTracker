DELIMITER $$
 
CREATE PROCEDURE obtenerUsuarios(
 IN pEmail 	VARCHAR(255),
 IN pPassword	VARCHAR(255),
 OUT shipped    VARCHAR(255),
 OUT canceled   VARCHAR(255),
 OUT resolved   INT)
BEGIN

DECLARE resultado VARCHAR(255);
SET resultado = NULL;

 
 SELECT email  FROM users WHERE email = pEmail;
 IF ( (SELECT email  FROM users WHERE email = pEmail) IS NULL)  THEN
	SELECT 'Email inexistente' INTO shipped  ;
	SELECT canceled = 'Email inexistente';
	SELECT resolved = 0;
 END IF;
--	IF (NOT(lcEmail IS NULL) AND (NOT lcPassword IS NULL)) THEN

--		WHERE a.email = lcEmail AND a.password =lcPassword;
		
--	END IF; 

END$$

DELIMITER ;

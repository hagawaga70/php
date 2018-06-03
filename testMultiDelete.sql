WITH data (f_id_temp, l_id_temp) 
AS (
		VALUES (127, 321)
), 
ins1 AS (
	DELETE FROM begleitet 
	WHERE	f_id = f_id_temp AND l_id = l_id_temp
	SELECT l_id_temp, f_id_temp FROM data
	ON CONFLICT DO NOTHING
	Returning l_id
)
	DELETE FROM lehrer
	WHERE 		l_id = l_id_temp 
	AND			1 = (SELECT count(*) FROM begleitet WHERE l_id = l_id_temp)
	SELECT 	l_id, f_id
	FROM 	data
	ON CONFLICT DO NOTHING;

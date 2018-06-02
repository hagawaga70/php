WITH data (f_id, l_id,anrede,vname,nname,telnr) 
AS (
		VALUES (102, 1, 'Frau','Gabi', 'Moon',33499)
), 
ins1 AS (
	INSERT INTO lehrer (l_id,anrede,vname,nname,telnr)
	SELECT l_id,anrede,vname,nname,telnr FROM data
	ON CONFLICT DO NOTHING
	Returning l_id
)
	INSERT INTO begleitet (l_id,f_id)
	SELECT 	l_id, f_id
	FROM 	data
	JOIN ins1 USING (l_id);

SELECT 				ak.ak_id,ak.bewert,ak.bezng,ak.art,ak.katgrie,ak.fabezg,ak.ort,
					ak.vorstzg,ak.jahrz1,ak.jahrz2,ak.jahrz3,ak.jahrz4,ak.mialt,
					ak.dauer,ak.akpreis,an.an_name
FROM 				aktivitaet ak
LEFT OUTER JOIN 	anbieter an 
ON 					(ak.anbietr = an.an_id);

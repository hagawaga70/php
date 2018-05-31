SELECT              f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name, o.o_name
FROM                fahrt f 
LEFT OUTER JOIN     unterkunft u 
ON                  (f.f_unterkunft=u.u_id)
LEFT OUTER JOIN     ort o 
ON                  (u.u_ort = o.o_id)



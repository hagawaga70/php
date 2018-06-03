			if ($suchfenster == 1){   // Der Link "suche/Search" im Menue "Lehreren" wurde angeklickt


				// ---> Zunächste werden alle Datensätze angezeigt -----------------------------------------------

				$formSelect = pg_query($db,
											"	SELECT 				l.l_id, l.anrede, l.vname, l.nname,l.telnr
												FROM 				lehrer l 
												;
								");

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	


				$index=0;				// Zählt die Anzahl der Datensätze 

				// ---> Erstellen eines Arrays mit den l_id's . Die ID's werden für das Select-Menü benötigt

				while($row=pg_fetch_assoc($formSelect)){
					$lehrerID[$index] = $row[$attributeLehrer[0]]		;
					$index++										;
				}
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				$operator[0]=">"	;		// Array mit unterschiedlichen Operatoren für das Suchabfrage
				$operator[1]=">="	;		// |
				$operator[2]="="	;		// |
				$operator[3]="<="	;		// |
				$operator[4]="<"	;		// |


				$verknuepfung[0] ="AND";	// Array mit unterschiedlichen Verknüpfungen unterschiedlicher Suchanfrage (NICHT UMGESETZT)
				$verknuepfung[1] ="OR";		// |

				asort($lehrerID);	// Sortierung der ID's



				
				echo'	<div id="suche">'																;
				echo		'<form action="./lehrer.php?modus=3" method="post" autocomplete="off">'		;   // Such-Formular l_id
				echo			'<label>LehrerID:'														;

				
				// 09 ---> Auswahlmenü Operatoren --------------------------------------------------------------
				echo			'<select name="l_id_operator">'											;
				echo '				<option>---				</option>'									;
				foreach ($operator as $key => $val) {	
					if (array_key_exists('l_id_operator',$_POST) && $_POST['l_id_operator']== $val){
						echo '				<option selected >'.$val.'</option>'						;  	// Nach der Suche werden die Suchparameter
					}else{																					// automatisch voreingestellt
						echo '				<option >'.$val.'</option>'									;
					}
				}
				echo '			</select>'																;	
				// 09 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				// 10 ---> Auswahlmenü l_id's -------------------------------------------------------------------
				echo			'<select name="l_id">'													;
				echo '				<option>---</option>'												;

				foreach ($lehrerID as $key => $val) {	
					if (array_key_exists('l_id',$_POST) && $_POST['l_id']== $val){
						echo '				<option selected >'.$val.'</option>'						;	// Nach der Suche werden die Suchparameter
					}else{																					// automatisch voreingestellt
						echo '				<option>'.$val.'</option>'									;
					}
				}
				
				echo '			</select>'																;

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// ---> Manuelle Eingabe der gesuchten l_id --------------------------------------------------
				if (array_key_exists('l_id_input',$_POST) && $_POST['l_id_input'] != ''){
					echo '			<input id="l_id_input" name="l_id_input" maxlength="5" size="5" value='.$_POST['l_id_input'].'>'	; // Voreinstellung s.o	
				}else{
					echo '			<input id="l_id_input" name="l_id_input" maxlength="5" size="5" >'									;	
				}
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


	
				// ---> Auswahlmenü Verknüpfungsart -----------------------------------------------------------
				echo			'<select name="l_id_verknuepfung">'												;
				echo '				<option>---				</option>'											;
				foreach ($verknuepfung as $key => $val) {	
					if (array_key_exists('l_id_verknuepfung',$_POST) && $_POST['l_id_verknuepfung']== $val){
						echo '				<option selected >'.$val.'</option>'								;
					}else{
						echo '				<option>'.$val.'</option>'											;
					}
				}

				echo '			</select>'																		;
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
				echo '		</label>'																	;
				echo '		<button type="submit" name="action" value="5">Suche</button>'				;
				echo '	</form>'																		;
				echo '</div>'																			;		
			}


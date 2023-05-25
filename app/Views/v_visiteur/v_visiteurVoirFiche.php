<?= $this->extend('l_visiteur') ?>

<?= $this->section('body') ?>
<div id="contenu">
	<h2>Renseigner ma fiche de frais du mois <?= substr($mois,4,2)."-".substr($mois,0,4) ?></h2>
					
	<div class="corpsForm">
	  
		<fieldset>
			<legend>Eléments forfaitisés</legend>
			<?php
				$montantTotal = 0;
				$list = '';
				foreach ($fiche['lesFraisForfait'] as $unFrais)
				{

					$idFrais = $unFrais['idfrais']; // id produit
					$libelle = $unFrais['libelle']; // libelle produit
					$quantite = $unFrais['quantite']; // la quantite de chaque produit

					$montant = $unFrais['montant'];
					$montantUnitaire = $quantite * $montant; // montant total de chaque produit

					$montantTotal += $montantUnitaire; // calcul du montant total

					$list .= '"'. $idFrais . ',' . $quantite . ',' . $montant . '";'; // on récupère les informations nécessaire au javascript
					?>
					<script language="JavaScript">
						var nom = '<?php echo $list; ?>';
						console.log(nom);
					</script>
					<?php

					echo 
					'<p>
						<label for="'.$idFrais.'">'.$libelle.'</label>
						<input type="number" id="'.$idFrais.'" name="lesFrais['.$idFrais.']" onchange="calctotal(nom)" min="0" size="10" maxlength="5" value="'.$quantite.'" />
						
                                                <input type="text" name="lesFrais['.$idFrais.']"  size="10" maxlength="5" value="'.$montant.' €" disabled/>
						<input type="text" id="'.$idFrais.'Montant" name="lesFrais['.$idFrais.']" size="10" maxlength="5" value="'.$montantUnitaire.' €" disabled/>
                                                
					</p>';
				}
				echo 
					'<p>
						<label for="total"> Coût total : </label>
						
						<input type="text" id="total" size="10" maxlength="5" value="'.$montantTotal.' €" disabled/>
					</p>
					';
			?>
		</fieldset>
		
		<p></p>
	</div>
	
	<table class="listeLegere">
		<caption>Descriptif des éléments hors forfait</caption>
		<tr>
			<th >Date</th>
			<th >Libellé</th>  
			<th >Montant</th>  
		</tr>
          
		<?php    
			foreach($fiche['lesFraisHorsForfait'] as $unFraisHorsForfait) 
			{
				$date = $unFraisHorsForfait['date'];
				$libelle = $unFraisHorsForfait['libelle'];
				$montant=$unFraisHorsForfait['montant'];
				$id = $unFraisHorsForfait['id'];
				echo 
				'<tr>
					<td class="date">'.$date.'</td>
					<td class="libelle">'.$libelle.'</td>
					<td class="montant">'.$montant.'</td>
				</tr>';
			}
		?>	  
                                          
    </table>

</div>
<?= $this->endSection() ?>

<?= $this->extend('l_comptable') ?>

<?= $this->section('body') ?>
<div id="contenu">
    <h2>Renseigner ma fiche de frais du mois <?= substr($mois, 4, 2) . "-" . substr($mois, 0, 4) ?></h2>

    <?php if (!empty($notify)) echo '<p id="notify" >' . $notify . '</p>'; ?>

    <form method="post" action="<?= site_url("comptable/modLaFiche/" . $mois) ?>">
        <div class="corpsForm">
            <fieldset>
                <legend>Modifier l'état de la fiche de frais</legend>
                <p>
                    <label for="etatSelect">Nouvel État : </label>
                    <select name="etat" id="etatSelect">
                        <?php
                        foreach ($etat['lesEtats'] as $e) {
                            echo '<option value="' . $e['id'] . '">' . $e['libelle'] . '</option>';
                        }
                        ?>

                    </select>
                </p>
            </fieldset>
            <div class="piedForm">
                <p>
                    <input id="valider" type="submit" value="Valider" size="20" />
                    <input id="effacer" type="reset" value="Effacer" size="20" />
                </p> 
            </div>
    </form>

</div>
<?= $this->endSection() ?>
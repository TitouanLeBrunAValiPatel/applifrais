function calctotal(id) {
    const prixTotal = document.getElementById('total');
    prix = 0;
    
    monTableau = new Array(id.split(';'));
    monTableau.forEach(element => {
        element.pop();

        element.forEach(ele => {

            ele = ele.replace('"', '');
            var tableauFini = ele.split(',');
            
            var libelle = tableauFini[0];
            
            var montant = tableauFini[2];

            var idfrais =  document.getElementById(libelle);
            var idfraisMontant =  document.getElementById(libelle + "Montant"); // recupere l'id de la balise montant de chaque produit (ETP, 

        
            idfrais.value = (parseFloat(idfrais.value) < 0 ? 0 : parseFloat(idfrais.value));
            var montantUnitaire = parseFloat(idfrais.value) * parseFloat(montant);
            if(isNaN(montantUnitaire))
            {
                montantUnitaire = 0;
            }
            prix += parseFloat(montantUnitaire);
            idfraisMontant.value = parseFloat(montantUnitaire) + ' €';

        
        
            

           
        });
        prixTotal.value = parseFloat(prix) + ' €';
        
    });

function myPrompt()
{
    var comment = prompt('Veuillez mettre un commentaire de refus');
    comment;
    //    if (comment !== null)
    //    {
    //        return comment;
    //    }
    //    return false;
}



}

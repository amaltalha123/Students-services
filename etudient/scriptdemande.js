const typeDocumentSelect = document.getElementById("type_document");
const dynamicFields = document.getElementById("dynamic-fields");

// Ajout d'un événement pour détecter les changements dans le champ "Type de Document"
typeDocumentSelect.addEventListener("change", function () {
    // Effacer les champs dynamiques existants
    dynamicFields.innerHTML = "";

    // Ajouter des champs dynamiques uniquement si "Convention de stage" est sélectionnée
    if (this.value === "Convention de stage") {
        const entrepriseField = document.createElement("div");
        entrepriseField.className = "form-group1";
        entrepriseField.innerHTML = `
            <label for="entreprise">Nom de l'entreprise :</label>
            <input type="text" id="entreprise" name="entreprise" required>
        `;
        dynamicFields.appendChild(entrepriseField);

        const dureeField = document.createElement("div");
        dureeField.className = "form-group1";
        dureeField.innerHTML = `
            <label for="duree_stage">Durée du stage (mois):</label>
            <input type="number" id="duree_stage" name="duree_stage" required>
        `;
        dynamicFields.appendChild(dureeField);

        const sujetField = document.createElement("div");
        sujetField.className = "form-group1";
        sujetField.innerHTML = `
            <label for="sujet_stage">Sujet du stage :</label>
            <textarea id="sujet_stage" name="sujet_stage" rows="4" required></textarea>
        `;
        dynamicFields.appendChild(sujetField);

        const localisationField = document.createElement("div");
        localisationField.className = "form-group1";
        localisationField.innerHTML = `
            <label for="localisation">Localisation :</label>
            <textarea id="localisation" name="localisation" rows="4" required></textarea>
        `;
        dynamicFields.appendChild(localisationField);

        const encadrantEntrepriseField = document.createElement("div");
        encadrantEntrepriseField.className = "form-group1";
        encadrantEntrepriseField.innerHTML = `
            <label for="encadrant_entreprise">Encadrant à l'entreprise :</label>
            <input type="text" id="encadrant_entreprise" name="encadrant_entreprise">
        `;
        dynamicFields.appendChild(encadrantEntrepriseField);

        const encadrantEcoleField = document.createElement("div");
        encadrantEcoleField.className = "form-group1";
        encadrantEcoleField.innerHTML = `
            <label for="encadrant_ecole">Encadrant à l'école :</label>
            <input type="text" id="encadrant_ecole" name="encadrant_ecole">
        `;
        dynamicFields.appendChild(encadrantEcoleField);
    }
    if(this.value === "Relevé de notes"){
        const niveau_demandeField = document.createElement("div");
        niveau_demandeField.className = "form-group1";
        niveau_demandeField.innerHTML = `
            
            <label for="niveau_demande">Séléctionner le niveau :</label>
                <select id="niveau_demande" name="niveau_demande" required>
                    <option value="">-- Sélectionner un type --</option>
                    <option value="2AP1">2AP1</option>
                    <option value="2PA2">2AP2</option>
                    <option value="CI1">CI1</option>
                    <option value="CI2">CI2</option>
                    <option value="CI3">CI3</option>
                </select>
        `;
        dynamicFields.appendChild(niveau_demandeField);
    }
});

     
        function verifierChamps() {
            // Récupération des champs
            const email = document.getElementById("email");
            const numeroApogee = document.getElementById("numero_apogee");
            const submitButton = document.getElementById("submitButton");

            // Définir les expressions régulières
            const regexEmail = /^[a-zA-Z0-9._%+-]+@etu\.uae\.ac\.ma$/;
            const regexNumeroApogee = /^[0-9]{8}$/;

            let formulaireValide = true;

            // Vérification de l'email
            if (!regexEmail.test(email.value)) {
                appliquerClasse(email, false, "email_error", "Format d'email (.....@etu.uae.ac.ma).");
                formulaireValide = false;
            } else {
                appliquerClasse(email, true, "email_error");
            }

            // Vérification du numéro Apogée
            if (!regexNumeroApogee.test(numeroApogee.value)) {
                appliquerClasse(numeroApogee, false, "numero_error", "Numéro Apogée (8 chiffres max).");
                formulaireValide = false;
            } else {
                appliquerClasse(numeroApogee, true, "numero_error");
            }

            // Activer ou désactiver le bouton de soumission
            submitButton.disabled = !formulaireValide;
        }

        function appliquerClasse(element, valide, errorId, message = "") {
            const errorSpan = document.getElementById(errorId);
            
            if (valide) {
                element.classList.add("valide");
                element.classList.remove("invalide");
                errorSpan.textContent = "";
            } else {
                element.classList.add("invalide");
                element.classList.remove("valide");
                errorSpan.textContent = message;
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            // Ajouter des écouteurs d'événements pour valider les champs en temps réel
            const inputs = document.querySelectorAll("#email, #numero_apogee");
            inputs.forEach(input => input.addEventListener("input", verifierChamps));

            // Initialiser la vérification des champs au chargement
            verifierChamps();
            
        });
        
        document.getElementById('submitButton').addEventListener('click', function (event) {
            event.preventDefault(); // Empêche le rechargement de la page
        
            // Récupération des champs de base
            const name = document.getElementById('nom').value;
            const email = document.getElementById('email').value;
            const numero_apogee = document.getElementById('numero_apogee').value;
            const prenom = document.getElementById('prenom').value;
            const cin = document.getElementById('CIN').value;
            const type_document = document.getElementById('type_document').value;
            niveau_demande
        
            // Récupération des champs dynamiques (si "Convention de stage" est sélectionnée)
            let additionalData1 = '';
            if (type_document === 'Convention de stage') {
                const entreprise = document.getElementById('entreprise')?.value || '';
                const duree_stage = document.getElementById('duree_stage')?.value || '';
                const sujet_stage = document.getElementById('sujet_stage')?.value || '';
                const localisation = document.getElementById('localisation')?.value || '';
                const encadrant_entreprise = document.getElementById('encadrant_entreprise')?.value || '';
                const encadrant_ecole = document.getElementById('encadrant_ecole')?.value || '';
        
                // Ajouter les champs dynamiques à la requête
                additionalData1 = `&entreprise=${encodeURIComponent(entreprise)}&duree_stage=${encodeURIComponent(duree_stage)}&sujet_stage=${encodeURIComponent(sujet_stage)}&localisation=${encodeURIComponent(localisation)}&encadrant_entreprise=${encodeURIComponent(encadrant_entreprise)}&encadrant_ecole=${encodeURIComponent(encadrant_ecole)}`;
            }
            let additionalData2 = '';
            if (type_document === 'Relevé de notes') {
                const niveau_demande = document.getElementById('niveau_demande')?.value || '';
                // Ajouter les champs dynamiques à la requête
                additionalData2 = `&niveau_demande=${encodeURIComponent(niveau_demande)}`;
            }
        
            // Envoi de la requête AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'Studentdemande.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('response').innerText = xhr.responseText;
                } else {
                    console.error('Erreur lors de l\'envoi de la requête :', xhr.status);
                }
            };
            if (type_document === 'Convention de stage') {
                xhr.send(`nom=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&numero_apogee=${encodeURIComponent(numero_apogee)}&prenom=${encodeURIComponent(prenom)}&CIN=${encodeURIComponent(cin)}&type_document=${encodeURIComponent(type_document)}${additionalData1}`);
            }else if(type_document === 'Relevé de notes'){
                xhr.send(`nom=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&numero_apogee=${encodeURIComponent(numero_apogee)}&prenom=${encodeURIComponent(prenom)}&CIN=${encodeURIComponent(cin)}&type_document=${encodeURIComponent(type_document)}${additionalData2}`);
            }
        });
        
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addEntrepriseBtn');
    const entrepriseSection = document.getElementById('entrepriseSection');
    const cancelBtn = document.getElementById('cancelBtn');
    const entrepriseForm = document.getElementById('entrepriseForm');
    const logoUpload = document.getElementById('logoUpload');
    const logoInput = document.getElementById('logo');
    
    // Afficher/masquer la section au clic sur le bouton "Ajouter une Entreprise"
    addBtn.addEventListener('click', function() {
        if (entrepriseSection.style.display === 'block') {
            entrepriseSection.style.display = 'none';
            addBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Ajouter une Entreprise';
        } else {
            entrepriseSection.style.display = 'block';
            addBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Masquer le formulaire';
            // Réinitialiser le formulaire
            entrepriseForm.reset();
        }
    });
    
    // Masquer la section au clic sur "Annuler"
    cancelBtn.addEventListener('click', function() {
        entrepriseSection.style.display = 'none';
        addBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Ajouter une Entreprise';
        entrepriseForm.reset();
    });
    
    // Gérer l'upload du logo
    logoUpload.addEventListener('click', function() {
        logoInput.click();
    });
    
    logoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const fileName = this.files[0].name;
            logoUpload.innerHTML = `
                <div class="upload-icon">
                    <i class="fas fa-file-image"></i>
                </div>
                <p class="upload-text">${fileName}</p>
                <p class="upload-hint">Logo sélectionné</p>
            `;
        }
    });
    
    // Soumission du formulaire
    entrepriseForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Récupérer les valeurs du formulaire
        const nom = document.getElementById('nom').value;
        const secteur = document.getElementById('secteur').value;
        const ville = document.getElementById('ville').value;
        const email = document.getElementById('email').value;
        const telephone = document.getElementById('telephone').value;
        
        // Ici, vous pouvez ajouter le code pour envoyer les données à votre backend
        // Par exemple, via fetch() ou XMLHttpRequest
        
        // Pour l'instant, affichons les données dans la console
        console.log('Nouvelle entreprise ajoutée:');
        console.log('Nom:', nom);
        console.log('Secteur:', secteur);
        console.log('Ville:', ville);
        console.log('Email:', email);
        console.log('Téléphone:', telephone);
        console.log('Logo:', logoInput.files[0] ? logoInput.files[0].name : 'Aucun logo sélectionné');
        
        // Afficher un message de succès
        alert(`Entreprise "${nom}" ajoutée avec succès !`);
        
        // Masquer le formulaire et réinitialiser
        entrepriseSection.style.display = 'none';
        addBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Ajouter une Entreprise';
        entrepriseForm.reset();
        
        // Réinitialiser l'area d'upload
        logoUpload.innerHTML = `
            <div class="upload-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <p class="upload-text">Cliquez pour télécharger le logo</p>
            <p class="upload-hint">Formats acceptés: JPG, PNG, SVG (max. 5MB)</p>
        `;
    });
    
    // Animation pour le bouton d'ajout
    addBtn.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
    });
    
    addBtn.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

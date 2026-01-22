
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addEntrepriseBtn');
    const entrepriseSection = document.getElementById('entrepriseSection');
    const cancelBtn = document.getElementById('cancelBtn');
    const entrepriseForm = document.getElementById('entrepriseForm');
    const logoUpload = document.getElementById('logoUpload');
    const logoInput = document.getElementById('logo');
    
    // Afficher/masquer la section

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
    
    // Masquer au clic sur Annuler
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
            const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2); // MB
            
            // Vérifier la taille
            if (this.files[0].size > 5 * 1024 * 1024) {
                alert('Le fichier est trop volumineux (max 5MB)');
                this.value = '';
                return;
            }
            
            logoUpload.innerHTML = `
                <div class="upload-icon">
                    <i class="fas fa-file-image"></i>
                </div>
                <p class="upload-text">${fileName}</p>
                <p class="upload-hint">Taille: ${fileSize} MB</p>
            `;
        }
    });
    
    // Validation côté client avant soumission
    entrepriseForm.addEventListener('submit', function(e) {
        const nom = document.getElementById('nom').value.trim();
        const secteur = document.getElementById('secteur').value;
        const ville = document.getElementById('ville').value.trim();
        const email = document.getElementById('email').value.trim();
        const telephone = document.getElementById('telephone').value.trim();
        
        let errors = [];
        
        if (!nom) errors.push('Le nom est obligatoire');
        if (!secteur) errors.push('Le secteur est obligatoire');
        if (!ville) errors.push('La ville est obligatoire');
        if (!email || !email.includes('@')) errors.push('Email invalide');
        if (!telephone) errors.push('Le téléphone est obligatoire');
        
        if (errors.length > 0) {
            e.preventDefault();
            alert('Erreurs:\n' + errors.join('\n'));
            return false;
        }
        
        // Le formulaire sera soumis normalement
        return true;
    });
});
function toggleForm() {
        const container = document.getElementById('companyFormContainer');
        container.classList.toggle('hidden');
        // Reset form titles to Add mode
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-file-alt text-indigo-500"></i> Ajouter une Entreprise';
    }

    // وظيفة التعديل (مثال)
    function editCompany(id) {
        toggleForm();
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit text-orange-500"></i> Modifier l\'Entreprise #' + id;
        // هنا يمكنك جلب بيانات الشركة بـ AJAX ووضعها في الـ inputs
    }

    // وظيفة تأكيد الحذف
    function confirmDelete(id) {
        if (confirm("Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.")) {
            // هنا تقوم بتوجيه المستخدم لرابط الحذف أو إرسال Form الحذف
            window.location.href = "/company/delete/" + id;
        }
    }
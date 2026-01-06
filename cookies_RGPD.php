<!-- TarteAuCitron RGPD -->
<script src="https://cdn.jsdelivr.net/npm/tarteaucitronjs@latest/tarteaucitron.js"></script>

<script>
tarteaucitron.init({
    privacyUrl: "",            // Lien vers la politique de confidentialité
    orientation: "bottom",     // Position du bandeau (haut ou bas)
    showAlertSmall: true,      // Afficher l'alerte en petit
    cookieslist: true,         // Lister les cookies après consentement
    hashtag: "#tarteaucitron", // Ancre du bouton
    handleBrowserDNTRequest: true,  // Respecter la demande "Do Not Track"
    highPrivacy: true,         // Pas de cookies avant consentement
    acceptAllCta: true,        // Bouton pour accepter tout
    moreInfoLink: true,        // Lien vers plus d'informations
    useExternalCss: false      // Utiliser un CSS externe ou non
});
</script>

<!-- Activation du service tiers après consentement -->
<script>
(tarteaucitron.job = tarteaucitron.job || []).push('youtube');
</script>

<!-- Bouton pour gérer les cookies -->
<div style="text-align:center; margin-top:20px;">
    <a href="javascript:tarteaucitron.userInterface.openPanel();">
        Gérer mes cookies
    </a>
</div>

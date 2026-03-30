// Message de bienvenue
document.addEventListener("DOMContentLoaded", () => {
    console.log("Bienvenue sur Student Housing !");
});

// Exemple: confirmation avant suppression
function confirmDelete() {
    return confirm("Êtes-vous sûr de vouloir supprimer cette annonce ?");
}

// Exemple: toggle menu mobile
function toggleMenu() {
    const nav = document.querySelector("nav");
    nav.classList.toggle("open");
}

let favoritePath = "{{ path('cart_favorite', {'id': 0}) }}";

function addToFavorites(productId) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            type: "POST",
            url: favoritePath.replace(/0/, productId),
            success: function (data) {
                console.log("Produit ajouté aux favoris");
                resolve(data);
            },
            error: function (error) {
                console.log("Erreur lors de l'ajout du produit aux favoris");
                reject(error);
            }
        });
    });
}

document.getElementById("add-to-favorites").addEventListener("click", function () {
    let productId = // Obtenez l'ID du produit à partir de votre code Twig;
        addToFavorites(productId)
            .then(function (data) {
                console.log("Requête réussie avec les données suivantes:", data);
            })
            .catch(function (error) {
                console.error("Erreur lors de la requête:", error);
            });
});

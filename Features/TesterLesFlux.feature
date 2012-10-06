# features/testerLesFlux.feature
# language: fr

@api @flux @service @obligatoire
Fonctionnalité: Tester la disponibilité du service distant
    Afin d'exploiter les données exposées par l'API distante
    En tant que service local
    Je dois valider que le service distant est bien disponible

    Scénario: Vérifier qu'on peut atteindre le service distant
        Etant donné que l'URL de l'API est connue
        Quand on fait un ping l'URL de l'API
        Alors l'URL de l'API répond par un code HTTP 200
        Et le corps de la réponse HTTP n'est pas vide

    Scénario: Vérifier que l'API distante contient des informations utilisables
        Etant donné que l'URL de l'API est connue
        Quand on fait un ping l'URL de l'API
        Alors l'API répond avec un flux RSS 2.0 valide

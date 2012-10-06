# features/recupereLesDonneesEnFonctionDunContexte.feature
# language: fr

@api @flux @data @obligatoire
Fonctionnalité: Récuperer les évènements sismiques en fonction d'une position
    Afin d'être alerté de tout risque sismique
    En tant qu'utilisateur final localisé
    Je dois pouvoir récupérer les évènements sismiques les plus récents et les plus proches

    Scénario: S'assurer que l'on peut récupérer ma position courante
        Etant donné un utilisateur dont on connaît l'adresse postale
        Quand il interroge le service
        Alors on vérifie qu'on peut le localiser
        Et lui renvoyer son tuple latitude-longitude

    Scénario: Afficher un message si mon adresse postale est irrécupérable
        Etant donné un utilisateur dont on ignore l'adresse postale
        Quand il interroge le service
        Alors on lui indique qu'on ne peut pas le géo-localiser

    Scénario: Afficher un message si mon adresse postale n'est pas géolocalisable
        Etant donné un utilisateur dont on connaît l'adresse postale
        Quand il interroge le service
        Et que cette adresse n'est pas géolocalisable
        Alors on lui indique qu'on ne peut pas connaître sa position géographique

    Scénario: Afficher un message si aucun évènement n'est proche de ma position courante
        Etant donné un utilisateur dont on connaît la position géographique
        Quand il interroge le service
        Et qu'aucun évènement sismique récent n'est proche de lui
        Alors on lui indique qu'aucun sismique n'est proche de lui

    Scénario: En fonction de ma position, afficher les évènements sismiques proches
        Etant donné un utilisateur dont on connaît la position géographique
        Quand il utilise le service
        Et qu'au moins un évènement sismique récent est proche de lui
        Alors on lui donne le détail de chaque évènement proche et récent


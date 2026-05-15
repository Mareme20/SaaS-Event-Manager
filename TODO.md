# TODO - Mobile navigation fixes

## Étape 1 : Plan confirmé
- [x] Analyser les layouts et pages concernées
- [x] Valider le plan de correction avec le user (burger + drawer avant/après connexion)

## Étape 2 : Implémenter le menu mobile sur le catalogue (Welcome)
- [ ] Ajouter un bouton burger sur mobile
- [ ] Ajouter un drawer/overlay avec les liens (Tarification, Connexion, Créer un compte, Tableau de bord si auth)
- [ ] Gérer la fermeture (clic overlay + clic lien)

## Étape 3 : Implémenter le menu mobile sur le layout connecté (AuthenticatedLayout)
- [x] Ajouter un bouton burger sur mobile
- [x] Ajouter un drawer/overlay avec les liens principaux
- [x] S’assurer que le drawer est visible et au bon z-index
- [x] Gérer la fermeture au clic lien

## Étape 4 : Rebuild & test
- [ ] Lancer `npm run build` ou `npm run dev` (selon le workflow habituel)
- [ ] Tester sur mobile : Welcome (avant connexion) + navigation après connexion
- [ ] Corriger styles responsives si nécessaire



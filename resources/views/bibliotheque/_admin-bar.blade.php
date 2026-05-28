<x-public-manage-bar
    label="Bibliothèque"
    :permissions="['publier-bibliotheque', 'administrer-utilisateurs']"
    :create-route="route('admin.ressources.create')"
    :list-route="route('admin.ressources.index')"
    :item="isset($ressource) ? $ressource : null"
    :edit-route="isset($ressource) ? route('admin.ressources.edit', $ressource) : null"
    :toggle-route="isset($ressource) ? route('contenu.ressources.toggle', $ressource) : null"
    published-key="is_validated"
/>

# 📰 trends Wayo - Documentation

## ✅ Structure Complète Créée

### 🗂️ Structure des fichiers

```
application/
├── controllers/
│   └── Articles.php                    ✅ Contrôleur principal du trends
│
├── models/
│   └── Article_model.php              ✅ Modèle avec données de test
│
├── views/frontend/ultimate/Articles/
│   ├── index.php                      ✅ Liste des articles (grille)
│   ├── show.php                       ✅ Détail d'un article
│   ├── category.php                   ✅ Articles par catégorie
│   ├── tag.php                        ✅ Articles par tag
│   ├── partials/
│   │   └── sidebar.php                ✅ Sidebar réutilisable
│   └── pages/
│       ├── about_trends.php             ✅ À propos du trends
│       ├── faq.php                    ✅ FAQ du trends
│       └── contact.php                ✅ Contact trends
│
└── config/
    └── routes.php                     ✅ Routes configurées

assets/frontend/ultimate/css/
└── trends.css                           ✅ CSS dédié avec charte Wayo
```

## 🎨 Design & Charte Graphique

### Couleurs Wayo
- **Orange principal** : `#fc7b30`
- **Orange secondaire** : `#d45a1f`
- **Texte** : `#1A1A1A`
- **Texte secondaire** : `#666`, `#999`
- **Fond** : `#fafafa`, `#fff`

### Typography
- **Police principale** : `Urbanist` (Google Fonts)
- **Poids** : 400 (normal), 600 (semi-bold), 700 (bold), 800 (extra-bold)

### Caractéristiques du design
✨ **Moderne & Premium**
- Gradients subtils
- Ombres douces (box-shadow)
- Bordures arrondies (20px, 30px)
- Animations fluides (hover effects)
- Cards avec effet de profondeur

🎯 **Responsive**
- Design adaptatif mobile/tablette/desktop
- Sticky sidebar sur desktop
- Navigation optimisée mobile

🌍 **Support RTL**
- Direction RTL pour l'arabe
- Positionnement adaptatif

## 🌐 URLs du trends

### Routes configurées
```
/trends                           → Liste tous les articles
/trends/tag/wayo                  → Articles avec tag "wayo"
/trends/[slug]                    → Article détaillé
```

## 🎯 Fonctionnalités

### Page Index (`/trends`)
- **Hero Section** avec titre et description
- **Filtres de catégories** (All, Actualités, Conseils, Tutoriels)
- **Grille d'articles** responsive (3 colonnes desktop, 2 tablette, 1 mobile)
- **Cards animées** avec image, catégorie badge, titre, extrait
- **Effet hover** élégant (translation + shadow)
- **État vide** géré avec message

### Page Article (`/trends/[slug]`)
- **Breadcrumb** navigation
- **Meta informations** (auteur, date, temps de lecture)
- **Image featured** full-width avec border-radius
- **Contenu riche** avec typographie optimisée
- **Sidebar sticky** avec :
  - Recherche d'articles
  - Articles récents
  - Catégories avec compteurs
  - Tags cloud
- **Partage social** (Facebook, Twitter, LinkedIn)
- **Tags de l'article**

### Pages statiques
- **About trends** : Présentation avec icônes et cards
- **FAQ** : Accordion Bootstrap 5
- **Contact** : Formulaire stylisé

## 🔧 Données de Test

Le modèle `Article_model` contient actuellement des **données mockées** :

```php
[
    'title' => 'Bienvenue sur le trends Wayo',
    'slug' => 'bienvenue-sur-le-trends-wayo',
    'summary' => 'Découvrez toutes les actualités...',
    'category' => 'Actualités',
    'image' => 'https://images.unsplash.com/...',
    // ...
]
```

### ➡️ Prochaines étapes recommandées

1. **Créer la table en base de données** :
```sql
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `summary` text,
  `content` longtext,
  `image` varchar(500),
  `category` varchar(100),
  `category_slug` varchar(100),
  `author` varchar(100),
  `tags` varchar(500),
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_slug` (`category_slug`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

2. **Modifier le modèle** pour utiliser la vraie base de données au lieu des données mockées

3. **Créer un backend admin** pour gérer les articles

## 🎨 Personnalisation CSS

Le fichier `trends.css` est entièrement personnalisable. Principales classes :

### Layout
- `.trends-hero` - Section hero
- `.trends-container` - Container principal
- `.article-card` - Card d'article
- `.article-body-content` - Contenu de l'article

### Components
- `.category-filter` - Barre de filtres
- `.cat-btn` - Bouton de catégorie
- `.sidebar-widget` - Widget sidebar
- `.tag-cloud` - Nuage de tags

### Utilities
- `.hover-orange` - Hover orange
- `.text-orange` - Texte orange
- `.bg-orange` - Background orange

## 📱 Navigation

Le trends est accessible depuis :
- **Menu desktop** : Lien "trends" après "How it works"
- **Menu mobile** : Lien "trends" dans le offcanvas

## ✨ Animations

Animations CSS intégrées :
- `fadeInDown` - Hero title
- `fadeInUp` - Hero description
- Hover transitions sur les cards
- Smooth scrolling

## 🌟 Points forts du design

1. **Cohérence visuelle** avec le reste de Wayo
2. **Performance optimisée** avec CSS pur (pas de framework lourd)
3. **Accessibilité** (ARIA labels, semantic HTML)
4. **SEO-ready** (meta tags, heading structure)
5. **Mobile-first** responsive design

## 🐛 Debug

Si la page ne s'affiche pas :
1. Vérifier que le serveur Apache est démarré
2. Vérifier les routes dans `application/config/routes.php`
3. Vérifier les chemins des vues dans `Articles.php`
4. Consulter les logs PHP : `xampp/apache/logs/error.log`

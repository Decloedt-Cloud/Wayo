# Documentation des Routes - School Management System

## Table des matières
1. [Routes Frontend (Public)](#routes-frontend-public)
2. [Routes API](#routes-api)
3. [Routes Backend (Masking App)](#routes-backend-masking-app)
4. [Routes Spéciales](#routes-spéciales)

---

## Routes Frontend (Public)

### Routes principales
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/` | home/index | Page d'accueil |
| `/home` | home/index | Page d'accueil |

### Communautés et Inscription
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/communities` | home/communities | Liste des communautés |
| `/communities/(.+)` | home/communities | Détails communauté |
| `/join/community` | admission/online_admission | Inscription communauté |
| `/join/member` | admission/online_admission_student | Inscription membre |

### Pages d'information
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/about` | home/about | À propos |
| `/contact` | home/contact | Contact |
| `/support` | home/contact | Support |
| `/faq` | home/faq | FAQ |
| `/terms` | home/terms_conditions | Conditions |
| `/privacy_policy` | home/privacy_policy | Politique de confidentialité |

### Actualités et Médias
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/trends` | articles/index | Articles/Tendances |
| `/trends/category/(:any)` | articles/category | Catégorie article |
| `/trends/tag/(:any)` | articles/tag | Tag article |
| `/trends/(:any)` | articles/show | Détails article |
| `/gallery` | home/galerie | Galerie |
| `/events` | home/events | Événements |

### Autres routes frontend
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/teachers` | home/teachers | Liste des enseignants |
| `/noticeboard` | home/noticeboard | Tableau d'affichage |
| `/webinaire` | home/webinaire | Webinaires |

---

## Routes API

### Authentification et Utilisateurs
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/login` | POST | api/Admin/login | Connexion |
| `/api/register` | POST | api/Admin/register | Inscription |
| `/api/user` | GET | api/Admin/user | Info utilisateur |
| `/api/edit` | POST | api/Admin/editProfile | Modifier profil |
| `/api/editPassword` | POST | api/Admin/updatePassword | Modifier mot de passe |

### Gestion des écoles (Superadmin)
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/schools` | GET | api/Admin/schools | Liste écoles |
| `/api/school/create` | POST | api/Admin/online_admission_school | Créer école |
| `/api/school/update` | PUT | api/Admin/update_school | Mettre à jour école |
| `/api/school/delete/(:num)` | DELETE | api/Admin/delete_school/$1 | Supprimer école |
| `/api/search_schools` | GET | api/Admin/search_schools | Rechercher écoles |

### Gestion des administrateurs
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/create_admin` | POST | api/Admin/create_admin | Créer admin |
| `/api/get_admin` | GET | api/Admin/all_admins | Liste admins |
| `/api/editAdmin/(:num)` | PUT | api/Admin/edit_admin/$1 | Modifier admin |
| `/api/deleteAdmin/(:num)` | DELETE | api/Admin/Deladmin/$1 | Supprimer admin |

### Gestion des enseignants
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/teachers` | GET | api/Admin/all_teacher | Liste enseignants |
| `/api/teachers/Create` | POST | api/Admin/create_teacher | Créer enseignant |
| `/api/teachers/edit/(:num)` | PUT | api/Admin/edit_teacher/$1 | Modifier enseignant |
| `/api/teachers/delete/(:num)` | DELETE | api/Admin/delete_teacher/$1 | Supprimer enseignant |
| `/api/teachersById/(:num)` | GET | api/Admin/teacher_by_id/$1 | Détails enseignant |
| `/api/teacherPermission` | POST | api/Admin/add_teacher_permission | Permissions |
| `/api/teachers_by_class/(:num)` | GET | api/Admin/teachers_by_class/$1 | Enseignants par classe |

### Gestion des étudiants
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/GetStudentsList` | GET | api/Admin/get_students_list | Liste étudiants |
| `/api/ApproveStudent` | POST | api/Admin/approve_student | Approuver étudiant |
| `/api/DeleteStudent` | DELETE | api/Admin/delete_student | Supprimer étudiant |
| `/api/onlineadmission` | POST | api/Admin/onlineadmission | Admission en ligne |
| `/api/onlineadmissionList/(:num)` | GET | api/Admin/onlineadmissionList/$1 | Liste admissions |
| `/api/approveOnlineAdmissions/(:num)` | PUT | api/Admin/approveOnlineAdmissions/$1 | Approuver admission |

### Classes et Sections
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/classes` | GET | api/Admin/classes | Liste classes |
| `/api/CreateClass` | POST | api/Admin/class_create | Créer classe |
| `/api/UpdateClass` | PUT | api/Admin/class_update | Mettre à jour classe |
| `/api/DeleteClass` | DELETE | api/Admin/class_del | Supprimer classe |
| `/api/GetSectionsList` | GET | api/Admin/section | Liste sections |
| `/api/GetSectionsForClass` | GET | api/Admin/sections_for_class | Sections par classe |

### Matières (Subjects)
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/subjects` | GET | api/Admin/subjects_names | Liste matières |
| `/api/CreateSubject` | POST | api/Admin/create_subject | Créer matière |
| `/api/subjects` | GET | api/Admin/subjects_names | Liste matières |

### Cours (Courses)
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/AllCourses` | GET | api/Admin/all_courses | Tous les cours |
| `/api/CreateCourse` | POST | api/Admin/create_course | Créer cours |
| `/api/EditCourse/(:num)` | PUT | api/Admin/edit_course/$1 | Modifier cours |
| `/api/GetCourse/(:num)` | GET | api/Admin/course_details/$1 | Détails cours |
| `/api/DeleteCourse/(:num)` | DELETE | api/Admin/delete_course/$1 | Supprimer cours |
| `/api/courses_by_class/(:any)` | GET | api/Admin/courses_by_class/$1 | Cours par classe |
| `/api/CoursesByStatus/(:any)` | GET | api/Admin/courses_by_status/$1 | Cours par statut |

### Leçons (Lessons)
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/lesson/create` | POST | api/Admin/add_lesson | Créer leçon |
| `/api/AllLessons/(:num)` | GET | api/Admin/all_lesson_types/$1 | Toutes les leçons |
| `/api/AddLessonYoutubeVideo` | POST | api/Admin/add_lesson_youtube | Ajouter vidéo YouTube |
| `/api/UpdateLessonYoutubeVideo` | PUT | api/Admin/update_lesson_youtube | Modifier vidéo |
| `/api/DeleteLesson` | DELETE | api/Admin/delete_lesson | Supprimer leçon |

### Quiz
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/quizzes/AddQuiz` | POST | api/Admin/add_quiz | Créer quiz |
| `/api/quizzes/UpdateQuiz` | PUT | api/Admin/update_quiz | Modifier quiz |
| `/api/quizzes/delete` | DELETE | api/Admin/delete_quiz | Supprimer quiz |
| `/api/quizzes/AddQuestion` | POST | api/Admin/add_question | Ajouter question |
| `/api/quizzes/GetQuestions/(:num)` | GET | api/Admin/get_quiz_questions/$1 | Questions quiz |
| `/api/quizzes/EditQuestions` | PUT | api/Admin/edit_question | Modifier question |
| `/api/quizzes/DeleteQuestions` | DELETE | api/Admin/delete_question | Supprimer question |
| `/api/SubmitQuiz` | POST | api/Admin/submit_quiz | Soumettre quiz |
| `/api/GetQuizResult` | GET | api/Admin/quiz_result | Résultats quiz |

### Examens
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/GetExams/(:num)/(:num)` | GET | api/Admin/exams_by_school_id/$1/$2 | Examens par école |
| `/api/CreateExams` | POST | api/Admin/create_exam | Créer examen |
| `/api/EditExams` | PUT | api/Admin/edit_exam | Modifier examen |
| `/api/DeleteExams/(:num)` | DELETE | api/Admin/delete_exam/$1 | Supprimer examen |

### Notes et Présences
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/AddMarks` | POST | api/Admin/add_marks | Ajouter notes |
| `/api/UpdateMarks` | PUT | api/Admin/update_marks | Mettre à jour notes |
| `/api/attendance/CreateAttendance` | POST | api/Admin/create_attendance | Créer présence |
| `/api/attendance/bulk_update` | PUT | api/Admin/bulk_attendance_update | Mise à jour lot |
| `/api/attendance/UpdateAttendanceStatus` | PUT | api/Admin/update_attendance_status | Modifier présence |
| `/api/attendance/filter` | GET | api/Admin/filter_attendance | Filtrer présences |

### Bibliothèque
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/GetBooks/(:num)/(:num)` | GET | api/Admin/books_by_school_id/$1/$2 | Livres par école |
| `/api/CreateBook` | POST | api/Admin/create_book | Créer livre |
| `/api/EditBook` | PUT | api/Admin/edit_book | Modifier livre |
| `/api/DeleteBook/(:num)` | DELETE | api/Admin/delete_book/$1 | Supprimer livre |
| `/api/GetBookIssue` | GET | api/Admin/book_issues | Prêts en cours |
| `/api/CreateBookIssue` | POST | api/Admin/create_book_issue | Créer prêt |

### Événements
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/GetEvent/(:num)` | GET | api/Admin/events_by_school_id/$1 | Événements école |
| `/api/CreateEvent` | POST | api/Admin/create_event | Créer événement |
| `/api/EditEvent/(:num)` | PUT | api/Admin/edit_event/$1 | Modifier événement |
| `/api/DeleteEvent/(:num)` | DELETE | api/Admin/delete_event/$1 | Supprimer événement |

### Dépenses
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/GetExpenses/(:num)` | GET | api/Admin/get_expenses/$1 | Dépenses école |
| `/api/CreateExpense` | POST | api/Admin/create_expense | Créer dépense |
| `/api/EditExpense` | PUT | api/Admin/edit_expense | Modifier dépense |
| `/api/DeleteExpense` | DELETE | api/Admin/delete_expense | Supprimer dépense |
| `/api/CreateExpenseCategory` | POST | api/Admin/create_expense_category | Catégorie dépense |

### Facturation (Invoices)
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/createInvoice` | POST | api/Admin/create_invoice | Créer facture |
| `/api/invoices/CreateSingleInvoice` | POST | api/Admin/create_single_invoice | Facture simple |
| `/api/invoices/CreateMassInvoice` | POST | api/Admin/create_mass_invoice | Factures masse |
| `/api/invoices/update/(:num)` | PUT | api/Admin/update_invoice/$1 | Modifier facture |
| `/api/invoices/delete/(:num)` | DELETE | api/Admin/delete_invoice/$1 | Supprimer facture |
| `/api/PaidInvoice` | POST | api/Admin/paid_invoice | Marquer payée |

### paramètres Système
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/GetSystemSettings/(:num)` | GET | api/Admin/system_settings/$1 | Paramètres système |
| `/api/UpdateSystemSettings/(:num)` | PUT | api/Admin/update_system_settings/$1 | Modifier paramètres |
| `/api/GetSchoolSettings/(:num)` | GET | api/Admin/school_settings/$1 | Paramètres école |
| `/api/UpdateSchoolSettings/(:num)` | PUT | api/Admin/school_settings_update/$1 | Modifier école |
| `/api/SetSmtpSettings` | POST | api/Admin/set_smtp_settings | Config SMTP |
| `/api/SetPaymentSettings` | POST | api/Admin/set_payment_settings | Config paiement |
| `/api/UpdateStripeSettings` | PUT | api/Admin/update_stripe_settings | Config Stripe |
| `/api/UpdatePaypalSettings` | PUT | api/Admin/update_paypal_settings | Config PayPal |

### Galerie et Médias
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/CreateGallery` | POST | api/Admin/create_gallery | Créer galerie |
| `/api/GetGalleries` | GET | api/Admin/galleries | Liste galeries |
| `/api/GetGalleryImage` | GET | api/Admin/gallery_images_by_id | Images galerie |
| `/api/DeleteGallery` | DELETE | api/Admin/gallery_by_id | Supprimer galerie |
| `/api/DeleteGalleryImage` | DELETE | api/Admin/gallery_image_by_id | Supprimer image |

### Tableau d'affichage (Noticeboard)
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/notices/create` | POST | api/Admin/create_noticeboard | Créer notice |
| `/api/allnoticeboard` | GET | api/Admin/fetch_all_notices | Toutes les notices |
| `/api/noticeboard/get/(:num)` | GET | api/Admin/noticeboard/$1 | Notice spécifique |
| `/api/notices/update/(:num)` | PUT | api/Admin/update_noticeboard/$1 | Modifier notice |
| `/api/notices/delete/(:num)` | DELETE | api/Admin/noticeboard_delete/$1 | Supprimer notice |
| `/api/notices/filter/(:num)` | GET | api/Admin/filter_notices/$1 | Filtrer notices |

### Devise (FX Rates)
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/api/fx` | api/FxRates/index | Taux de change |
| `/api/fx/today` | api/FxRates/today | Taux du jour |
| `/api/fx/latest` | api/FxRates/latest | Derniers taux |
| `/api/fx/date/(:any)` | api/FxRates/date/$1 | Taux par date |
| `/api/fx/convert` | api/FxRates/convert | Convertir devise |

### Wall (Publications)
| URI | Méthode | Contrôleur | Description |
|-----|---------|------------|-------------|
| `/api/communities/(:num)/wall` | GET | api/Wall/community/$1 | Mur communauté |
| `/api/communities/(:num)/wall` | POST | api/Wall/community_posts/$1 | Poster communauté |
| `/api/classes/(:num)/wall` | GET | api/Wall/class/$1 | Mur classe |
| `/api/classes/(:num)/wall` | POST | api/Wall/class_posts/$1 | Poster classe |
| `/api/posts/(:num)/report` | POST | api/Wall/report/$1 | Signaler post |
| `/api/posts/(:num)` | DELETE | api/Wall/posts/$1 | Supprimer post |

---

## Routes Backend (Masking App)

Ces routes permettent l'accès masqué aux fonctionnalités backend selon le rôle de l'utilisateur.

### Routes membres
| URI | Contrôleur dynamique | Description |
|-----|----------------------|-------------|
| `/app/member` | {role}/student | Tableau de bord étudiant |
| `/app/member/(.+)` | {role}/student/$1 | Pages étudiant |

### Routes enseignants
| URI | Contrôleur dynamique | Description |
|-----|----------------------|-------------|
| `/app/mentor` | {role}/teacher | Tableau de bord enseignant |
| `/app/mentor/(.+)` | {role}/teacher/$1 | Pages enseignant |

### Routes certifications
| URI | Contrôleur dynamique | Description |
|-----|----------------------|-------------|
| `/app/certifications` | {role}/exam | Examens/Certifications |
| `/app/certifications/(.+)` | {role}/exam/$1 | Pages examens |

### Routes communautés
| URI | Contrôleur dynamique | Description |
|-----|----------------------|-------------|
| `/app/community_list` | {role}/school | Liste communautés |
| `/app/community_list/(.+)` | {role}/school/$1 | Détails communauté |
| `/app/community_settings` | {role}/school_settings | Paramètres |
| `/app/community_settings/(.+)` | {role}/school_settings/$1 | Pages paramètres |

### Routes cours
| URI | Contrôleur dynamique | Description |
|-----|----------------------|-------------|
| `/app/courses` | addons/courses | Cours |
| `/app/courses/(:num)` | student/manage_class/courses/$1 | Cours par ID |
| `/app/courses/(.+)` | addons/courses/$1 | Page cours |
| `/app/lessons` | addons/lessons | Leçons |
| `/app/lessons/(.+)` | addons/lessons/$1 | Page leçon |

### Routes paiement
| URI | Contrôleur dynamique | Description |
|-----|----------------------|-------------|
| `/app/payment` | student/payment | Paiement |
| `/app/payment/(:any)` | admin/payment/$1 | Page paiement |
| `/app/payment/(:num)` | student/payment/classe/$1 | Paiement classe |
| `/app/payment/cart/(:num)` | student/payment/cart/$1 | Panier |

---

## Routes Spéciales

### BigBlueButton (Visioconférence)
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/bigbluebutton` | bigbluebutton/index | Index BBB |
| `/bigbluebutton/create/(:num)` | bigbluebutton/create_meeting/$1 | Créer réunion |
| `/bigbluebutton/join/(:any)/(:any)/(:any)` | BigBlueButtonController/join/$1/$2/$3 | Rejoindre |
| `/bigbluebutton/start` | BigBlueButton/join_meeting | Démarrer réunion |
| `/bigbluebutton/get_meetings` | BigBlueButton/get_meetings | Liste réunions |
| `/bigbluebutton/webhook` | bigbluebutton/webhook | Webhook BBB |

### Meeting
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/meeting/create` | meeting/create | Créer réunion |
| `/meeting/join` | meeting/join | Rejoindre réunion |

### Chat
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/app/chat` | chat | Chat application |

### Export
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/export/(:any)` | admin/export/$1 | Export données |
| `/export/(:any)/(:num)` | admin/export/$1/$2 | Export avec ID |
| `/export/url` | admin/export/url | Export URL |

### Tâches planifiées (Cron)
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/cron/fx_fetch_daily` | Cron/fx_fetch_daily | Taux quotidien |
| `/cron/fx_health` | Cron/fx_health | Santé FX |
| `/cron/fx_cleanup` | Cron/fx_cleanup | Nettoyage FX |

### Routes Superadmin
| URI | Contrôleur | Description |
|-----|------------|-------------|
| `/superadmin/community_list` | superadmin/school | Liste écoles |
| `/superadmin/community_list/(.+)` | superadmin/school/$1 | École spécifique |

---

## Préfixes de langue

Le système supporte les préfixes de langue dans les URLs:

| Code | Langue |
|------|--------|
| `fr` | Français |
| `en` | Anglais |
| `ar` | Arabe |
| `es` | Espagnol |
| `nl` | Néerlandais |

Exemples:
- `/fr/home` → Page d'accueil française
- `/en/teachers` → Page enseignants anglaise

---

## Notes

- Les routes utilisant `(:num)` attendent un ID numérique
- Les routes utilisant `(:any)` acceptent n'importe quelle valeur
- Les routes API utilisent le contrôleur `api/Admin` comme point d'entrée principal
- Le système de masking app (`/app/*`) routing dynamiquement vers le contrôleur approprié selon le rôle de l'utilisateur connecté

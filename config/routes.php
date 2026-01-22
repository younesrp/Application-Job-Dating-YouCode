<?php



use App\controllers\back\DashboardController;
use App\controllers\back\AnnoncesController;
use App\controllers\back\StudentController;
use App\controllers\back\CandidatureController;

use App\controllers\back\CompanyController;

// 1. لائحة الشركات
$router->get('/admin/companies', [CompanyController::class, 'index']);

// 2. إضافة شركة جديدة
$router->get('/admin/companies/create', [CompanyController::class, 'create']);
$router->post('/admin/companies/save', [CompanyController::class, 'save']);

// 3. تعديل شركة
$router->get('/admin/companies/edit/{id}', [CompanyController::class, 'edit']);
$router->post('/admin/companies/update/{id}', [CompanyController::class, 'update']);

// 4. حذف شركة
$router->get('/admin/companies/delete/{id}', [CompanyController::class, 'delete']);

// ... الروابط القديمة ...

// ✅ هذا هو الجديد: رابط لتحديث حالة الترشيح (POST حيت كنبدلو الداتا)
$router->post('/admin/candidatures/update/{id}', [CandidatureController::class, 'updateStatus']);


// الطريقة 1: باستعمال ::class (هي اللي كننصحك بيها، بروفيسيونيل)
$router->get('admin/dashboard', [DashboardController::class, 'index']);

// أو الطريقة 2: بالكتابة المباشرة (إلا ما بغيتيش دير use الفوق)
// $router->get('admin/dashboard', ['App\controllers\back\DashboardController', 'index']);
// ما ديرش namespace هنا باش ما يدوخش السيرفر

// الطريقة الصحيحة: (Rabat, Controller, Method)
// حيدنا 'middleware' ودرنا 'index' هو الأخر (Method)

// حيدنا 'middleware:' وحطينا 'index' كمتغير ثالث عادي
// ملاحظة: إلا كنتي باغي تزيد middleware (بحال 'auth')، زيدو هو الرابع:
// $router->get('/admin/dashboard', 'App\controllers\back\DashboardController', 'index', 'auth');   


// زيد هادي باش إلا دخلتي لـ localhost:8003 تطلع ليك شي حاجة
$router->get('/', function() {
    echo "<h1>Bienvenue sur la page d'accueil !</h1>";
});
// صفحة إضافة إعلان (Formulaire)
$router->get('/admin/annonces/create', [AnnoncesController::class, 'create']);

// معالجة البيانات (Save)
$router->post('/admin/annonces/save', [AnnoncesController::class, 'save']);

// صفحة الإعلانات
// بدل السطر القديم بهدا:
$router->get('/admin/annonces', [AnnoncesController::class, 'index']);

// الحذف (كنستعملو {id} باش نعرفو إنا وحدة غنمسحو)
$router->get('/admin/annonces/delete/{id}', [AnnoncesController::class, 'delete']);

// صفحة التعديل (Edit Form)
$router->get('/admin/annonces/edit/{id}', [AnnoncesController::class, 'edit']);

// تسجيل التعديلات (Update Action)
$router->post('/admin/annonces/update/{id}', [AnnoncesController::class, 'update']);
// 1. Import Class

// 2. Add Route
$router->get('/admin/students', [StudentController::class, 'index']);


// ...
$router->get('/admin/candidatures', [CandidatureController::class, 'index']);
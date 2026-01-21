<?php



use App\controllers\back\DashboardController;

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